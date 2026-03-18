#!/bin/sh
# ==============================================================================
# setup_quiosco.sh
# Instalación desatendida de kiosko con Alpine Linux 3.23 y Chromium
# Ejecutar como root desde una instalación limpia
# ==============================================================================

set -e  # Detener el script si cualquier comando falla

# ==============================================================================
# Parámetros de red
# ==============================================================================
echo "======================================================"
echo " Configuración de red"
echo "======================================================"
printf "Tipo de conexión (1=Cable, 2=WiFi, 3=Ya estoy conectado, saltar este paso): "
read NET_TYPE

if [ "$NET_TYPE" != "1" ] && [ "$NET_TYPE" != "2" ] && [ "$NET_TYPE" != "3" ]; then
  echo "ERROR: Opción no válida. Introduce 1 (Cable), 2 (WiFi) o 3 (saltar)."
  exit 1
fi

if [ "$NET_TYPE" = "3" ]; then
  echo ">>> Configuración de red omitida. Se usará la conexión existente."
fi

if [ "$NET_TYPE" = "2" ]; then
  printf "SSID (nombre de la red): "
  read WIFI_SSID
  printf "Contraseña: "
  read WIFI_PASS

  if [ -z "$WIFI_SSID" ] || [ -z "$WIFI_PASS" ]; then
    echo "ERROR: El SSID y la contraseña no pueden estar vacíos."
    exit 1
  fi
fi

# ==============================================================================
# Contraseña SSH del usuario quiosco
# ==============================================================================
echo "======================================================"
echo " Contraseña SSH del usuario quiosco"
echo "======================================================"
printf "Contraseña para quiosco (SSH): "
read QUIOSCO_PASS
printf "Repite la contraseña: "
read QUIOSCO_PASS2

if [ -z "$QUIOSCO_PASS" ]; then
  echo "ERROR: La contraseña no puede estar vacía."
  exit 1
fi

if [ "$QUIOSCO_PASS" != "$QUIOSCO_PASS2" ]; then
  echo "ERROR: Las contraseñas no coinciden."
  exit 1
fi

# ==============================================================================
# URL del quiosco
# ==============================================================================
echo "======================================================"
echo " URL a mostrar en el quiosco"
echo "======================================================"
printf "URL (ej: http://192.168.1.100 o https://miservidor.local): "
read KIOSKO_URL

if [ -z "$KIOSKO_URL" ]; then
  echo "ERROR: La URL no puede estar vacía."
  exit 1
fi

# Detectar si la URL es http o https para configurar los flags de Chromium
URL_SCHEME=$(echo "$KIOSKO_URL" | cut -d: -f1)

echo ""
echo "======================================================"
echo " Resumen de configuración"
echo "======================================================"
if [ "$NET_TYPE" = "1" ]; then
  echo " Red:  Cable (eth0 - DHCP)"
elif [ "$NET_TYPE" = "2" ]; then
  echo " Red:  WiFi - SSID: $WIFI_SSID"
else
  echo " Red:  Conexión existente (configuración omitida)"
fi
echo " URL:  $KIOSKO_URL"
echo " SSH:  Usuario quiosco con contraseña definida. Root deshabilitado."
echo "======================================================"
printf "¿Continuar con la instalación? (s/n): "
read CONFIRM
if [ "$CONFIRM" != "s" ] && [ "$CONFIRM" != "S" ]; then
  echo "Instalación cancelada."
  exit 0
fi

echo ">>> [1/7] Habilitando repositorios community e instalando dependencias..."

# Responder "c" (community) a setup-apkrepos de forma automática
printf 'c\n' | setup-apkrepos

apk update

if [ "$NET_TYPE" = "2" ]; then
  apk add --no-progress \
      xorg-server xf86-video-fbdev xf86-input-libinput \
      eudev xrandr setxkbmap xset xsetroot xinit \
      openbox chromium nano curl \
      linux-firmware-rtlwifi wpa_supplicant
else
  apk add --no-progress \
      xorg-server xf86-video-fbdev xf86-input-libinput \
      eudev xrandr setxkbmap xset xsetroot xinit \
      openbox chromium nano curl
fi

echo ">>> [2/7] Creando usuario quiosco y configurando contraseña..."

adduser -D quiosco
# Establecer la contraseña introducida durante la configuración inicial
printf "%s\n%s\n" "$QUIOSCO_PASS" "$QUIOSCO_PASS" | passwd quiosco
addgroup quiosco video
addgroup quiosco input

echo ">>> [3/7] Configurando autologin en /etc/inittab..."

cp /etc/inittab /etc/inittab.bak

cat << 'EOF' > /etc/inittab
::sysinit:/sbin/openrc -q -C sysinit
::sysinit:/sbin/openrc -q -C boot
::wait:/sbin/openrc -q -C default
tty1::respawn:/bin/login -f quiosco
::ctrlaltdel:/sbin/reboot
::shutdown:/sbin/openrc -q -C shutdown
EOF

echo ">>> [4/7] Aplicando política de sistema para deshabilitar traducción..."

