/*
 * SupServer — Surveillance Salle Serveurs IoT
 * Capteurs : DHT22, MQ135, PIR (HC-SR501), ACS712 (30A)
 * GSM      : SIM900 via SoftwareSerial (SMS + HTTP POST)
 * Cible    : Arduino Mega / Uno (recommandé Mega pour mémoire)
 *
 * Câblage :
 *   DHT22    → pin 7  (DATA)
 *   MQ135    → A0     (AOUT)
 *   PIR      → pin 8  (OUT)
 *   ACS712   → A1     (OUT)  — modèle 30A : sensibilité 66 mV/A
 *   SIM900 TX → pin 11 (RX Arduino)
 *   SIM900 RX → pin 10 (TX Arduino)
 *   SIM900 PWR → pin 9 (HIGH 1s pour allumer)
 */

#include <SoftwareSerial.h>
#include <DHT.h>

// ── Pins ──────────────────────────────────────────────────
#define DHT_PIN       7
#define DHT_TYPE      DHT22
#define MQ135_PIN     A0
#define PIR_PIN       8
#define ACS712_PIN    A1
#define SIM900_TX     10
#define SIM900_RX     11
#define SIM900_PWR    9

// ── Config réseau ─────────────────────────────────────────
const char APN[]        = "internet";          // APN opérateur
const char SERVER_HOST[] = "votre-domaine.com"; // remplacer par IP/domaine réel
const int  SERVER_PORT  = 80;
const char API_PATH[]   = "/api/capteurs";
const char SMS_NUMERO[] = "+237687988340";

// ── Seuils d'alerte ───────────────────────────────────────
const float TEMP_WARN  = 35.0,  TEMP_CRIT  = 40.0;
const float HUM_WARN   = 75.0,  HUM_CRIT   = 85.0;
const float GAZ_WARN   = 300.0, GAZ_CRIT   = 500.0;
const float COUR_WARN  = 10.0,  COUR_CRIT  = 15.0;
const float PUIS_WARN  = 3000.0, PUIS_CRIT = 5000.0;
const float TENSION    = 220.0; // tension secteur constante (à mesurer si transformateur)

// ── Intervalles ───────────────────────────────────────────
const unsigned long ENVOI_INTERVAL    = 10000;  // envoi HTTP toutes les 10s
const unsigned long ALERTE_COOLDOWN   = 300000; // SMS max 1 fois toutes les 5 min par type

DHT           dht(DHT_PIN, DHT_TYPE);
SoftwareSerial sim900(SIM900_RX, SIM900_TX);

unsigned long dernierEnvoi   = 0;
unsigned long dernierSMSTemp = 0;
unsigned long dernierSMSGaz  = 0;
unsigned long dernierSMSCour = 0;
unsigned long dernierSMSPir  = 0;
bool          pirPrecedent   = false;


// ── Setup ─────────────────────────────────────────────────
void setup() {
  Serial.begin(9600);
  sim900.begin(9600);
  dht.begin();
  pinMode(PIR_PIN, INPUT);
  pinMode(SIM900_PWR, OUTPUT);

  Serial.println(F("SupServer IoT — Démarrage..."));
  allumerSIM900();
  delay(3000);
  initGSM();
  Serial.println(F("Prêt."));
}


// ── Loop ──────────────────────────────────────────────────
void loop() {
  float temperature = dht.readTemperature();
  float humidite    = dht.readHumidity();
  float gaz         = lireGaz();
  float courant     = lireCourant();
  float puissance   = courant * TENSION;
  bool  pir         = digitalRead(PIR_PIN) == HIGH;

  if (isnan(temperature)) temperature = 0;
  if (isnan(humidite))    humidite    = 0;

  Serial.print(F("T=")); Serial.print(temperature);
  Serial.print(F(" H=")); Serial.print(humidite);
  Serial.print(F(" G=")); Serial.print(gaz);
  Serial.print(F(" I=")); Serial.print(courant);
  Serial.print(F(" P=")); Serial.print(puissance);
  Serial.print(F(" PIR=")); Serial.println(pir ? F("OUI") : F("NON"));

  // Alertes SMS
  unsigned long now = millis();
  verifierAlerteTemp(temperature, now);
  verifierAlerteGaz(gaz, now);
  verifierAlerteCourant(courant, puissance, now);
  verifierAlertePIR(pir, now);
  pirPrecedent = pir;

  // Envoi HTTP toutes les ENVOI_INTERVAL ms
  if (now - dernierEnvoi >= ENVOI_INTERVAL) {
    dernierEnvoi = now;
    envoyerHTTP(temperature, humidite, gaz, courant, puissance, TENSION, pir);
  }

  delay(1000);
}


