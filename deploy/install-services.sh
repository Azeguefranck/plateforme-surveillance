#!/bin/bash
# Installation des services systemd pour la plateforme SUPSERVER
# Lancer avec : sudo bash deploy/install-services.sh

set -e

PROJECT_DIR="/opt/lampp/htdocs/plateforme_de_surveillance_des_salles_serveurs"

echo "==> Copie du script relay-upload-monitor..."
cp "$PROJECT_DIR/deploy/relay-upload-monitor.sh" /usr/local/bin/relay-upload-monitor
chmod +x /usr/local/bin/relay-upload-monitor

echo "==> Installation des services systemd..."
cp "$PROJECT_DIR/deploy/serial-relay.service" /etc/systemd/system/
cp "$PROJECT_DIR/deploy/relay-upload-monitor.service" /etc/systemd/system/

echo "==> Rechargement systemd..."
systemctl daemon-reload

echo "==> Activation et démarrage des services..."
systemctl enable serial-relay.service
systemctl enable relay-upload-monitor.service
systemctl start serial-relay.service
systemctl start relay-upload-monitor.service

echo ""
echo "Services installés et démarrés."
echo "  Status : sudo systemctl status serial-relay.service"
echo "  Logs   : sudo journalctl -u serial-relay.service -f"
