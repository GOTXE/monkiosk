#!/usr/bin/env bash
set -euo pipefail

# Gestion de credenciales Basic Auth para /estado_quioscos
# Uso:
#   sudo tools/manage_estado_quioscos_basic_auth.sh set <usuario> [contrasena]
#   sudo tools/manage_estado_quioscos_basic_auth.sh del <usuario>
#   sudo tools/manage_estado_quioscos_basic_auth.sh list

HTPASSWD_FILE="/etc/nginx/.htpasswd_estado_quioscos"

usage() {
    cat <<'EOF'
Uso:
  sudo tools/manage_estado_quioscos_basic_auth.sh set <usuario> [contrasena]
  sudo tools/manage_estado_quioscos_basic_auth.sh del <usuario>
  sudo tools/manage_estado_quioscos_basic_auth.sh list
EOF
}

require_root() {
    if [[ "${EUID}" -ne 0 ]]; then
        echo "Este script requiere root (usa sudo)." >&2
        exit 1
    fi
}

read_password_if_missing() {
    local provided="${1:-}"
    if [[ -n "${provided}" ]]; then
        printf '%s' "${provided}"
        return
    fi

    local p1 p2
    read -r -s -p "Nueva contrasena: " p1
    echo
    read -r -s -p "Repetir contrasena: " p2
    echo
    if [[ -z "${p1}" ]]; then
        echo "Contrasena vacia no permitida." >&2
        exit 1
    fi
    if [[ "${p1}" != "${p2}" ]]; then
        echo "Las contrasenas no coinciden." >&2
        exit 1
    fi
    printf '%s' "${p1}"
}

set_user() {
    local user="$1"
    local pass="$2"
    local hash tmp

    hash="$(openssl passwd -apr1 "${pass}")"
    tmp="$(mktemp)"

    if [[ -f "${HTPASSWD_FILE}" ]]; then
        awk -F':' -v u="${user}" -v h="${hash}" '
            BEGIN {updated=0}
            $1 == u {print u ":" h; updated=1; next}
            {print}
            END {if (!updated) print u ":" h}
        ' "${HTPASSWD_FILE}" > "${tmp}"
    else
        printf '%s:%s\n' "${user}" "${hash}" > "${tmp}"
    fi

    install -o root -g www-data -m 640 "${tmp}" "${HTPASSWD_FILE}"
    rm -f "${tmp}"
    echo "Usuario actualizado: ${user}"
}

del_user() {
    local user="$1"
    local tmp

    if [[ ! -f "${HTPASSWD_FILE}" ]]; then
        echo "No existe ${HTPASSWD_FILE}" >&2
        exit 1
    fi

    tmp="$(mktemp)"
    awk -F':' -v u="${user}" '$1 != u {print}' "${HTPASSWD_FILE}" > "${tmp}"
    install -o root -g www-data -m 640 "${tmp}" "${HTPASSWD_FILE}"
    rm -f "${tmp}"
    echo "Usuario eliminado (si existia): ${user}"
}

list_users() {
    if [[ ! -f "${HTPASSWD_FILE}" ]]; then
        echo "Sin fichero: ${HTPASSWD_FILE}"
        exit 0
    fi
    cut -d':' -f1 "${HTPASSWD_FILE}"
}

main() {
    require_root
    local action="${1:-}"
    case "${action}" in
        set)
            local user="${2:-}"
            [[ -n "${user}" ]] || { usage; exit 1; }
            local pass
            pass="$(read_password_if_missing "${3:-}")"
            set_user "${user}" "${pass}"
            ;;
        del)
            local user="${2:-}"
            [[ -n "${user}" ]] || { usage; exit 1; }
            del_user "${user}"
            ;;
        list)
            list_users
            ;;
        *)
            usage
            exit 1
            ;;
    esac
}

main "$@"
