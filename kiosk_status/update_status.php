<?php
header('Content-Type: application/json; charset=utf-8');

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

// Datos adicionales
$remote_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$current_time = time();
$inactivity_limit = 150; // segundos

// Actualiza o agrega el estado del quiosco
$status_data[$kiosk_name] = [
    'status' => $status,
    'last_updated' => $current_time,
    'ip' => $remote_ip
];

// Limpia equipos no permitidos y offline de la lista de estados
foreach ($status_data as $name => $info) {
    $is_offline = !isset($info['last_updated']) || (($current_time - $info['last_updated']) > $inactivity_limit);
    if (!empty($allowed_hosts) && !in_array($name, $allowed_hosts) && $is_offline) {
        error_log("Eliminando $name: no está en allowed_hosts y está inactivo.");
        unset($status_data[$name]);
    }
}

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