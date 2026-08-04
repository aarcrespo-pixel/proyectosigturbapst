<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n-title="pageLoginTitle">Registro de Usuarios</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <a class="logo-back" href="index.php" aria-label="Volver al inicio" data-i18n-aria-label="loginBack">
        <img src="../img/logoblanco.png" alt="Volver al inicio" data-i18n-alt="loginBackAlt">
    </a>

    <main>
        <section class="contenedor">
            <h1 data-i18n="loginTitle">Iniciar Sesión</h1>
            <p class="intro" data-i18n="loginIntro">Completa tus datos y registra tu cuenta o inicia sesión.</p>
            <form>
                <label for="Usuario" data-i18n="loginUsername">Usuario / Correo Electrónico</label>
                <input type="text" id="Usuario" placeholder="Juan Perez o juanperez@gmail.com" data-i18n-placeholder="loginUsernamePlaceholder" />

                <label for="Contraseña" data-i18n="loginPassword">Contraseña</label>
                <input type="password" id="Contraseña" placeholder="1234567" data-i18n-placeholder="loginPasswordPlaceholder" />

                <button type="button" class="forgot-password" onclick="event.preventDefault()" data-i18n="loginForgot">Olvidé mi contraseña</button>

                <div class="botones">
                    <a class="action-btn" href="registrarse.php" data-i18n="loginRegister">Registrarse</a>
                    <button type="button" id="btnIniciar" class="action-btn" data-i18n="loginSubmit">Iniciar Sesión</button>
                </div>

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
