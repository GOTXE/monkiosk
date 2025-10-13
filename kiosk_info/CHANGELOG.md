````markdown
# Changelog - Monkiosk

Todos los cambios notables en este proyecto serán documentados en este archivo.

## [2.0.0] - 2025-10-12

### 🎉 Nuevas Características Principales

#### Soporte de Videos
- ✨ **Reproducción de videos MP4 y WEBM** con audio
  - Códecs soportados: H.264+AAC (MP4), VP9+Opus (WEBM)
  - Resolución óptima: 1920x1080 (Full HD)
  - Reproducción automática con audio activado
  - Avance automático al finalizar el video
  - Object-fit: contain para mantener proporciones
  - Fondo negro para mejor visualización

#### Gestión Mejorada de Contenido
- 🎯 **Detección automática de tipo de contenido**
  - Imágenes: JPG, JPEG, PNG
  - Documentos: PDF
  - Videos: MP4, WEBM
- 🔄 **Sistema mejorado de transiciones**
  - Limpieza de recursos al cambiar contenido
  - Elementos separados para video, iframe e imagen
  - Timeouts específicos por tipo de contenido
  - Eliminado setInterval global

#### Herramientas Auxiliares
- 🛠️ **Script de conversión de videos** (`tools/convert_video.sh`)
  - Conversión automática a formato óptimo
  - Ajuste a resolución 1920x1080
  - Optimización de códecs
  - Información detallada de archivos
  - Soporte para MP4 y WEBM

### 📚 Documentación

#### Nueva Documentación
- 📖 **GUIA_VIDEOS.md** - Guía completa de videos
  - Especificaciones técnicas detalladas
  - Instrucciones de conversión con FFmpeg
  - Solución de problemas comunes
  - Mejores prácticas
  - Ejemplos de uso

- 📖 **kiosk_web/docs/README.md** - Guía de contenido
  - Formatos soportados
  - Convención de nombres
  - Tamaños recomendados
  - Ejemplos de estructura

- 📖 **tools/README.md** - Documentación de herramientas
  - Uso del script de conversión
  - Parámetros de conversión
  - Solución de problemas
  - Ejemplos prácticos

#### Documentación Actualizada
- 📝 Actualizado **README.md** con sección de videos
- 📝 Actualizado **README_EN.md** con soporte de videos
- 📝 Actualizado **estructura.md** con nueva organización

### 🎨 Páginas de Prueba y Conceptos

- 🧪 **test_video.html** - Página de prueba para videos
  - Detección de soporte de códecs
  - Información del navegador
  - Monitor de carga y reproducción
  - Instrucciones de uso

- 💡 **admin_concept.html** - Concepto de panel de administración
  - Mockup de funcionalidades futuras
  - Roadmap de desarrollo
  - Stack tecnológico propuesto
  - Preguntas para definir proyecto

### 🔧 Mejoras Técnicas

#### index.php
- ✅ Filtro mejorado de archivos (incluye mp4, webm)
- ✅ Sistema de elementos múltiples (video, iframe, img)
- ✅ Manejo robusto de errores
- ✅ Logging detallado en consola
- ✅ Limpieza de recursos al cambiar contenido
- ✅ Pausado correcto de videos
- ✅ Gestión de eventos de video (onended, onerror)

