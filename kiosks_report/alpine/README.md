# Heartbeat en Alpine (OpenRC)

Archivos:
- `install_heartbeat_alpine.sh`: instalador no interactivo.
- `heartbeat.conf.example`: plantilla de configuración.
- `kioskmonitoring.openrc`: servicio OpenRC.

## Instalación rápida (mínima interacción)
Desde el repo clonado en el quiosco:

```sh
sudo /ruta/monkiosk/kiosks_report/alpine/install_heartbeat_alpine.sh \
  --primary-url http://192.168.1.10/estado_quioscos/update_status.php \
  --fallback-url http://192.168.1.11/estado_quioscos/update_status.php
```

## Opciones útiles
- `--no-start`: instala pero no arranca el servicio.
- `--no-enable`: instala pero no lo agrega a arranque.
- `--force-config`: sobrescribe `/etc/kiosk/heartbeat.conf` si ya existe.
- `--kiosk-url http://IP_LOCAL/`: fija URL local del quiosco (si se omite, se autodetecta IP).
- `--control-token TOKEN`: token para consulta de acciones remotas (`get_action.php`).

Ayuda:
```sh
/ruta/monkiosk/kiosks_report/alpine/install_heartbeat_alpine.sh --help
```

## Verificación
```sh
rc-service kioskmonitoring status
tail -f /var/log/kiosk-heartbeat.log
```
