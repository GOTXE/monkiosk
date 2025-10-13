<?php
// Verifica si la solicitud es POST para actualizar el estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén los datos JSON enviados por el cliente
    $data = json_decode(file_get_contents('php://input'), true);

    // Valida que los datos tengan el formato correcto
    if (!isset($data['name'], $data['status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }

    $kiosk_name = trim($data['name']); // Elimina espacios en blanco del nombre
    $status = $data['status'];

    // Ruta al archivo de estados y archivo de hostnames permitidos
    $status_file = __DIR__ . '/status.json';
    $allowed_hosts_file = __DIR__ . '/allowed_hosts.txt';

    // Cargar lista de hosts permitidos
    $allowed_hosts = file_exists($allowed_hosts_file)
        ? array_map('trim', file($allowed_hosts_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
        : [];

    // Carga los estados actuales
    $status_data = file_exists($status_file) ? json_decode(file_get_contents($status_file), true) : [];

    // Tiempo actual y límite de inactividad para equipos restirados de la lista allowed_hosts
    $current_time = time();
    $inactivity_limit = 150; // 2.5 minutos CAMBIAR EN PRODUCCION

    // Actualiza o agrega el estado del quiosco
    $status_data[$kiosk_name] = [
        'status' => $status,
        'last_updated' => $current_time
    ];

    // Limpia equipos no permitidos y offline de la lista de estados
    foreach ($status_data as $name => $info) {
        $is_offline = ($current_time - $info['last_updated']) > $inactivity_limit;
        if (!in_array($name, $allowed_hosts) && $is_offline) {
            error_log("Eliminando $name: no está en allowed_hosts y está inactivo.");
            unset($status_data[$name]);
        }
    }

    // Guarda los datos actualizados en el archivo JSON
    file_put_contents($status_file, json_encode($status_data, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}