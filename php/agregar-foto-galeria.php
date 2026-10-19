<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$esAjax = (($_POST['ajax'] ?? $_GET['ajax'] ?? '') === '1');

function responderSubidaGaleria(array $payload, int $estado = 200, bool $esAjax = false): void
{
    if ($esAjax) {
        http_response_code($estado);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    if ($estado >= 400) {
        http_response_code($estado);
        exit((string) ($payload['message'] ?? 'No se pudo completar la operación.'));
    }
    header('Location: eventos.php?foto=guardada#galeria-contenedor');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    responderSubidaGaleria(['success' => false, 'message' => 'Método no permitido.'], 405, $esAjax);
}

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario || !in_array($usuario['rol'], ['administrador', 'organizador'], true)) {
    responderSubidaGaleria(['success' => false, 'message' => 'No tienes permiso para subir fotos a la galería.'], 403, $esAjax);
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    responderSubidaGaleria(['success' => false, 'message' => 'La solicitud expiró. Recarga la página e inténtalo de nuevo.'], 403, $esAjax);
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
    responderSubidaGaleria(['success' => false, 'message' => 'Completa un título, una descripción y un tipo de evento válidos.'], 422, $esAjax);
}

$campoFoto = $_FILES['foto'] ?? null;
if (!is_array($campoFoto) || !isset($campoFoto['name'])) {
    responderSubidaGaleria(['success' => false, 'message' => 'Selecciona al menos una foto para agregar a la galería.'], 422, $esAjax);
}

$archivos = [];
if (is_array($campoFoto['name'])) {
    foreach (array_keys($campoFoto['name']) as $indice) {
        $archivos[] = [
            'name' => $campoFoto['name'][$indice] ?? '',
            'tmp_name' => $campoFoto['tmp_name'][$indice] ?? '',
            'error' => $campoFoto['error'][$indice] ?? UPLOAD_ERR_NO_FILE,
            'size' => $campoFoto['size'][$indice] ?? 0,
        ];
    }
} else {
    $archivos[] = $campoFoto;
}

if (!$archivos || (count($archivos) === 1 && ($archivos[0]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE)) {
    responderSubidaGaleria(['success' => false, 'message' => 'Selecciona al menos una foto para agregar a la galería.'], 422, $esAjax);
}

$directorioFotos = __DIR__ . '/../uploads/galeria-eventos';
if (!is_dir($directorioFotos) && !mkdir($directorioFotos, 0775, true) && !is_dir($directorioFotos)) {
    error_log('No se pudo crear el directorio para fotos de la galería de eventos.');
    responderSubidaGaleria(['success' => false, 'message' => 'No se pudo guardar ninguna foto.'], 500, $esAjax);
}

$extensionesPorMime = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
];
$resultados = [];
$fotosGuardadas = [];
$cantidadMaxima = 10;

foreach ($archivos as $indice => $archivo) {
    $nombreOriginal = is_string($archivo['name'] ?? null) ? basename($archivo['name']) : 'Foto ' . ($indice + 1);
    if ($indice >= $cantidadMaxima) {
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => 'Se permiten hasta 10 fotos por operación.'];
        continue;
    }

    $errorSubida = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($errorSubida !== UPLOAD_ERR_OK) {
        $mensajeError = in_array($errorSubida, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'El archivo supera el límite de tamaño del servidor.'
            : ($errorSubida === UPLOAD_ERR_NO_FILE ? 'No se seleccionó un archivo.' : 'No se pudo recibir el archivo.');
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => $mensajeError];
        continue;
    }

    $rutaTemporal = (string) ($archivo['tmp_name'] ?? '');
    $tamano = (int) ($archivo['size'] ?? 0);
    if ($tamano <= 0 || $tamano > 5 * 1024 * 1024 || !is_uploaded_file($rutaTemporal)) {
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => 'La foto debe pesar más de 0 y no superar los 5 MB.'];
        continue;
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($rutaTemporal);
    $datosImagen = @getimagesize($rutaTemporal);
    if (
        !isset($extensionesPorMime[$mime])
        || $datosImagen === false
        || !in_array($datosImagen[2] ?? null, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)
        || ($datosImagen[0] ?? 0) < 1
        || ($datosImagen[1] ?? 0) < 1
        || ($datosImagen[0] ?? 0) > 10000
        || ($datosImagen[1] ?? 0) > 10000
        || ($datosImagen[0] ?? 0) * ($datosImagen[1] ?? 0) > 40000000
    ) {
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => 'El archivo debe ser una imagen JPG, PNG o WEBP válida.'];
        continue;
    }

    $nombreArchivo = sigtur_nombre_imagen_subida((int) $usuario['id'], $nombreOriginal, $extensionesPorMime[$mime]);
    $rutaFoto = $directorioFotos . DIRECTORY_SEPARATOR . $nombreArchivo;
    if (!move_uploaded_file($rutaTemporal, $rutaFoto)) {
        error_log('No se pudo mover una foto subida a la galería de eventos.');
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => 'No se pudo guardar el archivo.'];
        continue;
    }

    try {
        $guardarFoto = $pdo->prepare('INSERT INTO galeria_eventos (imagen, titulo, descripcion, tipo_evento, usuario_id)
            VALUES (:imagen, :titulo, :descripcion, :tipo_evento, :usuario_id)');
        $guardarFoto->execute([
            ':imagen' => $nombreArchivo,
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':tipo_evento' => $tipoEvento,
            ':usuario_id' => (int) $usuario['id'],
        ]);
    } catch (Throwable $error) {
        if (is_file($rutaFoto) && !unlink($rutaFoto)) {
            error_log('No se pudo limpiar una foto cuyo registro de galería no se guardó.');
        }
        error_log('Error al guardar foto de galería de eventos: ' . $error->getMessage());
        $resultados[] = ['name' => $nombreOriginal, 'success' => false, 'message' => 'No se pudo guardar esta foto en la galería.'];
        continue;
    }

    $fotoGuardada = [
        'id' => (int) $pdo->lastInsertId(),
        'imagen' => '../uploads/galeria-eventos/' . rawurlencode($nombreArchivo),
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'categoria' => $tipoEvento,
        'usuario_id' => (int) $usuario['id'],
        'usuario_nombre' => (string) ($usuario['nombre_completo'] ?? 'Usuario'),
        'usuario_perfil_publico' => true,
        'fecha' => date('d/m/Y'),
    ];
    $fotosGuardadas[] = $fotoGuardada;
    $resultados[] = ['name' => $nombreOriginal, 'success' => true, 'message' => 'Subida correctamente.'];
}

$subidasCorrectas = count($fotosGuardadas);
if ($esAjax) {
    responderSubidaGaleria([
        'success' => $subidasCorrectas > 0,
        'message' => $subidasCorrectas > 0
            ? "Se agregaron {$subidasCorrectas} foto(s) a la galería."
            : 'No se pudo subir ninguna foto.',
        'results' => $resultados,
        'fotos' => $fotosGuardadas,
    ], $subidasCorrectas > 0 ? 200 : 422, true);
}

if ($subidasCorrectas === 0) {
    responderSubidaGaleria(['success' => false, 'message' => $resultados[0]['message'] ?? 'No se pudo agregar la foto a la galería.'], 422);
}
responderSubidaGaleria(['success' => true], 200);
