# Sistema de Quioscos y Monitorización

## Índice

- Idea principal
- Descripción General
- Estructura del Proyecto
- Instalación (Servidor)
- Configuración de Quioscos
- Funcionamiento
- Operación y uso diario
- Versionado, ramas y PR
- Validación mínima
- Herramientas Auxiliares
- Documentación recomendada por perfil
- Créditos

## La idea principal 🚀

Este proyecto 🖥️ surgió de una necesidad real: `una amig@` seguía atrapada en métodos de los **años 90** para actualizar contenidos en pantallas: pendrives, caminatas matutinas con cara de lunes eterno 📬😤 y pánicos innecesarios cada vez que aparecía el temido “NO SIGNAL” o la misma presentación de PowerPoint de hace una década 🦖📊.

Los requisitos que me transmitió fueron claros y directos:

- Extremadamente sencillo de implementar y usar 🤏
- Coste lo más cercano a cero euros 💸
- Fácil de mantener 🛠️
- Y el comentario final que lo resume todo: “A ti te encantan estos cacharros raros 🔧🤓, ¡así te entretienes!”

Aquí NO vas a encontrar código digno de un unicornio 🦄 ni arquitectura que haga babear a los devs de SpaceX.  
Lo único que compartimos con SpaceX es que ambos proyectos **despegan**… pero el nuestro lo hace con:

- Hardware reciclado que aún huele a sótano y nostalgia 🏚️🍓
- Código pragmático (mucho copy-paste selectivo de Stack Overflow y ayuda de IAs modernas) 🤷‍♂️💻
- Una dosis generosa de pruebas, fe y café para que nada se incendie 🔥🙏
- Y fe ciega en que no se queme todo al mismo tiempo 🔥🙏💥

**Presupuesto estimado**: 0 € (reutilizando lo que ya existe) 😶‍🌫️  
**Mantenimiento recomendado**: reinicio ocasional + monitorización remota básica + un poco de sentido común 🪵  
**Soporte técnico**: ... cargando módulo de paciencia... 🤬🌙

Bienvenidos al **futuro low-cost y eficiente** de los quiosocs digitales: pantallas que se actualizan automáticamente por WiFi, sin paseos mañaneros ni dramas innecesarios 🚶‍♂️→🌐

Porque a veces la mejor innovación no necesita millones… solo un poco de ingenio, hardware sobrante y ganas de resolver problemas reales. 😏

#### Actualización de estado (o cómo mentimos sin remordimientos) 😂💀

Prometimos solemnemente:  
“Solo la v1, lo dejamos morir dignamente y nos olvidamos para siempre. Palabra de dev quemado.” 🪦🔥

Mentimos como bellacos.  
Ya estamos en la **v2** y contando features como si no hubiera un mañana. 😈🚀

Porque no hay nada como un  “proyecto low-cost” y añadirle 17 cosas más “porque total, ya que estamos” 🤡🛠️

Presupuesto: sigue en 0 €  
Tiempo invertido: más horas de las que quiero admitir  
Arrepentimiento: también infinito, pero con cafeína ☕😭

Bienvenidos a la **versión 2** *La última lo juro edición final*  
~~==(¡NO! no habrá v3, ¡¡lo sabes!!)==~~ 🤫👨‍💻📝💻🔥☕

¿Quién necesita disciplina cuando tienes FOMO técnico y un amigo que dice “y si le metemos…”? 🫠💾

#### Documentación para el futuro (o cómo no morir sin dejar rastro) 🧙‍♂️📜
Entonces, ¿por qué documentar todo esto? Muy sencillo: porque el `gotxe` y el `panoramix` del futuro no querrán recordar cómo funcionaba esto y, cuando ese amig@ **tenga un problema** :boom: (**porque lo tendrá**), nos llamará :telephone_receiver: preguntando cómo reinstalarlo . 
Pero esa vez no se lo vamos a reinstalar porque los `dos` del futuro, que fueron los que lo hicieron, seguramente estarán a otras cosas y hasta puede que en otro lugar, así que más le vale leer esto 😅 para poder hacerlo por sí mismo, y con la ayuda de la IA. 🙏✝️

## INFORMACIÓN QUE SI IMPORTA:

## Descripción General

Monkiosk en su estado actual (fuente de versión: [`VERSION`](./VERSION), actual `0.2.1`) se compone de tres bloques principales:

- **Presentación de contenido en quiosco** (`kiosk_web/index.php`) leyendo archivos de `kiosk_web/docs/`.
- **Gestión web y monitorización** en `estado_quioscos/` con login, control de quioscos permitidos, estado del servidor, acciones remotas y gestión de documentos.
- **Heartbeat en equipos** con scripts de `kiosks_report/alpine/` y soporte de instalación en `Quioscos_install_alpine/`.

El flujo operativo es:

1. El quiosco reproduce contenido desde `kiosk_web/index.php`.
2. El quiosco reporta estado a `estado_quioscos/update_status.php`.
3. La web `estado_quioscos/index.html` muestra estado de quioscos y servidor.
4. Si hay acciones pendientes (por ejemplo reinicio), el quiosco las recoge desde `estado_quioscos/get_action.php`.

## Estructura del Proyecto

Estructura principal actual:

```bash
monkiosk/
├── AGENTS.md
├── README.md
├── README_EN.md
├── VERSION
├── estado_quioscos/                         
│   ├── index.html
│   ├── login.php
│   ├── update_status.php
│   ├── get_action.php
│   ├── docs_manager.php
│   ├── slide_settings.php
│   ├── app_config.php
│   ├── config.local.php.example
│   ├── control_config.php.example
│   ├── styles.css
│   └── img/
├── kiosk_web/                               
│   ├── index.php
│   ├── docs/
│   └── vendor/pdfjs/
├── kiosks_report/                           
│   ├── alpine/                              
│   │   ├── install_heartbeat_alpine.sh
│   │   ├── report_status.sh
│   │   ├── kioskmonitoring.openrc
│   │   └── heartbeat.conf.example                            
├── Quioscos_install_alpine/
│   ├── install_report.sh
│   ├── setup_quiosco.sh
│   └── report/
├── kiosk_info/
│   ├── index.php
│   ├── doc.php
│   ├── assets/
│   └── src/
├── tools/
│   ├── init_estado_quioscos_runtime.sh
│   ├── backup_monkiosk.sh
│   ├── restore_monkiosk.sh
│   ├── convert_video.sh
│   └── manage_estado_quioscos_basic_auth.sh
└── tech_docs/
    ├── README.md
    ├── manual_tecnico.md
    ├── manual_usuario.md
    ├── politica_versionado.md
    ├── politica_archivos_runtime_locales.md
    └── registro_cambios.md
```

## Instalación (Servidor)

### Requisitos Previos

- Debian 13 con acceso `root`/`sudo`
- Repositorio clonado en el servidor
- Conectividad entre servidor y quioscos

### Pasos de Instalación

1. Instala dependencias base web:
   ```sh
   sudo apt update
   sudo apt install -y nginx php-fpm
   ```
2. Publica la aplicación en la raíz web (modelo actual del proyecto):
   ```sh
   sudo mkdir -p /var/www/html
   sudo cp -a kiosk_web/. /var/www/html/
   sudo cp -a estado_quioscos /var/www/html/
   ```
3. Prepara configuración local de `estado_quioscos`:
   ```sh
   cd /var/www/html/estado_quioscos
   sudo cp -n config.local.php.example config.local.php
   sudo cp -n control_config.php.example control_config.php
   ```
4. Edita `config.local.php` y ajusta al servidor:
   - `docs_dir` debe apuntar a `/var/www/html/docs`
   - `server.php_fpm_unit` en Debian 13 suele ser `php8.2-fpm`
5. Inicializa runtime local (desde la raíz del repo clonado; crea usuarios, JSON runtime y token de control):
   ```bash
   cd /ruta/al/repo/monkiosk
   sudo ./tools/init_estado_quioscos_runtime.sh --target-dir /var/www/html/estado_quioscos
   ```
   O versión explícita recomendada:
   ```bash
   sudo ./tools/init_estado_quioscos_runtime.sh \
     --target-dir /var/www/html/estado_quioscos \
     --admin-user admin \
     --admin-password 'CAMBIAR_CLAVE' \
     --control-token 'CAMBIAR_TOKEN'
   ```
6. Ajusta propietario/permisos para que Nginx y PHP-FPM escriban runtime:
   ```sh
   sudo chown -R nginx:nginx /var/www/html
   sudo find /var/www/html -type d -exec chmod 755 {} \;
   sudo find /var/www/html -type f -exec chmod 644 {} \;
   ```
7. Habilita y arranca servicios en systemd:
   ```sh
   sudo systemctl enable nginx php8.2-fpm
   sudo systemctl restart nginx php8.2-fpm
   ```
