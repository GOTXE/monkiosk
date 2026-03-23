# Plan de cambio: silenciar timeout esperado y diagnóstico de visibilidad

Fecha: 2026-02-23  
Rama: `develope_monitorizacion`

## 1) Objetivo
Evitar ruido en `kioskmonitoring.err` cuando el primary falla y el fallback funciona.

## 2) Alcance
- `kiosks_report/debian/report_status.sh`
- `Quioscos_install_alpine/report/report_status.sh`
- copias desplegadas en `/opt/monitoring`

## 3) Plan
1. Cambiar `curl -sS` por `curl -s` en envío de heartbeat.
2. Validar sintaxis shell.
3. Sincronizar scripts desplegados.

## 4) Ejecución
Completada.

- Cambio aplicado: `curl -sS -o` -> `curl -s -o`.
- Validación: `sh -n` OK en ambos scripts.
- Despliegue sincronizado:
  - `/opt/monitoring/report_status.sh`
  - `/opt/monitoring/Quioscos_install_alpine/report/report_status.sh`