// ── Lecture MQ135 → ppm approximatif ─────────────────────
float lireGaz() {
  int raw = analogRead(MQ135_PIN);
  return map(raw, 0, 1023, 0, 1000);
}


// ── Lecture ACS712 30A ────────────────────────────────────
float lireCourant() {
  long somme = 0;
  for (int i = 0; i < 100; i++) {
    somme += analogRead(ACS712_PIN);
    delayMicroseconds(100);
  }
  float moy    = somme / 100.0;
  float volts  = (moy / 1024.0) * 5.0;
  float courant = (volts - 2.5) / 0.066; // 66 mV/A pour ACS712-30A
  return abs(courant);
}


// ── Alertes SMS ───────────────────────────────────────────
void verifierAlerteTemp(float t, unsigned long now) {
  if (t >= TEMP_CRIT && now - dernierSMSTemp > ALERTE_COOLDOWN) {
    dernierSMSTemp = now;
    String msg = "ALERTE CRITIQUE TEMPERATURE\n";
    msg += "Valeur: "; msg += t; msg += "°C\n";
    msg += "Seuil critique: "; msg += TEMP_CRIT; msg += "°C\n";
    msg += "RISQUE: Surchauffe serveurs\n";
    msg += "ACTION: Verifier climatisation\n";
    msg += "SupServer IoT";
    envoyerSMS(msg);
  } else if (t >= TEMP_WARN && now - dernierSMSTemp > ALERTE_COOLDOWN) {
    dernierSMSTemp = now;
    String msg = "AVERTISSEMENT TEMPERATURE\n";
    msg += "Valeur: "; msg += t; msg += "°C\n";
    msg += "Surveiller le refroidissement\nSupServer IoT";
    envoyerSMS(msg);
  }
}

void verifierAlerteGaz(float g, unsigned long now) {
  if (g >= GAZ_CRIT && now - dernierSMSGaz > ALERTE_COOLDOWN) {
    dernierSMSGaz = now;
    String msg = "ALERTE CRITIQUE GAZ\n";
    msg += "Valeur: "; msg += g; msg += " ppm\n";
    msg += "RISQUE: Fuite danger - incendie\n";
    msg += "ACTION: Evacuer - couper alim\nSupServer IoT";
    envoyerSMS(msg);
  } else if (g >= GAZ_WARN && now - dernierSMSGaz > ALERTE_COOLDOWN) {
    dernierSMSGaz = now;
    String msg = "AVERTISSEMENT GAZ\n";
    msg += "Valeur: "; msg += g; msg += " ppm\n";
    msg += "Surveiller ventilation\nSupServer IoT";
    envoyerSMS(msg);
  }
}

void verifierAlerteCourant(float c, float p, unsigned long now) {
  if (c >= COUR_CRIT && now - dernierSMSCour > ALERTE_COOLDOWN) {
    dernierSMSCour = now;
    String msg = "ALERTE CRITIQUE COURANT\n";
    msg += "Courant: "; msg += c; msg += "A\n";
    msg += "Puissance: "; msg += p; msg += "W\n";
    msg += "RISQUE: Court-circuit\n";
    msg += "ACTION: Reduire charge\nSupServer IoT";
    envoyerSMS(msg);
  }
}

