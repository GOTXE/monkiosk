# Plan de cambio: renombrar usuario SSH a ofap_srv_term

Fecha: 2026-02-19  
Rama: `develope_monitorizacion`

## 1) Objetivo
Renombrar el usuario de acceso SSH `ofap_term` a `ofap_srv_term`, manteniendo home, permisos y acceso por SSH.

## 2) Alcance
- Sistema operativo (usuarios y grupos)
- `/etc/ssh/sshd_config`

## 3) Plan
1. Validar existencia de `ofap_term` y ausencia de `ofap_srv_term`.
2. Renombrar usuario y grupo principal.
3. Mover home a `/home/ofap_srv_term` conservando contenido.
4. Actualizar `AllowUsers` en SSH.
5. Validar configuración y recargar servicio SSH.
6. Verificar estado final.

## 4) Ejecución
Completada.

- Usuario renombrado:
  - `ofap_term` -> `ofap_srv_term`
- Grupo principal renombrado:
  - `ofap_term` -> `ofap_srv_term`
- Home movida:
  - `/home/ofap_term` -> `/home/ofap_srv_term`
- SSH actualizado:
  - `AllowUsers ofap_srv_term`
  - `sshd -t` OK
  - servicio recargado
- Backup de SSH config:
  - `/etc/ssh/sshd_config.bak.20260223_103907`
