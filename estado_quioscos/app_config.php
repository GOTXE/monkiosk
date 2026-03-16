<?php

function eq_array_merge_recursive_distinct(array $base, array $override): array {
    foreach ($override as $key => $value) {
        if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
            $base[$key] = eq_array_merge_recursive_distinct($base[$key], $value);
            continue;
        }
        $base[$key] = $value;
    }
    return $base;
}

function eq_config(): array {
    static $config = null;
    if (is_array($config)) {
        return $config;
    }

    $defaults = [
        'docs_dir' => '/var/www/html/docs',
        'slide_settings_file' => __DIR__ . '/slide_settings.json',
        'allowed_kiosks_file' => __DIR__ . '/allowed_kiosks.json',
        'legacy_allowed_hosts_file' => __DIR__ . '/allowed_hosts.txt',
        'offline_timeout_seconds' => 150,
        'server' => [
            'php_fpm_unit' => 'php8.2-fpm',
        ],
    ];

    $localPath = __DIR__ . '/config.local.php';
    $override = [];
    if (is_file($localPath)) {
        $loaded = require $localPath;
        if (is_array($loaded)) {
            $override = $loaded;
        }
    }

    $config = eq_array_merge_recursive_distinct($defaults, $override);
    return $config;
}

function eq_docs_dir(): string {
    $path = (string)(eq_config()['docs_dir'] ?? '/var/www/html/docs');
    return $path !== '' ? $path : '/var/www/html/docs';
}

function eq_slide_settings_file(): string {
    $path = (string)(eq_config()['slide_settings_file'] ?? (__DIR__ . '/slide_settings.json'));
    return $path !== '' ? $path : (__DIR__ . '/slide_settings.json');
}

function eq_allowed_kiosks_file(): string {
    $path = (string)(eq_config()['allowed_kiosks_file'] ?? (__DIR__ . '/allowed_kiosks.json'));
    return $path !== '' ? $path : (__DIR__ . '/allowed_kiosks.json');
}

function eq_status_file(): string {
    return __DIR__ . '/status.json';
}

function eq_protection_file(): string {
    return __DIR__ . '/allowed_kiosks_protection.json';
}

function eq_unknown_attempts_file(): string {
    return __DIR__ . '/unknown_kiosk_attempts.json';
}

function eq_legacy_allowed_hosts_file(): string {
    $path = (string)(eq_config()['legacy_allowed_hosts_file'] ?? (__DIR__ . '/allowed_hosts.txt'));
    return $path !== '' ? $path : (__DIR__ . '/allowed_hosts.txt');
}

function eq_offline_timeout_seconds(): int {
    $value = (int)(eq_config()['offline_timeout_seconds'] ?? 150);
    return $value > 0 ? $value : 150;
}

function eq_php_fpm_unit(): string {
    $unit = (string)(eq_config()['server']['php_fpm_unit'] ?? 'php8.2-fpm');
    return $unit !== '' ? $unit : 'php8.2-fpm';
}

function eq_hostname_is_valid(string $hostname): bool {
    return (bool)preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]{0,127}$/', $hostname);
}

function eq_normalize_hostname(string $hostname): string {
    return strtolower(trim($hostname));
}

function eq_normalize_ip(string $ip): string {
    return trim($ip);
}

function eq_is_valid_ip_or_empty(string $ip): bool {
    if ($ip === '') {
        return true;
    }
    return filter_var($ip, FILTER_VALIDATE_IP) !== false;
}

function eq_load_allowed_kiosks(): array {
    $jsonPath = eq_allowed_kiosks_file();
    if (is_file($jsonPath)) {
        $raw = @file_get_contents($jsonPath);
        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['items']) && is_array($decoded['items'])) {
                return array_values(array_filter(array_map('eq_normalize_allowed_kiosk_item', $decoded['items'])));
            }
        }
    }

    $legacyPath = eq_legacy_allowed_hosts_file();
    if (!is_file($legacyPath)) {
        return [];
    }

    $lines = file($legacyPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return [];
    }

    $items = [];
    foreach ($lines as $line) {
        $hostname = trim((string)$line);
        if (!eq_hostname_is_valid($hostname)) {
            continue;
        }
        $items[] = [
            'hostname' => $hostname,
            'ip' => '',
            'enabled' => true,
        ];
    }
    return $items;
}

