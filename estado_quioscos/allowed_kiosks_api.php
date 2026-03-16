<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
require_once __DIR__ . '/app_config.php';

auth_require_json();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'items' => eq_load_allowed_kiosks_for_crud(),
        'protection_enabled' => eq_load_protection_state(),
        'unknown_attempts' => eq_load_unknown_kiosk_attempts(),
        'presentation_viewers' => eq_load_presentation_viewers(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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

$action = trim((string)($data['action'] ?? 'save_items'));
if ($action === 'set_protection') {
    $enabled = !empty($data['enabled']);
    if (!eq_save_protection_state($enabled)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la protección'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    echo json_encode([
        'success' => true,
        'protection_enabled' => eq_load_protection_state(),
        'items' => eq_load_allowed_kiosks_for_crud(),
        'unknown_attempts' => eq_load_unknown_kiosk_attempts(),
        'presentation_viewers' => eq_load_presentation_viewers(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'save_items') {
    $confirmText = trim((string)($data['confirm_text'] ?? ''));
    if ($confirmText !== 'GUARDAR') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Debes escribir GUARDAR para confirmar',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

$items = $data['items'] ?? null;
    if (!is_array($items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Lista inválida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $normalized = [];
    $seen = [];
    foreach ($items as $item) {
        $entry = eq_normalize_allowed_kiosk_item($item);
        if (!is_array($entry)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Hay filas con hostname o IP inválidos'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $key = eq_normalize_hostname($entry['hostname']);
        if (isset($seen[$key])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No puede haber hostnames duplicados'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $seen[$key] = true;
        $normalized[] = $entry;
    }

    if (count($normalized) > 200) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Máximo 200 quioscos'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!eq_save_allowed_kiosks($normalized)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la configuración'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    foreach ($normalized as $entry) {
        eq_remove_unknown_kiosk_attempt((string)$entry['hostname']);
    }

    echo json_encode([
        'success' => true,
        'items' => eq_load_allowed_kiosks_for_crud(),
        'protection_enabled' => eq_load_protection_state(),
        'unknown_attempts' => eq_load_unknown_kiosk_attempts(),
        'presentation_viewers' => eq_load_presentation_viewers(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Acción no válida'], JSON_UNESCAPED_UNICODE);
