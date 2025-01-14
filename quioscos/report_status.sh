#!/bin/bash

# Configuración
server_url="http://<IP_DEL_SERVIDOR>/update_status.php"  # Cambia aquí a update_status.php
quiosco_name=$(hostname)  # Obtén el nombre del dispositivo usando el comando hostname

# Función para reportar el estado al servidor
report_status() {
    curl -X POST -H "Content-Type: application/json" \
        -d "{\"name\": \"$quiosco_name\", \"status\": \"Online\"}" \
        "$server_url"
}

# Enviar estado cada 60 segundos
while true; do
    report_status
    sleep 60
done
