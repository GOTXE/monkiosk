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
    'services' => $services
];

echo json_encode($status, JSON_UNESCAPED_UNICODE);
