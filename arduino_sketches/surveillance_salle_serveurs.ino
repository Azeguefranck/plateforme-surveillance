/*
 * ============================================================
 *  SURVEILLANCE SALLE SERVEURS
 *  Arduino Uno — Liaison série USB → serial_relay.py → Laravel
 * ============================================================
 *  Capteurs :
 *    DHT22  → broche D4  (température / humidité)
 *    MQ135  → broche A0  (gaz / qualité air)
 *    PIR    → broche D5  (détection mouvement / intrusion)
 *  Sorties :
 *    LED RGB → D9 (R), D10 (G), D11 (B)
 *    Buzzer  → D6
 *
 *  MULTI-SALLE / MULTI-ÉQUIPEMENT :
 *    Changer SALLE_ID et EQUIPEMENT_ID selon l'Arduino déployé.
 *    EQUIPEMENT_ID = 0  →  mesure globale de la salle
 *    EQUIPEMENT_ID = X  →  mesure liée à l'équipement X (ID dans la DB)
 * ============================================================
 */

#include <DHT.h>

// ╔══════════════════════════════════════════════════════════╗
// ║  CONFIGURATION — à modifier par Arduino déployé          ║
// ╚══════════════════════════════════════════════════════════╝
#define SALLE_ID       1
#define EQUIPEMENT_ID  0

// ── Broches ──────────────────────────────────────────────────
#define DHT_PIN     4
#define DHT_TYPE    DHT22
#define MQ135_PIN   A0
#define PIR_PIN     5
#define BUZZER_PIN  6
#define LED_R       9
#define LED_G       10
#define LED_B       11

// ── Seuils d'alerte (ASHRAE TC 9.9) ──────────────────────────
int SEUIL_TEMP_W = 28, SEUIL_TEMP_C = 32;   // °C
int SEUIL_HUM_W  = 75, SEUIL_HUM_C  = 85;   // %HR
int SEUIL_GAZ_W  = 400, SEUIL_GAZ_C = 600;  // ppm
bool PIR_ACTIF   = true;

// ── Timings ───────────────────────────────────────────────────
#define INTERVALLE_MS  10000UL   // stockage DB toutes les 10 s
#define LIVE_MS         2000UL   // live dashboard + vérif alertes toutes les 2 s
#define EMAIL_COOLDOWN 600000UL  // anti-spam email  10 min
#define PIR_COOLDOWN   300000UL  // anti-spam PIR     5 min
#define DEBOUNCE_REQ        1    // 1 lecture suffit (serveur gère le cooldown)

// ── Variables mesures ─────────────────────────────────────────
float temperature = 0.0;
float humidite    = 0.0;
int   gaz         = 0;
int   pir         = 0;

// ── Anti-rebond alertes ───────────────────────────────────────
int  cntTemp=0, cntHum=0, cntGaz=0;
bool etatTemp=false, etatHum=false, etatGaz=false;

// ── Timers anti-spam ──────────────────────────────────────────
unsigned long tEmailTemp=0, tEmailHum=0, tEmailGaz=0, tEmailPir=0;
unsigned long tDernierEnvoi=0, tLive=0;

// ── Buffer JSON ───────────────────────────────────────────────
char jsonBuf[256];

// ── Buzzer non-bloquant avec tone() ──────────────────────────
// 0=silence  1=warning 440Hz  2=critique 880Hz  3=intrusion 1200Hz
int  buzzerMode   = 0;
bool buzzerEtat   = false;
int  buzzerPhase  = 0;
unsigned long tBuzz = 0;

// ── LED clignotante non-bloquante ─────────────────────────────
// 0=verte fixe  1=orange clignotant lent  2=rouge clignotant rapide
int  ledMode  = 0;
bool ledEtat  = true;
unsigned long tLedBlink = 0;

DHT dht(DHT_PIN, DHT_TYPE);

// ── Prototypes ────────────────────────────────────────────────
void lireCapteurs();
void verifierAlertes();
void envoyerLive();
void envoyerDonnees();
void envoyerAlerte(const char* cat, const char* niv, const char* msg);
void afficherSerie();
void ledVerte();
void ledRouge();
void ledOrange();
void ledEteinte();
void gererBuzzer();
void gererLED();
void beepBloquant(int ms);

