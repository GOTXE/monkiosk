<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';

echo json_encode([
    'success' => true,
    'authenticated' => auth_is_authenticated(),
    'user' => auth_current_user()
], JSON_UNESCAPED_UNICODE);
