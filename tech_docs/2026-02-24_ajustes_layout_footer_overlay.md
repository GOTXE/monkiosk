# Plan y Ejecucion - Ajustes layout footer y overlay en servidor

## Objetivo
Mejorar presentacion en `estado_quioscos`:
- footer siempre al fondo de la ventana.
- `OFAP 601` centrado.
- icono GitHub a la derecha.
- bloque de `Quiosco para ver cuenta regresiva` alineado a la derecha, con etiqueta encima de selector/botones.

## Ejecucion
1. `estado_quioscos/styles.css`
- `container` con `min-height: 100vh` y layout en columna.
- `app-footer` con `margin-top: auto`.
- `footer-brand` centrado visualmente y `footer-github` posicionado a la derecha.

2. `estado_quioscos/styles.css` (overlay)
- `overlay-control-row` y `overlay-control-main` ajustados a derecha.
- etiqueta principal en una linea completa encima del selector y botones.
- textos de estado (`overlay-active`/`overlay-inactive`) alineados a derecha.
