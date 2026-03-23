# Plan de cambio: botón reiniciar visible solo en "Información"

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Ocultar el botón de reinicio en vista principal de la tarjeta y mostrarlo solo dentro del bloque desplegable "Información".

## 2) Alcance
- `estado_quioscos/index.html`
- `estado_quioscos/styles.css`

## 3) Plan
1. Mover botón `Reiniciar` dentro de `<details class="kiosk-report">`.
2. Ajustar estilos para mantenerlo claro dentro del panel.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`
  - botón `Reiniciar` movido dentro de `Información`.
- `estado_quioscos/styles.css`
  - `.kiosk-actions` ajustado para alineación dentro del desplegable.
