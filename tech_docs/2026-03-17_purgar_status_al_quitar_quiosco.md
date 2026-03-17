# 2026-03-17 purgar_status_al_quitar_quiosco

## Planificacion

### Objetivo

Hacer que un quiosco eliminado de `Quioscos: Reporte permitido` desaparezca inmediatamente del reporte principal, sin esperar a recarga manual o a un nuevo ciclo de heartbeat.

### Alcance

- revisar el guardado actual de la lista permitida
- purgar de `status.json` los quioscos que ya no queden permitidos
- mantener el comportamiento de `Intentos de conexión`

### Riesgos

- eliminar de `status.json` equipos que no deberían purgarse
- mezclar la purga inmediata con la limpieza ya existente de `update_status.php`

### Archivos a tocar

- `estado_quioscos/app_config.php`
- `estado_quioscos/allowed_kiosks_api.php`
- este archivo

## Ejecucion

- se ha añadido `eq_purge_status_for_allowed_kiosks()` en `estado_quioscos/app_config.php`
- al guardar la lista permitida en `estado_quioscos/allowed_kiosks_api.php` se purga inmediatamente `status.json`
- los quioscos que ya no quedan permitidos desaparecen del reporte sin esperar a `F5` o a un nuevo heartbeat

### Pruebas

- `php -l estado_quioscos/app_config.php`
- `php -l estado_quioscos/allowed_kiosks_api.php`
- despliegue en producción para comprobación del flujo

### Resultado

Quitar un quiosco de la lista permitida y guardar hace que desaparezca inmediatamente del estado principal, salvo que vuelva a intentar reportar y aparezca como intento de conexión.
