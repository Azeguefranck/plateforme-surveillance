#include <DHT.h>
#include <SoftwareSerial.h>

// ╔══════════════════════════════════════════════════╗
// ║         ⚙  CONFIGURATION — MODIFIEZ ICI         ║
// ╚══════════════════════════════════════════════════╝
const String SERVER_IP   = "lego-sanitizer-hexagram.ngrok-free.dev";
const int    SERVER_PORT = 80;
const int    ROOM_ID     = 1;
const String APN         = "orangecm";      // APN SIM (orangecm / mtn / internet)

// ╔══════════════════════════════════════════════════╗
// ║                    BROCHES                       ║
// ╚══════════════════════════════════════════════════╝
#define DHT_PIN       2
#define DHT_TYPE      DHT22
#define PIR_PIN       3
#define GSM_PWRKEY    4
#define PZEM_RX       5
#define PZEM_TX       6
#define GSM_TX        7
#define GSM_RX        8
#define BUZZER_PIN    9
#define LED_R_PIN    10
#define LED_G_PIN    11
#define LED_B_PIN    12
#define MQ135_PIN    A0

// ╔══════════════════════════════════════════════════╗
// ║               SEUILS D'ALERTE                    ║
// ╚══════════════════════════════════════════════════╝
#define TEMP_ATTN  28.0f
#define TEMP_DANG  32.0f
#define TEMP_CRIT  40.0f
#define GAZ_ATTN    400
#define GAZ_DANG    600
#define GAZ_CRIT   1000
#define HUM_ATTN     65
#define HUM_DANG     75
#define HUM_CRIT     85

#define SEND_INTERVAL 10000UL

// ╔══════════════════════════════════════════════════╗
// ║                    OBJETS                        ║
// ╚══════════════════════════════════════════════════╝
DHT            dht(DHT_PIN, DHT_TYPE);
SoftwareSerial gsmSerial(GSM_RX, GSM_TX);
SoftwareSerial pzemSerial(PZEM_RX, PZEM_TX);

// ╔══════════════════════════════════════════════════╗
// ║                   VARIABLES                      ║
// ╚══════════════════════════════════════════════════╝
float    temperature = 25.0;
float    humidity    = 50.0;
float    voltage     = 0.0;
float    current_a   = 0.0;
float    power_w     = 0.0;
int      gasLevel    = 350;
bool     pirDetected = false;

int      gasBuf[5]   = {350,350,350,350,350};
uint8_t  gasIdx      = 0;
uint8_t  prevLevel   = 0;
uint32_t sendCount   = 0;
unsigned long lastSend = 0;
bool     gsmReady    = false;

// ╔══════════════════════════════════════════════════╗
// ║              PROTOTYPES FONCTIONS                ║
// ╚══════════════════════════════════════════════════╝
void     lireCapteurs();
void     lirePZEM();
uint8_t  calculerNiveau();
void     mettreAJourLED(uint8_t n);
void     gererBuzzer(uint8_t n);
void     pipDanger();
void     printJSON();
bool     envoyerGSM();
void     powerOnGSM();
bool     initGSM();
void     sendAT(const __FlashStringHelper* cmd, int ms);
String   sendATread(const __FlashStringHelper* cmd, int ms);
String   readGSM(int ms);
void     setLED(uint8_t r, uint8_t g, uint8_t b);
const char* niveauStr(uint8_t n);

// ╔══════════════════════════════════════════════════╗
// ║                     SETUP                        ║
// ╚══════════════════════════════════════════════════╝
void setup() {
  Serial.begin(9600);

  pinMode(BUZZER_PIN, OUTPUT); digitalWrite(BUZZER_PIN, LOW);
  pinMode(LED_R_PIN,  OUTPUT);
  pinMode(LED_G_PIN,  OUTPUT);
  pinMode(LED_B_PIN,  OUTPUT);
  pinMode(PIR_PIN,    INPUT);

  setLED(0, 0, 80);

  dht.begin();
  gsmSerial.begin(9600);

  Serial.println(F("===================================="));
  Serial.println(F("  SURVEILLANCE v3.0 — Demarrage..."));
  Serial.println(F("===================================="));

  delay(2000);
  float t = dht.readTemperature();
  if (isnan(t)) Serial.println(F("[DHT22] ERREUR — verifier cablage pin 2"));
  else { Serial.print(F("[DHT22] OK — ")); Serial.print(t,1); Serial.println(F(" C")); }

  powerOnGSM();
  gsmReady = initGSM();

  if (gsmReady) {
    setLED(0, 80, 0);
    Serial.println(F("[SYS] Mode GSM + USB actif"));
  } else {
    setLED(0, 0, 80);
    Serial.println(F("[SYS] Mode USB uniquement — lancez: python3 arduino_live.py"));
  }
}