// ╔══════════════════════════════════════════════════════════╗
// ║  SETUP                                                    ║
// ╚══════════════════════════════════════════════════════════╝
void setup() {
  Serial.begin(9600);
  dht.begin();

  pinMode(PIR_PIN,    INPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_R,  OUTPUT);
  pinMode(LED_G,  OUTPUT);
  pinMode(LED_B,  OUTPUT);

  digitalWrite(BUZZER_PIN, LOW);
  ledVerte();

  Serial.println("DEMARRAGE SYSTEME");
  delay(300);
  Serial.println("SYSTEME PRET");

  beepBloquant(100); delay(100); beepBloquant(100);
}

// ╔══════════════════════════════════════════════════════════╗
// ║  LOOP                                                     ║
// ╚══════════════════════════════════════════════════════════╝
void loop() {
  unsigned long now = millis();
  static unsigned long tPirCheck = 0;

  gererBuzzer();
  gererLED();

  // ── PIR vérifié toutes les secondes ───────────────────────
  if (now - tPirCheck >= 1000UL) {
    tPirCheck = now;
    if (digitalRead(PIR_PIN) == HIGH && PIR_ACTIF) {
      if (now - tEmailPir >= PIR_COOLDOWN) {
        tEmailPir = now;
        envoyerAlerte("INTRUSION", "CRITIQUE", "Mouvement detecte dans la salle serveurs");
        buzzerMode  = 3; buzzerPhase = 0; tBuzz      = now;
        ledMode     = 2; ledEtat     = true; tLedBlink = now;
      }
    }
  }

  // ── Live + vérif alertes toutes les 2 s ───────────────────
  // Les capteurs lents (DHT22) utilisent la dernière valeur connue.
  // Garantit des alertes email en moins de 2 s dès le premier dépassement.
  if (now - tLive >= LIVE_MS) {
    tLive = now;
    gaz = analogRead(MQ135_PIN);
    pir = digitalRead(PIR_PIN);
    // Relire DHT22 si 2 s se sont écoulées (fréquence max du capteur)
    float t2 = dht.readTemperature();
    float h2 = dht.readHumidity();
    if (!isnan(t2)) temperature = t2;
    if (!isnan(h2)) humidite    = h2;
    verifierAlertes();   // vérifie seuils et envoie alerte immédiate si besoin
    envoyerLive();       // met à jour le dashboard
  }

  // ── Stockage DB toutes les 10 s ───────────────────────────
  if (now - tDernierEnvoi >= INTERVALLE_MS) {
    tDernierEnvoi = now;
    envoyerDonnees();   // enregistre en base (pas de doublon de vérif alertes)
    afficherSerie();
  }
}

// ╔══════════════════════════════════════════════════════════╗
// ║  LECTURE CAPTEURS                                         ║
// ╚══════════════════════════════════════════════════════════╝
void lireCapteurs() {
  float t = dht.readTemperature();
  float h = dht.readHumidity();
  if (!isnan(t)) temperature = t;
  if (!isnan(h)) humidite    = h;
  gaz = analogRead(MQ135_PIN);
  pir = digitalRead(PIR_PIN);
}

