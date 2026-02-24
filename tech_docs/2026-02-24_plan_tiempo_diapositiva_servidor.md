# Plan y Ejecucion - Tiempo de diapositiva configurable

## Objetivo
Permitir que el operador cambie el tiempo de diapositiva del quiosco desde `estado_quioscos/index.html`, dentro de `SERVIDOR > Informacion`, en pasos de 5 segundos.

## Plan
1. Exponer el valor actual en backend de servidor.
2. Crear endpoint para leer/guardar el tiempo de diapositiva.
3. Añadir selector tipo barrilete en la web de gestion.
4. Aplicar el valor guardado en `kiosk_web/index.php`.
5. Desplegar y validar sintaxis.

## Ejecucion realizada
1. `estado_quioscos/server_status.php`
- Añadido `slide_interval_seconds` leyendo `slide_settings.json`.

2. `estado_quioscos/slide_settings.php`
- Nuevo endpoint:
  - `GET`: devuelve tiempo actual.
  - `POST`: guarda `slide_interval_seconds` normalizado (min 5, saltos de 5).

3. `estado_quioscos/index.html` y `estado_quioscos/styles.css`
- Nuevo control en `SERVIDOR > Informacion`:
  - `Tiempo de diapositiva: [ input number ] s`
  - Boton `Guardar`.
- Guardado via `fetch('slide_settings.php')`.

4. `kiosk_web/index.php`
- Sustituido intervalo fijo `5000` por lectura de configuracion desde `../estado_quioscos/slide_settings.json`.
- Se aplica en milisegundos al reproductor.

5. Soporte de configuracion
- Añadido `estado_quioscos/slide_settings.json` con valor inicial.

## Ajuste posterior (bug UX)
- Problema detectado: el selector se reseteaba cada 5 segundos por el refresco automatico de estado de servidor.
- Correccion aplicada en `estado_quioscos/index.html`:
  - estado local `slideIntervalDirty` y `pendingSlideIntervalSeconds`
  - mientras el operador edita, el valor no se pisa con el refresco
  - al guardar, se limpia estado pendiente y se sincroniza con backend

## Ajuste posterior (aplicacion en quiosco en caliente)
- Problema detectado: el tiempo cambiado quedaba guardado, pero el quiosco podia seguir con intervalo anterior hasta recargar pagina.
- Correccion aplicada en `kiosk_web/index.php`:
  - polling cada 15 segundos a `slide_settings.php`
  - actualizacion dinamica de `intervalo` en memoria sin recarga
  - limite mantenido: 5..120 segundos, saltos de 5
