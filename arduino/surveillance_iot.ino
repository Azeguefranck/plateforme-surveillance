/*
 * ================================================================
 *  PLATEFORME IoT - SURVEILLANCE SALLE SERVEUR
 *  Arduino UNO + SIM900 + DHT22 + MQ135 + PIR + ACS712
 * ================================================================
 *
 *  CÂBLAGE :
 *  - DHT22    → Pin 2  (DATA)
 *  - MQ135    → A0     (ANALOG)
 *  - PIR HC-SR501 → Pin 3  (DIGITAL)
 *  - ACS712   → A1     (ANALOG, 5A/20A/30A selon modèle)
 *  - SIM900   → Pins 7 (RX) / 8 (TX) via SoftwareSerial
 *  - LED Rouge→ Pin 13 (alerte)
 *  - LED Vert → Pin 12 (normal)
 *
 *  CONFIGURATION :
 *  Modifier les constantes ci-dessous selon votre installation.
 * ================================================================
 */

#include <SoftwareSerial.h>
#include <DHT.h>

// ── CONFIGURATION ────────────────────────────────────────────────

#define DHTPIN        2
#define DHTTYPE       DHT22
#define PIR_PIN       3
#define MQ135_PIN     A0
#define ACS712_PIN    A1
#define LED_ALERTE    13
#define LED_OK        12

// API Laravel
const char SERVER_HOST[] = "10.242.221.236";  // <- votre IP
const int  SERVER_PORT   = 80;
const char API_KEY[]     = "arduino_surv_2026_secret";
const int  SALLE_ID      = 1;

// Numéro admin (carte SIM +237...)
const char ADMIN_SMS[]   = "+237687988340";

// Intervalle envoi données (ms)
const unsigned long INTERVAL = 5000;

// ── OBJETS ───────────────────────────────────────────────────────

SoftwareSerial sim900(7, 8);  // RX=7, TX=8
DHT dht(DHTPIN, DHTTYPE);

// ── VARIABLES ─────────────────────────────────────────────────────

unsigned long dernierEnvoi = 0;
bool sim900Pret = false;
int nbAlertesSMS = 0;

// ── SETUP ────────────────────────────────────────────────────────

void setup() {
  Serial.begin(9600);
  sim900.begin(9600);
  dht.begin();

  pinMode(PIR_PIN,    INPUT);
  pinMode(LED_ALERTE, OUTPUT);
  pinMode(LED_OK,     OUTPUT);

  digitalWrite(LED_OK,     HIGH);
  digitalWrite(LED_ALERTE, LOW);

  Serial.println(F("=== SURVEILLANCE IoT ==="));
  Serial.println(F("Initialisation..."));

  delay(3000);
  initSIM900();
}

// ── LOOP ─────────────────────────────────────────────────────────

void loop() {
  unsigned long maintenant = millis();

  if (maintenant - dernierEnvoi >= INTERVAL) {
    dernierEnvoi = maintenant;

    // Lecture capteurs
    float temperature = lireTemperature();
    float humidite    = lireHumidite();
    int   gaz         = lireGaz();
    float courant     = lireCourant();
    float tension     = 220.0;  // tension fixe ou via capteur ZMPT101B
    float puissance   = courant * tension;
    int   pir         = digitalRead(PIR_PIN);

    // Affichage série
    afficherDonnees(temperature, humidite, gaz, courant, puissance, pir);

    // Envoi API Laravel
    String reponse = envoyerDonnees(temperature, humidite, gaz, courant, tension, puissance, pir);

    // Analyse réponse et SMS si alerte
    if (reponse.length() > 0) {
      traiterReponse(reponse, temperature, humidite, gaz, courant, pir);
    }
  }
}

// ── LECTURES CAPTEURS ─────────────────────────────────────────────

float lireTemperature() {
  float t = dht.readTemperature();
  if (isnan(t)) {
    Serial.println(F("[DHT22] Erreur lecture temperature"));
    return 0.0;
  }
  return t;
}

float lireHumidite() {
  float h = dht.readHumidity();
  if (isnan(h)) {
    Serial.println(F("[DHT22] Erreur lecture humidite"));
    return 0.0;
  }
  return h;
}

int lireGaz() {
  int raw = analogRead(MQ135_PIN);
  // Conversion approximative en ppm (calibration selon votre capteur)
  int ppm = map(raw, 0, 1023, 0, 1000);
  return ppm;
}

float lireCourant() {
  // ACS712 5A : sensibilité 185mV/A — pour 20A : 100mV/A — pour 30A : 66mV/A
  int raw = analogRead(ACS712_PIN);
  float tension_mv = (raw / 1023.0) * 5000.0;  // en millivolts
  float courant = (tension_mv - 2500.0) / 185.0; // ACS712-5A
  if (courant < 0) courant = 0;
  return courant;
}

