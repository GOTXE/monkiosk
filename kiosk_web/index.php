<?php
// Desactivar caché del navegador para este HTML
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <title>Quiosco SINOFAP</title>
    <style>
        * {
            box-sizing: border-box;
        }
        
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            background-color: #000000;
            overflow: hidden;
        }

        iframe {
            width: 100vw;
            height: 100vh;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        video {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            background-color: #000000;
            position: absolute;
            top: 0;
            left: 0;
        }

        #contentContainer {
            width: 100vw;
            height: 100vh;
            position: relative;
            background-color: #000000;
        }
        
    #imageViewer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        object-fit: cover;
        background-color: #000000;
    }

        #documentFrame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            border: none;
        }

        #videoPlayer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            background-color: #000000;
        }
    </style>
    <?php
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
            if (file_exists($path)) {
                $timestamp = filemtime($path);
                return $archivo . '?v=' . $timestamp;
            } else {
                error_log("Archivo no encontrado: " . $path);
                return $archivo; // Devolver sin timestamp si el archivo no existe
            }
        }, $archivosValidos);
    }

    // Exportar la lista de documentos a JavaScript
    $documentos = obtenerDocumentos();
    
    // Log para debugging
    error_log("Documentos encontrados: " . json_encode($documentos));
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
        console.log('Lista inicial de documentos:', documentos);
        
        var currentIndex = 0;
        var intervalo = 5000; // 5 segundos por documento (imágenes y PDFs)
        var timeoutHandle = null;
        var currentType = null;

        // Referencias a elementos
        var videoPlayer = document.getElementById("videoPlayer");
        var documentFrame = document.getElementById("documentFrame");
        var imageViewer = document.getElementById("imageViewer");

        // Limpia por completo el reproductor de vídeo (pausa, quita src y recarga)
        function cleanupVideo() {
            try {
                videoPlayer.pause();
                videoPlayer.removeAttribute('src');
                // Limpia eventos anteriores por seguridad
                videoPlayer.onended = null;
                videoPlayer.onerror = null;
                videoPlayer.load();
            } catch (e) {
                console.warn('cleanupVideo: no se pudo limpiar el video', e);
            }
        }

        // Función para ocultar todos los elementos
        function ocultarTodosElementos() {
            videoPlayer.style.display = 'none';
            documentFrame.style.display = 'none';
            imageViewer.style.display = 'none';
            
            // Asegurar limpieza del reproductor de video
            cleanupVideo();
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
                // Extra: al salir de un video, garantizamos que no queden audios huérfanos
                cleanupVideo();

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
                        // Limpieza inmediata al terminar para evitar audio residual
                        cleanupVideo();
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    };
                    
                    // Manejar errores
                    videoPlayer.onerror = function(e) {
                        console.error("Error detallado al cargar el video: " + docActual);
                        console.error("Error event:", e);
                        if (this.error) {
                            console.error("Media error code:", this.error.code);
                            console.error("Media error message:", this.error.message);
                        }
                        // En error de video también limpiamos
                        cleanupVideo();
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
                    console.log('Cargando imagen:', docActual);
                    
                    // Cargar imagen directamente en el elemento img del DOM
                    imageViewer.onload = function () {
                        console.log('✓ Imagen mostrada:', docActual, '(1920x1080)');
                        this.style.display = 'block';
                        
                        // Programar siguiente documento después del intervalo
                        avanzarIndice();
                        timeoutHandle = setTimeout(mostrarSiguienteDocumento, intervalo);
                    };
                    
                    imageViewer.onerror = function (e) {
                        console.error("✗ Error real al mostrar imagen:", docActual);
                        console.error("Detalles:", e);
                        avanzarIndice();
                        mostrarSiguienteDocumento();
                    };
                    
                    // Asignar directamente al elemento img del DOM
                    imageViewer.src = docActual;

                } else if (ext === 'pdf') {
                    // Antes de mostrar PDF, aseguramos limpieza de video
                    cleanupVideo();
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
