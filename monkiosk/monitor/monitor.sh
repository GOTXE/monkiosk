#!/bin/bash

# Variables globales
ips_file="/var/www/html/monkiosk/ips.txt"  # Archivo que contiene las IPs y nombres
status_file="/var/www/html/monkiosk/status.txt"  # Archivo donde se guardará el estado de los equipos
server_url="http://localhost/index.php"  # URL a verificar del servidor local
check_interval=60  # Intervalo de comprobación en segundos

# Función para hacer ping a una IP
ping_ip() {
    local ip=$1
    ping -c 1 -W 1 "$ip" > /dev/null 2>&1
    return $?
}

# Función para verificar el estado del servidor
check_server_status() {
    curl -Is "$server_url" | head -n 1 | grep "200 OK" > /dev/null
    return $?
}

# Función para leer las IPs y nombres de un archivo
load_ips() {
    if [[ ! -f "$ips_file" ]]; then
        echo "Error: No se pudo encontrar el archivo $ips_file"
        exit 1
    fi
    cat "$ips_file"
}

# Función para escribir el estado en el archivo status.txt
write_status() {
    local ips_status=$1
    local server_status=$2

    # Añadir el formato adecuado para el servidor (con IP ficticia 127.0.0.1 para el servidor local)
    echo "SERVIDOR (127.0.0.1): $server_status" > "$status_file"
    
    # Escribir los estados de los equipos (quioscos) a continuación
    echo -e "$ips_status" >> "$status_file"  # Usar echo -e para procesar el '\n' como un salto de línea
    }

# Función principal para realizar las comprobaciones periódicas
main() {
    while true; do
        local ips_status=""
        local server_status="Offline"

        # Verificar el estado del servidor
        if check_server_status; then
            server_status="Online"
        fi

        # Leer las IPs y comprobar el estado de cada una
        while IFS=$'\t' read -r ip name; do
            if ping_ip "$ip"; then
                ips_status+="$name ($ip): Online\n"
            else
                ips_status+="$name ($ip): Offline\n"
            fi
        done < <(load_ips)

        # Escribir los resultados en el archivo status.txt
        write_status "$ips_status" "$server_status"

        # Esperar el intervalo antes de volver a comprobar
        echo "Comprobación completada. Próxima comprobación en $check_interval segundos."
        sleep "$check_interval"
    done
}

# Ejecutar la función principal
main
