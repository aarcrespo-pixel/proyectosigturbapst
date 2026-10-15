<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/contenido_publico.php';
sigtur_inicializar_contenido_publico($pdo);
$escapadas = $pdo->query("SELECT nombre, imagen, descripcion, detalle FROM contenido_turistico WHERE tipo = 'escapada' ORDER BY orden, id")->fetchAll();
$rutasTuristicas = $pdo->query("SELECT nombre, imagen, descripcion, detalle FROM contenido_turistico WHERE tipo = 'ruta' ORDER BY orden, id")->fetchAll();
$termasTuristicas = $pdo->query("SELECT nombre, imagen, descripcion, detalle, estrellas FROM contenido_turistico WHERE tipo = 'termas' ORDER BY orden, id")->fetchAll();
?>
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
    <link rel="stylesheet" href="../css/estilos.css?v=20261015-1">
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
                <?php foreach ($escapadas as $destino): ?>
                <article class="tarjeta-destino">
                    <img src="<?= htmlspecialchars($destino['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($destino['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="contenido">
                        <h3><?= htmlspecialchars($destino['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($destino['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="meta"><?= htmlspecialchars($destino['detalle'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="seccion-turismo">
            <h2 data-i18n="tourismSectionRoutesTitle">Rutas y experiencias</h2>
            <div class="rutas-grid">
                <?php foreach ($rutasTuristicas as $ruta): ?>
                <article class="tarjeta-ruta">
                    <img src="<?= htmlspecialchars($ruta['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($ruta['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="contenido">
                        <h3><?= htmlspecialchars($ruta['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($ruta['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="meta"><?= htmlspecialchars($ruta['detalle'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="seccion-turismo">
            <a href="#" class="boton-turismo" data-i18n="tourismDiscoverMore">Descubrir más</a>
        </section>

        <section class="seccion-turismo">
            <h2 data-i18n="tourismWaterparksTitle">Parques Acuáticos y Termas</h2>
            <div class="rutas-grid">
                <?php foreach ($termasTuristicas as $termas): ?>
                <?php $calificacion = (int) ($termas['estrellas'] ?? 0); ?>
                <article class="tarjeta-ruta">
                    <img src="<?= htmlspecialchars($termas['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($termas['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="contenido">
                        <h3><?= htmlspecialchars($termas['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($termas['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if ($calificacion > 0): ?><div class="hotel-info">
                            <div class="estrellas" aria-label="<?= $calificacion ?> estrellas"><?= str_repeat('★', $calificacion) . str_repeat('☆', 5 - $calificacion) ?></div>
                        </div><?php endif; ?>
                        <div class="meta"><?= htmlspecialchars($termas['detalle'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </article>
                <?php endforeach; ?>
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
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="../php/lugar.php?slug=plaza-artigas" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="../php/lugar.php?slug=plaza-treinta-y-tres-orientales" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="../php/lugar.php?slug=costanera-norte" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="../php/lugar.php?slug=costanera-sur" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="../php/lugar.php?slug=parque-benito-solari" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col footer-col--expandable">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerGastronomy">GASTRONOMÍA</h4>
                    <span class="footer-toggle-icon" aria-hidden="true">▾</span>
                </button>
                <div class="footer-links">
                    <a href="gastronomia.php#restaurante-la-vieja-bodega" class="footer-link">La Vieja Bodega</a>
                    <a href="gastronomia.php#restaurante-trattoria" class="footer-link">Trattoria</a>
                    <a href="gastronomia.php#restaurante-toscana" class="footer-link">Toscana</a>
                    <a href="gastronomia.php#restaurante-punto-y-aparte" class="footer-link">Punto y Aparte</a>
                    <a href="gastronomia.php#restaurante-rodelu" class="footer-link">Rodelu</a>
                    <a href="gastronomia.php#restaurante-el-tony" class="footer-link">El Tony</a>
                    <a href="gastronomia.php#restaurante-la-canilla" class="footer-link">La Canilla</a>
                    <a href="gastronomia.php#restaurante-el-dominico" class="footer-link">El Dominico</a>
                    <a href="gastronomia.php#restaurante-la-2000" class="footer-link">La 2000</a>
                    <a href="gastronomia.php#restaurante-buffet-del-chef" class="footer-link">Buffet del Chef</a>
                    <a href="gastronomia.php#restaurante-don-diego" class="footer-link">Don Diego</a>
                    <a href="gastronomia.php#restaurante-gaucho" class="footer-link">GAUCHO</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerSigtur">SIGTUR</h4>
                    <span class="footer-toggle-icon"></span>
                </button>
                <div class="footer-links">
                    <a href="https://instagram.com/bapstuy" target="_blank" rel="noopener noreferrer" class="footer-link footer-link--icon"><span class="social-icon">IG</span>Instagram</a>
                    <a href="https://facebook.com/sigtur" target="_blank" rel="noopener noreferrer" class="footer-link footer-link--icon"><span class="social-icon">FB</span>Facebook</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom" data-i18n="footerLegal">SIGTUR © 2026 by Bapst is licensed under CC BY-NC-ND 4.0</div>
    </footer>
    <script src="../js/script.js" defer></script>
    <script src="../js/lugar-links.js" defer></script>
</body> 

</html>