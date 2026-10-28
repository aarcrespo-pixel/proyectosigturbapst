<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', '../index.php');
$administradorId = (int) $administrador['id'];
$csrfToken = sigtur_csrf_token();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        exit('La sesión expiró. Recargá la página e intentá nuevamente.');
    }

    $reporteId = filter_var($_POST['reporte_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $respuesta = trim(is_string($_POST['respuesta'] ?? null) ? $_POST['respuesta'] : '');
    $longitudRespuesta = function_exists('mb_strlen') ? mb_strlen($respuesta, 'UTF-8') : strlen($respuesta);
    if (!$reporteId || $respuesta === '' || $longitudRespuesta > 500) {
        http_response_code(422);
        exit('La respuesta es obligatoria y no puede superar los 500 caracteres.');
    }

    $reporteStmt = $pdo->prepare("SELECT remitente_id FROM mensajes WHERE id = :id AND destinatario_id = :administrador AND mensaje LIKE '[Consulta %' LIMIT 1");
    $reporteStmt->execute([':id' => $reporteId, ':administrador' => $administradorId]);
    $destinatarioId = (int) $reporteStmt->fetchColumn();
    if ($destinatarioId <= 0) {
        http_response_code(404);
        exit('No se encontró el reporte seleccionado.');
    }

    $pdo->beginTransaction();
    try {
        $insertarRespuesta = $pdo->prepare('INSERT INTO mensajes (remitente_id, destinatario_id, mensaje, contexto) VALUES (:remitente, :destinatario, :mensaje, :contexto)');
        $insertarRespuesta->execute([
            ':remitente' => $administradorId,
            ':destinatario' => $destinatarioId,
            ':mensaje' => $respuesta,
            ':contexto' => 'soporte:reporte:' . (int) $reporteId,
        ]);
        $marcarLeido = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE id = :id AND destinatario_id = :administrador');
        $marcarLeido->execute([':id' => $reporteId, ':administrador' => $administradorId]);
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error al responder un reporte de soporte: ' . $error->getMessage());
        http_response_code(500);
        exit('No se pudo enviar la respuesta. Intentá nuevamente.');
    }

    header('Location: mensajes.php?respondido=1#reporte-' . (int) $reporteId);
    exit;
}

$reportesStmt = $pdo->prepare("SELECT m.id, m.remitente_id, m.mensaje, m.eliminado_para_todos, m.contexto, m.fecha_creacion, u.nombre_completo, u.email
    FROM mensajes m
    INNER JOIN usuarios u ON u.id = m.remitente_id
    WHERE m.destinatario_id = :administrador AND m.mensaje LIKE '[Consulta %'
      AND NOT EXISTS (SELECT 1 FROM mensajes_ocultos_usuario oculto WHERE oculto.mensaje_id = m.id AND oculto.usuario_id = :oculto_administrador)
    ORDER BY m.fecha_creacion DESC, m.id DESC
    LIMIT 100");
$reportesStmt->execute([':administrador' => $administradorId, ':oculto_administrador' => $administradorId]);
$reportes = $reportesStmt->fetchAll();
$respuestasPorReporte = [];
if ($reportes) {
    $respuestasStmt = $pdo->prepare("SELECT m.id, m.contexto, m.mensaje, m.eliminado_para_todos, m.fecha_creacion
        FROM mensajes m
        WHERE m.remitente_id = :administrador AND m.contexto LIKE 'soporte:reporte:%'
          AND NOT EXISTS (SELECT 1 FROM mensajes_ocultos_usuario oculto WHERE oculto.mensaje_id = m.id AND oculto.usuario_id = :oculto_administrador)
        ORDER BY m.id ASC");
    $respuestasStmt->execute([':administrador' => $administradorId, ':oculto_administrador' => $administradorId]);
    foreach ($respuestasStmt->fetchAll() as $respuestaGuardada) {
        if (preg_match('/^soporte:reporte:(\d+)$/', (string) $respuestaGuardada['contexto'], $coincidencia) === 1) {
            $respuestasPorReporte[(int) $coincidencia[1]][] = $respuestaGuardada;
        }
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
</head>
<body class="admin-panel-page">
    <main class="admin-panel admin-mensajes-main">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Gestionar mensajes</h1>
                <p>Revisa reportes y responde directamente a cada persona.</p>
            </div>
            <nav class="admin-nav" aria-label="Navegación de administración">
                <a class="admin-nav__link" href="panel-admin.php">Panel admin</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <?php if (($_GET['respondido'] ?? '') === '1'): ?>
            <p class="admin-notice" role="status">La respuesta se envió correctamente.</p>
        <?php endif; ?>

        <section class="admin-section admin-mensajes-reportes" aria-labelledby="reportesTitle">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Bandeja de entrada</p>
                    <h2 id="reportesTitle">Reportes de soporte</h2>
                    <p><?= count($reportes) ?> reportes recientes</p>
                </div>
            </div>
            <?php if (!$reportes): ?><p class="admin-empty">No hay reportes de soporte recibidos.</p><?php endif; ?>
            <?php foreach ($reportes as $reporte): ?>
                <?php $respuestas = $respuestasPorReporte[(int) $reporte['id']] ?? []; ?>
                <article class="admin-mensajes-reporte" id="reporte-<?= (int) $reporte['id'] ?>">
                    <header class="admin-mensajes-reporte__header">
                        <div>
                            <strong><?= htmlspecialchars($reporte['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= htmlspecialchars($reporte['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <span class="admin-mensajes-estado <?= $respuestas ? '' : 'admin-mensajes-estado--pendiente' ?>"><?= $respuestas ? 'Respondido' : 'Pendiente' ?></span>
                    </header>
                    <div class="admin-question-chat__message admin-question-chat__message--visitor" data-message-item data-message-type="direct" data-message-api="api/mensajes.php" data-message-id="<?= (int) $reporte['id'] ?>" data-can-edit="0" data-can-delete="0">
                        <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><button type="button" data-message-action="delete-me"<?= !empty($reporte['eliminado_para_todos']) ? '' : ' disabled' ?>>Eliminar para mí</button></div></details>
                        <p data-message-text><?= !empty($reporte['eliminado_para_todos']) ? 'Mensaje eliminado para todos' : nl2br(htmlspecialchars(preg_replace('/^\[Consulta [^\]]+\]:\s*/', '', $reporte['mensaje']) ?? $reporte['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                    <small><?= htmlspecialchars($reporte['contexto'] ?? 'Soporte', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars(date('d/m/Y H:i', strtotime($reporte['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></small>
                    <?php foreach ($respuestas as $respuestaGuardada): ?>
                        <div class="admin-mensajes-respuesta" data-message-item data-message-type="direct" data-message-api="api/mensajes.php" data-message-id="<?= (int) $respuestaGuardada['id'] ?>" data-can-edit="<?= empty($respuestaGuardada['eliminado_para_todos']) ? '1' : '0' ?>" data-can-delete="1">
                            <strong>Respuesta del equipo</strong>
                            <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (empty($respuestaGuardada['eliminado_para_todos'])): ?><button type="button" data-message-action="edit">Editar</button><button type="button" data-message-action="delete-me">Eliminar para mí</button><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php else: ?><button type="button" data-message-action="delete-me">Eliminar para mí</button><?php endif; ?></div></details>
                            <p data-message-text><?= !empty($respuestaGuardada['eliminado_para_todos']) ? 'Mensaje eliminado para todos' : nl2br(htmlspecialchars($respuestaGuardada['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <small><?= htmlspecialchars(date('d/m/Y H:i', strtotime($respuestaGuardada['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                    <?php endforeach; ?>
                    <form class="admin-form admin-mensajes-responder" method="post" action="mensajes.php#reporte-<?= (int) $reporte['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="reporte_id" value="<?= (int) $reporte['id'] ?>">
                        <label class="admin-form__wide">Responder
                            <textarea name="respuesta" maxlength="500" required placeholder="Escribe una respuesta (máximo 500 caracteres)…"></textarea>
                        </label>
                        <div class="admin-form__actions">
                            <button class="admin-button" type="submit">Enviar respuesta</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script src="../js/tema.js" defer></script>
    <script src="../js/mensajes-acciones.js"></script>
</body>
</html>