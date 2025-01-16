# Documentación del Sistema de Presentación y Monitorización de Quioscos

## Índice

- Idea principal
- Descripción General
- Estructura del Proyecto
- Instalación
- Configuración
- Archivos y Directorios
- Funcionamiento
- Personalización
- Licencia

## Idea principal

Este proyecto :floppy_disk: nace de una necesidad muy especial, la de `un amigo` que quería mostrar información en ciertos monitores. Los requisitos son claros y casi ~~patét~~ poéticos: `solo necesito algo extremadamente sencillo, de coste cercano a 0, fácil de usar y mantener, además se que a tí te gusta eso de los cacharros y así te entretienes`. :zipper_mouth_face:

No esperes encontrar aquí código digno de admiración o soluciones dignas de SpaceX. De hecho, lo único que compartimos con ellos es que seguimos vivos y somos capaces de hacer cosas con un presupuesto que en nuestro caso roza el cero absoluto :money_with_wings:.

Entonces, ¿por qué documentar todo esto? Muy sencillo: porque el `gotxe` y el `panoramix` del futuro no querrán recordar cómo funcionaba esto y, cuando ese amigo **tenga un problema** :boom: (**porque lo tendrá**), nos llamará :telephone_receiver: preguntando cómo reinstalarlo . Pero esa vez no se lo vamos a reinstalar porque estos `dos` del futuro seguro estarán a otras cosas y quien sabe si en otro lugar (vivos), así que más le vale leer esto 😅.

## Descripción General

Este proyecto es un sistema de presentación y monitorización de quioscos basado en web. Hay una pagina web `index.php`que pone a disposición de los quioscos los documentos a presentar y un sistema de monitorización de los quioscos `update_status.php` e `index.html`, que combina un script `Bash` que verifica la conectividad de la red de los dispositivos y una interfaz web que los presenta visualmente su estado en tiempo real.

### Características Clave

- Servidor quiosco que muestra documentos en un ciclo continuo.
- Monitorización del estado de múltiples quioscos y un servidor local.
- Muestra los resultados en una interfaz web dinámica utilizando HTML, CSS y JavaScript.
- Actualiza el estado de los dispositivos cada 60 segundos sin necesidad de recargar toda la página.
- Proporciona indicadores de estado en tiempo real (online/offline) con fecha_hora para cada quiosco.

## Estructura del Proyecto

La estructura del proyecto es la siguiente:

monkiosk/
├── kiosk_web/   
│   └── styles.css      
│   ├── img/            
│   │   └── ...svg
│   ├── docs/        
│   │   └── ...jpg
│   ├── index.php 
│   ├── update_status.php
│   ├── status.json
│   ├── allowed_hosts.txt
│   ├── index.html

Quioscos/
├── kiosk_report/
│   ├── kioskmonitoring.service
│   ├── report_status.sh


## Instalación del Servidor

### Requisitos Previos

- Servidor: El Linux que tu quieras con Nginx, php y caffeine.

### Pasos de Instalación

1. **Clonar el Repositorio**:
    ```bash
    git clone https://github.com/GOTXE/network-monitoring.git
    ```
    puede ser que no te tengas git (lo más probable), pues utiliza un navegador o si eres más de utilizar terminal usa wget
    ```bash
     wget https://github.com/GOTXE/monkiosk/archive/refs/heads/main.zip
    ```
    Después repartes cada cosa en su sitio según está la estructura del proyecto (:eye::eye:) y lo que te contaré más adelante.

2. **Instalar Paquetes Requeridos en el Servidor**:
    ```bash
    sudo apt update
    sudo apt install nginx curl php-fpm
    ```

3. **Configurar la Interfaz Web**:
    - Copiar el contenido de la carpeta `kiosk_web` al directorio `/var/www/html/<tu_nombre_favorito>`
    - Asegurarse de que Nginx esté configurado para servir el archivo `index.php`, `index.html` y `update_status.php`.

4. **Configurar Nginx**
        P E N D I E N T E

6. **Configurar IPs**:
    En el archivo `allowed_hosts.txt`, agregar los nombres correspondientes para cada quiosco en el siguiente formato:
    ```
    Kiosk1
    Kiosk2
    ```

## Configuración en los Quioscos

### Configuración del Servicio de Monitorización

Para asegurarse de que el script de monitorización se ejecute como un servicio en segundo plano y se reinicie automáticamente si falla, se debe crear una unidad de servicio `systemd`.

1. **Clonar el Repositorio**:
    ```bash
    git clone https://github.com/GOTXE/network-monitoring.git
    ```
    puede ser que no te tengas git (lo más probable), pues utiliza un navegador o si eres más de utilizar terminal usa wget
    ```bash
     wget https://github.com/GOTXE/monkiosk/archive/refs/heads/main.zip
    ```
    Después repartes cada cosa en su sitio según está la estructura del proyecto (:eye::eye:) y lo que te contaré más adelante.

