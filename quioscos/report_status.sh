#!/bin/bash

# Configuración
server_url="http://servidor-central/endpoint.php"  # URL del servidor para recibir el POST
quiosco_name=$(hostname)  

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
