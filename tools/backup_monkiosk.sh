#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-/var/backups/monkiosk}"
RETENTION_COUNT="${RETENTION_COUNT:-14}"
TIMESTAMP="$(date +%F_%H%M%S)"
ARCHIVE_NAME="monkiosk_backup_${TIMESTAMP}.tar.gz"
ARCHIVE_PATH="${BACKUP_DIR}/${ARCHIVE_NAME}"

SOURCES=(
  "home/kiosk/kioskos"
  "var/www/html"
  "etc/nginx/sites-available/default"
)

require_root() {
  if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: este script debe ejecutarse como root." >&2
    exit 1
  fi
}

ensure_backup_dir() {
  install -d -m 750 "${BACKUP_DIR}"
}

create_archive() {
  echo "Creando backup: ${ARCHIVE_PATH}"
  tar -czpf "${ARCHIVE_PATH}" -C / "${SOURCES[@]}"
}

verify_archive() {
  if [ ! -s "${ARCHIVE_PATH}" ]; then
    echo "ERROR: no se ha creado el archivo de backup." >&2
    exit 1
  fi
  tar -tzf "${ARCHIVE_PATH}" >/dev/null
}

apply_retention() {
  mapfile -t archives < <(find "${BACKUP_DIR}" -maxdepth 1 -type f -name 'monkiosk_backup_*.tar.gz' | sort)
  if [ "${#archives[@]}" -le "${RETENTION_COUNT}" ]; then
    return
  fi

  local remove_count=$(( ${#archives[@]} - RETENTION_COUNT ))
  for ((i=0; i<remove_count; i++)); do
    rm -f -- "${archives[$i]}"
  done
}

show_summary() {
  local size
  size="$(du -h "${ARCHIVE_PATH}" | awk '{print $1}')"
  echo "Backup completado."
  echo "Archivo: ${ARCHIVE_PATH}"
  echo "Tamaño: ${size}"
  echo "Retención: ${RETENTION_COUNT} copias"
}

main() {
  require_root
  ensure_backup_dir
  create_archive
  verify_archive
  apply_retention
  show_summary
}

main "$@"
