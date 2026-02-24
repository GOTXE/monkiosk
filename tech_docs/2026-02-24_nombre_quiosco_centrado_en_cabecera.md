# Plan de cambio: centrar nombre de quiosco entre icono y pill

Fecha: 2026-02-24
Rama: `test_control_reinicio`

## 1) Objetivo
Centrar el nombre del quiosco en la cabecera de la tarjeta, quedando entre el icono (izquierda) y el pill de estado (derecha).

## 2) Alcance
- `estado_quioscos/index.html`
- `estado_quioscos/styles.css`

## 3) Plan
1. Ajustar markup de cabecera en `index.html` a estructura de 3 columnas.
2. Ajustar CSS (`.kiosk-head`) para centrar nombre y mantener pill alineado a la derecha.
3. Desplegar en servidor de test.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- `estado_quioscos/index.html`:
  - cabecera de tarjeta cambiada a `icono | nombre | pills`.
- `estado_quioscos/styles.css`:
  - `.kiosk-head` en grid de 3 columnas.
  - `.kiosk-name` centrado.
