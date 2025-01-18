# Documentación del Sistema de Quioscos y Monitorización

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

No esperes encontrar aquí código digno de admiración o soluciones dignas de SpaceX. De hecho, lo único que compartimos con ellos es que estamos vivos y somos capaces de hacer cosas con un presupuesto que en nuestro caso roza casi el cero absoluto :money_with_wings:.

Entonces, ¿por qué documentar todo esto? Muy sencillo: porque el `gotxe` y el `panoramix` del futuro no querrán recordar cómo funcionaba esto y, cuando ese amigo **tenga un problema** :boom: (**porque lo tendrá**), nos llamará :telephone_receiver: preguntando cómo reinstalarlo . 
Pero esa vez no se lo vamos a reinstalar porque los `dos` del futuro, que fueron los que lo hcicieron, seguro estarán a otras cosas y quien sabe si en otro lugar (vivos), así que más le vale leer esto 😅.

## Descripción General

Este proyecto es un sistema de presentación y monitorización de quioscos basado en web. Hay una pagina web `index.php`que pone a disposición de los quioscos los documentos a presentar y un sistema de monitorización de los quioscos `update_status.php` e `index.html`, que combina un script `Bash` que verifica la conectividad de la red de los quioscos y una interfaz web que presenta visualmente su estado en tiempo real.

### Características Clave

- Servidor quiosco que muestra documentos en un ciclo continuo.
- Monitorización del estado de múltiples quioscos, servidor y puerta de enlace.
- Muestra los resultados en una interfaz web dinámica utilizando HTML, CSS y JavaScript.
- Actualiza el estado de los dispositivos cada 60 segundos sin necesidad de recargar toda la página.
- Proporciona indicadores de estado en tiempo real (online/offline) con fecha_hora para cada elemento.

## Estructura del Proyecto

La estructura y los permisos del proyecto es la siguiente:

monkiosk/
├── kiosk_web/              [750]
│   ├── styles.css          [644]
│   ├── img/                [755]
│   │   └── ...svg
│   ├── docs/               [755]
│   │   └── ...jpg
│   ├── index.php           [640]
│   ├── update_status.php   [640]
│   ├── status.json         [640]
│   ├── allowed_hosts.txt   [600]
│   ├── index.html          [644]

Quioscos/
├── kiosk_report/           [750]
│   ├── kioskmonitoring.service [644]
│   ├── report_status.sh    [750]
├── kiosk_/                 [750]
│   ├── autostart           [644]



## Instalación del Servidor

### Requisitos Previos

