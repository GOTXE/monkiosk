# Error al subir MP4 en Gestion diapositivas

## Planificacion

- Objetivo: revisar el error `JSON.parse: unexpected character` al subir videos MP4 desde `Gestion diapositivas`.
- Alcance: flujo de subida en `estado_quioscos/docs_manager.php` y validacion de errores en `estado_quioscos/docs_api.php`.
- Riesgos: no cambiar la politica de nombres, tipos permitidos ni rutas de documentos; mantener respuestas JSON para el frontend.
- Archivos previstos:
  - `estado_quioscos/docs_manager.php`
  - `estado_quioscos/docs_api.php`
  - `tech_docs/registro_cambios.md`

## Ejecucion

- `estado_quioscos/docs_manager.php` deja de usar `response.json()` directamente y lee primero la respuesta como texto.
- Si el servidor responde HTML, texto no JSON o HTTP 413, la interfaz muestra un error operativo en lugar del error tecnico de `JSON.parse`.
- `estado_quioscos/docs_api.php` detalla los errores de `$_FILES['file']['error']`.
- Se detecta el caso de multipart vacio por superar `post_max_size` y se responde JSON con HTTP 413 cuando PHP llega a ejecutar el endpoint.
- Si el corte ocurre antes de PHP, por ejemplo `client_max_body_size` de Nginx, el frontend informa de respuesta HTML/no JSON o archivo demasiado grande.
- Se copian `docs_manager.php` y `docs_api.php` a `/var/www/html/estado_quioscos/`.
- Se ajusta produccion para aceptar la promesa de la UI:
  - Nginx: `client_max_body_size 220M`
  - PHP-FPM: `post_max_size = 220M`
  - PHP-FPM: `upload_max_filesize = 200M`
- Se actualiza `VERSION` a `0.2.1` por tratarse de un bugfix compatible.

## Tester

- `php -l estado_quioscos/docs_api.php`
- `php -l estado_quioscos/docs_manager.php`
- Revision de diff acotado a subida/listado/borrado de documentos y documentacion.
- `nginx -t`
- `php-fpm8.4 -t`
- `systemctl reload nginx`
- `systemctl reload php8.4-fpm`
- Comprobado que los servicios quedan `active`.
- No se pudo validar subida por `curl` local: la sesion no conecta a `127.0.0.1:80/443` aunque los servicios aparecen activos.
- Validacion manual del usuario: la subida del MP4 funciona tras desplegar cambios y recargar limites.
