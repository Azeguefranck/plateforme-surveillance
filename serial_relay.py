#!/usr/bin/env python3
# Lit les données Arduino sur USB et les envoie à l'API Laravel.
# Démarre : sudo systemctl start serial-relay.service
# Status  : sudo journalctl -u serial-relay.service -f

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
PORTS        = ["/dev/ttyACM0", "/dev/ttyACM1", "/dev/ttyUSB0", "/dev/ttyUSB1"]
BOOT_WAIT    = 1.5   # secondes d'attente après connexion (boot Arduino ~0.6s + marge)
RETRY_DELAY  = 1.5   # secondes avant de retenter après une déconnexion

# ── Nettoyage à l'arrêt (SIGTERM / SIGINT) ───────────────────────────────────
def _cleanup(signum=None, frame=None):
    try: os.remove(LIVE_FILE)
    except OSError: pass
    sys.exit(0)
signal.signal(signal.SIGTERM, _cleanup)
signal.signal(signal.SIGINT,  _cleanup)


# ── Nettoyage des lignes série ────────────────────────────────────────────────
def _clean(raw_bytes: bytes) -> str:
    """Décode et retire les caractères non imprimables (évite les blobs journalctl)."""
    s = raw_bytes.decode("utf-8", errors="ignore")
    return "".join(c for c in s if c.isprintable() or c == "\t").strip()


# ── Ouverture du port sans reset Arduino ──────────────────────────────────────
def ouvrir_port(port: str) -> serial.Serial:
    s = serial.Serial()
    s.port     = port
    s.baudrate = BAUD_RATE
    s.timeout  = 0.2    # timeout court pendant l'attente de boot
    s.dtr      = False
    s.rts      = False
    s.open()
    s.setDTR(False)

    # Vider ACTIVEMENT le tampon pendant BOOT_WAIT secondes
    # (reset_input_buffer seul ne vide pas toujours les tampons noyau USB CDC)
    deadline = time.time() + BOOT_WAIT
    while time.time() < deadline:
        try:
            s.read(max(1, s.in_waiting))   # lit et jette tout
        except Exception:
            break
        time.sleep(0.02)

    s.timeout = 1.0    # rétablir timeout normal pour la lecture
    s.reset_input_buffer()
    return s


# ── Détection du port (sans ouverture) ───────────────────────────────────────
def trouver_port() -> str | None:
    for p in PORTS:
        if os.path.exists(p):
            return p
    return None


# ── Envoi HTTP POST vers l'API ────────────────────────────────────────────────
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


# ── Écriture du fichier live (dashboard temps réel) ──────────────────────────
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


