<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
require_once __DIR__ . '/../helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();
require_once __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

function responderSeguimiento(array $payload, int $status = 200): void
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
    $seguidorId = (int) ($usuario['id'] ?? 0);
    if (!$seguidorId) {
        responderSeguimiento(['success' => false, 'error' => 'Iniciá sesión para seguir perfiles.'], 401);
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderSeguimiento(['success' => false, 'error' => 'Método no permitido.'], 405);
    }
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        responderSeguimiento(['success' => false, 'error' => 'La sesión expiró. Recargá la página e intentá nuevamente.'], 403);
    }

    $accion = (string) ($_POST['accion'] ?? 'toggle');
    if (in_array($accion, ['aceptar', 'rechazar'], true)) {
        $solicitanteId = filter_var($_POST['solicitante_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$solicitanteId || $solicitanteId === $seguidorId) {
            responderSeguimiento(['success' => false, 'error' => 'La solicitud seleccionada no es válida.'], 422);
        }

        $pdo->beginTransaction();
        $consultaSolicitud = $pdo->prepare('SELECT id FROM solicitudes_seguimiento WHERE seguidor_id = :solicitante AND seguido_id = :destinatario LIMIT 1 FOR UPDATE');
        $consultaSolicitud->execute([':solicitante' => $solicitanteId, ':destinatario' => $seguidorId]);
        $solicitudId = $consultaSolicitud->fetchColumn();
        if ($solicitudId === false) {
            $pdo->rollBack();
            responderSeguimiento(['success' => false, 'error' => 'La solicitud ya no está pendiente.'], 404);
        }

        if ($accion === 'aceptar') {
            $aceptar = $pdo->prepare('INSERT IGNORE INTO seguidores (seguidor_id, seguido_id) VALUES (:seguidor, :seguido)');
            $aceptar->execute([':seguidor' => $solicitanteId, ':seguido' => $seguidorId]);
        }
        $eliminarSolicitud = $pdo->prepare('DELETE FROM solicitudes_seguimiento WHERE id = :id');
        $eliminarSolicitud->execute([':id' => (int) $solicitudId]);
        $pdo->commit();

        responderSeguimiento(['success' => true, 'accion' => $accion, 'solicitante_id' => (int) $solicitanteId]);
    }

    $seguidoId = filter_var($_POST['usuario_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$seguidoId || $seguidoId === $seguidorId) {
        responderSeguimiento(['success' => false, 'error' => 'No se puede seguir este perfil.'], 422);
    }

    $consultaUsuario = $pdo->prepare('SELECT perfil_publico FROM usuarios WHERE id = :id LIMIT 1');
    $consultaUsuario->execute([':id' => $seguidoId]);
    $perfilPublico = $consultaUsuario->fetchColumn();
    if ($perfilPublico === false) {
        responderSeguimiento(['success' => false, 'error' => 'Usuario no encontrado.'], 404);
    }

    $consultaSeguidor = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
    $consultaSeguidor->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
    $siguiendo = (bool) $consultaSeguidor->fetchColumn();

    if ($accion === 'estado') {
        $consultaSolicitud = $pdo->prepare('SELECT 1 FROM solicitudes_seguimiento WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
        $consultaSolicitud->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
        $pendiente = (bool) $consultaSolicitud->fetchColumn();
        responderSeguimiento([
            'success' => true,
            'following' => $siguiendo,
            'pending' => $pendiente,
            'label' => $siguiendo ? 'Siguiendo' : ($pendiente ? 'Pendiente' : 'Seguir'),
        ]);
    }

    if ($accion !== 'toggle') {
        responderSeguimiento(['success' => false, 'error' => 'Acción no reconocida.'], 422);
    }

    $pdo->beginTransaction();
    if ($siguiendo) {
        $eliminar = $pdo->prepare('DELETE FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido');
        $eliminar->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
        $siguiendo = false;
        $pendiente = false;
    } elseif (!(bool) $perfilPublico) {
        $eliminarSolicitud = $pdo->prepare('DELETE FROM solicitudes_seguimiento WHERE seguidor_id = :seguidor AND seguido_id = :seguido');
        $eliminarSolicitud->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
        $siguiendo = false;
        if ($eliminarSolicitud->rowCount() > 0) {
            $pendiente = false;
        } else {
            $insertarSolicitud = $pdo->prepare('INSERT IGNORE INTO solicitudes_seguimiento (seguidor_id, seguido_id) VALUES (:seguidor, :seguido)');
            $insertarSolicitud->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
            $pendiente = true;
        }
    } else {
        $eliminarSolicitud = $pdo->prepare('DELETE FROM solicitudes_seguimiento WHERE seguidor_id = :seguidor AND seguido_id = :seguido');
        $eliminarSolicitud->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
        $insertar = $pdo->prepare('INSERT IGNORE INTO seguidores (seguidor_id, seguido_id) VALUES (:seguidor, :seguido)');
        $insertar->execute([':seguidor' => $seguidorId, ':seguido' => $seguidoId]);
        $siguiendo = true;
        $pendiente = false;
    }
    $pdo->commit();

    $totalSeguidores = $pdo->prepare('SELECT COUNT(*) FROM seguidores WHERE seguido_id = :seguido');
    $totalSeguidores->execute([':seguido' => $seguidoId]);
    responderSeguimiento([
        'success' => true,
        'following' => $siguiendo,
        'pending' => $pendiente,
        'followers' => (int) $totalSeguidores->fetchColumn(),
        'label' => $siguiendo ? 'Siguiendo' : ($pendiente ? 'Pendiente' : 'Seguir'),
    ]);
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('[seguir] ' . $exception->getMessage());
    responderSeguimiento(['success' => false, 'error' => 'No se pudo actualizar el seguimiento.'], 500);
}
