<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$todosEventosStmt = $pdo->query("SELECT e.id, e.titulo, e.descripcion, e.fecha, e.ubicacion, e.categoria, e.tipo_entrada, e.precio, e.imagen_portada, e.organizador_id, u.nombre_completo AS organizador_nombre
    FROM eventos e
    LEFT JOIN usuarios u ON u.id = e.organizador_id
    ORDER BY CASE WHEN fecha >= CURDATE() THEN 0 ELSE 1 END,
        CASE WHEN fecha >= CURDATE() THEN fecha END ASC,
        CASE WHEN fecha < CURDATE() THEN fecha END DESC,
        e.id DESC");
$todosEventos = $todosEventosStmt->fetchAll();
$fotosGaleriaStmt = $pdo->prepare('SELECT id, imagen, titulo, descripcion, tipo_evento FROM galeria_eventos WHERE usuario_id = :organizador ORDER BY fecha_creacion DESC, id DESC');
$fotosGaleriaStmt->execute([':organizador' => (int) $usuario['id']]);
$fotosGaleria = $fotosGaleriaStmt->fetchAll();
$consultasStmt = $pdo->prepare('SELECT p.id, p.pregunta, p.respuesta, p.fecha_creacion, e.titulo, e.id AS evento_id, u.nombre_completo FROM preguntas_eventos p INNER JOIN eventos e ON e.slug = p.evento_slug INNER JOIN usuarios u ON u.id = p.usuario_id WHERE e.organizador_id = :organizador OR e.organizador_id = 0 ORDER BY p.fecha_creacion DESC');
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
                    <h2 id="event-questions-title">Consultas de eventos</h2>
                    <p>Responde las preguntas de tus eventos y de los que todavía no tienen organizador asignado.</p>
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

        <section class="admin-section" id="mis-eventos" aria-labelledby="organizer-events-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Agenda propia</p>
                    <h2 id="organizer-events-title">Todos los eventos</h2>
                    <p>Puedes editar tus eventos; los de otros organizadores son solo de consulta.</p>
                </div>
                <div class="admin-form__actions">
                    <span class="admin-count"><?= count($todosEventos) ?> eventos</span>
                    <a class="admin-button" href="eventos.php">Crear evento</a>
                </div>
            </div>
            <?php if (!$todosEventos): ?>
                <p class="admin-empty">Todavía no tienes eventos. Puedes crear uno desde la sección Eventos.</p>
            <?php else: ?>
                <div class="admin-event-list" id="admin-event-list">
                    <?php foreach ($todosEventos as $indiceEvento => $evento):
                        $fechaEvento = strtotime($evento['fecha']);
                        $esFuturo = $fechaEvento !== false && $fechaEvento >= strtotime('today'); ?>
                        <article class="admin-event" data-event-index="<?= (int) $indiceEvento ?>">
                            <div class="admin-event__summary">
                                <div class="admin-event__date">
                                    <span><?= $fechaEvento ? htmlspecialchars(date('d', $fechaEvento), ENT_QUOTES, 'UTF-8') : '—' ?></span>
                                    <small><?= $fechaEvento ? htmlspecialchars(date('M Y', $fechaEvento), ENT_QUOTES, 'UTF-8') : 'Sin fecha' ?></small>
                                </div>
                                <div class="admin-event__info">
                                    <div class="admin-event__badges">
                                        <span class="admin-status <?= $esFuturo ? 'admin-status--upcoming' : 'admin-status--past' ?>"><?= $esFuturo ? 'Próximo' : 'Finalizado' ?></span>
                                        <span class="admin-category"><?= htmlspecialchars($evento['categoria'] ?: 'Sin categoría', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <h3><?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p><?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($evento['organizador_nombre'] ?: 'Sin organizador', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                                <a class="admin-text-link admin-event__open" href="evento.php?id=<?= (int) $evento['id'] ?>">Ver evento</a>
                            </div>
                            <details class="admin-event__editor">
                                <summary>Editar evento</summary>
                                <form class="admin-form" method="post" enctype="multipart/form-data" action="crear-evento.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                                    <label class="admin-form__wide">Descripción
                                        <textarea name="descripcion" maxlength="5000" required><?= htmlspecialchars($evento['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </label>
                                    <label>Título<input name="titulo" maxlength="255" required value="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"></label>
                                    <label>Fecha<input type="date" name="fecha" required value="<?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8') ?>"></label>
                                    <label>Ubicación<input name="ubicacion" maxlength="255" required value="<?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?>"></label>
                                    <label>Categoría<input name="categoria" maxlength="100" required value="<?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>"></label>
                                    <label>Tipo de entrada
                                        <select name="tipoEntrada">
                                            <option value="Gratuito" <?= $evento['tipo_entrada'] === 'Gratuito' ? 'selected' : '' ?>>Gratuito</option>
                                            <option value="De Pago" <?= $evento['tipo_entrada'] === 'De Pago' ? 'selected' : '' ?>>De Pago</option>
                                        </select>
                                    </label>
                                    <label>Precio<input type="number" min="0" step="0.01" name="precio" value="<?= htmlspecialchars((string) ($evento['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
                                    <label class="admin-form__wide admin-event__image-field">Foto del evento
                                        <img class="admin-event__image-preview" src="<?= htmlspecialchars(sigtur_url_imagen_subida((string) ($evento['imagen_portada'] ?? ''), 'eventos', '../img/porco.avif'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto actual de <?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                        <input class="admin-event__image-input" type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
                                        <span class="admin-field-hint">Si no seleccionas otra foto, se conserva la actual.</span>
                                    </label>
                                    <div class="admin-form__wide admin-form__actions">
                                        <button class="admin-button" type="submit">Guardar cambios</button>
                                        <a class="admin-text-link" href="evento.php?id=<?= (int) $evento['id'] ?>">Abrir evento</a>
                                    </div>
                                </form>
                                <?php if ((int) $evento['organizador_id'] === (int) $usuario['id']): ?>
                                <form method="post" action="crear-evento.php" onsubmit="return confirm('¿Eliminar este evento? Esta acción no se puede deshacer.')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                                    <button class="admin-button admin-button--danger" type="submit">Eliminar evento</button>
                                </form>
                                <?php endif; ?>
                            </details>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($todosEventos) > 4): ?>
                    <button class="admin-button admin-events-toggle" type="button" data-admin-events-toggle aria-expanded="false" aria-controls="admin-event-list">Ver más eventos</button>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="admin-section" id="mis-fotos" aria-labelledby="organizer-gallery-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Fotos publicadas</p>
                    <h2 id="organizer-gallery-title">Mis fotos de la galería</h2>
                    <p>Edita la información o reemplaza las fotos que subiste.</p>
                </div>
                <span class="admin-count"><?= count($fotosGaleria) ?> fotos</span>
            </div>
            <?php if (isset($_GET['galeria']) && $_GET['galeria'] === 'actualizada'): ?>
                <p class="admin-notice" role="status">La foto y su información se actualizaron correctamente.</p>
            <?php endif; ?>
            <?php if (!$fotosGaleria): ?>
                <p class="admin-empty">Todavía no subiste fotos a la galería de eventos.</p>
            <?php else: ?>
                <div class="admin-event-list">
                    <?php foreach ($fotosGaleria as $foto): ?>
                        <article class="admin-event">
                            <details class="admin-event__editor">
                                <summary><?= htmlspecialchars($foto['titulo'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($foto['tipo_evento'], ENT_QUOTES, 'UTF-8') ?></summary>
                                <form class="admin-form" method="post" enctype="multipart/form-data" action="editar-foto-galeria.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="foto_id" value="<?= (int) $foto['id'] ?>">
                                    <label>Título
                                        <input name="titulo" maxlength="160" required value="<?= htmlspecialchars($foto['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Tipo de evento
                                        <select name="tipo_evento" required>
                                            <?php foreach (['Competencias', 'Carreras', 'Deportivo', 'Discotecas', 'Cultural', 'Feria', 'Otro'] as $tipoFoto): ?>
                                                <option value="<?= htmlspecialchars($tipoFoto, ENT_QUOTES, 'UTF-8') ?>" <?= $foto['tipo_evento'] === $tipoFoto ? 'selected' : '' ?>><?= htmlspecialchars($tipoFoto, ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label class="admin-form__wide">Descripción
                                        <textarea name="descripcion" maxlength="2000" required><?= htmlspecialchars($foto['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </label>
                                    <label class="admin-form__wide admin-event__image-field">Foto actual
                                        <img class="admin-event__image-preview" src="<?= htmlspecialchars(sigtur_url_imagen_subida((string) $foto['imagen'], 'galeria-eventos'), ENT_QUOTES, 'UTF-8') ?>" alt="Foto actual: <?= htmlspecialchars($foto['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                        <input class="admin-event__image-input" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
                                        <span class="admin-field-hint">Selecciona una imagen JPG, PNG o WEBP para reemplazarla; si no seleccionas otra, se conserva la actual.</span>
                                    </label>
                                    <div class="admin-form__wide admin-form__actions">
                                        <button class="admin-button" type="submit">Guardar cambios</button>
                                    </div>
                                </form>
                            </details>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <script src="../js/panel-admin.js" defer></script>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