- Servidor: El Linux que tu quieras con Nginx, php y caffeine, :coffee: de este también! En este caso es debian.
 [![debian](https://img.shields.io/badge/DEBIAN-d70a53)](https://www.debian.org/distrib/)   [![NGINX](https://img.shields.io/badge/NGINX-8A2BE2)](https://nginx.org/en/docs/http/ngx_http_index_module.html)    [![PHP](https://img.shields.io/badge/PHP-4D5D8C)](https://www.php.net/) [![CAFFEINE](https://img.shields.io/badge/CAFFEINE-a18262)](https://duckduckgo.com/?t=h_&q=caffeine+linux+&ia=web)

### Pasos de Instalación

1. **Descarga los archivos del Repositorio**:
    Utiliza un navegador o si prefieres desde terminal con `wget`
    ```bash
     wget https://github.com/GOTXE/monkiosk/archive/refs/heads/main.zip
    ```
    Para el servidor solo te hace falta la carpeta `kiosk_web`.

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

5. **Configurar hostnames**:
    En el archivo `allowed_hosts.txt`, agregar los nombres correspondientes para cada quiosco en el siguiente formato:
    
    ```
    Kiosk1
    Kiosk2
    ```
6. **Modificaciones en los archivos**
    En el archivo `index.php`, hay una línea `var intervalo = 5000;` en la que tienes que establecer el tiempo que quieres que se presente cada diapositiva (está en ms).

    En el archivo `index.html` hay una línea `const timeout = 150;` en la que puedes determinar si un equipo está offline (está en sg).

# Configuración de los Quioscos

## Instalación de Kioscos (en Xubuntu 24.04)

Esta implementación se basa en una imagen limpia y minimalista de Xubuntu 24.04, donde se crea un único usuario, `kiosco`.

### Pasos de Instalación

#### 1. Establecer Contraseña de Administrador
```bash
sudo passwd root
```
#### 2. Actualizar la Distrubución y los Paquetes
```bash
sudo apt update && sudo apt upgrade -y
```

#### 3. Instalar los Paquetes Necesarios
```bash
sudo apt install unclutter l3afpad curl chromium caffeine
```

#### 4. Deshabilitar el Llavero del Navegador
```bash
sudo chmod -x /usr/bin/gnome-keyring*
```

#### 5. Aplicar Capa de Personalización de Plymouth
This will overwrite the default `xubuntu-logo`.V
```bash
sudo cp -R xubuntu-logo /usr/share/plymouth/themes
sudo update-initramfs -u
sudo reboot now
```

#### 6. Deshabilitar Traductor de Google en Chromium
- Open Chromium.
- Navigate to settings and disable Google Translator (enabled by default).

#### 7. Crear o Copiar el Script Autostart
**Referencia:** El script original se puede en contrar en  [josfaber/debian-kiosk-installer](https://github.com/josfaber/debian-kiosk-installer). Solo se ha utilizado el script en sí para esta implementación.

Guarda el script como `autostart`:
```bash
#!/bin/bash
unclutter -idle 0.1 -grab -root &
while :
do
  chromium \
    --no-first-run \
    --start-maximized \
    --disable \
    --disable-translate \
    --disable-infobars \
    --disable-suggestions-service \
    --disable-save-password-bubble \
    --disable-session-crashed-bubble \
    --incognito \
    --kiosk "https://elmundo.com"
  sleep 2
done &
```

Copy the script to the following location:
```bash
/home/kiosk/.config/autostart/autostart
```

#### 8. Haz que el Script del Kiosko sea Ejecutable y Pruébalo
```bash
sudo chmod +x /home/kiosk/.config/autostart/autostart
sh /home/kiosk/.config/autostart/autostart
```

#### 9. Configurar Aplicacioned de Inicio
Asegurarse que las siguientes aplicaciones y scripts estén configurados para ejecutarse al incion:
- **Caffeine:** Verificar que se está ejecutando, con alguno de estos comandos:
  ```bash
  ps aux | grep caffeine
  pgrep caffeine
  ```
- **Script de Inicio:** Agrergar y habilitar el sript de inicio automático.

#### 10. Restringir el Usuario `kiosk`
Eliminar derechos de `sudo` comentando la línea correspondiente:
```bash
sudo visudo
```


### Configuración del Servicio de Monitorización

Para asegurarse de que el script de monitorización se ejecute como un servicio en segundo plano y se reinicie automáticamente si falla, se debe crear una unidad de servicio `systemd`.

1. Descargar archivos del Repositorio:

    Utiliza un navegador o si prefieres desde terminal con `wget`
    ```bash
     wget https://github.com/GOTXE/monkiosk/archive/refs/heads/main.zip
    ```
    Ahora la carpeta que hace falta es `kiosk_report`.

2. Configurar el Script de Monitorización:
    - Colocar `report_status.sh` `/opt/monitoring/report_status.sh`. Probablemente la carpeta monitoring no exista, así que tendrás que crearla
     ```bash
     sudo mkdir /opt/monitoring
     ```
    - coloca el archivo `report_status.sh`en esa carpeta y abrelo con tu editor favorito 
    ```bash
    sudo nano report_status.sh
    ```
    busca la línea: `server_url="http://<IP_DEL_SERVIDOR>/update_status.php`y pon la ip del servidor.

     ***Recargar systemd***:
    ```bash
    sudo systemctl daemon-reload
    ```

     ***Habilitar el servicio para que inicie al arrancar***:
    ```bash
    sudo systemctl enable kioskmonitoring.service
    ```

     ***Iniciar el servicio***:
    ```bash
    sudo systemctl start kioskmonitoring.service
    ```

     ***Verificar el estado del servicio***:
    ```bash
    sudo systemctl status kioskmonitoring.service
    ```

# Funcionamiento

### Script de Monitorización (`report_status.sh`)

Este script verifica continuamente la disponibilidad del servidor enviando una solicitud HTTP a `http://<IP_DEL_SERVIDOR>/update_status.php`. También obtiene el nombre del quiosco usando el comando `hostname` y envía su estado al servidor. Recibiendo el estado del quiosco en la web `update_status.php`, escribiendo en el archivo `status.json` el estado del quisco.

### Interfaz Web

La página web `ìndex.html` lee el archivo `status.json` cada 5 segundos para actualizar el estado de los quioscos y el servidor sin necesidad de recargar la página. Los quioscos se presentan visualmente con iconos codificados por colores (verde para online, rojo para offline) además de presentar la fecha_hora de visto el equipo.


### Personalización
Intervalo de Actualización:
Puedes cambiar el intervalo de actualización en el script Bash (report_status.sh) modificando la variable interval.

### Iconos de Quioscos
Para personalizar los iconos utilizados para los quioscos, reemplaza el archivo quiosco.svg en el directorio img/ con tu imagen preferida.

### Intervalo de Cambio de Documentos
Para cambiar el intervalo de tiempo entre documentos en el servidor quiosco `index.php`, modifica el valor en milisegundos en la variable `var intervalo = 5000;`


***Fin***
¿ Pero has llegado hasta aquí ? :clap::clap::clap:

Si has leído todo y lo llevaste a la práctica, tendrás un sistema   [![RAE](https://img.shields.io/badge/FUNCIONAL-42FC)](https://dle.rae.es/funcional)


Este pequeño proyecto está pensado para alguien sin conocimientos que pueda tener esta herramienta sencilla y sin complicaciones :vulcan_salute:


Oye que igual nos calentamos :fire:, se nos pone el morro fino :lips: y nos ponemos con una versión 2 :rocket:... 


Las imagenes usadas en la web son de [svgrepo.com](https://www.svgrepo.com).