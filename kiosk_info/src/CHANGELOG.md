# Changelog - Monkiosk

Todos los cambios notables en este proyecto serán documentados en este archivo.

## [2.2.0] - 2026-03-17

### 🎉 Nuevas Características Principales

#### Gestión de Quioscos Permitidos
- ✨ Nueva pantalla para gestionar quioscos permitidos desde la web.
- ✨ Alta de equipos detectados desde `Intentos de conexion` con paso directo a la tabla principal.
- ✨ Protección global activable/desactivable para controlar qué quioscos pueden reportar al servidor.
- ✨ Validación por `hostname` y `IP fija` opcional para cada quiosco.
- ✨ Actualización automática de `Intentos de conexion` sin necesidad de recargar la página.

#### Gestión y Acceso a la Presentación
- ✨ Protección de la presentación web del quiosco cuando la protección global está activada.
- ✨ Nuevo listado de `Accesos a presentación` con IP, hostname si se puede resolver y clasificación de quioscos conocidos.
- ✨ Restricción de acceso a la presentación por `IP fija` autorizada.

#### Mejoras de Interfaz en Estado Quioscos
- ✨ Recordatorio solo de usuario en login, sin guardar la contraseña en local.
- ✨ Mostrar u ocultar contraseña con icono integrado en login y cambio de contraseña.
- ✨ Botón único para `Desplegar todas` o `Cerrar todas` las tarjetas de quioscos.
- ✨ Footer común centrado y ajustes de navegación en menú hamburguesa.
- ✨ Aviso visual de caducidad del certificado HTTPS del servidor cuando faltan 30 días o menos.

#### Gestión de Diapositivas
- ✨ Mejoras de usabilidad en `Gestión diapositivas` con selección de archivo más clara y vista previa resaltada.
- ✨ Validación más explicativa de nombres de archivo en subidas.
- ✨ Limpieza automática de la vista previa al borrar un archivo previsualizado.
- ✨ Ajuste visual del panel de archivos con altura fija y scroll interno.

#### Recuperación y Operación
- ✨ Nuevos scripts de backup local y restauración guiada.
- ✨ Script de inicialización para crear archivos runtime locales durante la instalación.

### 🔒 Seguridad
- 🔒 `kiosk_info` protegido con la misma sesión web de `estado_quioscos`.

### 🛠️ Configuración y Operación
- 🛠️ Centralización de configuración común en `app_config.php`.
- 🛠️ Simplificación de rutas y reducción de dependencias implícitas entre código y despliegue.

### 📚 Documentación
- 📖 Nuevos manuales de usuario y técnico.
- 📖 Nueva política de versionado con `VERSION` como fuente única.
- 📖 Nueva guía de lectura para agentes y actualización de `AGENTS.md` como índice operativo.

## [2.1.0] - 2026-02-26

### 🎉 Nuevas Características Principales

#### Monitorización y Gestión de Quioscos
- ✨ Nueva interfaz de monitorización **horizontal** en `estado_quioscos`.
- ✨ Renombre funcional de `kiosk_status` a **`estado_quioscos`**.
- ✨ Tarjetas de quiosco con estado en color (online/offline/reiniciando), hostname en mayúsculas y detalles persistentes.
- ✨ Orden de tarjetas por hostname con orden natural (`quiosco2` antes que `quiosco10`).
- ✨ Panel de **servidor** con información operativa (servicios web, uptime, RAM y disco por `/`, `/var`, `/home`).

#### Control Remoto
- ✨ Reinicio remoto de quioscos desde el panel.
- ✨ Acciones de servidor desde el panel: reinicio web y reinicio servidor con confirmaciones.
- ✨ Estado visual de servicios y fechas de últimos reinicios con formato local.

#### Gestión de Diapositivas
- ✨ Selector de tiempo de diapositiva (5s a 120s) desde la web de gestión.
- ✨ Confirmación de cambio mostrando valor actual y nuevo.
- ✨ Indicador visual de cambio pendiente (nuevo valor en color).

