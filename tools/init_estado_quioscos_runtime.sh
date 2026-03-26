#!/usr/bin/env bash
set -euo pipefail

TARGET_DIR="${TARGET_DIR:-/var/www/html/estado_quioscos}"
ADMIN_USER="${ADMIN_USER:-}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-}"
CONTROL_TOKEN_VALUE="${CONTROL_TOKEN_VALUE:-}"

usage() {
  cat <<'EOF'
Uso:
  sudo ./tools/init_estado_quioscos_runtime.sh [opciones]

Opciones:
  --target-dir RUTA        Directorio de estado_quioscos. Por defecto: /var/www/html/estado_quioscos
  --admin-user USUARIO     Usuario inicial del panel web
  --admin-password CLAVE   Contraseña inicial del panel web
  --control-token TOKEN    Token para control_config.php
  --help                   Mostrar ayuda
EOF
}

require_root() {
  if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: este script debe ejecutarse como root." >&2
    exit 1
  fi
}

parse_args() {
  while [ "$#" -gt 0 ]; do
    case "$1" in
      --target-dir)
        TARGET_DIR="${2:-}"
        shift 2
        ;;
      --admin-user)
        ADMIN_USER="${2:-}"
        shift 2
        ;;
      --admin-password)
        ADMIN_PASSWORD="${2:-}"
        shift 2
        ;;
      --control-token)
        CONTROL_TOKEN_VALUE="${2:-}"
        shift 2
        ;;
      --help|-h)
        usage
        exit 0
        ;;
      *)
        echo "ERROR: opción no reconocida: $1" >&2
        usage
        exit 1
        ;;
    esac
  done
}

ensure_target_dir() {
  if [ ! -d "${TARGET_DIR}" ]; then
    echo "ERROR: no existe el directorio ${TARGET_DIR}" >&2
    exit 1
  fi
}

write_file_if_missing() {
  local path="$1"
  local mode="$2"
  local content="$3"

  if [ -e "${path}" ]; then
    return
  fi

  printf '%b' "${content}" > "${path}"
  chmod "${mode}" "${path}"
}

random_token() {
  tr -dc 'A-Za-z0-9' </dev/urandom | head -c 48
}

create_auth_users() {
  local path="${TARGET_DIR}/auth_users.json"
  if [ -e "${path}" ]; then
    return
  fi

  if [ -z "${ADMIN_USER}" ]; then
    ADMIN_USER="admin"
  fi
  if [ -z "${ADMIN_PASSWORD}" ]; then
    ADMIN_PASSWORD="$(random_token)"
    echo "Contraseña inicial generada para ${ADMIN_USER}: ${ADMIN_PASSWORD}"
  fi

  local hash
  hash="$(php -r 'echo password_hash($argv[1], PASSWORD_DEFAULT);' "${ADMIN_PASSWORD}")"

  cat > "${path}" <<EOF
{
    "${ADMIN_USER}": "${hash}"
}
EOF
  chmod 640 "${path}"
}

create_control_config() {
  local path="${TARGET_DIR}/control_config.php"
  if [ -e "${path}" ]; then
    return
  fi
  if [ -z "${CONTROL_TOKEN_VALUE}" ]; then
    CONTROL_TOKEN_VALUE="$(random_token)"
    echo "Token inicial generado para control remoto."
  fi

  cat > "${path}" <<EOF
<?php
define('CONTROL_TOKEN', '${CONTROL_TOKEN_VALUE}');
EOF
  chmod 640 "${path}"
}

main() {
  require_root
  parse_args "$@"
  ensure_target_dir

  write_file_if_missing "${TARGET_DIR}/allowed_hosts.txt" 640 ""
  write_file_if_missing "${TARGET_DIR}/allowed_kiosks.json" 640 "{\n    \"updated_at\": \"\",\n    \"items\": []\n}\n"
  write_file_if_missing "${TARGET_DIR}/allowed_kiosks_protection.json" 640 "{\n    \"report_enabled\": true,\n    \"presentation_enabled\": true,\n    \"updated_at\": \"\"\n}\n"
  write_file_if_missing "${TARGET_DIR}/status.json" 640 "{}\n"
  write_file_if_missing "${TARGET_DIR}/actions.json" 640 "{}\n"
  write_file_if_missing "${TARGET_DIR}/slide_settings.json" 640 "{\n    \"slide_interval_seconds\": 5,\n    \"updated_at\": \"\"\n}\n"
  write_file_if_missing "${TARGET_DIR}/overlay_config.json" 640 "{\n    \"enabled\": false,\n    \"target_kiosk\": \"\",\n    \"updated_at\": \"\"\n}\n"
  write_file_if_missing "${TARGET_DIR}/unknown_kiosk_attempts.json" 640 "[]\n"
  write_file_if_missing "${TARGET_DIR}/presentation_viewers.json" 640 "[]\n"

  create_auth_users
  create_control_config

  echo "Inicialización completada en ${TARGET_DIR}"
}

main "$@"
