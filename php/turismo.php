<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo | SIGTUR</title>
    <link rel="icon" href="/img/logoblanco.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css/turismo.css">
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
            <img src="../img/gastronomia.jpg" class="bottom-nav-icon" alt="Gastronomía">
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
                    <img src="../img/Costanera_Norte.jpeg" alt="Costanera de Salto" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="0">
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="0">Costanera Norte</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="0">Un paseo al borde del río para disfrutar de arte urbano, música en vivo y atardeceres junto al agua.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="0">Ideal para familias · 3 km</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <img src="../img/BaSalto.jpg" alt="Basalto" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="1">
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="1">BaSalto</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="1">Centro histórico con monumentos, cafés y espacios culturales que invitan a recorrer la identidad local.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="1">Cultura · Centro</div>
                    </div>
                </article>
                <article class="tarjeta-destino">
                    <img src="../img/solari.jfif" alt="Parque Solari" data-i18n-alt="tourismEscapadasAlt" data-i18n-index="2">
                    <div class="contenido">
                        <h3 data-i18n="tourismEscapadasTitles" data-i18n-index="2">Parque Benito Solari</h3>
                        <p data-i18n="tourismEscapadasDescriptions" data-i18n-index="2">Un lugar verde donde se mezclan senderos, picnic y actividades deportivas al aire libre.</p>
                        <div class="meta" data-i18n="tourismEscapadasMeta" data-i18n-index="2">Naturaleza · Relax</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo">
            <h2 data-i18n="tourismSectionRoutesTitle">Rutas y experiencias</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <img src="../img/Trouville.jpg" alt="Trouville" data-i18n-alt="tourismRoutesAlt" data-i18n-index="0">
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="0">La Trouville</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="0">Probá platos típicos y descubrí sabores locales en mercados, parrillas y cafeterías con encanto.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="0">Comida</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/Cine_Sarandi.jpg" alt="Cine Sarandi" data-i18n-alt="tourismRoutesAlt" data-i18n-index="1">
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="1">Cine Sarandi</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="1">Itinerarios para vivir la ciudad de noche con espectáculos, bares y rincones de música en vivo.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="1">Cine</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/fosa.webp" alt="La Fosa" data-i18n-alt="tourismRoutesAlt" data-i18n-index="2">
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="2">La Fosa</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="2">Circuito de skate, bicicleta y barras calistenicas.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="2">Deportes · Guía disponible</div>
                    </div>
                </article>
                <article class="tarjeta-ruta">
                    <img src="../img/Shopping_Salto.jpg" alt="Shopping Salto" data-i18n-alt="tourismRoutesAlt" data-i18n-index="3">
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="3">Salto Shopping</h3>
                        <p data-i18n="tourismRoutesDescriptions" data-i18n-index="3">Shopping con variedad de tiendas y zonas para comer.</p>
                        <div class="meta" data-i18n="tourismRoutesMeta" data-i18n-index="3">Shopping</div>
                    </div>
                </article>
            </div>
        </section>

        <section class="seccion-turismo">
            <a href="#" class="boton-turismo" data-i18n="tourismDiscoverMore">Descubrir más</a>
        </section>

        <section class="seccion-turismo">
            <h2 data-i18n="tourismWaterparksTitle">Parques Acuáticos y Termas</h2>
            <div class="rutas-grid">
                <article class="tarjeta-ruta">
                    <img src="https://www.salto.gub.uy/sites/default/files/styles/16_9_lg/public/2025-07/termas-dayman.png.webp?itok=Y_zseTHZ" alt="Dayman" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="0">
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
                    <img src="https://s3.amazonaws.com/pdugallery2/25801/big_nosotros_01.webp" alt="Aquamania" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="1">
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
                    <img src="https://images.trvl-media.com/lodging/2000000/1160000/1159500/1159497/e0a87b61.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" alt="La Fosa" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="2">
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
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRtxRTmV6EPSUjXRqh8S7b3vhCQMugTwAgSTY-maV-T8qUuaUmApX5-SYk&s=10" alt="Aguas Claras" data-i18n-alt="tourismWaterparkAlt" data-i18n-index="3">
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

        <section class="seccion-turismo">
            <a href="#" class="boton-turismo" data-i18n="tourismDiscoverMore">Descubrir más</a>
        </section>

        <section class="seccion-turismo">
            <h2 data-i18n="tourismGalleryTitle">Galería turística</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria"><img src="../img/solari.jfif" alt="Parque Solari" data-i18n-alt="tourismGalleryAlt" data-i18n-index="0"><div class="contenido"><h3 data-i18n="tourismGalleryTitles" data-i18n-index="0">Parque Benito Solari</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/fosa.webp" alt="La Fosa" data-i18n-alt="tourismGalleryAlt" data-i18n-index="1"><div class="contenido"><h3 data-i18n="tourismGalleryTitles" data-i18n-index="1">La Fosa</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Shopping_Salto.jpg" alt="Shopping Salto" data-i18n-alt="tourismGalleryAlt" data-i18n-index="2"><div class="contenido"><h3 data-i18n="tourismGalleryTitles" data-i18n-index="2">Shopping Salto</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Cine_Sarandi.jpg" alt="Cine Sarandi" data-i18n-alt="tourismGalleryAlt" data-i18n-index="3"><div class="contenido"><h3 data-i18n="tourismGalleryTitles" data-i18n-index="3">Cine Sarandi</h3></div></article>
                <a href="#" class="boton-turismo" data-i18n="tourismGallerySeeAll">Ver todas las galerías</a>
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
                <div class="mapa-lugares">
                    <a href="https://www.google.com/maps/search/?api=1&query=Termas%20del%20Dayman%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Termas del Dayman</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Termas%20de%20Arapey%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Termas de Arapey</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Parque%20Benito%20Solari%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Parque Benito Solari</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Costanera%20Norte%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Costanera Norte</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerEvents">EVENTOS</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="0">Eventos destacados</a>
                    <a href="../php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="1">Próximos eventos</a>
                    <a href="../php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="2">Eventos anteriores</a>
                    <a href="../php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="3">Todos los eventos</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="../php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="../php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="../php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="../php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerTourism">TURISMO</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="0">Actividades</a>
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="1">Lugares turísticos</a>
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="2">Experiencias</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon"></span>
                </button>
                <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">IG</span>Instagram</a>
                <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">FB</span>Facebook</a>
            </div>
        </div>
        <div class="footer-bottom" data-i18n="footerLegal">SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0</div>
    </footer>
    <script src="../js/script.js" defer></script>
    <script src="../js/interacciones-tarjetas.js" defer></script>
</body> 

</html>