#### Cuenta Regresiva en Quiosco
- ✨ Activación/desactivación remota de overlay de cuenta regresiva por quiosco.
- ✨ Selección del quiosco objetivo desde la interfaz de servidor.

#### Gestión Web de Documentos (`/docs`)
- ✨ Nueva pantalla `docs_manager.php` para listar, previsualizar, subir y eliminar archivos.
- ✨ Vista previa integrada para imagen, PDF y video.
- ✨ Sobrescritura con confirmación cuando el nombre ya existe.
- ✨ Regla de nombre para subida: debe empezar por número (ej. `01_portada.pdf`).

### 🔒 Seguridad
- 🔒 Migración a login web de gestor (sesión) en `estado_quioscos`.
- 🔒 Cambio de contraseña desde la propia interfaz de gestión.
- 🔒 Política de contraseña reforzada: mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.
- 🔒 Flujo de cambio de contraseña endurecido: el modal no se cierra en error y muestra validación en rojo hasta corrección.
- 🔒 Menú de gestión con Documentación, Cambiar contraseña y Cerrar sesión.
- 🔒 `estado_quioscos` servido por **HTTPS** con redirección desde HTTP.
- 🔒 Endpoints sensibles protegidos (sesión + validación + CSRF en gestión de documentos).

### 🐧 Monitorización en Alpine/Debian
- 🛠️ Reestructuración de `kiosks_report` por plataforma (`debian` / `alpine`).
- 🛠️ Instalador no interactivo para Alpine (`Quioscos_install_alpine/install_report.sh`).
- 🛠️ Heartbeat con fallback y recogida de métricas (IP, uptime, carga, RAM, disco, HDMI).
- 🛠️ Normalización automática de URL de reporte para evitar configuraciones incompletas.

### 📚 Documentación
- 📖 Creación de documentación operativa incremental en `tech_docs/`.
- 📖 Actualización de guía de repositorio (`AGENTS.md`) y notas de despliegue.

## [2.0.0] - 2025-10-12

### 🎉 Nuevas Características Principales

#### Soporte de Videos
- ✨ **Reproducción de videos MP4 y WEBM** con audio.
- Códecs soportados: H.264+AAC (MP4), VP9+Opus (WEBM).
- Resolución óptima: 1920x1080 (Full HD).
- Reproducción automática con audio activado.
- Avance automático al finalizar el video.
- `object-fit: contain` para mantener proporciones.

#### Gestión Mejorada de Contenido
- 🎯 Detección automática de tipo de contenido (imagen, PDF, video).
- 🔄 Sistema mejorado de transiciones con limpieza de recursos.

#### Herramientas Auxiliares
- 🛠️ Script de conversión de videos: `tools/convert_video.sh`.

## [1.0.0] - 2024-XX-XX

### Características Iniciales
- 📺 Sistema de kiosk básico para imágenes y PDFs.
- 🖥️ Sistema de monitorización de kiosks.
- 🔄 Actualización automática de contenido.
- 📊 Dashboard de estado de kiosks.
- 🐧 Soporte para Xubuntu 24.04.
- 📝 Documentación básica en español e inglés.

---

## Tipos de Cambios

- ✨ `Nuevas características` - Nueva funcionalidad añadida
- 🔧 `Mejoras` - Mejora de funcionalidad existente
- 🐛 `Correcciones` - Corrección de bugs
- 📚 `Documentación` - Cambios en documentación
- 🎨 `Estilos` - Cambios que no afectan funcionalidad
- 🔄 `Refactorización` - Cambios internos sin nueva funcionalidad
- ⚡ `Rendimiento` - Mejoras de rendimiento
- 🔒 `Seguridad` - Correcciones de seguridad

## Enlaces

- [Repositorio en GitHub](https://github.com/GOTXE/monkiosk)
- [README Principal](https://github.com/GOTXE/monkiosk/blob/main/README.md)
