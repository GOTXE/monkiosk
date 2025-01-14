<?php
// Check if the request is a POST request to update the status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON payload from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract the kiosk name and status
    $kiosk_name = $data['name'];
    $status = $data['status'];

    // Define the path to the status file
    $status_file = '../monkiosk/status.txt';

    // Read the current status file
    $status_data = file_exists($status_file) ? json_decode(file_get_contents($status_file), true) : [];

    // Update the status for the kiosk
    $status_data[$kiosk_name] = $status;

    // Write the updated status back to the file
    file_put_contents($status_file, json_encode($status_data));

    // Respond with a success message
    echo json_encode(['message' => 'Status updated successfully']);
    exit;
}

// Existing code to display documents
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos en Bucle</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }
        iframe {
            width: 100vw; /* Ancho completo de la ventana */
            height: 100vh; /* Alto completo de la ventana */
            border: none;
        }
        img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <iframe id="documentFrame"></iframe>

    <script>
        var documentos = [
            <?php
            // Carga de documentos
            $dir = '/var/www/html/docs';
            $archivos = scandir($dir);

            $archivosValidos = array_filter($archivos, function($archivo) {
                return preg_match('/\.(pdf|jpg|jpeg|png)$/i', $archivo);
            });

            $primero = true;
            foreach ($archivosValidos as $archivo) {
                if (!$primero) {
                    echo ',';
                }
                echo '"' . "/docs/" . $archivo . '"';
                $primero = false;
            }
            ?>
        ];

        var currentIndex = 0;
        var intervalo = 5000; // 5 segundos para las pruebas, puedes cambiarlo luego
        var recarga = 10000; // Tiempo para recargar la página

        // Cambiar el documento mostrado en el iframe
        function cambiarDocumento() {
            if (documentos.length > 0) {
                var docActual = documentos[currentIndex];
                var ext = docActual.split('.').pop().toLowerCase();

                // Si es una imagen, la mostramos como <img> en vez de usar <iframe>
                if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
                    document.getElementById("documentFrame").srcdoc = '<img src="' + docActual + '" style="width:100%;height:auto;">';
                } else {
                    document.getElementById("documentFrame").src = docActual; // PDFs se muestran en iframe
                }

                // Incrementa el índice o vuelve al inicio
                currentIndex = (currentIndex + 1) % documentos.length;
            }
        }

        // Cambia el documento al cargar la página
        cambiarDocumento();

        // Cambia el documento según el intervalo
        setInterval(cambiarDocumento, intervalo);

        // Actualiza la lista de documentos cada cierto tiempo
        setInterval(function() {
            window.location.reload(); }, recarga);
    </script>
</body>
</html>
