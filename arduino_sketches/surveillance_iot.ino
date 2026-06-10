#include <DHT.h>

#define SALLE_ID    1
#define DHT_PIN     4
#define DHT_TYPE    DHT22

#define MQ135_PIN   A0
#define PIR_PIN     5

#define BUZZER_PIN  6

#define LED_R       9
#define LED_G       10
#define LED_B       11

DHT dht(DHT_PIN, DHT_TYPE);

int SEUIL_TEMP_W = 28;
int SEUIL_TEMP_C = 32;

int SEUIL_HUM_W  = 75;
int SEUIL_HUM_C  = 85;

int SEUIL_GAZ_W  = 400;
int SEUIL_GAZ_C  = 600;

bool PIR_ACTIF = true;

float temperature = 0.0;
float humidite = 0.0;

int gaz = 0;
int pir = 0;

#define DEBOUNCE_REQ 3

int cntTemp = 0;
int cntHum  = 0;
int cntGaz  = 0;

bool etatTemp = false;
bool etatHum  = false;
bool etatGaz  = false;

#define EMAIL_COOLDOWN 600000UL
#define PIR_COOLDOWN   300000UL

#define INTERVALLE_MS 30000UL
#define LIVE_MS        2000UL

unsigned long tEmailTemp = 0;
unsigned long tEmailHum  = 0;
unsigned long tEmailGaz  = 0;
unsigned long tEmailPir  = 0;

unsigned long tDernierEnvoi = 0;
unsigned long tLive = 0;

char jsonBuf[250];

void lireCapteurs();
void verifierAlertes();
void envoyerLive();
void envoyerDonnees();
void envoyerAlerte(const char* cat,const char* niv,const char* msg);
void afficherSerie();

void ledVerte();
void ledRouge();
void beep(int ms);

void setup() {

  Serial.begin(9600);

  dht.begin();

  pinMode(PIR_PIN, INPUT);

  pinMode(BUZZER_PIN, OUTPUT);

  pinMode(LED_R, OUTPUT);
  pinMode(LED_G, OUTPUT);
  pinMode(LED_B, OUTPUT);

  digitalWrite(BUZZER_PIN, LOW);

  ledVerte();

  Serial.println("DEMARRAGE SYSTEME");

  delay(2000);

  Serial.println("SYSTEME PRET");

  beep(100);
  delay(100);
  beep(100);
}

void loop() {

  unsigned long now = millis();

  static unsigned long tPirCheck = 0;

  if (now - tPirCheck >= 1000UL) {

    tPirCheck = now;

    if (digitalRead(PIR_PIN) == HIGH && PIR_ACTIF) {

      if (now - tEmailPir >= PIR_COOLDOWN) {

        tEmailPir = now;

        envoyerAlerte(
          "INTRUSION",
          "CRITIQUE",
          "Mouvement detecte dans la salle serveurs"
        );

        beep(200);
        delay(100);
        beep(200);

        ledRouge();
      }
    }
  }

  if (now - tLive >= LIVE_MS) {

    tLive = now;

    gaz = analogRead(MQ135_PIN);

    pir = digitalRead(PIR_PIN);

    envoyerLive();
  }

  if (now - tDernierEnvoi >= INTERVALLE_MS) {

    tDernierEnvoi = now;

    lireCapteurs();

    verifierAlertes();

    envoyerDonnees();

    afficherSerie();
  }
}

void lireCapteurs() {

  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (!isnan(t))
    temperature = t;

  if (!isnan(h))
    humidite = h;

  gaz = analogRead(MQ135_PIN);

  pir = digitalRead(PIR_PIN);
}

