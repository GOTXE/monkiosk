# 2026-03-17 implantar_flujo_ramas

## Planificacion

### Objetivo

Implantar el flujo de ramas acordado para el proyecto:

- `main`
- `dev`
- `feature/*`
- `hotfix/*`

Y limpiar las ramas actuales que ya no encajan en ese esquema, manteniendo la rama de trabajo actual y `main`.

### Alcance

- actualizar la documentación de proceso y agentes
- crear la rama `dev`
- eliminar ramas locales y remotas que ya no deben seguir

### Riesgos

- borrar una rama que aún se necesite como referencia
- dejar inconsistencia entre ramas locales y remotas
- documentar una política distinta de la realmente implantada

### Archivos a tocar

- `AGENTS.md`
- `tech_docs/guia_lectura_agente.md`
- `tech_docs/manual_tecnico.md`
- `tech_docs/14_flujo_git_y_politicas_repos.md`
- `tech_docs/registro_cambios.md`
- este archivo

### Ramas previstas tras el cambio

- `main`
- `dev`
- `simplificar-gestion-web`

## Ejecucion

- se ha documentado en `AGENTS.md`, `tech_docs/manual_tecnico.md`, `tech_docs/guia_lectura_agente.md` y `tech_docs/14_flujo_git_y_politicas_repos.md` el modelo operativo:
  - `main`
  - `dev`
  - `feature/*`
  - `hotfix/*`
- se ha creado la rama `dev` desde `simplificar-gestion-web`
- se ha publicado `origin/dev`
- se ha eliminado la rama local `test_control_reinicio`
- se han eliminado las ramas remotas:
  - `origin/test_control_reinicio`
  - `origin/desarrollo_general`

### Pruebas

- comprobacion manual de ramas locales y remotas tras la operacion
- comprobacion de que `dev` apunta al estado actual de `simplificar-gestion-web`

### Resultado

El repositorio queda alineado con el flujo acordado, manteniendo como base operativa:

- `main`
- `dev`
- `simplificar-gestion-web`