8. Verifica acceso:
   - Presentación: `http://IP_SERVIDOR/index.php`
   - Gestión: `http://IP_SERVIDOR/estado_quioscos/login.php`

Notas importantes:

- Los archivos runtime/locales no deben versionarse en Git.
- La referencia oficial de esta política está en [`tech_docs/politica_archivos_runtime_locales.md`](./tech_docs/politica_archivos_runtime_locales.md).

## Configuración de Quioscos

### 🖥️ Quiosco Alpine Linux + Chromium

Instalación desatendida de un quiosco digital basado en **Alpine Linux 3.23** con **Chromium en modo kiosk**.
El sistema arranca directamente en un navegador a pantalla completa apuntando a la URL configurada, sin escritorio ni interfaz de usuario adicional.

### 📋 Requisitos previos

- Una máquina (física o virtual) con soporte para arranque desde USB/CD.
- ISO de Alpine Linux 3.23 (edición **standard**).
- El archivo `setup_quiosco.sh` disponible (vía USB o red).
- Conexión a internet durante la instalación (para descargar paquetes).

### 🚀 Proceso de instalación

#### Paso 0. Instalación base de Alpine Linux

Arranca desde la ISO de Alpine y entra como root:

```sh
root
```

Ejecuta el asistente:

```sh
setup-alpine
```

Durante el asistente, usa estos valores:

| Parámetro | Valor recomendado |
|---|---|
| Keyboard layout | `es` / variante `es` |
| Hostname | el que quieras (ej: `quiosco01`) |
| Interfaz de red | `eth0` (o la disponible) |
| IP | `dhcp` |
| Root password | elige una segura |
| Timezone | `Europe/Madrid` |
| NTP client | `busybox` |
| APK mirror | `1` (o el más cercano) |
| Setup user | `no` |
| SSH server | `openssh` |
| Allow root SSH login | `yes` (temporal, el script lo desactivará) |
| SSH key | `none` |
| Disco | normalmente `sda` |
| Modo de uso | `sys` (instalado en disco) |
| Confirmar borrado | `y` |

Cuando finalice:

```sh
reboot
```

#### Paso 1. Copiar todos los archivos necesarios (como root)

Antes de comenzar, copia tanto `setup_quiosco.sh` como la carpeta `Quioscos_install_alpine` (con el instalador de monitorización) al directorio `/root` del quiosco.

Opción 1a. Desde un USB (formato NTFS):

```sh
apk add ntfs-3g
fdisk -l
mkdir /root/USB
mount -t ntfs-3g /dev/sdb1 /root/USB
cp /root/USB/setup_quiosco.sh /root/
cp -r /root/USB/Quioscos_install_alpine /root/
```

Opción 1b. Desde otro equipo por SCP (red):

En la máquina Alpine, comprueba la IP:

```sh
ip a
```

Desde el equipo origen (Linux/macOS/WSL):

```sh
scp setup_quiosco.sh root@<IP_DEL_QUIOSCO>:/root/
scp -r Quioscos_install_alpine root@<IP_DEL_QUIOSCO>:/root/
```

#### Paso 2. Ejecutar el script de configuración del quiosco

El script `setup_quiosco.sh` realiza la instalación de forma totalmente desatendida tras un breve asistente inicial.
Te pedirá:

1. Tipo de conexión: Cable (`eth0` por DHCP), WiFi (SSID y contraseña) o saltar el paso.
2. Contraseña SSH para el usuario `quiosco` (dos veces).
3. URL del quiosco (para esta instalación: `http://192.168.244.254`).

Ejecuta el script desde `/root`:

```sh
chmod +x /root/setup_quiosco.sh
sh /root/setup_quiosco.sh
```

Tras confirmar el resumen, el script ejecuta automáticamente:

| Fase | Acción |
|---|---|
| 1/8 | Habilita repositorios `community` e instala dependencias (Xorg, Openbox, Chromium…) |
| 2/8 | Crea el usuario `quiosco` y lo añade a `video` e `input` |
| 3/8 | Configura autologin en `tty1` para `quiosco` |
| 4/8 | Aplica política de Chromium para deshabilitar traducción |
| 5/8 | Configura red (cable/WiFi) o la omite |
| 6/8 | Carga firmware WiFi si aplica (`rtl8xxxu`) |
| 7/8 | Configura SSH: `quiosco` habilitado, root deshabilitado |
| 8/8 | Genera `.profile` y `.xinitrc` y reinicia |

Al terminar, el sistema reinicia automáticamente en 5 segundos (`Ctrl+C` para cancelar).

