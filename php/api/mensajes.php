<?php
/* El buffer se inicia antes de cargar PDO para capturar warnings de conexión
   y evitar que PHP anteponga HTML a la respuesta JSON del frontend. */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderMensajes(array $payload, int $status = 200): void
{
    http_response_code($status);
    ob_end_clean();
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    session_start();
    require_once __DIR__ . '/../conexion.php';
    require_once __DIR__ . '/../helpers/censura.php';
    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
    if ($usuarioId <= 0) responderMensajes(['success' => false, 'error' => 'Debes iniciar sesión para usar los mensajes'], 401);

    $accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'conversaciones');
    $otroUsuarioId = (int) ($_GET['usuario_id'] ?? $_POST['usuario_id'] ?? $_GET['receptor_id'] ?? $_POST['receptor_id'] ?? 0);
    if (!isset($_GET['accion'], $_POST['accion']) && $otroUsuarioId > 0) {
        $accion = 'conversacion';
    }
    if ($accion === 'enviar') {
        $texto = censurarTexto(trim((string) ($_POST['mensaje'] ?? '')));
        $fotoUrl = null;
        $audioUrl = null;
        $directorioChat = __DIR__ . '/../../uploads/chat';
        if (!is_dir($directorioChat)) mkdir($directorioChat, 0775, true);
        /* multipart/form-data deja los archivos en $_FILES; validamos MIME y
           extensión en servidor antes de moverlos al directorio público. */
        foreach (['foto' => 'fotoUrl', 'audio' => 'audioUrl'] as $campo => $variable) {
            if (empty($_FILES[$campo]) || $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) continue;
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$campo]['tmp_name']);
            $permitidos = $campo === 'foto' ? ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'] : ['audio/webm' => 'webm', 'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3', 'audio/wav' => 'wav'];
            if (!isset($permitidos[$mime])) responderMensajes(['success' => false, 'error' => 'Tipo de archivo no permitido'], 422);
            $nombreArchivo = $campo . '_' . bin2hex(random_bytes(12)) . '.' . $permitidos[$mime];
            if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $directorioChat . '/' . $nombreArchivo)) responderMensajes(['success' => false, 'error' => 'No se pudo guardar el archivo'], 500);
            ${$variable} = 'uploads/chat/' . $nombreArchivo;
        }
        if ($otroUsuarioId <= 0 || $otroUsuarioId === $usuarioId || ($texto === '' && !$fotoUrl && !$audioUrl) || mb_strlen($texto) > 500) {
            responderMensajes(['success' => false, 'error' => 'Revisá el destinatario y el mensaje.'], 422);
        }
        /* PDO separa valores del SQL y la validación limita la entrada antes de
           insertarla, evitando inyección y datos fuera del contrato. */
        $insertar = $pdo->prepare('INSERT INTO mensajes (remitente_id, destinatario_id, mensaje, foto_url, audio_url) VALUES (:emisor, :receptor, :mensaje, :foto, :audio)');
        $insertar->execute([':emisor' => $usuarioId, ':receptor' => $otroUsuarioId, ':mensaje' => $texto, ':foto' => $fotoUrl, ':audio' => $audioUrl]);
    }
    if ($accion === 'leer' && $otroUsuarioId > 0) {
        $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :otro AND destinatario_id = :usuario');
        $marcar->execute([':otro' => $otroUsuarioId, ':usuario' => $usuarioId]);
    }
    if ($accion === 'conversacion' && $otroUsuarioId > 0) {
        $consulta = $pdo->prepare('SELECT m.id, m.remitente_id, m.destinatario_id, m.mensaje, m.foto_url, m.audio_url, m.fecha_creacion, m.leido, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil FROM mensajes m INNER JOIN usuarios u ON u.id = m.remitente_id WHERE (m.remitente_id = :usuario AND m.destinatario_id = :otro) OR (m.remitente_id = :otro AND m.destinatario_id = :usuario) ORDER BY m.fecha_creacion ASC, m.id ASC LIMIT 100');
        $consulta->execute([':usuario' => $usuarioId, ':otro' => $otroUsuarioId]);
        $mensajes = $consulta->fetchAll();
        responderMensajes(['success' => true, 'data' => $mensajes, 'messages' => $mensajes]);
    }
    $consulta = $pdo->prepare('SELECT u.id AS usuario_id, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil, MAX(m.fecha_creacion) AS ultima_fecha, SUM(CASE WHEN m.destinatario_id = :usuario AND m.leido = 0 THEN 1 ELSE 0 END) AS no_leidos FROM mensajes m INNER JOIN usuarios u ON u.id = CASE WHEN m.remitente_id = :usuario2 THEN m.destinatario_id ELSE m.remitente_id END WHERE m.remitente_id = :usuario3 OR m.destinatario_id = :usuario4 GROUP BY u.id, u.nombre_completo, u.nickname, u.foto_perfil, u.avatar ORDER BY ultima_fecha DESC');
    $consulta->execute([':usuario' => $usuarioId, ':usuario2' => $usuarioId, ':usuario3' => $usuarioId, ':usuario4' => $usuarioId]);
    $conversaciones = $consulta->fetchAll();
    responderMensajes(['success' => true, 'data' => $conversaciones, 'conversations' => $conversaciones]);
} catch (Throwable $exception) {
    /* El buffer se limpia también en errores de MySQL para que el cliente reciba
       JSON válido en lugar del HTML generado por un warning o notice. */
    responderMensajes(['success' => false, 'error' => 'No se pudo cargar la mensajería.'], 500);
}