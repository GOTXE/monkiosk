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
            margin: 0 0 10px;
            font-size: 1.05rem;
            color: #1f4d86;
        }
        .status {
            min-height: 20px;
            margin-bottom: 8px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
        }
        .status.ok { color: var(--ok); }
        .status.err { color: var(--danger); }
        .upload-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
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
            max-height: 62vh;
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
            height: 62vh;
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
            .layout {
                grid-template-columns: minmax(560px, 1.12fr) minmax(380px, 1fr);
            }
            .files {
                max-height: 58vh;
            }
            .preview-wrap {
                height: 58vh;
            }
        }
        @media (max-width: 1180px) {
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
            .files {
                max-height: 54vh;
            }
            .preview-wrap {
                height: 54vh;
                min-height: 300px;
            }
            th, td {
                padding: 6px;
            }
        }
        @media (max-width: 1024px) {
            .layout {
                grid-template-columns: 1fr;
            }
            .files {
                max-height: 45vh;
            }
            .preview-wrap {
                height: 45vh;
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
                    <input id="file-input" type="file" aria-label="Seleccionar archivo">
                    <button id="upload-btn" class="btn" type="button">Subir</button>
                </div>
                <p class="upload-hint">Permitidos: .jpg .jpeg .png .webp .pdf .mp4 | Máx 200MB | Nombre: debe empezar por número y usar letras, números, ., -, _</p>
                <p class="upload-hint">Ejemplo nombre: <strong>01_portada.pdf</strong> o <strong>02_slide.webp</strong></p>

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
    </div>

    <script>
        const csrfToken = <?= json_encode($csrfToken, JSON_UNESCAPED_UNICODE) ?>;
        const statusEl = document.getElementById('status');
        const filesBody = document.getElementById('files-body');
        const fileInput = document.getElementById('file-input');
        const uploadBtn = document.getElementById('upload-btn');
        const previewEl = document.getElementById('preview');

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

        function clearPreview(message) {
            previewEl.innerHTML = `<div class="preview-msg">${esc(message)}</div>`;
        }

        function showPreview(item) {
            const name = String(item.name || '');
            const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
            const url = String(item.preview_url || '');
            if (!url) {
                clearPreview('No hay URL de previsualización');
                return;
            }

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
                const data = await response.json();
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
            const data = await response.json();

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
            await fetchList();
        }

        async function deleteFile(name) {
            try {
                const response = await fetch('docs_api.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, csrf_token: csrfToken })
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'No se pudo eliminar');
                }
                setStatus(`Archivo eliminado: ${data.name}`, 'ok');
                await fetchList();
            } catch (error) {
                setStatus(`Error eliminando: ${error.message}`, 'err');
            }
        }

        uploadBtn.addEventListener('click', async () => {
            try {
                await uploadFile(false);
            } catch (error) {
                setStatus(`Error subiendo: ${error.message}`, 'err');
            }
        });

        fetchList();
    </script>
</body>
</html>
