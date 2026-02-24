<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

$config_file = __DIR__ . '/control_config.php';
if (!file_exists($config_file)) {
    http_response_code(503);
    echo json_encode(['error' => 'Control config missing']);
    exit;
}
require_once $config_file;

if (!defined('CONTROL_TOKEN')) {
    http_response_code(500);
    echo json_encode(['error' => 'Control config invalid']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$name = trim((string)($data['name'] ?? ''));
$action = trim((string)($data['action'] ?? ''));

if ($name === '' || strlen($name) > 128) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid kiosk name']);
    exit;
}
if ($action !== 'reboot') {
    http_response_code(400);
    echo json_encode(['error' => 'Unsupported action']);
    exit;
}

$actions_file = __DIR__ . '/actions.json';
$actions = [];
if (file_exists($actions_file)) {
    $existing = @file_get_contents($actions_file);
    if ($existing !== false && trim($existing) !== '') {
        $decoded = json_decode($existing, true);
        if (is_array($decoded)) {
            $actions = $decoded;
        }
    }
}

$requested_by = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$actions[$name] = [
    'action' => $action,
    'requested_at' => time(),
    'requested_by' => $requested_by
];

$json_out = json_encode($actions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$fp = @fopen($actions_file, 'c+');
if ($fp === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to open actions file']);
    exit;
}
if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(['error' => 'Unable to lock actions file']);
    exit;
}
ftruncate($fp, 0);
rewind($fp);
$written = fwrite($fp, $json_out);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to write actions file']);
    exit;
}

// Marcar estado visible de reinicio en status.json
$status_file = __DIR__ . '/status.json';
$status_data = [];
if (file_exists($status_file)) {
    $existing_status = @file_get_contents($status_file);
    if ($existing_status !== false && trim($existing_status) !== '') {
        $decoded_status = json_decode($existing_status, true);
        if (is_array($decoded_status)) {
            $status_data = $decoded_status;
        }
    }
}
if (!isset($status_data[$name]) || !is_array($status_data[$name])) {
    $status_data[$name] = [];
}
$status_data[$name]['status'] = 'Reiniciando';
$status_data[$name]['reboot_requested_at'] = time();

$status_out = json_encode($status_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$status_fp = @fopen($status_file, 'c+');
if ($status_fp !== false && flock($status_fp, LOCK_EX)) {
    ftruncate($status_fp, 0);
    rewind($status_fp);
    fwrite($status_fp, $status_out);
    fflush($status_fp);
    flock($status_fp, LOCK_UN);
    fclose($status_fp);
}

echo json_encode(['success' => true, 'queued' => ['name' => $name, 'action' => $action]]);
