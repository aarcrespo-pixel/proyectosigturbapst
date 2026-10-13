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

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="../index.php" class="bottom-nav-item">
            <img src="../img/logoazul.png" class="bottom-nav-icon" alt="Inicio" data-i18n-alt="navHome">
            <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
        </a>
        <a href="eventos.php" class="bottom-nav-item">
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
        <a href="gastronomia.php" class="bottom-nav-item active">
            <img src="../img/gastronomia.jpg" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label">Gastronomía</span>
        </a>
    </nav>

    <main class="pagina-gastronomia">
        <section class="hero-gastronomia">
            <div class="hero-gastronomia__contenido">
                <span>Sabores de Salto</span>
                <h1>GASTRONOMÍA</h1>
                <p>Descubrí propuestas locales, productos regionales y lugares para disfrutar la identidad de Salto.</p>
            </div>
        </section>

        <section class="gastronomia-resumen" aria-label="Resumen gastronómico de Salto">
            <div><strong>12</strong><span>restaurantes para descubrir</span></div>
            <div><strong>Salto</strong><span>restaurantes locales</span></div>
            <div><strong>Maps</strong><span>ubicaciones para consultar</span></div>
            <div><strong>+</strong><span>opciones para distintos gustos</span></div>
        </section>

        <section class="seccion-gastronomia" id="propuestas-gastronomicas">
            <div class="seccion-gastronomia__encabezado">
                <span class="gastronomia-kicker">Restaurantes de Salto</span>
                <h2>Elegí dónde disfrutar</h2>
                <p>Explorá estos restaurantes y abrí su ubicación en el mapa. Consultá directamente con cada local sus horarios y propuestas.</p>
            </div>
            <div class="gastronomia-filtros">
                <label class="gastronomia-buscador">
                    <span class="visually-hidden">Buscar restaurantes</span>
                    <input type="search" id="buscar-gastronomia" placeholder="Buscar un restaurante..." autocomplete="off">
                </label>
            </div>
            <div class="gastronomia-grid" aria-label="Restaurantes de Salto">
                <?php
                $propuestasGastronomicas = [
                    ['La Vieja Bodega', '../img/r1.jfif'],
                    ['Trattoria', '../img/r2.jfif'],
                    ['Toscana', '../img/r3.jfif'],
                    ['Punto y Aparte', '../img/r4.jpg'],
                    ['Rodelu', '../img/r5.jfif'],
                    ['El Tony', '../img/r6.jfif'],
                    ['La Canilla', '../img/r7.jfif'],
                    ['El Dominico', '../img/r8.jfif'],
                    ['La 2000', '../img/gastronomia.jpg'],
                    ['Buffet del Chef', '../img/Trouville.jpg'],
                    ['Don Diego', '../img/Shopping_Salto.jpg'],
                    ['GAUCHO', '../img/feria_emprendedores.jpg'],
                ];
                foreach ($propuestasGastronomicas as [$nombre, $imagen]):
                    $restauranteId = 'restaurante-' . strtolower(str_replace(' ', '-', $nombre));
                    $busqueda = $nombre . ', Salto, Uruguay';
                    $urlMapa = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($busqueda);
                ?>
                    <article class="tarjeta-gastronomia" id="<?= htmlspecialchars($restauranteId, ENT_QUOTES, 'UTF-8') ?>" data-gastronomia-categoria="restaurante">
                        <img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="tarjeta-gastronomia__contenido">
                            <span class="tarjeta-gastronomia__meta">Restaurante</span>
                            <h3><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p>Consultá la propuesta, los horarios y los precios directamente con el restaurante.</p>
                            <a href="<?= htmlspecialchars($urlMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Ver ubicación <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="gastronomia-sin-resultados" id="gastronomia-sin-resultados" hidden>No encontramos restaurantes con ese nombre.</p>
            <p class="gastronomia-nota">Las ubicaciones se abren en Google Maps. Los horarios y las propuestas pueden cambiar; confirmalos directamente con cada restaurante.</p>
        </section>

        <section class="seccion-gastronomia favoritos-gastronomia" aria-labelledby="favoritos-gastronomia-titulo">
            <div class="seccion-gastronomia__encabezado">
                <span class="gastronomia-kicker">Ideas para salir</span>
                <h2 id="favoritos-gastronomia-titulo">Tres recorridos con sabor</h2>
                <p>Combiná gastronomía, paseo y cultura en una salida pensada para disfrutar sin apuro.</p>
            </div>
            <div class="recorridos-grid">
                <article class="recorrido-gastronomia recorrido-gastronomia--orange">
                    <span class="recorrido-gastronomia__numero">01</span>
                    <h3>Centro y merienda</h3>
                    <p>Plaza Artigas, una caminata por el centro y una pausa con café, torta o medialunas.</p>
                    <span class="recorrido-gastronomia__duracion">2 horas · plan tranquilo</span>
                </article>
                <article class="recorrido-gastronomia recorrido-gastronomia--green">
                    <span class="recorrido-gastronomia__numero">02</span>
                    <h3>Río y sabores locales</h3>
                    <p>Atardecer en la Costanera, paseo al aire libre y una cena con parrilla o cocina regional.</p>
                    <span class="recorrido-gastronomia__duracion">3 horas · para compartir</span>
                </article>
                <article class="recorrido-gastronomia recorrido-gastronomia--blue">
                    <span class="recorrido-gastronomia__numero">03</span>
                    <h3>Plan familiar</h3>
                    <p>Salto Shopping, entretenimiento y opciones variadas para que cada persona elija su plato.</p>
                    <span class="recorrido-gastronomia__duracion">Media jornada · familiar</span>
                </article>
            </div>
        </section>

        <section class="seccion-gastronomia datos-gastronomia" aria-labelledby="datos-gastronomia-titulo">
            <div class="datos-gastronomia__intro">
                <span class="gastronomia-kicker">Guía rápida</span>
                <h2 id="datos-gastronomia-titulo">Antes de salir</h2>
                <p>Pequeños datos que ayudan a planificar mejor una salida gastronómica por Salto.</p>
            </div>
            <div class="datos-gastronomia__lista">
                <div><strong>Mejor horario</strong><span>El centro suele tener más movimiento entre las 12:00 y las 14:00, y desde las 20:00.</span></div>
                <div><strong>Para probar</strong><span>Parrilla, chivitos, pastas caseras, jugos naturales y dulces de naranja.</span></div>
                <div><strong>Para ahorrar</strong><span>Buscá menús del día al mediodía y compartí meriendas o tablas.</span></div>
                <div><strong>Planificar</strong><span>Los horarios pueden cambiar en feriados, fines de semana y temporada alta.</span></div>
            </div>
        </section>

        <section class="seccion-gastronomia agenda-gastronomia" aria-labelledby="agenda-gastronomia-titulo">
            <div class="seccion-gastronomia__encabezado">
                <span class="gastronomia-kicker">Elegí el momento</span>
                <h2 id="agenda-gastronomia-titulo">Un día entero para comer rico</h2>
                <p>Ideas sencillas para convertir cualquier recorrido por Salto en una experiencia gastronómica.</p>
            </div>
            <div class="agenda-gastronomia__grid">
                <article><span>08:00</span><h3>Desayuno lento</h3><p>Café, medialunas, tostadas y jugo natural para arrancar el día con calma.</p><strong>Centro · $250 a $700</strong></article>
                <article><span>12:30</span><h3>Almuerzo salteño</h3><p>Menú del día, pasta casera, milanesa o una opción rápida para seguir paseando.</p><strong>Centro · $350 a $800</strong></article>
                <article><span>17:00</span><h3>Merienda de autor</h3><p>Tortas, dulces regionales y una pausa con amigos después de visitar la ciudad.</p><strong>Cafeterías · $300 a $900</strong></article>
                <article><span>21:00</span><h3>Cena para compartir</h3><p>Parrilla, chivito, pizza o una mesa abundante para cerrar el día junto al río.</p><strong>Centro y costanera · $700 a $2.200</strong></article>
            </div>
        </section>

        <section class="seccion-gastronomia productos-gastronomia" aria-labelledby="productos-gastronomia-titulo">
            <div class="productos-gastronomia__imagen">
                <img src="../img/festival_naranja.jpg" alt="Productos regionales de Salto">
            </div>
            <div class="productos-gastronomia__contenido">
                <span class="gastronomia-kicker">Para llevar un pedacito de Salto</span>
                <h2 id="productos-gastronomia-titulo">Productos regionales</h2>
                <p>La experiencia no termina en el restaurante. Ferias y emprendimientos locales convierten los ingredientes de la zona en recuerdos para compartir.</p>
                <ul>
                    <li><strong>Dulces de naranja</strong><span>Mermeladas, cascaritas confitadas y conservas.</span></li>
                    <li><strong>Jugos y cítricos</strong><span>Sabores frescos para disfrutar en temporada.</span></li>
                    <li><strong>Panificados caseros</strong><span>Tortas, bizcochos y recetas de tradición familiar.</span></li>
                    <li><strong>Canastas locales</strong><span>Una selección ideal para regalar o llevar de viaje.</span></li>
                </ul>
            </div>
        </section>

        <section class="seccion-gastronomia historia-gastronomia" aria-labelledby="historia-gastronomia-titulo">
            <div class="historia-gastronomia__intro">
                <span class="gastronomia-kicker">Identidad local</span>
                <h2 id="historia-gastronomia-titulo">La mesa de Salto cuenta una historia</h2>
                <p>La gastronomía salteña mezcla la cocina rioplatense, los productos del litoral y la vida de una ciudad que creció junto al río Uruguay. En sus mesas aparecen las carnes a la parrilla, las pastas, las harinas, los dulces caseros y los sabores de estación.</p>
            </div>
            <div class="historia-gastronomia__grid">
                <article>
                    <span class="historia-gastronomia__numero">01</span>
                    <h3>Una ciudad junto al río</h3>
                    <p>Salto se desarrolló como un punto de intercambio, trabajo y encuentro en el litoral. Esa mezcla de recorridos y culturas se refleja en una cocina sencilla, abundante y pensada para compartir.</p>
                </article>
                <article>
                    <span class="historia-gastronomia__numero">02</span>
                    <h3>La naranja como emblema</h3>
                    <p>El clima y los suelos del departamento favorecieron la producción citrícola. La naranja forma parte de la identidad productiva de Salto y aparece en jugos, mermeladas, postres, licores y ferias locales.</p>
                </article>
                <article>
                    <span class="historia-gastronomia__numero">03</span>
                    <h3>Sabores que siguen vivos</h3>
                    <p>Hoy restaurantes, cafeterías, ferias y emprendimientos reinterpretan esa historia con propuestas actuales, sin perder el vínculo con los productos y las costumbres de la región.</p>
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
                    <h4 data-i18n="footerPlaces">LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="lugar.php?slug=plaza-artigas" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="0">Plaza Artigas</a>
                    <a href="lugar.php?slug=plaza-treinta-y-tres-orientales" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="1">Plaza Treinta y Tres Orientales</a>
                    <a href="lugar.php?slug=costanera-norte" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="2">Costanera Norte</a>
                    <a href="lugar.php?slug=costanera-sur" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="3">Costanera Sur</a>
                    <a href="lugar.php?slug=parque-benito-solari" class="footer-link" data-i18n="footerPlaceLinks" data-i18n-index="4">Parque Benito Solari</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buscador = document.getElementById('buscar-gastronomia');
            const restaurantes = [...document.querySelectorAll('.tarjeta-gastronomia')];
            const mensajeSinResultados = document.getElementById('gastronomia-sin-resultados');

            buscador.addEventListener('input', () => {
                const consulta = buscador.value.trim().toLocaleLowerCase('es');
                let visibles = 0;

                restaurantes.forEach((restaurante) => {
                    const coincide = restaurante.textContent.toLocaleLowerCase('es').includes(consulta);
                    restaurante.hidden = !coincide;
                    if (coincide) visibles += 1;
                });

                mensajeSinResultados.hidden = visibles > 0;
            });
        });
    </script>
</body>

</html>
