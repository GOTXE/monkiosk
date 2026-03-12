<?php

function auth_session_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name('estado_quioscos_sid');
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => false
        ]);
    }
}

function auth_users_file(): string {
    return __DIR__ . '/auth_users.json';
}

function auth_load_users(): array {
    $path = auth_users_file();
    if (!is_file($path)) {
        return [];
    }
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $json = json_decode($raw, true);
    return is_array($json) ? $json : [];
}

function auth_resolve_username(array $users, string $username): string {
    $needle = strtolower(trim($username));
    if ($needle === '') {
        return '';
    }
    foreach ($users as $candidate => $_hash) {
        if (strtolower((string)$candidate) === $needle) {
            return (string)$candidate;
        }
    }
    return '';
}

function auth_is_authenticated(): bool {
    auth_session_start();
    return isset($_SESSION['auth_user']) && is_string($_SESSION['auth_user']) && $_SESSION['auth_user'] !== '';
}

function auth_current_user(): string {
    auth_session_start();
    return (string)($_SESSION['auth_user'] ?? '');
}

function auth_require_page(): void {
    if (auth_is_authenticated()) {
        return;
    }
    header('Location: /estado_quioscos/login.php');
    exit;
}

function auth_require_json(): void {
    if (auth_is_authenticated()) {
        return;
    }
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

function auth_csrf_token(): string {
    auth_session_start();
    $token = (string)($_SESSION['csrf_token'] ?? '');
    if ($token !== '') {
        return $token;
    }
    $token = bin2hex(random_bytes(24));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function auth_verify_csrf(?string $token): bool {
    auth_session_start();
    $current = (string)($_SESSION['csrf_token'] ?? '');
    $given = trim((string)$token);
    return $current !== '' && $given !== '' && hash_equals($current, $given);
}
