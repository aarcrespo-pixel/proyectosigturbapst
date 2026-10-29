<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$eventoActualizadoId = filter_var($_GET['evento_actualizado'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$destacadoActualizado = filter_var($_GET['destacado_actualizado'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$accionDestacado = (string) ($_GET['accion_destacado'] ?? '');
$eventosDestacadosIds = array_fill_keys(array_map('intval', $pdo->query('SELECT evento_id FROM eventos_destacados')->fetchAll(PDO::FETCH_COLUMN)), true);
$todosEventosStmt = $pdo->query("SELECT e.id, e.titulo, e.descripcion, e.fecha, e.ubicacion, e.categoria, e.tipo_entrada, e.precio, e.precio_texto, e.imagen_portada, e.es_pasado, e.organizador_id, u.nombre_completo AS organizador_nombre
    FROM eventos e
    LEFT JOIN usuarios u ON u.id = e.organizador_id
    ORDER BY CASE WHEN fecha >= CURDATE() THEN 0 ELSE 1 END,
        CASE WHEN fecha >= CURDATE() THEN fecha END ASC,
        CASE WHEN fecha < CURDATE() THEN fecha END DESC,
        e.id DESC");
$todosEventos = $todosEventosStmt->fetchAll();
$consultasPendientesStmt = $pdo->prepare('SELECT COUNT(*)
    FROM preguntas_eventos p
    WHERE p.respuesta IS NULL OR p.respuesta = \'\'');
$consultasPendientesStmt->execute();
$consultasPendientes = (int) $consultasPendientesStmt->fetchColumn();
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
                <a class="admin-nav__link" href="consultas-eventos-organizador.php">
                    Consultas de los eventos
                    <?php if ($consultasPendientes > 0): ?>
                        <span class="admin-count admin-count--pending-nav"><?= $consultasPendientes ?> pendientes</span>
                    <?php endif; ?>
                </a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <?php if ($eventoActualizadoId): ?>
            <p class="admin-notice" role="status">Los cambios del evento se guardaron correctamente.</p>
        <?php endif; ?>
        <?php if ($destacadoActualizado && in_array($accionDestacado, ['agregar', 'quitar'], true)): ?>
            <p class="admin-notice" role="status">El evento se <?= $accionDestacado === 'agregar' ? 'agregó a' : 'quitó de' ?> los destacados.</p>
        <?php endif; ?>

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

        <section class="admin-section" id="mis-eventos" aria-labelledby="organizer-events-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Agenda del sitio</p>
                    <h2 id="organizer-events-title">Todos los eventos</h2>
                    <p>Edita los eventos y elige cuáles quieres mostrar como destacados en la página de Eventos.</p>
                </div>
                <div class="admin-form__actions">
                    <span class="admin-count"><?= count($todosEventos) ?> eventos</span>
                    <a class="admin-button" href="eventos.php">Crear evento</a>
                </div>
            </div>
            <?php if (!$todosEventos): ?>
                <p class="admin-empty">Todavía no hay eventos. Puedes crear uno desde la sección Eventos.</p>
            <?php else: ?>
                <div class="admin-event-list" id="admin-event-list" data-open-event-id="<?= $eventoActualizadoId ?: '' ?>">
                    <?php foreach ($todosEventos as $indiceEvento => $evento):
                        $fechaEvento = strtotime($evento['fecha']);
                        $esFuturo = (int) $evento['es_pasado'] === 0 && $fechaEvento !== false && $fechaEvento >= strtotime('today'); ?>
                        <article class="admin-event" id="evento-<?= (int) $evento['id'] ?>" data-event-index="<?= (int) $indiceEvento ?>">
                            <?php $eventoEditorAbierto = (int) $eventoActualizadoId === (int) $evento['id']; ?>
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
                                <div class="admin-event__actions">
                                    <a class="admin-text-link admin-event__open" href="evento.php?id=<?= (int) $evento['id'] ?>">Ver evento</a>
                                    <button class="admin-button admin-event__edit-toggle" type="button" aria-expanded="<?= $eventoEditorAbierto ? 'true' : 'false' ?>" aria-controls="editor-evento-<?= (int) $evento['id'] ?>" data-event-editor-toggle>Editar</button>
                                    <?php if ($esFuturo || isset($eventosDestacadosIds[(int) $evento['id']])): ?>
                                        <form class="admin-event__feature-form" method="post" action="actualizar-evento-destacado.php">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                                            <input type="hidden" name="accion" value="<?= isset($eventosDestacadosIds[(int) $evento['id']]) ? 'quitar' : 'agregar' ?>">
                                            <button class="admin-button <?= isset($eventosDestacadosIds[(int) $evento['id']]) ? 'admin-button--secondary' : '' ?>" type="submit">
                                                <?= isset($eventosDestacadosIds[(int) $evento['id']]) ? 'Quitar destacado' : 'Agregar a destacados' ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="admin-event__editor" id="editor-evento-<?= (int) $evento['id'] ?>"<?= $eventoEditorAbierto ? '' : ' hidden' ?>>
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
                                    <label>Precio o valor<input type="text" maxlength="120" name="precio" value="<?= htmlspecialchars((string) ($evento['precio_texto'] ?? $evento['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
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
                            </div>
                            <form class="admin-event__delete-form" method="post" action="crear-evento.php" onsubmit="return confirm('¿Eliminar este evento? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                                <button class="admin-button admin-button--danger" type="submit">Eliminar evento</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($todosEventos) > 4): ?>
                    <button class="admin-button admin-events-toggle" type="button" data-admin-events-toggle aria-expanded="false" aria-controls="admin-event-list">Ver más eventos</button>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>
    <script src="../js/tema.js" defer></script>
    <script src="../js/panel-admin.js" defer></script>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
