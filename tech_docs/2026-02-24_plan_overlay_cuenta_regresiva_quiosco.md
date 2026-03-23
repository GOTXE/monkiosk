# Plan y Ejecucion - Overlay de cuenta regresiva por quiosco

## Objetivo
Permitir activar/desactivar una cuenta regresiva visible en un unico quiosco elegido desde `estado_quioscos`, para validar si el tiempo de diapositiva es suficiente.

## Plan
1. Crear estado persistente de overlay (activo + quiosco objetivo).
2. Crear endpoint para consultar/actualizar ese estado desde la web de gestion.
3. Extender `estado_quioscos/index.html` con selector de quiosco y botones:
   - Mostrar tiempo
   - Desactivar tiempo
4. Mostrar en rojo el quiosco objetivo cuando el overlay este activo.
5. Hacer que `kiosk_web/index.php` consulte el estado y, si corresponde, muestre cuenta regresiva grande en esquina inferior derecha.

## Criterios
- Mantener simplicidad operativa para no tecnicos.
- Estado persistente tras recarga de pagina.
- Sin dependencias externas.

## Ejecucion realizada
1. Backend de estado/control
- `estado_quioscos/overlay_config.json`: estado persistente inicial.
- `estado_quioscos/overlay_state.php`: lectura de estado + resolucion de quiosco cliente por `REMOTE_ADDR` contra `status.json`.
- `estado_quioscos/overlay_control.php`: activar/desactivar overlay en un quiosco objetivo.

2. Web de gestion (`estado_quioscos/index.html` + `styles.css`)
- En `SERVIDOR > Informacion` se anadio:
  - selector de quiosco ordenado por nombre.
  - boton `Mostrar tiempo`.
  - boton `Desactivar tiempo`.
- Si esta activo, se muestra en rojo: `Mostrando tiempo en: QUIOSCO...`.
- Integrado con polling existente para mantener estado al recargar.

3. Quiosco (`kiosk_web/index.php`)
- Overlay visual grande en esquina inferior derecha (`#countdownOverlay`).
- Polling a `overlay_state.php` para saber si este quiosco debe mostrar contador.
- Cuenta regresiva por diapositiva en imagen/PDF.
- En video se oculta el contador.

## Ajuste posterior (matching de red)
- Problema detectado: en algunos quioscos `local_ip` reportada no coincide con la IP origen real usada para abrir la web.
- Correccion:
  - `update_status.php` guarda `source_ip` (IP de `REMOTE_ADDR` del heartbeat).
  - `overlay_state.php` intenta match en este orden: `source_ip`, `ip`, `local_ip`.