> ⚠️ Importante: una vez reiniciado, el acceso SSH como `root` queda deshabilitado. El Paso 3 debe ejecutarse antes del reinicio, cancelándolo con `Ctrl+C`.

#### Paso 3. Instalar el agente de monitorización

Este paso debe realizarse inmediatamente después de que `setup_quiosco.sh` termine, antes de reiniciar.

```sh
cd /root/Quioscos_install_alpine
sh install_report.sh --primary-url 192.168.244.254 --fallback-url 192.2.254.4 --force-config
```

Ahora introducir el token de control (sustituir o completar por `CONTROL_TOKEN="8eba4b574f4b62e94aa8aa081b55a657a604fcff"`):

```sh
nano /etc/kiosk/heartbeat.conf
```

Al terminar, reinicia manualmente:

```sh
reboot
```

Verificar servicio activo:

```sh
rc-service kioskmonitoring status
tail -f /var/log/kiosk-heartbeat.log
```

#### Opciones del instalador `install_report.sh`

| Opción | Descripción | Valor por defecto |
|---|---|---|
| `--primary-url URL` | URL principal del servidor (**obligatoria**) | — |
| `--fallback-url URL` | URL alternativa si la principal no responde | — |
| `--interval N` | Intervalo entre heartbeats (segundos) | `60` |
| `--max-retries N` | Reintentos por endpoint | `3` |
| `--retry-interval N` | Espera entre reintentos (segundos) | `10` |
| `--connect-timeout N` | Timeout de conexión curl (segundos) | `3` |
| `--max-time N` | Timeout total curl (segundos) | `5` |
| `--kiosk-url URL` | URL local del quiosco (informativa) | — |
| `--control-token TOKEN` | Token para consultar acciones remotas | — |
| `--no-start` | No arrancar el servicio al finalizar | — |
| `--no-enable` | No habilitar en arranque | — |
| `--force-config` | Sobrescribir `/etc/kiosk/heartbeat.conf` | — |

> ⚠️ Si `/etc/kiosk/heartbeat.conf` ya existe, usa `--force-config` para sobrescribirla. Sin ese flag, se conserva.

#### Archivos instalados

```text
/opt/monitoring/report_status.sh       -> Script principal heartbeat
/etc/kiosk/heartbeat.conf              -> Configuración (URLs, intervalos, token…)
/etc/init.d/kioskmonitoring            -> Servicio OpenRC
```

### ⚙️ Comportamiento del sistema tras la instalación

```text
Arranque -> autologin como quiosco (tty1)
         -> .profile lanza startx
         -> .xinitrc inicia Openbox + Chromium en modo kiosk
         -> Chromium abre la URL configurada a pantalla completa
```

- El perfil de Chromium se borra en cada arranque (`~/.config/chromium`).
- El cursor del ratón está oculto (`startx -- -nocursor`).
- Protector de pantalla y energía del monitor desactivados (`xset s off -dpms`).
- Chromium arranca en modo incógnito sin infobars ni prompts de contraseña.

Flags de Chromium según esquema URL:

| Esquema | Flags adicionales |
|---|---|
| `https://` | Ninguno extra |
| `http://` | `--allow-running-insecure-content`, `--ignore-certificate-errors`, `--unsafely-treat-insecure-origin-as-secure` |

### 🔧 Acceso de mantenimiento

Acceso remoto SSH con usuario `quiosco`:

```sh
ssh quiosco@<IP_DEL_QUIOSCO>
```

> ⚠️ El acceso SSH como `root` queda deshabilitado tras la instalación. Para tareas privilegiadas usa `doas` o consola local.

### 📁 Estructura de archivos relevantes

```text
/etc/inittab                                    -> Autologin en tty1
/etc/network/interfaces                         -> Configuración de red
/etc/wpa_supplicant/wpa_supplicant.conf         -> Credenciales WiFi (si aplica)
/etc/ssh/sshd_config                            -> Configuración SSH
/etc/chromium/policies/managed/disable_translate.json  -> Política Chromium
/home/quiosco/.profile                          -> Lanza startx
/home/quiosco/.xinitrc                          -> Inicia Openbox y Chromium
/opt/monitoring/report_status.sh                -> Agente de monitorización
/etc/kiosk/heartbeat.conf                       -> Configuración del agente
/etc/init.d/kioskmonitoring                     -> Servicio OpenRC
```

### 📦 Paquetes instalados

El script instala, según escenario:

