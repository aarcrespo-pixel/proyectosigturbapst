<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/correo.php';
sigtur_iniciar_sesion_segura();

$pdo->exec("CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `user_id` INT NOT NULL PRIMARY KEY,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `requested_at` DATETIME NOT NULL,
    KEY `idx_password_reset_expiry` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS `password_reset_rate_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `rate_key` CHAR(64) NOT NULL,
    `created_at` DATETIME NOT NULL,
    KEY `idx_password_reset_rate_key_date` (`rate_key`, `created_at`),
    KEY `idx_password_reset_rate_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec('DELETE FROM password_reset_tokens WHERE expires_at <= UTC_TIMESTAMP()');
$pdo->exec('DELETE FROM password_reset_rate_events WHERE created_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)');

$mensajeEstado = '';
$tipoEstado = 'status';
$emailSolicitado = '';
$accion = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $emailSolicitado = is_string($_POST['email'] ?? null) ? strtolower(trim($_POST['email'])) : '';
    $csrf = is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null;
    if (!sigtur_validar_csrf($csrf)) {
        $mensajeEstado = 'La sesión expiró. Recargá la página e intentá nuevamente.';
        $tipoEstado = 'error';
    } elseif ($accion === 'request') {
        if (!filter_var($emailSolicitado, FILTER_VALIDATE_EMAIL) || strlen($emailSolicitado) > 100) {
            $mensajeEstado = 'Ingresá una dirección de correo válida.';
            $tipoEstado = 'error';
        } else {
            $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $ipKey = hash('sha256', "ip\0" . $ip);
            $pairKey = hash('sha256', "email-ip\0" . $ip . "\0" . $emailSolicitado);
            $consultarLimites = $pdo->prepare(
                'SELECT rate_key, COUNT(*) AS total FROM password_reset_rate_events
                 WHERE rate_key IN (:ip_key, :pair_key)
                   AND created_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)
                 GROUP BY rate_key'
            );
            $consultarLimites->execute([':ip_key' => $ipKey, ':pair_key' => $pairKey]);
            $intentos = [];
            foreach ($consultarLimites->fetchAll() as $limite) {
                $intentos[$limite['rate_key']] = (int) $limite['total'];
            }
            if (($intentos[$ipKey] ?? 0) >= 10 || ($intentos[$pairKey] ?? 0) >= 3) {
                $mensajeEstado = 'Se alcanzó el límite de solicitudes. Esperá un tiempo antes de volver a intentarlo.';
                $tipoEstado = 'error';
            } elseif (!filter_var(sigtur_correo_configuracion('SIGTUR_MAIL_FROM'), FILTER_VALIDATE_EMAIL)) {
                $mensajeEstado = 'El servicio de correo todavía no está configurado. Contactá al soporte de SIGTUR.';
                $tipoEstado = 'error';
            } else {
                $registrarIntento = $pdo->prepare(
                    'INSERT INTO password_reset_rate_events (rate_key, created_at)
                     VALUES (:ip_key, UTC_TIMESTAMP()), (:pair_key, UTC_TIMESTAMP())'
                );
                $registrarIntento->execute([':ip_key' => $ipKey, ':pair_key' => $pairKey]);

                $buscarUsuario = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
                $buscarUsuario->execute([':email' => $emailSolicitado]);
                $usuarioId = (int) $buscarUsuario->fetchColumn();

                if ($usuarioId > 0) {
                    $buscarSolicitud = $pdo->prepare(
                        'SELECT requested_at FROM password_reset_tokens WHERE user_id = :user_id LIMIT 1'
                    );
                    $buscarSolicitud->execute([':user_id' => $usuarioId]);
                    $solicitudAnterior = $buscarSolicitud->fetchColumn();
                    $puedeEnviar = !$solicitudAnterior
                        || strtotime((string) $solicitudAnterior . ' UTC') <= time() - 60;

                    if ($puedeEnviar) {
                        $codigo = '';
                        for ($indice = 0; $indice < 10; $indice++) {
                            $codigo .= (string) random_int(0, 9);
                        }
                        $guardarToken = $pdo->prepare(
                            'INSERT INTO password_reset_tokens (user_id, token_hash, expires_at, attempts, requested_at)
                             VALUES (:user_id, :token_hash, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 15 MINUTE), 0, UTC_TIMESTAMP())
                             ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash),
                                 expires_at = VALUES(expires_at), attempts = 0, requested_at = VALUES(requested_at)'
                        );
                        $guardarToken->execute([
                            ':user_id' => $usuarioId,
                            ':token_hash' => password_hash($codigo, PASSWORD_DEFAULT),
                        ]);
                        try {
                            sigtur_enviar_codigo_recuperacion($emailSolicitado, $codigo);
                        } catch (RuntimeException $error) {
                            error_log('SIGTUR password-reset email delivery failed: ' . $error->getMessage());
                        }
                    }
                }

                if ($mensajeEstado === '') {
                    $mensajeEstado = 'Si el correo corresponde a una cuenta, recibirás un código si el servicio de correo está disponible. Revisá también el correo no deseado.';
                }
            }
        }
    } elseif ($accion === 'reset') {
        $codigo = is_string($_POST['code'] ?? null) ? trim($_POST['code']) : '';
        $nuevaContrasena = is_string($_POST['new_password'] ?? null) ? $_POST['new_password'] : '';
        $confirmarContrasena = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        if (!filter_var($emailSolicitado, FILTER_VALIDATE_EMAIL) || strlen($emailSolicitado) > 100) {
            $mensajeEstado = 'El correo, el código o su vigencia no son válidos.';
            $tipoEstado = 'error';
        } elseif (preg_match('/^\d{10}$/', $codigo) !== 1) {
            $mensajeEstado = 'Ingresá el código de 10 dígitos recibido por correo.';
            $tipoEstado = 'error';
        } elseif (strlen($nuevaContrasena) < 12 || strlen($nuevaContrasena) > 72) {
            $mensajeEstado = 'La nueva contraseña debe tener entre 12 y 72 caracteres.';
            $tipoEstado = 'error';
        } elseif (!hash_equals($nuevaContrasena, $confirmarContrasena)) {
            $mensajeEstado = 'Las contraseñas no coinciden.';
            $tipoEstado = 'error';
        } else {
            $pdo->beginTransaction();
            $buscarToken = $pdo->prepare(
                'SELECT u.id, t.token_hash, t.expires_at, t.attempts
                 FROM usuarios u
                 INNER JOIN password_reset_tokens t ON t.user_id = u.id
                 WHERE u.email = :email
                 LIMIT 1 FOR UPDATE'
            );
            $buscarToken->execute([':email' => $emailSolicitado]);
            $token = $buscarToken->fetch();
            if (
                !$token
                || strtotime((string) $token['expires_at'] . ' UTC') <= time()
                || (int) $token['attempts'] >= 5
            ) {
                if ($token) {
                    $eliminarToken = $pdo->prepare('DELETE FROM password_reset_tokens WHERE user_id = :user_id');
                    $eliminarToken->execute([':user_id' => (int) $token['id']]);
                }
                $pdo->commit();
                $mensajeEstado = 'El correo, el código o su vigencia no son válidos. Solicitá un código nuevo.';
                $tipoEstado = 'error';
            } elseif (!password_verify($codigo, (string) $token['token_hash'])) {
                $sumarIntento = $pdo->prepare('UPDATE password_reset_tokens SET attempts = attempts + 1 WHERE user_id = :user_id');
                $sumarIntento->execute([':user_id' => (int) $token['id']]);
                $pdo->commit();
                $mensajeEstado = 'El correo, el código o su vigencia no son válidos.';
                $tipoEstado = 'error';
            } else {
                $actualizarContrasena = $pdo->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
                $actualizarContrasena->execute([
                    ':password' => password_hash($nuevaContrasena, PASSWORD_DEFAULT),
                    ':id' => (int) $token['id'],
                ]);
                $eliminarToken = $pdo->prepare('DELETE FROM password_reset_tokens WHERE user_id = :user_id');
                $eliminarToken->execute([':user_id' => (int) $token['id']]);
                $pdo->commit();
                header('Location: login.php?recuperacion=exito');
                exit;
            }
        }
    } else {
        $mensajeEstado = 'La solicitud no es válida. Recargá la página e intentá nuevamente.';
        $tipoEstado = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | SIGTUR</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/recuperar-contrasena.css">
</head>
<body>
    <main>
        <section class="contenedor recovery-card">
            <a class="recovery-back" href="login.php">← Volver a iniciar sesión</a>
            <h1>Recuperar cuenta</h1>
            <p class="intro">Te enviaremos un código de verificación al correo asociado a tu cuenta.</p>

            <?php if ($mensajeEstado !== ''): ?>
                <p class="recovery-message recovery-message--<?= htmlspecialchars($tipoEstado, ENT_QUOTES, 'UTF-8') ?>" role="<?= $tipoEstado === 'error' ? 'alert' : 'status' ?>">
                    <?= htmlspecialchars($mensajeEstado, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endif; ?>

            <form method="post" action="recuperar-contrasena.php" class="recovery-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="request">
                <label for="request-email">Correo de tu cuenta</label>
                <input id="request-email" type="email" name="email" maxlength="100" autocomplete="email" required value="<?= htmlspecialchars($emailSolicitado, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="action-btn">Enviar código</button>
            </form>

            <hr>

            <h2>Ya tengo un código</h2>
            <p class="recovery-help">El código vence a los 15 minutos y permite hasta cinco intentos.</p>
            <form method="post" action="recuperar-contrasena.php" class="recovery-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="action" value="reset">
                <label for="reset-email">Correo de tu cuenta</label>
                <input id="reset-email" type="email" name="email" maxlength="100" autocomplete="email" required value="<?= htmlspecialchars($emailSolicitado, ENT_QUOTES, 'UTF-8') ?>">
                <label for="reset-code">Código de verificación</label>
                <input id="reset-code" type="text" name="code" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" autocomplete="one-time-code" required>
                <label for="new-password">Nueva contraseña</label>
                <input id="new-password" type="password" name="new_password" minlength="12" maxlength="72" autocomplete="new-password" required>
                <label for="confirm-password">Repetí la nueva contraseña</label>
                <input id="confirm-password" type="password" name="confirm_password" minlength="12" maxlength="72" autocomplete="new-password" required>
                <button type="submit" class="action-btn">Cambiar contraseña</button>
            </form>
        </section>
    </main>
</body>
</html>
