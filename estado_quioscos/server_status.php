<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
require_once __DIR__ . '/app_config.php';
auth_require_json();

function run_single_line(string $cmd): string {
    $out = @shell_exec($cmd);
    if (!is_string($out)) {
        return 'unknown';
    }
    $line = trim($out);
    return $line === '' ? 'unknown' : $line;
}

function read_trimmed_file(string $path): string {
    $raw = @file_get_contents($path);
    if (!is_string($raw)) {
        return 'unknown';
    }
    $value = trim($raw);
    return $value === '' ? 'unknown' : $value;
}

function get_mem_free_mb(): int {
    $raw = @file('/proc/meminfo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($raw)) {
        return 0;
    }
    foreach ($raw as $line) {
        if (strpos($line, 'MemAvailable:') === 0) {
            $parts = preg_split('/\s+/', trim($line));
            return isset($parts[1]) ? (int)round(((int)$parts[1]) / 1024) : 0;
        }
    }
    return 0;
}

function get_disk_free_mb(): int {
    return get_mount_free_mb('/');
}

function get_mount_free_mb(string $mountpoint): int {
    $bytes = @disk_free_space($mountpoint);
    if (!is_numeric($bytes) || $bytes === false) {
        return 0;
    }
    return (int)round(((float)$bytes) / (1024 * 1024));
}

function systemd_active_enter_timestamp(string $unit): string {
    $cmd = "ts=\"\$(systemctl show " . escapeshellarg($unit) . " -p ActiveEnterTimestamp --value 2>/dev/null || true)\"; " .
        "[ -n \"\$ts\" ] && date -d \"\$ts\" '+%Y-%m-%d %H:%M:%S' 2>/dev/null || echo unknown";
    return run_single_line($cmd);
}

function get_slide_interval_seconds(): int {
    $default = 5;
    $path = eq_slide_settings_file();
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

    $seconds = (int)($json['slide_interval_seconds'] ?? $default);
    if ($seconds < 5) {
        return 5;
    }
    if ($seconds > 120) {
        return 120;
    }
    if ($seconds % 5 !== 0) {
        $seconds = (int)(round($seconds / 5) * 5);
    }
    return $seconds;
}

function run_command_raw(string $cmd): string {
    $out = @shell_exec($cmd);
    return is_string($out) ? trim($out) : '';
}

function release_info_from_runtime_file(): ?array {
    $path = __DIR__ . '/release.json';
    if (!is_file($path)) {
        return null;
    }

    $raw = @file_get_contents($path);
    if (!is_string($raw) || trim($raw) === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return null;
    }

    $version = trim((string)($decoded['version'] ?? ''));
    if ($version === '') {
        return null;
    }

    return [
        'version' => $version,
        'git_tag' => trim((string)($decoded['git_tag'] ?? '')),
        'git_commit' => trim((string)($decoded['git_commit'] ?? '')),
        'deployed_at' => trim((string)($decoded['deployed_at'] ?? '')),
        'source' => 'runtime',
    ];
}

function release_info_from_repo(): array {
    $repoDir = '/opt/monkiosk';
    $version = 'unknown';
    $versionFile = $repoDir . '/VERSION';
    if (is_file($versionFile)) {
        $rawVersion = @file_get_contents($versionFile);
        if (is_string($rawVersion) && trim($rawVersion) !== '') {
            $version = trim($rawVersion);
        }
    }

    $gitCommit = run_single_line('git -C ' . escapeshellarg($repoDir) . ' rev-parse --short HEAD 2>/dev/null || true');
    if ($gitCommit === 'unknown') {
        $gitCommit = '';
    }

    return [
        'version' => $version,
        'git_tag' => $version !== 'unknown' ? 'v' . ltrim($version, 'v') : '',
        'git_commit' => $gitCommit,
        'deployed_at' => '',
        'source' => 'repo',
    ];
}

