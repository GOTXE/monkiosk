# Herramientas para Monkiosk

Este directorio contiene scripts auxiliares para facilitar el trabajo con Monkiosk.

## convert_video.sh

Script para convertir videos a formatos optimizados para reproducción en kiosks.

### Uso

```bash
# Hacer el script ejecutable (solo la primera vez)
chmod +x convert_video.sh

# Convertir video a MP4 (recomendado)
./convert_video.sh input.avi output.mp4

# Convertir a WEBM
./convert_video.sh input.mov output.webm webm

# Conversión automática (detecta formato por extensión)
./convert_video.sh video.avi
```

### Características

- **Conversión a MP4**: H.264 + AAC (máxima compatibilidad)
- **Conversión a WEBM**: VP9 + Opus (open source)
- **Ajuste automático a 1920x1080**: Mantiene aspect ratio
- **Padding negro**: Si el video no es 16:9
- **Optimización**: Bitrate balanceado para calidad y tamaño

### Requisitos

- FFmpeg instalado:
  ```bash
  sudo apt install ffmpeg
  ```

### Ejemplos de uso

#### Convertir múltiples videos
```bash
#!/bin/bash
for video in *.avi; do
    ./convert_video.sh "$video" "${video%.avi}.mp4"
done
```

#### Convertir y mover a docs/
```bash
#!/bin/bash
./convert_video.sh input.mov 1.mp4
mv 1.mp4 ../kiosk_web/docs/
```

### Parámetros de conversión

#### MP4 (H.264 + AAC)
- Códec video: libx264
- Preset: medium (balance velocidad/calidad)
- CRF: 23 (calidad constante)
- Códec audio: AAC
- Bitrate audio: 192 kbps
- Sample rate: 48 kHz

#### WEBM (VP9 + Opus)
- Códec video: libvpx-vp9
- Bitrate video: 5 Mbps
- Códec audio: libopus
- Bitrate audio: 192 kbps

### Solución de problemas

**Error: FFmpeg no está instalado**
```bash
sudo apt update
sudo apt install ffmpeg
```

**Video muy grande después de conversión**
- Aumenta el valor CRF (23 → 26) para menor calidad/tamaño
- Reduce el bitrate de video (5M → 3M)

**Video de baja calidad**
- Reduce el valor CRF (23 → 20) para mayor calidad
- Aumenta el bitrate de video (5M → 8M)

## Futuras herramientas

Posibles scripts adicionales a desarrollar:

- `batch_convert.sh` - Conversión en lote
- `optimize_images.sh` - Optimización de imágenes
- `generate_playlist.sh` - Generar configuración automática
- `check_media.sh` - Verificar validez de archivos multimedia

---

Para más información, consulta `GUIA_VIDEOS.md` en la raíz del proyecto.
