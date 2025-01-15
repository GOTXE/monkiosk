#!/bin/bash

# Configuración
server_url="http://<IP_DEL_SERVIDOR>/update_status.php"  # URL del servidor para enviar el estado
quiosco_name=$(hostname)  # Obtiene el nombre del dispositivo usando el comando hostname

# Función para reportar el estado al servidor# Reintento en caso de fallo
max_retries=3
retry_interval=10  # Segundos entre reintentos
interval=10  # Segundos entre envios

    # This function attempts to report the kiosk's status to the server by sending a POST request
    # with JSON data containing the kiosk's name and status. It retries the request up to a maximum
    # number of attempts if it fails, waiting for a specified interval between each retry. If all
    # attempts fail, it logs an error message and returns a failure code.

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
