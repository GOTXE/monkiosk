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
