# Plan de cambio: control seguro de reinicio remoto (prueba)

Fecha: 2026-02-23
Rama: `test_control_reinicio`

## 1) Objetivo
Añadir capacidad de reiniciar quioscos desde `estado_quioscos/index.html` con protección de acceso.

## 2) Diseño de seguridad
1. Operador: al lanzar acción, debe introducir contraseña de control.
2. Backend: `control_proxy.php` valida contraseña (hash) y encola acción.
3. Quiosco: consulta acciones en `get_action.php` con token interno (`X-Control-Token`).
4. El servidor entrega acción una sola vez y la elimina de cola.

## 3) Alcance
- `estado_quioscos/index.html` (botón de reinicio + llamada backend)
- `estado_quioscos/styles.css` (estilo botón)
- `estado_quioscos/control_config.php.example` (nuevo)
- `estado_quioscos/control_proxy.php` (nuevo)
- `estado_quioscos/get_action.php` (nuevo)
- `kiosks_report/debian/report_status.sh`
- `Quioscos_install_alpine/report/report_status.sh`

## 4) Pruebas previstas
- Sintaxis shell y PHP.
- Encolado de acción con contraseña correcta/incorrecta.
- Lectura de acción por quiosco con token.
- Verificación de consumo único de acción.

## 5) Ejecución
Completada.

- Backend de control añadido:
  - `estado_quioscos/control_proxy.php` (encola acción, valida contraseña de control).
  - `estado_quioscos/get_action.php` (quiosco consulta acción con token y consume una sola vez).
  - `estado_quioscos/control_config.php.example` (plantilla de hash/token).

- UI de control:
  - `estado_quioscos/index.html`: botón `Reiniciar` por quiosco con confirmación y prompt de contraseña.
  - `estado_quioscos/styles.css`: estilos de botón de reinicio.

- Scripts de quiosco:
  - `kiosks_report/debian/report_status.sh`
  - `Quioscos_install_alpine/report/report_status.sh`
  - Tras heartbeat exitoso consultan `get_action.php`; si reciben `reboot`, ejecutan reinicio local.

- Configuración extra en heartbeat:
  - nuevo campo `CONTROL_TOKEN` (en plantillas e instaladores).

- Validación funcional (local):
  - encolar acción: OK
  - primera lectura de acción: `reboot`
  - segunda lectura: `none` (consumo único)
