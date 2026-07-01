#!/bin/bash
# Libère le port série en moins de 1s dès qu'avrdude démarre

SIGNAL="/tmp/arduino_upload"

while true; do
    if pgrep -f avrdude > /dev/null 2>&1; then
        touch "$SIGNAL"
        while pgrep -f avrdude > /dev/null 2>&1; do
            sleep 0.2
        done
        sleep 2
        rm -f "$SIGNAL"
    fi
    sleep 0.2
done
