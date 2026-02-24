# Plan y Ejecucion - Validacion de contraseñas en modal

## Objetivo
Evitar cambio de contraseña si `Nueva` y `Repetir` no coinciden y avisar en rojo dentro del modal.

## Ejecucion
1. `estado_quioscos/index.html`
- Se agrega bloque de error:
  - `Las contraseñas no coinciden`
- Se añade validación en vivo en campos `Nueva` y `Repetir`.
- Se bloquea acción de `Cambiar contraseña` si no coinciden.

2. `estado_quioscos/styles.css`
- Estilo `.modal-error` en rojo para visibilidad inmediata.
