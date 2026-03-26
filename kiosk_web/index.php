<?php
// Desactivar caché del navegador para este HTML
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$appConfigCandidates = [
    __DIR__ . '/../estado_quioscos/app_config.php',
    __DIR__ . '/estado_quioscos/app_config.php',
];
foreach ($appConfigCandidates as $appConfigPath) {
    if (is_file($appConfigPath)) {
        require_once $appConfigPath;
        break;
    }
}
$remoteAddr = (string)($_SERVER['REMOTE_ADDR'] ?? '');
if (function_exists('eq_register_presentation_viewer')) {
    eq_register_presentation_viewer(
        $remoteAddr,
        (string)($_SERVER['HTTP_USER_AGENT'] ?? '')
    );
}
if (function_exists('eq_is_presentation_access_allowed') && !eq_is_presentation_access_allowed($remoteAddr)) {
    if (function_exists('eq_register_presentation_unknown_attempt')) {
        eq_register_presentation_unknown_attempt($remoteAddr);
    }
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Acceso a presentacion no permitido\n";
    exit;
}
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

        #countdownOverlay {
            position: absolute;
            right: 30px;
            bottom: 24px;
            z-index: 9999;
            display: none;
            min-width: 84px;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(0, 20, 45, 0.82);
            color: #ffffff;
            border: 1px solid rgba(123, 192, 255, 0.75);
            text-align: center;
            font-family: Verdana, sans-serif;
            font-size: 56px;
            font-weight: 700;
            line-height: 1;
            user-select: none;
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

    function obtenerIntervaloDiapositivaMs() {
        $defaultSeconds = 5;
        $settingsPath = eq_slide_settings_file();

        if (!is_file($settingsPath)) {
            return $defaultSeconds * 1000;
        }

        $raw = @file_get_contents($settingsPath);
        if (!is_string($raw) || trim($raw) === '') {
            return $defaultSeconds * 1000;
        }

        $json = json_decode($raw, true);
        if (!is_array($json)) {
            return $defaultSeconds * 1000;
        }

        $seconds = (int)($json['slide_interval_seconds'] ?? $defaultSeconds);
        if ($seconds < 5) {
            $seconds = 5;
        }
        if ($seconds > 120) {
            $seconds = 120;
        }
        if ($seconds % 5 !== 0) {
            $seconds = (int)(round($seconds / 5) * 5);
        }
        return $seconds * 1000;
    }

    $intervaloMs = obtenerIntervaloDiapositivaMs();
    
    // Log para debugging
    error_log("Documentos encontrados: " . json_encode($documentos));
    error_log("Intervalo de diapositiva (ms): " . $intervaloMs);
    ?>
    <!-- Cargar PDF.js localmente (debe descargarse en vendor/pdfjs/) -->
    <script src="vendor/pdfjs/pdf.min.js"></script>
    <script>
        if (window.pdfjsLib) {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'vendor/pdfjs/pdf.worker.min.js';
            console.log('PDF.js local cargado, workerSrc configurado');
        } else {
            console.warn('PDF.js no está disponible localmente en vendor/pdfjs/');
        }
    </script>
</head>

