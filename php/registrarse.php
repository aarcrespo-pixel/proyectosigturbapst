<?php
session_start();
require_once __DIR__ . '/conexion.php';

if (isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$message = '';
$messageType = '';

/* El registro concentra validación, comprobación de unicidad e inserción en un
    único flujo para que una cuenta incompleta nunca llegue a MySQL. */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* Normalizamos y validamos todos los campos antes de tocar la base de
       datos para rechazar entradas incompletas o con formato inválido. */
    $nombreCompleto = trim($_POST['nombre_completo'] ?? '');
    $cedula = trim($_POST['cedula'] ?? '');
    $email = trim(strtolower($_POST['email'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';
    $aceptaTerminos = isset($_POST['aceptar_terminos']) ? $_POST['aceptar_terminos'] : '';

    if ($nombreCompleto === '' || $cedula === '' || $email === '' || $password === '' || $confirmarPassword === '' || $aceptaTerminos !== 'on') {
        $message = 'Completa todos los campos obligatorios y acepta los términos y condiciones.';
        $messageType = 'error';
    } elseif (!preg_match('/^\d{1,8}$/', $cedula)) {
        $message = 'La cédula debe contener solo números y hasta 8 dígitos.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Ingresa un correo electrónico válido.';
        $messageType = 'error';
    } elseif ($password !== $confirmarPassword) {
        $message = 'Las contraseñas no coinciden.';
        $messageType = 'error';
    } elseif ($telefono !== '' && !preg_match('/^[0-9+\-\s()]{6,15}$/', $telefono)) {
        $message = 'El número telefónico no es válido.';
        $messageType = 'error';
    } else {
        // La consulta parametrizada evita inyección y detecta duplicados antes del INSERT.
        $existe = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email OR cedula = :cedula LIMIT 1');
        $existe->execute([':email' => $email, ':cedula' => $cedula]);
        $usuarioExiste = $existe->fetch();

        if ($usuarioExiste) {
            $message = 'Ya existe una cuenta con ese email o cédula.';
            $messageType = 'error';
        } else {
            // Nunca guardamos la contraseña plana: password_hash genera un hash verificable.
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO usuarios (nombre_completo, cedula, email, telefono, password) VALUES (:nombre_completo, :cedula, :email, :telefono, :password)');
            $stmt->execute([
                ':nombre_completo' => $nombreCompleto,
                ':cedula' => $cedula,
                ':email' => $email,
                ':telefono' => $telefono,
                ':password' => $hash,
            ]);

            header('Location: login.php?registro=exito');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse | Sigtur Salto</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <a class="logo-back" href="../index.php" aria-label="Volver al inicio">
        <img src="../img/logoblanco.png" alt="Volver al inicio">
    </a>

    <main>
        <section class="contenedor">
            <h1>Regístrate</h1>
            <p class="intro">Completa tus datos para crear tu cuenta y empezar a disfrutar del sitio.</p>

            <?php if ($message !== ''): ?>
                <div id="formMessage" class="form-message <?php echo $messageType === 'success' ? 'success' : 'error'; ?>" role="alert">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($message === ''): ?>
                <div id="formMessage" class="form-message" role="alert" style="display: none;"></div>
            <?php endif; ?>

            <form id="registroForm" method="post" action="registrarse.php" novalidate>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="Nombre">Nombre completo</label>
                        <input type="text" id="Nombre" name="nombre_completo" placeholder="Nombre completo" value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
                    </div>

                    <div class="field-group">
                        <label for="Cedula">Cédula de Identidad</label>
                        <input type="text" id="Cedula" name="cedula" inputmode="numeric" maxlength="8" placeholder="12345678" value="<?php echo htmlspecialchars($_POST['cedula'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
                    </div>

                    <div class="field-group">
                        <label for="CorreoElectronico">Correo electrónico</label>
                        <input type="email" id="CorreoElectronico" name="email" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
                    </div>

                    <div class="field-group">
                        <label for="Telefono">Número telefónico</label>
                        <input type="tel" id="Telefono" name="telefono" maxlength="15" placeholder="Número telefónico" value="<?php echo htmlspecialchars($_POST['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                    </div>

                    <div class="field-group">
                        <label for="Contraseña">Contraseña</label>
                        <div class="password-field">
                            <input type="password" id="Contraseña" name="password" placeholder="Contraseña" required />
                            <button type="button" class="toggle-password" data-target="Contraseña" aria-label="Mostrar contraseña">
                                <span class="toggle-icon">👁</span>
                            </button>
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="ConfirmarContraseña">Confirmar contraseña</label>
                        <div class="password-field">
                            <input type="password" id="ConfirmarContraseña" name="confirmar_password" placeholder="Confirmar contraseña" required />
                            <button type="button" class="toggle-password" data-target="ConfirmarContraseña" aria-label="Mostrar contraseña">
                                <span class="toggle-icon">👁</span>
                            </button>
                        </div>
                    </div>
                </div>

                <label class="checkbox-row" for="AceptarTerminos">
                    <input type="checkbox" id="AceptarTerminos" name="aceptar_terminos" value="on" <?php echo (isset($_POST['aceptar_terminos']) && $_POST['aceptar_terminos'] === 'on') ? 'checked' : ''; ?> required>
                    <span>Acepto los <a href="#">términos y condiciones</a> y la <a href="#">política de privacidad</a>.</span>
                </label>

                <div class="botones">
                    <button type="submit" id="btnAgregar" class="action-btn">Registrarse</button>
                </div>

                <p class="register-prompt">
                    ¿Ya tenés una cuenta?
                    <a href="login.php" class="register-link" data-i18n="loginRegister">Inicia sesión acá</a>
                </p>

                <a class="google-button" href="https://accounts.google.com/signin/v2/identifier?service=mail" target="_blank" rel="noopener noreferrer">
                    <img class="google-icon" src="../img/google-logo.png" alt="Google logo">
                    Iniciar con Google
                </a>
            </form>
        </section>
    </main>

    <script src="../js/script.js" defer></script>
    <script src="../js/login.js" defer></script>
</body>
</html>
