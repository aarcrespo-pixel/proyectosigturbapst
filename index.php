<?php
session_start();
require_once __DIR__ . '/php/conexion.php';
require_once __DIR__ . '/php/helpers/imagenes.php';
require_once __DIR__ . '/php/helpers/contenido_publico.php';

sigtur_inicializar_contenido_publico($pdo);
$eventosDestacados = $pdo->query('SELECT id, titulo, descripcion, fecha, ubicacion, imagen_portada FROM eventos WHERE es_pasado = 0 AND fecha >= CURDATE() ORDER BY fecha ASC, id DESC LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);
$lugaresDestacados = $pdo->query("SELECT id, slug, nombre, descripcion, imagen FROM lugares WHERE seccion_publica <> 'oculto' ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
$gastronomiaDestacada = $pdo->query('SELECT nombre, descripcion, imagen FROM gastronomia_catalogo ORDER BY orden ASC, nombre ASC')->fetchAll(PDO::FETCH_ASSOC);
$resolverImagenIndex = static function (string $imagen, string $carpeta, string $fallback): string {
    $ruta = sigtur_url_imagen_subida($imagen, $carpeta, $fallback);
    return str_starts_with($ruta, '../') ? substr($ruta, 3) : $ruta;
};
$resumirTextoIndex = static function (string $texto, int $limite = 180): string {
    $texto = trim(preg_replace('/\s+/', ' ', $texto) ?? '');
    $longitud = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    if ($longitud <= $limite) {
        return $texto;
    }

    $resumen = function_exists('mb_substr') ? mb_substr($texto, 0, $limite, 'UTF-8') : substr($texto, 0, $limite);
    $ultimoEspacio = strrpos($resumen, ' ');
    if ($ultimoEspacio !== false) {
        $resumen = substr($resumen, 0, $ultimoEspacio);
    }

    return rtrim($resumen, " \t\n\r\0\x0B.,;:") . '…';
};
foreach ($eventosDestacados as &$eventoDestacado) {
    $eventoDestacado['imagen_portada'] = $resolverImagenIndex((string) ($eventoDestacado['imagen_portada'] ?? ''), 'eventos', 'img/porco.avif');
}
unset($eventoDestacado);
foreach ($lugaresDestacados as &$lugarDestacado) {
    $lugarDestacado['imagen'] = $resolverImagenIndex((string) $lugarDestacado['imagen'], 'contenido-publico', 'img/Plaza_artigas.webp');
}
unset($lugarDestacado);
foreach ($gastronomiaDestacada as &$propuestaGastronomica) {
    $propuestaGastronomica['imagen'] = $resolverImagenIndex((string) $propuestaGastronomica['imagen'], 'contenido-publico', 'img/gastronomia.png');
}
unset($propuestaGastronomica);
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

<body class="pagina-inicio">
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
            <img src="img/gastronomia.png" class="bottom-nav-icon" alt="Gastronomía">
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
                <img class="feature-image-gastronomia" src="img/gastronomia.png" alt="Propuesta gastronómica de Salto" loading="lazy">
                <h3 data-i18n="homeFeatureGastronomy">Gastronomía</h3>
                <button class="feature-toggle" aria-expanded="false">▼</button>
            </div>
            <div class="feature-text">
                <p data-i18n="homeFeatureGastronomyText1">Descubre sabores locales, cafeterías y propuestas para disfrutar cada momento.</p>
                <p data-i18n="homeFeatureGastronomyText2">Encuentra opciones para compartir, conocer la identidad de Salto y volver por más.</p>
            </div>
        </div>

    </section>

    <section class="seccion-eventos" data-carrusel-seccion="eventos">
        <h2 data-i18n="homeEventsTitle">Eventos Destacados</h2>
        <div class="carrusel" data-carrusel="eventos">
            <button class="flecha izquierda" type="button" aria-label="Evento anterior"></button>
            <div class="contenedor-slides">
                <?php if ($eventosDestacados): ?>
                    <?php foreach ($eventosDestacados as $indiceEvento => $eventoDestacado): ?>
                        <div class="slide<?= $indiceEvento === 0 ? ' activo' : '' ?>">
                            <div class="info-evento">
                                <h3><?= htmlspecialchars((string) $eventoDestacado['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($resumirTextoIndex((string) ($eventoDestacado['descripcion'] ?: $eventoDestacado['ubicacion'])), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="accion-boton">
                                    <a href="php/evento.php?id=<?= (int) $eventoDestacado['id'] ?>" class="boton-amarillo">Ver más</a>
                                </div>
                            </div>
                            <img src="<?= htmlspecialchars((string) $eventoDestacado['imagen_portada'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $eventoDestacado['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="slide activo">
                        <div class="info-evento">
                            <h3>No hay eventos próximos</h3>
                            <p>Explorá la agenda completa para conocer las novedades.</p>
                            <div class="accion-boton"><a href="php/eventos.php" class="boton-amarillo">Ver eventos</a></div>
                        </div>
                        <img src="img/porco.avif" alt="Agenda de eventos" loading="lazy">
                    </div>
                <?php endif; ?>
            </div>

            <button class="flecha derecha" type="button" aria-label="Siguiente evento"></button>

        </div>

        <div class="indicadores">
            <?php for ($indice = 0, $total = max(1, count($eventosDestacados)); $indice < $total; $indice++): ?>
                <button class="indicador<?= $indice === 0 ? ' activo' : '' ?>" type="button" aria-label="Ver evento <?= $indice + 1 ?>"></button>
            <?php endfor; ?>
        </div>

    </section>

    <section class="seccion-eventos" data-carrusel-seccion="lugares-destacados">
        <h2 data-i18n="homePlacesTitle">Lugares destacados</h2>
        <div class="carrusel" data-carrusel="lugares-destacados">
            <button class="flecha izquierda" aria-label="Lugar anterior" data-i18n-aria-label="homePlacesPrev"></button>
            <div class="contenedor-slides">
                <?php if ($lugaresDestacados): ?>
                    <?php foreach ($lugaresDestacados as $indiceLugar => $lugarDestacado): ?>
                        <div class="slide<?= $indiceLugar === 0 ? ' activo' : '' ?>">
                            <div class="info-evento">
                                <h3><?= htmlspecialchars((string) $lugarDestacado['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($resumirTextoIndex((string) $lugarDestacado['descripcion']), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="accion-boton"><a href="php/lugar.php?slug=<?= rawurlencode((string) $lugarDestacado['slug']) ?>&amp;origen=lugares" class="boton-amarillo" data-i18n="homePlacesButton">Ver lugares</a></div>
                            </div>
                            <img src="<?= htmlspecialchars((string) $lugarDestacado['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $lugarDestacado['nombre'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="slide activo">
                        <div class="info-evento"><h3>Descubrí Salto</h3><p>Explorá los lugares turísticos de la ciudad.</p><div class="accion-boton"><a href="php/lugares.php" class="boton-amarillo">Ver lugares</a></div></div>
                        <img src="img/Plaza_artigas.webp" alt="Lugares de Salto" loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
            <button class="flecha derecha" aria-label="Siguiente lugar" data-i18n-aria-label="homePlacesNext"></button>
        </div>
        <div class="indicadores">
            <?php for ($indice = 0, $total = max(1, count($lugaresDestacados)); $indice < $total; $indice++): ?>
                <button class="indicador<?= $indice === 0 ? ' activo' : '' ?>" type="button" aria-label="Ver lugar <?= $indice + 1 ?>"></button>
            <?php endfor; ?>
        </div>
    </section>

    <section class="seccion-eventos" data-carrusel-seccion="gastronomia-destacada">
        <h2 data-i18n="homeGastronomyTitle">Gastronomía destacada</h2>
        <div class="carrusel" data-carrusel="gastronomia-destacada">
            <button class="flecha izquierda" aria-label="Lugar gastronómico anterior" data-i18n-aria-label="homeGastronomyPrev"></button>
            <div class="contenedor-slides">
                <?php if ($gastronomiaDestacada): ?>
                    <?php foreach ($gastronomiaDestacada as $indiceGastronomia => $propuestaGastronomica): ?>
                        <div class="slide<?= $indiceGastronomia === 0 ? ' activo' : '' ?>">
                            <div class="info-evento">
                                <h3><?= htmlspecialchars((string) $propuestaGastronomica['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($resumirTextoIndex((string) $propuestaGastronomica['descripcion']), ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="accion-boton"><a href="php/gastronomia.php#propuestas-gastronomicas" class="boton-amarillo" data-i18n="homeGastronomyButton">Ver gastronomía</a></div>
                            </div>
                            <img src="<?= htmlspecialchars((string) $propuestaGastronomica['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $propuestaGastronomica['nombre'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="slide activo">
                        <div class="info-evento"><h3>Sabores de Salto</h3><p>Descubrí las propuestas gastronómicas locales.</p><div class="accion-boton"><a href="php/gastronomia.php#propuestas-gastronomicas" class="boton-amarillo">Ver gastronomía</a></div></div>
                        <img src="img/gastronomia.png" alt="Gastronomía de Salto" loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
            <button class="flecha derecha" aria-label="Siguiente lugar gastronómico" data-i18n-aria-label="homeGastronomyNext"></button>
        </div>
        <div class="indicadores">
            <?php for ($indice = 0, $total = max(1, count($gastronomiaDestacada)); $indice < $total; $indice++): ?>
                <button class="indicador<?= $indice === 0 ? ' activo' : '' ?>" type="button" aria-label="Ver propuesta gastronómica <?= $indice + 1 ?>"></button>
            <?php endfor; ?>
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
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="php/lugares.php" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerGastronomy">GASTRONOMÍA</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="php/gastronomia.php#propuestas-gastronomicas" class="footer-link" data-i18n="footerGastronomyLinks" data-i18n-index="0">Restaurantes</a>
                    <a href="php/gastronomia.php#propuestas-gastronomicas" class="footer-link" data-i18n="footerGastronomyLinks" data-i18n-index="1">Cafés y propuestas locales</a>
                    <a href="php/gastronomia.php#propuestas-gastronomicas" class="footer-link" data-i18n="footerGastronomyLinks" data-i18n-index="2">Explorar gastronomía</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
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

    <script src="js/translator.js?v=20261019-i18n-4" defer></script>
    <script src="js/script.js?v=nav-reveal-hint-20261029-1"></script>
</body>

</html>