function get_release_info(): array {
    $runtime = release_info_from_runtime_file();
    if (is_array($runtime)) {
        return $runtime;
    }

    return release_info_from_repo();
}

function certificate_cache_file(): string {
    return __DIR__ . '/certificate_status.json';
}

function get_certificate_expiry_info(): array {
    $cacheFile = certificate_cache_file();
    if (is_file($cacheFile)) {
        $rawCache = @file_get_contents($cacheFile);
        if (is_string($rawCache) && trim($rawCache) !== '') {
            $cached = json_decode($rawCache, true);
            $checkedAt = isset($cached['checked_at']) ? (int)$cached['checked_at'] : 0;
            if (is_array($cached) && $checkedAt > 0 && (time() - $checkedAt) < 86400) {
                return [
                    'available' => !empty($cached['available']),
                    'warning' => !empty($cached['warning']),
                    'days_remaining' => isset($cached['days_remaining']) ? (int)$cached['days_remaining'] : null,
                    'expires_at' => (string)($cached['expires_at'] ?? ''),
                    'checked_at' => $checkedAt,
                ];
            }
        }
    }

    $command = "openssl s_client -connect 127.0.0.1:443 -servername localhost </dev/null 2>/dev/null | openssl x509 -noout -enddate 2>/dev/null";
    $raw = run_command_raw($command);
    if ($raw === '' || stripos($raw, 'notAfter=') !== 0) {
        $result = [
            'available' => false,
            'warning' => false,
            'days_remaining' => null,
            'expires_at' => '',
            'checked_at' => time(),
        ];
        @file_put_contents($cacheFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
        return $result;
    }

    $dateText = trim(substr($raw, strlen('notAfter=')));
    $timestamp = strtotime($dateText);
    if ($timestamp === false) {
        $result = [
            'available' => false,
            'warning' => false,
            'days_remaining' => null,
            'expires_at' => '',
            'checked_at' => time(),
        ];
        @file_put_contents($cacheFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
        return $result;
    }

    $secondsRemaining = $timestamp - time();
    $daysRemaining = (int)floor($secondsRemaining / 86400);

    $result = [
        'available' => true,
        'warning' => $daysRemaining <= 30,
        'days_remaining' => $daysRemaining,
        'expires_at' => gmdate('Y-m-d H:i:s', $timestamp),
        'checked_at' => time(),
    ];
    @file_put_contents($cacheFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n", LOCK_EX);
    return $result;
}

$phpFpmUnit = eq_php_fpm_unit();
$services = [
    'nginx' => run_single_line("systemctl is-active nginx 2>/dev/null || true"),
    'php_fpm' => run_single_line("systemctl is-active " . escapeshellarg($phpFpmUnit) . " 2>/dev/null || true")
];

$status = [
    'success' => true,
    'time' => time(),
    'hostname' => gethostname() ?: read_trimmed_file('/etc/hostname'),
    'last_server_restart' => run_single_line("uptime -s 2>/dev/null || true"),
    'uptime' => (string)((int)explode('.', read_trimmed_file('/proc/uptime'))[0]),
    'load1' => explode(' ', read_trimmed_file('/proc/loadavg'))[0] ?? 'unknown',
    'mem_free_mb' => get_mem_free_mb(),
    'disk_free_mb' => get_disk_free_mb(),
    'disk_root_free_mb' => get_mount_free_mb('/'),
    'disk_var_free_mb' => get_mount_free_mb('/var'),
    'disk_home_free_mb' => get_mount_free_mb('/home'),
    'slide_interval_seconds' => get_slide_interval_seconds(),
    'last_web_restart' => systemd_active_enter_timestamp('nginx'),
    'last_php_restart' => systemd_active_enter_timestamp($phpFpmUnit),
    'services' => $services,
    'certificate' => get_certificate_expiry_info(),
    'release' => get_release_info(),
];

echo json_encode($status, JSON_UNESCAPED_UNICODE);
