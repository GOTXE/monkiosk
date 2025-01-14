#!/bin/bash

# Configuración
server_url="http://localhost:8000/update_status.php"  # Cambia aquí a update_status.php
quiosco_name=$(hostname)  # Obtén el nombre del dispositivo usando el comando hostname

# Función para reportar el estado al servidor# Reintento en caso de fallo
max_retries=3
retry_interval=10  # Segundos entre reintentos
interval=10  # Segundos entre envios

report_status() {
    for ((i=1; i<=max_retries; i++)); do
        curl -X POST -H "Content-Type: application/json" \
            -d "{\"name\": \"$quiosco_name\", \"status\": \"Online\"}" \
            "$server_url" && return 0
        echo "Intento $i fallido. Reintentando en $retry_interval segundos..."
        sleep $retry_interval
    done
    echo "No se pudo conectar al servidor después de $max_retries intentos."
    return 1
}

# Enviar estado cada 60 segundos
while true; do
    start_time=$(date +%s)
    report_status
    end_time=$(date +%s)

    # Ajustar el intervalo para mantener un envío constante cada 60 segundos
    elapsed=$((end_time - start_time))
    sleep_time=$((interval - elapsed))
    if (( sleep_time > 0 )); then
        sleep $sleep_time
    fi
done
