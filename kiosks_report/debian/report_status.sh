#!/bin/sh
# Heartbeat para quioscos (compatible con Alpine/BusyBox ash)

set -eu

# Valores por defecto (placeholder).
# La configuración real debe ir en /etc/kiosk/heartbeat.conf.
# Si ese archivo existe, sus valores tienen prioridad sobre estos.
PRIMARY_URL="http://<IP_DEL_SERVIDOR>/estado_quioscos/update_status.php"
FALLBACK_URL=""
INTERVAL=60
MAX_RETRIES=3
RETRY_INTERVAL=10
CONNECT_TIMEOUT=3
MAX_TIME=5
KIOSK_URL=""
CONTROL_TOKEN=""

CONFIG_FILE="/etc/kiosk/heartbeat.conf"
if [ -f "$CONFIG_FILE" ]; then
    # shellcheck disable=SC1090
    . "$CONFIG_FILE"
fi

normalize_report_url() {
    url_raw="$1"
    if [ -z "$url_raw" ]; then
        echo ""
        return 0
    fi

    url="$(printf '%s' "$url_raw" | sed 's/^[[:space:]]*//; s/[[:space:]]*$//')"
    case "$url" in
        *://*) ;;
        *) url="http://$url" ;;
    esac

    case "$url" in
        */update_status.php) echo "$url" ;;
        */get_action.php) echo "${url%/get_action.php}/update_status.php" ;;
        */estado_quioscos) echo "$url/update_status.php" ;;
        */estado_quioscos/) echo "${url}update_status.php" ;;
        *.php) echo "$url" ;;
        */) echo "${url}estado_quioscos/update_status.php" ;;
        *) echo "$url/estado_quioscos/update_status.php" ;;
    esac
}

PRIMARY_URL="$(normalize_report_url "$PRIMARY_URL")"
FALLBACK_URL="$(normalize_report_url "$FALLBACK_URL")"

KIOSK_NAME="$(hostname)"
LOG_FILE="/var/log/kiosk-heartbeat.log"
LAST_OK_URL=""

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

get_hdmi_status() {
    if ls /sys/class/drm/*HDMI*/status >/dev/null 2>&1; then
        for f in /sys/class/drm/*HDMI*/status; do
            if [ -f "$f" ]; then
                state="$(cat "$f" 2>/dev/null || true)"
                if [ "$state" = "connected" ]; then
                    echo "connected"
                    return 0
                fi
            fi
        done
        echo "disconnected"
        return 0
    fi
    echo "unknown"
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
    hdmi_connected="$(get_hdmi_status)"

    printf '{'
    printf '"name":"%s",' "$(json_escape "$KIOSK_NAME")"
    printf '"status":"Online",'
    printf '"local_ip":"%s",' "$(json_escape "$local_ip")"
    printf '"kiosk_url":"%s",' "$(json_escape "$kiosk_url")"
    printf '"uptime_s":%s,' "$uptime_s"
    printf '"load1":"%s",' "$(json_escape "$load1")"
    printf '"mem_free_mb":%s,' "$mem_free_mb"
    printf '"disk_free_mb":%s,' "$disk_free_mb"
    printf '"hdmi_connected":"%s",' "$(json_escape "$hdmi_connected")"
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
            LAST_OK_URL="$url"
            echo "$(date '+%F %T') heartbeat OK -> $url" >> "$LOG_FILE"
            return 0
        fi

        echo "$(date '+%F %T') intento $attempt/$MAX_RETRIES fallido ($code) -> $url" >> "$LOG_FILE"
        attempt=$((attempt + 1))
        sleep "$RETRY_INTERVAL"
    done
    return 1
}

derive_action_url() {
    printf '%s' "$1" | sed 's#/update_status\.php$#/get_action.php#'
}

fetch_pending_action() {
    target_url="$1"
    action_url="$(derive_action_url "$target_url")"
    if [ "$action_url" = "$target_url" ]; then
        return 0
    fi

    headers=""
    if [ -n "$CONTROL_TOKEN" ]; then
        headers="-H X-Control-Token:$CONTROL_TOKEN"
    fi

    # shellcheck disable=SC2086
    response="$(curl -s --connect-timeout "$CONNECT_TIMEOUT" --max-time "$MAX_TIME" \
        $headers \
        --get --data-urlencode "name=$KIOSK_NAME" "$action_url" || true)"

    action="$(printf '%s' "$response" | sed -n 's/.*"action"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' | head -n 1)"
    if [ "$action" = "reboot" ]; then
        echo "$(date '+%F %T') acción recibida: reboot" >> "$LOG_FILE"
        sync
        reboot
    fi
}

while true; do
    start_time="$(date +%s)"
    LAST_OK_URL=""

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

    if [ -n "$LAST_OK_URL" ]; then
        fetch_pending_action "$LAST_OK_URL"
    fi

    end_time="$(date +%s)"
    elapsed=$((end_time - start_time))
    sleep_time=$((INTERVAL - elapsed))
    if [ "$sleep_time" -gt 0 ]; then
        sleep "$sleep_time"
    fi
done
