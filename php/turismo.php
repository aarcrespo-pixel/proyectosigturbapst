<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/eventos_mapa.php';

$eventosMapa = sigtur_eventos_visibles_en_mapa($pdo);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css/turismo.css?v=20261112-routes-click-fix-1">
    <link rel="stylesheet" href="../css/legal.css">
</head>

<body>
    <?php
    $headerCurrentPage = 'turismo';
    $headerActivePage = 'turismo';
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
        <a href="turismo.php" class="bottom-nav-item active" aria-current="page">
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

    <main class="pagina-turismo">
        <section class="hero-turismo">
            <div class="hero-texto">
                <span data-i18n="tourismHeroLabel">Descubrí el turismo en Salto</span>
                <h1 data-i18n="tourismHeroTitle">TURISMO</h1>
                <p data-i18n="tourismHeroText">Explorá propuestas de turismo local, recorridos gastronómicos y experiencias al aire libre diseñadas para cada tipo de visitante.</p>
            </div>
        </section>

        <section class="seccion-turismo" id="destinos">
            <h2 data-i18n="tourismSectionEscapadasTitle">Escapadas recomendadas</h2>
            <div class="destinos-grid">
                <article class="tarjeta-destino">
                    <div class="tarjeta-destino__imagen">
                        <img src="../img/Costanera_Norte.jpeg" alt="Costanera de Salto" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="0">
                        <span class="tarjeta-destino__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="0">Costanera Norte</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="0">Un paseo al borde del río para disfrutar de arte urbano, música en vivo y atardeceres junto al agua.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="0">Ideal para familias · 3 km</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <div class="tarjeta-destino__imagen">
                        <img src="../img/BaSalto.jpg" alt="Basalto" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="1">
                        <span class="tarjeta-destino__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="1">BaSalto</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="1">Centro histórico con monumentos, cafés y espacios culturales que invitan a recorrer la identidad local.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="1">Cultura · Centro</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <div class="tarjeta-destino__imagen">
                        <img src="../img/solari.jfif" alt="Parque Solari" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="2">
                        <span class="tarjeta-destino__indicacion" aria-hidden="true">Tocá para conocer</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="2">Parque Benito Solari</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="2">Un lugar verde donde se mezclan senderos, picnic y actividades deportivas al aire libre.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="2">Naturaleza · Relax</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo" id="rutas-experiencias">
            <h2 data-i18n="tourismSectionRoutesTitle">Rutas y experiencias</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="../img/Trouville.jpg" alt="Trouville" data-i18n-alt="tourismRoutesAlt" data-i18n-index="0">
                        <a class="tarjeta-ruta__indicacion" href="lugar.php?id=la-trouville&amp;origen=turismo">Tocá para ver más</a>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="0">La Trouville</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="0">Probá platos típicos y descubrí sabores locales en mercados, parrillas y cafeterías con encanto.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="0">Comida</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="../img/Cine_Sarandi.jpg" alt="Cine Sarandi" data-i18n-alt="tourismRoutesAlt" data-i18n-index="1">
                        <a class="tarjeta-ruta__indicacion" href="lugar.php?id=cine-sarandi&amp;origen=turismo">Tocá para ver más</a>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="1">Cine Sarandi</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="1">Itinerarios para vivir la ciudad de noche con espectáculos, bares y rincones de música en vivo.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="1">Cine</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="../img/fosa.webp" alt="La Fosa" data-i18n-alt="tourismRoutesAlt" data-i18n-index="2">
                        <a class="tarjeta-ruta__indicacion" href="lugar.php?id=la-fosa&amp;origen=turismo">Tocá para ver más</a>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="2">La Fosa</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="2">Circuito de skate, bicicleta y barras calistenicas.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="2">Deportes · Guía disponible</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="../img/Shopping_Salto.jpg" alt="Shopping Salto" data-i18n-alt="tourismRoutesAlt" data-i18n-index="3">
                        <a class="tarjeta-ruta__indicacion" href="lugar.php?id=salto-shopping&amp;origen=turismo">Tocá para ver más</a>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="3">Salto Shopping</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="3">Shopping con variedad de tiendas y zonas para comer.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="3">Shopping</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo" id="parques-termas">
            <h2 data-i18n="tourismWaterparksTitle">Parques Acuáticos y Termas</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="https://www.salto.gub.uy/sites/default/files/styles/16_9_lg/public/2025-07/termas-dayman.png.webp?itok=Y_zseTHZ" alt="Dayman" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="0">
                        <span class="tarjeta-ruta__indicacion" aria-hidden="true">Tocá para ver más</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismWaterparkTitles" data-i18n-index="0">Termas del Dayman</h3>
                        <p data-i18n="tourismWaterparkDescriptions" data-i18n-index="0">Termas naturales con aguas termales.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                        </div>
                        <div class="meta" data-i18n="tourismWaterparkMeta" data-i18n-index="0">Termas</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="https://s3.amazonaws.com/pdugallery2/25801/big_nosotros_01.webp" alt="Aquamania" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="1">
                        <span class="tarjeta-ruta__indicacion" aria-hidden="true">Tocá para ver más</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismWaterparkTitles" data-i18n-index="1">Acuamania</h3>
                        <p data-i18n="tourismWaterparkDescriptions" data-i18n-index="1">Parque acuático con diversas atracciones y áreas de descanso.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                        </div>
                        <div class="meta" data-i18n="tourismWaterparkMeta" data-i18n-index="1">Parque Acuático</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="https://images.trvl-media.com/lodging/2000000/1160000/1159500/1159497/e0a87b61.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" alt="La Fosa" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="2">
                        <span class="tarjeta-ruta__indicacion" aria-hidden="true">Tocá para ver más</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismWaterparkTitles" data-i18n-index="2">Termas de la Arapey</h3>
                        <p data-i18n="tourismWaterparkDescriptions" data-i18n-index="2">Termas para relajarse en familia.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                        </div>
                        <div class="meta" data-i18n="tourismWaterparkMeta" data-i18n-index="2">Termas</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <div class="tarjeta-ruta__imagen">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtxRTmV6EPSUjXRqh8S7b3vhCQMugTwAgSTY-maV-T8qUuaUmApX5-SYk&s=10" alt="Aguas Claras" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="3">
                        <span class="tarjeta-ruta__indicacion" aria-hidden="true">Tocá para ver más</span>
                    </div>
                    <div class="contenido">
                        <h3 data-i18n="tourismWaterparkTitles" data-i18n-index="3">Agua Clara</h3>
                        <p data-i18n="tourismWaterparkDescriptions" data-i18n-index="3">Piscinas relajantes para disfrutar en las vacaciones.</p>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                        </div>
                        <div class="meta" data-i18n="tourismWaterparkMeta" data-i18n-index="3">Termas</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo mapa-seccion" aria-labelledby="mapa-turismo-titulo">
            <div class="mapa-encabezado">
                <span class="mapa-kicker">Ubicaciones</span>
                <h2 id="mapa-turismo-titulo">Planificá tu recorrido</h2>
                <p>Encontrá en el mapa los principales puntos turísticos y de descanso.</p>
            </div>
            <div class="mapa-contenido">
                <iframe src="https://www.google.com/maps?q=Salto%2C%20Uruguay&output=embed" loading="lazy" title="Mapa turístico de Salto"></iframe>
                <div class="mapa-lugares" id="mapa-turismo-lista">
                    <label class="mapa-lugares__buscador">
                        <span class="visually-hidden">Buscar un lugar o evento en el mapa</span>
                        <input type="search" data-map-search placeholder="Buscar un lugar o evento..." autocomplete="off">
                    </label>
                    <a href="https://www.google.com/maps/search/?api=1&query=Termas%20del%20Dayman%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Termas del Dayman</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Termas%20de%20la%20Arapey%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Termas de la Arapey</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Parque%20Benito%20Solari%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Parque Benito Solari</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Costanera%20Norte%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Costanera Norte</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=BaSalto%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">BaSalto</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=La%20Trouville%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">La Trouville</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Cine%20Sarand%C3%AD%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Cine Sarandí</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=La%20Fosa%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">La Fosa</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Salto%20Shopping%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Salto Shopping</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Acuamania%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Acuamania</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Agua%20Clara%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Agua Clara</a>
                    <?php foreach ($eventosMapa as $eventoMapa):
                        $ubicacionMapa = trim((string) $eventoMapa['ubicacion']);
                        $consultaMapa = $ubicacionMapa . ', Salto, Uruguay';
                        $urlMapaEvento = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($consultaMapa);
                    ?>
                        <a href="<?= htmlspecialchars($urlMapaEvento, ENT_QUOTES, 'UTF-8') ?>" data-map-query="<?= htmlspecialchars($consultaMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Evento: <?= htmlspecialchars((string) $eventoMapa['titulo'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($ubicacionMapa, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <p class="mapa-lugares__sin-resultados" data-map-empty hidden>No se encontraron ubicaciones.</p>
                    <button class="mapa-lugares__toggle" id="mapa-turismo-toggle" type="button" aria-expanded="false" aria-controls="mapa-turismo-lista" hidden>Ver más</button>
                </div>
            </div>
        </section>
    </main>
    <footer class="legal-page-link"><a href="terminos-privacidad.php" data-i18n="legalPageLink">Términos, privacidad y seguridad</a></footer>

    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
    <script src="../js/lugar-links.js?v=20261028-event-map-1" defer></script>
</body>

</html>