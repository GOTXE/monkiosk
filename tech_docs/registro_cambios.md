# Registro de Cambios

Bitácora cronológica de cambios del proyecto. Añadir nuevas entradas al final.

## Formato de entrada

- Fecha: `YYYY-MM-DD HH:MM` (hora local)
- Área: `monitorizacion | kiosk_web | seguridad | despliegue | docs | otros`
- Cambio: descripción breve de qué se hizo.
- Archivos: rutas afectadas.
- Verificación: cómo se comprobó.

## Entradas

### 2026-02-26 00:00
- Área: docs
- Cambio: creación inicial del archivo de registro de cambios.
- Archivos: `tech_docs/registro_cambios.md`
- Verificación: archivo creado y versionable en repositorio.

### 2026-02-26 11:53
- Área: seguridad
- Cambio: política de contraseña reforzada (8+ caracteres, mayúscula, número y carácter especial) y ajuste de UX para no cerrar modal en error.
- Archivos: `estado_quioscos/change_password.php`, `estado_quioscos/index.html`, `tech_docs/2026-02-26_politica_contrasenas_modal.md`, `kiosk_info/src/CHANGELOG.md`
- Verificación: validaciones frontend+backend con error visible en rojo y cierre de modal solo en éxito.

### 2026-03-13 11:30
- Área: seguridad
- Cambio: login de gestor ajustado para recordar solo el usuario, mantener el nombre de usuario sin distinción de mayúsculas/minúsculas y añadir visibilidad de contraseña en login y modal.
- Archivos: `estado_quioscos/login.php`, `estado_quioscos/login_action.php`, `estado_quioscos/auth_lib.php`, `estado_quioscos/index.html`, `estado_quioscos/styles.css`
- Verificación: `php -l` en archivos PHP modificados y comprobación manual del flujo de login.

### 2026-03-13 12:10
- Área: monitorizacion
- Cambio: creación de gestión web de quioscos permitidos con `hostname`, `IP fija`, activación por fila, protección global activable/desactivable e intentos de conexión detectados para alta rápida desde la web.
- Archivos: `estado_quioscos/allowed_kiosks.php`, `estado_quioscos/allowed_kiosks_api.php`, `estado_quioscos/update_status.php`, `estado_quioscos/control_proxy.php`, `estado_quioscos/index.html`
- Verificación: `php -l` en endpoints y despliegue en producción con validación visual del menú y de la pantalla de gestión.

### 2026-03-13 12:25
- Área: despliegue
- Cambio: centralización de configuración local para rutas y ajustes operativos, reducción de dependencias de shell en `server_status.php` y unificación del acceso a `docs` y `slide_settings`.
- Archivos: `estado_quioscos/app_config.php`, `estado_quioscos/config.local.php.example`, `estado_quioscos/server_status.php`, `estado_quioscos/docs_api.php`, `estado_quioscos/docs_preview.php`, `estado_quioscos/slide_settings.php`, `kiosk_web/index.php`
- Verificación: `php -l` en archivos PHP modificados y despliegue funcional en `/var/www/html`.

### 2026-03-13 12:40
- Área: docs
- Cambio: homogeneización visual de páginas de `estado_quioscos` con footer común y mejora del flujo de confirmación de guardado en `Quioscos permitidos` mediante modal.
- Archivos: `estado_quioscos/login.php`, `estado_quioscos/docs_manager.php`, `estado_quioscos/allowed_kiosks.php`
- Verificación: `php -l` y revisión visual en producción.

### 2026-03-13 13:05
- Área: monitorizacion
- Cambio: mejora de usabilidad en `Quioscos permitidos`: etiqueta `Permitido` en lugar de `Activo`, activación por defecto de quioscos ya detectados cuando no existe lista previa, validación visual de IPv4 en `IP fija` y restricción de escritura a números y puntos.
- Archivos: `estado_quioscos/allowed_kiosks.php`, `estado_quioscos/app_config.php`
- Verificación: `php -l` y comprobación visual del formulario desplegado en producción.

### 2026-03-16 09:10
- Área: monitorizacion
- Cambio: ajustes de usabilidad en `Quioscos permitidos` para que un intento añadido desaparezca al instante de `Intentos de conexion`, una `IP fija` borrada permanezca vacía tras guardar y los campos rellenos se vean en negrita con ejemplos en cursiva.
- Archivos: `estado_quioscos/allowed_kiosks.php`, `estado_quioscos/app_config.php`
- Verificación: prueba manual en producción del alta desde `Intentos de conexion`, borrado de `IP fija` y revisión visual de estilos en los campos.

