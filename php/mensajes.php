<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', '../index.php');
$csrfToken = sigtur_csrf_token();
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }

    $preguntaId = filter_var($_POST['pregunta_id'] ?? null, FILTER_VALIDATE_INT);
    $respuesta = trim((string) ($_POST['respuesta'] ?? ''));
    $longitudRespuesta = mb_strlen($respuesta, 'UTF-8');
    if (!$preguntaId || $preguntaId <= 0 || $respuesta === '' || $longitudRespuesta > 4000) {
        http_response_code(422);
        exit('La respuesta es obligatoria y no puede superar los 4000 caracteres.');
    }

    $pdo->beginTransaction();
    try {
        $bloquearPregunta = $pdo->prepare("SELECT id FROM preguntas_frecuentes WHERE id = :id AND estado = 'pendiente' FOR UPDATE");
        $bloquearPregunta->execute([':id' => $preguntaId]);
        if (!$bloquearPregunta->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('La pregunta ya fue respondida o no existe.');
        }
        $insertarRespuesta = $pdo->prepare("INSERT INTO preguntas_frecuentes_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
            VALUES (:pregunta_id, :autor_id, 'administrador', :mensaje)");
        $insertarRespuesta->execute([
            ':pregunta_id' => $preguntaId,
            ':autor_id' => (int) $administrador['id'],
            ':mensaje' => $respuesta,
        ]);
        $actualizarPregunta = $pdo->prepare("UPDATE preguntas_frecuentes
            SET respuesta = :respuesta, respondido_por = :administrador, fecha_respuesta = CURRENT_TIMESTAMP, estado = 'respondida'
            WHERE id = :id");
        $actualizarPregunta->execute([
            ':respuesta' => $respuesta,
            ':administrador' => (int) $administrador['id'],
            ':id' => $preguntaId,
        ]);
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al responder pregunta de soporte: ' . $exception->getMessage());
        http_response_code(500);
        exit('No se pudo guardar la respuesta. Inténtalo nuevamente.');
    }
    header('Location: mensajes.php?respuesta=1#preguntasFaqTitle');
    exit;
}

$reportesStmt = $pdo->query("SELECT m.id, m.mensaje, m.contexto, m.fecha_creacion, u.nombre_completo, u.email FROM mensajes m INNER JOIN usuarios u ON u.id = m.remitente_id WHERE m.mensaje LIKE '[Consulta %' ORDER BY m.fecha_creacion DESC LIMIT 100");
$reportes = $reportesStmt->fetchAll();
$headerCurrentPage = 'mensajes';
$headerActivePage = 'mensajes';
$preguntasFaqStmt = $pdo->query("SELECT p.id, p.pregunta, p.respuesta, p.estado, p.fecha_creacion, p.fecha_respuesta, u.nombre_completo, COALESCE(p.email, u.email) AS email FROM preguntas_frecuentes p LEFT JOIN usuarios u ON u.id = p.usuario_id WHERE p.estado = 'pendiente' ORDER BY p.fecha_creacion DESC");
$preguntasFaq = $preguntasFaqStmt->fetchAll();
$mensajesPorPregunta = [];
if ($preguntasFaq) {
    $marcadoresFaq = implode(',', array_fill(0, count($preguntasFaq), '?'));
    $mensajesFaqStmt = $pdo->prepare("SELECT pregunta_id, autor_rol, mensaje, fecha_creacion
        FROM preguntas_frecuentes_mensajes
        WHERE pregunta_id IN ($marcadoresFaq)
        ORDER BY id ASC");
    $mensajesFaqStmt->execute(array_column($preguntasFaq, 'id'));
    foreach ($mensajesFaqStmt->fetchAll() as $mensajeFaq) {
        $mensajesPorPregunta[(int) $mensajeFaq['pregunta_id']][] = $mensajeFaq;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mensajes de administración | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
    <link rel="stylesheet" href="../css/mensajes-admin.css">
    <link rel="stylesheet" href="../css/listado-expandible.css">
</head>
<body class="admin-panel-page pagina-mensajes-admin">
    <main class="admin-panel admin-mensajes-main">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Gestionar mensajes</h1>
                <p>Administra las conversaciones y revisa los mensajes que llegan desde soporte.</p>
            </div>
            <nav class="admin-nav" aria-label="Accesos de administración">
                <a class="admin-nav__link" href="panel-admin.php">Panel de administración</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <section class="admin-section admin-mensajes-reportes" aria-labelledby="mensajesDirectosTitle">
            <div class="admin-section__heading">
                <div><p class="admin-eyebrow">Bandeja interna</p><h2 id="mensajesDirectosTitle">Mensajes entre administradores</h2><p>La mensajería directa está disponible en el botón flotante de la esquina inferior.</p></div>
            </div>
        </section>

        <section class="admin-section admin-mensajes-reportes" aria-labelledby="preguntasFaqTitle">
            <div class="admin-section__heading">
                <div><p class="admin-eyebrow">Centro de ayuda</p><h2 id="preguntasFaqTitle">Preguntas recibidas desde Soporte</h2><p>Consultas enviadas por los visitantes desde la sección de soporte.</p></div>
                <span class="admin-count"><?= count($preguntasFaq) ?> preguntas</span>
            </div>
            <?php if (($_GET['respuesta'] ?? '') === '1'): ?><p class="admin-notice" role="status">La respuesta quedó guardada y ya está disponible para el usuario en Preguntas frecuentes.</p><?php endif; ?>
            <?php if (!$preguntasFaq): ?><p class="admin-empty">No hay preguntas pendientes de respuesta.</p><?php endif; ?>
            <div class="admin-lista-expandible" data-lista-expandible data-cantidad-inicial="10">
            <?php foreach ($preguntasFaq as $preguntaFaq): ?>
                <article class="admin-mensajes-reporte" data-lista-item>
                    <div class="admin-mensajes-reporte__header">
                        <div><strong><?= htmlspecialchars($preguntaFaq['nombre_completo'] ?: 'Visitante', ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($preguntaFaq['email'] ?: 'Sin correo', ENT_QUOTES, 'UTF-8') ?></span></div>
                        <span class="admin-mensajes-estado <?= $preguntaFaq['estado'] === 'pendiente' ? 'admin-mensajes-estado--pendiente' : '' ?>"><?= htmlspecialchars($preguntaFaq['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars($preguntaFaq['pregunta'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <small><?= htmlspecialchars($preguntaFaq['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                    <?php foreach ($mensajesPorPregunta[(int) $preguntaFaq['id']] ?? [] as $mensajeFaq): ?>
                        <div class="admin-mensajes-respuesta <?= $mensajeFaq['autor_rol'] === 'turista' ? 'admin-mensajes-respuesta--visitante' : '' ?>">
                            <strong><?= $mensajeFaq['autor_rol'] === 'turista' ? 'Respuesta del turista' : 'Respuesta del administrador' ?></strong>
                            <p><?= nl2br(htmlspecialchars($mensajeFaq['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <small><?= htmlspecialchars($mensajeFaq['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                    <?php endforeach; ?>
                    <form class="admin-form admin-mensajes-responder" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $preguntaFaq['id'] ?>">
                        <label>Responder al visitante
                            <textarea name="respuesta" maxlength="4000" required></textarea>
                        </label>
                        <div class="admin-form__actions"><button class="admin-button" type="submit">Enviar respuesta</button></div>
                    </form>
                </article>
            <?php endforeach; ?>
            </div>
            <?php if (count($preguntasFaq) > 10): ?>
                <button class="admin-lista-expandible__boton" type="button" data-lista-boton data-texto-mas="Ver más preguntas" data-texto-menos="Ver menos preguntas" aria-expanded="false">Ver más preguntas</button>
            <?php endif; ?>
        </section>
        <section class="admin-section admin-mensajes-reportes" aria-labelledby="reportesTitle">
            <div class="admin-section__heading">
                <div><p class="admin-eyebrow">Seguimiento</p><h2 id="reportesTitle">Reportes de soporte recibidos</h2><p>Mensajes recibidos para revisión por el equipo administrador.</p></div>
                <span class="admin-count"><?= count($reportes) ?> reportes</span>
            </div>
            <?php if (!$reportes): ?><p class="admin-empty">No hay reportes pendientes de revisión.</p><?php endif; ?>
            <?php foreach ($reportes as $reporte): ?>
                <article class="admin-mensajes-reporte">
                    <div class="admin-mensajes-reporte__header">
                        <div><strong><?= htmlspecialchars($reporte['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong><span><?= htmlspecialchars($reporte['email'], ENT_QUOTES, 'UTF-8') ?></span></div>
                        <span class="admin-mensajes-estado"><?= htmlspecialchars($reporte['contexto'] ?? 'Soporte', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <p><?= nl2br(htmlspecialchars(preg_replace('/^\[Consulta [^\]]+\]:\s*/', '', $reporte['mensaje']) ?? $reporte['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <small><?= htmlspecialchars($reporte['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script src="../js/listado-expandible.js" defer></script>
</body>
</html>