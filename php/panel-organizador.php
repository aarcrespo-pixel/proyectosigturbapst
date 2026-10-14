<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$todosEventos = $pdo->query("SELECT id, titulo, descripcion, fecha, ubicacion, categoria, tipo_entrada, precio, imagen_portada, organizador_id
    FROM eventos
    ORDER BY CASE WHEN fecha >= CURDATE() THEN 0 ELSE 1 END,
        CASE WHEN fecha >= CURDATE() THEN fecha END ASC,
        CASE WHEN fecha < CURDATE() THEN fecha END DESC,
        id DESC")->fetchAll();
$organizadores = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'organizador' ORDER BY nombre_completo")->fetchAll();
$consultasStmt = $pdo->prepare('SELECT p.id, p.pregunta, p.respuesta, p.fecha_creacion, e.titulo, e.id AS evento_id, u.nombre_completo FROM preguntas_eventos p INNER JOIN eventos e ON e.slug = p.evento_slug INNER JOIN usuarios u ON u.id = p.usuario_id WHERE e.organizador_id = :organizador ORDER BY p.fecha_creacion DESC');
$consultasStmt->execute([':organizador' => (int) $usuario['id']]);
$consultas = $consultasStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel de organizador | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
</head>
<body class="admin-panel-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Organizador</p>
                <h1>Panel de organizador</h1>
                <p>Gestiona todos los eventos y responde las consultas relacionadas con tus eventos.</p>
            </div>
            <nav class="admin-nav" aria-label="Accesos de organizador">
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <section class="admin-section" aria-labelledby="organizer-content-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Contenido turístico</p>
                    <h2 id="organizer-content-title">Gestionar secciones</h2>
                    <p>Administra los contenidos que ven los visitantes en el sitio.</p>
                </div>
            </div>
            <div class="admin-content-links">
                <a class="admin-content-link" href="organizar-contenido.php?seccion=turismo"><strong>Turismo</strong><span>Escapadas, rutas y termas</span></a>
                <a class="admin-content-link" href="organizar-contenido.php?seccion=lugares"><strong>Lugares</strong><span>Lugares emblemáticos y de recreación</span></a>
                <a class="admin-content-link" href="organizar-contenido.php?seccion=gastronomia"><strong>Gastronomía</strong><span>Restaurantes y propuestas locales</span></a>
            </div>
        </section>

        <section class="admin-section" id="consultas" aria-labelledby="event-questions-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Atención a visitantes</p>
                    <h2 id="event-questions-title">Consultas de mis eventos</h2>
                    <p>Responde las preguntas realizadas desde las páginas de tus eventos.</p>
                </div>
            </div>
            <?php if (!$consultas): ?>
                <p class="admin-empty">Todavía no hay consultas vinculadas a tus eventos.</p>
            <?php else: ?>
                <div class="admin-question-list">
                    <?php foreach ($consultas as $consulta): ?>
                        <article class="admin-question">
                            <strong><?= htmlspecialchars($consulta['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <p><strong><?= htmlspecialchars($consulta['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>:</strong> <?= nl2br(htmlspecialchars($consulta['pregunta'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php if ($consulta['respuesta']): ?>
                                <p class="admin-answer"><strong>Respuesta:</strong> <?= nl2br(htmlspecialchars($consulta['respuesta'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php endif; ?>
                            <form class="admin-form admin-reply-form" method="post" action="api/responder_consulta_evento.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="pregunta_id" value="<?= (int) $consulta['id'] ?>">
                                <label>Responder
                                    <textarea name="respuesta" maxlength="2000" required><?= htmlspecialchars($consulta['respuesta'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                </label>
                                <button class="admin-button" type="submit">Enviar respuesta</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php include __DIR__ . '/partials/listado-eventos-administracion.php'; ?>
    </main>
    <script src="../js/panel-admin.js" defer></script>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
