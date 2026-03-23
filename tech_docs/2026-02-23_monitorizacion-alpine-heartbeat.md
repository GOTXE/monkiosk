# Plan de cambio: monitorización Alpine con heartbeat principal/fallback

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Preparar monitorización de quioscos para Alpine/Openbox/Chromium con heartbeat robusto y reporte básico visible en el panel.

## 2) Alcance
- `kiosks_report/debian/report_status.sh`
- `kiosks_report/debian/kioskmonitoring.service`
- `estado_quioscos/update_status.php`
- `estado_quioscos/index.html`
- Nuevos archivos Alpine/OpenRC en `kiosks_report/alpine/`

## 3) Diseño
1. Heartbeat con URL principal y fallback.
2. Configurable por archivo externo (`/etc/kiosk/heartbeat.conf`).
3. Reporte enviado: `name`, `status`, `local_ip`, `kiosk_url`, `uptime_s`, `load1`, `mem_free_mb`, `disk_free_mb`, `sent_to`.
4. Backend guarda campos opcionales saneados.
5. Frontend muestra mini-reporte por quiosco.
6. Añadir servicio OpenRC de ejemplo para Alpine.

## 4) Pruebas previstas
- Validación sintáctica de PHP y shell.
- Simulación de POST con campos opcionales.
- Comprobación de renderizado de nuevos campos en `index.html`.

## 5) Ejecución
Completada.

- `kiosks_report/debian/report_status.sh`
  - Reescrito a `sh` compatible Alpine/BusyBox.
  - Soporte de `PRIMARY_URL` + `FALLBACK_URL`.
  - Carga opcional de `/etc/kiosk/heartbeat.conf`.
  - Reporte extendido enviado al servidor:
    - `local_ip`, `kiosk_url`, `uptime_s`, `load1`, `mem_free_mb`, `disk_free_mb`, `report_target`.

- `kiosks_report/debian/kioskmonitoring.service`
  - Añadido `Type=simple` y `EnvironmentFile=-/etc/kiosk/heartbeat.conf`.

- `kiosks_report/alpine/`
  - Nuevo `heartbeat.conf.example`.
  - Nuevo `kioskmonitoring.openrc`.
  - Nuevo `README.md` con pasos de instalación.

- `estado_quioscos/update_status.php`
  - Acepta y sanea campos opcionales del mini-reporte.
  - Conserva lógica de `history`, `unstable` y limpieza.

- `estado_quioscos/index.html`
  - Añadido bloque `Ver reporte` por quiosco.
  - Se muestran campos extendidos cuando están presentes.

- `estado_quioscos/styles.css`
  - Estilos para bloque de reporte compacto.

## 6) Validaciones
- `php -l estado_quioscos/update_status.php` OK.
- `bash -n kiosks_report/debian/report_status.sh` OK.
- `sh -n kiosks_report/debian/report_status.sh` OK.
