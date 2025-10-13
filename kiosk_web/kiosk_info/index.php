<?php
// Solo permitir acceso desde localhost
$allowed = ['127.0.0.1', '::1', 'localhost'];
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, $allowed)) {
    header('HTTP/1.1 403 Forbidden');
    echo "<h1>403 Forbidden</h1><p>Access to kiosk_info is restricted to the server (localhost).</p>";
    exit;
}

require_once __DIR__ . '/Parsedown.php';
$pd = new Parsedown();

// Buscar los .md desde la raíz del repo (evita duplicados)
$root = realpath(__DIR__ . '/../../');
$mdCandidates = [
  $root . '/GUIA_RAPIDA.md',
  $root . '/GUIA_VIDEOS.md',
  $root . '/CHANGELOG.md',
  $root . '/estructura.md',
  $root . '/RESUMEN_MEJORAS.md',
  $root . '/tools/README.md',
  $root . '/kiosk_web/docs/README.md'
];
$files = [];
foreach ($mdCandidates as $c) {
  if (file_exists($c)) $files[] = $c;
}

// Sort by name
natcasesort($files);

$selected = $_GET['file'] ?? basename(reset($files));
$selectedPath = $dir . $selected;
if (!file_exists($selectedPath) || pathinfo($selectedPath, PATHINFO_EXTENSION) !== 'md') {
    $selectedPath = reset($files);
}

$md = file_get_contents($selectedPath);
$html = $pd->text($md);

?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kiosk Info - Documentación local</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#f7f7f7;color:#222}
    .sidebar{width:280px;position:fixed;left:0;top:0;bottom:0;background:#111;color:#fff;padding:12px;overflow:auto}
    .content{margin-left:300px;padding:24px}
    a{color:#4ea3ff}
    pre{background:#222;color:#f7f7f7;padding:12px;overflow:auto}
    code{background:#eee;padding:2px 4px;border-radius:4px}
    h1,h2,h3{color:#111}
    .note{font-size:0.9em;color:#666}
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Kiosk Info</h2>
    <p class="note">Acceso local. Archivos MD disponibles:</p>
    <ul>
    <?php foreach ($files as $f): $n = basename($f); ?>
      <li><a href="?file=<?php echo urlencode($n) ?>" <?php if ($n==basename($selectedPath)) echo 'style="font-weight:bold"';?>><?php echo htmlspecialchars($n) ?></a></li>
    <?php endforeach; ?>
    </ul>
    <hr>
    <p class="note">Visor local generado automáticamente.</p>
  </div>
  <div class="content">
    <h1><?php echo htmlspecialchars(basename($selectedPath)) ?></h1>
    <div><?php echo $html ?></div>
  </div>
</body>
</html>
