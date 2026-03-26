# Plan y Ejecucion - Correccion de inicializacion runtime para proteccion de presentacion

## Planificacion

### Objetivo

Corregir el script `tools/init_estado_quioscos_runtime.sh` para que una instalacion nueva genere JSON runtime valido y la proteccion de presentacion pueda cargar su configuracion.

### Alcance

- revisar como se escriben los archivos JSON iniciales
- corregir el formato generado por el inicializador
- alinear `allowed_kiosks_protection.json` con el formato actual de doble proteccion
- validar la generacion en un directorio temporal

### Riesgos

- dejar instalaciones nuevas con JSON invalido
- mantener compatibilidad defectuosa con el estado inicial de proteccion
- tocar un script de instalacion con impacto transversal

### Archivos a tocar

- `tools/init_estado_quioscos_runtime.sh`
- `tech_docs/2026-03-26_fix_init_runtime_json_presentacion.md`

## Ejecucion

- detectado que `write_file_if_missing()` escribia cadenas con `\n` literales usando `printf '%s'`
- eso dejaba invalidos varios JSON runtime creados en una instalacion nueva
- `allowed_kiosks.json` quedaba ilegible y `eq_load_allowed_kiosks()` devolvia lista vacia
- con la lista vacia, la proteccion de presentacion no llegaba a aplicar el bloqueo esperado
- corregido el helper para escribir escapes con `printf '%b'`
- actualizado el formato inicial de `allowed_kiosks_protection.json` a:
  - `report_enabled`
  - `presentation_enabled`

### Validacion

- `sh -n tools/init_estado_quioscos_runtime.sh`
- ejecucion del script contra un directorio temporal
- comprobacion con `php` de que:
  - `allowed_kiosks.json` es JSON valido
  - `allowed_kiosks_protection.json` es JSON valido
  - el estado de proteccion inicial contiene `report_enabled` y `presentation_enabled`
