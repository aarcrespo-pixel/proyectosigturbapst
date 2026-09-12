<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metaetiquetas y configuración inicial -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sigtur Salto</title>
    <link rel="icon" href="img/logoblanco.png"> <!-- Ícono de pestaña -->
    <!-- Importar fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Archivos CSS -->
    <link rel="stylesheet" href="css/estilos.css?v=<?= time(); ?>"> <!-- Estilos principales -->
    <link rel="stylesheet" href="css/intro.css"> <!-- Animación de intro -->
</head>

<body>
    <!-- Pantalla de carga inicial -->
    <div class="intro">
        <img src="img/logoblanco.png">
    </div>

    <?php
    $headerCurrentPage = 'index';
$headerActivePage = 'index';
include __DIR__ . '/php/header.php';
?>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="index.php" class="bottom-nav-item">
            <img src="img/logoazul.png" class="bottom-nav-icon" alt="Inicio">
            <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
        </a>
        <a href="php/eventos.php" class="bottom-nav-item active">
            <img src="img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="php/turismo.php" class="bottom-nav-item">
            <img src="img/turismo.png" class="bottom-nav-icon" alt="Turismo">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="php/lugares.php" class="bottom-nav-item">
            <img src="img/lugares.png" class="bottom-nav-icon" alt="Lugares">
            <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
        </a>
        <a href="php/gastronomia.php" class="bottom-nav-item">
            <img src="img/gastronomia.jpg" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label" data-i18n="navGastronomy">Gastronomía</span>
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
                <img src="img/eventos.jpeg" alt="Eventos">
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
                <img src="img/turismo.jpeg" alt="Turismo">
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
                <img src="img/lugares.jpeg" alt="Lugares">
                <h3 data-i18n="homeFeaturePlaces">Lugares</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p data-i18n="homeFeaturePlacesText1">Conoce los puntos más emblemáticos y los sitios imperdibles de Salto.</p>
                <p data-i18n="homeFeaturePlacesText2">Descubre lugares únicos donde vivir momentos especiales y recordar la visita.</p>
            </div>
        </div>
        <div class="feature">
            <div class="feature-header">
                <img src="img/gastronomia.jpg" alt="Gastronomía">
                <h3>Gastronomía</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p>Descubre sabores locales, cafeterías y propuestas para disfrutar cada momento.</p>
                <p>Encuentra opciones para compartir, conocer la identidad de Salto y volver por más.</p>
            </div>
        </div>

    </section>

    <section class="seccion-eventos" data-carrusel-seccion="eventos">
        <h2 data-i18n="homeEventsTitle">Eventos Destacados</h2>
        <div class="carrusel" data-carrusel="eventos">
            <button class="flecha izquierda"></button>
            <div class="contenedor-slides">
                <div class="slide activo">
                    <div class="info-evento">
                        <h3>PorcoNegro</h3>
                        <p>Todos los sabados de las 00:00 a 06:00!</p>
                        <div class="accion-boton">
                            <a href="php/eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="img/porco.avif" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>La Bambola</h3>
                        <p>Todos los fines de semana!</p>
                        <div class="accion-boton">
                            <a href="php/eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>

                    <img src="img/bambi.jpg" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>LSF</h3>
                        <p>Competición de futbol salteña.</p>
                        <div class="accion-boton">
                            <a href="php/eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="img/lsf.jpg" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>LSB</h3>
                        <p>Competición de básquetbol salteño.</p>
                        <div class="accion-boton">
                            <a href="php/eventos.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="img/lsb.jpg" alt="Evento">
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

    <section class="seccion-eventos" data-carrusel-seccion="lugares-destacados">
        <h2>Lugares destacados</h2>
        <div class="carrusel" data-carrusel="lugares-destacados">
            <button class="flecha izquierda" aria-label="Lugar anterior"></button>
            <div class="contenedor-slides">
                <div class="slide activo">
                    <div class="info-evento">
                        <h3>Plaza Artigas</h3>
                        <p>Un paseo céntrico para disfrutar de historia, arquitectura y movimiento local.</p>
                        <div class="accion-boton"><a href="php/lugares.php" class="boton-amarillo">Ver lugares</a></div>
                    </div>
                    <img src="img/Plaza_artigas.webp" alt="Plaza Artigas">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Costanera Norte</h3>
                        <p>Un recorrido junto al río para caminar, descansar y disfrutar el atardecer.</p>
                        <div class="accion-boton"><a href="php/lugares.php" class="boton-amarillo">Ver lugares</a></div>
                    </div>
                    <img src="img/Costanera_Norte.jpeg" alt="Costanera Norte">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Parque Benito Solari</h3>
                        <p>Espacios verdes y aire libre para compartir una salida tranquila.</p>
                        <div class="accion-boton"><a href="php/lugares.php" class="boton-amarillo">Ver lugares</a></div>
                    </div>
                    <img src="img/solari.jfif" alt="Parque Benito Solari">
                </div>
            </div>
            <button class="flecha derecha" aria-label="Siguiente lugar"></button>
        </div>
        <div class="indicadores">
            <span class="indicador activo"></span>
            <span class="indicador"></span>
            <span class="indicador"></span>
        </div>
    </section>

    <section class="seccion-eventos" data-carrusel-seccion="gastronomia-destacada">
        <h2>Gastronomía destacada</h2>
        <div class="carrusel" data-carrusel="gastronomia-destacada">
            <button class="flecha izquierda" aria-label="Lugar gastronómico anterior"></button>
            <div class="contenedor-slides">
                <div class="slide activo">
                    <div class="info-evento">
                        <h3>La Trouville</h3>
                        <p>Sabores locales y una propuesta ideal para compartir una buena salida.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/Trouville.jpg" alt="La Trouville">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Cine Sarandí</h3>
                        <p>Una salida para combinar entretenimiento, encuentro y propuestas cercanas.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/Cine_Sarandi.jpg" alt="Cine Sarandí">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Salto Shopping</h3>
                        <p>Opciones para comer, pasear y disfrutar una salida urbana.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/Shopping_Salto.jpg" alt="Salto Shopping">
                </div>
            </div>
            <button class="flecha derecha" aria-label="Siguiente lugar gastronómico"></button>
        </div>
        <div class="indicadores">
            <span class="indicador activo"></span>
            <span class="indicador"></span>
            <span class="indicador"></span>
        </div>
    </section>

    <section class="seccion-notificaciones">
        <h2 data-i18n="homeNewsTitle">Noticias en Salto</h2>
        <div class="contenedor-carta">
            <img src="img/peñ.jpg" alt="Carta" class="imagen-carta">
            <div class="texto-carta">
                <h2>El Clásico Salteño se juega este fin de semana, Peñarol vs. Nacional</h2>
                <p>Este sabado 18 de julio se disputará el clásico entre Peñarol y Nacional en el Estadio de Peñarol</p>
                <div class="accion-boton">
                    <a href="php/eventos.php" class="boton-amarillo" data-i18n="homeNewsButton">Ver noticia</a>
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
                    <a href="php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="0">Eventos destacados</a>
                    <a href="php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="1">Próximos eventos</a>
                    <a href="php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="2">Eventos anteriores</a>
                    <a href="php/eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="3">Todos los eventos</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerTourism">TURISMO</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="0">Actividades</a>
                    <a href="php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="1">Lugares turísticos</a>
                    <a href="php/turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="2">Experiencias</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon"></span>
                </button>
                    <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">IG</span>Instagram
                    </a>
                    <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">FB</span>Facebook
                    </a>
                    <button class="footer-bapst-button footer-info-button" type="button" aria-expanded="false" data-i18n="homeInfoButton">Información</button>
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

    <script src="js/script.js?v=dark-features"></script>    <div class="perfil-modal-backdrop" id="perfilModal" aria-hidden="true">
        <div class="perfil-modal" role="dialog" aria-modal="true" aria-labelledby="perfilModalTitle">
            <div class="perfil-modal-header">
                <div>
                    <p class="modal-kicker">Cuenta</p>
                    <h2 id="perfilModalTitle">Personalizar Perfil</h2>
                </div>
                <button type="button" class="perfil-modal-close" data-close-profile-modal aria-label="Cerrar">×</button>
            </div>

            <form class="perfil-form" method="post" action="php/personalizar-perfil.php" enctype="multipart/form-data">
                <div class="perfil-avatar-row">
                    <div class="avatar-preview-wrap">
                        <img id="avatarPreview" src="<?php echo htmlspecialchars($avatarRuta, ENT_QUOTES, 'UTF-8'); ?>" alt="Previsualización del avatar">
                    </div>
                    <label class="upload-box" for="avatarInput">
                        <input id="avatarInput" type="file" name="avatar" accept="image/*">
                        <span>Cambiar foto</span>
                    </label>
                </div>

                <div class="perfil-form-grid">
                    <label>
                        <span>Nombre de usuario</span>
                        <input type="text" name="nickname" maxlength="50" value="<?php echo htmlspecialchars($_SESSION['usuario_nickname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="@tuusuario">
                    </label>
                    <label>
                        <span>Ubicación</span>
                        <input type="text" name="ubicacion" maxlength="100" value="<?php echo htmlspecialchars($_SESSION['usuario_ubicacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Salto, Uruguay">
                    </label>
                    <label class="full-width">
                        <span>Biografía</span>
                        <textarea name="biografia" maxlength="150" rows="4" placeholder="Cuéntanos algo sobre vos..."><?php echo htmlspecialchars($_SESSION['usuario_biografia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </label>
                    <label class="full-width">
                        <span>Enlace web / red social</span>
                        <input type="url" name="sitio_web" value="<?php echo htmlspecialchars($_SESSION['usuario_sitio_web'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://tuweb.com">
                    </label>
                </div>

                <div class="perfil-modal-actions">
                    <button type="button" class="perfil-btn-secondary" data-close-profile-modal>Cancelar</button>
                    <button type="submit" class="perfil-btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
    <div class="clima-widget-flotante" aria-live="polite">
        <span id="clima-icon" class="clima-icon">☁️</span>
        <span id="clima-temp" class="clima-temp">16.0°C</span>
        <span id="clima-humedad" class="clima-detalle">Humedad: 70%</span>
        <span id="clima-ith" class="clima-detalle">ITH: 15.8</span>
    </div>

    <script src="js/ith.js"></script>
</body>

</html>