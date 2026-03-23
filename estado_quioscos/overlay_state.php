<?php
header('Content-Type: application/json; charset=utf-8');

$overlay_path = __DIR__ . '/overlay_config.json';
$status_path = __DIR__ . '/status.json';

function read_overlay_config(string $path): array {
    $default = [
        'enabled' => false,
        'target_kiosk' => '',
        'updated_at' => date('Y-m-d H:i:s')
    ];
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
    return [
        'enabled' => (bool)($json['enabled'] ?? false),
        'target_kiosk' => trim((string)($json['target_kiosk'] ?? '')),
        'updated_at' => trim((string)($json['updated_at'] ?? date('Y-m-d H:i:s')))
    ];
}

function read_status(string $path): array {
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

function normalize_ip(string $ip): string {
    return trim($ip);
}

function find_kiosk_name_by_ip(array $status, string $ip): string {
    $needle = normalize_ip($ip);
    if ($needle === '') {
        return '';
    }
    foreach ($status as $name => $info) {
        if (!is_array($info)) {
            continue;
        }
        $source_ip = normalize_ip((string)($info['source_ip'] ?? ''));
        if ($source_ip !== '' && $source_ip === $needle) {
            return (string)$name;
        }
        $report_ip = normalize_ip((string)($info['ip'] ?? ''));
        if ($report_ip !== '' && $report_ip === $needle) {
            return (string)$name;
        }
        $local_ip = normalize_ip((string)($info['local_ip'] ?? ''));
        if ($local_ip !== '' && $local_ip === $needle) {
            return (string)$name;
        }
    }
    return '';
}

$overlay = read_overlay_config($overlay_path);
$status = read_status($status_path);

$client_ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
$client_kiosk = find_kiosk_name_by_ip($status, $client_ip);
$target_kiosk = (string)$overlay['target_kiosk'];
$enabled = (bool)$overlay['enabled'];
$show_countdown = $enabled && $target_kiosk !== '' && strcasecmp($client_kiosk, $target_kiosk) === 0;

echo json_encode([
    'success' => true,
    'enabled' => $enabled,
    'target_kiosk' => $target_kiosk,
    'updated_at' => (string)$overlay['updated_at'],
    'client_ip' => $client_ip,
    'client_kiosk' => $client_kiosk,
    'show_countdown' => $show_countdown
], JSON_UNESCAPED_UNICODE);
