# 2026-03-17 aviso_pendiente_al_eliminar_quiosco

## Planificacion

### Objetivo

Mostrar el aviso dorado de cambio pendiente también cuando se elimina un quiosco de la tabla en `Quioscos permitidos`.

### Alcance

- ajustar el evento de borrado de filas en la UI
- mantener el mismo patrón de aviso usado al añadir quioscos

### Riesgos

- mostrar avisos duplicados o inconsistentes
- dejar el estado visual sin limpiar al guardar

### Archivos a tocar

- `estado_quioscos/allowed_kiosks.php`
- este archivo

## Ejecucion

- se ha ajustado el evento de borrado de filas en `estado_quioscos/allowed_kiosks.php`
- al eliminar un quiosco de la tabla se muestra aviso dorado de cambio pendiente
- el mensaje incluye el hostname eliminado cuando existe

### Pruebas

- `php -l estado_quioscos/allowed_kiosks.php`
- despliegue en producción para validación visual del flujo de borrado

### Resultado

Eliminar un quiosco de la tabla ya marca correctamente que faltan guardar cambios.
