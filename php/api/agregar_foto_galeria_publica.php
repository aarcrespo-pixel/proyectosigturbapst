<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/imagenes.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario || $usuario['rol'] !== 'organizador') {
    http_response_code(403);
    exit('Solo los organizadores pueden agregar fotos a estas galerías.');
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La sesión expiró. Recargá la página e intentá nuevamente.');
}

$tipoGaleria = (string) ($_POST['tipo_galeria'] ?? '');
$paginasGaleria = [
    'Turismo' => ['archivo' => 'turismo.php', 'ancla' => 'galeria-turistica'],
    'Lugares' => ['archivo' => 'lugares.php', 'ancla' => 'galeria-historica'],
];
if (!isset($paginasGaleria[$tipoGaleria])) {
    http_response_code(422);
    exit('La galería indicada no es válida.');
}

$campoFotos = $_FILES['fotos'] ?? null;
if (!is_array($campoFotos) || !is_array($campoFotos['name'] ?? null)) {
    http_response_code(422);
    exit('Seleccioná al menos una foto para agregar.');
}

$cantidadFotos = count($campoFotos['name']);
if ($cantidadFotos < 1 || $cantidadFotos > 10) {
    http_response_code(422);
    exit('Podés agregar entre 1 y 10 fotos por vez.');
}

$directorioFotos = __DIR__ . '/../../uploads/galeria-eventos';
if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0775, true) && !is_dir($directorioFotos)) {
    error_log('No se pudo crear el directorio para la galería turística.');
    http_response_code(500);
    exit('No se pudo preparar el espacio para guardar las fotos.');
}

$extensionesPermitidas = [
    'image/jpeg' => ['extension' => 'jpg', 'tipo' => IMAGETYPE_JPEG],
    'image/png' => ['extension' => 'png', 'tipo' => IMAGETYPE_PNG],
    'image/webp' => ['extension' => 'webp', 'tipo' => IMAGETYPE_WEBP],
];
$guardadas = 0;
$errores = [];

for ($indice = 0; $indice < $cantidadFotos; $indice++) {
    $nombreOriginal = basename((string) ($campoFotos['name'][$indice] ?? 'foto'));
    $rutaTemporal = (string) ($campoFotos['tmp_name'][$indice] ?? '');
    $errorSubida = (int) ($campoFotos['error'][$indice] ?? UPLOAD_ERR_NO_FILE);
    $tamano = (int) ($campoFotos['size'][$indice] ?? 0);

    if ($errorSubida !== UPLOAD_ERR_OK) {
        $errores[] = $nombreOriginal . ': no se pudo recibir el archivo.';
        continue;
    }
    if ($tamano <= 0 || $tamano > 5 * 1024 * 1024 || !is_uploaded_file($rutaTemporal)) {
        $errores[] = $nombreOriginal . ': debe pesar más de 0 y no superar los 5 MB.';
        continue;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($rutaTemporal);
    $datosImagen = @getimagesize($rutaTemporal);
    if (
        !isset($extensionesPermitidas[$mime])
        || $datosImagen === false
        || ($datosImagen[2] ?? null) !== ($extensionesPermitidas[$mime]['tipo'] ?? null)
        || ($datosImagen[0] ?? 0) < 1
        || ($datosImagen[1] ?? 0) < 1
    ) {
        $errores[] = $nombreOriginal . ': elegí una imagen JPG, PNG o WEBP válida.';
        continue;
    }

    $nombreArchivo = sigtur_nombre_imagen_subida(
        (int) $usuario['id'],
        $nombreOriginal,
        $extensionesPermitidas[$mime]['extension']
    );
    $rutaFoto = $directorioFotos . DIRECTORY_SEPARATOR . $nombreArchivo;
    if (!move_uploaded_file($rutaTemporal, $rutaFoto)) {
        error_log('No se pudo guardar una foto de la galería turística.');
        $errores[] = $nombreOriginal . ': no se pudo guardar el archivo.';
        continue;
    }

    try {
        $guardarFoto = $pdo->prepare(
            "INSERT INTO galeria_eventos (imagen, titulo, descripcion, tipo_evento, usuario_id)
             VALUES (:imagen, :titulo, '', :tipo_evento, :usuario_id)"
        );
        $titulo = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $guardarFoto->execute([
            ':imagen' => $nombreArchivo,
            ':titulo' => mb_substr($titulo !== '' ? $titulo : 'Foto turística', 0, 160, 'UTF-8'),
            ':tipo_evento' => $tipoGaleria,
            ':usuario_id' => (int) $usuario['id'],
        ]);
        $guardadas++;
    } catch (Throwable $error) {
        if (is_file($rutaFoto) && !unlink($rutaFoto)) {
            error_log('No se pudo limpiar una foto turística sin registro en la base de datos.');
        }
        error_log('Error al guardar foto turística: ' . $error->getMessage());
        $errores[] = $nombreOriginal . ': no se pudo guardar en la galería.';
    }
}

if ($guardadas === 0) {
    http_response_code(422);
    exit(htmlspecialchars(implode(' ', $errores) ?: 'No se pudo agregar ninguna foto.', ENT_QUOTES, 'UTF-8'));
}

$paginaGaleria = $paginasGaleria[$tipoGaleria];
header('Location: ../' . $paginaGaleria['archivo'] . '?fotos_agregadas=' . $guardadas . ($errores ? '&algunas_fotos_fallaron=1' : '') . '#' . $paginaGaleria['ancla']);
exit;
