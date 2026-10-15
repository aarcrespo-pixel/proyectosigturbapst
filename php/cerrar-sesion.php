<?php

require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
    http_response_code(403);
    exit('La solicitud para cerrar sesión no es válida. Recargá la página e intentá nuevamente.');
}

/* Vaciamos la sesión y después invalidamos su cookie para cerrar también el
    identificador persistido en el navegador. */
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_unset();
session_destroy();

header('Location: ../index.php');
exit;
