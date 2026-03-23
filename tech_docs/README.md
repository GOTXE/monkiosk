# tech_docs

Este directorio guarda documentación técnica previa a cada cambio.

## Regla de trabajo
Antes de modificar código, crear un archivo `.md` con:
1. **Planificación**: objetivo, alcance, riesgos, archivos a tocar.
2. **Ejecución**: cambios realizados, pruebas, resultado.

## Flujo obligatorio de trabajo

Para cualquier cambio del proyecto se seguirá siempre esta secuencia:

1. `tarea`
2. `planner`
3. `coder`
4. `tester` si es necesario
5. `documenta`
6. `commit`

Reglas:

- no se modifica codigo sin planificación previa en `tech_docs`
- si el cambio requiere prueba, debe quedar reflejado en la fase `tester`
- antes del commit deben quedar actualizadas las notas tecnicas o manuales que apliquen

## Convención de nombres
`YYYY-MM-DD_<tema>.md`

Ejemplo: `2026-02-18_monitorizacion-horizontal.md`