// ── AFFICHAGE SÉRIE ───────────────────────────────────────────────

void afficherDonnees(float t, float h, int g, float c, float p, int pir) {
  Serial.println(F("--- Mesures ---"));
  Serial.print(F("Temp: ")); Serial.print(t); Serial.println(F(" C"));
  Serial.print(F("Hum:  ")); Serial.print(h); Serial.println(F(" %"));
  Serial.print(F("Gaz:  ")); Serial.print(g); Serial.println(F(" ppm"));
  Serial.print(F("Cur:  ")); Serial.print(c); Serial.println(F(" A"));
  Serial.print(F("Pwr:  ")); Serial.print(p); Serial.println(F(" W"));
  Serial.print(F("PIR:  ")); Serial.println(pir ? F("DETECTE !") : F("Aucun"));
}

// ── INIT SIM900 ───────────────────────────────────────────────────

void initSIM900() {
  Serial.println(F("[SIM900] Initialisation..."));

  // Test AT
  envoyerAT("AT", 2000);
  delay(500);
  envoyerAT("ATE0", 1000);       // Désactiver écho
  delay(500);
  envoyerAT("AT+CMGF=1", 1000); // Mode texte SMS
  delay(500);
  envoyerAT("AT+CSCS=\"GSM\"", 1000); // Encodage GSM

  // Vérifier réseau
  String rep = envoyerAT("AT+CREG?", 3000);
  if (rep.indexOf("+CREG: 0,1") >= 0 || rep.indexOf("+CREG: 0,5") >= 0) {
    sim900Pret = true;
    Serial.println(F("[SIM900] Réseau OK"));
    digitalWrite(LED_OK, HIGH);
  } else {
    Serial.println(F("[SIM900] Pas de réseau"));
  }
}

// ── ENVOI AT COMMAND ──────────────────────────────────────────────

String envoyerAT(const char* cmd, unsigned long timeout) {
  sim900.println(cmd);
  delay(100);
  unsigned long debut = millis();
  String reponse = "";
  while (millis() - debut < timeout) {
    while (sim900.available()) {
      reponse += (char) sim900.read();
    }
    if (reponse.indexOf("OK") >= 0 || reponse.indexOf("ERROR") >= 0) break;
  }
  return reponse;
}

// ── ENVOI HTTP POST vers API Laravel ─────────────────────────────

String envoyerDonnees(float t, float h, int g, float c, float ten, float p, int pir) {
  if (!sim900Pret) {
    Serial.println(F("[HTTP] SIM900 non prêt"));
    return "";
  }

  // Construire JSON
  String json = "{";
  json += "\"api_key\":\"" + String(API_KEY) + "\",";
  json += "\"salle_id\":" + String(SALLE_ID) + ",";
  json += "\"temperature\":" + String(t, 2) + ",";
  json += "\"humidite\":" + String(h, 2) + ",";
  json += "\"gaz\":" + String(g) + ",";
  json += "\"courant\":" + String(c, 3) + ",";
  json += "\"tension\":" + String(ten, 1) + ",";
  json += "\"puissance\":" + String(p, 2) + ",";
  json += "\"pir\":" + String(pir);
  json += "}";

  int len = json.length();

  Serial.println(F("[HTTP] Ouverture connexion..."));

  // Ouvrir connexion TCP
  String cmd = "AT+CIPSTART=\"TCP\",\"";
  cmd += SERVER_HOST;
  cmd += "\",";
  cmd += SERVER_PORT;
  String rep = envoyerAT(cmd.c_str(), 5000);
  if (rep.indexOf("ERROR") >= 0 && rep.indexOf("ALREADY") < 0) {
    Serial.println(F("[HTTP] Erreur connexion TCP"));
    envoyerAT("AT+CIPCLOSE", 2000);
    return "";
  }

  delay(1000);

  // Préparer envoi données
  cmd = "AT+CIPSEND=";
  String requete = "POST /api/arduino/data HTTP/1.1\r\n";
  requete += "Host: "; requete += SERVER_HOST; requete += "\r\n";
  requete += "Content-Type: application/json\r\n";
  requete += "Content-Length: "; requete += String(len); requete += "\r\n";
  requete += "Connection: close\r\n\r\n";
  requete += json;

  cmd += String(requete.length());
  sim900.println(cmd);
  delay(500);

  // Attendre >
  unsigned long t0 = millis();
  bool pret = false;
  while (millis() - t0 < 3000) {
    if (sim900.available() && sim900.read() == '>') { pret = true; break; }
  }

  if (!pret) {
    Serial.println(F("[HTTP] Timeout attente >"));
    envoyerAT("AT+CIPCLOSE", 2000);
    return "";
  }

  sim900.print(requete);

  // Lire réponse
  String reponse = "";
  t0 = millis();
  while (millis() - t0 < 8000) {
    while (sim900.available()) {
      reponse += (char) sim900.read();
    }
    if (reponse.indexOf("CLOSED") >= 0 || reponse.indexOf("\r\n\r\n") >= 0) break;
  }

  envoyerAT("AT+CIPCLOSE", 2000);

  // Extraire corps JSON
  int bodyStart = reponse.indexOf("\r\n\r\n");
  if (bodyStart >= 0) {
    String body = reponse.substring(bodyStart + 4);
    Serial.println(F("[HTTP] Réponse API:"));
    Serial.println(body);
    return body;
  }

  return reponse;
}

