# Politica de versionado

## Objetivo

Monkiosk usa versionado semantico para releases, prereleases y tags.

La version la define el impacto real del cambio, no el nombre de la rama.

## Formato oficial

- release estable: `vMAJOR.MINOR.PATCH`
- prerelease beta: `vMAJOR.MINOR.PATCH-beta.N`
- prerelease rc: `vMAJOR.MINOR.PATCH-rc.N`

Solo se usaran:

- `beta`
- `rc`

Los tags de release deben ser anotados.

## Regla para subir version

- `MAJOR`: cambio incompatible o que rompe flujos ya existentes
- `MINOR`: funcionalidad nueva compatible con lo anterior
- `PATCH`: correccion, ajuste o mejora compatible sin romper comportamiento esperado

## Flujo recomendado

### Cambio pequeño o hotfix

1. desarrollar en la rama que corresponda
2. validar
3. merge a `main`
4. crear tag anotado de release estable

Ejemplo:

- `v2.3.4`

### Cambio grande o con validacion progresiva

1. desarrollar y validar internamente
2. publicar `beta`
3. publicar `rc`
4. publicar release estable

Ejemplos:

- `v2.4.0-beta.1`
- `v2.4.0-beta.2`
- `v2.4.0-rc.1`
- `v2.4.0`

## Fuente unica de version

La fuente unica de version del proyecto es el archivo:

- `VERSION`

Contenido esperado:

- sin prefijo `v`
- ejemplo estable: `2.4.0`
- ejemplo prerelease: `2.4.0-beta.1`

El tag Git correspondiente debe reflejar exactamente esa version con prefijo `v`.

Ejemplos:

- `VERSION`: `2.4.0`
- tag: `v2.4.0`

- `VERSION`: `2.4.0-rc.1`
- tag: `v2.4.0-rc.1`

## Reglas practicas

- no usar la rama para decidir la version
- no publicar release sin tag anotado
- actualizar `VERSION` antes de crear el tag
- si hay prerelease, la estable final debe salir de la misma base validada
- registrar el cambio principal en `tech_docs/registro_cambios.md` o en el changelog que se use para release