. **Configurar el Script de Monitorización**:
    - Colocar [report_status.sh](http://_vscodecontentref_/3) en `/opt/monitoring/report_status.sh`.
    - Configurar un cron job o un servicio systemd para ejecutar `report_status.sh` cada minuto.

1. **Crear el archivo de servicio**:
    ```bash
    sudo nano /etc/systemd/system/kioskmonitoring.service
    ```

    Contenido del archivo:
    ```service
    [Unit]
    Description=Monitorización de Quioscos
    After=network.target

    [Service]
    ExecStart=/opt/monitoring/report_status.sh  # Ruta a tu script Bash
    Restart=always  # Para asegurarse de que se reinicie en caso de fallo
    RestartSec=10    # Espera 10 segundos antes de reiniciar si se cierra

    [Install]
    WantedBy=multi-user.target
    ```

2. **Recargar systemd**:
    ```bash
    sudo systemctl daemon-reload
    ```

3. **Habilitar el servicio para que inicie al arrancar**:
    ```bash
    sudo systemctl enable kioskmonitoring.service
    ```

4. **Iniciar el servicio**:
    ```bash
    sudo systemctl start kioskmonitoring.service
    ```

5. **Verificar el estado del servicio**:
    ```bash
    sudo systemctl status kioskmonitoring.service
    ```

## Archivos y Directorios

### [styles.css](http://_vscodecontentref_/4)

Archivo CSS que contiene los estilos para la interfaz web. Define estilos para el cuerpo, contenedores, quioscos, y estados online/offline.

### [allowed_hosts.txt](http://_vscodecontentref_/5)

Archivo de texto que contiene la lista de hosts permitidos. Cada línea debe contener una IP y un nombre de quiosco.

### [index.html](http://_vscodecontentref_/6)

Archivo HTML principal que muestra el estado de los quioscos. Incluye un script JavaScript para actualizar el estado de los quioscos en tiempo real.

### [index.php](http://_vscodecontentref_/7)

Archivo PHP que muestra documentos en un iframe. Los documentos se obtienen de un directorio específico y se muestran en un ciclo.

### [status.json](http://_vscodecontentref_/8)

Archivo JSON que contiene el estado actual de los quioscos. Este archivo es actualizado por el script `update_status.php`.

### [update_status.php](http://_vscodecontentref_/9)

Script PHP que recibe solicitudes POST para actualizar el estado de los quioscos. Valida los datos recibidos y actualiza el archivo `status.json`.

### [kioskmonitoring.service](http://_vscodecontentref_/10)

Archivo de configuración de `systemd` para ejecutar el script de monitorización como un servicio.

### [report_status.sh](http://_vscodecontentref_/11)

Script Bash que envía el estado del quiosco al servidor. Se ejecuta en un bucle infinito y reintenta en caso de fallo.

### [.gitignore](http://_vscodecontentref_/12)

Archivo que especifica qué archivos y directorios deben ser ignorados por Git.

### [README.md](http://_vscodecontentref_/13)

Archivo README que proporciona una visión general del proyecto, incluyendo la instalación y configuración.

### [estructura.md](http://_vscodecontentref_/14)

Archivo que describe la estructura del proyecto.

### [test.txt](http://_vscodecontentref_/15)

Archivo de prueba que contiene un comando `curl` para enviar una solicitud POST al servidor.

## Funcionamiento

### Script de Monitorización (`report_status.sh`)

Este script verifica continuamente la disponibilidad del servidor enviando una solicitud HTTP a `http://<IP_DEL_SERVIDOR>/update_status.php`. También obtiene el nombre del quiosco usando el comando `hostname` y envía su estado al servidor.

### Interfaz Web

La página web, construida con HTML, CSS y JavaScript, lee el archivo `status.json` cada 5 segundos para actualizar el estado de los quioscos y el servidor sin necesidad de recargar la página. Los quioscos se representan visualmente con íconos codificados por colores (verde para online, rojo para offline).

### Servidor Quiosco (`index.php`)

El archivo `index.php` está diseñado para mostrar documentos en un ciclo continuo. Los documentos se obtienen de un directorio específico /dosc y para mostrarlos en los quisocos.

### Personalización
Intervalo de Actualización
Puedes cambiar el intervalo de actualización en el script Bash (report_status.sh) modificando la variable interval.

### Iconos de Quioscos
Para personalizar los íconos utilizados para los quioscos, reemplaza el archivo quiosco.svg en el directorio img/ con tu imagen preferida.

### Intervalo de Cambio de Documentos
Para cambiar el intervalo de tiempo entre documentos en el servidor quiosco, modifica el valor en milisegundos en la función setInterval en el archivo index.php.

#### Paciencia
¿ Pero has llegado hasta aquí ? Te veo diciendo ¡como puede ser que a esta (:shit:) le pongan licencia!. Pues por darle ese toque :nerd_face: a un pedazo de código que casi podría ser un :bug: error de programación :bug:.
Si no tienes ni idea de programación este es tu sitio! :vulcan_salute:

Oye que igual nos calentamos :fire:, se nos pone el morro fino :lips: y nos ponemos con una versión 2 :rocket:... 

#### Licencia
Llegados a este punto, este proyecto es de código abierto y está disponible bajo la licencia MIT. Siéntete libre de usarlo y modificarlo según tus necesidades.

Agradezco las imagenes usadas que son de https://www.svgrepo.com