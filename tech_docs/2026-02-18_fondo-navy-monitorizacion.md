# Plan de cambio: fondo navy en monitorización

Fecha: 2026-02-18  
Rama: `develope_monitorizacion`

## 1) Objetivo
Cambiar el fondo global de la web de monitorización a azul navy sólido.

## 2) Alcance
- `kiosk_status/styles.css`

## 3) Plan
1. Sustituir el fondo actual (gradiente) por color navy en `html, body`.
2. Verificar sintaxis CSS y consistencia visual esperada.

## 4) Ejecución
Completada.

- Archivo modificado: `kiosk_status/styles.css`
- Cambio aplicado:
  - `--bg` actualizado a `#001f3f` (azul navy).
  - Fondo global en `html, body` cambiado a `background-color: var(--bg)` (sólido).
