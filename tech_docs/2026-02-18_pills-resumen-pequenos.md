# Plan de cambio: pills de resumen más pequeños

Fecha: 2026-02-18  
Rama: `develope_monitorizacion`

## 1) Objetivo
Reducir tamaño visual de los pills del resumen (`Total`, `Online`, `Offline`, `Inestables`).

## 2) Alcance
- `kiosk_status/styles.css`

## 3) Plan
1. Pasar contenedor de resumen a distribución compacta horizontal.
2. Reducir padding, tipografía y radio para pills más pequeños.
3. Mantener legibilidad y contraste.

## 4) Ejecución
Completada.

- Archivo modificado: `kiosk_status/styles.css`
- Cambios aplicados:
  - `.summary` pasó de `grid` a `flex` compacto con `gap: 8px`.
  - `.summary-card` más pequeño: `padding: 6px 10px`, `border-radius: 999px`.
  - Tipografía reducida:
    - etiqueta (`span`): `0.78rem`
    - valor (`strong`): `0.92rem`
  - Eliminado ajuste móvil previo de grid para mantener comportamiento compacto.
