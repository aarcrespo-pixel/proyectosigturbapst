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
    require_once __DIR__ . '/../helpers/autorizacion.php';

    $usuarioActual = sigtur_usuario_actual($pdo);
    if (!$usuarioActual) {
        responderHistorial(['status' => 'error', 'success' => false, 'message' => 'Sesión de usuario no iniciada.'], 401);
    }
    if (!in_array($usuarioActual['rol'], ['administrador', 'organizador'], true)) {
        responderHistorial(['status' => 'error', 'success' => false, 'message' => 'La mensajería directa es exclusiva para administradores y organizadores.'], 403);
    }
    $emisorId = (int) $usuarioActual['id'];
    // FIX REFRESCO / SINCRONIZACIÓN: ambos nombres llegan al mismo receptor;
    // el ID activo de la conversación es la única fuente para esta lectura.
    $receptorRaw = $_GET['receptor_id'] ?? $_GET['destinatario_id'] ?? $_POST['receptor_id'] ?? $_POST['destinatario_id'] ?? null;
    if ($receptorRaw === null || trim((string) $receptorRaw) === '') {
        responderHistorial(['status' => 'error', 'success' => false, 'message' => 'El ID del receptor llegó vacío o indefinido.'], 422);
    }
    $receptorId = filter_var($receptorRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$receptorId || $receptorId === $emisorId) {
        responderHistorial(['status' => 'error', 'success' => false, 'message' => 'El ID del receptor no es válido.'], 422);
    }
    $receptorStmt = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id LIMIT 1');
    $receptorStmt->execute([':id' => $receptorId]);
    $rolReceptor = $receptorStmt->fetchColumn();
    if (!$rolReceptor || sigtur_normalizar_rol((string) $rolReceptor) !== $usuarioActual['rol']) {
        responderHistorial(['status' => 'error', 'success' => false, 'message' => 'No tienes permiso para abrir esta conversación.'], 403);
    }

    if (isset($_GET['sync_ids'])) {
        $ids = array_slice(array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $_GET['sync_ids'])),
            static fn (int $id): bool => $id > 0
        ))), 0, 100);
        if (!$ids) responderHistorial(['status' => 'success', 'success' => true, 'mensajes' => []]);
        $marcadores = [];
        $parametrosSync = [
            ':sync_emisor_a' => $emisorId,
            ':sync_receptor_a' => $receptorId,
            ':sync_emisor_b' => $receptorId,
            ':sync_receptor_b' => $emisorId,
            ':sync_usuario_oculto' => $emisorId,
        ];
        foreach ($ids as $indice => $id) {
            $marcador = ':sync_id_' . $indice;
            $marcadores[] = $marcador;
            $parametrosSync[$marcador] = $id;
        }
        $sync = $pdo->prepare("SELECT m.id, m.eliminado_para_todos,
                CASE WHEN m.eliminado_para_todos = 1 THEN '' ELSE m.mensaje END AS mensaje
            FROM mensajes m
            LEFT JOIN mensajes_ocultos_usuario oculto
                ON oculto.mensaje_id = m.id AND oculto.usuario_id = :sync_usuario_oculto
            WHERE m.id IN (" . implode(', ', $marcadores) . ")
              AND ((m.remitente_id = :sync_emisor_a AND m.destinatario_id = :sync_receptor_a)
                OR (m.remitente_id = :sync_emisor_b AND m.destinatario_id = :sync_receptor_b))
              AND oculto.mensaje_id IS NULL");
        $sync->execute($parametrosSync);
        responderHistorial(['status' => 'success', 'success' => true, 'mensajes' => $sync->fetchAll(PDO::FETCH_ASSOC)]);
    }

     $afterRaw = $_GET['after_id'] ?? null;
     $beforeRaw = $_GET['before_id'] ?? null;
     $afterId = $afterRaw === null ? 0 : filter_var($afterRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
     $beforeId = $beforeRaw === null ? 0 : filter_var($beforeRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
     if (($afterRaw !== null && !$afterId) || ($beforeRaw !== null && !$beforeId) || ($afterId && $beforeId)) {
          responderHistorial(['status' => 'error', 'success' => false, 'message' => 'El cursor del historial no es válido.'], 422);
     }

     /* El historial se pagina y el polling consulta únicamente IDs nuevos para
         evitar transferir y volver a pintar toda la conversación cada pocos segundos. */
     $condicionCursor = '';
     $parametros = [
          ':mi_id_a' => $emisorId,
          ':receptor_id_a' => $receptorId,
          ':receptor_id_b' => $receptorId,
          ':mi_id_b' => $emisorId,
     ];
     if ($afterId) {
          $condicionCursor = ' AND m.id > :after_id';
          $parametros[':after_id'] = $afterId;
     } elseif ($beforeId) {
          $condicionCursor = ' AND m.id < :before_id';
          $parametros[':before_id'] = $beforeId;
     }
     $orden = $afterId ? 'ASC' : 'DESC';
     $sql = "SELECT m.id, m.remitente_id AS emisor_id, m.destinatario_id AS receptor_id,
                     CASE WHEN m.eliminado_para_todos = 1 THEN '' ELSE m.mensaje END AS mensaje,
                     m.eliminado_para_todos, m.contexto, m.foto_url, m.video_url, m.audio_url, m.fecha_creacion AS fecha,
                     DATE_FORMAT(m.fecha_creacion, '%H:%i') AS hora, m.leido,
                     u.nombre_completo AS nombre_usuario, u.nickname,
                     COALESCE(u.foto_perfil, u.avatar) AS foto_perfil
              FROM mensajes m
              INNER JOIN usuarios u ON u.id = m.remitente_id
              LEFT JOIN mensajes_ocultos_usuario oculto
                  ON oculto.mensaje_id = m.id AND oculto.usuario_id = :usuario_oculto
              WHERE ((m.remitente_id = :mi_id_a AND m.destinatario_id = :receptor_id_a)
                  OR (m.remitente_id = :receptor_id_b AND m.destinatario_id = :mi_id_b)){$condicionCursor}
                  AND oculto.mensaje_id IS NULL
              ORDER BY m.id {$orden}
              LIMIT 101";
     $parametros[':usuario_oculto'] = $emisorId;
     $stmt = $pdo->prepare($sql);
     $stmt->execute($parametros);
     $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
     $hayAnteriores = count($mensajes) > 100 && !$afterId;
     if ($hayAnteriores || ($afterId && count($mensajes) > 100)) {
          $mensajes = array_slice($mensajes, 0, 100);
     }
     if (!$afterId) {
          $mensajes = array_reverse($mensajes);
     }
     $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :receptor AND destinatario_id = :emisor AND leido = 0');
     $marcar->execute([':emisor' => $emisorId, ':receptor' => $receptorId]);
     responderHistorial(['status' => 'success', 'success' => true, 'mensajes' => $mensajes, 'data' => $mensajes, 'has_more' => $hayAnteriores]);
} catch (Throwable $exception) {
    /* Incluso una excepción de PDO termina como JSON válido después de limpiar
       el buffer, nunca como una página HTML inyectada en la respuesta. */
    responderHistorial(['status' => 'error', 'success' => false, 'message' => 'Error al consultar BD', 'detail' => $exception->getMessage()], 500);
}
