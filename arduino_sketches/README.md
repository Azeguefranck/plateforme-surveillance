# Sketches Arduino — SUPSERVER

Quatre firmwares pour la surveillance des salles serveurs.

| Fichier | Connexion | Capteurs | Usage |
|---|---|---|---|
| `surveillance_salle_serveurs.ino` | USB Serial → serial_relay.py | DHT22 D4, MQ135 A0, ACS712 A1, PIR D5, Buzzer D6, LED D9/10/11 | **Référence opérationnelle** |
| `arduino_salle.ino` | USB Serial → serial_relay.py | DHT22 D4, MQ135 A0, ACS712 A1, PIR D5, Buzzer D6, LED D9/10/11 | Version simplifiée |
| `surveillance_iot.ino` | GSM SIM900 (HTTP direct) | DHT22 D7, MQ135 A0, ACS712 A1, PIR D8, SIM900 D9/10/11 | Déploiement GSM + ACS712 |
| `arduino_surveillance_salle_serveur.ino` | GSM SIM900 (HTTP direct) | DHT22 D2, MQ135 A0, PZEM-004T, PIR D3 | Déploiement GSM + PZEM |

**Seuils communs :** T ≥ 28/32 °C — H ≥ 75/85 % — G ≥ 400/600 ppm — I ≥ 10/15 A — P ≥ 1000/1500 W

**Serveur cible :** `https://lego-sanitizer-hexagram.ngrok-free.dev` — endpoint `/api/capteurs`
