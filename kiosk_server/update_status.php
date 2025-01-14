<?php
// Ruta del archivo donde se guardan los estados
$status_file = "/var/www/html/monkiosk/status.txt";

// Verifica si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer el contenido del POST
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['name']) && isset($data['status'])) { // Validar los datos enviados
        $name = htmlspecialchars($data['name']); // Nombre del quiosco
        $status = htmlspecialchars($data['status']); // Estado del quiosco

        // Leer el contenido actual del archivo
        $current_status = file_exists($status_file) ? file_get_contents($status_file) : "";
        $lines = explode("\n", $current_status); // Dividir el archivo en líneas
        $updated = false;

        foreach ($lines as &$line) {
            if (strpos($line, $name) !== false) { // Si el quiosco ya existe en el archivo
                $line = "$name: $status"; // Actualiza su estado
                $updated = true;
            }
        }

        // Si no se encuentra el quiosco, añadirlo
        if (!$updated) {
            $lines[] = "$name: $status";
        }

        // Escribir el nuevo contenido al archivo
        file_put_contents($status_file, implode("\n", $lines));

        // Respuesta exitosa
        echo json_encode(["success" => true]);
    } else {
        // Respuesta de error si los datos son inválidos
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Datos inválidos"]);
    }
} else {
    // Si no es una solicitud POST, devolver un error
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
