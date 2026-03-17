# Politica de archivos runtime locales

## Objetivo

Evitar que el repositorio contenga datos vivos de produccion o configuracion local de una instalacion concreta.

## Regla general

En Git solo deben entrar:

- codigo
- documentacion
- ejemplos
- valores de referencia no sensibles

No deben entrar en Git:

- usuarios reales
- estados vivos
- colas de acciones
- caches
- intentos de conexion
- listas de quioscos de una instalacion concreta
- configuraciones locales con secretos o datos internos

## Archivos runtime locales de `estado_quioscos`

Estos archivos deben tratarse como locales de instalacion:

- `estado_quioscos/auth_users.json`
- `estado_quioscos/allowed_hosts.txt`
- `estado_quioscos/allowed_kiosks.json`
- `estado_quioscos/allowed_kiosks_protection.json`
- `estado_quioscos/status.json`
- `estado_quioscos/actions.json`
- `estado_quioscos/slide_settings.json`
- `estado_quioscos/overlay_config.json`
- `estado_quioscos/unknown_kiosk_attempts.json`
- `estado_quioscos/presentation_viewers.json`
- `estado_quioscos/certificate_status.json`
- `estado_quioscos/control_config.php`
- `estado_quioscos/config.local.php`

## Regla de instalacion

Si un archivo runtime es necesario para que la app funcione, debe:

- crearse durante la instalacion
- o crearse con valores por defecto al primer uso

Nunca debe subirse al repositorio con datos reales de produccion.

## Script de inicializacion

La inicializacion recomendada se hace con:

- `tools/init_estado_quioscos_runtime.sh`

## Relacion con agentes

Los agentes deben conocer esta politica a traves de:

- `AGENTS.md`
- `tech_docs/guia_lectura_agente.md`
