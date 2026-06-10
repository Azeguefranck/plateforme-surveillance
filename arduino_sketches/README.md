# Sketches Arduino — SUPSERVER

Cinq firmwares pour la surveillance des salles serveurs.

| Fichier | Connexion | Capteurs | Usage |
|---|---|---|---|
| `plateforme_de_surveillance_des_salles_serveurs.ino` | USB Serial → serial_relay.py | DHT22 D4, MQ135 A0, PIR D5, Buzzer D6, LED D9/10/11 | **Fichier opérationnel du projet** |
| `surveillance_salle_serveurs.ino` | USB Serial → serial_relay.py | DHT22 D4, MQ135 A0, PIR D5, Buzzer D6, LED D9/10/11 | Version avancée (buzzer non-bloquant) |
| `arduino_salle.ino` | USB Serial → serial_relay.py | DHT22 D4, MQ135 A0, PIR D5, Buzzer D6, LED D9/10/11 | Version simplifiée |
| `surveillance_iot.ino` | GSM SIM900 (HTTP direct) | DHT22 D7, MQ135 A0, PIR D8, SIM900 D9/10/11 | Déploiement GSM |
| `arduino_surveillance_salle_serveur.ino` | GSM SIM900 (HTTP direct) | DHT22 D2, MQ135 A0, PZEM-004T, PIR D3 | Déploiement GSM + PZEM |

**Seuils :** T ≥ 28/32 °C — H ≥ 75/85 % — G ≥ 400/600 ppm

**Timings (fichier opérationnel) :**
- Live dashboard : toutes les **2 secondes** (`LIVE_MS = 2000`)
- Stockage DB : toutes les **10 secondes** (`INTERVALLE_MS = 10000`) → 8 640 enregistrements/jour

**Serveur cible :** `https://lego-sanitizer-hexagram.ngrok-free.dev` — endpoint `/api/capteurs`
