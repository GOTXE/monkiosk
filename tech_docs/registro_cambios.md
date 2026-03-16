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
