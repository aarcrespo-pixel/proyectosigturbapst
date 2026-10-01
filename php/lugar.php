<?php
require_once __DIR__ . '/conexion.php';

/* El slug llega por GET, pero solo aceptamos caracteres de URL conocidos para
   impedir que el parámetro se use como fragmento SQL o ruta de archivo. */
$identificador = trim((string) ($_GET['id'] ?? $_GET['slug'] ?? ''));
$slug = strtolower(preg_replace('/[^a-z0-9-]/', '', $identificador));
if ($identificador === '') {
    http_response_code(400);
    exit('Lugar no especificado.');
}

$consultaLugar = ctype_digit($identificador)
    ? $pdo->prepare('SELECT * FROM lugares WHERE id = :id LIMIT 1')
    : $pdo->prepare('SELECT * FROM lugares WHERE slug = :slug LIMIT 1');
$consultaLugar->execute(ctype_digit($identificador) ? [':id' => (int) $identificador] : [':slug' => $slug]);
$lugar = $consultaLugar->fetch();
if (!$lugar) {
    http_response_code(404);
    exit('Lugar turístico no encontrado.');
}

/* La relación usa lugar_id; el límite mantiene la sección final ligera y el
   orden por fecha ofrece al visitante los próximos eventos primero. */
$consultaEventos = $pdo->prepare('SELECT id, titulo, fecha, ubicacion, categoria, imagen_portada FROM eventos WHERE lugar_id = :lugar_id AND es_pasado = 0 ORDER BY fecha ASC LIMIT 3');
$consultaEventos->execute([':lugar_id' => (int) $lugar['id']]);
$eventosRelacionados = $consultaEventos->fetchAll();
$atractivos = array_values(array_filter(array_map('trim', explode('|', $lugar['atractivos']))));
$imagenLugar = htmlspecialchars($lugar['imagen'], ENT_QUOTES, 'UTF-8');
$nombreLugar = htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombreLugar ?> | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/lugar.css">
</head>
<body class="lugar-page">
<?php $navBase = ''; $activePage = 'lugares'; include __DIR__ . '/nav.php'; ?>

<main>
    <section class="lugar-hero" style="--lugar-image: url('<?= $imagenLugar ?>')">
        <div class="lugar-hero__overlay"></div>
        <div class="lugar-hero__content">
            <span class="lugar-badge" data-i18n="placeCategoryLabel"> <?= htmlspecialchars($lugar['categoria'], ENT_QUOTES, 'UTF-8') ?> </span>
            <h1 class="lugar-hero-titulo"><?= $nombreLugar ?></h1>
            <p class="lugar-location">⌖ <?= htmlspecialchars($lugar['direccion'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </section>

    <div class="lugar-content">
        <section class="lugar-copy">
            <article class="lugar-module">
                <p class="lugar-kicker" data-i18n="placeAboutKicker">Descubrí Salto</p>
                <h2 class="lugar-seccion-titulo" data-i18n="placeAboutTitle">Sobre este lugar</h2>
                <p class="lugar-descripcion"><?= htmlspecialchars($lugar['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
            <article class="lugar-module">
                <h2 class="lugar-seccion-titulo" data-i18n="placeHistoryTitle">Historia y Patrimonio</h2>
                <p class="lugar-historia-texto"><?= htmlspecialchars($lugar['historia'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
            <article class="lugar-module">
                <h2 class="lugar-seccion-titulo" data-i18n="placeThingsTitle">Qué hacer y Atractivos</h2>
                <div class="lugar-attractions">
                    <?php foreach ($atractivos as $atractivo): ?><div class="lugar-attraction">✦ <span><?= htmlspecialchars($atractivo, ENT_QUOTES, 'UTF-8') ?></span></div><?php endforeach; ?>
                </div>
            </article>
        </section>

        <aside class="lugar-useful">
            <h2 class="lugar-seccion-titulo" data-i18n="placeUsefulTitle">Información Útil y Ubicación</h2>
            <div class="useful-item"><strong data-i18n="placeHoursLabel">Horarios</strong><span><?= htmlspecialchars($lugar['horarios'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <div class="useful-item"><strong data-i18n="placeRecommendationsLabel">Recomendaciones</strong><span><?= htmlspecialchars($lugar['recomendaciones'], ENT_QUOTES, 'UTF-8') ?></span></div>
            <a class="map-link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($lugar['direccion']) ?>" data-i18n="placeMapButton">Abrir ubicación en Maps</a>
        </aside>
    </div>

    <section class="lugar-events">
        <div class="lugar-events__heading"><p class="lugar-kicker" data-i18n="placeEventsKicker">Agenda local</p><h2 class="lugar-seccion-titulo" data-i18n="placeEventsTitle">Próximos Eventos en este Lugar</h2></div>
        <?php if (!$eventosRelacionados): ?>
            <p class="empty-events" data-i18n="placeNoEvents">No hay eventos próximos en este lugar.</p>
        <?php else: ?>
            <div class="lugar-events-grid">
                <?php foreach ($eventosRelacionados as $evento): ?>
                    <a href="evento.php?id=<?= (int) $evento['id'] ?>" class="card-evento-sugerido-link">
                        <div class="card-evento-sugerido">
                            <div class="card-img-container"><img src="<?= htmlspecialchars($evento['imagen_portada'] ?: '../img/porco.avif', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"></div>
                            <div class="lugar-event-card__body"><span class="event-category"><?= htmlspecialchars($evento['categoria'], ENT_QUOTES, 'UTF-8') ?></span><h3><?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><span class="event-date">📅 <?= htmlspecialchars(date('d/m/Y', strtotime($evento['fecha'])), ENT_QUOTES, 'UTF-8') ?></span><span class="event-more" data-i18n="placeViewMore">Ver Más</span></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<script src="../js/script.js" defer></script>
</body>
</html>