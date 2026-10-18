<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';

header('Content-Type: application/json; charset=utf-8');

function responderPrivacidad(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$usuario = sigtur_usuario_actual($pdo);
if (!$usuario) {
    responderPrivacidad(['success' => false, 'message' => 'Iniciá sesión para cambiar esta preferencia.'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderPrivacidad(['success' => false, 'message' => 'Método no permitido.'], 405);
}

$csrfToken = $_POST['csrf_token'] ?? null;
if (!sigtur_validar_csrf(is_string($csrfToken) ? $csrfToken : null)) {
    responderPrivacidad(['success' => false, 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], 403);
}

$perfilPublico = filter_var($_POST['perfil_publico'] ?? null, FILTER_VALIDATE_INT);
if (!in_array($perfilPublico, [0, 1], true)) {
    responderPrivacidad(['success' => false, 'message' => 'La opción de visibilidad no es válida.'], 422);
}

$actualizar = $pdo->prepare('UPDATE usuarios SET perfil_publico = :perfil_publico WHERE id = :usuario_id');
$actualizar->execute([
    ':perfil_publico' => $perfilPublico,
    ':usuario_id' => (int) $usuario['id'],
]);

responderPrivacidad([
    'success' => true,
    'perfil_publico' => (bool) $perfilPublico,
    'message' => $perfilPublico ? 'Tu perfil ahora es público.' : 'Tu perfil ahora es privado.',
]);
