<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/notificaciones.php';
require_once __DIR__ . '/../helpers/idempotencia.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* Normalizamos la pregunta antes de persistirla. htmlspecialchars evita que
   una futura pantalla administrativa interprete HTML enviado por el usuario. */
$pregunta = trim((string) ($_POST['pregunta'] ?? ''));
$pregunta = htmlspecialchars($pregunta, ENT_QUOTES, 'UTF-8');
$claveEnvio = $_POST['submission_key'] ?? null;
$ambitoEnvio = 'pregunta-soporte:' . (int) ($_SESSION['usuario_id'] ?? 0);
if (sigtur_envio_ya_procesado($ambitoEnvio, $claveEnvio)) {
    echo json_encode(['success' => true, 'message' => 'Pregunta enviada correctamente'], JSON_UNESCAPED_UNICODE);
    exit;
}
$usuarioId = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
$email = trim((string) ($_POST['email'] ?? ($_SESSION['usuario_email'] ?? '')));

if ($pregunta === '' || mb_strlen($pregunta) < 5) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Escribe una pregunta de al menos 5 caracteres.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($usuarioId === null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Indica un email válido para recibir respuesta.'], JSON_UNESCAPED_UNICODE);
    exit;
}

/* La consulta preparada separa datos y SQL: usuario_id/email identifican al
   remitente, estado permite moderación y NOW() registra el momento de envío. */
$insertar = $pdo->prepare("INSERT INTO preguntas_frecuentes (usuario_id, email, pregunta, estado, fecha_creacion)
    VALUES (:usuario_id, :email, :pregunta, 'pendiente', NOW())");
$insertar->execute([
    ':usuario_id' => $usuarioId,
    ':email' => $email !== '' ? $email : null,
    ':pregunta' => $pregunta,
]);
sigtur_marcar_envio_procesado($ambitoEnvio, $claveEnvio);

sigtur_notificar_equipo_consulta(
    $pdo,
    (int) ($usuarioId ?? 0),
    'Nueva pregunta de soporte',
    'Se recibió una pregunta nueva en Soporte.',
    'consulta_soporte'
);

echo json_encode(['success' => true, 'message' => 'Pregunta enviada correctamente'], JSON_UNESCAPED_UNICODE);