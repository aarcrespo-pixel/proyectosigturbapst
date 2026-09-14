<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../conexion.php';

$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
$eventoId = filter_input(INPUT_POST, 'evento_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$texto = trim((string) ($_POST['comentario'] ?? $_POST['pregunta'] ?? ''));

if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$eventoId || $texto === '' || mb_strlen($texto) > 500) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Campos incompletos'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* El endpoint recibe el ID estable del evento, pero preguntas_eventos conserva
   el slug para mantener compatibilidad con las preguntas ya existentes. */
$consultaEvento = $pdo->prepare('SELECT slug FROM eventos WHERE id = :evento_id LIMIT 1');
$consultaEvento->execute([':evento_id' => $eventoId]);
$slugEvento = $consultaEvento->fetchColumn();
if (!$slugEvento) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Evento no encontrado'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Creamos la tabla de forma idempotente para que el endpoint siga funcionando
   aunque se invoque antes de visitar la vista que normalmente hace la migración. */
$pdo->exec("CREATE TABLE IF NOT EXISTS preguntas_eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento_slug VARCHAR(120) NOT NULL,
    usuario_id INT NOT NULL,
    pregunta TEXT NOT NULL,
    respuesta TEXT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (evento_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$insertar = $pdo->prepare('INSERT INTO preguntas_eventos (evento_slug, usuario_id, pregunta) VALUES (:evento_slug, :usuario_id, :pregunta)');
$ok = $insertar->execute([
    ':evento_slug' => $slugEvento,
    ':usuario_id' => $usuarioId,
    ':pregunta' => $texto,
]);

echo json_encode([
    'success' => $ok,
    'message' => $ok ? '¡Pregunta/Comentario enviado con éxito!' : 'No se pudo guardar el mensaje.',
], JSON_UNESCAPED_UNICODE);
