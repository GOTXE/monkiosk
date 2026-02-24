# Plan de cambio: renombrar "Ver reporte" a "Información"

Fecha: 2026-02-23
Rama: `test_control_reinicio`

## 1) Objetivo
Cambiar el texto del desplegable de datos del quiosco en `estado_quioscos/index.html` de "Ver reporte" a "Información".

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Cambiar literal del `<summary>` del bloque `kiosk-report`.
2. Desplegar en servidor de pruebas.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`:
  - `<summary>Ver reporte</summary>` -> `<summary>Información</summary>`.
- Desplegado en servidor:
  - `/var/www/html/estado_quioscos/index.html`
