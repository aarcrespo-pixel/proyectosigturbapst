<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/eventos_mapa.php';

$usuarioActual = sigtur_usuario_actual($pdo);
$eventosMapa = sigtur_eventos_visibles_en_mapa($pdo);
$puedeAgregarFotosLugares = $usuarioActual && $usuarioActual['rol'] === 'organizador';
$fotosLugaresStmt = $pdo->prepare("SELECT imagen, titulo FROM galeria_eventos WHERE tipo_evento = 'Lugares' ORDER BY fecha_creacion DESC, id DESC");
$fotosLugaresStmt->execute();
$fotosLugares = $fotosLugaresStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lugares | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css/lugares.css">
</head>

<body>
    <?php
    $headerCurrentPage = 'lugares';
    $headerActivePage = 'lugares';
    include __DIR__ . '/header.php';
    ?>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="../index.php" class="bottom-nav-item">
            <img src="../img/logoazul.png" class="bottom-nav-icon" alt="Inicio" data-i18n-alt="navHome">
            <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
        </a>
        <a href="eventos.php" class="bottom-nav-item active">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos" data-i18n-alt="navEvents">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo" data-i18n-alt="navTourism">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item">
            <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares" data-i18n-alt="navPlaces">
            <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
        </a>
        <a href="gastronomia.php" class="bottom-nav-item">
            <img src="../img/gastronomia.png" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label">Gastronomía</span>
        </a>
    </nav>

    <main class="pagina-lugares">
        <section class="hero-lugares">
            <div class="hero-texto">
                <span data-i18n="placesHeroLabel">Lugares históricos</span>
                <h1 data-i18n="placesHeroTitle">LUGARES</h1>
                <p data-i18n="placesHeroText">Descubre los lugares más emblemáticos de Salto, llenos de historia y cultura.</p>
                <a class="hero-gallery-link" href="#galeria-historica" data-i18n="placesGalleryButton">Ver todas las galerías</a>
            </div>
        </section>

        <section class="seccion-lugares" id="lugares-emblematicos">
            <h2 data-i18n="placesSectionEmblematicTitle">Lugares emblemáticos</h2>
            <div class="destinos-grid">
                <article class="tarjeta-lugar">
                    <img src="../img/Plaza_33.webp" alt="Costanera Sur" data-i18n-alt="placesEmblematicAlt" data-i18n-index="0">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="0">Plaza Treinta y Tres Orientales</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="0">Paseo tranquilo junto al río con espacios verdes, miradores y zonas para descansar al aire libre.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar">
                    <img src="../img/Plaza_artigas.webp" alt="Plaza histórica" data-i18n-alt="placesEmblematicAlt" data-i18n-index="1">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="1">Plaza Artigas</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="1">Plaza emblemática del centro con arquitectura tradicional, cafeterías y actividad cultural permanente.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar">
                    <img src="../img/solari.jfif" alt="Parque Benito Solari" data-i18n-alt="placesEmblematicAlt" data-i18n-index="2">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="2">Parque Benito Solari</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="2">Gran área verde perfecta para caminatas familiares, actividades deportivas y eventos al aire libre.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-lugares">
            <h2 data-i18n="placesSectionCategoriesTitle">Categorías de lugares</h2>
            <div class="intereses-grid">
                <article class="tarjeta-interes">
                    <img src="../img/gastronomia.jpg" alt="Cafés y gastronomía" data-i18n-alt="placesCategoryAlt" data-i18n-index="0">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesCategoryTitles" data-i18n-index="0">Gastronomía</h3>
                        <p data-i18n="placesCategoryDescriptions" data-i18n-index="0">Selección de cafés, parrillas y locales con sabores locales que marcan tendencia en la ciudad.</p>
                    </div>
                </article>
                <article class="tarjeta-interes">
                    <img src="../img/cultura.jpg" alt="Arte urbano" data-i18n-alt="placesCategoryAlt" data-i18n-index="1">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesCategoryTitles" data-i18n-index="1">Cultura</h3>
                        <p data-i18n="placesCategoryDescriptions" data-i18n-index="1">Galerías, murales y espacios históricos que muestran la identidad y patrimonio de Salto.</p>
                    </div>
                </article>
                <article class="tarjeta-interes">
                    <img src="../img/Naturaleza.webp" alt="Espacios naturales" data-i18n-alt="placesCategoryAlt" data-i18n-index="2">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesCategoryTitles" data-i18n-index="2">Naturaleza</h3>
                        <p data-i18n="placesCategoryDescriptions" data-i18n-index="2">Áreas verdes, parques y paseos naturales para los amantes del aire libre y la calma.</p>
                    </div>
                </article>
                <article class="tarjeta-interes">
                    <img src="../img/experiencias.webp" alt="Atracciones urbanas" data-i18n-alt="placesCategoryAlt" data-i18n-index="3">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesCategoryTitles" data-i18n-index="3">Experiencias</h3>
                        <p data-i18n="placesCategoryDescriptions" data-i18n-index="3">Actividades interactivas, mercados y puntos de encuentro que enriquecen cada visita.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-lugares">
            <h2 data-i18n="placesSectionLeisureTitle">Lugares para Distraerse</h2>
            <div class="destinos-grid">
                <article class="tarjeta-lugar">
                    <img src="https://www.salto.gub.uy/sites/default/files/styles/16_9_xl_scale/public/2024-08/0505190000.JPG.webp?itok=67uThcCX" alt="Plaza Roosvelt" data-i18n-alt="placesLeisureAlt" data-i18n-index="0">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="0">Plaza Roosvelt</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="0">Plaza tranquila en la costa para tomar mates con amigos u familiares.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar">
                    <img src="../img/Costanera_Norte.jpeg" alt="Costanera Norte" data-i18n-alt="placesLeisureAlt" data-i18n-index="1">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="1">Costanera Norte</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="1">Área costera con vistas al río, espacios verdes y zonas para actividades al aire libre.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar">
                    <img src="https://dzpqda01g2dxp.cloudfront.net/images/attractions/4069/photos/4069_0.jpg" alt="Muelle Negro" data-i18n-alt="placesLeisureAlt" data-i18n-index="2">
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="2">Muelle Negro</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="2">Zona con vista al río para relajarse y pasar el rato con amigos.</p>
                    </div>
                </article>
            </div>
        </section>


        <section class="seccion-lugares" id="galeria-historica">
            <div class="gallery-section-heading">
                <div>
                    <span class="gallery-section-kicker">Memoria e identidad</span>
                    <h2 data-i18n="placesSectionGalleryTitle">Galería Histórica</h2>
                    <p data-i18n="placesGalleryIntro">Un recorrido visual por los espacios y las tradiciones que cuentan la historia de Salto.</p>
                </div>
                <span class="gallery-section-count" aria-label="<?= count($fotosLugares) + 5 ?> fotos destacadas"><?= count($fotosLugares) + 5 ?> lugares</span>
            </div>
            <?php if (isset($_GET['fotos_agregadas'])): ?>
                <p class="lugares-galeria-feedback" role="status">
                    Se agregaron <?= max(1, (int) $_GET['fotos_agregadas']) ?> foto<?= (int) $_GET['fotos_agregadas'] === 1 ? '' : 's' ?> a la galería.
                    <?php if (isset($_GET['algunas_fotos_fallaron'])): ?>Algunas fotos no se pudieron subir.<?php endif; ?>
                </p>
            <?php endif; ?>
            <div class="galeria-lugares gallery-photo-grid">
                <article class="tarjeta-galeria"><img src="../img/Plaza_33.webp" alt="Plaza Treinta y Tres y su entorno histórico" loading="lazy" decoding="async" data-i18n-alt="placesGalleryAlt" data-i18n-index="0"><div class="tarjeta-contenido"><span class="gallery-photo-tag">Patrimonio</span><h3 data-i18n="placesGalleryTitles" data-i18n-index="0">Plaza Treinta y Tres</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Plaza_artigas.webp" alt="Monumentos de la Plaza Artigas" loading="lazy" decoding="async" data-i18n-alt="placesGalleryAlt" data-i18n-index="1"><div class="tarjeta-contenido"><span class="gallery-photo-tag">Historia</span><h3 data-i18n="placesGalleryTitles" data-i18n-index="1">Plaza Artigas</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Cine_Sarandi.jpg" alt="Calle céntrica junto al Cine Sarandí" loading="lazy" decoding="async" data-i18n-alt="placesGalleryAlt" data-i18n-index="2"><div class="tarjeta-contenido"><span class="gallery-photo-tag">Centro</span><h3 data-i18n="placesGalleryTitles" data-i18n-index="2">Centro histórico</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/cultura.jpg" alt="Expresión de la cultura popular salteña" loading="lazy" decoding="async" data-i18n-alt="placesGalleryAlt" data-i18n-index="3"><div class="tarjeta-contenido"><span class="gallery-photo-tag">Tradiciones</span><h3 data-i18n="placesGalleryTitles" data-i18n-index="3">Cultura salteña</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Costanera_Norte.jpeg" alt="Paseo costero de Salto" loading="lazy" decoding="async" data-i18n-alt="placesGalleryAlt" data-i18n-index="4"><div class="tarjeta-contenido"><span class="gallery-photo-tag">Paisaje urbano</span><h3 data-i18n="placesGalleryTitles" data-i18n-index="4">Costanera de Salto</h3></div></article>
                <?php foreach ($fotosLugares as $fotoLugar): ?>
                    <?php $fotoLugarUrl = sigtur_url_imagen_subida((string) $fotoLugar['imagen'], 'galeria-eventos', '../img/porco.avif'); ?>
                    <article class="tarjeta-galeria">
                        <img src="<?= htmlspecialchars($fotoLugarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($fotoLugar['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        <div class="tarjeta-contenido"><h3><?= htmlspecialchars($fotoLugar['titulo'], ENT_QUOTES, 'UTF-8') ?></h3></div>
                    </article>
                <?php endforeach; ?>
                <?php if ($puedeAgregarFotosLugares): ?>
                    <form class="lugares-galeria-add" method="post" action="api/agregar_foto_galeria_publica.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tipo_galeria" value="Lugares">
                        <input class="lugares-galeria-add__input" id="fotos-lugares" type="file" name="fotos[]" accept="image/jpeg,image/png,image/webp" multiple required>
                        <button class="lugares-galeria-add__button" type="button" aria-label="Agregar fotos a la galería histórica" title="Agregar fotos">+</button>
                    </form>
                <?php endif; ?>
            </div>
        </section>


        <section class="seccion-lugares mapa-seccion" aria-labelledby="mapa-lugares-titulo">
            <div class="mapa-encabezado">
                <span class="mapa-kicker">Ubicaciones</span>
                <h2 id="mapa-lugares-titulo">Encontrá estos lugares</h2>
                <p>Explorá en el mapa dónde queda cada punto destacado de Salto.</p>
            </div>
            <div class="mapa-contenido">
                <iframe src="https://www.google.com/maps?q=Salto%2C%20Uruguay&output=embed" loading="lazy" title="Mapa de lugares destacados en Salto"></iframe>
                <div class="mapa-lugares" id="mapa-lugares-lista">
                    <label class="mapa-lugares__buscador">
                        <span class="visually-hidden">Buscar un lugar o evento en el mapa</span>
                        <input type="search" data-map-search placeholder="Buscar un lugar o evento..." autocomplete="off">
                    </label>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20Artigas%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Plaza Artigas</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20Treinta%20y%20Tres%20Orientales%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Plaza Treinta y Tres Orientales</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Parque%20Benito%20Solari%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Parque Benito Solari</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Muelle%20Negro%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Muelle Negro</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20Roosvelt%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Plaza Roosvelt</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Costanera%20Norte%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Costanera Norte</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Cine%20Sarand%C3%AD%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Cine Sarandí</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Centro%20hist%C3%B3rico%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Centro histórico</a>
                    <?php foreach ($eventosMapa as $eventoMapa):
                        $ubicacionMapa = trim((string) $eventoMapa['ubicacion']);
                        $consultaMapa = $ubicacionMapa . ', Salto, Uruguay';
                        $urlMapaEvento = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($consultaMapa);
                    ?>
                        <a href="<?= htmlspecialchars($urlMapaEvento, ENT_QUOTES, 'UTF-8') ?>" data-map-query="<?= htmlspecialchars($consultaMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Evento: <?= htmlspecialchars((string) $eventoMapa['titulo'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($ubicacionMapa, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <p class="mapa-lugares__sin-resultados" data-map-empty hidden>No se encontraron ubicaciones.</p>
                    <button class="mapa-lugares__toggle" id="mapa-lugares-toggle" type="button" aria-expanded="false" aria-controls="mapa-lugares-lista" hidden>Ver más</button>
                </div>
            </div>
        </section>
    </main>

    <script src="../js/script.js" defer></script>
    <script src="../js/lugar-links.js?v=20261028-event-map-1" defer></script>
    <?php if ($puedeAgregarFotosLugares): ?>
        <script>
            const botonAgregarFotosLugares = document.querySelector('.lugares-galeria-add__button');
            const selectorFotosLugares = document.getElementById('fotos-lugares');
            botonAgregarFotosLugares?.addEventListener('click', () => selectorFotosLugares?.click());
            selectorFotosLugares?.addEventListener('change', () => {
                if (selectorFotosLugares.files.length > 0) selectorFotosLugares.form.submit();
            });
        </script>
    <?php endif; ?>

</html>
