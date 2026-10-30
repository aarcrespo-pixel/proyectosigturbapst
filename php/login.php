<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();

// Evitamos que una sesión activa vuelva a mostrar el formulario de login.
if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$message = '';
$messageType = '';
$loginInputValue = $_SERVER['REQUEST_METHOD'] === 'POST' && is_string($_POST['login'] ?? null)
    ? trim($_POST['login'])
    : '';
$emailIncorrecto = false;
$passwordIncorrecta = false;

/* El POST se procesa antes del HTML para poder redirigir tras autenticar o
    conservar un mensaje de error en la misma pantalla. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        $message = 'La sesión expiró. Recargá la página e intentá nuevamente.';
        $messageType = 'error';
    } else {
    /* El mismo valor se acepta como email o cédula y se consulta con un
       Prepared Statement para impedir inyección SQL. */
    $loginInput = $loginInputValue;
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    if ($loginInput === '' || $password === '' || strlen($loginInput) > 100 || strlen($password) > 4096) {
        $message = 'Ingresá tu email o cédula y tu contraseña.';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare('SELECT id, nombre_completo, email, cedula, password, rol, avatar, banner, nickname, biografia, ubicacion, sitio_web, avatar_crop_x, avatar_crop_zoom FROM usuarios WHERE email = :email OR cedula = :cedula LIMIT 1');
        $stmt->execute([
            ':email' => $loginInput,
            ':cedula' => $loginInput,
        ]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
                /* Guardamos los datos mínimos de identidad y perfil para que el
                    resto de controladores pueda autorizar y personalizar la UI. */
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['rol'] = sigtur_normalizar_rol($usuario['rol'] ?? null);
            $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_avatar'] = !empty($usuario['avatar']) ? $usuario['avatar'] : 'default-avatar.png';
            $_SESSION['usuario_avatar_crop_x'] = max(0, min(100, (float) ($usuario['avatar_crop_x'] ?? 50)));
            $_SESSION['usuario_avatar_crop_zoom'] = max(1, min(2.5, (float) ($usuario['avatar_crop_zoom'] ?? 1)));
            $_SESSION['usuario_banner'] = !empty($usuario['banner']) ? $usuario['banner'] : 'default-banner.png';
            $_SESSION['usuario_nickname'] = !empty($usuario['nickname']) ? $usuario['nickname'] : strtolower(str_replace(' ', '', $usuario['nombre_completo']));
            $_SESSION['usuario_biografia'] = $usuario['biografia'] ?? '';
            $_SESSION['usuario_ubicacion'] = $usuario['ubicacion'] ?? '';
            $_SESSION['usuario_sitio_web'] = $usuario['sitio_web'] ?? '';
            if (password_needs_rehash($usuario['password'], PASSWORD_DEFAULT)) {
                $actualizarPassword = $pdo->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
                $actualizarPassword->execute([
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':id' => (int) $usuario['id'],
                ]);
            }
            header('Location: ../index.php');
            exit;
        }

        if (!$usuario && (filter_var($loginInput, FILTER_VALIDATE_EMAIL) || str_contains($loginInput, '@'))) {
            $emailIncorrecto = true;
        } elseif ($usuario) {
            $passwordIncorrecta = true;
        } else {
            $message = 'Credenciales inválidas. Verificá tus datos e intentá nuevamente.';
            $messageType = 'error';
        }
    }
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
    <script src="../js/volver-posicion.js" defer></script>
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
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <label for="login" data-i18n="loginUsername">Email o Cédula</label>
                <input type="text" id="login" name="login" autocomplete="username" maxlength="100" required placeholder="usuario@email.com o 12345678" value="<?= htmlspecialchars($loginInputValue, ENT_QUOTES, 'UTF-8') ?>" <?= $emailIncorrecto ? 'aria-invalid="true"' : '' ?> data-i18n-placeholder="loginUsernamePlaceholder" />
                <?php if ($emailIncorrecto): ?>
                    <p class="login-field-error" role="alert">El correo electrónico es incorrecto.</p>
                <?php endif; ?>

                <label for="password" data-i18n="loginPassword">Contraseña</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" autocomplete="current-password" maxlength="4096" required placeholder="••••••••" <?= $passwordIncorrecta ? 'aria-invalid="true"' : '' ?> data-i18n-placeholder="loginPasswordPlaceholder" />
                    <button type="button" class="toggle-password" data-target="password" aria-label="Mostrar contraseña">
                        <span class="toggle-icon">👁</span>
                    </button>
                </div>
                <?php if ($passwordIncorrecta): ?>
                    <p class="login-field-error" role="alert">Contraseña incorrecta.</p>
                <?php endif; ?>

                <button type="button" class="forgot-password" onclick="event.preventDefault()" data-i18n="loginForgot">Olvidé mi contraseña</button>

                <button type="submit" id="btnIniciar" class="action-btn login-submit" data-i18n="loginSubmit">Iniciar Sesión</button>

                <p class="register-prompt">
                    ¿No tenés una cuenta?
                    <a href="registrarse.php" class="register-link" data-i18n="loginRegister">Registrate acá</a>
                </p>

                <p class="legal-notice" data-i18n="loginLegal">Al iniciar sesión aceptas los términos y condiciones de la empresa, las reglas de uso y la política de privacidad.</p>
            </form>
        </section>
    </main>

    <script src="../js/translator.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/login.js"></script>
</body>
</html>