#### Estilos
- 🎨 Fondo cambiado a negro (#000000)
- 🎨 Estilos específicos para elemento video
- 🎨 Container flex para mejor centrado
- 🎨 Object-fit: contain para videos

### 📦 Archivos de Configuración

- ⚙️ **config.json.example** - Ejemplo de configuración
  - Configuración de intervalos
  - Gestión de contenido
  - Opciones de logging

- 📋 **.gitignore** actualizado
  - Exclusión de archivos multimedia
  - Mantiene README.md en docs/
  - Mantiene archivos .example

### 🗂️ Estructura del Proyecto

Nueva estructura de carpetas:
```
monkiosk/
├── kiosk_web/
│   ├── docs/              [Contenido multimedia + README]
│   ├── index.php          [Mejorado con soporte de video]
│   ├── test_video.html    [NUEVO - Tests]
│   ├── admin_concept.html [NUEVO - Concepto futuro]
│   └── config.json.example [NUEVO - Configuración]
├── tools/                 [NUEVO - Herramientas]
│   ├── convert_video.sh
  └── README.md
├── GUIA_VIDEOS.md         [NUEVO - Documentación]
└── ...
```

## [1.0.0] - 2024-XX-XX

### Características Iniciales
- 📺 Sistema de kiosk básico para imágenes y PDFs
- 🖥️ Sistema de monitorización de kiosks
- 🔄 Actualización automática de contenido
- 📊 Dashboard de estado de kiosks
- 🐧 Soporte para Xubuntu 24.04
- 📝 Documentación básica en español e inglés

---

## Tipos de Cambios

- ✨ `Nuevas características` - Nueva funcionalidad añadida
- 🔧 `Mejoras` - Mejora de funcionalidad existente
- 🐛 `Correcciones` - Corrección de bugs
- 📚 `Documentación` - Cambios en documentación
- 🎨 `Estilos` - Cambios que no afectan funcionalidad
- 🔄 `Refactorización` - Cambios en código que no añaden funcionalidad ni corrigen bugs
- ⚡ `Rendimiento` - Mejoras de rendimiento
- 🔒 `Seguridad` - Correcciones de vulnerabilidades

## Enlaces

- [Repositorio en GitHub](https://github.com/GOTXE/monkiosk)
- [Guía de Videos](GUIA_VIDEOS.md)
- [README Principal](README.md)

````
# Changelog - Monkiosk

Todos los cambios notables en este proyecto serán documentados en este archivo.

## [2.0.0] - 2025-10-12

### 🎉 Nuevas Características Principales

#### Soporte de Videos
- ✨ **Reproducción de videos MP4 y WEBM** con audio
  - Códecs soportados: H.264+AAC (MP4), VP9+Opus (WEBM)
  - Resolución óptima: 1920x1080 (Full HD)
  - Reproducción automática con audio activado
  - Avance automático al finalizar el video
  - Object-fit: contain para mantener proporciones
  - Fondo negro para mejor visualización

#### Gestión Mejorada de Contenido
- 🎯 **Detección automática de tipo de contenido**
  - Imágenes: JPG, JPEG, PNG
  - Documentos: PDF
  - Videos: MP4, WEBM
- 🔄 **Sistema mejorado de transiciones**
  - Limpieza de recursos al cambiar contenido
  - Elementos separados para video, iframe e imagen
  - Timeouts específicos por tipo de contenido
  - Eliminado setInterval global

#### Herramientas Auxiliares
- 🛠️ **Script de conversión de videos** (`tools/convert_video.sh`)
  - Conversión automática a formato óptimo
  - Ajuste a resolución 1920x1080
  - Optimización de códecs
  - Información detallada de archivos
  - Soporte para MP4 y WEBM

### 📚 Documentación

#### Nueva Documentación
- 📖 **GUIA_VIDEOS.md** - Guía completa de videos
  - Especificaciones técnicas detalladas
  - Instrucciones de conversión con FFmpeg
  - Solución de problemas comunes
  - Mejores prácticas
  - Ejemplos de uso

- 📖 **kiosk_web/docs/README.md** - Guía de contenido
  - Formatos soportados
  - Convención de nombres
  - Tamaños recomendados
  - Ejemplos de estructura

- 📖 **tools/README.md** - Documentación de herramientas
  - Uso del script de conversión
  - Parámetros de conversión
  - Solución de problemas
  - Ejemplos prácticos

#### Documentación Actualizada
- 📝 Actualizado **README.md** con sección de videos
- 📝 Actualizado **README_EN.md** con soporte de videos
- 📝 Actualizado **estructura.md** con nueva organización

### 🎨 Páginas de Prueba y Conceptos

- 🧪 **test_video.html** - Página de prueba para videos
  - Detección de soporte de códecs
  - Información del navegador
  - Monitor de carga y reproducción
  - Instrucciones de uso

- 💡 **admin_concept.html** - Concepto de panel de administración
  - Mockup de funcionalidades futuras
  - Roadmap de desarrollo
  - Stack tecnológico propuesto
  - Preguntas para definir proyecto

### 🔧 Mejoras Técnicas

#### index.php
- ✅ Filtro mejorado de archivos (incluye mp4, webm)
- ✅ Sistema de elementos múltiples (video, iframe, img)
- ✅ Manejo robusto de errores
- ✅ Logging detallado en consola
- ✅ Limpieza de recursos al cambiar contenido
- ✅ Pausado correcto de videos
- ✅ Gestión de eventos de video (onended, onerror)

#### Estilos
- 🎨 Fondo cambiado a negro (#000000)
- 🎨 Estilos específicos para elemento video
- 🎨 Container flex para mejor centrado
- 🎨 Object-fit: contain para videos

### 📦 Archivos de Configuración

- ⚙️ **config.json.example** - Ejemplo de configuración
  - Configuración de intervalos
  - Gestión de contenido
  - Opciones de logging

- 📋 **.gitignore** actualizado
  - Exclusión de archivos multimedia
  - Mantiene README.md en docs/
  - Mantiene archivos .example

### 🗂️ Estructura del Proyecto

Nueva estructura de carpetas:
```
monkiosk/
├── kiosk_web/
│   ├── docs/              [Contenido multimedia + README]
│   ├── index.php          [Mejorado con soporte de video]
│   ├── test_video.html    [NUEVO - Tests]
│   ├── admin_concept.html [NUEVO - Concepto futuro]
│   └── config.json.example [NUEVO - Configuración]
├── tools/                 [NUEVO - Herramientas]
│   ├── convert_video.sh
│   └── README.md
├── GUIA_VIDEOS.md         [NUEVO - Documentación]
└── ...
```

## [1.0.0] - 2024-XX-XX

### Características Iniciales
- 📺 Sistema de kiosk básico para imágenes y PDFs
- 🖥️ Sistema de monitorización de kiosks
- 🔄 Actualización automática de contenido
- 📊 Dashboard de estado de kiosks
- 🐧 Soporte para Xubuntu 24.04
- 📝 Documentación básica en español e inglés

---

## Tipos de Cambios

- ✨ `Nuevas características` - Nueva funcionalidad añadida
- 🔧 `Mejoras` - Mejora de funcionalidad existente
- 🐛 `Correcciones` - Corrección de bugs
- 📚 `Documentación` - Cambios en documentación
- 🎨 `Estilos` - Cambios que no afectan funcionalidad
- 🔄 `Refactorización` - Cambios en código que no añaden funcionalidad ni corrigen bugs
- ⚡ `Rendimiento` - Mejoras de rendimiento
- 🔒 `Seguridad` - Correcciones de vulnerabilidades

## Enlaces

- [Repositorio en GitHub](https://github.com/GOTXE/monkiosk)
- [Guía de Videos](GUIA_VIDEOS.md)
- [README Principal](README.md)
