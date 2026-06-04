<?php
require_once __DIR__ . '/auth_lib.php';
auth_require_page();
$csrfToken = auth_csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Documentos</title>
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
            --docs-panel-height: 62vh;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Verdana, sans-serif;
            background: var(--bg);
            color: #eaf2ff;
        }
        .page {
            width: min(1400px, 100%);
            margin: 0 auto;
            padding: 16px;
            min-height: 100vh;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px;
        }
        .topbar h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .topbar-actions {
            display: inline-flex;
            gap: 8px;
        }
        .btn {
            border: 1px solid #2a70c8;
            background: var(--btn);
            color: #fff;
            border-radius: 10px;
            padding: 9px 12px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn:hover { background: var(--btn-hover); }
        .upload-row .btn {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.86rem;
        }
        .btn-danger {
            border-color: #b5252d;
            background: #d94848;
        }
        .btn-danger:hover { background: #be3434; }
        .layout {
            display: grid;
            grid-template-columns: minmax(620px, 1.15fr) minmax(420px, 1fr);
            gap: 12px;
            align-items: start;
        }
        .layout > * {
            min-width: 0;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px;
            color: var(--ink);
            min-height: 0;
            min-width: 0;
        }
        .card h2 {
            margin: 0 0 6px;
            font-size: 1.05rem;
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
        .status {
            min-height: 20px;
            margin-bottom: 4px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
        }
        #selected-file {
            text-align: center;
        }
        .status.ok { color: var(--ok); }
        .status.warn { color: #a35300; }
        .status.err { color: var(--danger); }
        .upload-row {
            display: grid;
            grid-template-columns: auto auto;
            gap: 8px;
            align-items: center;
            margin-bottom: 4px;
        }
        .file-input-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        .upload-hint {
            color: var(--muted);
            font-size: 0.82rem;
            margin: 0 0 10px;
            overflow-wrap: anywhere;
        }
        .files {
            border: 1px solid #d7e1ee;
            border-radius: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            background: #f8fbff;
            height: calc(var(--docs-panel-height) - 124px);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            table-layout: fixed;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e4ebf4;
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        th {
            color: var(--muted);
            font-size: 0.8rem;
        }
        th:nth-child(1), td:nth-child(1) { width: 32%; }
        th:nth-child(2), td:nth-child(2) { width: 10%; }
        th:nth-child(3), td:nth-child(3) { width: 13%; }
        th:nth-child(4), td:nth-child(4) { width: 19%; }
        th:nth-child(5), td:nth-child(5) { width: 26%; }
        .row-actions {
            display: inline-flex;
            flex-wrap: nowrap;
            gap: 4px;
        }
        .small-btn {
            border: 1px solid #c8d7ea;
            background: #ffffff;
            color: #1f4d86;
            border-radius: 8px;
            padding: 4px 6px;
            font-size: 0.74rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }
        .small-btn:hover { background: #edf4ff; }
        .small-btn.active-preview {
            color: #a35300;
            border-color: #f0d1a8;
            background: #fff4e5;
        }
        .small-btn.danger {
            color: #a61e2a;
            border-color: #e8bcc1;
            background: #fff6f6;
        }
        .small-btn.danger:hover { background: #ffe9ea; }
        .preview-wrap {
            border: 1px solid #d7e1ee;
            border-radius: 10px;
            background: #f8fbff;
            height: var(--docs-panel-height);
            min-height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .preview-msg {
            color: var(--muted);
            font-weight: 700;
            padding: 12px;
            text-align: center;
        }
        .preview-wrap iframe,
        .preview-wrap img,
        .preview-wrap video {
            width: 100%;
            height: 100%;
            border: 0;
            object-fit: contain;
            background: #fff;
        }
        @media (max-width: 1400px) {
            :root {
                --docs-panel-height: 58vh;
            }
            .layout {
                grid-template-columns: minmax(560px, 1.12fr) minmax(380px, 1fr);
            }
        }
        @media (max-width: 1180px) {
            :root {
                --docs-panel-height: 54vh;
            }
            .page {
                padding: 12px;
            }
            .layout {
                grid-template-columns: 1fr 0.95fr;
                gap: 10px;
            }
            .card {
                padding: 10px;
            }
            .preview-wrap { min-height: 300px; }
            th, td {
                padding: 6px;
            }
        }
        @media (max-width: 1024px) {
            :root {
                --docs-panel-height: 45vh;
            }
            .layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="topbar">
            <h1>Gestión de Documentos</h1>
            <div class="topbar-actions">
                <a class="btn" href="/estado_quioscos/">Volver a Estado</a>
            </div>
        </div>

        <div class="layout">
            <section class="card">
                <h2>Archivos en /docs</h2>
                <div id="status" class="status"></div>

                <div class="upload-row">
                    <input id="file-input" class="file-input-hidden" type="file" aria-label="Seleccionar archivo">
                    <button id="select-file-btn" class="btn" type="button">Seleccionar archivo</button>
                    <button id="upload-btn" class="btn" type="button">Subir</button>
                </div>
                <div id="selected-file" class="status" style="margin-top:-2px;"></div>
                <p class="upload-hint">Permitidos: .jpg .jpeg .png .webp .pdf .mp4 | Máx 200MB</p>
                <p class="upload-hint">Nombre: debe empezar por número y usar letras, números, ., -, _ | Ejemplo: <strong>4_texto.extension</strong></p>

                <div class="files">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Tamaño</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="files-body"></tbody>
                    </table>
                </div>
            </section>

            <section class="card">
                <h2>Vista previa</h2>
                <div id="preview" class="preview-wrap">
                    <div class="preview-msg">Selecciona un archivo para previsualizar</div>
                </div>
            </section>
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

    <script>
        const csrfToken = <?= json_encode($csrfToken, JSON_UNESCAPED_UNICODE) ?>;
        const statusEl = document.getElementById('status');
        const filesBody = document.getElementById('files-body');
        const fileInput = document.getElementById('file-input');
        const selectFileBtn = document.getElementById('select-file-btn');
        const uploadBtn = document.getElementById('upload-btn');
        const selectedFileEl = document.getElementById('selected-file');
        const previewEl = document.getElementById('preview');
        let statusClearTimer = null;
        let currentPreviewName = '';

        const extType = {
            jpg: 'imagen',
            jpeg: 'imagen',
            png: 'imagen',
            webp: 'imagen',
            pdf: 'pdf',
            mp4: 'video'
        };

        function setStatus(msg, mode = '') {
            statusEl.textContent = msg;
            statusEl.className = `status ${mode}`.trim();
            if (statusClearTimer) {
                window.clearTimeout(statusClearTimer);
                statusClearTimer = null;
            }
            if (mode === 'err' && msg) {
                statusClearTimer = window.setTimeout(() => {
                    statusEl.textContent = '';
                    statusEl.className = 'status';
                    statusClearTimer = null;
                }, 5000);
            }
        }

        function setSelectedFileStatus(msg = '', mode = '') {
            if (!selectedFileEl) return;
            selectedFileEl.textContent = msg;
            selectedFileEl.className = `status ${mode}`.trim();
        }

        function esc(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function fmtBytes(bytes) {
            const n = Number(bytes || 0);
            if (!Number.isFinite(n) || n <= 0) return '-';
            if (n >= 1024 * 1024) return `${(n / (1024 * 1024)).toFixed(1)} MB`;
            if (n >= 1024) return `${(n / 1024).toFixed(1)} KB`;
            return `${n} B`;
        }

        function fmtDate(ts) {
            const n = Number(ts || 0);
            if (!n) return '-';
            return new Date(n * 1000).toLocaleDateString('es-ES');
        }

        async function readJsonResponse(response) {
            const raw = await response.text();
            try {
                return raw ? JSON.parse(raw) : {};
            } catch (_error) {
                if (response.status === 413) {
                    throw new Error('El archivo es demasiado grande para la configuracion del servidor');
                }
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('text/html')) {
                    throw new Error(`El servidor devolvio HTML en lugar de JSON (HTTP ${response.status})`);
                }
                throw new Error(`Respuesta no JSON del servidor (HTTP ${response.status})`);
            }
        }

        function clearPreview(message) {
            currentPreviewName = '';
            updatePreviewButtonsState();
            previewEl.innerHTML = `<div class="preview-msg">${esc(message)}</div>`;
        }

        function updatePreviewButtonsState() {
            filesBody.querySelectorAll('button[data-preview]').forEach((btn) => {
                const raw = decodeURIComponent(btn.getAttribute('data-preview') || '');
                let itemName = '';
                try {
                    const item = JSON.parse(raw);
                    itemName = String(item.name || '');
                } catch (_e) {
                    itemName = '';
                }
                btn.classList.toggle('active-preview', itemName !== '' && itemName === currentPreviewName);
            });
        }

        function showPreview(item) {
            const name = String(item.name || '');
            const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
            const url = String(item.preview_url || '');
            if (!url) {
                clearPreview('No hay URL de previsualización');
                return;
            }
            currentPreviewName = name;
            updatePreviewButtonsState();

            if (extType[ext] === 'imagen') {
                previewEl.innerHTML = `<img src="${esc(url)}" alt="${esc(name)}">`;
                return;
            }
            if (extType[ext] === 'pdf') {
                previewEl.innerHTML = `<iframe src="${esc(url)}" title="Vista previa PDF"></iframe>`;
                return;
            }
            if (extType[ext] === 'video') {
                previewEl.innerHTML = `<video src="${esc(url)}" controls preload="metadata"></video>`;
                return;
            }
            clearPreview('Sin vista previa disponible para este tipo de archivo');
        }

        async function fetchList() {
            try {
                const response = await fetch(`docs_api.php?action=list&_ts=${Date.now()}`, { cache: 'no-store' });
                const data = await readJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo listar');
                }

                const items = Array.isArray(data.items) ? data.items : [];
                filesBody.innerHTML = items.map((item) => {
                    const payload = encodeURIComponent(JSON.stringify(item));
                    return `
                        <tr>
                            <td>${esc(item.name || '')}</td>
                            <td>${esc(item.type || '-')}</td>
                            <td>${fmtBytes(item.size)}</td>
                            <td>${fmtDate(item.mtime)}</td>
                            <td>
                                <div class="row-actions">
                                    <button class="small-btn" type="button" data-preview="${payload}">Vista previa</button>
                                    <button class="small-btn danger" type="button" data-delete="${esc(item.name || '')}">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                if (!items.length) {
                    filesBody.innerHTML = '<tr><td colspan="5">No hay archivos</td></tr>';
                    clearPreview('No hay archivos para previsualizar');
                }

                filesBody.querySelectorAll('button[data-preview]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const raw = decodeURIComponent(btn.getAttribute('data-preview') || '');
                        try {
                            const item = JSON.parse(raw);
                            showPreview(item);
                        } catch (_e) {
                            clearPreview('No se pudo cargar vista previa');
                        }
                    });
                });
                updatePreviewButtonsState();

                filesBody.querySelectorAll('button[data-delete]').forEach((btn) => {
                    btn.addEventListener('click', async () => {
                        const name = String(btn.getAttribute('data-delete') || '').trim();
                        if (!name) return;
                        const ok = window.confirm(`¿Eliminar ${name}? Esta acción no se puede deshacer.`);
                        if (!ok) return;
                        await deleteFile(name);
                    });
                });

                setStatus(`Total archivos: ${items.length}`);
            } catch (error) {
                setStatus(`Error listando archivos: ${error.message}`, 'err');
            }
        }

        async function uploadFile(overwrite = false) {
            const file = fileInput.files && fileInput.files[0];
            if (!file) {
                setStatus('Selecciona un archivo primero', 'err');
                return;
            }

            const fd = new FormData();
            fd.append('action', 'upload');
            fd.append('csrf_token', csrfToken);
            fd.append('overwrite', overwrite ? '1' : '0');
            fd.append('file', file);

            const response = await fetch('docs_api.php', {
                method: 'POST',
                body: fd
            });
            const data = await readJsonResponse(response);

            if (data && data.error === 'exists' && !overwrite) {
                const confirmOverwrite = window.confirm(`El archivo ${file.name} ya existe. ¿Sobrescribir?`);
                if (confirmOverwrite) {
                    await uploadFile(true);
                }
                return;
            }

            if (!response.ok || !data.success) {
                throw new Error((data && (data.message || data.error)) || 'No se pudo subir');
            }

            setStatus(
                data.overwritten
                    ? `Archivo sobrescrito: ${data.name}`
                    : `Archivo subido: ${data.name}`,
                'ok'
            );
            fileInput.value = '';
            setSelectedFileStatus('');
            await fetchList();
        }

        async function deleteFile(name) {
            try {
                const response = await fetch('docs_api.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, csrf_token: csrfToken })
                });
                const data = await readJsonResponse(response);
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo eliminar');
                }
                setStatus(`Archivo eliminado: ${data.name}`, 'ok');
                if (currentPreviewName && currentPreviewName === data.name) {
                    clearPreview('Selecciona un archivo para previsualizar');
                }
                await fetchList();
            } catch (error) {
                setStatus(`Error eliminando: ${error.message}`, 'err');
            }
        }

        if (selectFileBtn) {
            selectFileBtn.addEventListener('click', () => {
                fileInput.click();
            });
        }

        uploadBtn.addEventListener('click', async () => {
            try {
                await uploadFile(false);
            } catch (error) {
                setStatus(`Error subiendo: ${error.message}`, 'err');
            }
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            if (!file) {
                setSelectedFileStatus('');
                return;
            }
            setSelectedFileStatus(`Archivo seleccionado: ${file.name}`, 'warn');
        });

        fetchList();
    </script>
</body>
</html>
