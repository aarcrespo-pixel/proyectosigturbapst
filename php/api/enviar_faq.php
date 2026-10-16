<?php
session_start();
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Se guarda texto plano; las vistas administrativas deben escaparlo al mostrarlo. */
$pregunta = trim((string) ($_POST['pregunta'] ?? ''));
$usuarioId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$email = trim((string) ($_SESSION['usuario_email'] ?? ''));

if ($usuarioId === null || $usuarioId <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Inicia sesión para enviar una pregunta y recibir la respuesta en Preguntas frecuentes.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($pregunta === '' || mb_strlen($pregunta) < 5) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Escribe una pregunta de al menos 5 caracteres.'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Solo las cuentas autenticadas pueden enviar; así la respuesta queda ligada
   a la cuenta remitente y se muestra de forma privada en Preguntas frecuentes. */
$insertar = $pdo->prepare("INSERT INTO preguntas_frecuentes (usuario_id, email, pregunta, estado, fecha_creacion)
    VALUES (:usuario_id, :email, :pregunta, 'pendiente', NOW())");
$insertar->execute([
    ':usuario_id' => $usuarioId,
    ':email' => $email !== '' ? $email : null,
    ':pregunta' => $pregunta,
]);

echo json_encode(['success' => true, 'message' => 'Pregunta enviada correctamente'], JSON_UNESCAPED_UNICODE);
