<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_requerir_rol($pdo, ['administrador'], 'login.php', '../index.php');
$csrfToken = sigtur_csrf_token();
$totalUsuarios = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
require_once __DIR__ . '/helpers/solicitudes_rango.php';
sigtur_asegurar_tabla_solicitudes_rango($pdo);
$solicitudesPendientes = (int) $pdo->query("SELECT COUNT(*) FROM solicitudes_rango WHERE estado = 'pendiente'")->fetchColumn();
$mensajesStmt = $pdo->prepare('SELECT COUNT(*) FROM mensajes WHERE destinatario_id = :administrador AND leido = 0');
$mensajesStmt->execute([':administrador' => (int) $_SESSION['usuario_id']]);
$mensajesPendientes = (int) $mensajesStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
</head>
<body class="admin-panel-page admin-dashboard-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Panel de administración</h1>
            </div>
            <nav class="admin-nav" aria-label="Accesos de administración">
                <a class="admin-nav__link" href="admin-usuarios.php">Gestionar usuarios</a>
                <a class="admin-nav__link" href="admin-solicitudes-rango.php">Solicitud de rango</a>
                <a class="admin-nav__link" href="mensajes.php">Gestionar mensajes</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>
        <div class="admin-overview" aria-label="Resumen de administración">
            <article class="admin-stat"><span class="admin-stat__label">Usuarios registrados</span><strong><?= $totalUsuarios ?></strong></article>
            <article class="admin-stat"><span class="admin-stat__label">Solicitudes de rango pendientes</span><strong><?= $solicitudesPendientes ?></strong></article>
            <article class="admin-stat"><span class="admin-stat__label">Mensajes sin leer</span><strong><?= $mensajesPendientes ?></strong></article>
        </div>
        <section class="admin-section admin-role-intro" aria-labelledby="admin-role-title">
            <p class="admin-eyebrow">Tu rol en SIGTUR</p>
            <h2 id="admin-role-title">Administrador</h2>
            <p>Gestiona usuarios y solicitudes de rango, atiende los mensajes y envía avisos generales a los turistas.</p>
        </section>

        <section class="admin-section" aria-labelledby="notice-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Comunicación</p>
                    <h2 id="notice-title">Avisos para turistas</h2>
                    <p>Envía una notificación general a las cuentas de turista.</p>
                </div>
            </div>
            <details class="admin-content-item">
                <summary>Redactar aviso</summary>
                <form class="admin-form admin-content-form" method="post" action="api/notificaciones.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="accion" value="crear">
                    <input type="hidden" name="destino" value="todos">
                    <label>Título<input name="titulo" maxlength="120" required placeholder="Nuevo evento o aviso"></label>
                    <label>Tipo
                        <select name="tipo">
                            <option value="general">General</option>
                            <option value="evento">Evento</option>
                            <option value="aviso">Aviso</option>
                        </select>
                    </label>
                    <label class="admin-form__wide">Mensaje<textarea name="mensaje" maxlength="500" required placeholder="Escribe el aviso para los turistas..."></textarea></label>
                    <div class="admin-form__wide admin-form__actions"><button class="admin-button" type="submit">Enviar aviso</button></div>
                </form>
            </details>
        </section>

    </main>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
