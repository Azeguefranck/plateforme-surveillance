#!/usr/bin/env python3
"""
serial_relay.py — Arduino USB Serial -> API Laravel
DTR=False : ne reset PAS l'Arduino a l'ouverture du port.

Lancer  : python3 serial_relay.py
Fond    : nohup python3 serial_relay.py >> /tmp/relay.log 2>&1 &
Arreter : pkill -f serial_relay.py
UPLOAD  : pkill -f serial_relay.py  AVANT de telecharger dans Arduino IDE
"""

import serial
import json
import re
import urllib.request
import urllib.error
import time
import sys
import os

API_URL   = "http://localhost:8000/api/capteurs"
BAUD_RATE = 9600
PORTS     = ["/dev/ttyACM0", "/dev/ttyACM1", "/dev/ttyUSB0", "/dev/ttyUSB1"]


def ouvrir_port(port: str):
    """Ouvre le port Serie SANS asserter DTR (evite le reset Arduino)."""
    s = serial.Serial()
    s.port     = port
    s.baudrate = BAUD_RATE
    s.timeout  = 5
    s.dtr      = False   # NE PAS reset l'Arduino a l'ouverture
    s.rts      = False
    s.open()
    time.sleep(0.5)
    s.reset_input_buffer()
    return s


def trouver_port():
    for p in PORTS:
        if os.path.exists(p):
            try:
                s = ouvrir_port(p)
                s.close()
                return p
            except (serial.SerialException, OSError):
                continue
    return None


def post_capteurs(payload: dict):
    body = json.dumps(payload).encode()
    req  = urllib.request.Request(
        API_URL,
        data=body,
        headers={"Content-Type": "application/json"},
        method="POST"
    )
    try:
        with urllib.request.urlopen(req, timeout=8) as resp:
            result = json.loads(resp.read())
            return result.get("success", False), result.get("alertes", 0)
    except (urllib.error.URLError, TimeoutError, OSError) as e:
        return False, str(e)


def traiter_ligne(line: str):
    if not line.startswith("{"):
        if line:
            print(f"[dbg] {line}", flush=True)
        return

    try:
        # Arduino snprintf ne supporte pas %f sur AVR → remplace "?" par 0
        line_clean = re.sub(r':\s*\?', ':0', line)
        data = json.loads(line_clean)
    except json.JSONDecodeError:
        print(f"[json err] {line}", flush=True)
        return

    msg_type = data.get("type", "")
    ts       = time.strftime("%H:%M:%S")

    # Données live (cycle 2s) → fichier JSON, pas en base de données
    if msg_type == "live":
        live = {
            "temperature": data.get("temperature", 0),
            "humidite":    data.get("humidite", 0),
            "gaz":         data.get("gaz", 0),
            "courant":     data.get("courant", 0),
            "puissance":   data.get("puissance", 0),
            "pir":         data.get("pir", 0),
            "salle_id":    data.get("salle_id", 1),
            "ts":          time.strftime("%Y-%m-%dT%H:%M:%S"),
        }
        try:
            with open("/tmp/latest_sensor.json", "w") as f:
                json.dump(live, f)
        except OSError:
            pass
        return

    # Donnees capteurs (cycle 30s) → POST /api/capteurs
    if msg_type == "donnees":
        payload = {
            "temperature": data.get("temperature", 0),
            "humidite":    data.get("humidite", 0),
            "gaz":         data.get("gaz", 0),
            "courant":     data.get("courant", 0),
            "puissance":   data.get("puissance", 0),
            "pir":         data.get("pir", 0),
            "salle_id":    data.get("salle_id", 1),
        }
        ok, alertes = post_capteurs(payload)
        if ok:
            t    = payload["temperature"]
            h    = payload["humidite"]
            g    = payload["gaz"]
            i    = payload["courant"]
            p    = payload["puissance"]
            pir  = payload["pir"]
            extra = f"  | {alertes} alerte(s)" if alertes else ""
            print(f"[OK]  {ts}  T={t}C  H={h}%  Gaz={g}  I={i}A  P={p}W  PIR={pir}{extra}", flush=True)
        else:
            print(f"[ERR] {ts}  {alertes}", flush=True)

    # Intrusion PIR immediate → POST avec pir=1 pour email serveur
    elif msg_type == "alerte" and data.get("categorie") == "INTRUSION":
        payload = {
            "temperature": data.get("temperature", 0),
            "humidite":    data.get("humidite", 0),
            "gaz":         data.get("gaz", 0),
            "courant":     data.get("courant", 0),
            "puissance":   data.get("puissance", 0),
            "pir":         1,
            "salle_id":    data.get("salle_id", 1),
        }
        ok, _ = post_capteurs(payload)
        print(f"[PIR] {ts}  INTRUSION -> email {'OK' if ok else 'ERREUR'}", flush=True)

    # Autres alertes seuils → log (email gere cote serveur via donnees)
    elif msg_type == "alerte":
        cat = data.get("categorie", "?")
        niv = data.get("niveau", "?")
        print(f"[ALT] {ts}  {cat} {niv}", flush=True)


def run():
    print(f"[RELAY] Recherche port Arduino...", flush=True)

    port = trouver_port()
    if not port:
        print("[RELAY] Aucun port trouve. Branchez l'Arduino et relancez.", flush=True)
        sys.exit(1)

    print(f"[RELAY] {time.strftime('%H:%M:%S')}  {port}  ->  {API_URL}", flush=True)
    print(f"[RELAY] DTR=False (Arduino ne reset pas a l'ouverture)", flush=True)
    print("-" * 60, flush=True)

    while True:
        try:
            ser = ouvrir_port(port)
            print(f"[RELAY] Port ouvert. Attente donnees...", flush=True)
            buf = b""
            while True:
                try:
                    byte = ser.read(1)
                except serial.SerialException:
                    ser.close()
                    break

                if not byte:
                    continue

                if byte == b"\n":
                    line = buf.decode("utf-8", errors="ignore").strip()
                    buf  = b""
                    if line:
                        try:
                            traiter_ligne(line)
                        except Exception as ex:
                            print(f"[RELAY] Erreur traitement: {ex}", flush=True)
                else:
                    buf += byte
                    if len(buf) > 600:
                        buf = b""

        except serial.SerialException as e:
            print(f"[RELAY] Reconnexion dans 5s... ({e})", flush=True)
            time.sleep(5)
        except KeyboardInterrupt:
            print("\n[RELAY] Arret.", flush=True)
            sys.exit(0)


if __name__ == "__main__":
    run()
