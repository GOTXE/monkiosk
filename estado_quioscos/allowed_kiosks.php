<?php
require_once __DIR__ . '/auth_lib.php';
require_once __DIR__ . '/app_config.php';
auth_require_page();
$csrfToken = auth_csrf_token();
$items = eq_load_allowed_kiosks_for_crud();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quioscos Permitidos</title>
    <style>
        :root {
            --bg: #001f3f;
            --card: #ffffff;
            --ink: #13233b;
            --muted: #4f627c;
            --line: #d8e2ef;
            --btn: #2b78d9;
            --btn-hover: #1f66bf;
            --danger: #c92a2a;
            --ok: #2f9e44;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Verdana, sans-serif; background: var(--bg); color: #eaf2ff; }
        .page { width: min(1100px, 100%); margin: 0 auto; padding: 16px; min-height: 100vh; }
        .topbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .title { margin: 0; font-size: 1.5rem; }
        .btn {
            border: 1px solid #2a70c8;
            background: var(--btn);
            color: #fff;
            border-radius: 10px;
            padding: 9px 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: var(--btn-hover); }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(31, 102, 191, 0.22);
        }
        .card {
            background: var(--card);
            color: var(--ink);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px;
        }
        .hint { margin: 0 0 6px; color: var(--muted); font-size: 0.9rem; }
        .status { min-height: 0; margin-bottom: 0; font-size: 0.9rem; font-weight: 700; color: var(--muted); }
        .status:not(:empty) { min-height: 20px; }
        .status.ok { color: var(--ok); }
        .status.warn {
            color: #a35300;
            animation: pending-save-blink 1s ease-in-out infinite;
        }
        .status.err { color: var(--danger); }
        @keyframes pending-save-blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e4ebf4; padding: 8px; text-align: left; vertical-align: middle; }
        th { color: var(--muted); font-size: 0.82rem; }
        input[type="text"] {
            width: 100%;
            border: 1px solid #cdd9e8;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 0.92rem;
            font-weight: 400;
            font-style: normal;
            color: var(--ink);
            background: #f8fbff;
        }
        input[type="text"]::placeholder {
            font-style: italic;
            font-weight: 400;
            color: #6f839d;
        }
        .input-filled { font-weight: 700 !important; }
        .ip-invalid {
            border-color: #c92a2a !important;
            background: #fff6f6 !important;
            color: #a61e2a !important;
        }
        .row-actions { display: inline-flex; gap: 6px; }
        .small-btn {
            border: 1px solid #c8d7ea;
            background: #fff;
            color: #1f4d86;
            border-radius: 8px;
            padding: 6px 8px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.14s ease, box-shadow 0.14s ease, background-color 0.14s ease, border-color 0.14s ease;
        }
        .small-btn:hover {
            transform: translateY(-1px);
            background: #edf4ff;
            border-color: #9ebbe1;
            box-shadow: 0 8px 18px rgba(31, 77, 134, 0.16);
        }
        .small-btn.danger { color: #a61e2a; border-color: #e8bcc1; background: #fff6f6; }
        .small-btn.danger:hover {
            background: #ffe9ea;
            border-color: #e19aa3;
            box-shadow: 0 8px 18px rgba(166, 30, 42, 0.14);
        }
        .actions { margin-top: 8px; display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
        .table-actions {
            justify-content: space-between;
            align-items: center;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 10px;
        }
        .table-actions .status {
            text-align: center;
            font-size: 0.84rem;
        }
        .section-title { margin: 18px 0 10px; color: #1f4d86; font-size: 1.02rem; }
        .protection-box {
            border: 1px solid #d7e1ee;
            border-radius: 10px;
            background: #f8fbff;
            padding: 6px 10px;
            margin-bottom: 10px;
        }
        .protection-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px solid #e4ebf4;
        }
        .protection-row:last-child { border-bottom: 0; }
        .protection-label {
            color: #1f4d86;
            font-size: 0.84rem;
            font-weight: 700;
        }
        .protection-label span {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 400;
        }
        .protection-toggle {
            min-width: 110px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #c8d7ea;
            background: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
        }
        .protection-toggle.protection-on { color: #1f7a35; background: #e9f8ec; border-color: #b8e1c0; }
        .protection-toggle.protection-off { color: #a61e2a; background: #fff1f1; border-color: #efc2c2; }
        .warn-text { color: #a35300; }
        .attempts-box {
            margin-top: 18px;
            padding: 12px;
            border: 1px solid #d7e1ee;
            border-radius: 10px;
            background: #f8fbff;
        }
        .attempt-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e4ebf4;
        }
        .attempt-item:last-child { border-bottom: 0; }
        .attempt-meta { color: var(--muted); font-size: 0.84rem; }
        .viewer-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 4px;
            padding: 10px 0;
            border-bottom: 1px solid #e4ebf4;
        }
        .viewer-item:last-child { border-bottom: 0; }
        .viewer-item.known-kiosk {
            background: #eef8f0;
            border: 1px solid #b8e1c0;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 8px;
        }
        .viewer-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .viewer-badge.known {
            background: #d9f2e0;
            color: #1f7a35;
        }
        .viewer-badge.generic {
            background: #eef4fb;
            color: #1f4d86;
        }
        .app-footer {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #d6e7ff;
            font-size: 0.84rem;
            width: 100%;
        }
        .footer-brand { font-weight: 700; }
        .footer-github {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            color: #d6e7ff;
            border: 1px solid rgba(214, 231, 255, 0.35);
            text-decoration: none;
        }
        .footer-github svg { width: 15px; height: 15px; fill: currentColor; }
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 16, 38, 0.62);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1200;
            backdrop-filter: blur(2px);
        }
        .modal-backdrop[hidden] { display: none; }
        .confirm-modal-card {
            width: min(420px, 100%);
            background: #ffffff;
            color: var(--ink);
            border: 1px solid rgba(201, 42, 42, 0.55);
            border-radius: 12px;
            padding: 16px;
            box-shadow:
                0 0 0 4px rgba(201, 42, 42, 0.18),
                0 0 28px rgba(201, 42, 42, 0.28),
                0 20px 54px rgba(0, 0, 0, 0.3);
            transform: translateY(-6px);
        }
        .confirm-modal-card.modal-safe {
            border-color: rgba(47, 158, 68, 0.55);
            box-shadow:
                0 0 0 4px rgba(47, 158, 68, 0.18),
                0 0 28px rgba(47, 158, 68, 0.22),
                0 20px 54px rgba(0, 0, 0, 0.3);
        }
        .confirm-modal-card.modal-danger {
            border-color: rgba(201, 42, 42, 0.55);
            box-shadow:
                0 0 0 4px rgba(201, 42, 42, 0.18),
                0 0 28px rgba(201, 42, 42, 0.28),
                0 20px 54px rgba(0, 0, 0, 0.3);
        }
        .confirm-modal-card h3 { margin: 0 0 12px; color: #1f4d86; }
        .confirm-modal-card p { margin: 0 0 12px; color: var(--muted); }
        .confirm-modal-card input[type="text"] { margin-top: 6px; }
        .modal-actions {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar">
            <h1 class="title">Quioscos Permitidos</h1>
            <a class="btn" href="/estado_quioscos/">Volver</a>
        </div>

        <div class="card">
            <p class="hint">
                Define los quioscos válidos por <strong>hostname</strong> y, opcionalmente, su <strong>IP fija</strong>.
                Si una IP está informada, el sistema exigirá que coincida con el reporte recibido. Marca <strong>Permitido</strong> para autorizar ese quiosco.
            </p>
            <div class="protection-box">
                <div class="protection-row">
                    <div class="protection-label">Reporte quioscos: <span>permitir o denegar acceso a reporte</span></div>
                    <button id="toggle-report-protection-btn" class="protection-toggle" type="button"></button>
                </div>
                <div class="protection-row">
                    <div class="protection-label">Presentación: <span>restringir o permitir acceso a la presentación</span></div>
                    <button id="toggle-presentation-protection-btn" class="protection-toggle" type="button"></button>
                </div>
            </div>

            <h2 class="section-title">Quioscos: Reporte permitido</h2>
            <table>
                <thead>
                    <tr>
                        <th>Hostname</th>
                        <th>IP fija</th>
                        <th>Permitido</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="rows"></tbody>
            </table>

            <div class="actions table-actions">
                <button id="add-row" class="small-btn" type="button">Añadir quiosco</button>
                <div id="status" class="status"></div>
                <button id="save-btn" class="btn" type="button">Guardar cambios</button>
            </div>

            <div class="attempts-box">
                <h2 class="section-title" style="margin-top:0;">Intentos de conexión</h2>
                <div class="attempt-meta" style="margin-bottom:8px;">Equipos detectados a la espera de otorgar permiso de acceso.</div>
                <div class="attempt-meta" style="margin-bottom:8px;color:#7a8da7;">Actualización automática cada 15 s.</div>
                <div id="unknown-attempts"></div>
            </div>

            <div class="attempts-box">
                <h2 class="section-title" style="margin-top:0;">Accesos a presentación</h2>
                <div class="attempt-meta" style="margin-bottom:8px;">Listado de equipos que están abriendo la presentación web del quiosco.</div>
                <div class="attempt-meta" style="margin-bottom:8px;color:#7a8da7;">Útil sobre todo cuando la protección está desactivada.</div>
                <div id="presentation-viewers"></div>
            </div>
        </div>
        <footer class="app-footer">
            <span class="footer-brand">OFAP 601</span>
            <a class="footer-github" href="https://github.com/GOTXE/monkiosk" target="_blank" rel="noopener noreferrer" aria-label="Repositorio GitHub monkiosk" title="Repositorio GitHub monkiosk">
                <svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M8 0C3.58 0 0 3.58 0 8a8 8 0 0 0 5.47 7.59c.4.07.55-.17.55-.38v-1.49c-2.23.48-2.7-.95-2.7-.95-.36-.92-.89-1.16-.89-1.16-.73-.5.06-.49.06-.49.81.06 1.24.83 1.24.83.72 1.24 1.89.88 2.35.67.07-.52.28-.88.5-1.08-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.58.82-2.14-.08-.2-.36-1.01.08-2.1 0 0 .67-.21 2.2.82a7.62 7.62 0 0 1 4 0c1.53-1.03 2.2-.82 2.2-.82.44 1.09.16 1.9.08 2.1.51.56.82 1.27.82 2.14 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48v2.2c0 .21.15.46.55.38A8 8 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/>
                </svg>
            </a>
        </footer>
    </div>
        <div id="save-modal" class="modal-backdrop" hidden>
        <div class="confirm-modal-card">
            <h3>Confirmar guardado</h3>
            <p>Escribe <strong class="warn-text">GUARDAR</strong> para aplicar los cambios de la lista.</p>
            <input id="save-confirm-text" type="text" autocomplete="off">
            <div class="modal-actions">
                <button id="save-modal-cancel" class="small-btn danger" type="button">Cancelar</button>
                <button id="save-modal-confirm" class="btn" type="button">Guardar</button>
            </div>
        </div>
    </div>
        <div id="protection-modal" class="modal-backdrop" hidden>
        <div id="protection-modal-card" class="confirm-modal-card modal-danger">
            <h3 id="protection-modal-title">Confirmar cambio de protección</h3>
            <p id="protection-modal-text">¿Seguro que quieres cambiar esta protección?</p>
            <div class="modal-actions">
                <button id="protection-modal-cancel" class="small-btn danger" type="button">Cancelar</button>
                <button id="protection-modal-confirm" class="btn" type="button">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = <?= json_encode($csrfToken, JSON_UNESCAPED_UNICODE) ?>;
        const initialItems = <?= json_encode($items, JSON_UNESCAPED_UNICODE) ?>;
        const rowsEl = document.getElementById('rows');
        const statusEl = document.getElementById('status');
        const unknownAttemptsEl = document.getElementById('unknown-attempts');
        const presentationViewersEl = document.getElementById('presentation-viewers');
        const toggleReportProtectionBtn = document.getElementById('toggle-report-protection-btn');
        const togglePresentationProtectionBtn = document.getElementById('toggle-presentation-protection-btn');
        const saveModal = document.getElementById('save-modal');
        const saveConfirmTextEl = document.getElementById('save-confirm-text');
        const saveModalCancel = document.getElementById('save-modal-cancel');
        const saveModalConfirm = document.getElementById('save-modal-confirm');
        const protectionModal = document.getElementById('protection-modal');
        const protectionModalCard = document.getElementById('protection-modal-card');
        const protectionModalTitle = document.getElementById('protection-modal-title');
        const protectionModalText = document.getElementById('protection-modal-text');
        const protectionModalCancel = document.getElementById('protection-modal-cancel');
        const protectionModalConfirm = document.getElementById('protection-modal-confirm');
        const attemptsRefreshIntervalMs = 15000;
        let currentItems = Array.isArray(initialItems) ? initialItems : [];
        let currentProtection = { report_enabled: true, presentation_enabled: true };
        let currentUnknownAttempts = [];
        let currentPresentationViewers = [];
        let attemptsRefreshTimer = null;
        let statusClearTimer = null;
        let pendingProtectionChange = null;

        function setStatus(message, cls) {
            statusEl.textContent = message || '';
            statusEl.className = 'status' + (cls ? ` ${cls}` : '');
            if (statusClearTimer) {
                window.clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            if (message && (cls === 'ok' || cls === 'err')) {
                statusClearTimer = window.setTimeout(() => {
                    statusEl.textContent = '';
                    statusEl.className = 'status';
                    statusClearTimer = null;
                }, 5000);
            }
        }

        function renderRows(items) {
            currentItems = Array.isArray(items) ? items : [];
            rowsEl.innerHTML = '';
            currentItems.forEach((item) => addRow(item));
            if (!currentItems.length) addRow({ hostname: '', ip: '', enabled: true });
        }

        function formatRelativeTime(ts) {
            const value = Number(ts || 0);
            if (!value) return 'sin dato';
            const seconds = Math.max(0, Math.floor(Date.now() / 1000) - value);
            if (seconds < 60) return `hace ${seconds}s`;
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `hace ${minutes} min`;
            const hours = Math.floor(minutes / 60);
            return `hace ${hours} h`;
        }

        function renderUnknownAttempts(items) {
            const currentHostnames = new Set(collectItems().map((item) => String(item.hostname || '').toLowerCase()).filter((hostname) => hostname !== ''));
            currentUnknownAttempts = (Array.isArray(items) ? items : []).filter((item) => {
                const hostname = String(item.hostname || '').toLowerCase();
                return hostname !== '' && !currentHostnames.has(hostname);
            });
            if (!currentUnknownAttempts.length) {
                unknownAttemptsEl.innerHTML = '<div class="attempt-meta">No hay intentos pendientes.</div>';
                return;
            }
            unknownAttemptsEl.innerHTML = currentUnknownAttempts.map((item) => `
                <div class="attempt-item">
                    <div>
                        <strong>${escapeHtml(item.hostname || '')}</strong>
                        <div class="attempt-meta">IP: ${escapeHtml(item.ip || '-')} | Intentos: ${Number(item.attempts || 0)} | Último: ${formatRelativeTime(item.last_seen)}</div>
                    </div>
                    <button class="small-btn" type="button" data-add-attempt="${escapeHtml(item.hostname || '')}">Añadir</button>
                </div>
            `).join('');
            unknownAttemptsEl.querySelectorAll('[data-add-attempt]').forEach((button) => {
                button.addEventListener('click', () => {
                    const hostname = String(button.getAttribute('data-add-attempt') || '').toLowerCase();
                    const attempt = currentUnknownAttempts.find((item) => String(item.hostname || '').toLowerCase() === hostname);
                    if (!attempt) return;
                    const items = collectItems();
                    if (!items.some((item) => item.hostname === hostname)) {
                        items.push({
                            hostname,
                            ip: String(attempt.ip || ''),
                            enabled: true,
                        });
                        renderRows(items);
                        renderUnknownAttempts(currentUnknownAttempts.filter((item) => String(item.hostname || '').toLowerCase() !== hostname));
                        setStatus(`Añadido ${hostname} a la tabla. Falta guardar cambios.`, 'warn');
                    }
                });
            });
        }

        function renderProtection(protection) {
            currentProtection = {
                report_enabled: !protection || protection.report_enabled !== false,
                presentation_enabled: !protection || protection.presentation_enabled !== false,
            };

            toggleReportProtectionBtn.textContent = currentProtection.report_enabled ? 'Protegido' : 'Desprotegido';
            toggleReportProtectionBtn.className = `protection-toggle ${currentProtection.report_enabled ? 'protection-on' : 'protection-off'}`;

            togglePresentationProtectionBtn.textContent = currentProtection.presentation_enabled ? 'Protegido' : 'Desprotegido';
            togglePresentationProtectionBtn.className = `protection-toggle ${currentProtection.presentation_enabled ? 'protection-on' : 'protection-off'}`;
        }

        function renderPresentationViewers(items) {
            currentPresentationViewers = Array.isArray(items) ? items : [];
            if (currentProtection.presentation_enabled) {
                presentationViewersEl.innerHTML = '<div class="attempt-meta">Protección activada. Solo deberían aparecer accesos de IPs fijas autorizadas.</div>';
                return;
            }
            if (!currentPresentationViewers.length) {
                presentationViewersEl.innerHTML = '<div class="attempt-meta">No hay accesos recientes a la presentación.</div>';
                return;
            }
            presentationViewersEl.innerHTML = currentPresentationViewers.map((item) => `
                <div class="viewer-item ${item.known_kiosk ? 'known-kiosk' : ''}">
                    <strong>${escapeHtml(item.ip || '-')}</strong>
                    <div><span class="viewer-badge ${item.known_kiosk ? 'known' : 'generic'}">${item.known_kiosk ? 'Quiosco conocido' : 'Acceso genérico'}</span></div>
                    <div class="attempt-meta">Hostname: ${escapeHtml(item.hostname || 'sin resolver')} | Accesos: ${Number(item.hits || 0)} | Último: ${formatRelativeTime(item.last_seen)}</div>
                </div>
            `).join('');
        }

        async function refreshAttemptsOnly() {
            try {
                const response = await fetch('/estado_quioscos/allowed_kiosks_api.php', { cache: 'no-store' });
                const data = await response.json();
                if (!response.ok || !data || !data.success) {
                    return;
                }
                renderProtection(data.protection || {});
                renderUnknownAttempts(Array.isArray(data.unknown_attempts) ? data.unknown_attempts : []);
                renderPresentationViewers(Array.isArray(data.presentation_viewers) ? data.presentation_viewers : []);
            } catch (_) {
            }
        }

        function startAttemptsAutoRefresh() {
            if (attemptsRefreshTimer) {
                window.clearInterval(attemptsRefreshTimer);
            }
            attemptsRefreshTimer = window.setInterval(() => {
                if (document.hidden) {
                    return;
                }
                refreshAttemptsOnly();
            }, attemptsRefreshIntervalMs);
        }

        function openSaveModal() {
            saveConfirmTextEl.value = '';
            saveModal.hidden = false;
            saveConfirmTextEl.focus();
        }

        function closeSaveModal() {
            saveModal.hidden = true;
        }

        function openProtectionModal(type, nextEnabled) {
            const targetLabel = type === 'report' ? 'reporte' : 'presentación';
            const actionLabel = nextEnabled ? 'activar' : 'desactivar';
            protectionModalTitle.textContent = 'Confirmar cambio de protección';
            protectionModalText.textContent = `¿Seguro que quieres ${actionLabel} la protección de ${targetLabel}?`;
            protectionModalCard.classList.remove('modal-safe', 'modal-danger');
            protectionModalCard.classList.add(nextEnabled ? 'modal-safe' : 'modal-danger');
            pendingProtectionChange = { type, nextEnabled };
            protectionModal.hidden = false;
        }

        function closeProtectionModal() {
            protectionModal.hidden = true;
            pendingProtectionChange = null;
        }

        function addRow(item) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" data-field="hostname" value="${escapeHtml(item.hostname || '')}" placeholder="kiosk01"></td>
                <td><input type="text" inputmode="numeric" data-field="ip" value="${escapeHtml(item.ip || '')}" placeholder="192.168.1.10"></td>
                <td><input type="checkbox" data-field="enabled" ${item.enabled !== false ? 'checked' : ''}></td>
                <td class="row-actions"><button class="small-btn danger" type="button">Eliminar</button></td>
            `;
            const hostnameInput = tr.querySelector('[data-field="hostname"]');
            const ipInput = tr.querySelector('[data-field="ip"]');
            hostnameInput.addEventListener('input', () => {
                hostnameInput.value = hostnameInput.value.toLowerCase();
                updateFilledInputState(hostnameInput);
            });
            ipInput.addEventListener('input', () => {
                ipInput.value = ipInput.value.replace(/[^0-9.]/g, '');
                updateFilledInputState(ipInput);
                updateIpInputState(ipInput);
            });
            updateFilledInputState(hostnameInput);
            updateFilledInputState(ipInput);
            updateIpInputState(ipInput);
            tr.querySelector('button').addEventListener('click', () => {
                const removedHostname = String(hostnameInput.value || '').trim().toLowerCase();
                tr.remove();
                if (!rowsEl.children.length) addRow({ hostname: '', ip: '', enabled: true });
                setStatus(
                    removedHostname !== ''
                        ? `Eliminado ${removedHostname} de la tabla. Falta guardar cambios.`
                        : 'Se ha eliminado un quiosco de la tabla. Falta guardar cambios.',
                    'warn'
                );
            });
            rowsEl.appendChild(tr);
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function isValidIpv4(ip) {
            const value = String(ip || '').trim();
            if (value === '') return true;
            if (!/^\d+(?:\.\d+){3}$/.test(value)) return false;
            const parts = value.split('.');
            if (parts.length !== 4) return false;
            return parts.every((part) => {
                if (part === '') return false;
                const n = Number(part);
                return Number.isInteger(n) && n >= 0 && n <= 255;
            });
        }

        function updateIpInputState(input) {
            input.classList.toggle('ip-invalid', !isValidIpv4(input.value));
        }

        function updateFilledInputState(input) {
            input.classList.toggle('input-filled', String(input.value || '').trim() !== '');
        }

        function collectItems() {
            return Array.from(rowsEl.querySelectorAll('tr')).map((row) => ({
                hostname: row.querySelector('[data-field="hostname"]').value.trim().toLowerCase(),
                ip: row.querySelector('[data-field="ip"]').value.trim(),
                enabled: row.querySelector('[data-field="enabled"]').checked
            })).filter((item) => item.hostname !== '' || item.ip !== '');
        }

        function hasInvalidIpInputs() {
            let invalid = false;
            rowsEl.querySelectorAll('[data-field="ip"]').forEach((input) => {
                updateIpInputState(input);
                if (!isValidIpv4(input.value)) {
                    invalid = true;
                }
            });
            return invalid;
        }

        document.getElementById('add-row').addEventListener('click', () => {
            addRow({ hostname: '', ip: '', enabled: true });
        });

        async function submitSaveItems() {
            setStatus('');
            const items = collectItems();
            if (hasInvalidIpInputs()) {
                setStatus('Hay IPs fijas no válidas. Revisa los campos marcados en rojo.', 'err');
                closeSaveModal();
                return;
            }
            try {
                const response = await fetch('/estado_quioscos/allowed_kiosks_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'save_items',
                        csrf_token: csrfToken,
                        confirm_text: saveConfirmTextEl.value,
                        items
                    })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo guardar');
                }
                renderRows(Array.isArray(data.items) ? data.items : items);
                renderProtection(data.protection || {});
                renderUnknownAttempts(Array.isArray(data.unknown_attempts) ? data.unknown_attempts : []);
                renderPresentationViewers(Array.isArray(data.presentation_viewers) ? data.presentation_viewers : []);
                closeSaveModal();
                setStatus('Configuración guardada correctamente.', 'ok');
            } catch (error) {
                setStatus(error.message || 'No se pudo guardar', 'err');
            }
        }

        document.getElementById('save-btn').addEventListener('click', openSaveModal);
        saveModalCancel.addEventListener('click', closeSaveModal);
        saveModalConfirm.addEventListener('click', submitSaveItems);
        saveModal.addEventListener('click', (event) => {
            if (event.target === saveModal) {
                closeSaveModal();
            }
        });

        async function applyProtectionChange(type, nextEnabled) {
            setStatus('');
            try {
                const response = await fetch('/estado_quioscos/allowed_kiosks_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'set_protection',
                        type,
                        csrf_token: csrfToken,
                        enabled: nextEnabled
                    })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo cambiar la protección');
                }
                renderProtection(data.protection || {});
                renderUnknownAttempts(Array.isArray(data.unknown_attempts) ? data.unknown_attempts : []);
                renderPresentationViewers(Array.isArray(data.presentation_viewers) ? data.presentation_viewers : []);
            } catch (error) {
                setStatus(error.message || 'No se pudo cambiar la protección', 'err');
            }
        }

        toggleReportProtectionBtn.addEventListener('click', () => openProtectionModal('report', !currentProtection.report_enabled));
        togglePresentationProtectionBtn.addEventListener('click', () => openProtectionModal('presentation', !currentProtection.presentation_enabled));
        protectionModalCancel.addEventListener('click', closeProtectionModal);
        protectionModalConfirm.addEventListener('click', async () => {
            if (!pendingProtectionChange) {
                closeProtectionModal();
                return;
            }
            const { type, nextEnabled } = pendingProtectionChange;
            closeProtectionModal();
            await applyProtectionChange(type, nextEnabled);
        });
        protectionModal.addEventListener('click', (event) => {
            if (event.target === protectionModal) {
                closeProtectionModal();
            }
        });

        renderRows(currentItems);
        fetch('/estado_quioscos/allowed_kiosks_api.php', { cache: 'no-store' })
            .then((response) => response.json())
            .then((data) => {
                if (!data || !data.success) return;
                renderRows(Array.isArray(data.items) ? data.items : currentItems);
                renderProtection(data.protection || {});
                renderUnknownAttempts(Array.isArray(data.unknown_attempts) ? data.unknown_attempts : []);
                renderPresentationViewers(Array.isArray(data.presentation_viewers) ? data.presentation_viewers : []);
            })
            .catch(() => {
                renderProtection({ report_enabled: true, presentation_enabled: true });
                renderUnknownAttempts([]);
                renderPresentationViewers([]);
            });
        startAttemptsAutoRefresh();
    </script>
</body>
</html>
