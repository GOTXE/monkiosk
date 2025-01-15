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
        var documentos = [];
        var currentIndex = 0;
        var intervalo = 5000; // 5 segundos para las pruebas, CAMBIARLO EN PRODUCCION
        var recarga = 10000; // Tiempo para recargar la página, CAMBIARLO EN PRODUCCION

        // Lista de documentos generada desde PHP
        documentos = [
            <?php
            // Define la carpeta donde están tus archivos
            $dir = '/var/www/html/docs';

            // Obtén todos los archivos del directorio
            $archivos = scandir($dir);

            // Filtra los archivos válidos (PDF, JPG, PNG, etc.)
            $archivosValidos = array_filter($archivos, function($archivo) {
                return preg_match('/\.(pdf|jpg|jpeg|png)$/i', $archivo);
            });

            // Imprime los archivos en formato JavaScript
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
