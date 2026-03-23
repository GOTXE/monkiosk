# Plan de cambio: evitar Ctrl+F5 por caché de status.json

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Evitar que el panel dependa de recarga dura (`Ctrl+F5`) para ver cambios de estado.

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Añadir cache-busting al fetch de `status.json` con query de timestamp.
2. Mantener `cache: 'no-store'`.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`
  - `fetch` actualizado a: `status.json?_ts=${Date.now()}`
  - se mantiene `cache: 'no-store'`
