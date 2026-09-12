<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($loginInput === '' || $password === '') {
        $message = 'Ingresá tu email o cédula y tu contraseña.';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email OR cedula = :cedula LIMIT 1');
        $stmt->execute([
            ':email' => $loginInput,
            ':cedula' => $loginInput,
        ]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_avatar'] = !empty($usuario['avatar']) ? $usuario['avatar'] : 'default-avatar.png';
            $_SESSION['usuario_banner'] = !empty($usuario['banner']) ? $usuario['banner'] : 'default-banner.png';
            $_SESSION['usuario_nickname'] = !empty($usuario['nickname']) ? $usuario['nickname'] : strtolower(str_replace(' ', '', $usuario['nombre_completo']));
            $_SESSION['usuario_biografia'] = $usuario['biografia'] ?? '';
            $_SESSION['usuario_ubicacion'] = $usuario['ubicacion'] ?? '';
            $_SESSION['usuario_sitio_web'] = $usuario['sitio_web'] ?? '';
            header('Location: ../index.php');
            exit;
        }

        $message = 'Credenciales inválidas. Verificá tus datos e intentá nuevamente.';
        $messageType = 'error';
    }
}

if (isset($_GET['registro']) && $_GET['registro'] === 'exito') {
    $message = '¡Cuenta creada con éxito! Por favor, iniciá sesión.';
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n-title="pageLoginTitle">Registro de Usuarios</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <a class="logo-back" href="../index.php" aria-label="Volver al inicio" data-i18n-aria-label="loginBack">
        <img src="../img/logoblanco.png" alt="Volver al inicio" data-i18n-alt="loginBackAlt">
    </a>

    <main>
        <section class="contenedor">
            <h1 data-i18n="loginTitle">Iniciar Sesión</h1>
            <p class="intro" data-i18n="loginIntro">Completa tus datos y registra tu cuenta o inicia sesión.</p>

            <?php if ($message !== ''): ?>
                <div id="formMessage" class="form-message <?php echo $messageType === 'success' ? 'success' : 'error'; ?>" role="alert">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" class="login-form" method="post" action="login.php" novalidate>
                <label for="login" data-i18n="loginUsername">Email o Cédula</label>
                <input type="text" id="login" name="login" autocomplete="off" placeholder="usuario@email.com o 12345678" data-i18n-placeholder="loginUsernamePlaceholder" />

                <label for="password" data-i18n="loginPassword">Contraseña</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" placeholder="••••••••" data-i18n-placeholder="loginPasswordPlaceholder" />
                    <button type="button" class="toggle-password" data-target="password" aria-label="Mostrar contraseña">
                        <span class="toggle-icon">👁</span>
                    </button>
                </div>

                <button type="button" class="forgot-password" onclick="event.preventDefault()" data-i18n="loginForgot">Olvidé mi contraseña</button>

                <button type="submit" id="btnIniciar" class="action-btn login-submit" data-i18n="loginSubmit">Iniciar Sesión</button>

                <p class="register-prompt">
                    ¿No tenés una cuenta?
                    <a href="registrarse.php" class="register-link" data-i18n="loginRegister">Registrate acá</a>
                </p>

                <a class="google-button" href="https://accounts.google.com/signin/v2/identifier?service=mail" target="_blank" rel="noopener noreferrer">
                    <img class="google-icon" src="../img/google-logo.png" alt="Google logo" data-i18n-alt="loginGoogleAlt">
                    <span data-i18n="loginGoogle">Iniciar con Google</span>
                </a>

                <p class="google-legal" data-i18n="loginLegal">Al iniciar sesión aceptas los términos y condiciones de la empresa, las reglas de uso y la política de privacidad.</p>
            </form>
        </section>
    </main>

    <script src="../js/script.js"></script>
    <script src="../js/login.js"></script>
</body>
</html>
