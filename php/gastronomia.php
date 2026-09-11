<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastronomía | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css/gastronomia.css">
</head>

<body>
    <?php
    $headerCurrentPage = 'gastronomia';
    $headerActivePage = 'gastronomia';
    include __DIR__ . '/header.php';
    ?>

    <main class="pagina-gastronomia">
        <section class="hero-gastronomia">
            <div class="hero-gastronomia__contenido">
                <span>Sabores de Salto</span>
                <h1>GASTRONOMÍA</h1>
                <p>Descubrí propuestas locales, productos regionales y lugares para disfrutar la identidad de Salto.</p>
            </div>
        </section>

        <section class="seccion-gastronomia">
            <h2>Propuestas para disfrutar</h2>
            <div class="gastronomia-grid">
                <article class="tarjeta-gastronomia">
                    <img src="../img/gastronomia.jpg" alt="Gastronomía local">
                    <h3>Sabores locales</h3>
                    <p>Recorré opciones para conocer la cocina y los productos de la región.</p>
                </article>
                <article class="tarjeta-gastronomia">
                    <img src="../img/carta.png" alt="Carta de comidas">
                    <h3>Para cada momento</h3>
                    <p>Encontrá lugares para compartir una comida, una merienda o una salida especial.</p>
                </article>
                <article class="tarjeta-gastronomia">
                    <img src="../img/feria_emprendedores.jpg" alt="Feria de emprendedores">
                    <h3>Producción regional</h3>
                    <p>Conocé emprendimientos y ferias que celebran el talento gastronómico de Salto.</p>
                </article>
            </div>
        </section>

        <section class="seccion-gastronomia mapa-seccion" aria-labelledby="mapa-gastronomia-titulo">
            <div class="mapa-encabezado">
                <span class="mapa-kicker">Ubicaciones</span>
                <h2 id="mapa-gastronomia-titulo">Dónde disfrutar</h2>
                <p>Ubicá propuestas gastronómicas y lugares para salir en Salto.</p>
            </div>
            <div class="mapa-contenido">
                <iframe src="https://www.google.com/maps?q=Salto%2C%20Uruguay&output=embed" loading="lazy" title="Mapa gastronómico de Salto"></iframe>
                <div class="mapa-lugares">
                    <a href="https://www.google.com/maps/search/?api=1&query=La%20Trouville%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">La Trouville</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Cine%20Sarandi%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Cine Sarandí</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Salto%20Shopping%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Salto Shopping</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20Artigas%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Zona Plaza Artigas</a>
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
                    <a href="eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="0">Eventos destacados</a>
                    <a href="eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="1">Próximos eventos</a>
                    <a href="eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="2">Eventos anteriores</a>
                    <a href="eventos.php" class="footer-link" data-i18n="footerEventLinks" data-i18n-index="3">Todos los eventos</a>
                </div>
            </div>
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="lugares.php" class="footer-link">Plaza Artigas</a>
                    <a href="lugares.php" class="footer-link">Costanera Norte</a>
                    <a href="lugares.php" class="footer-link">Parque Benito Solari</a>
                </div>
            </div>
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4 data-i18n="footerTourism">TURISMO</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="0">Actividades</a>
                    <a href="turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="1">Lugares turísticos</a>
                    <a href="turismo.php" class="footer-link" data-i18n="footerTourismLinks" data-i18n-index="2">Experiencias</a>
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
</body>

</html>
