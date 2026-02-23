# Plan de cambio: persistir desplegado de "Ver reporte"

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Mantener abierto el bloque "Ver reporte" tras los refrescos periódicos del panel.

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Guardar estado abierto/cerrado por quiosco en una estructura en memoria.
2. Aplicar atributo `open` al render si corresponde.
3. Escuchar evento `toggle` para actualizar estado del usuario.

## 4) Ejecución
Completada.

- Archivo modificado: `estado_quioscos/index.html`
- Cambios aplicados:
  - Añadido `reportOpenState` para recordar por quiosco si `Ver reporte` está abierto.
  - En cada render, se aplica atributo `open` según estado recordado.
  - Se añade listener `toggle` en cada `details.kiosk-report` para persistir el estado tras interacción.
  - Clave de quiosco robusta con `encodeURIComponent/decodeURIComponent`.
