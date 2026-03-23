# Plan de cambio: formato MB/GB en mini-reporte

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Mostrar RAM libre y disco libre en GB cuando el valor sea mayor de 1024 MB.

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Añadir función de formateo para MB/GB.
2. Aplicarla en `renderMiniReport` para `mem_free_mb` y `disk_free_mb`.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- Archivo modificado: `estado_quioscos/index.html`
- Añadida función `formatStorageMb(valueMb)`.
- `RAM libre` y `Disco libre` ahora muestran:
  - `MB` si < 1024
  - `GB` con 1 decimal si >= 1024