function eq_normalize_allowed_kiosk_item($item): ?array {
    if (!is_array($item)) {
        return null;
    }

    $hostname = eq_normalize_hostname((string)($item['hostname'] ?? ''));
    $ip = eq_normalize_ip((string)($item['ip'] ?? ''));
    $enabled = !isset($item['enabled']) || (bool)$item['enabled'];

    if (!eq_hostname_is_valid($hostname)) {
        return null;
    }
    if (!eq_is_valid_ip_or_empty($ip)) {
        return null;
    }

    return [
        'hostname' => $hostname,
        'ip' => $ip,
        'enabled' => $enabled,
    ];
}

function eq_save_allowed_kiosks(array $items): bool {
    $normalized = [];
    $seen = [];
    foreach ($items as $item) {
        $entry = eq_normalize_allowed_kiosk_item($item);
        if (!is_array($entry)) {
            continue;
        }
        $key = eq_normalize_hostname($entry['hostname']);
        if ($key === '' || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $normalized[] = $entry;
    }

    usort($normalized, static function (array $a, array $b): int {
        return strnatcasecmp($a['hostname'], $b['hostname']);
    });

    $payload = json_encode([
        'updated_at' => date('Y-m-d H:i:s'),
        'items' => $normalized,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (!is_string($payload) || @file_put_contents(eq_allowed_kiosks_file(), $payload . "\n", LOCK_EX) === false) {
        return false;
    }

    $legacyLines = [];
    foreach ($normalized as $entry) {
        if (!empty($entry['enabled'])) {
            $legacyLines[] = $entry['hostname'];
        }
    }
    @file_put_contents(eq_legacy_allowed_hosts_file(), implode("\n", $legacyLines) . (count($legacyLines) ? "\n" : ''), LOCK_EX);

    return true;
}

function eq_allowed_kiosks_lookup(array $items): array {
    $lookup = [];
    foreach ($items as $item) {
        if (!is_array($item) || empty($item['enabled'])) {
            continue;
        }
        $key = eq_normalize_hostname((string)($item['hostname'] ?? ''));
        if ($key === '') {
            continue;
        }
        $lookup[$key] = $item;
    }
    return $lookup;
}

function eq_resolve_allowed_kiosk(array $lookup, string $hostname): ?array {
    $key = eq_normalize_hostname($hostname);
    if ($key === '' || !isset($lookup[$key]) || !is_array($lookup[$key])) {
        return null;
    }
    return $lookup[$key];
}

function eq_load_known_kiosks_from_status(): array {
    $path = eq_status_file();
    if (!is_file($path)) {
        return [];
    }

    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    $items = [];
    foreach ($decoded as $hostname => $info) {
        if (!eq_hostname_is_valid((string)$hostname)) {
            continue;
        }
        $ip = '';
        if (is_array($info)) {
            $candidateIp = trim((string)($info['local_ip'] ?? $info['source_ip'] ?? $info['ip'] ?? ''));
            if (eq_is_valid_ip_or_empty($candidateIp)) {
                $ip = $candidateIp;
            }
        }
        $items[] = [
            'hostname' => (string)$hostname,
            'ip' => $ip,
            'enabled' => false,
        ];
    }

    usort($items, static function (array $a, array $b): int {
        return strnatcasecmp($a['hostname'], $b['hostname']);
    });

    return $items;
}

function eq_load_allowed_kiosks_for_crud(): array {
    $configured = eq_load_allowed_kiosks();
    $known = eq_load_known_kiosks_from_status();
    $defaultEnabled = count($configured) === 0;

    $merged = [];
    foreach ($known as $item) {
        if ($defaultEnabled) {
            $item['enabled'] = true;
        }
        $key = eq_normalize_hostname($item['hostname']);
        if ($key !== '') {
            $merged[$key] = $item;
        }
    }

    foreach ($configured as $item) {
        $key = eq_normalize_hostname($item['hostname']);
        if ($key === '') {
            continue;
        }
        $merged[$key] = $item;
    }

    $items = array_values($merged);
    usort($items, static function (array $a, array $b): int {
        return strnatcasecmp($a['hostname'], $b['hostname']);
    });

    return $items;
}

function eq_load_protection_state(): bool {
    $path = eq_protection_file();
    if (!is_file($path)) {
        return true;
    }
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return true;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return true;
    }
    return !isset($decoded['enabled']) || (bool)$decoded['enabled'];
}

function eq_save_protection_state(bool $enabled): bool {
    $payload = json_encode([
        'enabled' => $enabled,
        'updated_at' => date('Y-m-d H:i:s'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return is_string($payload) && @file_put_contents(eq_protection_file(), $payload . "\n", LOCK_EX) !== false;
}

function eq_load_unknown_kiosk_attempts(): array {
    $path = eq_unknown_attempts_file();
    if (!is_file($path)) {
        return [];
    }
    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    $items = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) {
            continue;
        }
        $hostname = eq_normalize_hostname((string)($item['hostname'] ?? ''));
        $ip = eq_normalize_ip((string)($item['ip'] ?? ''));
        $lastSeen = (int)($item['last_seen'] ?? 0);
        $attempts = max(1, (int)($item['attempts'] ?? 1));
        if (!eq_hostname_is_valid($hostname) || !eq_is_valid_ip_or_empty($ip)) {
            continue;
        }
        $items[] = [
            'hostname' => $hostname,
            'ip' => $ip,
            'last_seen' => $lastSeen,
            'attempts' => $attempts,
        ];
    }

    usort($items, static function (array $a, array $b): int {
        return ($b['last_seen'] ?? 0) <=> ($a['last_seen'] ?? 0);
    });
    return $items;
}

function eq_save_unknown_kiosk_attempts(array $items): bool {
    $payload = json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return is_string($payload) && @file_put_contents(eq_unknown_attempts_file(), $payload . "\n", LOCK_EX) !== false;
}

function eq_register_unknown_kiosk_attempt(string $hostname, string $ip): void {
    $hostname = eq_normalize_hostname($hostname);
    $ip = eq_normalize_ip($ip);
    if (!eq_hostname_is_valid($hostname) || !eq_is_valid_ip_or_empty($ip)) {
        return;
    }

    $items = eq_load_unknown_kiosk_attempts();
    $map = [];
    foreach ($items as $item) {
        $key = eq_normalize_hostname((string)$item['hostname']);
        if ($key !== '') {
            $map[$key] = $item;
        }
    }

    $key = eq_normalize_hostname($hostname);
    $existing = $map[$key] ?? [
        'hostname' => $hostname,
        'ip' => $ip,
        'last_seen' => 0,
        'attempts' => 0,
    ];
    if ($existing['ip'] === '' && $ip !== '') {
        $existing['ip'] = $ip;
    }
    $existing['last_seen'] = time();
    $existing['attempts'] = ((int)$existing['attempts']) + 1;
    $map[$key] = $existing;

    eq_save_unknown_kiosk_attempts(array_values($map));
}

function eq_remove_unknown_kiosk_attempt(string $hostname): void {
    $key = eq_normalize_hostname($hostname);
    if ($key === '') {
        return;
    }
    $items = eq_load_unknown_kiosk_attempts();
    $filtered = array_values(array_filter($items, static function (array $item) use ($key): bool {
        return eq_normalize_hostname((string)$item['hostname']) !== $key;
    }));
    eq_save_unknown_kiosk_attempts($filtered);
}
