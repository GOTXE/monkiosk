# Plan y Ejecucion - Disco libre por mounts en servidor

## Objetivo
Mostrar en la card de servidor el espacio libre por rutas clave:
- `/`
- `/var`
- `/home`

con formato `n.nG libre`.

## Ejecucion
1. `estado_quioscos/server_status.php`
- se anade lectura por mountpoint:
  - `disk_root_free_mb`
  - `disk_var_free_mb`
  - `disk_home_free_mb`

2. `estado_quioscos/index.html`
- en `Disco libre` se muestran 3 lineas:
  - `/ n.nG libre`
  - `/var n.nG libre`
  - `/home n.nG libre`
