# Manual tecnico

## Objetivo

Este manual resume la parte tecnica de Monkiosk para instalacion, mantenimiento y recuperacion. La aplicacion esta pensada para ser simple, de bajo coste y facil de entender con pocos conocimientos.

## 1. Componentes principales

- `kiosk_web/`: web publica de presentacion de documentos
- `estado_quioscos/`: gestion web, monitorizacion, control y configuracion
- `kiosks_report/`: scripts de heartbeat y soporte para Debian y Alpine
- `kiosk_info/`: visor web de documentacion
- `tech_docs/`: documentacion operativa, historico y manuales

## 2. Flujo funcional

1. el quiosco reproduce documentos desde `kiosk_web/index.php`
2. el quiosco reporta estado a `estado_quioscos/update_status.php`
3. `estado_quioscos/index.html` muestra estado de quioscos y servidor
4. las ordenes de reinicio se guardan para que el quiosco las recoja en `get_action.php`

## 3. Archivos importantes

- `estado_quioscos/status.json`: estado actual de quioscos
- `estado_quioscos/allowed_kiosks.json`: lista de quioscos permitidos
- `estado_quioscos/allowed_kiosks_protection.json`: estado de la proteccion global
- `estado_quioscos/unknown_kiosk_attempts.json`: intentos de conexion pendientes
- `estado_quioscos/actions.json`: acciones pendientes para quioscos
- `estado_quioscos/slide_settings.json`: tiempo de diapositiva
- `estado_quioscos/certificate_status.json`: cache diaria del estado del certificado
- `estado_quioscos/auth_users.json`: usuarios web
- `estado_quioscos/control_config.php`: token compartido para lectura de acciones

## 4. Seguridad actual

- acceso web de gestion con login propio y CSRF
- nombres de usuario no sensibles a mayusculas/minusculas
- proteccion de quioscos por `hostname` y `IP fija` opcional
- la presentacion web del quiosco, con proteccion activada, solo admite `IP fija` autorizada
- lectura de acciones por token compartido `X-Control-Token`
- `kiosk_info` protegido con la misma sesion web de `estado_quioscos`
- no existe autenticacion fuerte en el heartbeat; la proteccion principal es la lista de quioscos permitidos

## 5. Despliegue actual

Referencias operativas habituales:

- repo: `/home/kiosk/kioskos`
- produccion web: `/var/www/html`
- gestion: `/var/www/html/estado_quioscos`
- presentacion: `/var/www/html/index.php`

La configuracion comun se centraliza en:

- `estado_quioscos/app_config.php`
- `estado_quioscos/config.local.php` si existe

La politica de archivos runtime locales esta en:

- `tech_docs/politica_archivos_runtime_locales.md`

## 6. Operaciones habituales

### Alta de quiosco

1. desactivar proteccion si hace falta
2. esperar a que el equipo aparezca en `Intentos de conexion`
3. pulsar `Añadir`
4. revisar `hostname` e `IP fija`
5. marcar `Permitido`
6. guardar con `GUARDAR`
7. reactivar proteccion

### Cambio de tiempo de diapositiva

- desde la tarjeta `SERVIDOR`
- el valor se guarda en `slide_settings.json`

### Gestion de documentos

- la pantalla visible al usuario es `Gestión diapositivas`
- permite seleccionar archivo, subirlo, previsualizarlo y eliminarlo
- la UI marca el archivo seleccionado como pendiente antes de subir
- si el archivo previsualizado se elimina, la vista previa se limpia

### Reinicio remoto

- desde `Informacion` del quiosco
- se escribe en `actions.json`
- el quiosco recoge la accion en su siguiente consulta

## 7. Significado tecnico de Inestable

Un quiosco se marca `Inestable` cuando cambia varias veces entre `online` y `offline` en una ventana corta.

Parametros actuales:

- ventana: `10 minutos`
- umbral: `3` transiciones
- historial corto almacenado por equipo en `status.json`

## 8. Certificado HTTPS

- la web comprueba el certificado local servido por `127.0.0.1:443`
- el resultado se cachea `24 horas`
- si quedan `30 dias o menos`, la tarjeta del servidor muestra un aviso rojo parpadeante

## 9. Recuperacion y diagnostico

Comprobar primero:

- `status.json`
- `allowed_kiosks.json`
- `unknown_kiosk_attempts.json`
- `actions.json`
- servicios web (`nginx`, `php-fpm`)

Preguntas utiles:

- el quiosco reporta a la URL correcta
- esta en la lista de permitidos
- coincide la `IP fija`
- el token de `get_action.php` sigue siendo correcto

### Backup local

El backup local recomendado se hace con:

- `tools/backup_monkiosk.sh`

Incluye:

- `/home/kiosk/kioskos`
- `/var/www/html`
- `/etc/nginx/sites-available/default`

Caracteristicas:

- crea `tar.gz` locales en `/var/backups/monkiosk`
- conserva permisos y propietarios
- mantiene `14` copias por defecto

### Restauracion guiada

La restauracion se hace con:

- `tools/restore_monkiosk.sh`

Comportamiento:

- lista backups por numero
- pide confirmacion fuerte con `RESTAURAR`
- crea una copia previa del estado actual
- restaura sobre `/`
- recarga `nginx`

Modo de prueba:

- `sudo ./tools/restore_monkiosk.sh --dry-run`

### Inicializacion de runtime local

Para crear los archivos locales necesarios de `estado_quioscos` en una instalacion nueva:

- `sudo ./tools/init_estado_quioscos_runtime.sh --target-dir /var/www/html/estado_quioscos`

## 10. Principios de mantenimiento

- evitar complejidad innecesaria
- preferir archivos planos y PHP simple
- documentar cambios operativos en `tech_docs/registro_cambios.md`
- no eliminar documentacion historica hasta validar el flujo nuevo en produccion
- seguir siempre la secuencia `tarea -> planner -> coder -> tester (si aplica) -> documenta -> commit`
- no modificar codigo sin nota previa en `tech_docs` segun `tech_docs/README.md`

## 11. Versionado

La politica de versionado del proyecto esta en:

- `tech_docs/politica_versionado.md`

La fuente unica de version es:

- `VERSION`

Regla base:

- el tipo de rama no define la version
- la version la define el impacto real del cambio

## 12. Pull Requests con IA

Para cualquier PR del proyecto se debe usar siempre la plantilla oficial:

- `tech_docs/14.1_plantilla_pr_vibecoding.md`

Uso esperado:

- copiar la plantilla al prompt
- adjuntar diff o commits reales
- generar la descripcion del PR sin alterar estructura ni secciones

## 13. Flujo Git del proyecto

La politica oficial de ramas, PR y control de sensibles esta en:

- `tech_docs/14_flujo_git_y_politicas_repos.md`

Reglas base:

- `main` solo estable y probado
- `dev` para integracion
- `feature/*` desde `dev`
- `hotfix/*` desde `main`
- sin commits directos a `main`
- sin despliegue de produccion desde `dev`

Estado operativo actual recomendado:

- integrar cambios en `dev`
- mantener `main` solo para estado listo para produccion
- usar ramas `feature/*` para nuevos cambios