<body>
    <div id="contentContainer">
        <iframe id="documentFrame" style="display:none;"></iframe>
    <canvas id="pdfCanvas" style="display:none;"></canvas>
        <video id="videoPlayer" style="display:none;" autoplay muted></video>
        <img id="imageViewer" style="display:none;">
        <div id="countdownOverlay"></div>
    </div>

    <script>
        // Usamos la copia local de PDF.js en vendor/pdfjs/
        // La carga del script local y la configuración de workerSrc ya se realiza en el <head>

        // Referencia al canvas para PDF
        var pdfCanvas = document.getElementById('pdfCanvas');
        var pdfRenderingTask = null;

        // Lista inicial de documentos generada desde PHP
        var documentos = <?php echo json_encode($documentos); ?>;
        console.log('Lista inicial de documentos:', documentos);
        
        var currentIndex = 0;
        var intervalo = <?php echo (int)$intervaloMs; ?>; // ms por documento (imágenes y PDFs)
        var timeoutHandle = null;
        var currentType = null;
        var intervaloPollHandle = null;
        var overlayPollHandle = null;
        var countdownIntervalHandle = null;
        var overlayEnabledForThisKiosk = false;

        // Referencias a elementos
        var videoPlayer = document.getElementById("videoPlayer");
        var documentFrame = document.getElementById("documentFrame");
        var imageViewer = document.getElementById("imageViewer");
        var countdownOverlay = document.getElementById("countdownOverlay");

        async function refrescarIntervaloDesdeServidor() {
            var urls = ['/estado_quioscos/slide_settings.php', 'estado_quioscos/slide_settings.php', '../estado_quioscos/slide_settings.php'];
            for (var i = 0; i < urls.length; i++) {
                try {
                    var resp = await fetch(urls[i] + '?_ts=' + Date.now(), { cache: 'no-store' });
                    if (!resp.ok) continue;
                    var data = await resp.json();
                    var seconds = Number(data && data.slide_interval_seconds ? data.slide_interval_seconds : 0);
                    if (!Number.isFinite(seconds) || seconds < 5) continue;
                    if (seconds > 120) seconds = 120;
                    if (seconds % 5 !== 0) seconds = Math.round(seconds / 5) * 5;

                    var nuevoIntervalo = Math.round(seconds * 1000);
                    if (nuevoIntervalo !== intervalo) {
                        intervalo = nuevoIntervalo;
                        console.log('Intervalo actualizado dinamicamente a', intervalo, 'ms');
                    }
                    return;
                } catch (e) {
                    // Intentar siguiente URL candidata
                }
            }
        }

        async function refrescarEstadoOverlay() {
            var urls = ['/estado_quioscos/overlay_state.php', 'estado_quioscos/overlay_state.php', '../estado_quioscos/overlay_state.php'];
            for (var i = 0; i < urls.length; i++) {
                try {
                    var resp = await fetch(urls[i] + '?_ts=' + Date.now(), { cache: 'no-store' });
                    if (!resp.ok) continue;
                    var data = await resp.json();
                    overlayEnabledForThisKiosk = Boolean(data && data.show_countdown);
                    if (!overlayEnabledForThisKiosk) {
                        stopCountdownOverlay();
                    }
                    return;
                } catch (e) {
                    // Intentar siguiente URL candidata
                }
            }
        }

        function stopCountdownOverlay() {
            if (countdownIntervalHandle) {
                clearInterval(countdownIntervalHandle);
                countdownIntervalHandle = null;
            }
            countdownOverlay.style.display = 'none';
            countdownOverlay.textContent = '';
        }

        function startCountdownOverlay(durationMs) {
            if (!overlayEnabledForThisKiosk) {
                stopCountdownOverlay();
                return;
            }
            if (!Number.isFinite(durationMs) || durationMs <= 0) {
                stopCountdownOverlay();
                return;
            }

            var remaining = Math.ceil(durationMs / 1000);
            stopCountdownOverlay();
            countdownOverlay.textContent = String(remaining);
            countdownOverlay.style.display = 'block';

            countdownIntervalHandle = setInterval(function() {
                if (!overlayEnabledForThisKiosk) {
                    stopCountdownOverlay();
                    return;
                }
                remaining -= 1;
                if (remaining <= 0) {
                    stopCountdownOverlay();
                    return;
                }
                countdownOverlay.textContent = String(remaining);
            }, 1000);
        }

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

        // Limpia canvas de PDF si estaba renderizando
        function cleanupPDF() {
            try {
                if (pdfRenderingTask && pdfRenderingTask.cancel) pdfRenderingTask.cancel();
            } catch (e) {
                console.warn('cleanupPDF:', e);
            }
            pdfRenderingTask = null;
            if (pdfCanvas) {
                pdfCanvas.style.display = 'none';
                var ctx = pdfCanvas.getContext('2d');
                if (ctx) ctx.clearRect(0, 0, pdfCanvas.width, pdfCanvas.height);
            }
        }

        // Función para ocultar todos los elementos
        function ocultarTodosElementos() {
            videoPlayer.style.display = 'none';
            documentFrame.style.display = 'none';
            imageViewer.style.display = 'none';
            cleanupPDF();
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
                    stopCountdownOverlay();
                    // Video
                    currentType = 'video';
                    videoPlayer.src = docActual;
                    videoPlayer.style.display = 'block';
                    
                    // No forzamos audio para cumplir políticas de autoplay en navegadores.
                    // Mantener muted=true permite autoplay; el audio podrá activarse mediante interacción del usuario.
                    videoPlayer.muted = true;

                    // Permitir unmute con un único click/tap del usuario (si se desea audio)
                    function unmuteOnInteraction() {
                        try {
                            if (videoPlayer && videoPlayer.muted) {
                                videoPlayer.muted = false;
                                console.log('Audio activado por interacción del usuario');
                            }
                        } catch (e) {
                            console.warn('unmuteOnInteraction error', e);
                        }
                        document.removeEventListener('click', unmuteOnInteraction);
                        document.removeEventListener('touchstart', unmuteOnInteraction);
                    }
                    document.addEventListener('click', unmuteOnInteraction);
                    document.addEventListener('touchstart', unmuteOnInteraction);
                    
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
                    
                    // Reproducir video (si el navegador lo permite). Si falla, avanzamos al siguiente.
                    videoPlayer.play().catch(function(error) {
                        console.error("Error al reproducir video:", error);
                        // En algunos navegadores (especialmente Firefox ESR) la reproducción automática
                        // con audio/estado puede estar bloqueada. En ese caso, avanzamos para no quedar bloqueados.
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
                        startCountdownOverlay(intervalo);
                        
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
                    // Antes de mostrar PDF, aseguramos limpieza de video y canvas anterior
                    cleanupVideo();
                    cleanupPDF();
                    // Usar PDF.js para renderizar a pantalla completa (sin UI del navegador)
                    currentType = 'pdf';
                    if (window.pdfjsLib) {
                        // Cargar y renderizar la primera página
                        pdfjsLib.getDocument(docActual).promise.then(function(pdf) {
                            return pdf.getPage(1);
                        }).then(function(page) {
                            var viewport = page.getViewport({ scale: 1 });
                            // Ajustar escala para caber en pantalla
                            var scale = Math.min(window.innerWidth / viewport.width, window.innerHeight / viewport.height);
                            var scaledViewport = page.getViewport({ scale: scale });
                            pdfCanvas.width = scaledViewport.width;
                            pdfCanvas.height = scaledViewport.height;
                            pdfCanvas.style.display = 'block';
                            var ctx = pdfCanvas.getContext('2d');
                            var renderContext = {
                                canvasContext: ctx,
                                viewport: scaledViewport
                            };
                            pdfRenderingTask = page.render(renderContext);
                            startCountdownOverlay(intervalo);
                            // Avanzar al siguiente documento pasado el tiempo
                            avanzarIndice();
                            timeoutHandle = setTimeout(function() {
                                cleanupPDF();
                                mostrarSiguienteDocumento();
                            }, intervalo);
                        }).catch(function(err){
                            console.error('Error renderizando PDF:', err);
                            avanzarIndice();
                            mostrarSiguienteDocumento();
                        });
                    } else {
                        // Fallback: mostrar en iframe si PDF.js no está disponible
                        documentFrame.src = docActual;
                        documentFrame.style.display = 'block';
                        startCountdownOverlay(intervalo);
                        avanzarIndice();
                        timeoutHandle = setTimeout(mostrarSiguienteDocumento, intervalo);
                    }
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
        refrescarEstadoOverlay();
        mostrarSiguienteDocumento(); // Mostrar el primer documento inmediatamente
        refrescarIntervaloDesdeServidor();
        intervaloPollHandle = setInterval(refrescarIntervaloDesdeServidor, 15000);
        overlayPollHandle = setInterval(refrescarEstadoOverlay, 5000);
    </script>
</body>

</html>
