# Plan y Ejecucion - Boton cerrar sesion en estado_quioscos

## Objetivo
Agregar boton `Cerrar sesion` debajo de `Documentacion` en la cabecera de `estado_quioscos`.

## Enfoque
1. UI:
- Nuevo bloque `topbar-actions` con dos elementos verticales:
  - enlace `Documentacion`
  - boton `Cerrar sesion`

2. Logout Basic Auth:
- JS invoca `GET /estado_quioscos/logout` con credenciales invalidas para forzar invalidacion en navegador.
- Redireccion posterior a `/estado_quioscos/`.

3. Nginx:
- Nueva ruta `location = /estado_quioscos/logout` que devuelve `401`.
