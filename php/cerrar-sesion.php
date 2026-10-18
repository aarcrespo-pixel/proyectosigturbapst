<?php
require_once __DIR__ . '/helpers/autorizacion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

$csrfToken = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null;
if (!sigtur_validar_csrf($csrfToken)) {
    http_response_code(403);
    exit('La sesión expiró. Recargá la página e intentá nuevamente.');
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
}
session_destroy();

header('Location: ../index.php');
exit;