- `xorg-server`, `xf86-video-fbdev`, `xf86-input-libinput`, `eudev`
- `xrandr`, `setxkbmap`, `xset`, `xsetroot`, `xinit`
- `openbox`
- `chromium`
- `openssh`
- `nano`
- `wpa_supplicant`, `linux-firmware-rtlwifi` (solo WiFi)

### 🛠️ Resolución de problemas comunes

El sistema no arranca en gráficos:

```sh
id quiosco
```

WiFi no conecta:

```sh
modprobe rtl8xxxu
rc-service wpa_supplicant status
```

Cambiar URL tras instalación:

```sh
nano /home/quiosco/.xinitrc
```

Reactivar temporalmente SSH root:

```sh
sed -i 's/^PermitRootLogin no/PermitRootLogin yes/' /etc/ssh/sshd_config
rc-service sshd restart
```

Agente heartbeat no envía:

```sh
rc-service kioskmonitoring status
tail -f /var/log/kiosk-heartbeat.log
nano /etc/kiosk/heartbeat.conf
rc-service kioskmonitoring restart
```

Cambio SSID/password:

```sh
wpa_passphrase "SSID" "passwd" > /etc/wpa_supplicant/wpa_supplicant.conf
```

### 📝 Notas

- El script usa `set -e`: se detiene ante cualquier error.
- Probado sobre Alpine Linux 3.23 con hardware compatible con `xf86-video-fbdev`.
- Para WiFi, incluye firmware para chipsets Realtek RTL8xxxU. Si usas otro adaptador, ajusta paquetes/driver en el script.

## Funcionamiento

- `estado_quioscos/update_status.php` recibe heartbeat de los quioscos.
- `estado_quioscos/index.html` muestra estado general, servidor y acciones de control.
- `estado_quioscos/docs_manager.php` gestiona subida/eliminación de documentos de `kiosk_web/docs/`.
- `estado_quioscos/get_action.php` permite al quiosco recoger acciones pendientes.
- `estado_quioscos/slide_settings.php` ajusta tiempo de diapositiva desde la UI de gestión.

## Operación y uso diario

Para uso diario (sin detalle técnico), consulta:

- [`tech_docs/manual_usuario.md`](./tech_docs/manual_usuario.md)

Para instalación, mantenimiento, recuperación y arquitectura:

- [`tech_docs/manual_tecnico.md`](./tech_docs/manual_tecnico.md)

## Versionado, ramas y PR

Referencias oficiales de trabajo:

- Política de versionado: [`tech_docs/politica_versionado.md`](./tech_docs/politica_versionado.md)
- Flujo Git y ramas: [`tech_docs/14_flujo_git_y_politicas_repos.md`](./tech_docs/14_flujo_git_y_politicas_repos.md)
- Plantilla de Pull Request: [`tech_docs/14.1_plantilla_pr_vibecoding.md`](./tech_docs/14.1_plantilla_pr_vibecoding.md)
- Flujo obligatorio de trabajo interno: [`tech_docs/README.md`](./tech_docs/README.md)

Modelo de ramas actual:

- `main`: estable y producción
- `dev`: integración
- `feature/*`: trabajo diario por cambio
- `hotfix/*`: corrección urgente sobre producción

## Validación mínima

No hay build centralizado. Mínimo por archivo tocado:

- `php -l <archivo.php>`
- `sh -n <script.sh>`
- Verificación manual en navegador del flujo afectado

## Herramientas Auxiliares

Scripts disponibles en `tools/`:

- `backup_monkiosk.sh`: backup local de proyecto y web.
- `restore_monkiosk.sh`: restauración guiada desde backup.
- `init_estado_quioscos_runtime.sh`: inicialización de runtime local.
- `convert_video.sh`: conversión de vídeo para quiosco.
- `manage_estado_quioscos_basic_auth.sh`: soporte de gestión basic auth (si aplica).

Documentación de uso:

- [`tools/README.md`](./tools/README.md)

## Documentación recomendada por perfil

- [`tech_docs/manual_usuario.md`](./tech_docs/manual_usuario.md)
- [`tech_docs/guia_uso_app.md`](./tech_docs/guia_uso_app.md)
- [`tech_docs/manual_tecnico.md`](./tech_docs/manual_tecnico.md)
- [`tech_docs/guia_lectura_agente.md`](./tech_docs/guia_lectura_agente.md)
- [`tech_docs/registro_cambios.md`](./tech_docs/registro_cambios.md)

### Imágenes y recursos locales

- Iconos e imágenes usadas en UI: [svgrepo.com](https://www.svgrepo.com)
