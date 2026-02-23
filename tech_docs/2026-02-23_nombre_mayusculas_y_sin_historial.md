# Plan de cambio: nombre en mayúsculas y eliminar historial

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
- Mostrar siempre nombre de quiosco en mayúsculas.
- Eliminar bloque "Ver historial" de la UI.

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Renderizar `name.toUpperCase()` en la tarjeta.
2. Quitar bloque `<details class="history">`.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`
  - nombre del quiosco renderizado en mayúsculas (`toUpperCase()`).
  - eliminado bloque visual "Ver historial".
