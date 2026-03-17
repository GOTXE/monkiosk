# Repository Guidelines

## Scope
Use this file as a compact index. Read only the referenced document that applies to the task.

Task-oriented reading map:
- `tech_docs/guia_lectura_agente.md`

## Project Structure
`estado_quioscos/` contains dashboard, auth, APIs, and control endpoints. `kiosk_web/` serves kiosk-facing content from `kiosk_web/docs/`. Heartbeat and install scripts live in `kiosks_report/` and `Quioscos_install_alpine/`. Technical references live in `tech_docs/` and helper scripts in `tools/`.

## Mandatory Workflow
Before any code change, follow `tarea -> planner -> coder -> tester (if needed) -> documenta -> commit`.

Reference:
- `tech_docs/README.md`

## Git And Releases
For branch rules, PR flow, protected `main`, and sensitive artifact policy, read:
- `tech_docs/14_flujo_git_y_politicas_repos.md`
- `tech_docs/politica_archivos_runtime_locales.md`

For versioning, tags, and the single version source, read:
- `tech_docs/politica_versionado.md`
- `VERSION`

## Pull Requests
Every PR description must use:
- `tech_docs/14.1_plantilla_pr_vibecoding.md`

## Validation
There is no central build system. Minimum checks for touched files:
- `php -l <file>`
- `sh -n <script>`
- manual browser verification for affected flows

## Style
Use 4-space indentation. Match the existing procedural PHP and lowercase snake_case filenames. Keep comments short and targeted.

## Security
Do not commit credentials, tokens, private docs, internal logs, or host-specific secrets. Keep runtime secrets in server-local files such as `/etc/kiosk/heartbeat.conf`.
