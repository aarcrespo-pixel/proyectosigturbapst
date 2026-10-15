<?php
error_reporting(0);
ini_set('display_errors', '0');
require_once __DIR__ . '/../helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/copilot_ai.php';

header('Content-Type: application/json; charset=utf-8');

function sigtur_ai_reply(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

set_exception_handler(static function (Throwable $exception): void {
    error_log('SIGTUR Gemini endpoint failed unexpectedly.');
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(['success' => false, 'message' => 'El asistente tuvo un problema interno. Intentá nuevamente más tarde.'], JSON_UNESCAPED_UNICODE);
});

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    sigtur_ai_reply(['success' => false, 'message' => 'Método no permitido.'], 405);
}

$usuarioActual = sigtur_usuario_actual($pdo);
if (!$usuarioActual) {
    sigtur_ai_reply(['success' => false, 'message' => 'Sesión no iniciada.'], 401);
}
if (sigtur_normalizar_rol($usuarioActual['rol'] ?? null) !== 'turista') {
    sigtur_ai_reply(['success' => false, 'message' => 'Esta función está disponible solo para turistas.'], 403);
}

$accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? '');
if ($accion === 'historial' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $historial = $pdo->prepare('SELECT id, mensaje, respuesta, fecha_creacion FROM copilot_ai_historial WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC, id DESC LIMIT 50');
    $historial->execute([':usuario_id' => (int) $usuarioActual['id']]);
    sigtur_ai_reply(['success' => true, 'items' => $historial->fetchAll()]);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sigtur_ai_reply(['success' => false, 'message' => 'Acción no válida.'], 405);
}
if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    sigtur_ai_reply(['success' => false, 'message' => 'La sesión expiró. Recargá la página e intentá nuevamente.'], 403);
}

if ($accion === 'borrar_historial') {
    $borrar = $pdo->prepare('DELETE FROM copilot_ai_historial WHERE usuario_id = :usuario_id');
    $borrar->execute([':usuario_id' => (int) $usuarioActual['id']]);
    sigtur_ai_reply(['success' => true, 'message' => 'Chat eliminado correctamente.']);
}
if ($accion !== '') {
    sigtur_ai_reply(['success' => false, 'message' => 'Acción no válida.'], 400);
}

$mensaje = is_string($_POST['message'] ?? null) ? trim($_POST['message']) : '';
$longitudMensaje = function_exists('mb_strlen') ? mb_strlen($mensaje, 'UTF-8') : strlen($mensaje);
if ($mensaje === '' || $longitudMensaje > 800) {
    sigtur_ai_reply([
        'success' => false,
        'message' => $mensaje === '' ? 'Escribí una pregunta antes de enviar.' : 'La pregunta no puede superar los 800 caracteres.',
    ], 422);
}

$historialCliente = [];
$historialRaw = $_POST['history'] ?? '';
if (is_string($historialRaw) && strlen($historialRaw) <= 12000) {
    $historialDecodificado = json_decode($historialRaw, true);
    if (is_array($historialDecodificado)) {
        foreach (array_slice($historialDecodificado, -10) as $turno) {
            if (!is_array($turno)
                || !in_array($turno['role'] ?? null, ['user', 'model'], true)
                || !is_string($turno['text'] ?? null)) {
                continue;
            }
            $textoTurno = trim($turno['text']);
            if ($textoTurno === '' || mb_strlen($textoTurno, 'UTF-8') > 1200) {
                continue;
            }
            $historialCliente[] = [
                'role' => $turno['role'],
                'parts' => [['text' => $textoTurno]],
            ];
        }
    }
}

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}
try {
    $resultado = sigtur_ai_gemini_response($mensaje, sigtur_ai_contexto_sigtur($pdo), $historialCliente);
    if (!isset($resultado['answer'])) {
        sigtur_ai_reply(['success' => false, 'message' => $resultado['error']], (int) $resultado['status']);
    }
    sigtur_ai_guardar_historial($pdo, (int) $usuarioActual['id'], $mensaje, $resultado['answer']);
    sigtur_ai_reply(['success' => true, 'answer' => $resultado['answer'], 'provider' => 'Google Gemini']);
} catch (Throwable $exception) {
    error_log('SIGTUR Gemini request failed while preparing public context or saving history.');
    sigtur_ai_reply(['success' => false, 'message' => 'El asistente tuvo un problema interno. Intentá nuevamente más tarde.'], 503);
}
