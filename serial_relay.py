#!/usr/bin/env python3

import serial
import json
import re
import signal
import urllib.request
import urllib.error
import time
import sys
import os

API_URL      = "http://localhost:8000/api/capteurs"
LIVE_FILE    = "/tmp/latest_sensor.json"
BAUD_RATE    = 9600
PORTS        = ["/dev/ttyACM0", "/dev/ttyACM1", "/dev/ttyUSB0", "/dev/ttyUSB1", "/dev/arduino"]
BOOT_WAIT    = 0.2
RETRY_DELAY  = 0.5

def _cleanup(signum=None, frame=None):
    sys.exit(0)
signal.signal(signal.SIGTERM, _cleanup)
signal.signal(signal.SIGINT,  _cleanup)


def _clean(raw_bytes: bytes) -> str:
    s = raw_bytes.decode("utf-8", errors="ignore")
    return "".join(c for c in s if c.isprintable() or c == "\t").strip()


def ouvrir_port(port: str) -> serial.Serial:
    s = serial.Serial()
    s.port     = port
    s.baudrate = BAUD_RATE
    s.timeout  = 0.2
    s.dtr      = False
    s.rts      = False
    s.open()
    s.setDTR(False)

    deadline = time.time() + BOOT_WAIT
    while time.time() < deadline:
        try:
            s.read(max(1, s.in_waiting))
        except Exception:
            break
        time.sleep(0.02)

    s.timeout = 1.0
    s.reset_input_buffer()
    return s


def trouver_port() -> str | None:
    for p in PORTS:
        if os.path.exists(p):
            return p
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
    except Exception as e:
        return False, str(e)


def ecrire_live(data: dict):
    live = {
        "temperature": float(data.get("temperature") or 0),
        "humidite":    float(data.get("humidite")    or 0),
        "gaz":         int  (data.get("gaz")         or 0),
        "pir":         int  (data.get("pir")         or 0),
        "salle_id":    data.get("salle_id", 1),
        "ts":          time.strftime("%Y-%m-%dT%H:%M:%S"),
    }
    try:
        with open(LIVE_FILE, "w") as f:
            json.dump(live, f)
    except OSError:
        pass
    return live


def traiter_ligne(line: str):
    if not line.startswith("{"):
        if line and line.isascii():
            print(f"[dbg] {line}", flush=True)
        return

    cleaned = re.sub(r':\s*\?', ':0', line)
    cleaned = re.sub(r':\s*,',  ':0,', cleaned)
    cleaned = re.sub(r':\s*}',  ':0}', cleaned)

    try:
        data = json.loads(cleaned)
    except json.JSONDecodeError:
        return

    msg_type = data.get("type", "")
    ts       = time.strftime("%H:%M:%S")

    if msg_type == "live" or (msg_type == "" and "temperature" in data):
        live = ecrire_live(data)
        if msg_type == "":
            ok, alertes = post_capteurs(live)
            extra = f"  | {alertes} alerte(s)" if alertes else ""
            status = "OK " if ok else "ERR"
            t = live["temperature"]; h = live["humidite"]; g = live["gaz"]
            print(f"[{status}] {ts}  T:{t}  H:{h}%  G:{g}  PIR:{live['pir']}{extra}", flush=True)
        return

    if msg_type == "donnees":
        payload = {
            "temperature": float(data.get("temperature") or 0),
            "humidite":    float(data.get("humidite")    or 0),
            "gaz":         int  (data.get("gaz")         or 0),
            "pir":         int  (data.get("pir")         or 0),
            "salle_id":    data.get("salle_id", 1),
        }
        ecrire_live(data)
        ok, alertes = post_capteurs(payload)
        t   = payload["temperature"]
        h   = payload["humidite"]
        g   = payload["gaz"]
        pir = payload["pir"]
        extra = f"  | {alertes} alerte(s)" if alertes else ""
        status = "OK " if ok else "ERR"
        print(f"[{status}] {ts}  T:{t}  H:{h}%  G:{g}  PIR:{pir}{extra}", flush=True)

    elif msg_type == "alerte":
        cat = data.get("categorie", "")
        niv = data.get("niveau", "")
        payload = {
            "temperature": float(data.get("temperature") or 0),
            "humidite":    float(data.get("humidite")    or 0),
            "gaz":         int  (data.get("gaz")         or 0),
            "pir":         1 if cat == "INTRUSION" else 0,
            "salle_id":    data.get("salle_id", 1),
        }
        ecrire_live(data)
        if cat == "INTRUSION":
            ok, _ = post_capteurs(payload)
            status = "OK " if ok else "ERR"
            print(f"[PIR][{status}] {ts}  {cat} {niv} -> {'email envoyé' if ok else 'ERREUR'}", flush=True)
        else:
            print(f"[ALT] {ts}  {cat} {niv} (live maj, POST ignoré : déjà couvert par donnees)", flush=True)


def run():
    UPLOAD_SIGNAL = "/tmp/arduino_upload"

    while True:

        if os.path.exists(UPLOAD_SIGNAL):
            print("[RELAY] Upload Arduino détecté — port libéré.", flush=True)
            while os.path.exists(UPLOAD_SIGNAL):
                time.sleep(0.5)
            print("[RELAY] Upload terminé — reprise.", flush=True)
            time.sleep(1.0)
            continue

        port = trouver_port()
        if not port:
            time.sleep(1.0)
            continue

        print(f"[RELAY] {time.strftime('%H:%M:%S')}  Connexion sur {port}", flush=True)

        try:
            ser = ouvrir_port(port)
        except serial.SerialException as e:
            print(f"[RELAY] Impossible d'ouvrir {port} : {e}", flush=True)
            time.sleep(RETRY_DELAY)
            continue

        print(f"[RELAY] Connecté — lecture en cours...", flush=True)
        buf = b""

        try:
            while True:

                if os.path.exists(UPLOAD_SIGNAL):
                    try: ser.reset_input_buffer()
                    except Exception: pass
                    ser.close()
                    break

                try:
                    byte = ser.read(1)
                except serial.SerialException:
                    break

                if not byte:
                    continue

                if byte == b"\n":
                    line = _clean(buf)
                    buf  = b""
                    if line:
                        try:
                            traiter_ligne(line)
                        except Exception as ex:
                            pass
                else:
                    buf += byte
                    if len(buf) > 512:
                        buf = b""

        except Exception:
            pass

        try: ser.close()
        except Exception: pass

        print(f"[RELAY] Déconnecté — reconnexion dans {RETRY_DELAY}s...", flush=True)
        time.sleep(RETRY_DELAY)


if __name__ == "__main__":
    run()
