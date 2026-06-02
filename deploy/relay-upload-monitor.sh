#!/bin/bash
# Libère le port série automatiquement quand avrdude lance un upload

while true; do
    if pgrep -f avrdude > /dev/null 2>&1; then
        systemctl stop serial-relay.service 2>/dev/null
        while pgrep -f avrdude > /dev/null 2>&1; do
            sleep 0.5
        done
        sleep 3
        systemctl start serial-relay.service 2>/dev/null
    fi
    sleep 1
done
