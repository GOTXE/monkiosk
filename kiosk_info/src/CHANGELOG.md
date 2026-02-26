# Changelog - Monkiosk

Todos los cambios notables en este proyecto serán documentados en este archivo.

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
