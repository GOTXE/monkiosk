# Plan de cambio: monitorización horizontal

Fecha: 2026-02-18  
Rama: `develope_monitorizacion`

## 1) Objetivo
Mejorar la web de monitorización manteniendo simplicidad operativa, aplicando:
- Estado más claro por tiempo (`hace X s`) y semáforo por tramos.
- Historial corto por equipo.
- Detección de equipos inestables.
- Resumen global arriba.
- Limpieza automática de equipos inactivos.
- Presentación horizontal de tarjetas.

## 2) Alcance
Archivos a modificar:
- `kiosk_status/update_status.php`
- `kiosk_status/index.html`
- `kiosk_status/styles.css`
- `kiosk_status/status.json` (estructura de datos, manteniendo compatibilidad)

## 3) Diseño funcional (simple)
### 3.1 Estado por tiempo
- Online: `< 60s`
- Advertencia: `60s - 150s`
- Offline: `> 150s`
- Mostrar: fecha local + texto relativo (`hace 23 s`).

### 3.2 Historial mínimo
- Guardar en cada host: `history` (array máx. 5 eventos).
- Evento: `{ts, status}`.
- Añadir evento solo si cambia el estado lógico (online/offline).

### 3.3 Inestabilidad
- Marcar `unstable=true` si hay >=3 cambios online/offline en ventana de 10 minutos.

### 3.4 Resumen superior
- Contadores: `Total`, `Online`, `Offline`, `Inestables`.

### 3.5 Limpieza automática
- Si `last_updated > 7 días`, mover a sección "inactivos" o eliminar del `status.json`.
- Opción elegida: eliminar para mantener simplicidad.

### 3.6 Layout horizontal
- Tarjetas en fila horizontal con `overflow-x` y responsive.
- Cada tarjeta muestra: nombre, estado, relativo, timestamp, badge inestable, botón historial.

## 4) Riesgos y mitigaciones
- Riesgo: romper clientes con `status.json` antiguo.
- Mitigación: fallback en frontend cuando no exista `history` o `unstable`.

- Riesgo: crecimiento de archivo JSON.
- Mitigación: historial máximo 5 y limpieza >7 días.

## 5) Plan de ejecución
1. Actualizar backend (`update_status.php`) para metadatos nuevos.
2. Ajustar frontend (`index.html`) para resumen, relativo e historial.
3. Aplicar estilo horizontal en `styles.css`.
4. Probar con `curl` y simulación de tiempos/estados.
5. Verificación visual final en navegador.

## 6) Pruebas previstas
- POST heartbeat normal y comprobar actualización.
- Simular offline editando timestamps y validar color/estado.
- Simular flapping (cambios rápidos) y validar badge inestable.
- Confirmar limpieza de registros >7 días.
- Confirmar scroll horizontal en desktop y móvil.

## 7) Ejecución
### 7.1 Cambios realizados
1. `kiosk_status/update_status.php`
- Añadido `history` por host (max 5 eventos con formato `{ts, state}`).
- Añadido cálculo de `unstable` (>=3 transiciones en 10 minutos).
- Añadida limpieza automática de hosts con más de 7 días sin latido.
- Mantenida compatibilidad con estructuras antiguas de `status.json`.

2. `kiosk_status/index.html`
- Refactor de renderizado completo para:
  - resumen superior (`Total`, `Online`, `Offline`, `Inestables`);
  - estado por tramos (`Online`, `Advertencia`, `Offline`);
  - tiempo relativo (`hace X s/min`);
  - historial desplegable por host;
  - badge `Inestable`.

3. `kiosk_status/styles.css`
- Reescritura con layout horizontal (`kiosk-strip` con scroll X).
- Tarjetas compactas con semáforo por borde izquierdo.
- Ajustes responsive para móvil.

### 7.2 Pruebas ejecutadas
- `php -l kiosk_status/update_status.php` OK.
- `bash -n kiosks_report/report_status.sh` OK.
- Revisión manual estática de `index.html` y `styles.css` completada.

### 7.3 Limitaciones de prueba en este entorno
- El entorno de ejecución no permite abrir puertos locales (`php -S ...` falla al bind), por lo que no se pudo hacer prueba HTTP end-to-end dentro de esta sesión.
- Queda pendiente validación visual en navegador del servidor instalado.