// ╔══════════════════════════════════════════════════╗
// ║                      LOOP                        ║
// ╚══════════════════════════════════════════════════╝
void loop() {
  if (millis() - lastSend >= SEND_INTERVAL) {
    lastSend = millis();
    sendCount++;

    lireCapteurs();

    uint8_t niveau = calculerNiveau();
    mettreAJourLED(niveau);
    gererBuzzer(niveau);
    prevLevel = niveau;

    printJSON();

    if (gsmReady) {
      bool ok = envoyerGSM();
      if (!ok && sendCount % 12 == 0) gsmReady = initGSM();
    }

    Serial.print(F("  #")); Serial.print(sendCount);
    Serial.print(F(" | T:")); Serial.print(temperature,1); Serial.print(F("C"));
    Serial.print(F(" H:"));   Serial.print(humidity,1);    Serial.print(F("%"));
    Serial.print(F(" G:"));   Serial.print(gasLevel);      Serial.print(F("ppm"));
    if (voltage > 0) {
      Serial.print(F(" V:")); Serial.print(voltage,1);  Serial.print(F("V"));
      Serial.print(F(" P:")); Serial.print(power_w,1);  Serial.print(F("W"));
    }
    Serial.print(F(" ["));
    Serial.print(niveauStr(niveau));
    Serial.println(F("]"));
  }
}

// ╔══════════════════════════════════════════════════╗
// ║               LECTURE CAPTEURS                   ║
// ╚══════════════════════════════════════════════════╝
void lireCapteurs() {
  float t = dht.readTemperature();
  float h = dht.readHumidity();
  if (!isnan(t)) temperature = t;
  if (!isnan(h)) humidity    = h;

  gasBuf[gasIdx] = analogRead(MQ135_PIN);
  gasIdx = (gasIdx + 1) % 5;
  long sum = 0;
  for (int i = 0; i < 5; i++) sum += gasBuf[i];
  gasLevel = (int)(sum / 5);

  pirDetected = (digitalRead(PIR_PIN) == HIGH);

  lirePZEM();
}

void lirePZEM() {
  pzemSerial.listen();
  const uint8_t req[] = {0x01, 0x04, 0x00, 0x00, 0x00, 0x0A, 0x70, 0x0D};
  pzemSerial.write(req, sizeof(req));
  delay(200);

  uint8_t buf[25];
  int n = 0;
  unsigned long t0 = millis();
  while (millis() - t0 < 500 && n < 25) {
    if (pzemSerial.available()) buf[n++] = pzemSerial.read();
  }

  if (n >= 22) {
    float v = ((uint16_t)(buf[3]  << 8 | buf[4]))  / 10.0;
    float c = ((uint32_t)(buf[7]  << 24 | buf[8]  << 16 | buf[5]  << 8 | buf[6]))  / 1000.0;
    float p = ((uint32_t)(buf[11] << 24 | buf[12] << 16 | buf[9]  << 8 | buf[10])) / 10.0;
    if (v > 10 && v < 300)   voltage   = v; else voltage   = 0;
    if (c > 0  && c < 100)   current_a = c; else current_a = 0;
    if (p > 0  && p < 30000) power_w   = p; else power_w   = 0;
  }
  gsmSerial.listen();
}

