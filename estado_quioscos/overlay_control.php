<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

$overlay_path = __DIR__ . '/overlay_config.json';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON'], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = trim((string)($data['action'] ?? ''));
$target = trim((string)($data['target_kiosk'] ?? ''));

if ($action !== 'enable' && $action !== 'disable') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unsupported action'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'enable' && $target === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'target_kiosk requerido'], JSON_UNESCAPED_UNICODE);
    exit;
}

$payload = [
    'enabled' => $action === 'enable',
    'target_kiosk' => $action === 'enable' ? $target : '',
    'updated_at' => date('Y-m-d H:i:s')
];

$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (!is_string($json) || @file_put_contents($overlay_path, $json . "\n", LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar configuracion'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'enabled' => $payload['enabled'],
    'target_kiosk' => $payload['target_kiosk'],
    'updated_at' => $payload['updated_at']
], JSON_UNESCAPED_UNICODE);
