<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

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

$action = trim((string)($data['action'] ?? ''));
$confirm = trim((string)($data['confirm'] ?? ''));

$commands = [
    'restart_web' => "sudo -n systemctl restart nginx php8.2-fpm",
    'reboot_server' => "sudo -n shutdown -r +1 'Reinicio solicitado desde estado_quioscos'"
];

if (!isset($commands[$action])) {
    http_response_code(400);
    echo json_encode(['error' => 'Unsupported action']);
    exit;
}

if ($action === 'reboot_server' && strtoupper($confirm) !== 'REINICIAR') {
    http_response_code(400);
    echo json_encode(['error' => 'Confirmation required']);
    exit;
}

$output = [];
$code = 1;
exec($commands[$action] . ' 2>&1', $output, $code);

$ok = ($code === 0);
if (!$ok) {
    http_response_code(500);
}

echo json_encode([
    'success' => $ok,
    'action' => $action,
    'exit_code' => $code,
    'output' => implode("\n", $output)
], JSON_UNESCAPED_UNICODE);
