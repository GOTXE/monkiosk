# 2026-03-17 runtime_local_y_gitignore

## Planificacion

### Objetivo

Separar de forma clara los archivos de codigo y los archivos runtime locales de la instalacion, para que los datos de produccion no entren nunca en el repositorio.

### Alcance

- adaptar `.gitignore` a la realidad actual de `estado_quioscos`
- documentar la politica de archivos runtime locales
- crear un script de inicializacion para generar archivos necesarios en instalacion
- sacar del indice Git los archivos runtime actualmente versionados

### Riesgos

- dejar fuera algun archivo runtime necesario
- romper una instalacion nueva si no existe inicializacion minima
- mezclar documentacion historica antigua con la politica nueva

### Archivos a tocar

- `.gitignore`
- `tools/README.md`
- `AGENTS.md`
- `tech_docs/guia_lectura_agente.md`
- `tech_docs/manual_tecnico.md`
- `tech_docs/registro_cambios.md`
- `tech_docs/politica_archivos_runtime_locales.md`
- `tools/init_estado_quioscos_runtime.sh`

### Archivos a sacar del repo

- `estado_quioscos/auth_users.json`
- `estado_quioscos/overlay_config.json`
- `estado_quioscos/slide_settings.json`
- `estado_quioscos/status.json`
- `estado_quioscos/allowed_hosts.txt`

## Ejecucion

- `.gitignore` se ha adaptado a los archivos runtime reales de `estado_quioscos`
- se ha creado `tech_docs/politica_archivos_runtime_locales.md`
- se ha creado `tools/init_estado_quioscos_runtime.sh` para inicializar runtime local en instalación
- se han actualizado referencias en `AGENTS.md`, `tech_docs/guia_lectura_agente.md`, `tools/README.md` y `tech_docs/manual_tecnico.md`
- los siguientes archivos han salido del indice Git con `git rm --cached`, sin borrarse del disco:
  - `estado_quioscos/auth_users.json`
  - `estado_quioscos/overlay_config.json`
  - `estado_quioscos/slide_settings.json`
  - `estado_quioscos/status.json`
  - `estado_quioscos/allowed_hosts.txt`

### Pruebas

- validacion de sintaxis shell:
  - `bash -n tools/init_estado_quioscos_runtime.sh`
- comprobacion manual de que los archivos runtime siguen presentes en disco tras salir del indice Git

### Resultado

El repositorio deja de versionar runtime local de `estado_quioscos`, la politica queda documentada y la instalación dispone de un script para crear los archivos necesarios con valores limpios.
