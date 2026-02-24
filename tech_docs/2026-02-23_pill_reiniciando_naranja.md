# Plan de cambio: pill "Reiniciando" en naranja

Fecha: 2026-02-23
Rama: `test_control_reinicio`

## 1) Objetivo
Al encolar reinicio desde la web, mostrar el pill del quiosco como `Reiniciando` en color naranja.

## 2) Alcance
- `estado_quioscos/control_proxy.php`
- `estado_quioscos/index.html`
- `estado_quioscos/styles.css`

## 3) Plan
1. En `control_proxy.php`, al encolar `reboot`, marcar `status=Reiniciando` en `status.json`.
2. En `index.html`, priorizar estado `Reiniciando` en `normalizeStatus`.
3. En CSS, añadir estilos para `state-restarting` y `status-restarting`.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `control_proxy.php` ahora marca `status=Reiniciando` en `status.json` al encolar reboot.
- `index.html` prioriza ese estado y muestra pill `Reiniciando`.
- `styles.css` añade estilo naranja para `state-restarting` y `status-restarting`.
