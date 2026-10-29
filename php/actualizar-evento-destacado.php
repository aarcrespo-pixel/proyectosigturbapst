<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: panel-organizador.php');
    exit;
}

sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}

$eventoId = filter_var($_POST['evento_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$accion = (string) ($_POST['accion'] ?? '');
if (!$eventoId || !in_array($accion, ['agregar', 'quitar'], true)) {
    http_response_code(422);
    exit('La acción solicitada no es válida.');
}

$consultaEvento = $pdo->prepare('SELECT id, es_pasado, fecha FROM eventos WHERE id = :id LIMIT 1');
$consultaEvento->execute([':id' => $eventoId]);
$evento = $consultaEvento->fetch();
if (!$evento) {
    http_response_code(404);
    exit('El evento no existe.');
}

if ($accion === 'agregar') {
    if ((int) $evento['es_pasado'] === 1 || $evento['fecha'] < date('Y-m-d')) {
        http_response_code(422);
        exit('Solo puedes destacar eventos vigentes o futuros.');
    }

    $siguienteOrden = (int) $pdo->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM eventos_destacados')->fetchColumn();
    $agregar = $pdo->prepare('INSERT INTO eventos_destacados (evento_id, orden) VALUES (:evento_id, :orden)
        ON DUPLICATE KEY UPDATE orden = VALUES(orden)');
    $agregar->execute([':evento_id' => $eventoId, ':orden' => $siguienteOrden]);
} else {
    $quitar = $pdo->prepare('DELETE FROM eventos_destacados WHERE evento_id = :evento_id');
    $quitar->execute([':evento_id' => $eventoId]);
}

header('Location: panel-organizador.php?destacado_actualizado=' . $eventoId . '&accion_destacado=' . $accion . '#evento-' . $eventoId);
exit;
