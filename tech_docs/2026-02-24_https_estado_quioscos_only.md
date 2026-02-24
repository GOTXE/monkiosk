# Plan y Ejecucion - HTTPS solo para estado_quioscos

## Objetivo
Forzar `estado_quioscos` por HTTPS y mantener la web de presentacion de quioscos en HTTP.

## Ejecucion en servidor (Nginx)
1. Certificado autofirmado (10 años)
- Ruta:
  - `/etc/ssl/ofap601/ofap601-kiosk-server.crt`
  - `/etc/ssl/ofap601/ofap601-kiosk-server.key`
- SAN incluidos:
  - `ofap601-kiosk-server`
  - `localhost`
  - `192.2.254.4`
  - `127.0.0.1`

2. Reglas de routing
- En `:80`:
  - `location ^~ /estado_quioscos { return 301 https://$host$request_uri; }`
  - resto del sitio sigue en HTTP.
- En `:443`:
  - solo permitido `estado_quioscos`.
  - resto de rutas devuelve `403`.

## Resultado
- `http://IP/estado_quioscos` redirige a `https://IP/estado_quioscos`.
- Presentacion de quiosco sigue disponible por HTTP sin cambios.