# ── Traitement d'une ligne JSON ───────────────────────────────────────────────
def traiter_ligne(line: str):
    if not line.startswith("{"):
        # Message texte (debug Arduino) — on l'affiche seulement s'il est ASCII pur
        if line and line.isascii():
            print(f"[dbg] {line}", flush=True)
        return

    # Nettoyer les valeurs manquantes générées par l'AVR
    cleaned = re.sub(r':\s*\?', ':0', line)     # "key":? → "key":0
    cleaned = re.sub(r':\s*,',  ':0,', cleaned)  # "key":, → "key":0,
    cleaned = re.sub(r':\s*}',  ':0}', cleaned)  # "key":} → "key":0}

    try:
        data = json.loads(cleaned)
    except json.JSONDecodeError:
        return   # JSON trop corrompu → ignorer silencieusement

    msg_type = data.get("type", "")
    ts       = time.strftime("%H:%M:%S")

    # ── Live (toutes les 2 s) → mise à jour tableau de bord ──────────────────
    if msg_type == "live" or (msg_type == "" and "temperature" in data):
        live = ecrire_live(data)
        # POST en DB uniquement si pas de champ "type" (Arduino legacy sans type)
        if msg_type == "":
            ok, alertes = post_capteurs(live)
            extra = f"  | {alertes} alerte(s)" if alertes else ""
            status = "OK " if ok else "ERR"
            t = live["temperature"]; h = live["humidite"]; g = live["gaz"]
            print(f"[{status}] {ts}  T:{t}  H:{h}%  G:{g}  PIR:{live['pir']}{extra}", flush=True)
        return

    # ── Données périodiques (toutes les 10 s) → enregistrement DB + alertes ──
    if msg_type == "donnees":
        payload = {
            "temperature": float(data.get("temperature") or 0),
            "humidite":    float(data.get("humidite")    or 0),
            "gaz":         int  (data.get("gaz")         or 0),
            "pir":         int  (data.get("pir")         or 0),
            "salle_id":    data.get("salle_id", 1),
        }
        # Mettre à jour le fichier live aussi (double sécurité dashboard)
        ecrire_live(data)
        ok, alertes = post_capteurs(payload)
        t   = payload["temperature"]
        h   = payload["humidite"]
        g   = payload["gaz"]
        pir = payload["pir"]
        extra = f"  | {alertes} alerte(s)" if alertes else ""
        status = "OK " if ok else "ERR"
        print(f"[{status}] {ts}  T:{t}  H:{h}%  G:{g}  PIR:{pir}{extra}", flush=True)

    # ── Alerte immédiate → email + SMS déclenchés sans attendre les 10 s ─────
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
        ok, _ = post_capteurs(payload)
        label  = "PIR" if cat == "INTRUSION" else "ALT"
        status = "OK " if ok else "ERR"
        print(f"[{label}][{status}] {ts}  {cat} {niv} -> {'email envoyé' if ok else 'ERREUR'}", flush=True)


# ── Boucle principale ─────────────────────────────────────────────────────────
def run():
    UPLOAD_SIGNAL = "/tmp/arduino_upload"

    while True:

        # ── Pause pendant un upload Arduino (flash IDE) ───────────────────────
        if os.path.exists(UPLOAD_SIGNAL):
            print("[RELAY] Upload Arduino détecté — port libéré.", flush=True)
            while os.path.exists(UPLOAD_SIGNAL):
                time.sleep(0.5)
            print("[RELAY] Upload terminé — reprise.", flush=True)
            time.sleep(1.0)
            continue

        # ── Attendre qu'un port Arduino soit disponible ───────────────────────
        port = trouver_port()
        if not port:
            time.sleep(1.0)   # polling rapide sans message (évite les spams)
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

                # ── Céder le port pour un upload ──────────────────────────────
                if os.path.exists(UPLOAD_SIGNAL):
                    try: ser.reset_input_buffer()
                    except Exception: pass
                    ser.close()
                    break

                # ── Lire un octet ─────────────────────────────────────────────
                try:
                    byte = ser.read(1)
                except serial.SerialException:
                    break   # déconnexion → sortir pour reconnecter

                # ── Timeout (pas de données) : continuer d'attendre ──────────
                # La déconnexion est détectée via SerialException, pas via
                # os.path.exists (qui peut être faux-positif pendant le reset USB).
                if not byte:
                    continue

                # ── Accumuler jusqu'au saut de ligne ─────────────────────────
                if byte == b"\n":
                    line = _clean(buf)
                    buf  = b""
                    if line:
                        try:
                            traiter_ligne(line)
                        except Exception as ex:
                            pass   # erreur inattendue → ignorer silencieusement
                else:
                    buf += byte
                    if len(buf) > 512:   # sécurité anti-débordement
                        buf = b""

        except Exception:
            pass

        try: ser.close()
        except Exception: pass

        # Supprimer le fichier live immédiatement → dashboard affiche "déconnecté" sans délai
        try: os.remove(LIVE_FILE)
        except OSError: pass

        print(f"[RELAY] Déconnecté — reconnexion dans {RETRY_DELAY}s...", flush=True)
        time.sleep(RETRY_DELAY)


if __name__ == "__main__":
    run()
