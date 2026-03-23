# Plan de cambio: orden natural por hostname

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Ordenar tarjetas por nombre de quiosco en orden natural (1,2,3...10) y no lexicográfico (1,10,11...).

## 2) Alcance
- `estado_quioscos/index.html`

## 3) Plan
1. Sustituir `localeCompare` simple por `Intl.Collator` con `numeric: true`.
2. Desplegar en servidor de test.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`
  - orden actualizado a `Intl.Collator(..., { numeric: true })`.
  - ahora `QUIOSCO2` va antes de `QUIOSCO10`.
