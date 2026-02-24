<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/auth_lib.php';
auth_require_json();

function run_single_line(string $cmd): string {
    $out = @shell_exec($cmd);
    if (!is_string($out)) {
        return 'unknown';
    }
    $line = trim($out);
    return $line === '' ? 'unknown' : $line;
}

function get_mem_free_mb(): int {
    $out = @shell_exec("awk '/MemAvailable/ {printf \"%.0f\", $2/1024}' /proc/meminfo");
    return is_string($out) && trim($out) !== '' ? (int)trim($out) : 0;
}

function get_disk_free_mb(): int {
    $out = @shell_exec("df -Pm / | awk 'NR==2 {print $4}'");
    return is_string($out) && trim($out) !== '' ? (int)trim($out) : 0;
}

function get_mount_free_mb(string $mountpoint): int {
    $safe = escapeshellarg($mountpoint);
    $out = @shell_exec("df -Pm {$safe} | awk 'NR==2 {print $4}'");
    return is_string($out) && trim($out) !== '' ? (int)trim($out) : 0;
}

function systemd_active_enter_timestamp(string $unit): string {
    $cmd = "ts=\"\$(systemctl show " . escapeshellarg($unit) . " -p ActiveEnterTimestamp --value 2>/dev/null || true)\"; " .
        "[ -n \"\$ts\" ] && date -d \"\$ts\" '+%Y-%m-%d %H:%M:%S' 2>/dev/null || echo unknown";
    return run_single_line($cmd);
}

function get_slide_interval_seconds(): int {
    $default = 5;
    $path = __DIR__ . '/slide_settings.json';
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

$services = [
    'nginx' => run_single_line("systemctl is-active nginx 2>/dev/null || true"),
    'php_fpm' => run_single_line("systemctl is-active php8.2-fpm 2>/dev/null || true")
];

$status = [
    'success' => true,
    'time' => time(),
    'hostname' => run_single_line('hostname'),
    'last_server_restart' => run_single_line("uptime -s 2>/dev/null || true"),
    'uptime' => run_single_line("awk '{print int($1)}' /proc/uptime"),
    'load1' => run_single_line("awk '{print $1}' /proc/loadavg"),
    'mem_free_mb' => get_mem_free_mb(),
    'disk_free_mb' => get_disk_free_mb(),
    'disk_root_free_mb' => get_mount_free_mb('/'),
    'disk_var_free_mb' => get_mount_free_mb('/var'),
    'disk_home_free_mb' => get_mount_free_mb('/home'),
    'slide_interval_seconds' => get_slide_interval_seconds(),
    'last_web_restart' => systemd_active_enter_timestamp('nginx'),
    'last_php_restart' => systemd_active_enter_timestamp('php8.2-fpm'),
    'services' => $services
];

echo json_encode($status, JSON_UNESCAPED_UNICODE);