// ── ANALYSE RÉPONSE + SMS ─────────────────────────────────────────

void traiterReponse(String body, float t, float h, int g, float c, int pir) {
  bool alerteActive = body.indexOf("\"alerte_active\":true") >= 0;
  bool envoyerSMS   = body.indexOf("\"envoyer_sms\":true") >= 0;

  // Niveau global
  bool estCritique = body.indexOf("\"niveau\":\"CRITIQUE\"") >= 0;

  if (estCritique) {
    digitalWrite(LED_ALERTE, HIGH);
    digitalWrite(LED_OK,     LOW);
    clignoterLED(LED_ALERTE, 3);
  } else if (alerteActive) {
    digitalWrite(LED_ALERTE, HIGH);
    clignoterLED(LED_ALERTE, 1);
  } else {
    digitalWrite(LED_ALERTE, LOW);
    digitalWrite(LED_OK,     HIGH);
  }

  // Envoi SMS si demandé
  if (envoyerSMS && sim900Pret) {
    // Extraire numéros depuis JSON (simplification: toujours le numéro admin)
    String message = construireSMS(t, h, g, c, pir, estCritique);
    envoyerSMS_GSM(ADMIN_SMS, message);
    nbAlertesSMS++;
  }
}

// ── CONSTRUCTION MESSAGE SMS ──────────────────────────────────────

String construireSMS(float t, float h, int g, float c, int pir, bool critique) {
  String msg = critique ? "ALERTE CRITIQUE\n" : "ALERTE SERVEUR\n";
  msg += "Salle: " + String(SALLE_ID) + "\n";

  if (t >= 40)  { msg += "Temp: " + String(t, 1) + "C !\n"; }
  if (g >= 500) { msg += "Gaz: "  + String(g) + "ppm !\n"; }
  if (c >= 15)  { msg += "Cur: "  + String(c, 1) + "A !\n"; }
  if (pir)      { msg += "Mouvement detecte !\n"; }

  msg += "\nRISQUES: ";
  if (t >= 40)  msg += "surchauffe ";
  if (g >= 500) msg += "incendie ";
  if (pir)      msg += "intrusion ";

  msg += "\nSOLUTIONS: ";
  if (t >= 40)  msg += "activer ventilation ";
  if (g >= 500) msg += "evacuer salle ";
  if (pir)      msg += "alerter securite ";

  // Limiter à 160 chars pour 1 SMS
  if (msg.length() > 160) msg = msg.substring(0, 157) + "...";

  return msg;
}

// ── ENVOI SMS ─────────────────────────────────────────────────────

void envoyerSMS_GSM(const char* numero, String message) {
  if (!sim900Pret) return;

  Serial.println(F("[SMS] Envoi SMS..."));
  Serial.print(F("[SMS] A: ")); Serial.println(numero);
  Serial.println(message);

  // Mode texte
  envoyerAT("AT+CMGF=1", 1000);
  delay(300);

  // Numéro destinataire
  String cmd = "AT+CMGS=\"";
  cmd += numero;
  cmd += "\"";
  sim900.println(cmd);
  delay(500);

  // Attendre >
  unsigned long t0 = millis();
  bool pret = false;
  while (millis() - t0 < 3000) {
    if (sim900.available() && sim900.read() == '>') { pret = true; break; }
  }

  if (!pret) {
    Serial.println(F("[SMS] Erreur: pas de >"));
    return;
  }

  // Envoyer message + Ctrl+Z (0x1A)
  sim900.print(message);
  sim900.write(0x1A);
  delay(5000);

  // Lire confirmation
  String rep = "";
  t0 = millis();
  while (millis() - t0 < 5000) {
    while (sim900.available()) rep += (char) sim900.read();
    if (rep.indexOf("+CMGS:") >= 0) { Serial.println(F("[SMS] Envoyé !")); break; }
    if (rep.indexOf("ERROR") >= 0)  { Serial.println(F("[SMS] Erreur envoi")); break; }
  }
}

// ── CLIGNOTEMENT LED ──────────────────────────────────────────────

void clignoterLED(int pin, int nb) {
  for (int i = 0; i < nb; i++) {
    digitalWrite(pin, HIGH); delay(200);
    digitalWrite(pin, LOW);  delay(200);
  }
  digitalWrite(pin, HIGH);
}
