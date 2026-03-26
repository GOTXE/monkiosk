# Plan y Ejecucion - Cambio de clave de gestor desde terminal

## Planificacion

### Objetivo

Anadir un script en `tools/` para cambiar desde terminal la contrasena del usuario web `gestor` en `estado_quioscos`.

### Alcance

- crear script dedicado para actualizar `auth_users.json`
- reutilizar las mismas reglas de complejidad que aplica la web
- permitir uso interactivo y no interactivo
- documentar el uso en `tools/README.md`

### Riesgos

- sobrescribir mal el archivo runtime `auth_users.json`
- dejar permisos o propietario distintos al fichero original
- aceptar una contrasena que luego no cumpla las reglas del panel web

### Archivos a tocar

- `tools/change_gestor_password.sh`
- `tools/README.md`
- `tech_docs/2026-03-26_cambio_clave_gestor_terminal.md`

## Ejecucion

- creado `tools/change_gestor_password.sh`
- el script actualiza el hash `password_hash()` del usuario indicado en `auth_users.json`
- por defecto parte de `gestor`, pero detecta y usa el nombre real del usuario web si en el runtime existe otro unico usuario
- soporta modo interactivo y no interactivo
- valida la misma complejidad minima que `change_password.php`
- conserva propietario, grupo y permisos del archivo runtime al reescribirlo
- documentado el uso en `tools/README.md`
- validacion realizada:
  - `sh -n tools/change_gestor_password.sh`
  - prueba funcional en `/tmp` con `auth_users.json` de ejemplo y verificacion con `password_verify()`
