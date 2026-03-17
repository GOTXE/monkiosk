# 2026-03-17 separar_protecciones_reporte_presentacion

## Planificacion

### Objetivo

Separar la proteccion de `estado_quioscos` en dos controles distintos:

- proteccion de reporte
- proteccion de presentacion

Y mantener visible el listado de `Intentos de conexion` sin necesidad de desactivar la proteccion de reporte.

### Alcance

- revisar la logica actual de proteccion global
- crear dos estados de proteccion independientes
- ajustar la UI de `Quioscos permitidos`
- adaptar el backend de reporte y de presentacion
- mantener el flujo de alta manual desde `Intentos de conexion`

### Riesgos

- mezclar estados de proteccion antiguos con los nuevos
- romper el acceso actual a la presentacion
- dejar de registrar intentos de conexion cuando la proteccion de reporte este activada

### Archivos a tocar

- `estado_quioscos/app_config.php`
- `estado_quioscos/allowed_kiosks.php`
- `estado_quioscos/allowed_kiosks_api.php`
- `estado_quioscos/update_status.php`
- `kiosk_web/index.php`
- `tech_docs/manual_usuario.md`
- `tech_docs/manual_tecnico.md`
- `tech_docs/registro_cambios.md`
- este archivo

## Ejecucion

- se ha separado el estado de proteccion en:
  - `report_enabled`
  - `presentation_enabled`
- se mantiene compatibilidad con el formato antiguo de un solo `enabled`
- `update_status.php` usa solo la proteccion de reporte
- `kiosk_web/index.php` usa solo la proteccion de presentacion
- la UI de `Quioscos permitidos` ahora muestra dos bloques separados
- los `Intentos de conexion` siguen registrandose y mostrandose con la proteccion de reporte activada
- se ha actualizado la documentacion de usuario y tecnica

### Pruebas

- `php -l estado_quioscos/app_config.php`
- `php -l estado_quioscos/allowed_kiosks_api.php`
- `php -l estado_quioscos/update_status.php`
- `php -l kiosk_web/index.php`

### Resultado

La proteccion queda dividida en dos superficies distintas y el alta manual de quioscos desde `Intentos de conexion` ya no depende de desactivar la proteccion de reporte.
