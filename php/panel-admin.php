<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$usuario = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$eventos = $pdo->query("SELECT id, titulo, descripcion, fecha, ubicacion, categoria, tipo_entrada, precio, imagen_portada, organizador_id
    FROM eventos
    ORDER BY CASE WHEN fecha >= CURDATE() THEN 0 ELSE 1 END,
        CASE WHEN fecha >= CURDATE() THEN fecha END ASC,
        CASE WHEN fecha < CURDATE() THEN fecha END DESC,
        id DESC")->fetchAll();
$consultas = $pdo->query('SELECT p.id, p.pregunta, p.respuesta, p.fecha_creacion, e.titulo, e.id AS evento_id, u.nombre_completo FROM preguntas_eventos p INNER JOIN eventos e ON e.slug = p.evento_slug INNER JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.fecha_creacion DESC')->fetchAll();
$preguntasFaq = $pdo->query("SELECT p.pregunta, p.email, p.estado, p.fecha_creacion, u.nombre_completo, u.email AS email_cuenta
    FROM preguntas_frecuentes p
    LEFT JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY CASE WHEN p.estado = 'pendiente' THEN 0 ELSE 1 END, p.fecha_creacion DESC
    LIMIT 100")->fetchAll();
$organizadores = $pdo->query("SELECT id, nombre_completo FROM usuarios WHERE rol = 'organizador' ORDER BY nombre_completo")->fetchAll();
$eventosFuturos = count(array_filter($eventos, static fn($evento) => strtotime($evento['fecha']) >= strtotime('today')));
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
<body class="admin-panel-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Panel de administración</h1>
                <p>Gestioná los contenidos y mantené organizadas las consultas de la comunidad.</p>
            </div>
            <nav class="admin-nav" aria-label="Accesos de administración">
                <a class="admin-nav__link" href="admin-usuarios.php">Gestionar usuarios</a>
                <a class="admin-nav__link" href="admin-contenido.php">Gestionar contenido</a>
                <a class="admin-nav__link" href="mensajes.php">Gestionar mensajes</a>
                <a class="admin-nav__link" href="eventos.php">Ver sitio</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <section class="admin-overview" aria-label="Resumen de administración">
            <article class="admin-stat">
                <span class="admin-stat__label">Eventos publicados</span>
                <strong><?= count($eventos) ?></strong>
            </article>
            <article class="admin-stat">
                <span class="admin-stat__label">Próximos eventos</span>
                <strong><?= $eventosFuturos ?></strong>
            </article>
            <article class="admin-stat">
                <span class="admin-stat__label">Preguntas recibidas</span>
                <strong><?= count($preguntasFaq) ?></strong>
            </article>
        </section>

        <section class="admin-section admin-notice" aria-labelledby="notice-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Comunicación</p>
                    <h2 id="notice-title">Enviar aviso general</h2>
                    <p>El mensaje se enviará a los turistas registrados.</p>
                </div>
            </div>
            <form class="admin-form" method="post" action="api/notificaciones.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="accion" value="crear">
                <input type="hidden" name="destino" value="todos">
                <label>Título
                    <input name="titulo" maxlength="120" required placeholder="Nuevo evento o aviso">
                </label>
                <label>Tipo
                    <select name="tipo">
                        <option value="general">General</option>
                        <option value="evento">Evento</option>
                        <option value="aviso">Aviso</option>
                    </select>
                </label>
                <label class="admin-form__wide">Mensaje
                    <textarea name="mensaje" maxlength="500" required placeholder="Escribí el aviso para los turistas..."></textarea>
                </label>
                <div class="admin-form__wide">
                    <button class="admin-button" type="submit">Enviar aviso</button>
                </div>
            </form>
        </section>

        <section class="admin-section" id="preguntas-soporte" aria-labelledby="support-questions-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Bandeja de entrada</p>
                    <h2 id="support-questions-title">Preguntas recibidas desde Soporte</h2>
                    <p>Las preguntas pendientes aparecen primero.</p>
                </div>
                <a class="admin-text-link" href="mensajes.php">Ver todos los mensajes</a>
            </div>
            <?php if (!$preguntasFaq): ?>
                <p class="admin-empty">Todavía no hay preguntas recibidas.</p>
            <?php else: ?>
                <div class="admin-question-list">
                    <?php foreach ($preguntasFaq as $preguntaFaq): ?>
                        <article class="admin-question">
                            <div class="admin-question__top">
                                <div>
                                    <strong><?= htmlspecialchars($preguntaFaq['nombre_completo'] ?: 'Visitante', ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span><?= htmlspecialchars($preguntaFaq['email'] ?: $preguntaFaq['email_cuenta'] ?: 'Sin correo', ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <span class="admin-status"><?= htmlspecialchars($preguntaFaq['estado'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <p><?= nl2br(htmlspecialchars($preguntaFaq['pregunta'], ENT_QUOTES, 'UTF-8')) ?></p>
                            <time><?= htmlspecialchars($preguntaFaq['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></time>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="admin-section" id="eventos" aria-labelledby="events-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Contenido del sitio</p>
                    <h2 id="events-title">Eventos</h2>
                    <p>Los próximos eventos aparecen primero. Abrí “Editar evento” para modificar sus datos.</p>
                </div>
                <span class="admin-count"><?= count($eventos) ?> eventos</span>
            </div>
            <?php if (!$eventos): ?>
                <p class="admin-empty">Todavía no hay eventos cargados.</p>
            <?php else: ?>
                <div class="admin-event-list" id="admin-event-list">
                    <?php $grupoEventosActual = null; $indiceEvento = 0; ?>
                    <?php foreach ($eventos as $evento):
                        $fechaEvento = strtotime($evento['fecha']);
                        $esFuturo = $fechaEvento !== false && $fechaEvento >= strtotime('today');
                        if ($grupoEventosActual !== $esFuturo):
                            $grupoEventosActual = $esFuturo;
                            $cantidadGrupo = count(array_filter($eventos, static function ($eventoGrupo) use ($esFuturo) {
                                $fechaGrupo = strtotime($eventoGrupo['fecha']);
                                return $fechaGrupo !== false && ($fechaGrupo >= strtotime('today')) === $esFuturo;
                            }));
                    ?>
                            <h3 class="admin-event-group-title"><?= $esFuturo ? 'Próximos eventos' : 'Eventos finalizados' ?><span><?= $cantidadGrupo ?></span></h3>
                    <?php endif; ?>
                        <article class="admin-event" data-event-index="<?= $indiceEvento++ ?>">
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
                                    <p><?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?> <span>·</span> Organizador #<?= (int) $evento['organizador_id'] ?></p>
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
                                    <label>Título
                                        <input name="titulo" maxlength="255" required value="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Fecha
                                        <input type="date" name="fecha" required value="<?= htmlspecialchars($evento['fecha'], ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Ubicación
                                        <input name="ubicacion" maxlength="255" required value="<?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Categoría
                                        <input name="categoria" maxlength="100" required value="<?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Tipo de entrada
                                        <select name="tipoEntrada">
                                            <option value="Gratuito" <?= $evento['tipo_entrada'] === 'Gratuito' ? 'selected' : '' ?>>Gratuito</option>
                                            <option value="De Pago" <?= $evento['tipo_entrada'] === 'De Pago' ? 'selected' : '' ?>>De Pago</option>
                                        </select>
                                    </label>
                                    <label>Precio
                                        <input type="number" min="0" step="0.01" name="precio" value="<?= htmlspecialchars((string) ($evento['precio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                    </label>
                                    <label>Organizador
                                        <select name="organizador_id">
                                            <option value="0" <?= (int) $evento['organizador_id'] === 0 ? 'selected' : '' ?>>Sin asignar</option>
                                            <?php foreach ($organizadores as $organizador): ?>
                                                <option value="<?= (int) $organizador['id'] ?>" <?= (int) $evento['organizador_id'] === (int) $organizador['id'] ? 'selected' : '' ?>><?= htmlspecialchars($organizador['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label class="admin-form__wide admin-event__image-field">Foto del evento
                                        <img
                                            class="admin-event__image-preview"
                                            src="<?= htmlspecialchars($evento['imagen_portada'] ?: '../img/porco.avif', ENT_QUOTES, 'UTF-8') ?>"
                                            alt="Foto actual de <?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                            loading="lazy"
                                        >
                                        <input class="admin-event__image-input" type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
                                        <span class="admin-field-hint">Seleccioná una nueva foto JPG, PNG o WEBP. Si no elegís otra, se conserva la actual.</span>
                                    </label>
                                    <div class="admin-form__wide admin-form__actions">
                                        <button class="admin-button" type="submit">Guardar cambios</button>
                                        <button class="admin-button admin-button--danger" type="submit" form="delete-event-<?= (int) $evento['id'] ?>" onclick="return confirm('¿Eliminar este evento? Esta acción no se puede deshacer.')">Eliminar evento</button>
                                    </div>
                                </form>
                                <form id="delete-event-<?= (int) $evento['id'] ?>" method="post" action="crear-evento.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="evento_id" value="<?= (int) $evento['id'] ?>">
                                </form>
                            </details>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if (count($eventos) > 4): ?>
                    <button class="admin-button admin-events-toggle" type="button" data-admin-events-toggle aria-expanded="false" aria-controls="admin-event-list">Ver más eventos</button>
                <?php endif; ?>
            <?php endif; ?>
        </section>

        <section class="admin-section" id="consultas" aria-labelledby="event-questions-title">
            <div class="admin-section__heading">
                <div>
                    <p class="admin-eyebrow">Atención a visitantes</p>
                    <h2 id="event-questions-title">Consultas sobre eventos</h2>
                    <p>Respuestas a preguntas realizadas desde las páginas de eventos.</p>
                </div>
            </div>
            <?php if (!$consultas): ?>
                <p class="admin-empty">Todavía no hay consultas de eventos.</p>
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
    </main>
    <script src="../js/panel-admin.js" defer></script>
</body>
</html>
