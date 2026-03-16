# Guia de uso de la aplicacion

## Objetivo

Esta guia resume el uso habitual de la solucion:

- visualizacion del estado de los quioscos desde `estado_quioscos`
- acceso operativo a un quiosco para revision o soporte
- gestion de documentos y ajustes basicos del sistema

No incluye datos privados, credenciales ni direcciones reales.

## 1. Acceso a `estado_quioscos`

1. Abre en el navegador la URL publica o interna asignada a `estado_quioscos`.
2. Inicia sesion con un usuario autorizado.
3. Tras entrar veras:
   - resumen superior con total, online, offline e inestables
   - panel del servidor
   - tarjetas de los quioscos

Si la sesion no es valida, la aplicacion redirige a la pantalla de login.

## 2. Como revisar un quiosco

Cada quiosco aparece como una tarjeta con su estado:

- `Online`: latido reciente
- `Advertencia`: sin latido reciente, pero aun dentro del margen
- `Offline`: sin comunicacion
- `Inestable`: ha tenido cambios frecuentes de estado

Pulsa `Informacion` en una tarjeta para ver:

- IP reportada
- tiempo encendido
- carga
- RAM libre
- disco libre
- estado HDMI

## 3. Como conectar a un quiosco

La aplicacion no guarda claves ni abre sesiones remotas por si sola. El flujo recomendado es:

1. Identificar el quiosco en `estado_quioscos`.
2. Abrir `Informacion` y anotar la IP o los datos utiles de red.
3. Conectarte con el metodo autorizado en tu entorno:
   - navegador web si el quiosco publica una URL propia
   - acceso remoto de sistemas
   - acceso local en pantalla y teclado

Si el equipo no responde pero sigue apareciendo, revisa primero su ultimo latido y el estado HDMI.

## 4. Reinicio de un quiosco

Desde la tarjeta del quiosco:

1. Abre `Informacion`.
2. Pulsa `Reiniciar`.
3. Confirma la accion.

La orden se encola para que el quiosco la recoja en su siguiente ciclo de comunicacion.

## 5. Gestion de documentos

Desde el menu superior:

1. Abre `Gestion de documentos`.
2. Sube archivos permitidos.
3. Previsualiza el contenido antes de dejarlo publicado.
4. Borra o sustituye archivos cuando sea necesario.

Reglas practicas:

- usa nombres simples, sin espacios ni caracteres raros
- revisa el orden y contenido publicado antes de cerrar
- si sustituyes un archivo, confirma que la version visible es la correcta

## 6. Otras acciones de gestion

Desde el menu o panel principal puedes:

- cambiar la contrasena del usuario gestor
- cerrar sesion
- ajustar el tiempo de diapositiva
- activar o desactivar la cuenta regresiva en un quiosco
- ejecutar acciones sobre el servidor, si tu perfil y el entorno lo permiten

## 7. Gestion de quioscos permitidos

Desde el menu superior:

1. Abre `Quioscos permitidos`.
2. Revisa la lista de equipos ya conocidos.
3. Marca `Permitido` en los quioscos que deban poder conectar.
4. Si una IP debe ser fija, escríbela en su fila.
5. Pulsa `Guardar cambios`.
6. En el modal, escribe `GUARDAR` y confirma.

Notas practicas:

- `GUARDAR` debe escribirse exactamente en mayúsculas
- el `hostname` se normaliza a minusculas
- si no existe todavia una lista guardada, los quioscos ya detectados aparecen marcados por defecto
- puedes dejar un equipo en la lista pero sin `Permitido` para conservarlo sin autorizarlo
- `IP fija` es opcional, pero si la rellenas debe ser una IPv4 valida
- si borras una `IP fija` y guardas, el campo queda realmente vacio
- el campo `IP fija` solo admite numeros y puntos
- si la IP es incorrecta, el campo se marca en rojo y no deja guardar
- los campos con valor se muestran en negrita y los textos de ejemplo aparecen en cursiva
- si anades un quiosco a la tabla y aun no has guardado, aparece un aviso dorado parpadeante para recordar que falta aplicar los cambios

## 8. Proteccion de acceso de quioscos

La pantalla `Quioscos permitidos` tiene un interruptor general:

- `Proteccion activada`: solo aceptan conexion los quioscos permitidos
- `Proteccion desactivada`: cualquier quiosco puede conectar temporalmente

Uso recomendado:

1. Desactiva la proteccion si vas a dar de alta equipos nuevos.
2. Espera a que aparezcan en `Intentos de conexion` o en la lista detectada.
3. Pulsa `Añadir` si procede.
4. Revisa hostname e IP.
5. Marca `Permitido`.
6. Guarda cambios.
7. Vuelve a activar la proteccion.

## 9. Intentos de conexion

Cuando la proteccion esta activada, los equipos no autorizados no entran en servicio, pero quedan registrados en `Intentos de conexion`.

Desde ahi puedes:

- ver `hostname`
- ver la IP reportada
- ver cuantos intentos ha hecho
- añadirlo rapidamente a la tabla principal; al hacerlo desaparece de `Intentos de conexion` y queda solo en `Quioscos permitidos`
- la lista se actualiza sola cada 15 segundos sin recargar toda la pagina
- si un equipo ya fue añadido a la tabla, no vuelve a salir en `Intentos de conexion` mientras siga en esa tabla

## 10. Recomendaciones de uso

- no compartas capturas con datos de red visibles
- evita hacer cambios simultaneos desde varias sesiones
- tras cada cambio relevante, comprueba la vista principal y el comportamiento del quiosco afectado

## 11. Aviso de certificado HTTPS

En la tarjeta `SERVIDOR`, la aplicacion muestra un aviso rojo parpadeante cuando al certificado HTTPS del servidor le quedan `30 dias o menos` para caducar.

Notas practicas:

- el aviso sigue visible aunque el bloque `Informacion` este contraido
- el calculo del certificado se reutiliza durante `24 horas`
- si no aparece ningun aviso, el certificado todavia no ha entrado en la ventana de alerta
