# Plan de cambio: script de instalación automática en Alpine

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Crear un script de instalación para Alpine con mínima interacción para desplegar heartbeat de monitorización.

## 2) Alcance
- `kiosks_report/alpine/install_heartbeat_alpine.sh` (nuevo)
- `kiosks_report/alpine/README.md` (actualización)

## 3) Diseño
- Script no interactivo (modo por variables de entorno y flags).
- Copia:
  - `kiosks_report/debian/report_status.sh` -> `/opt/monitoring/report_status.sh`
  - `kiosks_report/alpine/kioskmonitoring.openrc` -> `/etc/init.d/kioskmonitoring`
  - config -> `/etc/kiosk/heartbeat.conf`
- Configuración de URLs `PRIMARY_URL`/`FALLBACK_URL` y tiempos.
- Alta de servicio OpenRC, habilitar en `default` y arranque opcional.

## 4) Pruebas previstas
- `sh -n` del script.
- Revisión de ayuda y validación de parámetros obligatorios.

## 5) Ejecución
Completada.

- Nuevo script: `kiosks_report/alpine/install_heartbeat_alpine.sh`
  - Instalación no interactiva con flags.
  - Requiere `--primary-url`.
  - Soporta fallback y tuning de tiempos/reintentos.
  - Instala script en `/opt/monitoring/report_status.sh`.
  - Genera config en `/etc/kiosk/heartbeat.conf`.
  - Instala servicio OpenRC en `/etc/init.d/kioskmonitoring`.
  - Habilita/arranca servicio (desactivable con `--no-enable` / `--no-start`).

- Documentación actualizada:
  - `kiosks_report/alpine/README.md`

## 6) Validación
- `sh -n kiosks_report/alpine/install_heartbeat_alpine.sh` OK.
- `bash -n kiosks_report/alpine/install_heartbeat_alpine.sh` OK.
- `--help` mostrado correctamente.
