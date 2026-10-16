<?php
session_start();
require_once __DIR__ . '/php/conexion.php';

// La portada solo necesita los identificadores de los dos eventos destacados.
$eventosPublicados = $pdo->query("SELECT id, slug FROM eventos WHERE slug IN ('porco-negro', 'bambola')")->fetchAll();
$eventosPorSlug = [];
foreach ($eventosPublicados as $eventoPublicado) {
    $eventosPorSlug[$eventoPublicado['slug']] = $eventoPublicado;
}
$rutaPorco = isset($eventosPorSlug['porco-negro']) ? 'php/evento.php?id=' . (int) $eventosPorSlug['porco-negro']['id'] : 'php/eventos.php';
$rutaBambola = isset($eventosPorSlug['bambola']) ? 'php/evento.php?id=' . (int) $eventosPorSlug['bambola']['id'] : 'php/eventos.php';
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
    <link rel="stylesheet" href="css/estilos.css?v=20261015-1"> <!-- Estilos principales -->
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
                            <a href="<?= htmlspecialchars($rutaPorco, ENT_QUOTES, 'UTF-8') ?>" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="img/porco.avif" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>La Bambola</h3>
                        <p>Todos los fines de semana!</p>
                        <div class="accion-boton">
                            <a href="<?= htmlspecialchars($rutaBambola, ENT_QUOTES, 'UTF-8') ?>" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>

                    <img src="img/bambi.jpg" alt="Evento">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Carrera en Bicicleta</h3>
                        <p>Recorrido en bicicleta por la ciudad.</p>
                        <div class="accion-boton">
                            <a href="php/eventos/carrera-de-bicicleta.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&amp;s=10" alt="Carrera en Bicicleta">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Halloween en Polo</h3>
                        <p>Una noche temática para celebrar.</p>
                        <div class="accion-boton">
                            <a href="php/eventos/polo.php" class="boton-amarillo">Ver mas</a>
                        </div>
                    </div>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&amp;s=10" alt="Halloween en Polo">
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
                        <h3>La Vieja Bodega</h3>
                        <p>Conocé La Vieja Bodega y consultá su ubicación desde la sección de gastronomía.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/r1.jfif" alt="La Vieja Bodega">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Trattoria</h3>
                        <p>Encontrá Trattoria entre los restaurantes de Salto.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/r2.jfif" alt="Trattoria">
                </div>
                <div class="slide">
                    <div class="info-evento">
                        <h3>Toscana</h3>
                        <p>Descubrí Toscana y consultá su ubicación en la página de gastronomía.</p>
                        <div class="accion-boton"><a href="php/gastronomia.php" class="boton-amarillo">Ver gastronomía</a></div>
                    </div>
                    <img src="img/r3.jfif" alt="Toscana">
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

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="php/lugar.php?slug=plaza-artigas" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="php/lugar.php?slug=plaza-treinta-y-tres-orientales" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="php/lugar.php?slug=costanera-norte" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="php/lugar.php?slug=costanera-sur" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="php/lugar.php?slug=parque-benito-solari" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col footer-col--expandable">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerGastronomy">GASTRONOMÍA</h4>
                    <span class="footer-toggle-icon" aria-hidden="true">▾</span>
                </button>
                <div class="footer-links">
                    <a href="php/gastronomia.php#restaurante-la-vieja-bodega" class="footer-link">La Vieja Bodega</a>
                    <a href="php/gastronomia.php#restaurante-trattoria" class="footer-link">Trattoria</a>
                    <a href="php/gastronomia.php#restaurante-toscana" class="footer-link">Toscana</a>
                    <a href="php/gastronomia.php#restaurante-punto-y-aparte" class="footer-link">Punto y Aparte</a>
                    <a href="php/gastronomia.php#restaurante-rodelu" class="footer-link">Rodelu</a>
                    <a href="php/gastronomia.php#restaurante-el-tony" class="footer-link">El Tony</a>
                    <a href="php/gastronomia.php#restaurante-la-canilla" class="footer-link">La Canilla</a>
                    <a href="php/gastronomia.php#restaurante-el-dominico" class="footer-link">El Dominico</a>
                    <a href="php/gastronomia.php#restaurante-la-2000" class="footer-link">La 2000</a>
                    <a href="php/gastronomia.php#restaurante-buffet-del-chef" class="footer-link">Buffet del Chef</a>
                    <a href="php/gastronomia.php#restaurante-don-diego" class="footer-link">Don Diego</a>
                    <a href="php/gastronomia.php#restaurante-gaucho" class="footer-link">GAUCHO</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="https://instagram.com/bapstuy" target="_blank" rel="noopener noreferrer" class="footer-link footer-link--icon">
                        <span class="social-icon">IG</span>Instagram
                    </a>
                    <a href="https://facebook.com/sigtur" target="_blank" rel="noopener noreferrer" class="footer-link footer-link--icon">
                        <span class="social-icon">FB</span>Facebook
                    </a>
                    <div class="footer-info-actions">
                        <button class="footer-info-button" type="button">Información</button>
                        <button class="footer-bapst-button" type="button">Sobre BAPST</button>
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
                        <img id="avatarPreview" src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Previsualización del avatar">
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
</body>

</html>
