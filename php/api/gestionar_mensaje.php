<?php

session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/censura.php';
require_once __DIR__ . '/../helpers/consultas_eventos.php';

header('Content-Type: application/json; charset=utf-8');

function responderGestionMensaje(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario) {
    responderGestionMensaje(['success' => false, 'error' => 'Inicia sesión para gestionar mensajes.'], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderGestionMensaje(['success' => false, 'error' => 'Método no permitido.'], 405);
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    responderGestionMensaje(['success' => false, 'error' => 'La sesión expiró. Recargá la página e intentá nuevamente.'], 403);
}

$tipo = (string) ($_POST['tipo'] ?? '');
$accion = (string) ($_POST['accion'] ?? '');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$texto = censurarTexto(trim((string) ($_POST['texto'] ?? '')));
$usuarioId = (int) $usuario['id'];
$rol = (string) $usuario['rol'];
$limite = $tipo === 'evento' ? 2000 : 4000;
$longitud = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);

if (!in_array($tipo, ['evento', 'soporte', 'soporte_pregunta'], true)
    || !in_array($accion, ['editar', 'eliminar', 'eliminar_para_mi', 'eliminar_para_todos'], true)
    || !$id) {
    responderGestionMensaje(['success' => false, 'error' => 'La acción o el mensaje seleccionado no es válido.'], 422);
}
if ($accion === 'editar' && ($texto === '' || $longitud > $limite)) {
    responderGestionMensaje(['success' => false, 'error' => 'El mensaje no puede estar vacío ni superar el límite permitido.'], 422);
}

$autorId = 0;
$propietarioId = 0;
$claveMigracion = null;

