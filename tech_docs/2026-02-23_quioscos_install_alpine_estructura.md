# Plan de cambio: estructura Quioscos_install_alpine

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Adaptar instalacion Alpine a estructura:
- `Quioscos_install_alpine/install_report.sh`
- `Quioscos_install_alpine/report/*`

## 2) Alcance
- Crear carpeta `Quioscos_install_alpine/`.
- Añadir instalador raiz no interactivo.
- Añadir carpeta `report/` con archivos necesarios.
- Resolver rutas desde directorio del script.

## 3) Plan
1. Copiar `report_status.sh`, `kioskmonitoring.openrc`, `heartbeat.conf.example` a `Quioscos_install_alpine/report/`.
2. Crear `Quioscos_install_alpine/install_report.sh`.
3. Validar sintaxis (`sh -n`) y ayuda.

## 4) Ejecución
Completada.

- Estructura creada:
  - `Quioscos_install_alpine/install_report.sh`
  - `Quioscos_install_alpine/report/report_status.sh`
  - `Quioscos_install_alpine/report/kioskmonitoring.openrc`
  - `Quioscos_install_alpine/report/heartbeat.conf.example`

- Detalles clave:
  - `install_report.sh` resuelve rutas desde su propia ubicación (`BASE_DIR`) y no depende del directorio de ejecución.
  - Instalación no interactiva vía flags, con `--primary-url` obligatorio.

- Validación:
  - `sh -n Quioscos_install_alpine/install_report.sh` OK.
  - `bash -n Quioscos_install_alpine/install_report.sh` OK.
  - `--help` operativo.
