<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metaetiquetas y configuración inicial -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigtur Salto</title>
    <link rel="icon" href="../img/logoblanco.png"> <!-- Ícono de pestaña -->
    <!-- Importar fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Archivos CSS -->
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Estilos principales -->
    <link rel="stylesheet" href="../css/intro.css"> <!-- Animación de intro -->
</head>

<body>
    <!-- Pantalla de carga inicial -->
    <div class="intro">
        <img src="../img/logoblanco.png">
    </div>

    <!-- Menú principal -->
    <header class="menu-principal">
        <button class="hamburguesa">
            ☰
        </button>
        <nav class="menu">
            <a href="index.php" class="logo-link">
                <img src="../img/logoblanco.png" class="logo-menu" alt="SIGTUR">
            </a>
            <a href="eventos.php" data-i18n="navEvents">Eventos</a> <!-- Página actual marcada -->
            <a href="turismo.php" data-i18n="navTourism">Turismo</a>
            <a href="lugares.php" data-i18n="navPlaces">Lugares</a>
        </nav>

        <!-- Barra de búsqueda -->
        <div class="barra-busqueda">
            <input type="text" data-i18n-placeholder="searchPlaceholder" placeholder="Buscar eventos">
            <img src="../img/lupa.png" alt="Buscar">
        </div>

        <!-- Menú de perfil -->
        <div class="perfil">
            <button class="perfil-btn">
                <img src="../img/user.png" alt="Perfil">
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
        <div id="clima-widget" class="clima-widget"></div>
    
    </header>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="index.php" class="bottom-nav-item">
            <img src="../img/logoazul.png" class="bottom-nav-icon" alt="Inicio">
            <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
        </a>
        <a href="eventos.php" class="bottom-nav-item active">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item">
            <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares">
            <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
        </a>
    </nav>

    <section class="pantalla-inicio">
        <img class="imagen-inicio" src="https://www.gomezplatero.com/uploads/gallery/202405/20240522160708_1067064313.jpg" alt="Salto">
        <div class="degradado-inferior"></div> <!-- Degradado oscuro inferior -->
        <div class="texto-inicio">

        </div>
        <p class="texto-deslizar" data-i18n="homeScroll">
            Desliza para ver más
        </p>
        <!-- Botón de información -->
        <button class="info-btn" type="button" aria-expanded="false" data-i18n="homeInfoButton">Información</button>
        <!-- Panel con información sobre SIGTUR -->
        <div class="info-panel" id="info-panel">
            <h3 data-i18n="homeInfoTitle">¿Qué es SIGTUR?</h3>
            <p data-i18n="homeInfoText1">SIGTUR es una guía local de Salto pensada para ayudarte a descubrir eventos, lugares y experiencias
                únicas de la ciudad.</p>
            <p data-i18n="homeInfoText2">La plataforma reúne recomendaciones culturales, turísticas y de ocio para que cada visita sea más simple,
                informada y memorable.</p>
        </div>
    </section>


    <section class="seccion-features">

        <div class="feature">
            <div class="feature-header">
                <img src="../img/eventos.jpeg" alt="Eventos">
                <h3 data-i18n="homeFeatureEvents">Eventos</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p data-i18n="homeFeatureEventsText1">Descubre las fechas y actividades destacadas para planificar tu visita a Salto.</p>
                <p data-i18n="homeFeatureEventsText2">Explora propuestas para cada estilo y no te pierdas lo mejor que ofrece la ciudad.</p>
            </div>
        </div>
        <div class="feature">
            <div class="feature-header">
                <img src="../img/turismo.jpeg" alt="Turismo">
                <h3 data-i18n="homeFeatureTourism">Turismo</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p data-i18n="homeFeatureTourismText1">Encuentra ideas de recorridos y propuestas para disfrutar la ciudad y sus alrededores.</p>
                <p data-i18n="homeFeatureTourismText2">Conecta con opciones de paseos, experiencias y consejos para tu viaje.</p>
            </div>
        </div>
        <div class="feature">
            <div class="feature-header">
                <img src="../img/lugares.jpeg" alt="Lugares">
                <h3 data-i18n="homeFeaturePlaces">Lugares</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p data-i18n="homeFeaturePlacesText1">Conoce los puntos más emblemáticos y los sitios imperdibles de Salto.</p>
                <p data-i18n="homeFeaturePlacesText2">Descubre lugares únicos donde vivir momentos especiales y recordar la visita.</p>
            </div>
        </div>

    </section>

    <section class="seccion-eventos">
        <h2 data-i18n="homeEventsTitle">Eventos Destacados</h2>
        <div class="carrusel">
            <button class="flecha izquierda"></button>
            <div class="contenedor-slides">
                <div class="slide activo">
                    <div class="info-evento">
                        <h3>PorcoNegro</h3>
                        <p>Todos los sabados de las 00:00 a 06:00!</p>
                        <div class="accion-boton">
                            <a href="eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="../img/porco.avif" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>La Bambola</h3>
                        <p>Todos los fines de semana!</p>
                        <div class="accion-boton">
                            <a href="eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>

                    <img src="../img/bambi.jpg" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>LSF</h3>
                        <p>Competición de futbol salteña.</p>
                        <div class="accion-boton">
                            <a href="eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="../img/lsf.jpg" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>LSB</h3>
                        <p>Competición de básquetbol salteño.</p>
                        <div class="accion-boton">
                            <a href="eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="../img/lsb.jpg" alt="Evento">
                </div>
            </div>

            <button class="flecha derecha"></button>

        </div>

        <div class="indicadores">
            <span class="indicador activo"></span>
            <span class="indicador"></span>
            <span class="indicador"></span>
            <span class="indicador"></span>
        </div>

    </section>

    <section class="seccion-notificaciones">
        <h2 data-i18n="homeNewsTitle">Noticias en Salto</h2>
        <div class="contenedor-carta">
            <img src="../img/peñ.jpg" alt="Carta" class="imagen-carta">
            <div class="texto-carta">
                <h2>El Clásico Salteño se juega este fin de semana, Peñarol vs. Nacional</h2>
                <p>Este sabado 18 de julio se disputará el clásico entre Peñarol y Nacional en el Estadio de Peñarol</p>
                <div class="accion-boton">
                    <a href="eventos.php" class="boton-amarillo" data-i18n="homeNewsButton">Ver noticia</a>
                </div>
            </div>
        </div>
    </section>
    
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
                    <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">IG</span>Instagram
                    </a>
                    <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">FB</span>Facebook
                    </a>
                </div>
            </div>
        </div>
        </div>


        <div class="footer-bottom">
            <a href="https://bapst.netlify.app">SIGTUR</a> © 2026 by <a href="https://instagram.com/bapstuy">Bapst</a>
            is licensed under <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/">CC BY-NC-ND 4.0</a><img
                src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nd.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;">
        </div>
    </footer>

    <script src="../js/script.js"></script>
    <script src="../js/ith.js"></script>
</body>

</html>