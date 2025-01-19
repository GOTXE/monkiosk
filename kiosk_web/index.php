<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiosco SINOFAP</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            overflow: hidden;
        }

        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
        }

        img {
            width: 100%;
            height: 100%;
            max-width: 100vw;
            max-height: 100vh;
            object-fit: contain;
        }
    </style>
    <?php
    // Evitar caché en la página
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    // Generar lista de documentos
    function obtenerDocumentos() {
        $dir = './docs';

        // Verifica si el directorio existe
        if (!is_dir($dir)) {
            return [];
        }

        // Obtén todos los archivos del directorio
        $archivos = scandir($dir);

        // Filtra los archivos válidos (PDF, JPG, PNG)
        $archivosValidos = array_filter($archivos, function ($archivo) use ($dir) {
            $path = $dir . '/' . $archivo;
            return is_file($path) && preg_match('/\\.(pdf|jpg|jpeg|png)$/i', $archivo);
        });

        // Ordena los archivos numéricamente
        usort($archivosValidos, function ($a, $b) {
            return intval(pathinfo($a, PATHINFO_FILENAME)) - intval(pathinfo($b, PATHINFO_FILENAME));
        });

        // Agrega marcas de tiempo para invalidar caché en cada archivo
        return array_map(function ($archivo) use ($dir) {
            $path = $dir . '/' . $archivo;
            $timestamp = filemtime($path);
            return $archivo . '?v=' . $timestamp;
        }, $archivosValidos);
    }

    // Exportar la lista de documentos a JavaScript
    $documentos = obtenerDocumentos();
    ?>
</head>

<body>
    <iframe id="documentFrame"></iframe>

    <script>
        // Lista inicial de documentos generada desde PHP
        var documentos = <?php echo json_encode($documentos); ?>;
        var currentIndex = 0;
        var intervalo = 5000; // 5 segundos por documento

        // Función para mostrar el siguiente documento
        function mostrarSiguienteDocumento() {
            if (documentos.length > 0) {
                var documento = documentos[currentIndex];
                var ext = documento.split('.').pop().toLowerCase();
                var docActual = 'docs/' + documento;

                // Mostrar el archivo actual
                if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
                    var img = new Image();
                    img.onload = function () {
                        document.getElementById("documentFrame").srcdoc = '<img src="' + docActual + '" style="width:100%;height:100%;max-width:100vw;max-height:100vh;">';
                    };
                    img.onerror = function () {
                        console.error("Error al cargar la imagen: " + docActual);
                        avanzarIndice(); // Avanzar al siguiente documento
                    };
                    img.src = docActual;
                } else {
                    document.getElementById("documentFrame").src = docActual; // PDFs se muestran en iframe
                }

                avanzarIndice();
            }
        }

        // Función para avanzar el índice y reiniciar si es necesario
        function avanzarIndice() {
            currentIndex = (currentIndex + 1) % documentos.length;

            // Si estamos al final del ciclo, actualizar la lista de documentos
            if (currentIndex === 0) {
                actualizarDocumentos();
            }
        }

        // Función para actualizar la lista de documentos desde el servidor
        function actualizarDocumentos() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    // Extraer la lista de documentos desde el HTML
                    var nuevaLista = JSON.parse(html.match(/var documentos = (.*?);/s)[1]);

                    // Actualizar la lista si hay cambios
                    if (JSON.stringify(nuevaLista) !== JSON.stringify(documentos)) {
                        documentos = nuevaLista;
                        console.log("Lista de documentos actualizada:", documentos);
                    }
                })
                .catch(error => console.error("Error al actualizar documentos:", error));
        }

        // Inicia el ciclo de documentos
        mostrarSiguienteDocumento(); // Mostrar el primer documento inmediatamente
        setInterval(mostrarSiguienteDocumento, intervalo);
    </script>
</body>

</html>
