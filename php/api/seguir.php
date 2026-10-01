<?php
session_start();
require_once __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

$seguidorId = (int) ($_SESSION['usuario_id'] ?? 0);
$seguidoId = filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$seguidorId || !$seguidoId || $seguidorId === (int) $seguidoId) {
    http_response_code(422);
    echo json_encode(['error' => 'No se puede seguir este perfil.']);
    exit;
}

$existe = $pdo->prepare('SELECT id FROM usuarios WHERE id = :id LIMIT 1');
$existe->execute([':id' => $seguidoId]);
if (!$existe->fetchColumn()) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado.']);
    exit;
}

$consulta = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id LIMIT 1');
$consulta->execute([':seguidor_id' => $seguidorId, ':seguido_id' => $seguidoId]);
if ($consulta->fetchColumn()) {
    $accion = $pdo->prepare('DELETE FROM seguidores WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id');
    $accion->execute([':seguidor_id' => $seguidorId, ':seguido_id' => $seguidoId]);
    $siguiendo = false;
} else {
    $accion = $pdo->prepare('INSERT INTO seguidores (seguidor_id, seguido_id) VALUES (:seguidor_id, :seguido_id)');
    $accion->execute([':seguidor_id' => $seguidorId, ':seguido_id' => $seguidoId]);
    $siguiendo = true;
}

echo json_encode(['success' => true, 'following' => $siguiendo, 'label' => $siguiendo ? 'Siguiendo' : 'Seguir'], JSON_UNESCAPED_UNICODE);
