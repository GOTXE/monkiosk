# Plan y Ejecucion - Login web propio para estado_quioscos

## Objetivo
Disponer de una pagina de inicio de sesion propia (sin popup Basic Auth del navegador), con:
- card centrada.
- texto `Bienvenido a Estado Quioscos OFAP 601`.
- campos `usuario` y `contrasena`.
- checkbox `recordar credenciales`.

## Enfoque
1. Crear autenticacion de aplicacion con sesion PHP.
2. Crear `login.php` y `login_action.php`.
3. Crear `session_status.php` para que `index.html` redirija a login si no hay sesion.
4. Proteger endpoints de gestion con sesion.
5. Mantener endpoints de operacion de quioscos sin login.
6. Reutilizar cambio de contrasena desde web con el usuario autenticado.