// ╔══════════════════════════════════════════════════╗
// ║              LOGIQUE ALERTE & LED                ║
// ╚══════════════════════════════════════════════════╝
uint8_t calculerNiveau() {
  if (temperature >= TEMP_CRIT || gasLevel >= GAZ_CRIT || humidity >= HUM_CRIT) return 3;
  if (temperature >= TEMP_DANG || gasLevel >= GAZ_DANG || humidity >= HUM_DANG) return 2;
  if (temperature >= TEMP_ATTN || gasLevel >= GAZ_ATTN || humidity >= HUM_ATTN) return 1;
  return 0;
}

void mettreAJourLED(uint8_t n) {
  switch (n) {
    case 0: setLED(0,  80, 0);  break;
    case 1: setLED(80, 60, 0);  break;
    case 2: setLED(120,30, 0);  break;
    case 3: setLED(150, 0, 0);  break;
  }
}

void gererBuzzer(uint8_t n) {
  if (n >= 2 && n > prevLevel) pipDanger();
}

void pipDanger() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH); delay(150);
    digitalWrite(BUZZER_PIN, LOW);  delay(150);
  }
}

const char* niveauStr(uint8_t n) {
  switch (n) {
    case 1: return "attention";
    case 2: return "danger";
    case 3: return "critique";
    default: return "normal";
  }
}

void setLED(uint8_t r, uint8_t g, uint8_t b) {
  analogWrite(LED_R_PIN, r);
  analogWrite(LED_G_PIN, g);
  analogWrite(LED_B_PIN, b);
}

// ╔══════════════════════════════════════════════════╗
// ║          JSON SÉRIE → Python bridge              ║
// ╚══════════════════════════════════════════════════╝
void printJSON() {
  Serial.print(F("{\"type\":\"donnees\",\"salle_id\":"));
  Serial.print(ROOM_ID);
  Serial.print(F(",\"temperature\":")); Serial.print(temperature, 1);
  Serial.print(F(",\"humidite\":")); Serial.print(humidity, 1);
  Serial.print(F(",\"gaz\":")); Serial.print(gasLevel);
  Serial.print(F(",\"pir\":")); Serial.print(pirDetected ? "true" : "false");
  if (voltage > 0) {
    Serial.print(F(",\"voltage\":")); Serial.print(voltage, 1);
    Serial.print(F(",\"current\":")); Serial.print(current_a, 2);
    Serial.print(F(",\"power\":")); Serial.print(power_w, 1);
  }
  Serial.println(F("}"));
}

// ╔══════════════════════════════════════════════════╗
// ║          ENVOI HTTP DIRECT — SIM900 GPRS         ║
// ╚══════════════════════════════════════════════════╝
bool envoyerGSM() {
  gsmSerial.listen();

  String body = "{\"type\":\"donnees\",\"salle_id\":";
  body += String(ROOM_ID)                + ",";
  body += "\"temperature\":" + String(temperature, 1)         + ",";
  body += "\"humidite\":"    + String(humidity, 1)            + ",";
  body += "\"gaz\":"         + String(gasLevel)               + ",";
  body += "\"pir\":"         + String(pirDetected ? "true":"false");
  if (voltage > 0) {
    body += ",\"voltage\":"  + String(voltage, 1);
    body += ",\"current\":"  + String(current_a, 2);
    body += ",\"power\":"    + String(power_w, 1);
  }
  body += "}";

  String req  = "POST /api/capteurs HTTP/1.1\r\n";
  req += "Host: " + SERVER_IP + "\r\n";
  req += "Content-Type: application/json\r\n";
  req += "Content-Length: " + String(body.length()) + "\r\n";
  req += "Connection: close\r\n\r\n";
  req += body;

  gsmSerial.print(F("AT+CIPSTART=\"TCP\",\""));
  gsmSerial.print(SERVER_IP);
  gsmSerial.print(F("\","));
  gsmSerial.println(SERVER_PORT);

  if (readGSM(5000).indexOf("CONNECT") < 0) {
    sendAT(F("AT+CIPCLOSE"), 500);
    return false;
  }

  gsmSerial.print(F("AT+CIPSEND="));
  gsmSerial.println(req.length());

  unsigned long t0 = millis();
  bool prompt = false;
  while (millis() - t0 < 5000) {
    if (gsmSerial.available() && gsmSerial.read() == '>') { prompt = true; break; }
  }
  if (!prompt) { sendAT(F("AT+CIPCLOSE"), 500); return false; }

  gsmSerial.print(req);
  gsmSerial.write(26);

  String resp = readGSM(8000);
  sendAT(F("AT+CIPCLOSE"), 1000);

  bool ok = (resp.indexOf("200") >= 0 || resp.indexOf("201") >= 0);
  Serial.println(ok ? F("[GSM] OK envoye") : F("[GSM] Echec HTTP"));
  return ok;
}

