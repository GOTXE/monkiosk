<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos en Bucle</title>
    <style>
        
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
        var intervalo = 5000; // Tiempo para cambiar de documento (en milisegundos) CAMBIAR EN PRODUCCION
        var recarga = 10000; // Tiempo para recargar la página (en milisegundos) CAMBIAR EN PRODUCCION

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
