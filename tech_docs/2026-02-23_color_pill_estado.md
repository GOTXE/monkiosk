# Plan de cambio: color dinámico del pill de estado

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Hacer que el pill de estado use color semáforo:
- verde si online
- rojo si offline
- ámbar si advertencia

## 2) Alcance
- `estado_quioscos/index.html`
- `estado_quioscos/styles.css`

## 3) Plan
1. Añadir clase dinámica al pill (`status-online/status-offline/status-warning`).
2. Definir estilos por clase en CSS.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`
  - Pill de estado con clase dinámica: `status-online`, `status-warning`, `status-offline`.
- `estado_quioscos/styles.css`
  - `status-online`: verde
  - `status-offline`: rojo
  - `status-warning`: ámbar
