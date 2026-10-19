<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: panel-organizador.php#mis-fotos');
    exit;
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}

$fotoId = filter_var($_POST['foto_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$fotoId) {
    http_response_code(422);
    exit('La foto indicada no es válida.');
}

$consultaFoto = $pdo->prepare('SELECT id, imagen FROM galeria_eventos WHERE id = :id AND usuario_id = :usuario LIMIT 1');
$consultaFoto->execute([':id' => $fotoId, ':usuario' => (int) $usuario['id']]);
$fotoActual = $consultaFoto->fetch(PDO::FETCH_ASSOC);
if (!$fotoActual) {
    http_response_code(404);
    exit('La foto no existe o no tienes permiso para editarla.');
}

$titulo = trim(is_string($_POST['titulo'] ?? null) ? $_POST['titulo'] : '');
$descripcion = trim(is_string($_POST['descripcion'] ?? null) ? $_POST['descripcion'] : '');
$tipoEvento = trim(is_string($_POST['tipo_evento'] ?? null) ? $_POST['tipo_evento'] : '');
$tiposPermitidos = ['Competencias', 'Carreras', 'Deportivo', 'Discotecas', 'Cultural', 'Feria', 'Otro'];
if (
    $titulo === '' || mb_strlen($titulo, 'UTF-8') > 160
    || $descripcion === '' || mb_strlen($descripcion, 'UTF-8') > 2000
    || !in_array($tipoEvento, $tiposPermitidos, true)
) {
    http_response_code(422);
    exit('Completa un título, una descripción y un tipo de evento válidos.');
}

$imagen = (string) $fotoActual['imagen'];
$rutaNuevaImagen = null;
$archivoImagen = $_FILES['foto'] ?? null;
if (is_array($archivoImagen) && ($archivoImagen['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($archivoImagen['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        http_response_code(in_array($archivoImagen['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 413 : 422);
        exit('No se pudo recibir la imagen.');
    }
    if (
        (int) ($archivoImagen['size'] ?? 0) <= 0
        || (int) $archivoImagen['size'] > 5 * 1024 * 1024
        || !is_uploaded_file((string) ($archivoImagen['tmp_name'] ?? ''))
    ) {
        http_response_code(413);
        exit('La imagen debe pesar más de 0 y no superar los 5 MB.');
    }

    $extensionesPermitidas = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeImagen = (new finfo(FILEINFO_MIME_TYPE))->file($archivoImagen['tmp_name']);
    $datosImagen = @getimagesize($archivoImagen['tmp_name']);
    if (
        !isset($extensionesPermitidas[$mimeImagen])
        || $datosImagen === false
        || !in_array($datosImagen[2] ?? null, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)
        || ($datosImagen[0] ?? 0) < 1
        || ($datosImagen[1] ?? 0) < 1
        || ($datosImagen[0] ?? 0) > 10000
        || ($datosImagen[1] ?? 0) > 10000
        || ($datosImagen[0] ?? 0) * ($datosImagen[1] ?? 0) > 40000000
    ) {
        http_response_code(422);
        exit('El archivo debe ser una imagen JPG, PNG o WEBP válida.');
    }

    $directorioFotos = __DIR__ . '/../uploads/galeria-eventos';
    if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0775, true) && !is_dir($directorioFotos)) {
        http_response_code(500);
        exit('No se pudo preparar la carpeta de fotos.');
    }
    $nombreImagen = sigtur_nombre_imagen_subida((int) $usuario['id'], (string) ($archivoImagen['name'] ?? 'foto'), $extensionesPermitidas[$mimeImagen]);
    $rutaNuevaImagen = $directorioFotos . DIRECTORY_SEPARATOR . $nombreImagen;
    if (!move_uploaded_file($archivoImagen['tmp_name'], $rutaNuevaImagen)) {
        http_response_code(500);
        exit('No se pudo guardar la nueva imagen.');
    }
    $imagen = $nombreImagen;
}

try {
    $actualizarFoto = $pdo->prepare('UPDATE galeria_eventos SET imagen = :imagen, titulo = :titulo, descripcion = :descripcion, tipo_evento = :tipo_evento WHERE id = :id AND usuario_id = :usuario');
    $actualizarFoto->execute([
        ':imagen' => $imagen,
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':tipo_evento' => $tipoEvento,
        ':id' => (int) $fotoId,
        ':usuario' => (int) $usuario['id'],
    ]);
} catch (Throwable $error) {
    if ($rutaNuevaImagen && is_file($rutaNuevaImagen) && !unlink($rutaNuevaImagen)) {
        error_log('No se pudo limpiar la imagen nueva tras fallar la actualización de galería: ' . $fotoId);
    }
    throw $error;
}

if ($rutaNuevaImagen) {
    $nombreAnterior = basename(str_replace('\\', '/', (string) $fotoActual['imagen']));
    if (preg_match('/^(?:foto_[a-f0-9]{32}|usuario-\d+-[a-z0-9-]+-[a-f0-9]{32})\.(jpg|png|webp)$/D', $nombreAnterior) === 1) {
        $directorioReal = realpath(__DIR__ . '/../uploads/galeria-eventos');
        $archivoAnteriorReal = $directorioReal !== false ? realpath($directorioReal . DIRECTORY_SEPARATOR . $nombreAnterior) : false;
        if ($archivoAnteriorReal !== false && dirname($archivoAnteriorReal) === $directorioReal && is_file($archivoAnteriorReal) && !unlink($archivoAnteriorReal)) {
            error_log('No se pudo eliminar la imagen reemplazada de galería: ' . $fotoId);
        }
    }
}

header('Location: panel-organizador.php?galeria=actualizada#mis-fotos');
exit;
