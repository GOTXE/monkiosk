# 14. Flujo Git y politicas de repositorio

## Objetivo

Definir el flujo Git del proyecto, las ramas permitidas, las reglas de integración y el control de artefactos sensibles.

## Ramas

Ramas permitidas:

- `main`
- `dev`
- `feature/*`
- `hotfix/*`

## Reglas obligatorias

- `main` solo contiene estado estable y probado
- `dev` se usa para integracion continua sin despliegue a produccion
- las ramas `feature/*` salen desde `dev`
- las ramas `hotfix/*` salen desde `main` y deben volver a `main` y `dev`
- el formato de commit debe ser `tipo(scope): mensaje`
- toda PR debe revisar tests, seguridad y ausencia de sensibles
- el versionado semantico es obligatorio

## Reglas prohibidas

- no se permiten commits directos a `main`
- no se permite desplegar produccion desde `dev`
- no se permite publicar documentacion privada en `main`
- no se permite dejar tokens, configuraciones sensibles o logs internos publicos en el repositorio

## Flujo de trabajo

### Nueva feature

1. `checkout dev`
2. `pull`
3. crear `feature/...`
4. desarrollar
5. validar y probar
6. abrir PR a `dev`

### Integracion en dev

- solo por PR
- CI valida tests, lint y seguridad

### Release

1. PR de `dev` a `main`
2. merge `--no-ff`
3. tag semver anotado

### Ciclo del proyecto

1. `feature`
2. tests y documentacion
3. revision IA `99_revision_integral_app` si hay dudas
4. PR a `dev`
5. pruebas
6. PR a `main`
7. release
8. publicar solo documentacion publica
9. limpiar privadas

## Commits

Formato obligatorio:

- `feat(api): añadir endpoint de sincronizacion`
- `fix(db): corregido error en migracion`

## Pull Requests

Cada PR debe comprobar como minimo:

- tests
- seguridad
- ausencia de secretos o datos sensibles
- documentacion necesaria segun el cambio

Si hay dudas importantes, requiere revision adicional:

- `99_revision_integral_app`

## Sensibles y artefactos privados

No deben entrar en repositorio publico:

- tokens
- configuraciones internas sensibles
- logs internos
- documentacion privada

## Relacion con versionado

Esta politica se complementa con:

- `tech_docs/politica_versionado.md`
