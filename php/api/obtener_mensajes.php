<?php
/* El buffer se inicia antes de cargar la conexión para impedir que warnings o
   notices se mezclen con JSON y provoquen "Unexpected token '<'" en Fetch. */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderHistorial(array $respuesta, int $estado = 200): void
{
    http_response_code($estado);
    /* ob_clean elimina cualquier warning o espacio accidental; solo después
       se cierra el buffer para que la respuesta sea exclusivamente JSON. */
    if (ob_get_level() > 0) {
        ob_clean();
        ob_end_clean();
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    session_start();
    require_once __DIR__ . '/../conexion.php';

    if (!isset($_SESSION['usuario_id']) || (int) $_SESSION['usuario_id'] <= 0) {
        responderHistorial(['success' => false, 'message' => 'Sesión de usuario no iniciada.'], 401);
    }
    $emisorId = (int) $_SESSION['usuario_id'];
    $receptorRaw = $_GET['receptor_id'] ?? $_POST['receptor_id'] ?? null;
    if ($receptorRaw === null || trim((string) $receptorRaw) === '') {
        responderHistorial(['success' => false, 'message' => 'El ID del receptor llegó vacío o indefinido.'], 422);
    }
    $receptorId = filter_var($receptorRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$receptorId || $receptorId === $emisorId) {
        responderHistorial(['success' => false, 'message' => 'El ID del receptor no es válido.'], 422);
    }

    /* La condición bidireccional recupera ambos sentidos con una sentencia
       preparada y orden temporal, sin interpolar IDs proporcionados por HTTP. */
    $sql = 'SELECT m.id, m.remitente_id AS emisor_id, m.destinatario_id AS receptor_id,
                   m.mensaje, m.foto_url, m.audio_url, m.fecha_creacion AS fecha, m.leido,
                   u.nombre_completo AS nombre_usuario, u.nickname,
                   COALESCE(u.foto_perfil, u.avatar) AS foto_perfil
            FROM mensajes m
            INNER JOIN usuarios u ON u.id = m.remitente_id
            WHERE (m.remitente_id = :emisor AND m.destinatario_id = :receptor)
               OR (m.remitente_id = :receptor2 AND m.destinatario_id = :emisor2)
            ORDER BY m.fecha_creacion ASC, m.id ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':emisor' => $emisorId,
        ':receptor' => $receptorId,
        ':receptor2' => $receptorId,
        ':emisor2' => $emisorId,
    ]);
    $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :receptor AND destinatario_id = :emisor AND leido = 0');
    $marcar->execute([':emisor' => $emisorId, ':receptor' => $receptorId]);
    responderHistorial(['success' => true, 'mensajes' => $mensajes]);
} catch (Throwable $exception) {
    /* Incluso una excepción de PDO termina como JSON válido después de limpiar
       el buffer, nunca como una página HTML inyectada en la respuesta. */
    responderHistorial(['success' => false, 'message' => 'Error al consultar BD'], 500);
}
