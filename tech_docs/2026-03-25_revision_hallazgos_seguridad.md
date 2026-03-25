# Revision tecnica de hallazgos en monkiosk

Fecha: 2026-03-25

## Alcance

Revision manual del repositorio `monkiosk` centrada en:

- autenticacion y sesion web
- endpoints de control
- persistencia de estado en JSON
- despliegue de ficheros runtime

No se han ejecutado pruebas funcionales en navegador. Se han validado varios ficheros PHP con `php -l`.

## Hallazgos

### 1. Exposicion potencial de ficheros runtime sensibles dentro del docroot

Severidad: alta

La guia de despliegue publica `estado_quioscos` dentro de `/var/www/html` y el script de inicializacion crea en ese mismo arbol ficheros sensibles como:

- `auth_users.json`
- `control_config.php`
- `status.json`
- `allowed_kiosks.json`
- `unknown_kiosk_attempts.json`
- `presentation_viewers.json`

Referencias:

- `README.md`: pasos de despliegue y runtime local
- `tools/init_estado_quioscos_runtime.sh`: creacion de ficheros runtime

Riesgo:

- exposicion de hashes de contrasena
- fuga del token de control remoto
- fuga de IPs, hostnames, estado y metadatos operativos de quioscos
- dependencia de que la configuracion web bloquee esos ficheros de forma explicita

Recomendacion:

- mover todos los ficheros runtime fuera de `/var/www/html`
- hacer que `config.local.php` apunte a rutas privadas
- si temporalmente deben seguir en docroot, bloquear su acceso desde Nginx de forma explicita

### 2. Condiciones de carrera en escrituras JSON compartidas

Severidad: alta

Varios endpoints leen el JSON antes de adquirir el bloqueo exclusivo y despues escriben una version potencialmente obsoleta. El `flock` actual protege la escritura, pero no el ciclo completo leer-modificar-escribir.

Afectados:

- `estado_quioscos/update_status.php`
- `estado_quioscos/control_proxy.php`
- `estado_quioscos/get_action.php`

Riesgo:

- perdida de actualizaciones concurrentes
- desaparicion temporal o permanente de estados de quioscos
- sobrescritura de acciones pendientes
- consumo inconsistente de ordenes cuando varios clientes consultan a la vez

Recomendacion:

- abrir el fichero
- adquirir `LOCK_EX`
- releer el contenido desde el descriptor ya bloqueado
- aplicar los cambios en memoria
- truncar y reescribir dentro de la misma seccion critica

### 3. Falta de regeneracion de sesion tras login

Severidad: media

El login marca la sesion como autenticada, pero no regenera el identificador de sesion tras autenticarse correctamente.

Afectados:

- `estado_quioscos/auth_lib.php`
- `estado_quioscos/login_action.php`

Riesgo:

- session fixation si un atacante consigue fijar previamente el SID de la victima

Recomendacion:

- ejecutar `session_regenerate_id(true)` inmediatamente despues del login correcto
- valorar activar `session.use_strict_mode`

## Riesgos secundarios observados

Hay endpoints autenticados por sesion que no verifican CSRF de forma consistente:

- `estado_quioscos/change_password.php`
- `estado_quioscos/server_control.php`
- `estado_quioscos/control_proxy.php`
- `estado_quioscos/overlay_control.php`
- `estado_quioscos/slide_settings.php`

No se eleva aqui a hallazgo principal porque el uso de `SameSite=Lax` y JSON reduce la explotabilidad clasica, pero sigue siendo recomendable homogeneizar la proteccion CSRF.

## Validacion realizada

- `php -l estado_quioscos/update_status.php`
- `php -l estado_quioscos/control_proxy.php`
- `php -l estado_quioscos/server_control.php`
- `php -l estado_quioscos/auth_lib.php`

## Estado del arbol al revisar

Cambio local previo detectado en Git:

- `tools/init_estado_quioscos_runtime.sh` con cambio de modo a ejecutable
