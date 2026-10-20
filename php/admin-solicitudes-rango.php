<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/solicitudes_rango.php';

$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
sigtur_asegurar_tabla_solicitudes_rango($pdo);
$csrfToken = sigtur_csrf_token();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? null;
    if (!sigtur_validar_csrf(is_string($csrf) ? $csrf : null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }

    $solicitudIdInput = $_POST['solicitud_id'] ?? null;
    $solicitudId = is_string($solicitudIdInput)
        ? filter_var($solicitudIdInput, FILTER_VALIDATE_INT)
        : false;
    $decision = $_POST['decision'] ?? '';
    if (!$solicitudId || !in_array($decision, ['aprobar', 'rechazar'], true)) {
        http_response_code(422);
        exit('La solicitud o la decisión no son válidas.');
    }

    $pdo->beginTransaction();
    try {
        $buscarSolicitud = $pdo->prepare("SELECT usuario_id FROM solicitudes_rango WHERE id = :id AND estado = 'pendiente' LIMIT 1");
        $buscarSolicitud->execute([':id' => $solicitudId]);
        $usuarioIdSolicitud = $buscarSolicitud->fetchColumn();
        if ($usuarioIdSolicitud === false) {
            $pdo->rollBack();
            http_response_code(404);
            exit('La solicitud ya fue revisada o no existe.');
        }

        $bloquearUsuario = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id FOR UPDATE');
        $bloquearUsuario->execute([':id' => (int) $usuarioIdSolicitud]);
        $rolActual = $bloquearUsuario->fetchColumn();
        if ($rolActual === false) {
            $pdo->rollBack();
            http_response_code(404);
            exit('No se encontró la cuenta asociada a la solicitud.');
        }

        $bloquearSolicitud = $pdo->prepare("SELECT usuario_id, rol_solicitado FROM solicitudes_rango WHERE id = :id AND estado = 'pendiente' FOR UPDATE");
        $bloquearSolicitud->execute([':id' => $solicitudId]);
        $solicitud = $bloquearSolicitud->fetch();
        if (!$solicitud || (int) $solicitud['usuario_id'] !== (int) $usuarioIdSolicitud) {
            $pdo->rollBack();
            http_response_code(404);
            exit('La solicitud ya fue revisada o no existe.');
        }

        if ($decision === 'aprobar') {
            $rolActualNormalizado = sigtur_normalizar_rol((string) $rolActual);
            $cambioPermitido = match ($rolActualNormalizado) {
                'turista' => in_array($solicitud['rol_solicitado'], ['organizador', 'administrador'], true),
                'organizador' => in_array($solicitud['rol_solicitado'], ['turista', 'administrador'], true),
                default => false,
            };
            if (!$cambioPermitido) {
                $pdo->rollBack();
                http_response_code(422);
                exit('El cambio de rango solicitado no es válido para el rango actual de la cuenta.');
            }

            if ($rolActualNormalizado !== 'turista' && $rolActualNormalizado !== 'organizador') {
                $pdo->rollBack();
                http_response_code(409);
                exit('La cuenta ya no tiene el rango que permite este cambio. No se modificó el rol.');
            }

            if ($solicitud['rol_solicitado'] === 'organizador') {
                sigtur_liberar_catalogo_legacy($pdo, (int) $solicitud['usuario_id']);
            }
            if ($solicitud['rol_solicitado'] === 'turista') {
                $desvincularEventos = $pdo->prepare('UPDATE eventos SET organizador_id = 0 WHERE organizador_id = :usuario');
                $desvincularEventos->execute([':usuario' => (int) $solicitud['usuario_id']]);
            }
            $actualizarRol = $pdo->prepare('UPDATE usuarios SET rol = :rol WHERE id = :id');
            $actualizarRol->execute([
                ':rol' => $solicitud['rol_solicitado'],
                ':id' => (int) $solicitud['usuario_id'],
            ]);
        }

        $estado = $decision === 'aprobar' ? 'aprobada' : 'rechazada';
        $resolver = $pdo->prepare('UPDATE solicitudes_rango
            SET estado = :estado, revisado_por = :revisado_por, fecha_revision = CURRENT_TIMESTAMP,
                domicilio = \'\', motivo = \'\'
            WHERE id = :id');
        $resolver->execute([
            ':estado' => $estado,
            ':revisado_por' => (int) $administrador['id'],
            ':id' => $solicitudId,
        ]);
        $pdo->commit();
        header('Location: admin-solicitudes-rango.php?resuelta=1');
        exit;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al resolver solicitud de rango: ' . $exception->getMessage());
        http_response_code(500);
        exit('No se pudo resolver la solicitud. Inténtalo nuevamente.');
    }
}

$solicitudes = $pdo->query("SELECT s.id, s.usuario_id, s.rol_solicitado, s.domicilio, s.motivo, s.fecha_creacion,
        u.nombre_completo, u.email, u.cedula, u.telefono
    FROM solicitudes_rango s
    INNER JOIN usuarios u ON u.id = s.usuario_id
    WHERE s.estado = 'pendiente'
    ORDER BY s.fecha_creacion ASC, s.id ASC
    LIMIT 100")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitudes de rango | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
    <link rel="stylesheet" href="../css/solicitud-rango.css">
</head>
<body class="admin-panel-page">
    <main class="admin-panel solicitud-rango">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Solicitudes de rango</h1>
                <p>Revisa la identidad y el motivo antes de asignar un rango.</p>
            </div>
            <nav class="admin-nav" aria-label="Navegación de administración">
                <a class="admin-nav__link" href="panel-admin.php">Panel admin</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <?php if (($_GET['resuelta'] ?? '') === '1'): ?>
            <p class="solicitud-alert solicitud-alert--success" role="status">La solicitud fue resuelta y sus datos personales fueron borrados.</p>
        <?php endif; ?>

        <?php if (!$solicitudes): ?>
            <section class="solicitud-card"><p class="admin-empty">No hay solicitudes pendientes.</p></section>
        <?php else: ?>
            <?php foreach ($solicitudes as $solicitud): ?>
                <article class="solicitud-card solicitud-admin-card">
                    <header>
                        <div>
                            <p class="admin-eyebrow">Rango solicitado: <?= htmlspecialchars($solicitud['rol_solicitado'], ENT_QUOTES, 'UTF-8') ?></p>
                            <h2><?= htmlspecialchars($solicitud['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <p>Enviada <?= htmlspecialchars($solicitud['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </header>
                    <dl class="solicitud-identidad">
                        <div><dt>Correo</dt><dd><?= htmlspecialchars($solicitud['email'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Documento</dt><dd><?= htmlspecialchars($solicitud['cedula'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Teléfono</dt><dd><?= htmlspecialchars($solicitud['telefono'] ?: 'No registrado', ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Domicilio</dt><dd><?= htmlspecialchars($solicitud['domicilio'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                    </dl>
                    <div class="solicitud-motivo">
                        <strong>Motivo de la solicitud</strong>
                        <p><?= nl2br(htmlspecialchars($solicitud['motivo'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                    <form method="post" class="solicitud-decision">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="solicitud_id" value="<?= (int) $solicitud['id'] ?>">
                        <button class="admin-button" type="submit" name="decision" value="aprobar">Aprobar <?= htmlspecialchars($solicitud['rol_solicitado'], ENT_QUOTES, 'UTF-8') ?></button>
                        <button class="admin-button admin-button--danger" type="submit" name="decision" value="rechazar">Rechazar</button>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script src="../js/tema.js" defer></script>
    <script src="../js/translator.js" defer></script>
</body>
</html>
