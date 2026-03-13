<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
require_once __DIR__ . '/app_config.php';

$settings_file = eq_slide_settings_file();
$default_seconds = 5;

function normalize_seconds(int $seconds): int {
    if ($seconds < 5) {
        $seconds = 5;
    }
    if ($seconds > 120) {
        $seconds = 120;
    }
    if ($seconds % 5 !== 0) {
        $seconds = (int)(round($seconds / 5) * 5);
    }
    return $seconds;
}

function read_slide_seconds(string $path, int $default): int {
    if (!is_file($path)) {
        return $default;
    }
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return $default;
    }
    $json = json_decode($raw, true);
    if (!is_array($json)) {
        return $default;
    }
    return normalize_seconds((int)($json['slide_interval_seconds'] ?? $default));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'success' => true,
        'slide_interval_seconds' => read_slide_seconds($settings_file, $default_seconds)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

auth_require_json();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON'], JSON_UNESCAPED_UNICODE);
    exit;
}

$seconds = normalize_seconds((int)($data['slide_interval_seconds'] ?? $default_seconds));
$payload = json_encode([
    'slide_interval_seconds' => $seconds,
    'updated_at' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (!is_string($payload) || @file_put_contents($settings_file, $payload . "\n", LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'slide_interval_seconds' => $seconds
], JSON_UNESCAPED_UNICODE);
