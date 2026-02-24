# Plan de cambio: control servidor V1 sin auth de operador

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Añadir control básico del servidor desde `estado_quioscos/index.html` para operadores no técnicos, sin usuario/clave en la UI.

## 2) Alcance
- `estado_quioscos/index.html`
- `estado_quioscos/styles.css`
- `estado_quioscos/server_status.php` (nuevo)
- `estado_quioscos/server_control.php` (nuevo)
- `estado_quioscos/control_proxy.php` (quitar contraseña de operador)

## 3) Diseño
1. Panel "Servidor" con estado (nginx, php-fpm, monitorización, uptime, carga, RAM/disco libre).
2. Botones de acción:
   - Reiniciar web (nginx + php-fpm)
   - Reiniciar monitorización
   - Reiniciar servidor (con confirmación fuerte)
3. Backend ejecuta solo acciones allowlist vía `sudo -n`.
4. Sin auth de operador en frontend.

## 4) Pruebas previstas
- Validación de sintaxis PHP/JS.
- Lectura de estado servidor.
- Envío de acciones y manejo de errores de permisos.

## 5) Ejecución
Completada.

- Backend añadido:
  - `estado_quioscos/server_status.php`
  - `estado_quioscos/server_control.php`
- Control quiosco sin contraseña en UI:
  - `estado_quioscos/control_proxy.php` ya no exige `control_password`.
- UI:
  - `estado_quioscos/index.html` incluye panel `Servidor` con estado y acciones.
  - acciones: `Reiniciar Web`, `Reiniciar Monitor`, `Reiniciar Servidor` (confirmación fuerte).
  - botones de servidor y quioscos activos sin usuario/clave.
- Estilos:
  - `estado_quioscos/styles.css` actualizado para panel/acciones de servidor.
- Despliegue aplicado en test:
  - `index.html`, `styles.css`, `control_proxy.php`, `get_action.php`, `server_status.php`, `server_control.php`, `control_config.php.example`, `control_config.php`.