mkdir -p /etc/chromium/policies/managed/

cat << 'EOF' > /etc/chromium/policies/managed/disable_translate.json
{
  "TranslateEnabled": false
}
EOF

echo ">>> [5/7] Configurando red..."

if [ "$NET_TYPE" = "2" ]; then
  mkdir -p /etc/wpa_supplicant
  wpa_passphrase "$WIFI_SSID" "$WIFI_PASS" > /etc/wpa_supplicant/wpa_supplicant.conf

  cat << 'EOF' > /etc/network/interfaces
auto lo
iface lo inet loopback

#auto eth0
#iface eth0 inet dhcp

auto wlan0
iface wlan0 inet dhcp
    wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf
EOF

  rc-update add wpa_supplicant boot

  echo ">>> [6/7] Cargando firmware y módulo WiFi..."
  modprobe rtl8xxxu || true

elif [ "$NET_TYPE" = "1" ]; then
  cat << 'EOF' > /etc/network/interfaces
auto lo
iface lo inet loopback

auto eth0
iface eth0 inet dhcp
EOF

  rc-update add networking boot
  echo ">>> [6/7] Conexión por cable configurada (eth0 - DHCP). Sin firmware WiFi necesario."

else
  echo ">>> [6/7] Configuración de red omitida. Se mantiene la conexión existente."
fi

echo ">>> [7/8] Configurando SSH: acceso quiosco habilitado, root deshabilitado..."

# Asegurar que openssh está instalado
apk add --no-progress openssh

# Modificar sshd_config
sed -i 's/^#*PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config

# Restringir acceso SSH solo al usuario quiosco
grep -q '^AllowUsers' /etc/ssh/sshd_config \
  && sed -i 's/^AllowUsers.*/AllowUsers quiosco/' /etc/ssh/sshd_config \
  || echo "AllowUsers quiosco" >> /etc/ssh/sshd_config

# Habilitar y arrancar sshd
rc-update add sshd default

echo ">>> [8/8] Creando archivos de configuración del usuario quiosco..."

# .profile: idioma, limpieza de sesión anterior y arranque de X
cat << 'EOF' > /home/quiosco/.profile
export LANG=es_ES.UTF-8
export LC_ALL=es_ES.UTF-8
export LANGUAGE=es_ES.UTF-8

# Limpiar rastros de sesiones anteriores antes de arrancar
rm -rf /home/quiosco/.config/chromium

# Lanzar entorno gráfico en tty1, ocultando el cursor del ratón
if [ -z "$DISPLAY" ] && [ "$(tty)" = "/dev/tty1" ]; then
  startx -- -nocursor
fi
EOF

# .xinitrc: gestión de energía y lanzamiento de Chromium
# Los flags --allow-running-insecure-content, --ignore-certificate-errors y
# --unsafely-treat-insecure-origin-as-secure solo son necesarios para HTTP.
if [ "$URL_SCHEME" = "http" ]; then
  cat << EOF > /home/quiosco/.xinitrc
# Desactivar protector de pantalla y gestión de energía del monitor
xset s off -dpms

# Arrancar gestor de ventanas mínimo para que Chromium ocupe toda la pantalla
openbox &
sleep 1

# Lanzar Chromium en modo kiosko (URL http: flags de origen inseguro activos)
exec chromium-browser \\
  --kiosk \\
  --incognito \\
  --no-first-run \\
  --disable-translate \\
  --disable-features=Translate,TranslateUI,NotificationPresenter \\
  --lang=es-ES \\
  --accept-lang=es-ES \\
  --disable-save-password-bubble \\
  --disable-infobars \\
  --check-for-update-interval=31536000 \\
  --do-not-send-anonymous-usage-stats \\
  --allow-running-insecure-content \\
  --ignore-certificate-errors \\
  --unsafely-treat-insecure-origin-as-secure="$KIOSKO_URL" \\
  "$KIOSKO_URL"
EOF
else
  cat << EOF > /home/quiosco/.xinitrc
# Desactivar protector de pantalla y gestión de energía del monitor
xset s off -dpms

# Arrancar gestor de ventanas mínimo para que Chromium ocupe toda la pantalla
openbox &
sleep 1

# Lanzar Chromium en modo kiosko (URL https: sin flags de origen inseguro)
exec chromium-browser \\
  --kiosk \\
  --incognito \\
  --no-first-run \\
  --disable-translate \\
  --disable-features=Translate,TranslateUI,NotificationPresenter \\
  --lang=es-ES \\
  --accept-lang=es-ES \\
  --disable-save-password-bubble \\
  --disable-infobars \\
  --check-for-update-interval=31536000 \\
  --do-not-send-anonymous-usage-stats \\
  "$KIOSKO_URL"
EOF
fi

# Asignar propietario correcto
chown quiosco:quiosco /home/quiosco/.profile
chown quiosco:quiosco /home/quiosco/.xinitrc

echo ""
echo "======================================================"
echo " Instalación completada. Reiniciando en 5 segundos..."
echo " (Ctrl+C para cancelar el reinicio)"
echo "======================================================"
sleep 5
reboot now