if ($tipo === 'evento') {
    if (!in_array($rol, ['turista', 'organizador', 'administrador'], true)) {
        responderGestionMensaje(['success' => false, 'error' => 'No tienes permiso para gestionar consultas de eventos.'], 403);
    }
    sigtur_asegurar_mensajes_consulta_evento($pdo);
    $consulta = $pdo->prepare('SELECT m.autor_id, m.autor_rol, m.clave_migracion, m.mensaje, m.eliminado,
            p.usuario_id AS propietario_id, p.pregunta AS pregunta_inicial, p.respuesta AS respuesta_inicial
        FROM preguntas_eventos_mensajes m
        INNER JOIN preguntas_eventos p ON p.id = m.pregunta_id
        WHERE m.id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$fila) responderGestionMensaje(['success' => false, 'error' => 'No se encontró el mensaje de la consulta.'], 404);
    $autorId = (int) ($fila['autor_id'] ?? 0);
    $propietarioId = (int) $fila['propietario_id'];
    $mensajeEliminado = (int) ($fila['eliminado'] ?? 0) === 1;
    $claveMigracion = $fila['clave_migracion'];
    $esPreguntaInicial = $claveMigracion === 'pregunta_inicial'
        || ($fila['autor_rol'] === 'turista' && $fila['mensaje'] === $fila['pregunta_inicial']);
    $esRespuestaInicial = $claveMigracion === 'respuesta_inicial'
        || (in_array($fila['autor_rol'], ['organizador', 'administrador'], true)
            && (string) $fila['mensaje'] === (string) $fila['respuesta_inicial']);
    if ($rol === 'turista' && $propietarioId !== $usuarioId) {
        responderGestionMensaje(['success' => false, 'error' => 'No tienes permiso para gestionar esta conversación.'], 403);
    }
    if ($accion === 'editar' && $autorId !== $usuarioId) {
        responderGestionMensaje(['success' => false, 'error' => 'Solo puedes editar tus propios mensajes.'], 403);
    }
    if ($accion === 'eliminar_para_mi') {
        if ($autorId !== $usuarioId && !$mensajeEliminado) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo puedes quitar de tu chat un mensaje ajeno después de que se haya eliminado para todos.'], 403);
        }
        $ocultar = $pdo->prepare('INSERT IGNORE INTO mensajes_conversacion_ocultos (tipo, mensaje_id, usuario_id) VALUES (:tipo, :id, :usuario_id)');
        $ocultar->execute([':tipo' => $tipo, ':id' => $id, ':usuario_id' => $usuarioId]);
        responderGestionMensaje(['success' => true, 'id' => $id, 'accion' => $accion]);
    }

    if ($accion === 'editar') {
        if (!empty($fila['eliminado'])) responderGestionMensaje(['success' => false, 'error' => 'No se puede editar un mensaje eliminado para todos.'], 409);
        $actualizar = $pdo->prepare('UPDATE preguntas_eventos_mensajes SET mensaje = :mensaje, eliminado = 0 WHERE id = :id');
        $actualizar->execute([':mensaje' => $texto, ':id' => $id]);
        if ($esPreguntaInicial) {
            $pdo->prepare('UPDATE preguntas_eventos p INNER JOIN preguntas_eventos_mensajes m ON m.pregunta_id = p.id
                SET p.pregunta = :mensaje WHERE m.id = :id')->execute([':mensaje' => $texto, ':id' => $id]);
        } elseif ($esRespuestaInicial) {
            $pdo->prepare('UPDATE preguntas_eventos p INNER JOIN preguntas_eventos_mensajes m ON m.pregunta_id = p.id
                SET p.respuesta = :mensaje WHERE m.id = :id')->execute([':mensaje' => $texto, ':id' => $id]);
        }
    } else {
        if ($autorId !== $usuarioId) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo quien envió el mensaje puede eliminarlo para todos.'], 403);
        }
        $pdo->prepare('UPDATE preguntas_eventos_mensajes SET eliminado = 1 WHERE id = :id')->execute([':id' => $id]);
    }
} elseif ($tipo === 'soporte_pregunta') {
    $consulta = $pdo->prepare('SELECT usuario_id, pregunta_eliminada FROM preguntas_frecuentes WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $pregunta = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$pregunta) responderGestionMensaje(['success' => false, 'error' => 'No se encontró la pregunta de soporte.'], 404);
    $propietarioId = (int) $pregunta['usuario_id'];
    $preguntaEliminada = (int) $pregunta['pregunta_eliminada'] === 1;
    if ($rol === 'turista' && $propietarioId !== $usuarioId) {
        responderGestionMensaje(['success' => false, 'error' => 'No tienes permiso para gestionar esta conversación.'], 403);
    }
    if ($accion === 'editar' && ($propietarioId !== $usuarioId || $propietarioId === 0)) {
        responderGestionMensaje(['success' => false, 'error' => 'Solo puedes editar tu propia pregunta.'], 403);
    }
    if ($accion === 'eliminar_para_mi') {
        if ($propietarioId !== $usuarioId && !$preguntaEliminada) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo puedes quitar de tu chat una pregunta ajena después de que se haya eliminado para todos.'], 403);
        }
        $ocultar = $pdo->prepare('INSERT IGNORE INTO mensajes_conversacion_ocultos (tipo, mensaje_id, usuario_id) VALUES (:tipo, :id, :usuario_id)');
        $ocultar->execute([':tipo' => $tipo, ':id' => $id, ':usuario_id' => $usuarioId]);
        responderGestionMensaje(['success' => true, 'id' => $id, 'accion' => $accion]);
    }
    if ($accion === 'editar') {
        $estado = $pdo->prepare('SELECT pregunta_eliminada FROM preguntas_frecuentes WHERE id = :id');
        $estado->execute([':id' => $id]);
        if ((int) $estado->fetchColumn() === 1) responderGestionMensaje(['success' => false, 'error' => 'No se puede editar una pregunta eliminada para todos.'], 409);
        $pdo->prepare('UPDATE preguntas_frecuentes SET pregunta = :texto, pregunta_eliminada = 0 WHERE id = :id')
            ->execute([':texto' => $texto, ':id' => $id]);
    } else {
        if ($propietarioId !== $usuarioId) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo quien envió la pregunta puede eliminarla para todos.'], 403);
        }
        $pdo->prepare('UPDATE preguntas_frecuentes SET pregunta_eliminada = 1 WHERE id = :id')
            ->execute([':id' => $id]);
    }
} else {
    $consulta = $pdo->prepare('SELECT m.autor_id, m.eliminado, p.usuario_id AS propietario_id
        FROM preguntas_frecuentes_mensajes m
        INNER JOIN preguntas_frecuentes p ON p.id = m.pregunta_id
        WHERE m.id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $fila = $consulta->fetch(PDO::FETCH_ASSOC);
    if (!$fila) responderGestionMensaje(['success' => false, 'error' => 'No se encontró el mensaje de soporte.'], 404);
    $autorId = (int) ($fila['autor_id'] ?? 0);
    $propietarioId = (int) ($fila['propietario_id'] ?? 0);
    $mensajeEliminado = (int) ($fila['eliminado'] ?? 0) === 1;
    if ($rol === 'turista' && $propietarioId !== $usuarioId) {
        responderGestionMensaje(['success' => false, 'error' => 'No tienes permiso para gestionar esta conversación.'], 403);
    }
    if ($accion === 'editar' && $autorId !== $usuarioId) {
        responderGestionMensaje(['success' => false, 'error' => 'Solo puedes editar tus propios mensajes.'], 403);
    }
    if ($accion === 'eliminar_para_mi') {
        if ($autorId !== $usuarioId && !$mensajeEliminado) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo puedes quitar de tu chat un mensaje ajeno después de que se haya eliminado para todos.'], 403);
        }
        $ocultar = $pdo->prepare('INSERT IGNORE INTO mensajes_conversacion_ocultos (tipo, mensaje_id, usuario_id) VALUES (:tipo, :id, :usuario_id)');
        $ocultar->execute([':tipo' => $tipo, ':id' => $id, ':usuario_id' => $usuarioId]);
        responderGestionMensaje(['success' => true, 'id' => $id, 'accion' => $accion]);
    }
    if ($accion === 'editar') {
        $estado = $pdo->prepare('SELECT eliminado FROM preguntas_frecuentes_mensajes WHERE id = :id');
        $estado->execute([':id' => $id]);
        if ((int) $estado->fetchColumn() === 1) responderGestionMensaje(['success' => false, 'error' => 'No se puede editar un mensaje eliminado para todos.'], 409);
        $pdo->prepare('UPDATE preguntas_frecuentes_mensajes SET mensaje = :mensaje, eliminado = 0 WHERE id = :id')
            ->execute([':mensaje' => $texto, ':id' => $id]);
    } else {
        if ($autorId !== $usuarioId) {
            responderGestionMensaje(['success' => false, 'error' => 'Solo quien envió el mensaje puede eliminarlo para todos.'], 403);
        }
        $pdo->prepare('UPDATE preguntas_frecuentes_mensajes SET eliminado = 1 WHERE id = :id')
            ->execute([':id' => $id]);
    }
}

responderGestionMensaje([
    'success' => true,
    'id' => $id,
    'accion' => $accion,
    'texto' => $accion === 'editar' ? $texto : 'Mensaje eliminado',
]);
