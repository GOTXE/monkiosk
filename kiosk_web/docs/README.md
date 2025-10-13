# Directorio de Contenido para Kiosk

Este directorio contiene los archivos multimedia que se mostrarán en el kiosk.

## Formatos Soportados

- **Imágenes**: `.jpg`, `.jpeg`, `.png`
- **Documentos**: `.pdf`
- **Videos**: `.mp4`, `.webm`

## Convención de Nombres

Los archivos deben nombrarse con números para mantener el orden de presentación:

```
1.jpg
2.mp4
3.png
4.pdf
5.webm
...
```

## Tamaños Recomendados

### Imágenes
- Resolución: 1920x1080 píxeles (Full HD)
- Formato: JPG para fotos, PNG para gráficos con transparencia
- Peso: < 2 MB por imagen

### Videos
- Resolución: 1920x1080 píxeles (Full HD)
- Códec: H.264 + AAC (MP4) o VP9 + Opus (WEBM)
- Duración: 15-60 segundos recomendado
- Bitrate: 5-10 Mbps
- Peso: < 50 MB por video (depende de la duración)

### PDFs
- Tamaño: Carta o A4
- Peso: < 5 MB

## Ejemplos

Coloca tus archivos aquí siguiendo esta estructura:

```
docs/
├── 1.jpg          # Logo de empresa
├── 2.mp4          # Video institucional
├── 3.png          # Infografía
├── 4.pdf          # Documento informativo
├── 5.webm         # Otro video
└── README.md      # Este archivo
```

## Notas Importantes

1. El sistema lee los archivos automáticamente al iniciar
2. Para añadir nuevos archivos, simplemente cópialos aquí
3. El orden se determina por el número en el nombre del archivo
4. Los archivos se cachean con timestamp para forzar actualización
5. Los videos se reproducen completos antes de avanzar
6. Las imágenes y PDFs se muestran según el intervalo configurado (5s por defecto)

Para más información sobre videos, consulta `GUIA_VIDEOS.md` en la raíz del proyecto.

### Notas adicionales
- Para renderizar PDFs sin la UI del navegador y en modo offline, coloca `pdf.min.js` y `pdf.worker.min.js` en `vendor/pdfjs/` dentro de `kiosk_web/`.
- En algunos navegadores, la reproducción automática con audio puede estar bloqueada; el sistema inicia vídeos silenciados y permite activar audio mediante interacción del usuario.
