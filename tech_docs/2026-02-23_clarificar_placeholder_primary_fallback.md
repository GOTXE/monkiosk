# Plan de cambio: clarificar placeholder PRIMARY/FALLBACK

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Dejar claro en el script que `PRIMARY_URL` y `FALLBACK_URL` son valores por defecto (placeholder) y que la configuración real se toma de `/etc/kiosk/heartbeat.conf`.

## 2) Alcance
- `kiosks_report/debian/report_status.sh`
- `Quioscos_install_alpine/report/report_status.sh`

## 3) Plan
1. Mejorar comentarios de cabecera sobre placeholders.
2. Aclarar que `heartbeat.conf` tiene prioridad.

## 4) Ejecución
Pendiente.

## 4) Ejecución
Completada.

- Scripts actualizados:
  - `kiosks_report/debian/report_status.sh`
  - `Quioscos_install_alpine/report/report_status.sh`
- Comentario añadido:
  - `PRIMARY_URL`/`FALLBACK_URL` son placeholder por defecto.
  - La config real es `/etc/kiosk/heartbeat.conf` y tiene prioridad.
