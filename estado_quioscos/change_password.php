<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

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

$auth_user = auth_current_user();
$current = (string)($data['current_password'] ?? '');
$new = (string)($data['new_password'] ?? '');
$confirm = (string)($data['confirm_password'] ?? '');

if ($auth_user === '') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($current === '' || $new === '' || $confirm === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Campos incompletos'], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($new !== $confirm) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'La nueva contrasena no coincide'], JSON_UNESCAPED_UNICODE);
    exit;
}
if (strlen($new) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Minimo 8 caracteres'], JSON_UNESCAPED_UNICODE);
    exit;
}

$users = auth_load_users();
$stored = (string)($users[$auth_user] ?? '');
if ($stored === '' || !password_verify($current, $stored)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Contrasena actual incorrecta'], JSON_UNESCAPED_UNICODE);
    exit;
}

$users[$auth_user] = password_hash($new, PASSWORD_DEFAULT);
$payload = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (!is_string($payload) || @file_put_contents(auth_users_file(), $payload . "\n", LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar nueva contrasena'], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => true,
    'user' => $auth_user,
    'message' => 'Contrasena actualizada'
], JSON_UNESCAPED_UNICODE);
