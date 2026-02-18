<?php
header('Content-Type: application/json; charset=utf-8');

$offline_timeout = 150; // segundos
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

// Ruta al archivo de estados y archivo de hostnames permitidos
$status_file = __DIR__ . '/status.json';
$allowed_hosts_file = __DIR__ . '/allowed_hosts.txt';

// Cargar lista de hosts permitidos
$allowed_hosts = [];
if (file_exists($allowed_hosts_file)) {
    $allowed_hosts = array_map('trim', file($allowed_hosts_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

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

// Datos adicionales
$remote_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$current_time = time();

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

// Actualiza o agrega el estado del quiosco que reporta.
$status_data[$kiosk_name] = array_merge($existing_kiosk, [
    'status' => $status,
    'last_updated' => $current_time,
    'ip' => $remote_ip
]);

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

    // Si hay lista blanca, también elimina equipos no permitidos cuando estan offline.
    if (!empty($allowed_hosts) && !in_array($name, $allowed_hosts, true) && $is_offline) {
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
