<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: eventos.php');
    exit;
}

$organizador = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}

$titulo = trim(is_string($_POST['titulo'] ?? null) ? $_POST['titulo'] : '');
$descripcion = trim(is_string($_POST['descripcion'] ?? null) ? $_POST['descripcion'] : '');
$tipoEvento = trim(is_string($_POST['tipo_evento'] ?? null) ? $_POST['tipo_evento'] : '');
$tiposEventoPermitidos = ['Competencias', 'Carreras', 'Deportivo', 'Discotecas', 'Cultural', 'Feria', 'Otro'];
if (
    $titulo === '' || mb_strlen($titulo, 'UTF-8') > 160
    || $descripcion === '' || mb_strlen($descripcion, 'UTF-8') > 2000
    || !in_array($tipoEvento, $tiposEventoPermitidos, true)
) {
    http_response_code(422);
    exit('Completa un título, una descripción y un tipo de evento válidos.');
}

$archivoFoto = $_FILES['foto'] ?? null;
if (!is_array($archivoFoto) || ($archivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    http_response_code(422);
    exit('Selecciona una foto para agregar a la galería.');
}
if (($archivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    http_response_code(
        in_array($archivoFoto['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 413 : 422
    );
    exit('No se pudo recibir la foto. Probá con un archivo más pequeño.');
}
if (($archivoFoto['size'] ?? 0) > 5 * 1024 * 1024) {
    http_response_code(413);
    exit('La foto no puede superar los 5 MB.');
}

$extensionesPorMime = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
$mimeFoto = (new finfo(FILEINFO_MIME_TYPE))->file($archivoFoto['tmp_name']);
if (!isset($extensionesPorMime[$mimeFoto]) || @getimagesize($archivoFoto['tmp_name']) === false) {
    http_response_code(422);
    exit('El archivo debe ser una imagen JPG, PNG o WEBP válida.');
}

$directorioFotos = __DIR__ . '/../uploads/galeria-eventos';
if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0775, true) && !is_dir($directorioFotos)) {
    error_log('No se pudo crear el directorio para fotos de la galería de eventos.');
    http_response_code(500);
    exit('No se pudo guardar la foto.');
}
$nombreArchivo = 'foto_' . bin2hex(random_bytes(16)) . '.' . $extensionesPorMime[$mimeFoto];
$rutaFoto = $directorioFotos . '/' . $nombreArchivo;
if (!move_uploaded_file($archivoFoto['tmp_name'], $rutaFoto)) {
    error_log('No se pudo mover una foto subida a la galería de eventos.');
    http_response_code(500);
    exit('No se pudo guardar la foto.');
}

try {
    $guardarFoto = $pdo->prepare('INSERT INTO galeria_eventos (imagen, titulo, descripcion, tipo_evento, usuario_id)
        VALUES (:imagen, :titulo, :descripcion, :tipo_evento, :usuario_id)');
    $guardarFoto->execute([
        ':imagen' => '../uploads/galeria-eventos/' . $nombreArchivo,
        ':titulo' => $titulo,
        ':descripcion' => $descripcion,
        ':tipo_evento' => $tipoEvento,
        ':usuario_id' => (int) $organizador['id'],
    ]);
} catch (Throwable $error) {
    if (is_file($rutaFoto)) {
        unlink($rutaFoto);
    }
    error_log('Error al guardar foto de galería de eventos: ' . $error->getMessage());
    http_response_code(500);
    exit('No se pudo agregar la foto a la galería. Inténtalo nuevamente.');
}

header('Location: eventos.php?foto=guardada#galeria-contenedor');
exit;
