<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración básica de página -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo | SIGTUR</title>
    <link rel="icon" href="/img/logoblanco.png"> <!-- Ícono de pestaña -->
    <!-- Importar fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="../css/turismo.css"> <!-- Estilos específicos de turismo -->
</head>

<body>
    <!-- Menú principal -->
    <header class="menu-principal">
        <button class="hamburguesa" aria-label="Abrir menú" data-i18n-aria-label="navMenuOpen">☰</button>
        <nav class="menu">
            <a href="index.php" class="logo-link">
                <img src="../img/logoblanco.png" class="logo-menu" alt="SIGTUR">
            </a>
            <a href="eventos.php" data-i18n="navEvents">Eventos</a>
            <a href="turismo.php" class="pagina-activa" data-i18n="navTourism">Turismo</a>
            <a href="lugares.php" data-i18n="navPlaces">Lugares</a>
        </nav>

        <div class="barra-busqueda">
            <input type="text" placeholder="Buscar viajes y rutas" data-i18n-placeholder="searchPlaceholder">
            <img src="../img/lupa.png" alt="Buscar" data-i18n-alt="navSearch">
        </div>

        <div class="perfil">
            <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
                <img src="../img/userb.png" alt="Perfil" data-i18n-alt="navProfile">
            </button>
            <div class="perfil-menu">
                <?php if (!isset($loggedIn) || !$loggedIn): ?>
                    <a href="login.php" data-i18n="navLogin">Ingresar Usuario</a>
                <?php endif; ?>
                <a href="configuracion.php" data-i18n="navSettings">Configuración</a>
                <a href="soporte.php" data-i18n="navSupport">Soporte</a>
                <?php if (isset($loggedIn) && $loggedIn): ?>
                    <a href="cerrar-sesion.php" data-i18n="navLogout">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="index.php" class="bottom-nav-item">
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
    </nav>

    <main class="pagina-turismo">
        <section class="hero-turismo">
            <div class="hero-texto">
                <span data-i18n="tourismHeroLabel">Descubrí el turismo en Salto</span>
                <h1 data-i18n="tourismHeroTitle">TURISMO</h1>
                <p data-i18n="tourismHeroText">Explorá propuestas de turismo local, recorridos gastronómicos y experiencias al aire libre diseñadas para cada tipo de visitante.</p>
            </div>
        </section>

        <section class="seccion-turismo">
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
            <h2 data-i18n="tourismHotelsTitle">Hoteles</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOxxqJdx2__yz4HJSzIdUN71HMmZIDegVg7kkLQz4n5G1Ve6HxjOnV66w&s=10" alt="Salto Hotel & Casino" data-i18n-alt="tourismHotelAlt" data-i18n-index="0">
                    <div class="contenido">
                        <h3 data-i18n="tourismHotelNames" data-i18n-index="0">Salto Hotel & Casino</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                            <div class="precio" data-i18n="tourismHotelPrices" data-i18n-index="0">$230 por noche</div>
                            <div class="ubicacion" data-i18n="tourismHotelLocations" data-i18n-index="0">Dirección: 25 de Agosto 05</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/03/75/fd/47/hotel-eldorado.jpg?w=900&h=500&s=1" alt="Hotel Eldorado" data-i18n-alt="tourismHotelAlt" data-i18n-index="1">
                    <div class="contenido">
                        <h3 data-i18n="tourismHotelNames" data-i18n-index="1">Hotel Eldorado</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                            <div class="precio" data-i18n="tourismHotelPrices" data-i18n-index="1">$310 por noche</div>
                            <div class="ubicacion" data-i18n="tourismHotelLocations" data-i18n-index="1">Dirección: Av. Sarandi 20</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://cf.bstatic.com/xdata/images/hotel/max1024x768/28964462.jpg?k=ccae2ff8a5211286850c056eabe84fe4eec302509347514ebd59aaf81e31771a&o=" alt="Hotel Español" data-i18n-alt="tourismHotelAlt" data-i18n-index="2">
                    <div class="contenido">
                        <h3 data-i18n="tourismHotelNames" data-i18n-index="2">Hotel Español</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                            <div class="precio" data-i18n="tourismHotelPrices" data-i18n-index="2">$185 por noche</div>
                            <div class="ubicacion" data-i18n="tourismHotelLocations" data-i18n-index="2">Dirección: Barrio Hipódromo</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://d3l2053yktv8l0.cloudfront.net/images/DSC_3893.jpg" alt="Hotel Quiroga" data-i18n-alt="tourismHotelAlt" data-i18n-index="3">
                    <div class="contenido">
                        <h3 data-i18n="tourismHotelNames" data-i18n-index="3">Hotel Horacio Quiroga</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                            <div class="precio" data-i18n="tourismHotelPrices" data-i18n-index="3">$270 por noche</div>
                            <div class="ubicacion" data-i18n="tourismHotelLocations" data-i18n-index="3">Dirección: Brasil 826</div>
                        </div>
                    </div>
                </article>
                <a href="#" class="boton-turismo" data-i18n="tourismHotelsSeeAll">Ver todos los hoteles</a>
            </div>
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
            <h2 data-i18n="tourismGastronomyTitle">Gastronomía</h2>
            <div class="galeria-grid">
                <article class="tarjeta-galeria">
                    <img src="https://media-cdn.tripadvisor.com/media/photo-s/04/13/0f/8f/trouville.jpg" alt="La Trouville" data-i18n-alt="tourismRoutesAlt" data-i18n-index="0">
                    <div class="contenido">
                        <h3 data-i18n="tourismRoutesTitles" data-i18n-index="0">La Trouville</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                            <div class="ubicacion">Dirección: Uruguay 702</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/1c/3c/04/55/dominico-salti.jpg?w=1200&h=-1&s=1" alt="Dominico" data-i18n-alt="tourismHotelAlt" data-i18n-index="1">
                    <div class="contenido">
                        <h3>Dominico</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                            <div class="ubicacion">Dirección: Av. Blandengues 320</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTN9naO3vR30QRuFwNtzd7BYe--0vGV4ETKMZpq01-ARRQGb-nsOgRO_WOp&s=10" alt="La2000" data-i18n-alt="tourismHotelAlt" data-i18n-index="2">
                    <div class="contenido">
                        <h3>La 2000</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="4 estrellas" data-i18n-aria-label="tourismHotelStars4">★★★★☆</div>
                            <div class="ubicacion">Dirección: Av. Blandengues 195</div>
                        </div>
                    </div>
                </article>
                <article class="tarjeta-galeria">
                    <img src="https://dynamic-media-cdn.tripadvisor.com/media/photo-o/21/6c/1d/1b/puerta-de-ingreso-al.jpg?w=1200&h=1200&s=1" alt="Trattoria" data-i18n-alt="tourismHotelAlt" data-i18n-index="3">
                    <div class="contenido">
                        <h3>La Trattoria</h3>
                        <div class="hotel-info">
                            <div class="estrellas" aria-label="5 estrellas" data-i18n-aria-label="tourismHotelStars5">★★★★★</div>
                            <div class="ubicacion">Dirección: Uruguay 234</div>
                        </div>
                    </div>
                </article>
                <a href="#" class="boton-turismo" data-i18n="tourismGastronomySeeAll">Ver toda la gastronomía salteña</a>
            </div>
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
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="0">Hoteles</a>
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="1">Actividades</a>
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="2">Lugares turísticos</a>
                    <a href="../php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="3">Experiencias</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">IG</span>Instagram</a>
                <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon"><span class="social-icon">FB</span>Facebook</a>
            </div>
        </div>
        <div class="footer-bottom" data-i18n="footerLegal">SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0</div>
    </footer>
    <script src="../js/script.js" defer></script>
</body> 

</html>