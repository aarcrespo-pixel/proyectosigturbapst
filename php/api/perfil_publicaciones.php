<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
require_once __DIR__ . '/../helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();
require_once __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

function responderPerfilPublicaciones(array $payload, int $status = 200): void
{
    http_response_code($status);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $usuario = sigtur_usuario_actual($pdo);
    if (!$usuario) {
        responderPerfilPublicaciones(['success' => false, 'message' => 'Iniciá sesión para continuar.'], 401);
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        responderPerfilPublicaciones(['success' => false, 'message' => 'Método no permitido.'], 405);
    }
    $token = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null;
    if (!sigtur_validar_csrf($token)) {
        responderPerfilPublicaciones(['success' => false, 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], 403);
    }

    $usuarioId = (int) $usuario['id'];
    $accion = (string) ($_POST['accion'] ?? '');

    if ($accion === 'publicar') {
        $archivo = $_FILES['imagen'] ?? null;
        if (!is_array($archivo) || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'Seleccioná una imagen para publicar.'], 422);
        }
        if ((int) ($archivo['size'] ?? 0) <= 0 || (int) $archivo['size'] > 10 * 1024 * 1024 || !is_uploaded_file($archivo['tmp_name'])) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'La imagen debe pesar menos de 10 MB.'], 413);
        }

        $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        $datosImagen = @getimagesize($archivo['tmp_name']);
        if (!isset($tiposPermitidos[$mime]) || $datosImagen === false || ($datosImagen['mime'] ?? '') !== $mime) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'Usá una imagen JPG, PNG o WEBP válida.'], 422);
        }
        $ancho = (int) ($datosImagen[0] ?? 0);
        $alto = (int) ($datosImagen[1] ?? 0);
        if ($ancho <= 0 || $alto <= 0 || $ancho > 10000 || $alto > 10000 || $ancho * $alto > 40000000) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'La imagen tiene dimensiones no válidas.'], 422);
        }

        $directorio = __DIR__ . '/../../uploads/publicaciones';
        if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'No se pudo preparar la carpeta de publicaciones.'], 500);
        }
        $nombreArchivo = 'publicacion_' . bin2hex(random_bytes(16)) . '.' . $tiposPermitidos[$mime];
        $rutaArchivo = $directorio . DIRECTORY_SEPARATOR . $nombreArchivo;
        if (!move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'No se pudo guardar la imagen.'], 500);
        }

        try {
            $insertar = $pdo->prepare('INSERT INTO publicaciones (usuario_id, imagen, descripcion) VALUES (:usuario_id, :imagen, NULL)');
            $insertar->execute([':usuario_id' => $usuarioId, ':imagen' => 'uploads/publicaciones/' . $nombreArchivo]);
        } catch (Throwable $exception) {
            if (is_file($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            throw $exception;
        }

        responderPerfilPublicaciones(['success' => true, 'id' => (int) $pdo->lastInsertId(), 'message' => 'Imagen publicada.'], 201);
    }

    if ($accion === 'like') {
        $publicacionId = filter_var($_POST['publicacion_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$publicacionId) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'La publicación seleccionada no es válida.'], 422);
        }
        $consultaPublicacion = $pdo->prepare('SELECT p.usuario_id, u.perfil_publico FROM publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id WHERE p.id = :id LIMIT 1');
        $consultaPublicacion->execute([':id' => $publicacionId]);
        $publicacion = $consultaPublicacion->fetch(PDO::FETCH_ASSOC);
        if (!$publicacion) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'La publicación ya no está disponible.'], 404);
        }
        if (!(bool) $publicacion['perfil_publico'] && (int) $publicacion['usuario_id'] !== $usuarioId) {
            $consultaSeguidor = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
            $consultaSeguidor->execute([':seguidor' => $usuarioId, ':seguido' => (int) $publicacion['usuario_id']]);
            if (!$consultaSeguidor->fetchColumn()) {
                responderPerfilPublicaciones(['success' => false, 'message' => 'No podés interactuar con este perfil privado.'], 403);
            }
        }

        $pdo->beginTransaction();
        $consultaLike = $pdo->prepare('SELECT id FROM likes_publicaciones WHERE usuario_id = :usuario_id AND publicacion_id = :publicacion_id LIMIT 1 FOR UPDATE');
        $consultaLike->execute([':usuario_id' => $usuarioId, ':publicacion_id' => $publicacionId]);
        $likeExistente = $consultaLike->fetchColumn();
        if ($likeExistente !== false) {
            $eliminarLike = $pdo->prepare('DELETE FROM likes_publicaciones WHERE id = :id');
            $eliminarLike->execute([':id' => (int) $likeExistente]);
            $marcado = false;
        } else {
            $insertarLike = $pdo->prepare('INSERT IGNORE INTO likes_publicaciones (usuario_id, publicacion_id) VALUES (:usuario_id, :publicacion_id)');
            $insertarLike->execute([':usuario_id' => $usuarioId, ':publicacion_id' => $publicacionId]);
            $marcado = true;
        }
        $consultaTotal = $pdo->prepare('SELECT COUNT(*) FROM likes_publicaciones WHERE publicacion_id = :publicacion_id');
        $consultaTotal->execute([':publicacion_id' => $publicacionId]);
        $total = (int) $consultaTotal->fetchColumn();
        $pdo->commit();

        responderPerfilPublicaciones(['success' => true, 'liked' => $marcado, 'likes' => $total]);
    }

    if ($accion === 'eliminar') {
        $publicacionId = filter_var($_POST['publicacion_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$publicacionId) {
            responderPerfilPublicaciones(['success' => false, 'message' => 'La publicación seleccionada no es válida.'], 422);
        }

        $pdo->beginTransaction();
        $consultaPublicacion = $pdo->prepare('SELECT imagen FROM publicaciones WHERE id = :id AND usuario_id = :usuario_id LIMIT 1 FOR UPDATE');
        $consultaPublicacion->execute([':id' => $publicacionId, ':usuario_id' => $usuarioId]);
        $imagen = $consultaPublicacion->fetchColumn();
        if ($imagen === false) {
            $pdo->rollBack();
            responderPerfilPublicaciones(['success' => false, 'message' => 'No se encontró la publicación o no te pertenece.'], 404);
        }

        $eliminarComentarios = $pdo->prepare('DELETE FROM comentarios WHERE item_key = :item_key');
        $eliminarComentarios->execute([':item_key' => 'publicacion-' . (int) $publicacionId]);
        $eliminarPublicacion = $pdo->prepare('DELETE FROM publicaciones WHERE id = :id AND usuario_id = :usuario_id');
        $eliminarPublicacion->execute([':id' => $publicacionId, ':usuario_id' => $usuarioId]);
        if ($eliminarPublicacion->rowCount() !== 1) {
            throw new RuntimeException('La publicación dejó de estar disponible durante la eliminación.');
        }
        $pdo->commit();

        $nombreImagen = basename((string) $imagen);
        $rutaImagen = str_starts_with((string) $imagen, 'uploads/publicaciones/')
            ? __DIR__ . '/../../uploads/publicaciones/' . $nombreImagen
            : __DIR__ . '/../../uploads/banners/' . $nombreImagen;
        if ($nombreImagen !== '' && is_file($rutaImagen) && !unlink($rutaImagen)) {
            error_log('[perfil_publicaciones] No se pudo borrar el archivo ' . $nombreImagen);
        }

        responderPerfilPublicaciones(['success' => true, 'id' => (int) $publicacionId]);
    }

    responderPerfilPublicaciones(['success' => false, 'message' => 'Acción no reconocida.'], 422);
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[perfil_publicaciones] ' . $exception->getMessage());
    responderPerfilPublicaciones(['success' => false, 'message' => 'No se pudo completar la acción.'], 500);
}
