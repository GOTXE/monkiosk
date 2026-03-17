<?php
require_once __DIR__ . '/../estado_quioscos/auth_lib.php';
auth_require_page();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Documentación Monkiosk</title>
  <link rel="stylesheet" href="assets/github-markdown.min.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; height: 100vh; }
    #sidebar { width: 25%; background-color: #0b1220; color:#fff; padding: 20px; border-right: 1px solid #0b2136; overflow-y: auto; }
    #sidebar h2 { margin-top: 0; }
    #sidebar ul { list-style-type: none; padding: 0; }
    #sidebar li { margin-bottom: 10px; }
    #sidebar a { text-decoration: none; color: #cbd5e1; padding: 10px; display: block; border-radius: 6px; transition: background-color 0.2s; }
    #sidebar a:hover { background-color: #071227; color:#fff }
    #sidebar a.active { background: linear-gradient(90deg,#4ea3ff,#2b78d9); color:#fff; font-weight: bold; }
    #content { width: 75%; padding: 20px; overflow-y: auto; background:#f5f7fa }
  </style>
  <script src="assets/marked.min.js"></script>
</head>
<body>
  <div id="sidebar">
      <h2>Documentación Monkiosk</h2>
      <small style="color:#94a3b8">Manuales principales y respaldo local</small>
      <ul id="file-list"></ul>
  </div>
  <div id="content">
      <div class="markdown-body" id="markdown-content">
          <p>Selecciona un archivo de la izquierda para ver su contenido.</p>
      </div>
  </div>

  <script>
    const mdFiles = [
      { file: 'MANUAL_USUARIO.md', label: 'Manual Usuario', section: 'Manuales' },
      { file: 'MANUAL_TECNICO.md', label: 'Manual Técnico', section: 'Manuales' },
      { file: 'REGISTRO_CAMBIOS.md', label: 'Registro Cambios', section: 'Manuales' },
      { file: 'GUIA_USO_APP_RESPALDO.md', label: 'Guía Uso App', section: 'Respaldo' },
      { file: 'CHANGELOG.md', label: 'CHANGELOG', section: 'Respaldo' },
      { file: 'GUIA_RAPIDA.md', label: 'GUIA_RAPIDA', section: 'Respaldo' },
      { file: 'RESUMEN_MEJORAS.md', label: 'RESUMEN_MEJORAS', section: 'Respaldo' }
    ];

    function unwrapOuterFence(text){
      const lines = text.split(/\r?\n/);
      while(lines.length && /^\s*`{3,}.*$/.test(lines[0])) lines.shift();
      while(lines.length && /^\s*`{3,}.*$/.test(lines[lines.length-1])) lines.pop();
      return lines.join('\n');
    }

    function loadSidebar(){
      const ul = document.getElementById('file-list');
      let currentSection = '';
      mdFiles.forEach((entry, i) => {
        if (entry.section !== currentSection) {
          currentSection = entry.section;
          const sectionLi = document.createElement('li');
          sectionLi.textContent = currentSection;
          sectionLi.style.marginTop = i === 0 ? '0' : '16px';
          sectionLi.style.marginBottom = '8px';
          sectionLi.style.color = '#94a3b8';
          sectionLi.style.fontSize = '0.82rem';
          sectionLi.style.fontWeight = '700';
          ul.appendChild(sectionLi);
        }
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = '#';
        a.textContent = entry.label;
        a.dataset.file = entry.file;
        a.addEventListener('click', loadContent);
        li.appendChild(a);
        ul.appendChild(li);
        if (i === 0) setTimeout(() => a.click(), 0);
      });
    }

    function loadContent(event){
      event.preventDefault();
      const file = event.target.dataset.file;
      const contentDiv = document.getElementById('markdown-content');
      document.querySelectorAll('#sidebar a').forEach(a => a.classList.remove('active'));
      event.target.classList.add('active');
      const cacheBust = `v=${Date.now()}`;
      fetch(`doc.php?f=${encodeURIComponent(file)}&${cacheBust}`, { cache: 'no-store' })
        .then(r => { if(!r.ok) throw new Error('No se pudo cargar'); return r.text(); })
        .then(text => {
          text = unwrapOuterFence(text);
          contentDiv.innerHTML = marked.parse(text);
          contentDiv.setAttribute('data-file', file);
        })
        .catch(err => {
          contentDiv.innerHTML = `<p>Error al cargar el archivo: ${err.message}</p>`;
        });
    }

    window.addEventListener('load', loadSidebar);
  </script>
</body>
</html>