// ╔══════════════════════════════════════════════════╗
// ║           INITIALISATION SIM900                  ║
// ╚══════════════════════════════════════════════════╝
void powerOnGSM() {
  gsmSerial.listen();
  gsmSerial.println(F("AT"));
  delay(1000);
  if (readGSM(1000).indexOf("OK") >= 0) {
    Serial.println(F("[GSM] Deja allume"));
    return;
  }
  Serial.println(F("[GSM] Allumage..."));
  pinMode(GSM_PWRKEY, OUTPUT);
  digitalWrite(GSM_PWRKEY, HIGH); delay(200);
  digitalWrite(GSM_PWRKEY, LOW);  delay(1200);
  digitalWrite(GSM_PWRKEY, HIGH);
  delay(4000);
}

bool initGSM() {
  gsmSerial.listen();
  Serial.println(F("[GSM] Init..."));

  for (int i = 0; i < 5; i++) {
    gsmSerial.println(F("AT"));
    if (readGSM(1500).indexOf("OK") >= 0) goto gsm_found;
    delay(1000);
  }
  Serial.println(F("[GSM] Absent — USB only"));
  return false;

gsm_found:
  sendAT(F("ATE0"), 500);
  sendAT(F("AT+CMEE=1"), 500);

  if (sendATread(F("AT+CIMI"), 1500).length() < 5) {
    Serial.println(F("[GSM] SIM absente"));
    return false;
  }

  Serial.print(F("[GSM] Reseau"));
  for (int i = 0; i < 10; i++) {
    String r = sendATread(F("AT+CREG?"), 1000);
    if (r.indexOf(",1") >= 0 || r.indexOf(",5") >= 0) { Serial.println(F(" OK")); break; }
    if (i == 9) { Serial.println(F(" timeout")); return false; }
    Serial.print(F(".")); delay(2000);
  }

  String apns[] = {APN, "internet", "orange", "mtn"};
  for (int a = 0; a < 4; a++) {
    sendAT(F("AT+CIPSHUT"), 2000); delay(500);
    sendAT(F("AT+CGATT=1"), 2000); delay(500);

    gsmSerial.print(F("AT+CSTT=\""));
    gsmSerial.print(apns[a]);
    gsmSerial.println(F("\",\"\",\"\""));
    delay(2000);

    sendAT(F("AT+CIICR"), 8000); delay(3000);

    gsmSerial.println(F("AT+CIFSR"));
    delay(2000);
    String ip = readGSM(3000);
    ip.trim();

    if (ip.length() >= 7 && ip.indexOf("ERROR") < 0 && ip.indexOf("0.0.0") < 0) {
      Serial.print(F("[GSM] IP: ")); Serial.println(ip);
      Serial.print(F("[GSM] APN: ")); Serial.println(apns[a]);
      sendAT(F("AT+CIPMODE=0"), 500);
      return true;
    }
  }
  Serial.println(F("[GSM] GPRS echec"));
  return false;
}

// ╔══════════════════════════════════════════════════╗
// ║               UTILITAIRES GSM                    ║
// ╚══════════════════════════════════════════════════╝
void sendAT(const __FlashStringHelper* cmd, int ms) {
  gsmSerial.println(cmd);
  delay(ms);
  while (gsmSerial.available()) gsmSerial.read();
}

String sendATread(const __FlashStringHelper* cmd, int ms) {
  gsmSerial.println(cmd);
  delay(ms);
  String r = "";
  while (gsmSerial.available()) r += (char)gsmSerial.read();
  return r;
}

String readGSM(int ms) {
  String r = "";
  unsigned long t0 = millis();
  while (millis() - t0 < (unsigned long)ms) {
    if (gsmSerial.available()) r += (char)gsmSerial.read();
  }
  return r;
}