### 2026-03-16 10:05
- Área: monitorizacion
- Cambio: `Intentos de conexion` pasa a refrescarse automáticamente cada 15 segundos, muestra un aviso visual de autoactualización y no reintroduce en la lista equipos que el usuario ya ha añadido a la tabla de `Quioscos permitidos` aunque todavía no haya guardado.
- Archivos: `estado_quioscos/allowed_kiosks.php`
- Verificación: comprobación manual en producción del auto-refresco y del filtrado local tras pulsar `Añadir`.

### 2026-03-16 10:20
- Área: seguridad
- Cambio: añadido aviso visual persistente en la tarjeta del servidor cuando el certificado HTTPS entra en los últimos 30 días de validez. La comprobación del certificado queda cacheada 24 horas para no repetir la consulta en cada refresco de la web.
- Archivos: `estado_quioscos/server_status.php`, `estado_quioscos/index.html`, `estado_quioscos/styles.css`
- Verificación: `php -l` en `server_status.php` y despliegue en producción del aviso condicionado por días restantes.

### 2026-03-16 10:35
- Área: monitorizacion
- Cambio: simplificado el guardado en `Quioscos permitidos` para dejar una sola confirmación mediante el modal con `GUARDAR` exacto en mayúsculas. Además, el aviso de cambios pendientes pasa a mostrarse en dorado parpadeante para remarcar que todavía falta guardar.
- Archivos: `estado_quioscos/allowed_kiosks.php`, `estado_quioscos/allowed_kiosks_api.php`
- Verificación: `php -l` en `allowed_kiosks_api.php` y comprobación manual en producción del flujo de guardado y del aviso visual.

### 2026-03-16 11:05
- Área: seguridad
- Cambio: `kiosk_info` pasa a quedar protegido con la misma sesión de `estado_quioscos`, evitando un segundo login. La documentación se sirve por PHP protegido y el acceso directo a los markdown deja de ser la vía principal. Además, el menú principal renombra `Gestión de documentos` a `Gestión diapositivas` y se cierra al pulsar cualquier opción.
- Archivos: `estado_quioscos/auth_lib.php`, `estado_quioscos/index.html`, `kiosk_info/index.php`, `kiosk_info/doc.php`, `kiosk_info/index.html`, `kiosk_info/src/MANUAL_USUARIO.md`, `kiosk_info/src/MANUAL_TECNICO.md`, `kiosk_info/src/REGISTRO_CAMBIOS.md`, `kiosk_info/src/GUIA_USO_APP_RESPALDO.md`, `tech_docs/manual_usuario.md`, `tech_docs/manual_tecnico.md`
- Verificación: `php -l` en nuevos PHP, comprobación de redirección a login sin sesión y validación manual del nuevo menú de documentación.

### 2026-03-16 11:20
- Área: monitorizacion
- Cambio: la sección `Equipos` añade un único botón para `Desplegar todas` o `Cerrar todas` las tarjetas de quioscos, sin afectar al panel del servidor.
- Archivos: `estado_quioscos/index.html`, `estado_quioscos/styles.css`
- Verificación: comprobación manual en producción del cambio de texto dinámico y del despliegue/cierre global de tarjetas.

### 2026-03-16 11:45
- Área: seguridad
- Cambio: la protección de quioscos se extiende también a la presentación web del quiosco. Cuando la protección está activada, la presentación solo admite accesos desde `IP fija` autorizada. Además, `Quioscos permitidos` añade un listado de `Accesos a presentación`, resuelve mejor el hostname usando la configuración conocida y resalta los quioscos ya dados de alta frente a accesos genéricos.
- Archivos: `estado_quioscos/app_config.php`, `estado_quioscos/allowed_kiosks.php`, `estado_quioscos/allowed_kiosks_api.php`, `kiosk_web/index.php`
- Verificación: `php -l` en PHP modificados y comprobación manual en producción del bloqueo por IP fija y del listado de accesos.

### 2026-03-16 11:55
- Área: docs
- Cambio: mejoras de usabilidad en `Gestión diapositivas`: selector de archivo con nombre visible solo en dorado, botones más compactos, ayuda de nombre reordenada en dos líneas, errores autolimpiables a los 5 segundos, limpieza de la vista previa al borrar el archivo cargado y botón activo de `Vista previa` resaltado.
- Archivos: `estado_quioscos/docs_manager.php`, `estado_quioscos/docs_api.php`
- Verificación: `php -l` en `docs_api.php` y comprobación manual en producción del flujo de selección, subida, vista previa y borrado.

### 2026-03-16 13:10
- Área: backup
- Cambio: se añaden `tools/backup_monkiosk.sh` y `tools/restore_monkiosk.sh` para backup local con retención de `14` copias y restauración guiada con opción `--dry-run`.
- Archivos: `tools/backup_monkiosk.sh`, `tools/restore_monkiosk.sh`, `tools/README.md`, `tech_docs/manual_usuario.md`, `tech_docs/manual_tecnico.md`
- Verificación: revisión manual del flujo previsto y ejecución real de backup más simulación de restauración.
