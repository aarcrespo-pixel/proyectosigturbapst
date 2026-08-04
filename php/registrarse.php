<?php
require_once 'conexion.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contraseña = trim($_POST['contraseña'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '' || $correo === '' || $contraseña === '') {
        $message = 'Completa nombre, correo y contraseña para registrarte.';
        $messageType = 'error';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $message = 'Ingresa un correo electrónico válido.';
        $messageType = 'error';
    } else {
        $verificar = $conexion->prepare('SELECT id FROM usuario WHERE correo = ?');
        $verificar->bind_param('s', $correo);
        $verificar->execute();
        $verificar->store_result();

        if ($verificar->num_rows > 0) {
            $message = 'Ya existe una cuenta con ese correo.';
            $messageType = 'error';
        } else {
            $hash = password_hash($contraseña, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare('INSERT INTO usuario (correo, contraseña, nombre) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $correo, $hash, $nombre);

            if ($stmt->execute()) {
                $message = '¡Registro correcto! Ya puedes iniciar sesión.';
                $messageType = 'success';
            } else {
                $message = 'No se pudo completar el registro: ' . $stmt->error;
                $messageType = 'error';
            }
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
    <a class="logo-back" href="index.php" aria-label="Volver al inicio">
        <img src="../img/logoblanco.png" alt="Volver al inicio">
    </a>

    <main>
        <section class="contenedor">
            <h1>Regístrate</h1>
            <p class="intro">Completa tus datos para crear tu cuenta y empezar a disfrutar del sitio.</p>

            <?php if ($message !== ''): ?>
                <p class="<?php echo $messageType === 'success' ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <form method="post" action="registrarse.php">
                <label for="Nombre">Nombre completo</label>
                <input type="text" id="Nombre" name="nombre" placeholder="Nombre completo" required />

                <label for="CorreoElectronico">Correo electrónico</label>
                <input type="email" id="CorreoElectronico" name="correo" placeholder="Correo electrónico" required />

                <label for="Contraseña">Contraseña</label>
                <input type="password" id="Contraseña" name="contraseña" placeholder="Contraseña" required />

                <label for="Telefono">Número telefónico</label>
                <input type="tel" id="Telefono" name="telefono" placeholder="Número telefónico" />

                <div class="botones">
                    <button type="submit" id="btnAgregar" class="action-btn">Registrarse</button>
                    <a class="action-btn" href="login.php">Iniciar Sesión</a>
                </div>

                <a class="google-button" href="https://accounts.google.com/signin/v2/identifier?service=mail" target="_blank" rel="noopener noreferrer">
                    <img class="google-icon" src="../img/google-logo.png" alt="Google logo">
                    Iniciar con Google
                </a>
                <p class="google-legal">Al registrarte aceptas los <a href="#">términos y condiciones</a> de la empresa, las <a href="#">reglas de uso</a> y la <a href="#">política de privacidad</a>.</p>
            </form>
        </section>
    </main>
</body>
</html>
