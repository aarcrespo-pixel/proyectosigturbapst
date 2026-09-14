<?php

session_start();

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
