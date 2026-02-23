#!/bin/sh
# Heartbeat para quioscos (compatible con Alpine/BusyBox ash)

set -eu

# Se puede sobreescribir en /etc/kiosk/heartbeat.conf
PRIMARY_URL="http://<IP_DEL_SERVIDOR>/estado_quioscos/update_status.php"
FALLBACK_URL=""
INTERVAL=60
MAX_RETRIES=3
RETRY_INTERVAL=10
CONNECT_TIMEOUT=3
MAX_TIME=5
KIOSK_URL=""

CONFIG_FILE="/etc/kiosk/heartbeat.conf"
if [ -f "$CONFIG_FILE" ]; then
    # shellcheck disable=SC1090
    . "$CONFIG_FILE"
fi

KIOSK_NAME="$(hostname)"
LOG_FILE="/var/log/kiosk-heartbeat.log"

json_escape() {
    printf '%s' "$1" | sed 's/\\/\\\\/g; s/"/\\"/g'
}

get_local_ip() {
    ip -4 route get 1.1.1.1 2>/dev/null | awk '{
        for (i = 1; i <= NF; i++) if ($i == "src") { print $(i+1); exit }
    }'
}

get_fallback_ip() {
    ip -4 -o addr show scope global 2>/dev/null | awk '{
        split($4, parts, "/");
        print parts[1];
        exit
    }'
}

get_uptime_s() {
    cut -d. -f1 /proc/uptime 2>/dev/null || echo 0
}

get_load1() {
    awk '{print $1}' /proc/loadavg 2>/dev/null || echo "0.00"
}

get_mem_free_mb() {
    awk '/MemAvailable/ {printf "%.0f", $2/1024}' /proc/meminfo 2>/dev/null || echo 0
}

get_disk_free_mb() {
    df -Pm / 2>/dev/null | awk 'NR==2 {print $4}' || echo 0
}

build_payload() {
    target="$1"
    local_ip="$(get_local_ip)"
    if [ -z "$local_ip" ]; then
        local_ip="$(get_fallback_ip)"
    fi

    kiosk_url="$KIOSK_URL"
    if [ -z "$kiosk_url" ] && [ -n "$local_ip" ]; then
        kiosk_url="http://$local_ip/"
    fi

    uptime_s="$(get_uptime_s)"
    load1="$(get_load1)"
    mem_free_mb="$(get_mem_free_mb)"
    disk_free_mb="$(get_disk_free_mb)"

    printf '{'
    printf '"name":"%s",' "$(json_escape "$KIOSK_NAME")"
    printf '"status":"Online",'
    printf '"local_ip":"%s",' "$(json_escape "$local_ip")"
    printf '"kiosk_url":"%s",' "$(json_escape "$kiosk_url")"
    printf '"uptime_s":%s,' "$uptime_s"
    printf '"load1":"%s",' "$(json_escape "$load1")"
    printf '"mem_free_mb":%s,' "$mem_free_mb"
    printf '"disk_free_mb":%s,' "$disk_free_mb"
    printf '"report_target":"%s"' "$(json_escape "$target")"
    printf '}'
}

send_with_retries() {
    url="$1"
    payload="$2"
    attempt=1
    while [ "$attempt" -le "$MAX_RETRIES" ]; do
        code="$(curl -s -o /dev/null -w "%{http_code}" \
            --connect-timeout "$CONNECT_TIMEOUT" \
            --max-time "$MAX_TIME" \
            -X POST -H "Content-Type: application/json" \
            -d "$payload" "$url" || echo "000")"

        if [ "$code" = "200" ]; then
            echo "$(date '+%F %T') heartbeat OK -> $url" >> "$LOG_FILE"
            return 0
        fi

        echo "$(date '+%F %T') intento $attempt/$MAX_RETRIES fallido ($code) -> $url" >> "$LOG_FILE"
        attempt=$((attempt + 1))
        sleep "$RETRY_INTERVAL"
    done
    return 1
}

while true; do
    start_time="$(date +%s)"

    primary_payload="$(build_payload "$PRIMARY_URL")"
    if ! send_with_retries "$PRIMARY_URL" "$primary_payload"; then
        if [ -n "$FALLBACK_URL" ]; then
            fallback_payload="$(build_payload "$FALLBACK_URL")"
            if ! send_with_retries "$FALLBACK_URL" "$fallback_payload"; then
                echo "$(date '+%F %T') heartbeat FAIL en primary + fallback" >> "$LOG_FILE"
            fi
        else
            echo "$(date '+%F %T') heartbeat FAIL en primary (sin fallback)" >> "$LOG_FILE"
        fi
    fi

    end_time="$(date +%s)"
    elapsed=$((end_time - start_time))
    sleep_time=$((INTERVAL - elapsed))
    if [ "$sleep_time" -gt 0 ]; then
        sleep "$sleep_time"
    fi
done
