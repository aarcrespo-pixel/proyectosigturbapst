<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/notificaciones.php';
$usuario = sigtur_requerir_rol($pdo, ['organizador', 'administrador'], '../login.php', '../eventos.php');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}

$preguntaId = (int) ($_POST['pregunta_id'] ?? 0);
$respuesta = trim((string) ($_POST['respuesta'] ?? ''));
if ($preguntaId <= 0 || $respuesta === '' || mb_strlen($respuesta) > 2000) {
    http_response_code(422);
    exit('La respuesta debe tener entre 1 y 2000 caracteres.');
}

$consulta = $pdo->prepare('SELECT p.evento_slug, p.usuario_id, p.pregunta, e.organizador_id, e.titulo FROM preguntas_eventos p INNER JOIN eventos e ON e.slug = p.evento_slug WHERE p.id = :id LIMIT 1');
$consulta->execute([':id' => $preguntaId]);
$pregunta = $consulta->fetch();
if (!$pregunta) {
    http_response_code(404);
    exit('La consulta no existe.');
}
if ($usuario['rol'] === 'organizador' && (int) $pregunta['organizador_id'] !== (int) $usuario['id']) {
    http_response_code(403);
    exit('No tienes permiso para responder consultas de otro organizador.');
}

$actualizar = $pdo->prepare('UPDATE preguntas_eventos SET respuesta = :respuesta WHERE id = :id');
$actualizar->execute([':respuesta' => $respuesta, ':id' => $preguntaId]);

$destinatarioId = (int) ($pregunta['usuario_id'] ?? 0);
if ($destinatarioId > 0) {
    sigtur_crear_notificacion(
        $pdo,
        $destinatarioId,
        'Respuesta a tu consulta',
        'El organizador respondió tu consulta sobre ' . (string) ($pregunta['titulo'] ?? 'este evento') . '.\n' . $respuesta,
        'respuesta'
    );
}

header('Location: ../' . ($usuario['rol'] === 'administrador' ? 'panel-admin.php' : 'panel-organizador.php') . '#consultas');
exit;