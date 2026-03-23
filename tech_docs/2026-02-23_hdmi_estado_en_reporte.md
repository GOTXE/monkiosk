# Plan de cambio: estado HDMI en reporte

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Detectar si el quiosco tiene pantalla HDMI conectada y mostrarlo en `estado_quioscos/index.html`.

## 2) Alcance
- `kiosks_report/debian/report_status.sh`
- `Quioscos_install_alpine/report/report_status.sh`
- `estado_quioscos/update_status.php`
- `estado_quioscos/index.html`

## 3) Diseño
1. Detectar HDMI en script usando `/sys/class/drm/*HDMI*/status`.
2. Enviar campo `hdmi_connected` (`connected` / `disconnected` / `unknown`).
3. Backend guarda campo saneado.
4. Frontend muestra fila `HDMI` en "Ver reporte".

## 4) Ejecución
Completada.

- Scripts heartbeat actualizados:
  - `kiosks_report/debian/report_status.sh`
  - `Quioscos_install_alpine/report/report_status.sh`
  - detección HDMI por `/sys/class/drm/*HDMI*/status`
  - envío de campo `hdmi_connected` (`connected` / `disconnected` / `unknown`)

- Backend:
  - `estado_quioscos/update_status.php` guarda `hdmi_connected` saneado.

- Frontend:
  - `estado_quioscos/index.html` muestra fila `HDMI` en \"Ver reporte\".
  - mapeo visual: `Conectado`, `Desconectado`, `Desconocido`.

- Despliegue aplicado en servidor:
  - `/var/www/html/estado_quioscos/index.html`
  - `/var/www/html/estado_quioscos/update_status.php`
  - `/opt/monitoring/report_status.sh`
  - `/opt/monitoring/Quioscos_install_alpine/report/report_status.sh`
