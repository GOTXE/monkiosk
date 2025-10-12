# Guía de Soporte de Videos en Monkiosk

## 🎬 Introducción

El sistema Monkiosk ahora soporta la reproducción de videos con audio, permitiendo crear presentaciones multimedia completas que combinan imágenes, PDFs y videos.

## 📋 Formatos Soportados

### Videos
- **MP4** (H.264 + AAC) - Recomendado para mejor compatibilidad
- **WEBM** (VP8/VP9 + Vorbis/Opus) - Alternativa open-source

### Imágenes
- JPG/JPEG
- PNG

### Documentos
- PDF

## 🎥 Especificaciones Recomendadas para Videos

### Resolución
- **1920x1080 (Full HD)** - Óptimo para pantallas de kiosk
- 1280x720 (HD) - Alternativa para archivos más pequeños

### Códecs de Video
- **MP4**: H.264 (AVC) - Mayor compatibilidad
- **WEBM**: VP9 - Mejor compresión

### Códecs de Audio
- **MP4**: AAC-LC, 128-192 kbps, 48 kHz
- **WEBM**: Opus o Vorbis, 128-192 kbps

### Bitrate
- Video: 5-10 Mbps (Full HD)
- Audio: 128-192 kbps

### Frame Rate
- 24-30 fps (estándar)
- 60 fps (para contenido con mucho movimiento)

## 🛠️ Conversión de Videos

### Con FFmpeg (Línea de comandos)

#### Convertir a MP4 (H.264 + AAC)
```bash
ffmpeg -i input.avi -c:v libx264 -preset medium -crf 23 \
  -c:a aac -b:a 192k -ar 48000 \
  -vf scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2 \
  output.mp4
```

#### Convertir a WEBM (VP9 + Opus)
```bash
ffmpeg -i input.avi -c:v libvpx-vp9 -b:v 5M \
  -c:a libopus -b:a 192k \
  -vf scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2 \
  output.webm
```

#### Reducir tamaño manteniendo calidad
```bash
# Para MP4 (ajusta -crf: menor=mejor calidad, 18-28 recomendado)
ffmpeg -i input.mp4 -c:v libx264 -crf 23 -preset slow -c:a copy output.mp4

# Para WEBM
ffmpeg -i input.webm -c:v libvpx-vp9 -b:v 3M -c:a copy output.webm
```

### Con Herramientas Gráficas

#### HandBrake (Windows, Mac, Linux)
1. Abrir HandBrake
2. Seleccionar archivo de video
3. Preset: "Fast 1080p30"
4. Ajustar resolución si necesario: 1920x1080
5. Audio: AAC, 192 kbps
6. Iniciar conversión

#### VLC Media Player
1. Menú: Media > Convertir/Guardar
2. Añadir archivo de origen
3. Perfil: Video - H.264 + MP3 (MP4)
4. Configuración personalizada: 1920x1080, bitrate 5000 kb/s
5. Convertir

## 📁 Estructura de Archivos

Los archivos deben nombrarse numéricamente para mantener el orden:

```
kiosk_web/docs/
├── 1.jpg          # Primera imagen
├── 2.mp4          # Primer video
├── 3.png          # Segunda imagen
├── 4.webm         # Segundo video
├── 5.pdf          # Documento PDF
└── ...
```

## ⚙️ Configuración

### Intervalo para Imágenes y PDFs
En `index.php`, línea ~105:
```javascript
var intervalo = 5000; // 5 segundos (5000 ms)
```

### Videos
Los videos se reproducen hasta completarse automáticamente. No usan el intervalo configurado.

## 🔧 Solución de Problemas

### El video no se reproduce
- ✅ Verificar que el formato es MP4 o WEBM
- ✅ Comprobar los códecs (H.264+AAC para MP4)
- ✅ Revisar la consola del navegador (F12) para errores
- ✅ Verificar permisos del archivo (644)

### El video no tiene audio
- ✅ Verificar que el códec de audio es compatible (AAC o Opus)
- ✅ Comprobar que el navegador Chromium no está en modo mudo
- ✅ Revisar volumen del sistema

### El video se ve pixelado
- ✅ Aumentar el bitrate de video (8-10 Mbps)
- ✅ Usar resolución 1920x1080
- ✅ Cambiar preset a "slow" en ffmpeg

### Archivo muy grande
- ✅ Ajustar CRF a valor más alto (23-28)
- ✅ Reducir bitrate de video (3-5 Mbps)
- ✅ Considerar usar WEBM con VP9

## 🎯 Mejores Prácticas

1. **Nombrado consistente**: Usa nombres numéricos (1.mp4, 2.jpg, 3.webm)
2. **Optimización**: Comprime videos antes de subirlos
3. **Pruebas**: Prueba los videos en Chromium antes de desplegar
4. **Backups**: Mantén copias de los archivos originales
5. **Duración**: Videos entre 15-60 segundos son ideales para kiosks
6. **Audio**: Ajusta el volumen para que sea audible pero no molesto

## 📊 Ejemplos de Uso

### Presentación Corporativa
```
1.jpg  - Logo de empresa (5s)
2.mp4  - Video institucional (30s)
3.jpg  - Misión y visión (8s)
4.jpg  - Valores corporativos (8s)
5.mp4  - Testimonios de clientes (45s)
6.jpg  - Información de contacto (10s)
```

### Exhibición de Productos
```
1.mp4  - Video de producto 1 (20s)
2.jpg  - Especificaciones producto 1 (7s)
3.mp4  - Video de producto 2 (20s)
4.jpg  - Especificaciones producto 2 (7s)
5.pdf  - Catálogo completo (15s)
```

## 🚀 Características Avanzadas (Futuras)

Posibles mejoras para considerar:
- ⬜ Configuración JSON para duración personalizada por archivo
- ⬜ Soporte para playlists específicas por horario
- ⬜ Subtítulos opcionales para videos
- ⬜ Transiciones animadas entre contenidos
- ⬜ Control remoto de contenido vía API
- ⬜ Estadísticas de reproducción

## 📞 Soporte

Para problemas o sugerencias, consulta el README principal o abre un issue en GitHub.

---
**Última actualización**: Octubre 2025
