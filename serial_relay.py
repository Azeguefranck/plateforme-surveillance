#!/usr/bin/env python3
# Lit les données Arduino sur USB et les envoie à l'API Laravel.
# Lancer  : python3 serial_relay.py
# Fond    : nohup python3 serial_relay.py >> /tmp/relay.log 2>&1 &
# Arrêter : pkill -f serial_relay.py

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
    # DTR=False pour ne pas réinitialiser l'Arduino à l'ouverture du port
    s = serial.Serial()
    s.port     = port
    s.baudrate = BAUD_RATE
    s.timeout  = 0.5
    s.dtr      = False
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
        # Arduino AVR ne supporte pas %f dans snprintf, remplace "?" par 0
        line_clean = re.sub(r':\s*\?', ':0', line)
        data = json.loads(line_clean)
    except json.JSONDecodeError:
        print(f"[json err] {line}", flush=True)
        return

    msg_type = data.get("type", "")
    ts       = time.strftime("%H:%M:%S")

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
            t   = payload["temperature"]
            h   = payload["humidite"]
            g   = payload["gaz"]
            i   = payload["courant"]
            p   = payload["puissance"]
            pir = payload["pir"]
            extra = f"  | {alertes} alerte(s)" if alertes else ""
            print(f"[OK]  {ts}  T:{t}C  H:{h}%  G:{g}  I:{i}A  P:{p}W  PIR:{pir}{extra}", flush=True)
        else:
            print(f"[ERR] {ts}  {alertes}", flush=True)

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

    elif msg_type == "alerte":
        cat = data.get("categorie", "?")
        niv = data.get("niveau", "?")
        print(f"[ALT] {ts}  {cat} {niv}", flush=True)


def run():
    print(f"[RELAY] Recherche du port Arduino...", flush=True)

    port = trouver_port()
    if not port:
        print("[RELAY] Aucun port trouvé. Branchez l'Arduino et relancez.", flush=True)
        sys.exit(1)

    print(f"[RELAY] {time.strftime('%H:%M:%S')}  {port}  ->  {API_URL}", flush=True)
    print(f"[RELAY] DTR=False (pas de reset à l'ouverture)", flush=True)

    UPLOAD_SIGNAL = "/tmp/arduino_upload"

    while True:
        # Pause propre pendant un upload Arduino
        if os.path.exists(UPLOAD_SIGNAL):
            print("[RELAY] Upload détecté, port libéré.", flush=True)
            while os.path.exists(UPLOAD_SIGNAL):
                time.sleep(0.5)
            print("[RELAY] Upload terminé, reprise.", flush=True)
            time.sleep(2)
            continue

        try:
            ser = ouvrir_port(port)
            print(f"[RELAY] Port ouvert. En attente de données...", flush=True)
            buf = b""
            while True:
                # Céder le port si un upload commence
                if os.path.exists(UPLOAD_SIGNAL):
                    try:
                        ser.reset_input_buffer()
                    except Exception:
                        pass
                    ser.close()
                    break

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
                            print(f"[RELAY] Erreur: {ex}", flush=True)
                else:
                    buf += byte
                    if len(buf) > 600:
                        buf = b""

        except serial.SerialException as e:
            print(f"[RELAY] Reconnexion dans 5s... ({e})", flush=True)
            time.sleep(5)
        except KeyboardInterrupt:
            print("\n[RELAY] Arrêt.", flush=True)
            sys.exit(0)


if __name__ == "__main__":
    run()
