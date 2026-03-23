# Plan de cambio: renombrar kiosk_status a estado_quioscos

Fecha: 2026-02-18  
Rama: `develope_monitorizacion`

## 1) Objetivo
Cambiar la URL y módulo de monitorización de `kiosk_status` a `estado_quioscos`.

## 2) Alcance
- Renombrar carpeta en repo.
- Actualizar referencias internas y documentación.
- Desplegar en `/var/www/html/estado_quioscos`.

## 3) Plan
1. Detectar referencias a `kiosk_status`.
2. Renombrar carpeta en repo y ajustar rutas.
3. Copiar despliegue a nueva ruta web.
4. Verificar archivos y dejar backup.

## 4) Ejecución
Completada.

- Carpeta renombrada en repo:
  - `kiosk_status/` -> `estado_quioscos/`
- Referencias funcionales actualizadas:
  - `kiosks_report/report_status.sh` ahora usa `.../estado_quioscos/update_status.php`
  - `AGENTS.md` actualizado a la nueva ruta de módulo.
- Despliegue realizado:
  - Nuevo destino web: `/var/www/html/estado_quioscos`
  - Permisos ajustados para `www-data`.
- Verificación:
  - `php -l estado_quioscos/update_status.php` OK.
  - Hash de `index.html`, `styles.css`, `update_status.php` idéntico entre repo y despliegue.
