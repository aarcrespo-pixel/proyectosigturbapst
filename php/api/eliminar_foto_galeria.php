<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderEliminacionGaleria(array $payload, int $estado = 200): void
{
    http_response_code($estado);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    session_start();
    require_once __DIR__ . '/../conexion.php';
    require_once __DIR__ . '/../helpers/autorizacion.php';

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        responderEliminacionGaleria(['success' => false, 'message' => 'Método no permitido.'], 405);
    }
    $usuario = sigtur_usuario_actual($pdo);
    if (!$usuario || !in_array($usuario['rol'], ['administrador', 'organizador'], true)) {
        responderEliminacionGaleria(['success' => false, 'message' => 'No tienes permiso para eliminar fotos de la galería.'], 403);
    }
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        responderEliminacionGaleria(['success' => false, 'message' => 'La sesión expiró. Recarga la página e inténtalo de nuevo.'], 403);
    }

    $fotoId = filter_var($_POST['foto_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$fotoId) {
        responderEliminacionGaleria(['success' => false, 'message' => 'La foto indicada no es válida.'], 422);
    }

    $consulta = $pdo->prepare('SELECT id, imagen, usuario_id FROM galeria_eventos WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $fotoId]);
    $foto = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$foto) {
        responderEliminacionGaleria(['success' => false, 'message' => 'La foto ya no existe en la galería.'], 404);
    }
    if ($usuario['rol'] === 'organizador' && (int) $foto['usuario_id'] !== (int) $usuario['id']) {
        responderEliminacionGaleria(['success' => false, 'message' => 'Solo puedes eliminar las fotos que agregaste a la galería.'], 403);
    }

    $borrarRegistro = $pdo->prepare('DELETE FROM galeria_eventos WHERE id = :id');
    $borrarRegistro->execute([':id' => $fotoId]);
    if ($borrarRegistro->rowCount() !== 1) {
        responderEliminacionGaleria(['success' => false, 'message' => 'No se pudo eliminar la foto de la galería.'], 409);
    }

    $archivoEliminado = false;
    $advertencia = null;
    $rutaRelativa = str_replace('\\', '/', (string) $foto['imagen']);
    $prefijoGaleria = '../uploads/galeria-eventos/';
    $nombreArchivo = str_starts_with($rutaRelativa, $prefijoGaleria)
        ? substr($rutaRelativa, strlen($prefijoGaleria))
        : '';
    if (
        $nombreArchivo !== ''
        && basename($nombreArchivo) === $nombreArchivo
        && preg_match('/^foto_[a-f0-9]{32}\.(jpg|png|webp)$/D', $nombreArchivo) === 1
    ) {
        $directorioReal = realpath(__DIR__ . '/../../uploads/galeria-eventos');
        $archivoReal = $directorioReal !== false ? realpath($directorioReal . DIRECTORY_SEPARATOR . $nombreArchivo) : false;
        if ($archivoReal !== false && dirname($archivoReal) === $directorioReal && is_file($archivoReal)) {
            $archivoEliminado = unlink($archivoReal);
            if (!$archivoEliminado) {
                error_log('No se pudo eliminar el archivo físico de una foto de galería: ' . $fotoId);
                $advertencia = 'La foto se quitó de la galería, pero no se pudo eliminar su archivo físico.';
            }
        } else {
            $advertencia = 'La foto se quitó de la galería; su archivo físico ya no estaba disponible.';
        }
    } else {
        $advertencia = 'La foto se quitó de la galería, pero su archivo no estaba en el directorio administrado de galerías.';
    }

    responderEliminacionGaleria([
        'success' => true,
        'message' => 'La foto se eliminó de la galería.',
        'warning' => $advertencia,
        'file_deleted' => $archivoEliminado,
    ]);
} catch (Throwable $error) {
    error_log('Error al eliminar foto de galería: ' . $error->getMessage());
    responderEliminacionGaleria(['success' => false, 'message' => 'Ocurrió un error al eliminar la foto. Inténtalo nuevamente.'], 500);
}
