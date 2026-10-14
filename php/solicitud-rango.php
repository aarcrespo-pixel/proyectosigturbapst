<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/solicitudes_rango.php';

$usuario = sigtur_requerir_rol($pdo, ['turista', 'organizador'], 'login.php', '../index.php');
sigtur_asegurar_tabla_solicitudes_rango($pdo);
$csrfToken = sigtur_csrf_token();
$error = '';
$rolesSolicitables = $usuario['rol'] === 'organizador'
    ? ['turista', 'administrador']
    : ['organizador', 'administrador'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? null;
    if (!sigtur_validar_csrf(is_string($csrf) ? $csrf : null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }

    $rolSolicitado = $_POST['rol_solicitado'] ?? '';
    $domicilio = $_POST['domicilio'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $aceptaPrivacidad = $_POST['acepta_privacidad'] ?? '';
    $rolSolicitado = is_string($rolSolicitado) ? $rolSolicitado : '';
    $domicilio = is_string($domicilio) ? trim($domicilio) : '';
    $motivo = is_string($motivo) ? trim($motivo) : '';
    $longitudDomicilio = sigtur_longitud_utf8($domicilio);
    $longitudMotivo = sigtur_longitud_utf8($motivo);

    if (!in_array($rolSolicitado, $rolesSolicitables, true)) {
        $error = 'Selecciona un rango válido.';
    } elseif ($domicilio === '' || $longitudDomicilio === null || $longitudDomicilio > 255) {
        $error = 'Ingresa tu domicilio en hasta 255 caracteres.';
    } elseif ($longitudMotivo === null || $longitudMotivo < 20 || $longitudMotivo > 500) {
        $error = 'Explica tu solicitud en entre 20 y 500 caracteres.';
    } elseif (!is_string($aceptaPrivacidad) || $aceptaPrivacidad !== 'on') {
        $error = 'Debes confirmar que leíste cómo se usarán tus datos.';
    } else {
        $pdo->beginTransaction();
        try {
            $bloquearUsuario = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id FOR UPDATE');
            $bloquearUsuario->execute([':id' => (int) $usuario['id']]);
            $rolActual = $bloquearUsuario->fetchColumn();
            if ($rolActual === false || sigtur_normalizar_rol((string) $rolActual) !== $usuario['rol']) {
                $pdo->rollBack();
                http_response_code(403);
                exit('El rango de la cuenta cambió. Recarga la página antes de solicitar otro.');
            }

            $pendiente = $pdo->prepare("SELECT id FROM solicitudes_rango WHERE usuario_id = :id AND estado = 'pendiente' LIMIT 1");
            $pendiente->execute([':id' => (int) $usuario['id']]);
            if ($pendiente->fetchColumn()) {
                $pdo->rollBack();
                $error = 'Ya tienes una solicitud pendiente de revisión.';
            } else {
                $insertar = $pdo->prepare("INSERT INTO solicitudes_rango (usuario_id, rol_solicitado, domicilio, motivo)
                    VALUES (:usuario_id, :rol_solicitado, :domicilio, :motivo)");
                $insertar->execute([
                    ':usuario_id' => (int) $usuario['id'],
                    ':rol_solicitado' => $rolSolicitado,
                    ':domicilio' => $domicilio,
                    ':motivo' => $motivo,
                ]);
                $pdo->commit();
                header('Location: solicitud-rango.php?enviada=1');
                exit;
            }
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Error al registrar solicitud de rango: ' . $exception->getMessage());
            http_response_code(500);
            exit('No se pudo guardar la solicitud. Inténtalo nuevamente.');
        }
    }
}

$consultaSolicitud = $pdo->prepare('SELECT id, rol_solicitado, estado, fecha_creacion FROM solicitudes_rango WHERE usuario_id = :id ORDER BY fecha_creacion DESC, id DESC LIMIT 1');
$consultaSolicitud->execute([':id' => (int) $usuario['id']]);
$solicitudReciente = $consultaSolicitud->fetch();
$datosPersonales = null;
if ($usuario['rol'] === 'turista' || $usuario['rol'] === 'organizador') {
    $datosUsuario = $pdo->prepare('SELECT nombre_completo, email, cedula, telefono FROM usuarios WHERE id = :id LIMIT 1');
    $datosUsuario->execute([':id' => (int) $usuario['id']]);
    $datosPersonales = $datosUsuario->fetch();
    if (!$datosPersonales) {
        http_response_code(404);
        exit('No se encontró la cuenta.');
    }
}
$solicitudEnviada = ($_GET['enviada'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud de rango | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
    <link rel="stylesheet" href="../css/solicitud-rango.css">
</head>
<body class="admin-panel-page">
    <main class="admin-panel solicitud-rango">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Cuenta</p>
                <h1>Solicitud de rango</h1>
                <p>Solicita que el equipo revise un cambio de rango para tu cuenta.</p>
            </div>
            <nav class="admin-nav" aria-label="Navegación">
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Volver al inicio</a>
            </nav>
        </header>

        <?php if ($solicitudEnviada): ?>
            <p class="solicitud-alert solicitud-alert--success" role="status">Tu solicitud fue enviada al equipo administrador.</p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
            <p class="solicitud-alert solicitud-alert--error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($solicitudReciente && $solicitudReciente['estado'] === 'pendiente'): ?>
            <section class="solicitud-card">
                <p class="admin-eyebrow">En revisión</p>
                <h2>Ya recibimos tu solicitud</h2>
                <p>Solicitaste el rango de <strong><?= htmlspecialchars($solicitudReciente['rol_solicitado'], ENT_QUOTES, 'UTF-8') ?></strong> el <?= htmlspecialchars($solicitudReciente['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?>. Un administrador la revisará. Cuando esta solicitud sea aprobada o rechazada, podrás enviar otra si todavía quieres cambiar tu rango.</p>
            </section>
        <?php elseif ($solicitudReciente && $solicitudReciente['estado'] === 'aprobada' && $usuario['rol'] === 'administrador'): ?>
            <section class="solicitud-card">
                <p class="admin-eyebrow">Solicitud aprobada</p>
                <h2>Tu cuenta ya tiene el rango de administrador</h2>
                <p>La solicitud para cambiar tu rango fue aprobada.</p>
            </section>
        <?php elseif ($usuario['rol'] === 'administrador'): ?>
            <section class="solicitud-card">
                <p class="admin-eyebrow">Rango administrador</p>
                <h2>No necesitas solicitar un cambio de rango</h2>
                <p>Tu cuenta ya tiene el rango de administrador.</p>
            </section>
        <?php else: ?>
            <?php if ($solicitudReciente && $solicitudReciente['estado'] === 'rechazada'): ?>
                <p class="solicitud-alert solicitud-alert--error" role="status">Tu solicitud anterior para <strong><?= htmlspecialchars($solicitudReciente['rol_solicitado'], ENT_QUOTES, 'UTF-8') ?></strong> fue rechazada. Si tus circunstancias cambiaron, puedes enviar una nueva.</p>
            <?php elseif ($solicitudReciente && $solicitudReciente['estado'] === 'aprobada'): ?>
                <p class="solicitud-alert solicitud-alert--success" role="status">Tu solicitud anterior fue aprobada. Si necesitas otro cambio de rango, puedes enviar una nueva solicitud.</p>
            <?php endif; ?>
            <section class="solicitud-card">
                <div class="solicitud-privacidad">
                    <strong>Datos personales y privacidad</strong>
                    <p>El nombre, correo y documento ya registrados en tu cuenta se compartirán con los administradores junto con tu domicilio y explicación, solo para evaluar este cambio de rango. No incluyas información privada de otras personas. El domicilio y la explicación se borrarán al resolverla.</p>
                </div>

                <form method="post" class="solicitud-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                    <div class="solicitud-datos">
                        <label>Nombre completo
                            <input value="<?= htmlspecialchars($datosPersonales['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                        </label>
                        <label>Correo electrónico
                            <input type="email" value="<?= htmlspecialchars($datosPersonales['email'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                        </label>
                        <label>Documento de identidad registrado
                            <input value="<?= htmlspecialchars($datosPersonales['cedula'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                        </label>
                        <label>Teléfono registrado
                            <input value="<?= htmlspecialchars($datosPersonales['telefono'] ?: 'No registrado', ENT_QUOTES, 'UTF-8') ?>" readonly>
                        </label>
                    </div>

                    <label>Rango solicitado
                        <select name="rol_solicitado" required>
                            <option value="">Selecciona una opción</option>
                            <?php if ($usuario['rol'] === 'turista'): ?>
                                <option value="organizador" <?= ($_POST['rol_solicitado'] ?? '') === 'organizador' ? 'selected' : '' ?>>Organizador</option>
                            <?php else: ?>
                                <option value="turista" <?= ($_POST['rol_solicitado'] ?? '') === 'turista' ? 'selected' : '' ?>>Turista</option>
                            <?php endif; ?>
                            <option value="administrador" <?= ($_POST['rol_solicitado'] ?? '') === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                        </select>
                    </label>
                    <label>Domicilio
                        <input name="domicilio" maxlength="255" autocomplete="street-address" required value="<?= htmlspecialchars(is_string($_POST['domicilio'] ?? null) ? $_POST['domicilio'] : '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Calle, número, ciudad y departamento">
                        <small>Se solicita para verificar tu identidad. No agregues referencias privadas innecesarias.</small>
                    </label>
                    <label>¿Por qué solicitas este cambio de rango?
                        <textarea name="motivo" minlength="20" maxlength="500" rows="5" required><?= htmlspecialchars(is_string($_POST['motivo'] ?? null) ? $_POST['motivo'] : '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <small>Explica brevemente el motivo (20 a 500 caracteres).</small>
                    </label>
                    <label class="solicitud-consentimiento">
                        <input type="checkbox" name="acepta_privacidad" required>
                        <span>Confirmo que estos datos son correctos y acepto que el equipo administrador los use para evaluar esta solicitud.</span>
                    </label>
                    <button class="admin-button" type="submit">Enviar solicitud</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
