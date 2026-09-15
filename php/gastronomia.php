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
            <div><strong>12</strong><span>propuestas para descubrir</span></div>
            <div><strong>04</strong><span>estilos de salida</span></div>
            <div><strong>$</strong><span>opciones para distintos presupuestos</span></div>
            <div><strong>100%</strong><span>identidad salteña</span></div>
        </section>

        <section class="seccion-gastronomia" id="propuestas-gastronomicas">
            <div class="seccion-gastronomia__encabezado">
                <span class="gastronomia-kicker">Elegí tu plan</span>
                <h2>Sabores para cada momento</h2>
                <p>Desde una merienda tranquila hasta una salida para compartir, encontrá propuestas con el sabor de Salto.</p>
            </div>
            <div class="gastronomia-filtros" role="tablist" aria-label="Filtrar propuestas gastronómicas">
                <label class="gastronomia-buscador">
                    <span class="visually-hidden">Buscar propuestas</span>
                    <input type="search" id="buscar-gastronomia" placeholder="Buscar una propuesta..." autocomplete="off">
                </label>
                <button class="gastronomia-filtro activo" type="button" data-gastronomia-filtro="todos">Todo</button>
                <button class="gastronomia-filtro" type="button" data-gastronomia-filtro="cafetería">Cafés</button>
                <button class="gastronomia-filtro" type="button" data-gastronomia-filtro="parrilla">Parrillas</button>
                <button class="gastronomia-filtro" type="button" data-gastronomia-filtro="almuerzo">Almuerzos</button>
                <button class="gastronomia-filtro" type="button" data-gastronomia-filtro="regional">Productos regionales</button>
            </div>
            <div class="gastronomia-carrusel" aria-label="Lugares gastronómicos destacados">
                <button class="gastronomia-carrusel__flecha gastronomia-carrusel__flecha--anterior" type="button" aria-label="Ver propuesta gastronómica anterior">‹</button>
                <div class="gastronomia-grid">
                <?php
                $propuestasGastronomicas = [
                    ['La Trouville', 'Cafetería · Para compartir', 'Una propuesta cálida para disfrutar café, algo rico y una buena charla en el centro.', '08:00 a 23:00', '$$ · $400 a $1.000', '../img/Trouville.jpg', 'La Trouville, Salto, Uruguay', ['Café y medialunas · $250', 'Tostado completo · $450', 'Torta del día · $380', 'Merienda para dos · $900']],
                    ['Salto Shopping', 'Patio de comidas · Familiar', 'Un punto de encuentro para pasear, hacer una pausa y elegir entre distintas propuestas.', '10:00 a 22:00', '$$ · $500 a $1.400', '../img/Shopping_Salto.jpg', 'Salto Shopping, Salto, Uruguay', ['Hamburguesa con papas · $650', 'Pizza individual · $550', 'Menú familiar · $1.400', 'Postre y bebida · $400']],
                    ['Cocina del centro', 'Almuerzo · Cocina casera', 'Propuestas de cocina casera, minutas y platos locales para un almuerzo completo.', '11:30 a 15:00', '$ · $350 a $800', '../img/carta.png', 'Restaurantes del centro, Salto, Uruguay', ['Menú del día · $550', 'Milanesa con guarnición · $650', 'Pasta casera · $600', 'Jugo natural · $220']],
                    ['Parrillas salteñas', 'Parrilla · Cocina regional', 'Una guía para descubrir cortes a la parrilla, ensaladas y sabores rioplatenses en la zona céntrica.', '12:00 a 15:00 y 20:00 a 00:00', '$$$ · $900 a $2.200', '../img/gastronomia.jpg', 'Parrillas del centro, Salto, Uruguay', ['Asado para dos · $1.600', 'Chivito al plato · $850', 'Ensalada completa · $350', 'Postre casero · $300']],
                    ['Cafés y meriendas', 'Café · Dulce y salado', 'Opciones para una pausa tranquila: café, tortas, medialunas y meriendas para compartir.', '09:00 a 20:00', '$ · $250 a $700', '../img/feria_emprendedores.jpg', 'Cafeterías del centro, Salto, Uruguay', ['Café con leche · $220', 'Medialunas x3 · $260', 'Tostadas y dulce · $350', 'Merienda completa · $700']],
                    ['Sabores de naranja', 'Producto regional · Para llevar', 'Probá dulces, jugos y productos inspirados en la naranja, uno de los símbolos productivos del departamento.', '09:00 a 19:00', '$ · $180 a $650', '../img/festival_naranja.jpg', 'Mercado y ferias de Salto, Uruguay', ['Jugo natural · $180', 'Mermelada artesanal · $280', 'Dulce de naranja · $320', 'Canasta regional · $650']],
                    ['Pizzerías de Salto', 'Pizza · Para compartir', 'Pizzas al molde, porciones y opciones para compartir una noche informal con amigos.', '19:00 a 00:30', '$$ · $450 a $1.300', '../img/shopping.jpg', 'Pizzerías del centro, Salto, Uruguay', ['Pizza muzzarella · $650', 'Pizza familiar · $1.100', 'Fainá · $180', 'Refresco grande · $220']],
                    ['Hamburguesas artesanales', 'Hamburguesas · Casual', 'Opciones abundantes, papas y combinaciones caseras para una salida rápida y sabrosa.', '19:30 a 00:00', '$$ · $500 a $1.200', '../img/carta.png', 'Hamburgueserías de Salto, Uruguay', ['Hamburguesa clásica · $550', 'Doble con papas · $850', 'Papas con cheddar · $420', 'Combo para dos · $1.200']],
                    ['Parrilla de barrio', 'Parrilla · Familiar', 'Cortes a la brasa, guarniciones y ambiente familiar para disfrutar la cocina rioplatense.', '11:30 a 15:00 y 20:00 a 23:30', '$$$ · $900 a $2.400', '../img/porco.avif', 'Parrillas de barrio, Salto, Uruguay', ['Vacío con guarnición · $950', 'Asado para dos · $1.700', 'Chorizo al pan · $300', 'Ensalada mixta · $350']],
                    ['Dulces y helados', 'Postres · Familiar', 'Helados, tortas y opciones dulces para cerrar una comida o disfrutar una tarde de paseo.', '14:00 a 23:00', '$ · $180 a $750', '../img/peñ.jpg', 'Heladerías de Salto, Uruguay', ['Cucurucho · $220', 'Copa helada · $380', 'Porción de torta · $420', 'Postre para compartir · $750']],
                    ['Feria de emprendedores', 'Feria · Producción local', 'Emprendimientos salteños con comidas caseras, conservas y productos hechos en la región.', '10:00 a 18:00', '$ · $150 a $800', '../img/feria_emprendedores.jpg', 'Feria de emprendedores, Salto, Uruguay', ['Torta casera · $280', 'Conserva regional · $300', 'Pan artesanal · $220', 'Canasta emprendedora · $800']],
                    ['Noche de picadas', 'Picadas · Para compartir', 'Tablas, quesos, fiambres y panes para armar una salida relajada con amigos o familia.', '20:00 a 00:00', '$$ · $700 a $1.800', '../img/gastronomia.jpg', 'Bares y picadas de Salto, Uruguay', ['Picada individual · $550', 'Tabla para dos · $1.100', 'Tabla familiar · $1.800', 'Jugo o refresco · $220']],
                ];
                foreach ($propuestasGastronomicas as [$nombre, $categoria, $descripcion, $horario, $precio, $imagen, $busqueda, $menu]):
                    $urlMapa = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($busqueda);
                ?>
                    <article class="tarjeta-gastronomia" data-gastronomia-categoria="<?= htmlspecialchars(strtolower($categoria), ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="tarjeta-gastronomia__contenido">
                            <span class="tarjeta-gastronomia__meta"><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                            <div class="tarjeta-gastronomia__datos">
                                <span><strong>Horario</strong><?= htmlspecialchars($horario, ENT_QUOTES, 'UTF-8') ?></span>
                                <span><strong>Precio orientativo</strong><?= htmlspecialchars($precio, ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <details class="tarjeta-gastronomia__menu">
                                <summary>Ver menú orientativo</summary>
                                <ul><?php foreach ($menu as $item): ?><li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
                            </details>
                            <a href="<?= htmlspecialchars($urlMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Ver ubicación <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
                </div>
                <button class="gastronomia-carrusel__flecha gastronomia-carrusel__flecha--siguiente" type="button" aria-label="Ver siguiente propuesta gastronómica">›</button>
            </div>
            <button class="gastronomia-ver-todos" type="button" aria-expanded="false">Ver todos los lugares gastronómicos</button>
            <p class="gastronomia-nota">Horarios y precios orientativos en pesos uruguayos. Confirmá la información con cada establecimiento antes de salir, especialmente en feriados y temporada alta.</p>
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
    <script src="../js/gastronomia.js" defer></script>
</body>

</html>
