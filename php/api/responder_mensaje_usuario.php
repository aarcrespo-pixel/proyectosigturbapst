<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/notificaciones.php';
require_once __DIR__ . '/../helpers/consultas_eventos.php';
require_once __DIR__ . '/../helpers/idempotencia.php';

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario || !in_array($usuario['rol'], ['administrador', 'organizador'], true)) {
    http_response_code(403);
    exit('Solo administradores y organizadores pueden responder mensajes.');
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La sesión expiró. Recarga la página e inténtalo de nuevo.');
}

$tipo = (string) ($_POST['tipo'] ?? '');
$accion = (string) ($_POST['accion'] ?? 'responder');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$respuesta = trim(is_string($_POST['respuesta'] ?? null) ? $_POST['respuesta'] : '');
$longitud = function_exists('mb_strlen') ? mb_strlen($respuesta, 'UTF-8') : strlen($respuesta);
if ($tipo === 'soporte' && in_array($accion, ['vaciar', 'eliminar'], true)) {
    if ($usuario['rol'] !== 'administrador') {
        http_response_code(403);
        exit('Solo los administradores pueden gestionar estos chats de soporte.');
    }
    if (!$id) {
        http_response_code(422);
        exit('La conversación seleccionada no es válida.');
    }

    $pdo->beginTransaction();
    try {
        $bloquearPregunta = $pdo->prepare('SELECT id FROM preguntas_frecuentes WHERE id = :id FOR UPDATE');
        $bloquearPregunta->execute([':id' => $id]);
        if (!$bloquearPregunta->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('No se encontró la conversación de soporte.');
        }

        $eliminarMensajes = $pdo->prepare('DELETE FROM preguntas_frecuentes_mensajes WHERE pregunta_id = :id');
        $eliminarMensajes->execute([':id' => $id]);
        if ($accion === 'vaciar') {
            $reiniciarPregunta = $pdo->prepare("UPDATE preguntas_frecuentes
                SET estado = 'pendiente', respuesta = NULL, respondido_por = NULL, fecha_respuesta = NULL
                WHERE id = :id");
            $reiniciarPregunta->execute([':id' => $id]);
        } else {
            $eliminarPregunta = $pdo->prepare('DELETE FROM preguntas_frecuentes WHERE id = :id');
            $eliminarPregunta->execute([':id' => $id]);
        }
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al gestionar chat de soporte: ' . $exception->getMessage());
        http_response_code(500);
        exit('No se pudo actualizar la conversación. Inténtalo nuevamente.');
    }

    header('Location: ../mensajes-usuarios.php?chat=' . ($accion === 'vaciar' ? 'vaciado' : 'eliminado') . '#soporte-messages-title');
    exit;
}
if ($tipo === 'evento' && $usuario['rol'] !== 'organizador') {
    http_response_code(403);
    exit('Solo los organizadores pueden responder consultas de eventos.');
}
if (!$id || $respuesta === '' || $longitud > 2000) {
    http_response_code(422);
    exit('La respuesta debe tener entre 1 y 2000 caracteres.');
}
$claveEnvio = $_POST['submission_key'] ?? null;
$ambitoEnvio = 'respuesta-' . $tipo . ':' . $id . ':' . (int) $usuario['id'];
if (sigtur_envio_ya_procesado($ambitoEnvio, $claveEnvio)) {
    $destino = $tipo === 'soporte'
        ? '../mensajes-usuarios.php?chat=' . $id . '#soporte-' . $id
        : '../mensajes-usuarios.php#' . $tipo . '-' . $id;
    header('Location: ' . $destino);
    exit;
}

if ($tipo === 'evento') {
    sigtur_asegurar_mensajes_consulta_evento($pdo);
    $consulta = $pdo->prepare('SELECT p.usuario_id, e.titulo
        FROM preguntas_eventos p
        INNER JOIN eventos e ON e.slug = p.evento_slug
        WHERE p.id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $pregunta = $consulta->fetch();
    if (!$pregunta) {
        http_response_code(404);
        exit('No se encontró la pregunta del evento.');
    }

    $pdo->beginTransaction();
    $insertar = $pdo->prepare('INSERT INTO preguntas_eventos_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
        VALUES (:pregunta_id, :autor_id, :autor_rol, :mensaje)');
    $insertar->execute([
        ':pregunta_id' => $id,
        ':autor_id' => (int) $usuario['id'],
        ':autor_rol' => $usuario['rol'],
        ':mensaje' => $respuesta,
    ]);
    $pdo->prepare('UPDATE preguntas_eventos SET respuesta = :respuesta WHERE id = :id')
        ->execute([':respuesta' => $respuesta, ':id' => $id]);
    $pdo->commit();
    sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

    sigtur_crear_notificacion(
        $pdo,
        (int) $pregunta['usuario_id'],
        'Respuesta a tu consulta',
        'El equipo respondió tu pregunta sobre "' . $pregunta['titulo'] . '".',
        'respuesta'
    );
} elseif ($tipo === 'soporte' && $accion === 'responder') {
    $consulta = $pdo->prepare('SELECT usuario_id FROM preguntas_frecuentes WHERE id = :id LIMIT 1');
    $consulta->execute([':id' => $id]);
    $destinatarioRaw = $consulta->fetchColumn();
    if ($destinatarioRaw === false) {
        http_response_code(404);
        exit('No se encontró la pregunta de soporte.');
    }
    $destinatarioId = (int) $destinatarioRaw;

    $pdo->beginTransaction();
    $insertar = $pdo->prepare('INSERT INTO preguntas_frecuentes_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
        VALUES (:pregunta_id, :autor_id, :autor_rol, :mensaje)');
    $insertar->execute([
        ':pregunta_id' => $id,
        ':autor_id' => (int) $usuario['id'],
        ':autor_rol' => $usuario['rol'],
        ':mensaje' => $respuesta,
    ]);
    $pdo->prepare("UPDATE preguntas_frecuentes
        SET estado = 'respondida', respuesta = :respuesta, respondido_por = :respondido_por, fecha_respuesta = NOW()
        WHERE id = :id")->execute([
            ':respuesta' => $respuesta,
            ':respondido_por' => (int) $usuario['id'],
            ':id' => $id,
        ]);
    $pdo->commit();
    sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

    if ($destinatarioId > 0) {
        sigtur_crear_notificacion(
            $pdo,
            $destinatarioId,
            'Respuesta a tu pregunta de soporte',
            'El equipo de SIGTUR respondió tu pregunta.',
            'respuesta'
        );
    }
} elseif ($tipo === 'reporte') {
    $consulta = $pdo->prepare("SELECT remitente_id, mensaje, contexto, fecha_creacion
        FROM mensajes
        WHERE id = :id AND destinatario_id = :destinatario AND mensaje LIKE '[Consulta %'
        LIMIT 1");
    $consulta->execute([':id' => $id, ':destinatario' => (int) $usuario['id']]);
    $reporte = $consulta->fetch();
    if (!$reporte) {
        http_response_code(404);
        exit('No se encontró el mensaje de soporte.');
    }
    $contextoHilo = (string) ($reporte['contexto'] ?? '');
    if (preg_match('/^soporte:reporte:\d+$/', $contextoHilo) !== 1) {
        $contextoHilo = 'soporte:reporte:' . $id;
    }

    $pdo->beginTransaction();
    $insertar = $pdo->prepare('INSERT INTO mensajes (remitente_id, destinatario_id, mensaje, contexto)
        VALUES (:remitente, :destinatario, :mensaje, :contexto)');
    $insertar->execute([
        ':remitente' => (int) $usuario['id'],
        ':destinatario' => (int) $reporte['remitente_id'],
        ':mensaje' => $respuesta,
        ':contexto' => $contextoHilo,
    ]);
    $marcarConsultasLeidas = $pdo->prepare("UPDATE mensajes
        SET leido = 1
        WHERE mensaje LIKE '[Consulta %'
          AND (contexto = :contexto OR id = :id)
          AND destinatario_id IN (
              SELECT id FROM usuarios
              WHERE LOWER(rol) IN ('administrador', 'admin', 'moderador', 'organizador')
          )");
    $marcarConsultasLeidas->execute([
        ':contexto' => $contextoHilo,
        ':id' => $id,
    ]);
    $pdo->commit();
    sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

    sigtur_crear_notificacion(
        $pdo,
        (int) $reporte['remitente_id'],
        'Respuesta a tu consulta',
        'Un integrante del equipo SIGTUR respondió tu consulta.',
        'respuesta'
    );
} else {
    http_response_code(422);
    exit('El tipo de mensaje no es válido.');
}

$destino = $tipo === 'soporte'
    ? '../mensajes-usuarios.php?chat=' . $id . '#soporte-' . $id
    : '../mensajes-usuarios.php#' . $tipo . '-' . $id;
header('Location: ' . $destino);
exit;
