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
            background-color: #000000;
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

        video {
            width: 100vw;
            height: 100vh;
            object-fit: contain;
            background-color: #000000;
        }

        #contentContainer {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
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

        // Filtra los archivos válidos (PDF, JPG, PNG, MP4, WEBM)
        $archivosValidos = array_filter($archivos, function ($archivo) use ($dir) {
            $path = $dir . '/' . $archivo;
            return is_file($path) && preg_match('/\\.(pdf|jpg|jpeg|png|mp4|webm)$/i', $archivo);
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
    <div id="contentContainer">
        <iframe id="documentFrame" style="display:none;"></iframe>
        <video id="videoPlayer" style="display:none;" autoplay muted></video>
        <img id="imageViewer" style="display:none;">
    </div>

    <script>
        // Lista inicial de documentos generada desde PHP
        var documentos = <?php echo json_encode($documentos); ?>;
        var currentIndex = 0;
        var intervalo = 5000; // 5 segundos por documento (imágenes y PDFs)
        var timeoutHandle = null;
        var currentType = null;

        // Referencias a elementos
        var videoPlayer = document.getElementById("videoPlayer");
        var documentFrame = document.getElementById("documentFrame");
        var imageViewer = document.getElementById("imageViewer");

        // Función para ocultar todos los elementos
        function ocultarTodosElementos() {
            videoPlayer.style.display = 'none';
            documentFrame.style.display = 'none';
            imageViewer.style.display = 'none';
            
            // Pausar video si está reproduciendo
            if (!videoPlayer.paused) {
                videoPlayer.pause();
            }
            videoPlayer.src = '';
        }

        // Función para mostrar el siguiente documento
        function mostrarSiguienteDocumento() {
            if (documentos.length > 0) {
                var documento = documentos[currentIndex];
                var ext = documento.split('.').pop().toLowerCase().split('?')[0]; // Remover query params
                var docActual = 'docs/' + documento;

                console.log('Mostrando documento:', docActual, 'tipo:', ext);

                // Limpiar timeout anterior si existe
                if (timeoutHandle) {
                    clearTimeout(timeoutHandle);
                    timeoutHandle = null;
                }

                ocultarTodosElementos();

                // Mostrar el archivo según su tipo
                if (ext === 'mp4' || ext === 'webm') {
                    // Video
                    currentType = 'video';
                    videoPlayer.src = docActual;
                    videoPlayer.style.display = 'block';
                    
                    // Activar audio
                    videoPlayer.muted = false;
                    
                    // Cuando el video termine, avanzar al siguiente
                    videoPlayer.onended = function() {
                        console.log('Video terminado, avanzando...');
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    };
                    
                    // Manejar errores
                    videoPlayer.onerror = function() {
                        console.error("Error al cargar el video: " + docActual);
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    };
                    
                    // Reproducir video
                    videoPlayer.play().catch(function(error) {
                        console.error("Error al reproducir video:", error);
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    });

                } else if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') {
                    // Imagen
                    currentType = 'image';
                    var img = new Image();
                    img.onload = function () {
                        imageViewer.src = docActual;
                        imageViewer.style.display = 'block';
                        
                        // Programar siguiente documento después del intervalo
                        avanzarIndice();
                        timeoutHandle = setTimeout(mostrarSiguienteDocumento, intervalo);
                    };
                    img.onerror = function () {
                        console.error("Error al cargar la imagen: " + docActual);
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    };
                    img.src = docActual;

                } else if (ext === 'pdf') {
                    // PDF
                    currentType = 'pdf';
                    documentFrame.src = docActual;
                    documentFrame.style.display = 'block';
                    
                    // Programar siguiente documento después del intervalo
                    avanzarIndice();
                    timeoutHandle = setTimeout(mostrarSiguienteDocumento, intervalo);
                }
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
    </script>
</body>

</html>