// ╔══════════════════════════════════════════════════════════╗
// ║  VÉRIFICATION ALERTES                                     ║
// ╚══════════════════════════════════════════════════════════╝
void verifierAlertes() {
  bool alerteWarning  = false;
  bool alerteCritique = false;
  unsigned long now   = millis();

  // ── Température ──────────────────────────────────────────
  if (temperature >= SEUIL_TEMP_W) {
    if (++cntTemp >= DEBOUNCE_REQ) {
      bool crit = temperature >= SEUIL_TEMP_C;
      if (crit) alerteCritique = true; else alerteWarning = true;
      if (!etatTemp || now - tEmailTemp >= EMAIL_COOLDOWN) {
        envoyerAlerte("TEMPERATURE", crit ? "CRITIQUE" : "WARNING",
          "Surchauffe detectee dans la salle serveurs");
        tEmailTemp = now;
      }
      etatTemp = true;
    }
  } else { cntTemp = 0; etatTemp = false; }

  // ── Humidité ─────────────────────────────────────────────
  if (humidite >= SEUIL_HUM_W) {
    if (++cntHum >= DEBOUNCE_REQ) {
      bool crit = humidite >= SEUIL_HUM_C;
      if (crit) alerteCritique = true; else alerteWarning = true;
      if (!etatHum || now - tEmailHum >= EMAIL_COOLDOWN) {
        envoyerAlerte("HUMIDITE", crit ? "CRITIQUE" : "WARNING",
          "Humidite excessive detectee");
        tEmailHum = now;
      }
      etatHum = true;
    }
  } else { cntHum = 0; etatHum = false; }

  // ── Gaz / Qualité air ────────────────────────────────────
  if (gaz >= SEUIL_GAZ_W) {
    if (++cntGaz >= DEBOUNCE_REQ) {
      bool crit = gaz >= SEUIL_GAZ_C;
      if (crit) alerteCritique = true; else alerteWarning = true;
      if (!etatGaz || now - tEmailGaz >= EMAIL_COOLDOWN) {
        envoyerAlerte("GAZ", crit ? "CRITIQUE" : "WARNING",
          "Gaz dangereux detecte dans la salle");
        tEmailGaz = now;
      }
      etatGaz = true;
    }
  } else { cntGaz = 0; etatGaz = false; }

  if (pir == HIGH && PIR_ACTIF) alerteCritique = true;

  // ── Mode buzzer + LED ────────────────────────────────────
  if (alerteCritique) {
    if (buzzerMode != 3) { buzzerMode = 2; buzzerPhase = 0; tBuzz = millis(); }
    if (ledMode    != 2) { ledMode    = 2; ledEtat = true;  tLedBlink = millis(); }
  } else if (alerteWarning) {
    if (buzzerMode < 1)  { buzzerMode = 1; buzzerPhase = 0; tBuzz = millis(); }
    if (ledMode    < 1)  { ledMode    = 1; ledEtat = true;  tLedBlink = millis(); }
  } else {
    if (buzzerMode != 3 || pir == LOW) buzzerMode = 0;
    if (ledMode    != 2 || pir == LOW) ledMode    = 0;
  }
}

// ╔══════════════════════════════════════════════════════════╗
// ║  BUZZER NON-BLOQUANT — tone()                             ║
// ╚══════════════════════════════════════════════════════════╝
void gererBuzzer() {
  unsigned long now = millis();

  if (buzzerMode == 0) {
    noTone(BUZZER_PIN); buzzerEtat = false; buzzerPhase = 0; return;
  }
  if (buzzerMode == 1) {
    const unsigned long d[] = {200, 200, 200, 200, 2000};
    if (now - tBuzz >= d[buzzerPhase]) {
      tBuzz = now; buzzerPhase = (buzzerPhase + 1) % 5;
      bool on = (buzzerPhase == 0 || buzzerPhase == 2);
      if (on) tone(BUZZER_PIN, 440); else noTone(BUZZER_PIN);
    }
    return;
  }
  if (buzzerMode == 2) {
    unsigned long dur = buzzerEtat ? 150 : 100;
    if (now - tBuzz >= dur) {
      tBuzz = now; buzzerEtat = !buzzerEtat;
      if (buzzerEtat) tone(BUZZER_PIN, 880); else noTone(BUZZER_PIN);
    }
    return;
  }
  if (buzzerMode == 3) {
    const unsigned long d[] = {80, 100, 80, 100, 80, 1000};
    if (now - tBuzz >= d[buzzerPhase]) {
      tBuzz = now; buzzerPhase = (buzzerPhase + 1) % 6;
      bool on = (buzzerPhase == 0 || buzzerPhase == 2 || buzzerPhase == 4);
      if (on) tone(BUZZER_PIN, 1200); else noTone(BUZZER_PIN);
    }
    if (pir == LOW && buzzerPhase == 0) buzzerMode = 0;
    return;
  }
}

