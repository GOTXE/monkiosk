#!/usr/bin/env bash
set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-/var/backups/monkiosk}"
DRY_RUN=0
SELECTED_ARCHIVE=""

RESTORE_TARGETS=(
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

usage() {
  cat <<'EOF'
Uso:
  restore_monkiosk.sh [--dry-run] [--file /ruta/al/backup.tar.gz]

Opciones:
  --dry-run   Simula la restauración sin sobrescribir archivos.
  --file      Usa directamente el backup indicado.
  --help      Muestra esta ayuda.
EOF
}

parse_args() {
  while [ "$#" -gt 0 ]; do
    case "$1" in
      --dry-run)
        DRY_RUN=1
        shift
        ;;
      --file)
        SELECTED_ARCHIVE="${2:-}"
        if [ -z "${SELECTED_ARCHIVE}" ]; then
          echo "ERROR: falta la ruta tras --file." >&2
          exit 1
        fi
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

list_archives() {
  find "${BACKUP_DIR}" -maxdepth 1 -type f -name 'monkiosk_backup_*.tar.gz' | sort -r
}

pick_archive_interactive() {
  mapfile -t archives < <(list_archives)
  if [ "${#archives[@]}" -eq 0 ]; then
    echo "ERROR: no hay backups disponibles en ${BACKUP_DIR}." >&2
    exit 1
  fi

  echo "Backups disponibles:"
  local i=1
  for archive in "${archives[@]}"; do
    printf "  %d) %s\n" "${i}" "$(basename "${archive}")"
    i=$((i + 1))
  done

  while true; do
    printf "Elige el número del backup que quieres restaurar: "
    read -r choice
    if [[ "${choice}" =~ ^[0-9]+$ ]] && [ "${choice}" -ge 1 ] && [ "${choice}" -le "${#archives[@]}" ]; then
      SELECTED_ARCHIVE="${archives[$((choice - 1))]}"
      return
    fi
    echo "Valor no válido. Escribe solo el número mostrado en la lista."
  done
}

validate_archive() {
  if [ -z "${SELECTED_ARCHIVE}" ]; then
    pick_archive_interactive
  fi

  if [ ! -f "${SELECTED_ARCHIVE}" ]; then
    echo "ERROR: no existe el backup indicado: ${SELECTED_ARCHIVE}" >&2
    exit 1
  fi

  tar -tzf "${SELECTED_ARCHIVE}" >/dev/null
}

show_restore_plan() {
  echo
  echo "Se va a usar este backup:"
  echo "  ${SELECTED_ARCHIVE}"
  echo
  echo "Elementos que cubre la restauración:"
  for target in "${RESTORE_TARGETS[@]}"; do
    echo "  - /${target}"
  done
  echo
}

confirm_restore() {
  if [ "${DRY_RUN}" -eq 1 ]; then
    return
  fi

  echo "ATENCIÓN: esta acción sobrescribirá la instalación actual de Monkiosk."
  printf "Escribe RESTAURAR para continuar o pulsa Enter para cancelar: "
  read -r answer
  if [ "${answer}" != "RESTAURAR" ]; then
    echo "Restauración cancelada."
    exit 0
  fi
}

create_safeguard_backup() {
  local safeguard_name="monkiosk_pre_restore_$(date +%F_%H%M%S).tar.gz"
  local safeguard_path="${BACKUP_DIR}/${safeguard_name}"

  echo "Creando copia de seguridad previa: ${safeguard_path}"
  tar -czpf "${safeguard_path}" -C / "${RESTORE_TARGETS[@]}"
}

run_dry_run() {
  echo "Simulación de restauración activada."
  echo "Se restaurarían estos archivos:"
  tar -tzf "${SELECTED_ARCHIVE}"
  echo
  echo "No se ha sobrescrito ningún archivo."
}

restore_archive() {
  echo "Restaurando backup..."
  tar -xzpf "${SELECTED_ARCHIVE}" -C /
}

reload_services() {
  if command -v nginx >/dev/null 2>&1; then
    nginx -t
    systemctl reload nginx >/dev/null 2>&1 || systemctl restart nginx >/dev/null 2>&1 || true
  fi
}

show_finish_message() {
  echo
  echo "Restauración finalizada."
  echo "Comprobaciones recomendadas:"
  echo "  1. Abrir la web principal del quiosco."
  echo "  2. Entrar en estado_quioscos."
  echo "  3. Revisar que nginx responde correctamente."
}

main() {
  require_root
  parse_args "$@"
  validate_archive
  show_restore_plan
  confirm_restore

  if [ "${DRY_RUN}" -eq 1 ]; then
    run_dry_run
    exit 0
  fi

  create_safeguard_backup
  restore_archive
  reload_services
  show_finish_message
}

main "$@"