void verifierAlertes() {

  bool alerteActive = false;

  unsigned long now = millis();

  if (temperature >= SEUIL_TEMP_W) {

    if (++cntTemp >= DEBOUNCE_REQ) {

      alerteActive = true;

      if (!etatTemp || now - tEmailTemp >= EMAIL_COOLDOWN) {

        envoyerAlerte(
          "TEMPERATURE",
          temperature >= SEUIL_TEMP_C ?
          "CRITIQUE" : "WARNING",
          "Surchauffe detectee dans la salle serveurs"
        );

        tEmailTemp = now;
      }

      etatTemp = true;
    }

  } else {

    cntTemp = 0;
    etatTemp = false;
  }

  if (humidite >= SEUIL_HUM_W) {

    if (++cntHum >= DEBOUNCE_REQ) {

      alerteActive = true;

      if (!etatHum || now - tEmailHum >= EMAIL_COOLDOWN) {

        envoyerAlerte(
          "HUMIDITE",
          humidite >= SEUIL_HUM_C ?
          "CRITIQUE" : "WARNING",
          "Humidite excessive detectee"
        );

        tEmailHum = now;
      }

      etatHum = true;
    }

  } else {

    cntHum = 0;
    etatHum = false;
  }

  if (gaz >= SEUIL_GAZ_W) {

    if (++cntGaz >= DEBOUNCE_REQ) {

      alerteActive = true;

      if (!etatGaz || now - tEmailGaz >= EMAIL_COOLDOWN) {

        envoyerAlerte(
          "QUALITE_AIR",
          gaz >= SEUIL_GAZ_C ?
          "CRITIQUE" : "WARNING",
          "Qualite de l'air degradee"
        );

        tEmailGaz = now;
      }

      etatGaz = true;
    }

  } else {

    cntGaz = 0;
    etatGaz = false;
  }

  if (pir == HIGH && PIR_ACTIF)
    alerteActive = true;

  if (alerteActive) {

    ledRouge();

    beep(150);

  } else {

    ledVerte();

    digitalWrite(BUZZER_PIN, LOW);
  }
}

void envoyerLive() {

  char tB[8];
  char hB[8];

  dtostrf(temperature,4,1,tB);
  dtostrf(humidite,4,1,hB);

  snprintf(jsonBuf,sizeof(jsonBuf),
    "{\"type\":\"live\","
    "\"salle_id\":%d,"
    "\"temperature\":%s,"
    "\"humidite\":%s,"
    "\"gaz\":%d,"
    "\"pir\":%d}",
    SALLE_ID,
    tB,
    hB,
    gaz,
    pir
  );

  Serial.println(jsonBuf);
}

void envoyerDonnees() {

  char tB[8];
  char hB[8];

  dtostrf(temperature,4,1,tB);
  dtostrf(humidite,4,1,hB);

  snprintf(jsonBuf,sizeof(jsonBuf),
    "{\"type\":\"donnees\","
    "\"salle_id\":%d,"
    "\"temperature\":%s,"
    "\"humidite\":%s,"
    "\"gaz\":%d,"
    "\"pir\":%d}",
    SALLE_ID,
    tB,
    hB,
    gaz,
    pir
  );

  Serial.println(jsonBuf);
}

void envoyerAlerte(
  const char* cat,
  const char* niv,
  const char* msg) {

  char tB[8];
  char hB[8];

  dtostrf(temperature,4,1,tB);
  dtostrf(humidite,4,1,hB);

  snprintf(jsonBuf,sizeof(jsonBuf),
    "{\"type\":\"alerte\","
    "\"salle_id\":%d,"
    "\"categorie\":\"%s\","
    "\"niveau\":\"%s\","
    "\"message\":\"%s\","
    "\"temperature\":%s,"
    "\"humidite\":%s,"
    "\"gaz\":%d}",
    SALLE_ID,
    cat,
    niv,
    msg,
    tB,
    hB,
    gaz
  );

  Serial.println(jsonBuf);
}

void afficherSerie() {

  Serial.println("---");

  Serial.print("T:");
  Serial.print(temperature);
  Serial.print(" C  ");

  Serial.print("H:");
  Serial.print(humidite);
  Serial.print("%  ");

  Serial.print("G:");
  Serial.print(gaz);
  Serial.print("  ");

  Serial.print("PIR:");
  Serial.println(pir);
}

void ledVerte() {

  analogWrite(LED_R,0);
  analogWrite(LED_G,255);
  analogWrite(LED_B,0);
}

void ledRouge() {

  analogWrite(LED_R,255);
  analogWrite(LED_G,0);
  analogWrite(LED_B,0);
}

void beep(int ms) {

  digitalWrite(BUZZER_PIN,HIGH);
  delay(ms);
  digitalWrite(BUZZER_PIN,LOW);
}
