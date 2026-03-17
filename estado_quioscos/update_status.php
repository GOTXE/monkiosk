<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/app_config.php';

$offline_timeout = eq_offline_timeout_seconds();
$cleanup_ttl = 7 * 24 * 60 * 60; // 7 dias
$history_max_events = 5;
$unstable_window_seconds = 10 * 60; // 10 minutos
$unstable_transitions_threshold = 3;

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Lee y decodifica el cuerpo JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

if (!isset($data['name'], $data['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields: name and status']);
    exit;
}

$kiosk_name = trim($data['name']);
if ($kiosk_name === '' || strlen($kiosk_name) > 128) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid kiosk name']);
    exit;
}

$status = trim($data['status']);
if ($status === '') {
    $status = 'UNKNOWN';
}
if (strlen($status) > 64) {
    $status = substr($status, 0, 64);
}

$status_file = __DIR__ . '/status.json';
$allowed_kiosks_lookup = eq_allowed_kiosks_lookup(eq_load_allowed_kiosks());

// Carga los estados actuales (si el archivo está vacío o inválido, iniciar array vacío)
$status_data = [];
if (file_exists($status_file)) {
    $existing = @file_get_contents($status_file);
    if ($existing !== false && trim($existing) !== '') {
        $decoded = json_decode($existing, true);
        if (is_array($decoded)) {
            $status_data = $decoded;
        }
    }
}

function append_state_event(array $history, string $state, int $timestamp, int $max_events): array {
    $last = end($history);
    if (!is_array($last) || ($last['state'] ?? '') !== $state) {
        $history[] = [
            'ts' => $timestamp,
            'state' => $state
        ];
    }

    if (count($history) > $max_events) {
        $history = array_slice($history, -$max_events);
    }
    return $history;
}

function calculate_unstable(array $history, int $window_seconds, int $threshold, int $now): bool {
    if (count($history) < 2) {
        return false;
    }

    $recent = array_values(array_filter($history, function ($event) use ($window_seconds, $now) {
        return is_array($event) && isset($event['ts']) && (($now - (int)$event['ts']) <= $window_seconds);
    }));

    if (count($recent) < 2) {
        return false;
    }

    $transitions = 0;
    for ($i = 1; $i < count($recent); $i++) {
        $prev = $recent[$i - 1]['state'] ?? '';
        $curr = $recent[$i]['state'] ?? '';
        if ($prev !== $curr) {
            $transitions++;
        }
    }

    return $transitions >= $threshold;
}

function sanitize_string_field(array $data, string $key, int $max_len): ?string {
    if (!isset($data[$key])) {
        return null;
    }
    $value = trim((string)$data[$key]);
    if ($value === '') {
        return null;
    }
    if (strlen($value) > $max_len) {
        $value = substr($value, 0, $max_len);
    }
    return $value;
}

function sanitize_int_field(array $data, string $key, int $min = 0, int $max = 2147483647): ?int {
    if (!isset($data[$key])) {
        return null;
    }
    if (!is_numeric($data[$key])) {
        return null;
    }
    $value = (int)$data[$key];
    if ($value < $min) {
        $value = $min;
    }
    if ($value > $max) {
        $value = $max;
    }
    return $value;
}

// Datos adicionales
$remote_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$current_time = time();
$report_protection_enabled = eq_load_report_protection_enabled();
$allowed_kiosk = eq_resolve_allowed_kiosk($allowed_kiosks_lookup, $kiosk_name);
if ($report_protection_enabled && !empty($allowed_kiosks_lookup) && !is_array($allowed_kiosk)) {
    eq_register_unknown_kiosk_attempt($kiosk_name, $remote_ip);
    http_response_code(403);
    echo json_encode(['error' => 'Hostname no permitido']);
    exit;
}
if ($report_protection_enabled && is_array($allowed_kiosk)) {
    $expected_ip = (string)($allowed_kiosk['ip'] ?? '');
    if ($expected_ip !== '' && $expected_ip !== $remote_ip) {
        eq_register_unknown_kiosk_attempt($kiosk_name, $remote_ip);
        http_response_code(403);
        echo json_encode(['error' => 'IP no permitida para ese hostname']);
        exit;
    }
}
if (is_array($allowed_kiosk)) {
    eq_remove_unknown_kiosk_attempt($kiosk_name);
}

