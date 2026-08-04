<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lugares | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png"> <!-- Ícono de pestaña --> 
    <!-- Importar fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Estilos generales -->
    <link rel="stylesheet" href="../css/lugares.css"> <!-- Estilos específicos de lugares -->
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
            <a href="turismo.php" data-i18n="navTourism">Turismo</a>
            <a href="lugares.php" class="pagina-activa" data-i18n="navPlaces">Lugares</a>
        </nav>

        <div class="barra-busqueda">
            <input type="text" placeholder="Buscar lugares" data-i18n-placeholder="searchPlaceholder">
            <img src="../img/lupa.png" alt="Buscar" data-i18n-alt="navSearch">
        </div>

        <div class="perfil">
            <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
                <img src="../img/userb.png" alt="Perfil" data-i18n-alt="navProfile">
            </button>
            <div class="perfil-menu">
                <a href="login.php" data-i18n="navLogin">Ingresar Usuario</a>
                <a href="configuracion.php" data-i18n="navSettings">Configuración</a>
                <a href="soporte.php" data-i18n="navSupport">Soporte</a>
                <a href="cerrar-sesion.php" data-i18n="navLogout">Cerrar Sesión</a>
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

    <main class="pagina-lugares">
        <section class="hero-lugares">
            <div class="hero-texto">
                <span data-i18n="placesHeroLabel">Lugares históricos</span>
                <h1 data-i18n="placesHeroTitle">LUGARES</h1>
                <p data-i18n="placesHeroText">Descubre los lugares más emblemáticos de Salto, llenos de historia y cultura.</p>
            </div>
        </section>

        <section class="seccion-lugares">
            <h2 data-i18n="placesSectionEmblematicTitle">Lugares emblemáticos</h2>
            <div class="destinos-grid">
                <article class="tarjeta-lugar">
                    <img src="../img/plazab.jpeg" alt="Costanera Sur" data-i18n-alt="placesEmblematicAlt" data-i18n-index="0">
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

        <div class="accion-boton">
            <a href="#" class="boton-lugares" data-i18n="placesMoreButton">Ver más lugares</a>
        </div>

        <section class="seccion-lugares">
            <h2 data-i18n="placesSectionGalleryTitle">Galería Histórica</h2>
            <div class="galeria-lugares">
                <article class="tarjeta-galeria"><img src="../img/plazab.jpeg" alt="Vista de evento" data-i18n-alt="placesGalleryAlt" data-i18n-index="0"><div class="tarjeta-contenido"><h3 data-i18n="placesGalleryTitles" data-i18n-index="0">Plaza Treinta y Tres</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Plaza_artigas.webp" alt="Plaza central" data-i18n-alt="placesGalleryAlt" data-i18n-index="1"><div class="tarjeta-contenido"><h3 data-i18n="placesGalleryTitles" data-i18n-index="1">Plaza Artigas</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/solari.jfif" alt="Espacio de descanso" data-i18n-alt="placesGalleryAlt" data-i18n-index="2"><div class="tarjeta-contenido"><h3 data-i18n="placesGalleryTitles" data-i18n-index="2">Parque Benito Solari</h3></div></article>
                <article class="tarjeta-galeria"><img src="../img/Termas_Dayman.webp" alt="Punto turístico" data-i18n-alt="placesGalleryAlt" data-i18n-index="3"><div class="tarjeta-contenido"><h3 data-i18n="placesGalleryTitles" data-i18n-index="3">Termas del Dayman</h3></div></article>
            </div>
        </section>

        <div class="accion-boton">
            <a href="#" class="boton-lugares" data-i18n="placesGalleryButton">Ver todas las galerías</a>
        </div>
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
