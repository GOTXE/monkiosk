## Planificacion

- Objetivo: dejar una unica guia util para usuario, eliminando contenido obsoleto de `GUIA_RAPIDA.md` y absorbiendo en ella solo lo necesario de `GUIA_VIDEOS.md`.
- Alcance: documentacion de `kiosk_info/src` y copia web en produccion.
- Riesgos: dejar referencias rotas a `GUIA_VIDEOS.md` o instrucciones que ya no coincidan con la app.
- Archivos previstos:
  - `kiosk_info/src/GUIA_RAPIDA.md`
  - `kiosk_info/src/GUIA_VIDEOS.md`

## Ejecucion

- Se ha reescrito `kiosk_info/src/GUIA_RAPIDA.md` para dejar solo instrucciones vigentes para usuario.
- Se ha reducido `kiosk_info/src/GUIA_VIDEOS.md` a una referencia minima para no dejar enlaces rotos en el visor.
- Se ha eliminado finalmente `kiosk_info/src/GUIA_VIDEOS.md` del visor y de la lista de archivos permitidos.
- Se ha eliminado contenido obsoleto: edicion manual de `index.php`, referencia a `test_video.html`, soporte presentado como audio activo por defecto y bloques duplicados.
- Se ha ajustado `GUIA_RAPIDA.md` para indicar de forma explicita que el sistema pone los videos en silencio.
- Se ha actualizado la guia rapida en la ruta web de produccion en `/var/www/html/kiosk_info/src/` y se ha eliminado la guia de videos obsoleta.
- Verificacion:
  - lectura de las copias en produccion tras la actualizacion
