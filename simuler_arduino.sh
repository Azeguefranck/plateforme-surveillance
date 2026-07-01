#!/bin/bash
# Simule les données Arduino toutes les 2 secondes
# S'arrête automatiquement dès que le vrai Arduino est détecté

API="http://localhost:8000/api/capteurs"
I=0

echo "Simulation démarrée — ouvre le tableau de bord"
echo "Le script s'arrête automatiquement quand le vrai Arduino est branché"
echo "Ctrl+C pour arrêter manuellement"
echo ""

while true; do
    # Arrêt automatique si le vrai Arduino est détecté
    if ls /dev/ttyACM* /dev/ttyUSB* /dev/arduino 2>/dev/null | grep -q .; then
        echo "Arduino réel détecté — simulation arrêtée"
        exit 0
    fi

    T=$(LC_ALL=C awk "BEGIN{printf \"%.1f\", 25 + ($I % 20) * 0.2}")
    H=$(( 58 + (I % 18) ))
    G=$(( 340 + (I % 25) * 4 ))
    PIR=$(( I % 30 == 0 ? 1 : 0 ))

    curl -s -X POST "$API" \
        -H "Content-Type: application/json" \
        -d "{\"temperature\":$T,\"humidite\":$H,\"gaz\":$G,\"pir\":$PIR,\"salle_id\":1}" > /dev/null

    echo "$(date +%H:%M:%S)  T=${T}°C  H=${H}%  G=${G}ppm  PIR=${PIR}"
    I=$(( I + 1 ))
    sleep 2
done
