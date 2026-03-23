# Plan de cambio: contraste de texto sobre fondo navy

Fecha: 2026-02-18  
Rama: `develope_monitorizacion`

## 1) Objetivo
Mejorar legibilidad del texto tras el cambio a fondo navy.

## 2) Alcance
- `kiosk_status/styles.css`

## 3) Plan
1. Ajustar color del texto general de cabecera a tonos claros.
2. Mantener texto de tarjetas en tonos oscuros para preservar contraste sobre fondo blanco.

## 4) Ejecución
Completada.

- Archivo modificado: `kiosk_status/styles.css`
- Ajustes:
  - Añadida variable `--ink-on-navy: #eaf2ff`.
  - Texto global (`html, body`) en color claro para fondo navy.
  - `h1` y `h2.section-title` ajustados a tonos claros.
  - `.summary-card` y `.kiosk-card` forzados a `color: var(--ink)` para mantener buen contraste sobre fondo blanco.