void verifierAlertePIR(bool pir, unsigned long now) {
  if (pir && !pirPrecedent && now - dernierSMSPir > ALERTE_COOLDOWN) {
    dernierSMSPir = now;
    String msg = "ALERTE MOUVEMENT DETECTE\n";
    msg += "Intrusion detectee\nsalle serveurs\n";
    msg += "SupServer IoT";
    envoyerSMS(msg);
  }
}


// ── Envoi SMS via SIM900 ──────────────────────────────────
void envoyerSMS(String message) {
  Serial.println(F("Envoi SMS..."));
  sim900.print(F("AT+CMGF=1\r"));
  delay(200);
  sim900.print(F("AT+CMGS=\""));
  sim900.print(SMS_NUMERO);
  sim900.print(F("\"\r"));
  delay(200);
  sim900.print(message);
  sim900.write(26); // Ctrl+Z
  delay(3000);
  Serial.println(F("SMS envoyé."));
}


// ── Envoi HTTP POST via SIM900 ────────────────────────────
void envoyerHTTP(float temp, float hum, float gaz, float courant, float puis, float tension, bool pir) {
  Serial.println(F("Envoi HTTP..."));

  // Corps JSON
  String body = "{\"temperature\":";
  body += temp;   body += ",\"humidite\":";
  body += hum;    body += ",\"gaz\":";
  body += gaz;    body += ",\"courant\":";
  body += courant; body += ",\"puissance\":";
  body += puis;   body += ",\"tension\":";
  body += tension; body += ",\"pir\":";
  body += (pir ? "1" : "0");
  body += "}";

  // Requête HTTP
  String req = "POST ";
  req += API_PATH;
  req += " HTTP/1.1\r\nHost: ";
  req += SERVER_HOST;
  req += "\r\nContent-Type: application/json\r\nContent-Length: ";
  req += body.length();
  req += "\r\nConnection: close\r\n\r\n";
  req += body;

  // Ouvrir connexion TCP
  sim900.print(F("AT+CIPSHUT\r")); delay(1000);
  sim900.print(F("AT+CIPMUX=0\r")); delay(500);
  sim900.print(F("AT+CSTT=\"")); sim900.print(APN); sim900.print(F("\"\r")); delay(1000);
  sim900.print(F("AT+CIICR\r")); delay(3000);
  sim900.print(F("AT+CIFSR\r")); delay(1000);

  sim900.print(F("AT+CIPSTART=\"TCP\",\""));
  sim900.print(SERVER_HOST);
  sim900.print(F("\",\""));
  sim900.print(SERVER_PORT);
  sim900.print(F("\"\r"));
  delay(3000);

  if (attendreReponse("CONNECT", 5000)) {
    sim900.print(F("AT+CIPSEND="));
    sim900.print(req.length());
    sim900.print(F("\r"));
    delay(1000);
    sim900.print(req);
    delay(3000);
    sim900.print(F("AT+CIPCLOSE\r"));
    delay(1000);
    Serial.println(F("HTTP envoyé."));
  } else {
    Serial.println(F("Connexion TCP échouée."));
  }
}


// ── Attendre réponse GSM ──────────────────────────────────
bool attendreReponse(const char* attendu, unsigned long timeout) {
  unsigned long debut = millis();
  String reponse = "";
  while (millis() - debut < timeout) {
    if (sim900.available()) {
      char c = sim900.read();
      reponse += c;
      if (reponse.indexOf(attendu) >= 0) return true;
    }
  }
  return false;
}


// ── Allumer SIM900 ────────────────────────────────────────
void allumerSIM900() {
  digitalWrite(SIM900_PWR, HIGH);
  delay(1200);
  digitalWrite(SIM900_PWR, LOW);
  delay(3000);
}


// ── Init GSM (APN, SMS text mode) ────────────────────────
void initGSM() {
  sim900.print(F("AT\r")); delay(500);
  sim900.print(F("AT+CMGF=1\r")); delay(500);
  sim900.print(F("AT+CGDCONT=1,\"IP\",\"")); sim900.print(APN); sim900.print(F("\"\r")); delay(500);
  Serial.println(F("GSM initialisé."));
}
