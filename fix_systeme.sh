#!/bin/bash
set -e

echo "=== 1. Correction serial-relay.service (Restart=always) ==="
cat > /etc/systemd/system/serial-relay.service << 'EOF'
[Unit]
Description=Arduino Serial Relay
After=network.target surveillance-laravel.service
Wants=surveillance-laravel.service

[Service]
User=ahj
Group=dialout
WorkingDirectory=/opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs
ExecStart=/usr/bin/python3 /opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs/serial_relay.py
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
EOF

echo "=== 2. Correction relay-upload-monitor (signal file, 0.2s) ==="
cat > /usr/local/bin/relay-upload-monitor << 'EOF'
#!/bin/bash
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
EOF
chmod +x /usr/local/bin/relay-upload-monitor

echo "=== 3. Règles udev Arduino (tous chipsets) ==="
cat > /etc/udev/rules.d/99-arduino.rules << 'EOF'
# Permissions groupe dialout
SUBSYSTEMS=="usb", ATTRS{idVendor}=="2341", GROUP="dialout", MODE="0660"
SUBSYSTEMS=="usb", ATTRS{idVendor}=="1a86", GROUP="dialout", MODE="0660"
SUBSYSTEMS=="usb", ATTRS{idVendor}=="10c4", GROUP="dialout", MODE="0660"
SUBSYSTEMS=="usb", ATTRS{idVendor}=="0403", GROUP="dialout", MODE="0660"

# Symlink /dev/arduino + redémarrage relay automatique dès branchement
ACTION=="add", SUBSYSTEM=="tty", SUBSYSTEMS=="usb", ATTRS{idVendor}=="2341", SYMLINK+="arduino", RUN+="/bin/systemctl restart serial-relay.service"
ACTION=="add", SUBSYSTEM=="tty", SUBSYSTEMS=="usb", ATTRS{idVendor}=="1a86", SYMLINK+="arduino", RUN+="/bin/systemctl restart serial-relay.service"
ACTION=="add", SUBSYSTEM=="tty", SUBSYSTEMS=="usb", ATTRS{idVendor}=="10c4", SYMLINK+="arduino", RUN+="/bin/systemctl restart serial-relay.service"
ACTION=="add", SUBSYSTEM=="tty", SUBSYSTEMS=="usb", ATTRS{idVendor}=="0403", SYMLINK+="arduino", RUN+="/bin/systemctl restart serial-relay.service"
EOF

echo "=== 4. Rechargement udev ==="
udevadm control --reload-rules
udevadm trigger

echo "=== 5. Rechargement systemd et redémarrage services ==="
systemctl daemon-reload
systemctl enable serial-relay relay-upload-monitor surveillance-laravel surveillance-ngrok
systemctl restart serial-relay
systemctl restart relay-upload-monitor

echo ""
echo "=== ÉTAT DES SERVICES ==="
systemctl is-active serial-relay relay-upload-monitor surveillance-laravel surveillance-ngrok

echo ""
echo "TERMINÉ — Tout démarrera automatiquement au prochain démarrage."
