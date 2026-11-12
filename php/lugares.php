<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/eventos_mapa.php';
require_once __DIR__ . '/helpers/contenido_publico.php';

sigtur_inicializar_contenido_publico($pdo);

$usuarioActual = sigtur_usuario_actual($pdo);
$eventosMapa = sigtur_eventos_visibles_en_mapa($pdo);
$lugaresCatalogoStmt = $pdo->query("SELECT slug, nombre, categoria, direccion, imagen, descripcion
    FROM lugares
    WHERE seccion_publica <> 'oculto'
        AND slug NOT IN ('plaza-treinta-y-tres-orientales', 'plaza-artigas', 'parque-benito-solari', 'costanera-norte', 'muelle-negro')
    ORDER BY nombre");
$lugaresCatalogo = $lugaresCatalogoStmt->fetchAll();
$lugaresMapa = $pdo->query("SELECT nombre, direccion
    FROM lugares
    WHERE seccion_publica <> 'oculto'
    ORDER BY nombre")->fetchAll();
foreach ($lugaresCatalogo as &$lugarCatalogo) {
    $lugarCatalogo['imagen'] = sigtur_url_imagen_subida((string) $lugarCatalogo['imagen'], 'contenido-publico', '../img/Plaza_artigas.webp');
}
unset($lugarCatalogo);
$puedeAgregarFotosLugares = $usuarioActual && $usuarioActual['rol'] === 'organizador';
$creditoPlaza33 = sigtur_atribucion_imagen_lugar('plaza-treinta-y-tres-orientales');
$creditoPlazaArtigas = sigtur_atribucion_imagen_lugar('plaza-artigas');
$creditoParqueSolari = sigtur_atribucion_imagen_lugar('parque-benito-solari');
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
    <link rel="stylesheet" href="../css/lugares.css?v=20261112-tourism-catalog-1">
    <link rel="stylesheet" href="../css/galeria-lugares.css">
    <link rel="stylesheet" href="../css/legal.css">
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
        <a href="eventos.php" class="bottom-nav-item">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos" data-i18n-alt="navEvents">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo" data-i18n-alt="navTourism">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item active" aria-current="page">
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
            </div>
        </section>

        <section class="seccion-lugares" id="lugares-emblematicos">
            <h2 data-i18n="placesSectionEmblematicTitle">Lugares emblemáticos</h2>
            <div class="destinos-grid">
                <article class="tarjeta-lugar" id="plaza-treinta-y-tres">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/turismo/plaza-treinta-y-tres.jpg" alt="Plaza Treinta y Tres Orientales de Salto">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="0">Plaza Treinta y Tres Orientales</h3>
                        <p>Plaza céntrica de Salto con espacios verdes y patrimonio urbano.</p>
                        <?php if ($creditoPlaza33): ?><small class="lugar-foto-credit">Foto: <a href="<?= htmlspecialchars($creditoPlaza33['fuente'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($creditoPlaza33['texto'], ENT_QUOTES, 'UTF-8') ?></a></small><?php endif; ?>
                    </div>
                </article>
                <article class="tarjeta-lugar" id="plaza-artigas">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/turismo/plaza-artigas.jpg" alt="Monumento a José Gervasio Artigas en la plaza céntrica de Salto">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="1">Plaza Artigas</h3>
                        <p>Plaza céntrica y espacio cívico de Salto, reconocible por su monumento a José Gervasio Artigas.</p>
                        <?php if ($creditoPlazaArtigas): ?><small class="lugar-foto-credit">Foto: <a href="<?= htmlspecialchars($creditoPlazaArtigas['fuente'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($creditoPlazaArtigas['texto'], ENT_QUOTES, 'UTF-8') ?></a></small><?php endif; ?>
                    </div>
                </article>
                <article class="tarjeta-lugar" id="parque-benito-solari">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/turismo/parque-benito-solari.jpg" alt="Acceso al Parque Benito Solari de Salto">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="2">Parque Benito Solari</h3>
                        <p>Parque urbano histórico con áreas verdes y espacios para recorrer al aire libre.</p>
                        <?php if ($creditoParqueSolari): ?><small class="lugar-foto-credit">Foto: <a href="<?= htmlspecialchars($creditoParqueSolari['fuente'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($creditoParqueSolari['texto'], ENT_QUOTES, 'UTF-8') ?></a></small><?php endif; ?>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-lugares" id="categorias-lugares">
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

        <section class="seccion-lugares" id="lugares-distraccion">
            <h2 data-i18n="placesSectionLeisureTitle">Lugares para Distraerse</h2>
            <div class="destinos-grid">
                <article class="tarjeta-lugar">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/Naturaleza.webp" alt="Costanera Sur" data-i18n-alt="placesLeisureAlt" data-i18n-index="0">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="0">Costanera Sur</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="0">Paseo junto al río Uruguay para disfrutar del paisaje y recorrer la zona costera.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar" id="costanera-norte">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/Costanera_Norte.jpeg" alt="Costanera Norte" data-i18n-alt="placesLeisureAlt" data-i18n-index="1">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="1">Costanera Norte</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="1">Área costera con vistas al río, espacios verdes y zonas para actividades al aire libre.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar">
                    <div class="tarjeta-lugar__imagen">
                        <img src="https://dzpqda01g2dxp.cloudfront.net/images/attractions/4069/photos/4069_0.jpg" alt="Muelle Negro" data-i18n-alt="placesLeisureAlt" data-i18n-index="2" onerror="this.onerror=null;this.src='../img/Costanera_Norte.jpeg';">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="2">Muelle Negro</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="2">Zona con vista al río para relajarse y pasar el rato con amigos.</p>
                    </div>
                </article>
            </div>
        </section>

        <?php if ($lugaresCatalogo): ?>
            <section class="seccion-lugares" id="catalogo-lugares">
                <h2>Más lugares para descubrir</h2>
                <div class="destinos-grid">
                    <?php foreach ($lugaresCatalogo as $lugarCatalogo): ?>
                        <article class="tarjeta-lugar">
                            <div class="tarjeta-lugar__imagen">
                                <img src="<?= htmlspecialchars((string) $lugarCatalogo['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $lugarCatalogo['nombre'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                            </div>
                            <div class="tarjeta-contenido">
                                <p><?= htmlspecialchars((string) $lugarCatalogo['categoria'], ENT_QUOTES, 'UTF-8') ?></p>
                                <h3><?= htmlspecialchars((string) $lugarCatalogo['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars((string) $lugarCatalogo['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                                <a class="boton-amarillo" href="lugar.php?slug=<?= rawurlencode((string) $lugarCatalogo['slug']) ?>&amp;origen=lugares">Ver ficha</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <p>Algunos datos de ubicación se basan en OpenStreetMap. <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">Contribuidores de OpenStreetMap</a>.</p>
            </section>
        <?php endif; ?>

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
            <div class="galeria-lugares gallery-photo-grid" id="galeria-lugares-grid">
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
            <button class="place-gallery-toggle" type="button" data-gallery-toggle aria-expanded="false" aria-controls="galeria-lugares-grid" hidden>Ver más</button>
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
                    <?php foreach ($lugaresMapa as $lugarMapa):
                        $consultaMapaLugar = trim((string) $lugarMapa['direccion']) . ', Salto, Uruguay';
                        $urlMapaLugar = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($consultaMapaLugar);
                    ?>
                        <a href="<?= htmlspecialchars($urlMapaLugar, ENT_QUOTES, 'UTF-8') ?>" data-map-query="<?= htmlspecialchars($consultaMapaLugar, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars((string) $lugarMapa['nombre'], ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
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
    <footer class="legal-page-link"><a href="terminos-privacidad.php" data-i18n="legalPageLink">Términos, privacidad y seguridad</a></footer>

    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
    <script src="../js/lugar-links.js?v=20261028-event-map-1" defer></script>
    <script src="../js/galeria-lugares.js?v=20261103-gallery-more-1" defer></script>
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
