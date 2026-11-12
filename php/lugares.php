<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/eventos_mapa.php';
require_once __DIR__ . '/helpers/contenido_publico.php';

sigtur_inicializar_contenido_publico($pdo);

$eventosMapa = sigtur_eventos_visibles_en_mapa($pdo);
$lugaresCatalogoStmt = $pdo->query("SELECT slug, nombre, categoria, direccion, imagen, descripcion
    FROM lugares
    WHERE seccion_publica <> 'oculto'
        AND slug NOT IN ('plaza-treinta-y-tres-orientales', 'plaza-artigas', 'parque-benito-solari', 'plaza-roosvelt', 'costanera-norte', 'muelle-negro')
    ORDER BY nombre");
$lugaresCatalogo = $lugaresCatalogoStmt->fetchAll();
foreach ($lugaresCatalogo as &$lugarCatalogo) {
    $lugarCatalogo['imagen'] = sigtur_url_imagen_subida((string) $lugarCatalogo['imagen'], 'contenido-publico', '../img/Plaza_artigas.webp');
}
unset($lugarCatalogo);
$lugaresDistancias = $pdo->query('SELECT id, slug, nombre, categoria, latitud, longitud, osm_type, osm_id FROM lugares ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
$lugaresDistanciasJson = array_map(static function (array $lugar): array {
    $tipoOsm = in_array($lugar['osm_type'] ?? null, ['node', 'way', 'relation'], true) ? $lugar['osm_type'] : null;
    $idOsm = is_string($lugar['osm_id'] ?? null) && preg_match('/^[0-9]{1,32}$/', $lugar['osm_id']) ? $lugar['osm_id'] : null;
    $coordenadasValidas = $tipoOsm !== null
        && $idOsm !== null
        && is_numeric($lugar['latitud'] ?? null)
        && is_numeric($lugar['longitud'] ?? null);

    return [
        'id' => (int) $lugar['id'],
        'slug' => (string) $lugar['slug'],
        'name' => (string) $lugar['nombre'],
        'category' => (string) $lugar['categoria'],
        'latitude' => $coordenadasValidas ? (float) $lugar['latitud'] : null,
        'longitude' => $coordenadasValidas ? (float) $lugar['longitud'] : null,
        'osmUrl' => $coordenadasValidas ? 'https://www.openstreetmap.org/' . $tipoOsm . '/' . $idOsm : null,
    ];
}, $lugaresDistancias);
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
    <link rel="stylesheet" href="../css/lugares.css?v=20261029-interactive-cards-2">
    <link rel="stylesheet" href="../css/lugares-distancias.css?v=1">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
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
                        <img src="../img/Plaza_33.webp" alt="Costanera Sur" data-i18n-alt="placesEmblematicAlt" data-i18n-index="0">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="0">Plaza Treinta y Tres Orientales</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="0">Paseo tranquilo junto al río con espacios verdes, miradores y zonas para descansar al aire libre.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar" id="plaza-artigas">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/Plaza_artigas.webp" alt="Plaza histórica" data-i18n-alt="placesEmblematicAlt" data-i18n-index="1">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="1">Plaza Artigas</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="1">Plaza emblemática del centro con arquitectura tradicional, cafeterías y actividad cultural permanente.</p>
                    </div>
                </article>
                <article class="tarjeta-lugar" id="parque-benito-solari">
                    <div class="tarjeta-lugar__imagen">
                        <img src="../img/solari.jfif" alt="Parque Benito Solari" data-i18n-alt="placesEmblematicAlt" data-i18n-index="2">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesEmblematicTitles" data-i18n-index="2">Parque Benito Solari</h3>
                        <p data-i18n="placesEmblematicDescriptions" data-i18n-index="2">Gran área verde perfecta para caminatas familiares, actividades deportivas y eventos al aire libre.</p>
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
                        <img src="https://www.salto.gub.uy/sites/default/files/styles/16_9_xl_scale/public/2024-08/0505190000.JPG.webp?itok=67uThcCX" alt="Plaza Roosvelt" data-i18n-alt="placesLeisureAlt" data-i18n-index="0" onerror="this.onerror=null;this.src='../img/Plaza_33.webp';">
                        <span class="tarjeta-lugar__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="tarjeta-contenido">
                        <h3 data-i18n="placesLeisureTitles" data-i18n-index="0">Plaza Roosvelt</h3>
                        <p data-i18n="placesLeisureDescriptions" data-i18n-index="0">Plaza tranquila en la costa para tomar mates con amigos u familiares.</p>
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

        <section class="distancias-lugares" id="distancias-atractivos" aria-labelledby="distancias-lugares-titulo">
            <div class="distancias-lugares__encabezado">
                <span class="distancias-lugares__eyebrow">Explorá Salto</span>
                <h2 id="distancias-lugares-titulo">Distancias entre atractivos turísticos de Salto</h2>
                <p>Elegí dos lugares para comparar la distancia en línea recta y, cuando haya una ruta disponible, el recorrido caminando.</p>
            </div>
            <div class="distancias-lugares__panel">
                <form class="distancias-lugares__controles" data-distancias-form>
                    <label>
                        <span>Origen</span>
                        <select name="origen" data-distancias-origen required>
                            <option value="">Elegí un lugar</option>
                            <?php foreach ($lugaresDistanciasJson as $lugarDistancia): ?>
                                <option value="<?= (int) $lugarDistancia['id'] ?>" <?= $lugarDistancia['latitude'] === null ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($lugarDistancia['name'], ENT_QUOTES, 'UTF-8') ?><?= $lugarDistancia['latitude'] === null ? ' — ubicación pendiente en OpenStreetMap' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <span class="distancias-lugares__intercambio" aria-hidden="true">↔</span>
                    <label>
                        <span>Destino</span>
                        <select name="destino" data-distancias-destino required>
                            <option value="">Elegí un lugar</option>
                            <?php foreach ($lugaresDistanciasJson as $lugarDistancia): ?>
                                <option value="<?= (int) $lugarDistancia['id'] ?>" <?= $lugarDistancia['latitude'] === null ? 'disabled' : '' ?>>
                                    <?= htmlspecialchars($lugarDistancia['name'], ENT_QUOTES, 'UTF-8') ?><?= $lugarDistancia['latitude'] === null ? ' — ubicación pendiente en OpenStreetMap' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button class="distancias-lugares__boton" type="submit">Calcular distancia</button>
                </form>
                <p class="distancias-lugares__aviso" data-distancias-aviso role="status">Las ubicaciones se toman de objetos nombrados en OpenStreetMap. Los lugares sin una coincidencia identificable quedan deshabilitados hasta agregar su referencia OSM.</p>
                <div class="distancias-lugares__resultados" data-distancias-resultados aria-live="polite">
                    <article class="distancias-lugares__resultado">
                        <span>En línea recta</span>
                        <strong data-distancia-linea>—</strong>
                        <small>Estimación geográfica entre puntos de referencia.</small>
                    </article>
                    <article class="distancias-lugares__resultado">
                        <span>Caminando</span>
                        <strong data-distancia-caminando>—</strong>
                        <small data-tiempo-caminando>Se mostrará solo si el servicio devuelve una ruta peatonal.</small>
                    </article>
                </div>
                <div class="distancias-lugares__mapa" data-distancias-mapa role="region" aria-label="Mapa de los lugares seleccionados"></div>
                <div class="distancias-lugares__pie">
                    <p data-distancias-fuente>Seleccioná dos ubicaciones disponibles para verlas en el mapa.</p>
                    <div class="distancias-lugares__atribucion">
                        <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">Datos del mapa © contribuidores de OpenStreetMap</a>
                        <a href="https://routing.openstreetmap.de/" target="_blank" rel="noopener noreferrer">Rutas peatonales sobre datos de OpenStreetMap</a>
                    </div>
                </div>
            </div>
            <script type="application/json" data-distancias-lugares><?= json_encode($lugaresDistanciasJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) ?></script>
        </section>
    </main>
    <footer class="legal-page-link"><a href="terminos-privacidad.php" data-i18n="legalPageLink">Términos, privacidad y seguridad</a></footer>

    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
    <script src="../js/lugar-links.js?v=20261028-event-map-1" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script src="../js/lugares-distancias.js?v=1" defer></script>

</html>
