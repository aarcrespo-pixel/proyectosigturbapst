<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/consultas_eventos.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'panel-organizador.php');
$csrfToken = sigtur_csrf_token();
$chatAbiertoId = filter_var($_GET['chat'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
sigtur_asegurar_mensajes_consulta_evento($pdo);

$consultasStmt = $pdo->prepare('SELECT p.id, p.fecha_creacion, e.titulo, u.nombre_completo
    FROM preguntas_eventos p
    INNER JOIN eventos e ON e.slug = p.evento_slug
    INNER JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha_creacion DESC, p.id DESC');
$consultasStmt->execute();
$consultas = $consultasStmt->fetchAll();
$mensajesPorConsulta = [];
if ($consultas) {
    $idsConsultas = array_column($consultas, 'id');
    $marcadoresConsultas = implode(',', array_fill(0, count($idsConsultas), '?'));
    $mensajesStmt = $pdo->prepare("SELECT m.id, m.pregunta_id, m.autor_id, m.autor_rol, m.mensaje, m.fecha_creacion, m.clave_migracion, m.eliminado
        FROM preguntas_eventos_mensajes m
        WHERE m.pregunta_id IN ($marcadoresConsultas)
          AND NOT EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
              WHERE oculto.tipo = 'evento' AND oculto.mensaje_id = m.id AND oculto.usuario_id = ?)
        ORDER BY m.id ASC");
    $mensajesStmt->execute([...$idsConsultas, (int) $usuario['id']]);
    foreach ($mensajesStmt->fetchAll() as $mensajeConsulta) {
        $mensajesPorConsulta[(int) $mensajeConsulta['pregunta_id']][] = $mensajeConsulta;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consultas de los eventos | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
</head>
<body class="admin-panel-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Organizador</p>
                <h1>Consultas de los eventos</h1>
                <p>Revisa y responde las preguntas que los visitantes enviaron desde las páginas de tus eventos.</p>
            </div>
            <nav class="admin-nav" aria-label="Accesos de organizador">
                <a class="admin-nav__link admin-nav__link--subtle" href="panel-organizador.php">Panel de organizador</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <section class="admin-section" id="consultas" aria-labelledby="event-questions-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Atención a visitantes</p>
                    <h2 id="event-questions-title">Preguntas recibidas</h2>
                    <p>Las respuestas se enviarán al usuario y quedarán guardadas en el historial de la conversación.</p>
                </div>
            </div>
            <?php if (!$consultas): ?>
                <p class="admin-empty">Todavía no hay consultas de eventos.</p>
            <?php else: ?>
                <div class="admin-question-list">
                    <?php foreach ($consultas as $consulta): ?>
                        <article class="admin-question" id="consulta-<?= (int) $consulta['id'] ?>">
                            <div class="admin-question__header">
                                <strong><?= htmlspecialchars($consulta['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <div class="admin-support-chat__actions">
                                    <form method="post" action="api/responder_consulta_evento.php" onsubmit="return confirm('¿Vaciar los mensajes de esta conversación? La pregunta original se conservará y quedará pendiente.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="pregunta_id" value="<?= (int) $consulta['id'] ?>">
                                        <input type="hidden" name="accion" value="vaciar">
                                        <button class="admin-button admin-button--secondary" type="submit">Vaciar chat</button>
                                    </form>
                                    <form method="post" action="api/responder_consulta_evento.php" onsubmit="return confirm('¿Eliminar esta consulta y todo su chat? Esta acción no se puede deshacer.');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="pregunta_id" value="<?= (int) $consulta['id'] ?>">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <button class="admin-button admin-button--danger" type="submit">Eliminar chat</button>
                                    </form>
                                </div>
                            </div>
                            <details class="admin-support-chat admin-event-question-chat"<?= (int) $chatAbiertoId === (int) $consulta['id'] ? ' open' : '' ?>>
                                <summary class="admin-button admin-button--secondary">
                                    <span class="admin-event-question-chat__show">Ver chat</span>
                                    <span class="admin-event-question-chat__hide">Ocultar chat</span>
                                </summary>
                                <div class="admin-question-chat" aria-label="Conversación de la consulta">
                                    <?php foreach ($mensajesPorConsulta[(int) $consulta['id']] ?? [] as $mensajeConsulta):
                                        $esRespuestaEquipo = in_array($mensajeConsulta['autor_rol'], ['administrador', 'organizador'], true);
                                        $esMensajePropio = (int) ($mensajeConsulta['autor_id'] ?? 0) === (int) $usuario['id'];
                                        $puedeEditarMensaje = $esMensajePropio && empty($mensajeConsulta['eliminado']);
                                        $mensajeEliminado = !empty($mensajeConsulta['eliminado']);
                                        $puedeEliminarMiMensaje = $esMensajePropio || $mensajeEliminado; ?>
                                        <div class="admin-question-chat__message <?= $esRespuestaEquipo ? 'admin-question-chat__message--organizer' : 'admin-question-chat__message--visitor' ?>" data-message-item data-message-type="evento" data-message-id="<?= (int) $mensajeConsulta['id'] ?>" data-can-edit="<?= $puedeEditarMensaje ? '1' : '0' ?>" data-can-delete="<?= $esMensajePropio ? '1' : '0' ?>">
                                            <strong><?= $esRespuestaEquipo ? htmlspecialchars(ucfirst($mensajeConsulta['autor_rol']), ENT_QUOTES, 'UTF-8') : htmlspecialchars($consulta['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (!$mensajeEliminado && $puedeEditarMensaje): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $puedeEliminarMiMensaje ? '' : ' disabled' ?>>Eliminar para mí</button><?php if (!$mensajeEliminado && $esMensajePropio): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                                            <p data-message-text><?= !$mensajeEliminado ? nl2br(htmlspecialchars($mensajeConsulta['mensaje'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado para todos' ?></p>
                                            <small><?= htmlspecialchars($mensajeConsulta['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                            <form class="admin-form admin-reply-form" method="post" action="api/responder_consulta_evento.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="pregunta_id" value="<?= (int) $consulta['id'] ?>">
                                <label>Responder al visitante
                                    <textarea name="respuesta" maxlength="2000" required></textarea>
                                </label>
                                <button class="admin-button" type="submit">Enviar respuesta</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="../js/tema.js" defer></script>
    <script src="../js/mensajes-acciones.js"></script>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
