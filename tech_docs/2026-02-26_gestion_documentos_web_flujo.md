# Plan y Ejecución: Gestión Web de Documentos (`/docs`)

## Objetivo
Permitir al usuario gestor autenticado administrar los archivos de presentación en `www/html/docs` desde la web de `estado_quioscos`, con vista previa, subida, borrado y protección de acciones.

## Reglas funcionales
- Acceso solo con sesión activa de gestor.
- Pantalla dedicada: `docs_manager.php` (no modal).
- Listado de archivos con: nombre, tipo, tamaño, fecha.
- Vista previa integrada para imagen/PDF/MP4.
- Subida de archivos permitidos: `jpg`, `jpeg`, `png`, `webp`, `pdf`, `mp4`.
- Nombres válidos: letras, números, `.`, `-`, `_` (sin espacios ni caracteres extraños).
- Si el archivo existe: opción de sobrescribir **solo con confirmación explícita**.
- Borrado: confirmación explícita antes de ejecutar.

## Seguridad
- Endpoints de gestión protegidos con autenticación de sesión.
- Acciones sensibles (`upload`, `delete`) solo por `POST`.
- Token CSRF obligatorio en subida y borrado.
- Validación de nombre de archivo y extensión.
- Validación MIME real en servidor para subida.
- Protección de ruta (sin path traversal): solo archivos base dentro de directorio `docs`.

## Implementación prevista
1. Crear `docs_manager.php` con UI de listado/subida/vista previa.
2. Crear `docs_api.php` para acciones `list`, `upload`, `delete`.
3. Crear `docs_preview.php` para previsualización autenticada por nombre.
4. Añadir helpers CSRF en `auth_lib.php`.
5. Añadir acceso en menú de `estado_quioscos/index.html`.
6. Desplegar cambios a `/var/www/html/estado_quioscos` y probar.
