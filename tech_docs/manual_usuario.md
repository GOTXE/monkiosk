# Manual de usuario

## Objetivo

Este manual esta pensado para personas no tecnicas que necesitan usar `estado_quioscos` en el dia a dia.

Permite:

- ver el estado general de los quioscos
- revisar informacion basica de cada equipo
- reiniciar un quiosco desde la web
- gestionar documentos
- autorizar nuevos quioscos

No incluye rutas internas, credenciales ni tareas de instalacion.

## 1. Acceso a la aplicacion

1. Abre la web `estado_quioscos`.
2. Inicia sesion con tu usuario.
3. Veras:
   - resumen general
   - panel del servidor
   - lista de quioscos

## 2. Significado de los estados

- `Online`: el quiosco ha reportado hace poco.
- `Advertencia`: lleva un tiempo sin reportar, pero aun no se considera caido.
- `Offline`: no hay comunicacion reciente.
- `Inestable`: el quiosco ha pasado varias veces entre online y offline en poco tiempo. Suele indicar cortes de red, reinicios o fallos intermitentes.
- `Reiniciando`: se ha enviado una orden de reinicio y esta pendiente de completarse.

## 3. Como revisar un quiosco

Pulsa `Informacion` en la tarjeta del quiosco.

Podras ver:

- IP reportada
- tiempo encendido
- carga
- RAM libre
- disco libre
- estado HDMI

Si quieres abrir o cerrar todas las tarjetas de `Equipos` de una vez, usa el boton general de esa seccion.

## 4. Como reiniciar un quiosco

1. Abre `Informacion`.
2. Pulsa `Reiniciar`.
3. Confirma la accion.

La orden se queda preparada para que el quiosco la recoja en su siguiente comunicacion.

## 5. Gestion de documentos

En `Gestión diapositivas` puedes:

- subir documentos
- revisar una previsualizacion
- borrar o sustituir archivos

Recomendaciones:

- usa nombres simples
- el nombre debe empezar por numero
- si el nombre es invalido, la web indica el motivo y como corregirlo
- revisa el resultado antes de cerrar
- evita hacer cambios simultaneos desde varias sesiones

## 6. Quioscos permitidos

En `Quioscos permitidos` puedes decidir que equipos pueden conectar al servidor.

Uso habitual:

1. revisa los equipos detectados
2. marca `Permitido` en los que deban conectar
3. rellena `IP fija` solo si quieres exigir una IP concreta
4. pulsa `Guardar cambios`
5. escribe `GUARDAR` en mayusculas y confirma

Notas:

- si anades un equipo y no has guardado, aparece un aviso dorado parpadeante
- `Intentos de conexion` se actualiza solo cada 15 segundos
- al pulsar `Añadir`, el equipo pasa a la tabla principal

## 7. Proteccion de acceso

- `Proteccion activada`: solo conectan los quioscos permitidos
- `Proteccion desactivada`: cualquier quiosco puede conectar temporalmente

Con proteccion activada:

- el reporte del quiosco queda limitado a equipos permitidos
- la presentacion del quiosco tambien queda limitada a `IP fija` autorizada

Uso recomendado:

1. desactiva la proteccion si vas a dar de alta un quiosco nuevo
2. espera a que aparezca en `Intentos de conexion`
3. anadelo a la tabla
4. marca `Permitido`
5. guarda
6. vuelve a activar la proteccion

## 8. Aviso de certificado

En la tarjeta `SERVIDOR` puede aparecer un aviso rojo parpadeante si al certificado HTTPS le quedan `30 dias o menos` para caducar.

Si aparece, debes avisar a soporte tecnico para renovarlo.

## 9. Documentacion

La opcion `Documentación` del menu usa el mismo login que `estado_quioscos`.

No hace falta volver a escribir usuario y contrasena si ya tienes sesion iniciada.

## 10. Recomendaciones

- no compartas capturas con datos de red visibles
- si un quiosco aparece `Inestable`, revisa si tiene cortes o reinicios
- despues de cambios importantes, vuelve a comprobar la pantalla principal

## 11. Recuperacion desde backup

Si se ha preparado un backup del sistema, la recuperacion debe hacerse siguiendo el asistente del script.

Pasos:

1. abre una terminal en el servidor
2. entra en la carpeta `tools` del proyecto
3. ejecuta:
   - `sudo ./restore_monkiosk.sh`
4. el script mostrara la lista de backups disponibles
5. escribe solo el numero del backup que quieres restaurar
6. el script mostrara un resumen de lo que va a recuperar
7. si estas seguro, escribe `RESTAURAR`
8. espera a que termine
9. cuando finalice, comprueba:
   - la web principal del quiosco
   - `estado_quioscos`
   - que las paginas cargan con normalidad

Recomendaciones:

- no cierres la terminal mientras se ejecuta
- si tienes dudas, usa primero:
  - `sudo ./restore_monkiosk.sh --dry-run`
- no hace falta indicar rutas ni archivos si sigues el asistente
