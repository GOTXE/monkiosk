# Repository Guidelines

## Project Structure & Module Organization
This repository is split by runtime role:
- `kiosk_web/`: kiosk player (`index.php`) and media content in `kiosk_web/docs/`.
- `estado_quioscos/`: monitoring dashboard (`index.html`), API endpoint (`update_status.php`), and state file (`status.json`).
- `kiosks_report/`: kiosk-side reporting script (`report_status.sh`) and sample systemd unit.
- `kiosk_info/`: local documentation viewer and markdown sources in `kiosk_info/src/`.
- `tools/`: helper scripts such as `convert_video.sh`.

Keep assets near their module (`img/`, `assets/`, `docs/`) and avoid cross-module hardcoded paths.

## Build, Test, and Development Commands
No build pipeline is required; this is a PHP/HTML/JS/Bash project.

- Run kiosk player locally:
```bash
cd kiosk_web && php -S 0.0.0.0:8080
```
- Run monitor locally:
```bash
cd estado_quioscos && php -S 0.0.0.0:8081
```
- Validate Bash script syntax:
```bash
bash -n kiosks_report/report_status.sh
```
- Validate PHP syntax:
```bash
php -l kiosk_web/index.php
php -l estado_quioscos/update_status.php
```

## Coding Style & Naming Conventions
- Use 4-space indentation in PHP/JS/HTML/CSS and shell scripts.
- Prefer descriptive names in Spanish/English consistent with existing files.
- Keep kiosk media names numeric to preserve display order, e.g. `1.jpg`, `2.mp4`, `3.pdf`.
- Avoid adding framework tooling unless requested; keep dependencies minimal and local-first.

## Testing Guidelines
There is currently no automated test suite. Use targeted manual checks:
- Open `kiosk_web/index.php` and verify image/PDF/video rotation.
- POST heartbeat data to `estado_quioscos/update_status.php` and confirm `status.json` updates.
- Open `estado_quioscos/index.html` and verify online/offline transitions (timeout-based).

If you add tests, place them under a module-local `tests/` folder and document run commands in the module README.

## Commit & Pull Request Guidelines
Git history shows short, imperative commit messages (Spanish or English), e.g. `reordenar`, `Add video support`.
- Keep subject lines concise and action-oriented.
- One logical change per commit when possible.
- PRs should include: purpose, changed paths, deployment impact, and screenshots for UI updates (`kiosk_web`, `estado_quioscos`, `kiosk_info`).
- Link related issues/tasks and include rollback notes for config or service changes.

## Security & Configuration Tips
- Do not commit private keys, host secrets, or production URLs.
- Replace placeholder server URL in `kiosks_report/report_status.sh` before deployment.
- Keep `allowed_hosts.txt` curated to trusted kiosk hostnames only.
