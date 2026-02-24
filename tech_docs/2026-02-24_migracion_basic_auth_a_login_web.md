# Plan y Ejecucion - Migracion de Basic Auth a login web

## Motivo
Se solicita pantalla de inicio de sesion propia en `estado_quioscos` con formulario y opcion `recordar credenciales`.

## Resultado
1. Autenticacion por sesion PHP:
- `auth_lib.php`
- `auth_users.json`
- `login.php`
- `login_action.php`
- `logout.php`
- `session_status.php`

2. Control en frontend:
- `index.html` verifica sesion con `session_status.php` al arrancar.
- Si no hay sesion, redirige a `login.php`.
- Boton `Cerrar sesion` redirige a `logout.php`.

3. Endpoints de gestion protegidos por sesion:
- `server_status.php`
- `server_control.php`
- `control_proxy.php`
- `overlay_control.php`
- `change_password.php`
- `POST` de `slide_settings.php`

4. Cambio de contrasena desde web:
- `change_password.php` actualiza hash `password_hash` del usuario autenticado en `auth_users.json`.
