# 2026-03-16 agents_referencias

## Planificacion

### Objetivo

Reducir `AGENTS.md` para que funcione como indice breve de normas del repositorio y remita a documentos cortos ya existentes en `tech_docs`.

### Alcance

- revisar `AGENTS.md`
- mantenerlo compacto
- referenciar flujo de trabajo, versionado, PR y politica Git sin duplicar contenido

### Riesgos

- dejar fuera alguna norma operativa importante
- duplicar reglas ya presentes en `tech_docs`

### Archivos a tocar

- `AGENTS.md`
- `tech_docs/guia_lectura_agente.md`
- `tech_docs/registro_cambios.md`
- este archivo de trabajo

## Ejecucion

- `AGENTS.md` se ha reducido a formato de referencia breve.
- se han eliminado bloques largos de detalle ya cubiertos por `tech_docs`
- se referencian de forma directa:
  - `tech_docs/README.md`
  - `tech_docs/guia_lectura_agente.md`
  - `tech_docs/14_flujo_git_y_politicas_repos.md`
  - `tech_docs/politica_versionado.md`
  - `tech_docs/14.1_plantilla_pr_vibecoding.md`
  - `VERSION`
- se ha creado una guia de lectura por tipo de tarea para agentes
- se ha añadido una entrada en `tech_docs/registro_cambios.md`

### Pruebas

- revision manual del contenido de `AGENTS.md`
- revision manual del contenido de `tech_docs/guia_lectura_agente.md`
- comprobacion de que las rutas referenciadas existen en el repositorio

### Resultado

`AGENTS.md` queda mas corto, evita duplicacion y remite a una guia concreta para que una IA sepa que leer segun cada tarea.
