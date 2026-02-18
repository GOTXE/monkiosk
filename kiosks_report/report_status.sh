#!/bin/bash

# Configuración
server_url="http://<IP_DEL_SERVIDOR>/estado_quioscos/update_status.php"  # URL del servidor para enviar el estado
quiosco_name=$(hostname)  # Obtiene el nombre del dispositivo usando el comando hostname

# Configuración de reintentos y tiempos
max_retries=3
retry_interval=10  # Segundos entre reintentos
interval=60  # Intervalo en segundos entre envíos de estado

# Función para reportar el estado al servidor
report_status() {
    for ((i=1; i<=max_retries; i++)); do
        # Datos JSON a enviar
        json_data="{\"name\": \"$quiosco_name\", \"status\": \"Online\"}"
        echo "Enviando datos JSON: $json_data"
        echo "URL del servidor: $server_url"

        # Realiza la solicitud POST
        response=$(curl -s -w "%{http_code}" -X POST -H "Content-Type: application/json" \
            -d "$json_data" \
            "$server_url")
        http_code="${response: -3}"  # Extrae el código HTTP de la respuesta
        echo "Respuesta del servidor: $response"
        echo "Código HTTP: $http_code"

        if [[ $http_code -eq 200 ]]; then
            echo "Estado reportado correctamente al servidor."
            return 0
        fi
        echo "Intento $i fallido con código HTTP: $http_code. Reintentando en $retry_interval segundos..."
        sleep $retry_interval
    done
    echo "No se pudo conectar al servidor después de $max_retries intentos." >&2
    return 1
}

# Ciclo principal para enviar estado
while true; do
    start_time=$(date +%s)
    
    if ! report_status; then
        echo "Error crítico: No se pudo reportar el estado del quiosco." >&2
    fi

    end_time=$(date +%s)
    elapsed=$((end_time - start_time))
    sleep_time=$((interval - elapsed))
    if ((sleep_time > 0)); then
        sleep $sleep_time
    fi
done