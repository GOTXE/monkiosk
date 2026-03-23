<?php
header('Content-Type: application/json; charset=utf-8');

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

$token = (string)($_SERVER['HTTP_X_CONTROL_TOKEN'] ?? '');
if ($token === '' || !hash_equals(CONTROL_TOKEN, $token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$name = trim((string)($_GET['name'] ?? ''));
if ($name === '' || strlen($name) > 128) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid kiosk name']);
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

$action = 'none';
$meta = null;
if (isset($actions[$name]) && is_array($actions[$name])) {
    $action = (string)($actions[$name]['action'] ?? 'none');
    $meta = $actions[$name];
    unset($actions[$name]); // Consumo único de orden.

    $json_out = json_encode($actions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fp = @fopen($actions_file, 'c+');
    if ($fp !== false && flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $json_out);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

echo json_encode([
    'success' => true,
    'name' => $name,
    'action' => $action,
    'meta' => $meta
]);

