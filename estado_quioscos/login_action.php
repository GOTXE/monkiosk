<?php
require_once __DIR__ . '/auth_lib.php';
auth_session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /estado_quioscos/login.php');
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: /estado_quioscos/login.php?e=' . urlencode('Credenciales obligatorias'));
    exit;
}

$users = auth_load_users();
$stored = (string)($users[$username] ?? '');

if ($stored === '' || !password_verify($password, $stored)) {
    header('Location: /estado_quioscos/login.php?e=' . urlencode('Usuario o contraseña incorrectos'));
    exit;
}

$_SESSION['auth_user'] = $username;
$_SESSION['auth_time'] = time();

header('Location: /estado_quioscos/');
exit;
