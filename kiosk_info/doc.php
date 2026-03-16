<?php
require_once __DIR__ . '/../estado_quioscos/auth_lib.php';

if (!auth_is_authenticated()) {
    http_response_code(401);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No autenticado\n";
    exit;
}

$allowed = [
    'MANUAL_USUARIO.md',
    'MANUAL_TECNICO.md',
    'REGISTRO_CAMBIOS.md',
    'GUIA_USO_APP_RESPALDO.md',
    'CHANGELOG.md',
    'GUIA_RAPIDA.md',
    'GUIA_VIDEOS.md',
    'RESUMEN_MEJORAS.md',
];

$file = basename((string)($_GET['f'] ?? ''));
if ($file === '' || !in_array($file, $allowed, true)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Archivo no permitido\n";
    exit;
}

$path = __DIR__ . '/src/' . $file;
if (!is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Archivo no encontrado\n";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
readfile($path);
