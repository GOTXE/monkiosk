# Plan de cambio: layout grid adaptable en Z por hostname

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Cambiar la presentación de tarjetas a grid adaptable (lectura en Z), manteniendo orden por nombre de quiosco/hostname.

## 2) Alcance
- `estado_quioscos/styles.css`

## 3) Plan
1. Cambiar contenedor `kiosk-strip` de `flex nowrap` a `grid` responsive.
2. Ajustar ancho de `kiosk-card` para que ocupe la celda del grid.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/styles.css`
  - `kiosk-strip` cambiado a grid responsive (`auto-fill`, `minmax`).
  - `kiosk-card` ahora ocupa el ancho de la celda (`width: 100%`).
- El orden por hostname se mantiene en JS (`sort` alfabético), por lo que visualmente la lectura es en Z.
