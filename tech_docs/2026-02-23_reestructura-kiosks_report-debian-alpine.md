# Plan de cambio: reestructurar kiosks_report por plataforma

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Separar claramente archivos de Debian y Alpine dentro de `kiosks_report/`.

## 2) Alcance
- Mover:
  - `kiosks_report/report_status.sh` -> `kiosks_report/debian/report_status.sh`
  - `kiosks_report/kioskmonitoring.service` -> `kiosks_report/debian/kioskmonitoring.service`
- Actualizar rutas en documentación operativa.

## 3) Plan
1. Crear carpeta `kiosks_report/debian`.
2. Mover archivos.
3. Actualizar referencias en `README.md`, `AGENTS.md` y docs Alpine.
4. Validar sintaxis shell y estado git.

## 4) Ejecución
Completada.

- Estructura aplicada:
  - `kiosks_report/debian/report_status.sh`
  - `kiosks_report/debian/kioskmonitoring.service`
  - `kiosks_report/alpine/*` (ya existente y mantenido)
- Referencias actualizadas:
  - `AGENTS.md`
  - `README.md` (rutas operativas)
  - `kiosks_report/alpine/README.md`
  - `tech_docs/2026-02-23_monitorizacion-alpine-heartbeat.md`
- Validación:
  - `bash -n kiosks_report/debian/report_status.sh` OK
  - `sh -n kiosks_report/debian/report_status.sh` OK
