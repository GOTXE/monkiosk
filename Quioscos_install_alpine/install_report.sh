#!/bin/sh
# Instalador no interactivo de heartbeat para Alpine + OpenRC
# Estructura esperada:
#   Quioscos_install_alpine/
#     install_report.sh
#     report/
#       report_status.sh
#       kioskmonitoring.openrc
#       heartbeat.conf.example

set -eu

BASE_DIR="$(CDPATH= cd -- "$(dirname -- "$0")" && pwd)"
REPORT_DIR="$BASE_DIR/report"

SOURCE_SCRIPT="$REPORT_DIR/report_status.sh"
OPENRC_TEMPLATE="$REPORT_DIR/kioskmonitoring.openrc"
CONFIG_TEMPLATE="$REPORT_DIR/heartbeat.conf.example"

INSTALL_DIR="/opt/monitoring"
CONFIG_DIR="/etc/kiosk"
CONFIG_FILE="$CONFIG_DIR/heartbeat.conf"
SERVICE_FILE="/etc/init.d/kioskmonitoring"

PRIMARY_URL=""
FALLBACK_URL=""
INTERVAL="60"
MAX_RETRIES="3"
RETRY_INTERVAL="10"
CONNECT_TIMEOUT="3"
MAX_TIME="5"
KIOSK_URL=""
START_SERVICE="1"
ENABLE_SERVICE="1"
FORCE_CONFIG="0"

usage() {
    cat <<USAGE
Uso:
  $0 --primary-url URL [opciones]

Opciones:
  --primary-url URL        URL principal (obligatoria)
  --fallback-url URL       URL fallback (opcional)
  --interval N             Intervalo en segundos (default: 60)
  --max-retries N          Reintentos por endpoint (default: 3)
  --retry-interval N       Espera entre reintentos (default: 10)
  --connect-timeout N      Timeout de conexión curl (default: 3)
  --max-time N             Timeout total curl (default: 5)
  --kiosk-url URL          URL local del quiosco (opcional)
  --no-start               No arrancar servicio al final
  --no-enable              No habilitar en arranque OpenRC
  --force-config           Sobrescribir /etc/kiosk/heartbeat.conf si existe
  -h, --help               Mostrar ayuda
USAGE
}

require_root() {
    if [ "$(id -u)" -ne 0 ]; then
        echo "ERROR: ejecutar como root" >&2
        exit 1
    fi
}

require_cmd() {
    for c in "$@"; do
        if ! command -v "$c" >/dev/null 2>&1; then
            echo "ERROR: comando requerido no encontrado: $c" >&2
            exit 1
        fi
    done
}

is_int() {
    case "$1" in
        ''|*[!0-9]*) return 1 ;;
        *) return 0 ;;
    esac
}

while [ "$#" -gt 0 ]; do
    case "$1" in
        --primary-url) PRIMARY_URL="${2:-}"; shift 2 ;;
        --fallback-url) FALLBACK_URL="${2:-}"; shift 2 ;;
        --interval) INTERVAL="${2:-}"; shift 2 ;;
        --max-retries) MAX_RETRIES="${2:-}"; shift 2 ;;
        --retry-interval) RETRY_INTERVAL="${2:-}"; shift 2 ;;
        --connect-timeout) CONNECT_TIMEOUT="${2:-}"; shift 2 ;;
        --max-time) MAX_TIME="${2:-}"; shift 2 ;;
        --kiosk-url) KIOSK_URL="${2:-}"; shift 2 ;;
        --no-start) START_SERVICE="0"; shift ;;
        --no-enable) ENABLE_SERVICE="0"; shift ;;
        --force-config) FORCE_CONFIG="1"; shift ;;
        -h|--help) usage; exit 0 ;;
        *) echo "ERROR: opción desconocida: $1" >&2; usage; exit 1 ;;
    esac
done

require_root
require_cmd install cp rc-update rc-service

if [ -z "$PRIMARY_URL" ]; then
    echo "ERROR: --primary-url es obligatoria" >&2
    usage
    exit 1
fi

for n in "$INTERVAL" "$MAX_RETRIES" "$RETRY_INTERVAL" "$CONNECT_TIMEOUT" "$MAX_TIME"; do
    if ! is_int "$n"; then
        echo "ERROR: valor no numérico: $n" >&2
        exit 1
    fi
done

for f in "$SOURCE_SCRIPT" "$OPENRC_TEMPLATE" "$CONFIG_TEMPLATE"; do
    if [ ! -f "$f" ]; then
        echo "ERROR: falta archivo requerido: $f" >&2
        exit 1
    fi
done

install -d -m 755 "$INSTALL_DIR" "$CONFIG_DIR"
cp "$SOURCE_SCRIPT" "$INSTALL_DIR/report_status.sh"
chmod 755 "$INSTALL_DIR/report_status.sh"

if [ -f "$CONFIG_FILE" ] && [ "$FORCE_CONFIG" != "1" ]; then
    echo "INFO: $CONFIG_FILE ya existe; no se sobrescribe (usar --force-config)"
else
    cat > "$CONFIG_FILE" <<CFG
PRIMARY_URL="$PRIMARY_URL"
FALLBACK_URL="$FALLBACK_URL"
INTERVAL=$INTERVAL
MAX_RETRIES=$MAX_RETRIES
RETRY_INTERVAL=$RETRY_INTERVAL
CONNECT_TIMEOUT=$CONNECT_TIMEOUT
MAX_TIME=$MAX_TIME
KIOSK_URL="$KIOSK_URL"
CFG
    chmod 644 "$CONFIG_FILE"
fi

cp "$OPENRC_TEMPLATE" "$SERVICE_FILE"
chmod 755 "$SERVICE_FILE"

if [ "$ENABLE_SERVICE" = "1" ]; then
    rc-update add kioskmonitoring default >/dev/null 2>&1 || true
fi

if [ "$START_SERVICE" = "1" ]; then
    rc-service kioskmonitoring restart >/dev/null 2>&1 || rc-service kioskmonitoring start >/dev/null 2>&1 || true
fi

echo "Instalación completada"
echo "- Script: $INSTALL_DIR/report_status.sh"
echo "- Config: $CONFIG_FILE"
echo "- Servicio: $SERVICE_FILE"
echo "- Primary URL: $PRIMARY_URL"
if [ -n "$FALLBACK_URL" ]; then
    echo "- Fallback URL: $FALLBACK_URL"
fi
