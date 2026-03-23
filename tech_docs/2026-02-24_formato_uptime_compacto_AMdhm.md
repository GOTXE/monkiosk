# Plan de cambio: formato uptime compacto A/M/d/h/m

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Cambiar el formato de `Encendido` para mostrar unidades compactas y legibles:
- ejemplo corto: `1d 15h 33m`
- ejemplo medio: `1M 22d 14h`
- ejemplo largo: `3A 5M 10d`

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Criterio
- 1A = 365 días
- 1M = 30 días
- salida con hasta 3 unidades más significativas

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`:
  - `formatUptime(seconds)` actualizado a formato compacto.
  - Convención aplicada:
    - `1A = 365d`
    - `1M = 30d`
  - Se muestran hasta 3 unidades significativas:
    - `1d 15h 33m`
    - `1M 22d 14h`
    - `3A 5M 10d`
