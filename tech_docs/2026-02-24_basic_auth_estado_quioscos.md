# Plan y Ejecucion - Basic Auth para estado_quioscos

## Objetivo
Proteger el acceso de gestion a `estado_quioscos` con Basic Auth y habilitar gestion simple de cambio de contrasena.

## Enfoque
- Proteccion HTTP Basic Auth en Nginx para rutas de gestion.
- Mantener abiertas rutas usadas por quioscos (heartbeat/control polling) para no romper operativa.
- Script local para alta/cambio/baja de credenciales.

## Rutas protegidas (gestion)
- `/estado_quioscos/`
- `/estado_quioscos/index.html`
- `/estado_quioscos/status.json`
- `/estado_quioscos/server_status.php`
- `/estado_quioscos/server_control.php`
- `/estado_quioscos/control_proxy.php`
- `/estado_quioscos/overlay_control.php`
- `POST` a `/estado_quioscos/slide_settings.php` (GET queda abierto para lectura de quiosco)

## Rutas no protegidas (operacion quiosco)
- `/estado_quioscos/update_status.php`
- `/estado_quioscos/get_action.php`
- `/estado_quioscos/overlay_state.php`

## Gestion de contrasena
Script: `tools/manage_estado_quioscos_basic_auth.sh`
- `set <usuario> [contrasena]`
- `del <usuario>`
- `list`

## Cambio desde web
- Endpoint: `estado_quioscos/change_password.php`
- Acceso protegido por Basic Auth.
- Cambia la contrasena del usuario autenticado (`REMOTE_USER`) pidiendo:
  - contrasena actual
  - nueva contrasena
  - repeticion de nueva contrasena
- Formulario integrado en `estado_quioscos/index.html` dentro de `SERVIDOR > Informacion`.