// Normaliza registros existentes para compatibilidad.
foreach ($status_data as $name => $info) {
    if (!is_array($info)) {
        $status_data[$name] = [];
        $info = [];
    }

    if (!isset($info['history']) || !is_array($info['history'])) {
        $status_data[$name]['history'] = [];
    }

    if (!isset($info['unstable'])) {
        $status_data[$name]['unstable'] = false;
    }
}

$existing_kiosk = $status_data[$kiosk_name] ?? [];
$previous_last_updated = isset($existing_kiosk['last_updated']) ? (int)$existing_kiosk['last_updated'] : 0;
$was_online = $previous_last_updated > 0 && (($current_time - $previous_last_updated) <= $offline_timeout);
$history = isset($existing_kiosk['history']) && is_array($existing_kiosk['history']) ? $existing_kiosk['history'] : [];

$extra_fields = [];
$local_ip = sanitize_string_field($data, 'local_ip', 64);
if ($local_ip !== null) {
    $extra_fields['local_ip'] = $local_ip;
}
$kiosk_url = sanitize_string_field($data, 'kiosk_url', 255);
if ($kiosk_url !== null) {
    $extra_fields['kiosk_url'] = $kiosk_url;
}
$load1 = sanitize_string_field($data, 'load1', 32);
if ($load1 !== null) {
    $extra_fields['load1'] = $load1;
}
$report_target = sanitize_string_field($data, 'report_target', 255);
if ($report_target !== null) {
    $extra_fields['report_target'] = $report_target;
}
$hdmi_connected = sanitize_string_field($data, 'hdmi_connected', 32);
if ($hdmi_connected !== null) {
    $extra_fields['hdmi_connected'] = $hdmi_connected;
}
$uptime_s = sanitize_int_field($data, 'uptime_s');
if ($uptime_s !== null) {
    $extra_fields['uptime_s'] = $uptime_s;
}
$mem_free_mb = sanitize_int_field($data, 'mem_free_mb');
if ($mem_free_mb !== null) {
    $extra_fields['mem_free_mb'] = $mem_free_mb;
}
$disk_free_mb = sanitize_int_field($data, 'disk_free_mb');
if ($disk_free_mb !== null) {
    $extra_fields['disk_free_mb'] = $disk_free_mb;
}

// Actualiza o agrega el estado del quiosco que reporta.
$status_data[$kiosk_name] = array_merge($existing_kiosk, [
    'status' => $status,
    'last_updated' => $current_time,
    'ip' => $remote_ip,
    'source_ip' => $remote_ip
], $extra_fields);

if (!$was_online) {
    $history = append_state_event($history, 'online', $current_time, $history_max_events);
}
if (empty($history)) {
    $history = append_state_event($history, 'online', $current_time, $history_max_events);
}
$status_data[$kiosk_name]['history'] = $history;

// Limpieza y enriquecimiento para todos los equipos.
foreach ($status_data as $name => &$info) {
    $last_updated = isset($info['last_updated']) ? (int)$info['last_updated'] : 0;
    $is_offline = $last_updated <= 0 || (($current_time - $last_updated) > $offline_timeout);
    $history = isset($info['history']) && is_array($info['history']) ? $info['history'] : [];

    if ($is_offline) {
        $history = append_state_event($history, 'offline', $current_time, $history_max_events);
    } else {
        $history = append_state_event($history, 'online', $last_updated, $history_max_events);
    }

    $info['history'] = $history;
    $info['unstable'] = calculate_unstable(
        $history,
        $unstable_window_seconds,
        $unstable_transitions_threshold,
        $current_time
    );

    // Limpieza principal: eliminar equipos sin latidos en 7 dias.
    if ($last_updated > 0 && (($current_time - $last_updated) > $cleanup_ttl)) {
        unset($status_data[$name]);
        continue;
    }

    if ($report_protection_enabled && !empty($allowed_kiosks_lookup) && !is_array(eq_resolve_allowed_kiosk($allowed_kiosks_lookup, $name))) {
        unset($status_data[$name]);
    }
}
unset($info);

// Guardar con bloqueo para evitar condiciones de carrera
$json_out = json_encode($status_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$fp = @fopen($status_file, 'c+');
if ($fp === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to open status file for writing']);
    exit;
}

if (!flock($fp, LOCK_EX)) {
    fclose($fp);
    http_response_code(500);
    echo json_encode(['error' => 'Unable to lock status file']);
    exit;
}

ftruncate($fp, 0);
rewind($fp);
$written = fwrite($fp, $json_out);
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to write status file']);
    exit;
}

echo json_encode(['success' => true]);
