#!/bin/bash
set -e

echo "[1/4] Chargement des drivers USB série..."
modprobe cdc_acm  2>/dev/null && echo "  cdc_acm  OK" || echo "  cdc_acm  introuvable"
modprobe ch341    2>/dev/null && echo "  ch341    OK" || echo "  ch341    introuvable"
modprobe cp210x   2>/dev/null && echo "  cp210x   OK" || echo "  cp210x   introuvable"
modprobe ftdi_sio 2>/dev/null && echo "  ftdi_sio OK" || echo "  ftdi_sio introuvable"

echo "[2/4] Persistance au démarrage..."
cat > /etc/modules-load.d/arduino.conf << 'EOF'
cdc_acm
ch341
cp210x
ftdi_sio
EOF
echo "  /etc/modules-load.d/arduino.conf créé"

echo "[3/4] Règles udev..."
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
echo "  /etc/udev/rules.d/99-arduino.rules créé"

echo "[4/4] Rechargement udev..."
udevadm control --reload-rules
udevadm trigger

echo ""
echo "=== TERMINÉ ==="
echo "Débranche et rebranche l'Arduino — le relay redémarrera automatiquement."
echo "Vérifie avec : ls /dev/ttyACM* /dev/ttyUSB* /dev/arduino 2>/dev/null"
