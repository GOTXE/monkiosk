# Guía Rápida - Monkiosk con Videos

## 🚀 Inicio Rápido

### Para Añadir Videos

1. **Preparar video** (Opcional - si ya está en MP4 1920x1080)
	```bash
	cd tools
	./convert_video.sh mi_video.avi mi_video.mp4
	```

2. **Copiar a docs/**
	```bash
	cp mi_video.mp4 ../kiosk_web/docs/5.mp4
	```

3. **Listo!** El kiosk lo detectará automáticamente

## 📋 Formatos Soportados

| Tipo | Formatos | Comportamiento |
|------|----------|----------------|
| Videos | MP4, WEBM | Se reproduce hasta el final, luego avanza |
| Imágenes | JPG, PNG | Se muestra durante 5 segundos (configurable) |
| Documentos | PDF | Se muestra durante 5 segundos (configurable) |

## ⚙️ Configuración Rápida

### Cambiar duración de imágenes/PDFs
Edita `kiosk_web/index.php` y modifica la variable `intervalo` (valor en ms), por ejemplo:
```javascript
var intervalo = 8000; // 8 segundos (8000 ms)
```

### Verificar videos soportados
Abre `kiosk_web/test_video.html` en tu navegador

## 🎬 Especificaciones de Video Recomendadas

```
Resolución:    1920x1080 (Full HD)
Formato:       MP4 (recomendado) o WEBM
Video Códec:   H.264 (MP4) o VP9 (WEBM)
Audio Códec:   AAC (MP4) o Opus (WEBM)
Bitrate Video: 5-10 Mbps
Bitrate Audio: 128-192 kbps
Frame Rate:    24-30 fps
```

## 🛠️ Conversión de Videos

### Con el script incluido
```bash
cd tools
./convert_video.sh input.avi output.mp4
```

### Con FFmpeg directamente
```bash
ffmpeg -i input.avi -c:v libx264 -preset medium -crf 23 \
  -c:a aac -b:a 192k -ar 48000 \
  -vf scale=1920:1080:force_original_aspect_ratio=decrease,pad=1920:1080:(ow-iw)/2:(oh-ih)/2 \
  output.mp4
```

## 📁 Estructura de Archivos

```
kiosk_web/docs/
├── 1.jpg      ← Primera imagen (5s)
├── 2.mp4      ← Primer video (duración automática)
├── 3.png      ← Segunda imagen (5s)
├── 4.webm     ← Segundo video (duración automática)
├── 5.pdf      ← Documento (5s)
└── ...
```

Los archivos se reproducen en orden numérico.

## 🔍 Solución Rápida de Problemas

### El video no se reproduce
- ✅ Verifica que es MP4 o WEBM
- ✅ Comprueba el códec: `ffprobe video.mp4`
- ✅ Abre consola del navegador (F12) para ver errores
- ✅ Prueba con `test_video.html`

### Video sin audio
- ✅ Verifica códec de audio: AAC (MP4) o Opus (WEBM)
- ✅ Comprueba volumen del sistema
- ✅ Verifica que Chromium no esté en modo mudo

### Video pixelado
- ✅ Aumenta bitrate: `-b:v 8M` en ffmpeg
- ✅ Mejora preset: `-preset slow`
- ✅ Reduce CRF: `-crf 20` (menor = mejor calidad)

### Archivo muy grande
- ✅ Aumenta CRF: `-crf 26` (mayor = menor tamaño)
- ✅ Reduce bitrate: `-b:v 3M`
- ✅ Considera usar WEBM (mejor compresión)

## 📚 Documentación Completa

- **GUIA_VIDEOS.md** - Guía detallada de videos
- **README.md** - Documentación principal
- **tools/README.md** - Guía de herramientas
- **kiosk_web/docs/README.md** - Info sobre contenido

## 🎯 Ejemplos de Uso

### Presentación Corporativa
```
1.jpg  - Logo empresa (5s)
2.mp4  - Video institucional (30s) 
3.jpg  - Misión y visión (8s)
4.mp4  - Testimonios (45s)
5.jpg  - Contacto (10s)
```

### Catálogo de Productos
```
1.mp4  - Demo producto A (20s)
2.jpg  - Especificaciones A (7s)
3.mp4  - Demo producto B (20s)
4.jpg  - Especificaciones B (7s)
5.pdf  - Catálogo completo (15s)
```

## 💡 Tips

- 📏 Usa siempre 1920x1080 para mejor calidad
- ⏱️ Videos de 15-60 segundos son ideales
- 🔊 Ajusta volumen de videos antes de subir
- 📦 Comprime videos para ahorrar espacio
- 🔢 Nombres numéricos: 1.jpg, 2.mp4, 3.png...
- 🧪 Prueba siempre con `kiosk_web/test_video.html` primero

## Notas
- Para visualizar PDFs sin la interfaz del navegador, coloca `pdf.min.js` y `pdf.worker.min.js` en `kiosk_web/vendor/pdfjs/` (esto permite renderizar PDFs en canvas en modo offline).
- Por políticas de autoplay en navegadores, los videos pueden requerir `muted` para poder reproducirse automáticamente; el sistema inicia videos silenciados y permite activar audio mediante interacción del usuario.

## 🆘 Soporte

Si tienes problemas:
1. Revisa esta guía rápida
2. Consulta GUIA_VIDEOS.md
3. Revisa la consola del navegador (F12)
4. Abre un issue en GitHub

---

**Monkiosk v2.0** - Sistema de Kiosk Multimedia

````
