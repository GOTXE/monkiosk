# Plan y Ejecucion - Menu hamburguesa y modal de contraseña

## Objetivo
Unificar acciones de cabecera en un menu hamburguesa y mover cambio de contraseña a modal.

## Cambios aplicados
1. Cabecera (`estado_quioscos/index.html`)
- Se reemplazan botones sueltos por menu hamburguesa con orden:
  - `Documentación`
  - `Cambiar contraseña`
  - `Cerrar sesión` (estilo peligro/rojo)

2. Modal de cambio de contraseña
- Se añade modal centrado con campos:
  - antigua
  - nueva
  - repetir
- Botones:
  - cancelar
  - cambiar contraseña
- Se reutiliza backend `change_password.php`.

3. Ajustes de comportamiento/UX
- Cerrar menu al clicar fuera o `Esc`.
- Cerrar modal al clicar fuera o `Esc`.
- Correcciones de visibilidad con atributos `hidden`:
  - `.topbar-menu[hidden] { display: none; }`
  - `.modal-backdrop[hidden] { display: none; }`

4. Etiquetas de servicios web
- En card servidor:
  - `Nginx (web)`
  - `PHP-FPM (web)`
