<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SINOFAP</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
        }

        iframe {
            width: 100vw;
            /* Ancho completo de la ventana */
            height: 100vh;
            /* Alto completo de la ventana */
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
        var intervalo = 5000; // 5 segundos para las pruebas, puedes cambiarlo luego

        // Lista de documentos generada desde PHP
        documentos = [
            <?php
            // Define la carpeta donde están tus archivos
            $dir = '/var/www/html/docs';

            // Verifica si el directorio existe
            if (!is_dir($dir)) {
                echo 'console.error("El directorio no existe: ' . $dir . '");';
                exit;
            }

            // Obtén todos los archivos del directorio
            $archivos = scandir($dir);

            // Filtra los archivos válidos (PDF, JPG, PNG)
            $archivosValidos = array_filter($archivos, function ($archivo) {
                return preg_match('/\.(pdf|jpg|jpeg|png)$/i', $archivo);
            });

            // Ordena los archivos numéricamente
            usort($archivosValidos, function ($a, $b) {
                return intval(pathinfo($a, PATHINFO_FILENAME)) - intval(pathinfo($b, PATHINFO_FILENAME));
            });

            // Imprime los archivos en formato JavaScript
            $primero = true;
            foreach ($archivosValidos as $archivo) {
                if (!$primero) {
                    echo ',';
                }
                echo json_encode($archivo);
                $primero = false;
            }
            ?>
        ];

        // Función para mostrar el siguiente documento
        function mostrarSiguienteDocumento() {
            if (documentos.length > 0) {
                var documento = documentos[currentIndex];
                var ext = documento.split('.').pop().toLowerCase();
                var docActual = 'docs/' + documento;

                // Si hemos llegado al final de la lista de documentos, recargar la página
                if (currentIndex >= documentos.length - 1) {
                    setTimeout(function() {
                        location.reload();
                    }, intervalo);
                } else {
                    currentIndex++;
                }

                // Si es una imagen, la mostramos como <img> en vez de usar <iframe>
                if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
                    var img = new Image();
                    img.onload = function() {
                        document.getElementById("documentFrame").srcdoc = '<img src="' + docActual + '" style="width:100%;height:auto;">';
                    };
                    img.onerror = function() {
                        console.error("Error al cargar la imagen: " + docActual);
                        mostrarSiguienteDocumento(); // Intenta cargar el siguiente documento
                    };
                    img.src = docActual;
                } else {
                    document.getElementById("documentFrame").src = docActual; // PDFs se muestran en iframe
                }
            }
        }

        // Inicia el ciclo de documentos
        setInterval(mostrarSiguienteDocumento, intervalo);
    </script>
</body>

</html>