// ╔══════════════════════════════════════════════════════════╗
// ║  LED CLIGNOTANTE NON-BLOQUANTE                            ║
// ╚══════════════════════════════════════════════════════════╝
void gererLED() {
  unsigned long now = millis();
  if (ledMode == 0) { ledVerte(); return; }
  if (ledMode == 1) {
    if (now - tLedBlink >= 500) {
      tLedBlink = now; ledEtat = !ledEtat;
      if (ledEtat) ledOrange(); else ledEteinte();
    }
    return;
  }
  if (ledMode == 2) {
    if (now - tLedBlink >= 150) {
      tLedBlink = now; ledEtat = !ledEtat;
      if (ledEtat) ledRouge(); else ledEteinte();
    }
    return;
  }
}

// ╔══════════════════════════════════════════════════════════╗
// ║  ENVOI JSON SÉRIE                                         ║
// ╚══════════════════════════════════════════════════════════╝
void envoyerLive() {
  char tB[8], hB[8];
  dtostrf(temperature, 4, 1, tB);
  dtostrf(humidite,    4, 1, hB);
  snprintf(jsonBuf, sizeof(jsonBuf),
    "{\"type\":\"live\","
    "\"salle_id\":%d,\"equipement_id\":%d,"
    "\"temperature\":%s,\"humidite\":%s,\"gaz\":%d,\"pir\":%d}",
    SALLE_ID, EQUIPEMENT_ID, tB, hB, gaz, pir);
  Serial.println(jsonBuf);
}

void envoyerDonnees() {
  char tB[8], hB[8];
  dtostrf(temperature, 4, 1, tB);
  dtostrf(humidite,    4, 1, hB);
  snprintf(jsonBuf, sizeof(jsonBuf),
    "{\"type\":\"donnees\","
    "\"salle_id\":%d,\"equipement_id\":%d,"
    "\"temperature\":%s,\"humidite\":%s,\"gaz\":%d,\"pir\":%d}",
    SALLE_ID, EQUIPEMENT_ID, tB, hB, gaz, pir);
  Serial.println(jsonBuf);
}

void envoyerAlerte(const char* cat, const char* niv, const char* msg) {
  char tB[8], hB[8];
  dtostrf(temperature, 4, 1, tB);
  dtostrf(humidite,    4, 1, hB);
  snprintf(jsonBuf, sizeof(jsonBuf),
    "{\"type\":\"alerte\","
    "\"salle_id\":%d,\"equipement_id\":%d,"
    "\"categorie\":\"%s\",\"niveau\":\"%s\",\"message\":\"%s\","
    "\"temperature\":%s,\"humidite\":%s,\"gaz\":%d}",
    SALLE_ID, EQUIPEMENT_ID, cat, niv, msg, tB, hB, gaz);
  Serial.println(jsonBuf);
}

// ╔══════════════════════════════════════════════════════════╗
// ║  UTILITAIRES                                              ║
// ╚══════════════════════════════════════════════════════════╝
void afficherSerie() {
  Serial.println("---");
  Serial.print("T:"); Serial.print(temperature); Serial.print("C  ");
  Serial.print("H:"); Serial.print(humidite);    Serial.print("%  ");
  Serial.print("G:"); Serial.print(gaz);          Serial.print("ppm  ");
  Serial.print("PIR:"); Serial.println(pir);
}

void ledVerte()  { analogWrite(LED_R,   0); analogWrite(LED_G, 255); analogWrite(LED_B,   0); }
void ledRouge()  { analogWrite(LED_R, 255); analogWrite(LED_G,   0); analogWrite(LED_B,   0); }
void ledOrange() { analogWrite(LED_R, 255); analogWrite(LED_G,  80); analogWrite(LED_B,   0); }
void ledEteinte(){ analogWrite(LED_R,   0); analogWrite(LED_G,   0); analogWrite(LED_B,   0); }

void beepBloquant(int ms) {
  digitalWrite(BUZZER_PIN, HIGH); delay(ms); digitalWrite(BUZZER_PIN, LOW);
}
