<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

function docs_dir_path(): string {
    $candidates = [
        '/var/www/html/docs',
        __DIR__ . '/../kiosk_web/docs',
    ];
    foreach ($candidates as $candidate) {
        if (is_dir($candidate)) {
            return $candidate;
        }
    }
    return '/var/www/html/docs';
}

function is_valid_filename(string $name): bool {
    return (bool)preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/', $name);
}

function is_valid_upload_filename(string $name): bool {
    return (bool)preg_match('/^[0-9][A-Za-z0-9._-]{0,127}$/', $name);
}

function allowed_types(): array {
    return [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
        'mp4' => ['video/mp4'],
    ];
}

function ext_of(string $name): string {
    return strtolower(pathinfo($name, PATHINFO_EXTENSION));
}

function classify_type(string $ext): string {
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return 'imagen';
    }
    if ($ext === 'pdf') {
        return 'pdf';
    }
    if ($ext === 'mp4') {
        return 'video';
    }
    return 'desconocido';
}

function action_list(): void {
    $dir = docs_dir_path();
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $entries = @scandir($dir);
    if (!is_array($entries)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo listar docs'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $items = [];
    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $full = $dir . '/' . $entry;
        if (!is_file($full)) {
            continue;
        }
        if (!is_valid_filename($entry)) {
            continue;
        }
        $ext = ext_of($entry);
        if (!isset(allowed_types()[$ext])) {
            continue;
        }
        $items[] = [
            'name' => $entry,
            'type' => classify_type($ext),
            'size' => (int)@filesize($full),
            'mtime' => (int)@filemtime($full),
            'preview_url' => '/estado_quioscos/docs_preview.php?name=' . rawurlencode($entry),
        ];
    }

    usort($items, static function (array $a, array $b): int {
        return strnatcasecmp((string)$a['name'], (string)$b['name']);
    });

    echo json_encode(['success' => true, 'items' => $items], JSON_UNESCAPED_UNICODE);
    exit;
}

function action_upload(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!auth_verify_csrf((string)($_POST['csrf_token'] ?? ''))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'CSRF inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!isset($_FILES['file']) || !is_array($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Archivo requerido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $file = $_FILES['file'];
    $errorCode = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorCode !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Error en subida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $origName = (string)($file['name'] ?? '');
    $baseName = basename($origName);
    if (!is_valid_upload_filename($baseName)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nombre inválido. Debe empezar por número y usar solo letras, números, ., -, _'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $ext = ext_of($baseName);
    $allowed = allowed_types();
    if (!isset($allowed[$ext])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Extensión no permitida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tmpPath = (string)($file['tmp_name'] ?? '');
    if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Subida inválida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $maxBytes = 200 * 1024 * 1024;
    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > $maxBytes) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Tamaño no permitido (máx 200MB)'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmpPath);
    if (!in_array($mime, $allowed[$ext], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'MIME no permitido para la extensión'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $dir = docs_dir_path();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo crear directorio docs'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $target = $dir . '/' . $baseName;
    $exists = is_file($target);
    $overwrite = (string)($_POST['overwrite'] ?? '0') === '1';

    if ($exists && !$overwrite) {
        echo json_encode([
            'success' => false,
            'error' => 'exists',
            'message' => 'El archivo ya existe',
            'name' => $baseName,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!@move_uploaded_file($tmpPath, $target)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar el archivo'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    @chmod($target, 0644);

    echo json_encode([
        'success' => true,
        'name' => $baseName,
        'overwritten' => $exists,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function action_delete(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $raw = file_get_contents('php://input');
    $data = json_decode((string)$raw, true);
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!auth_verify_csrf((string)($data['csrf_token'] ?? ''))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'CSRF inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $name = trim((string)($data['name'] ?? ''));
    if (!is_valid_filename($name)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nombre inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $dir = docs_dir_path();
    $target = $dir . '/' . $name;
    if (!is_file($target)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'No existe'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!@unlink($target)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo eliminar'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode(['success' => true, 'name' => $name], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = trim((string)($_GET['action'] ?? $_POST['action'] ?? 'list'));
switch ($action) {
    case 'list':
        action_list();
        break;
    case 'upload':
        action_upload();
        break;
    case 'delete':
        action_delete();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
        exit;
}
