#!/usr/bin/env bash
set -euo pipefail

TARGET_DIR="${TARGET_DIR:-/var/www/html/estado_quioscos}"
AUTH_FILE=""
PASSWORD_VALUE=""
USER_NAME="gestor"
RESOLVED_USER_NAME=""

usage() {
    cat <<'EOF'
Uso:
  sudo ./tools/change_gestor_password.sh [opciones] [contrasena]

Opciones:
  --target-dir RUTA    Directorio de estado_quioscos. Por defecto: /var/www/html/estado_quioscos
  --auth-file RUTA     Ruta directa a auth_users.json
  --password CLAVE     Nueva contrasena
  --user USUARIO       Usuario a actualizar. Por defecto: gestor
  --help               Mostrar ayuda

Si no se indica contrasena, el script la pide por terminal dos veces.
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
            --auth-file)
                AUTH_FILE="${2:-}"
                shift 2
                ;;
            --password)
                PASSWORD_VALUE="${2:-}"
                shift 2
                ;;
            --user)
                USER_NAME="${2:-}"
                shift 2
                ;;
            --help|-h)
                usage
                exit 0
                ;;
            --*)
                echo "ERROR: opcion no reconocida: $1" >&2
                usage
                exit 1
                ;;
            *)
                if [ -n "${PASSWORD_VALUE}" ]; then
                    echo "ERROR: solo se admite una contrasena posicional." >&2
                    usage
                    exit 1
                fi
                PASSWORD_VALUE="$1"
                shift
                ;;
        esac
    done
}

resolve_auth_file() {
    if [ -n "${AUTH_FILE}" ]; then
        return
    fi
    AUTH_FILE="${TARGET_DIR}/auth_users.json"
}

ensure_inputs() {
    if [ ! -f "${AUTH_FILE}" ]; then
        echo "ERROR: no existe ${AUTH_FILE}" >&2
        exit 1
    fi
    if ! command -v php >/dev/null 2>&1; then
        echo "ERROR: no se encontro el comando php." >&2
        exit 1
    fi
}

resolve_user_name() {
    local resolved

    resolved="$(php -r '
    $path = $argv[1];
    $requested = $argv[2];
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === "") {
        fwrite(STDERR, "ERROR: auth_users.json vacio o no legible.\n");
        exit(1);
    }
    $users = json_decode($raw, true);
    if (!is_array($users) || $users === []) {
        fwrite(STDERR, "ERROR: auth_users.json no contiene usuarios validos.\n");
        exit(1);
    }

    foreach ($users as $candidate => $_hash) {
        if (strtolower((string)$candidate) === strtolower($requested)) {
            echo (string)$candidate;
            exit(0);
        }
    }

    $names = array_map("strval", array_keys($users));
    if (count($names) === 1) {
        echo $names[0];
        exit(0);
    }

    fwrite(STDERR, "ERROR: no existe el usuario solicitado y hay varios usuarios en auth_users.json.\n");
    fwrite(STDERR, "Usuarios disponibles: " . implode(", ", $names) . "\n");
    exit(1);
    ' "${AUTH_FILE}" "${USER_NAME}")"

    if [ -z "${resolved}" ]; then
        echo "ERROR: no se pudo resolver el usuario web." >&2
        exit 1
    fi

    RESOLVED_USER_NAME="${resolved}"
    echo "Vas a cambiar la contrasena del usuario web: ${RESOLVED_USER_NAME}"
}

read_password_if_missing() {
    if [ -n "${PASSWORD_VALUE}" ]; then
        return
    fi

    local p1="" p2=""
    read -r -s -p "Nueva contrasena para ${RESOLVED_USER_NAME}: " p1
    echo
    read -r -s -p "Repetir contrasena: " p2
    echo

    if [ -z "${p1}" ]; then
        echo "ERROR: la contrasena no puede estar vacia." >&2
        exit 1
    fi
    if [ "${p1}" != "${p2}" ]; then
        echo "ERROR: las contrasenas no coinciden." >&2
        exit 1
    fi

    PASSWORD_VALUE="${p1}"
}

validate_password() {
    local password="$1"

    if [ "${#password}" -lt 8 ]; then
        echo "ERROR: la contrasena debe tener al menos 8 caracteres." >&2
        exit 1
    fi
    if ! printf '%s' "${password}" | grep -q '[A-Z]'; then
        echo "ERROR: la contrasena debe incluir al menos 1 mayuscula." >&2
        exit 1
    fi
    if ! printf '%s' "${password}" | grep -q '[0-9]'; then
        echo "ERROR: la contrasena debe incluir al menos 1 numero." >&2
        exit 1
    fi
    if ! printf '%s' "${password}" | grep -q '[^A-Za-z0-9]'; then
        echo "ERROR: la contrasena debe incluir al menos 1 caracter especial." >&2
        exit 1
    fi
}

update_auth_file() {
    local tmp_file owner group mode

    tmp_file="$(mktemp)"
    owner="$(stat -c '%u' "${AUTH_FILE}")"
    group="$(stat -c '%g' "${AUTH_FILE}")"
    mode="$(stat -c '%a' "${AUTH_FILE}")"

    php -r '
    $path = $argv[1];
    $user = $argv[2];
    $password = $argv[3];
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === "") {
        fwrite(STDERR, "ERROR: auth_users.json vacio o no legible.\n");
        exit(1);
    }
    $users = json_decode($raw, true);
    if (!is_array($users)) {
        fwrite(STDERR, "ERROR: auth_users.json no contiene JSON valido.\n");
        exit(1);
    }
    $resolved = "";
    foreach ($users as $candidate => $_hash) {
        if (strtolower((string)$candidate) === strtolower($user)) {
            $resolved = (string)$candidate;
            break;
        }
    }
    if ($resolved === "") {
        fwrite(STDERR, "ERROR: no existe el usuario solicitado en auth_users.json.\n");
        exit(1);
    }
    $users[$resolved] = password_hash($password, PASSWORD_DEFAULT);
    $payload = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (!is_string($payload)) {
        fwrite(STDERR, "ERROR: no se pudo serializar auth_users.json.\n");
        exit(1);
    }
    echo $payload, PHP_EOL;
    ' "${AUTH_FILE}" "${RESOLVED_USER_NAME}" "${PASSWORD_VALUE}" > "${tmp_file}"

    install -o "${owner}" -g "${group}" -m "${mode}" "${tmp_file}" "${AUTH_FILE}"
    rm -f "${tmp_file}"
}

main() {
    require_root
    parse_args "$@"
    resolve_auth_file
    ensure_inputs
    resolve_user_name
    read_password_if_missing
    validate_password "${PASSWORD_VALUE}"
    update_auth_file
    echo "Contrasena actualizada para ${RESOLVED_USER_NAME} en ${AUTH_FILE}"
}

main "$@"
