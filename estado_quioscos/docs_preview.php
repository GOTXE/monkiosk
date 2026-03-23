<?php
require_once __DIR__ . '/auth_lib.php';
require_once __DIR__ . '/app_config.php';
auth_require_page();

function docs_dir_path(): string {
    return eq_docs_dir();
}

function is_valid_filename(string $name): bool {
    return (bool)preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/', $name);
}

$name = trim((string)($_GET['name'] ?? ''));
if (!is_valid_filename($name)) {
    http_response_code(400);
    echo 'Nombre inválido';
    exit;
}

$path = docs_dir_path() . '/' . $name;
if (!is_file($path)) {
    http_response_code(404);
    echo 'Archivo no encontrado';
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = (string)$finfo->file($path);
if ($mime === '') {
    $mime = 'application/octet-stream';
}

header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $mime);
header('Content-Length: ' . (string)filesize($path));
header('Content-Disposition: inline; filename="' . rawurlencode($name) . '"');
readfile($path);
