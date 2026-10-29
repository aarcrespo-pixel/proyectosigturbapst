<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/notificaciones.php';
require_once __DIR__ . '/../helpers/consultas_eventos.php';
require_once __DIR__ . '/../helpers/idempotencia.php';
$usuario = sigtur_usuario_actual($pdo);
if (!$usuario) {
    header('Location: ../login.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}

sigtur_asegurar_mensajes_consulta_evento($pdo);
$accion = (string) ($_POST['accion'] ?? 'responder');
$preguntaId = (int) ($_POST['pregunta_id'] ?? 0);
$claveEnvio = $_POST['submission_key'] ?? null;
$ambitoEnvio = 'consulta-evento:' . $preguntaId . ':' . (int) $usuario['id'] . ':' . $accion;
if (in_array($accion, ['mensaje', 'responder', 'respuesta'], true)
    && sigtur_envio_ya_procesado($ambitoEnvio, $claveEnvio)) {
    $destino = $accion === 'mensaje' ? '../soporte.php#chat-organizadores' : '../consultas-eventos-organizador.php?chat=' . $preguntaId . '#consulta-' . $preguntaId;
    header('Location: ' . $destino);
    exit;
}
$consulta = $pdo->prepare('SELECT p.evento_slug, p.usuario_id, p.pregunta, e.organizador_id, e.titulo
    FROM preguntas_eventos p
    INNER JOIN eventos e ON e.slug = p.evento_slug
    WHERE p.id = :id LIMIT 1');
if ($preguntaId <= 0) {
    http_response_code(422);
    exit('La consulta seleccionada no es válida.');
}
$consulta->execute([':id' => $preguntaId]);
$pregunta = $consulta->fetch();
if (!$pregunta) {
    http_response_code(404);
    exit('La consulta no existe.');
}

if (in_array($accion, ['vaciar', 'eliminar'], true)) {
    if ($usuario['rol'] !== 'organizador') {
        http_response_code(403);
        exit('Solo los organizadores pueden gestionar consultas de eventos.');
    }

    $pdo->beginTransaction();
    try {
        $bloquearConsulta = $pdo->prepare('SELECT id FROM preguntas_eventos WHERE id = :id FOR UPDATE');
        $bloquearConsulta->execute([':id' => $preguntaId]);
        if (!$bloquearConsulta->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('La consulta ya no existe.');
        }

        if ($accion === 'vaciar') {
            $vaciarMensajes = $pdo->prepare("DELETE FROM preguntas_eventos_mensajes
                WHERE pregunta_id = :id AND COALESCE(clave_migracion, '') <> 'pregunta_inicial'");
            $vaciarMensajes->execute([':id' => $preguntaId]);
            $reiniciarPregunta = $pdo->prepare('UPDATE preguntas_eventos SET respuesta = NULL WHERE id = :id');
            $reiniciarPregunta->execute([':id' => $preguntaId]);
        } else {
            $eliminarConsulta = $pdo->prepare('DELETE FROM preguntas_eventos WHERE id = :id');
            $eliminarConsulta->execute([':id' => $preguntaId]);
        }
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al gestionar consulta de evento: ' . $exception->getMessage());
        http_response_code(500);
        exit('No se pudo actualizar la consulta. Inténtalo nuevamente.');
    }

    header('Location: ../consultas-eventos-organizador.php?chat=' . ($accion === 'vaciar' ? 'vaciado' : 'eliminado') . '#consultas');
    exit;
}

$mensajeTexto = trim((string) ($_POST['mensaje'] ?? $_POST['respuesta'] ?? ''));
if ($mensajeTexto === '' || mb_strlen($mensajeTexto) > 2000) {
    http_response_code(422);
    exit('El mensaje debe tener entre 1 y 2000 caracteres.');
}

if ($accion === 'mensaje') {
    if ($usuario['rol'] !== 'turista' || (int) $pregunta['usuario_id'] !== (int) $usuario['id']) {
        http_response_code(403);
        exit('No tienes permiso para escribir en esta conversación.');
    }

    $pdo->beginTransaction();
    $agregarMensaje = $pdo->prepare("INSERT INTO preguntas_eventos_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
        VALUES (:pregunta_id, :autor_id, 'turista', :mensaje)");
    $agregarMensaje->execute([
        ':pregunta_id' => $preguntaId,
        ':autor_id' => (int) $usuario['id'],
        ':mensaje' => $mensajeTexto,
    ]);
    $pdo->prepare('UPDATE preguntas_eventos SET respuesta = NULL WHERE id = :id')->execute([':id' => $preguntaId]);
    $pdo->commit();
    sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

    sigtur_notificar_consulta_evento(
        $pdo,
        (int) $usuario['id'],
        (string) $pregunta['titulo'],
        $mensajeTexto
    );
    header('Location: ../soporte.php#chat-organizadores');
    exit;
}

if (!in_array($accion, ['responder', 'respuesta'], true)) {
    http_response_code(422);
    exit('La acción solicitada no es válida.');
}

if ($usuario['rol'] !== 'organizador') {
    http_response_code(403);
    exit('Solo los organizadores pueden responder consultas de eventos.');
}
$pdo->beginTransaction();
$agregarRespuesta = $pdo->prepare("INSERT INTO preguntas_eventos_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
    VALUES (:pregunta_id, :autor_id, :autor_rol, :mensaje)");
$agregarRespuesta->execute([
    ':pregunta_id' => $preguntaId,
    ':autor_id' => (int) $usuario['id'],
    ':autor_rol' => $usuario['rol'],
    ':mensaje' => $mensajeTexto,
]);
$actualizar = $pdo->prepare('UPDATE preguntas_eventos SET respuesta = :respuesta WHERE id = :id');
$actualizar->execute([':respuesta' => $mensajeTexto, ':id' => $preguntaId]);
$pdo->commit();
sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

$destinatarioId = (int) ($pregunta['usuario_id'] ?? 0);
if ($destinatarioId > 0) {
    sigtur_crear_notificacion(
        $pdo,
        $destinatarioId,
        'Respuesta a tu consulta',
        'El organizador respondió tu consulta sobre ' . (string) ($pregunta['titulo'] ?? 'este evento') . '.\n' . $mensajeTexto,
        'respuesta'
    );
}

header('Location: ../consultas-eventos-organizador.php?chat=' . $preguntaId . '#consulta-' . $preguntaId);
exit;