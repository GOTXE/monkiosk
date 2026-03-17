# Herramientas para Monkiosk

Este directorio contiene scripts auxiliares para facilitar el trabajo con Monkiosk.

## init_estado_quioscos_runtime.sh

Script para crear los archivos runtime locales necesarios de `estado_quioscos` durante la instalación.

### Qué crea si no existen

- `auth_users.json`
- `allowed_hosts.txt`
- `allowed_kiosks.json`
- `allowed_kiosks_protection.json`
- `status.json`
- `actions.json`
- `slide_settings.json`
- `overlay_config.json`
- `unknown_kiosk_attempts.json`
- `presentation_viewers.json`
- `control_config.php`

### Uso

```bash
sudo ./tools/init_estado_quioscos_runtime.sh \
  --target-dir /var/www/html/estado_quioscos \
  --admin-user admin \
  --admin-password 'CAMBIAR_CLAVE' \
  --control-token 'CAMBIAR_TOKEN'
```

Si no se indican contraseña o token, el script genera valores iniciales aleatorios.

## backup_monkiosk.sh

Script para crear un backup local completo de Monkiosk.

### Qué guarda

- `/home/kiosk/kioskos`
- `/var/www/html`
- `/etc/nginx/sites-available/default`

### Características

- crea un `.tar.gz` fechado en `/var/backups/monkiosk`
- mantiene permisos, propietarios y fechas
- conserva las últimas `14` copias por defecto

### Uso

```bash
sudo ./backup_monkiosk.sh
```

Variables opcionales:

```bash
sudo BACKUP_DIR=/ruta/backup RETENTION_COUNT=14 ./backup_monkiosk.sh
```

## restore_monkiosk.sh

Script guiado para restaurar Monkiosk desde un backup local.

### Características

- lista los backups disponibles y pide elegir uno por número
- solicita confirmación fuerte antes de sobrescribir
- crea una copia previa del estado actual antes de restaurar
- recarga `nginx` al final
- dispone de simulación con `--dry-run`

### Uso

```bash
sudo ./restore_monkiosk.sh
sudo ./restore_monkiosk.sh --dry-run
sudo ./restore_monkiosk.sh --file /var/backups/monkiosk/monkiosk_backup_YYYY-MM-DD_HHMMSS.tar.gz
```

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

Nota: Se recomienda usar una versión reciente de FFmpeg (por ejemplo >= 4.2) para compatibilidad con códecs modernos (VP9, Opus, etc.).

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
