<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/contenido_publico.php';
require_once __DIR__ . '/helpers/imagenes.php';

sigtur_inicializar_contenido_publico($pdo);
$propuestasGastronomicas = $pdo->query('SELECT id, nombre, imagen, descripcion, categoria, url_mapa FROM gastronomia_catalogo ORDER BY orden, nombre')->fetchAll(PDO::FETCH_ASSOC);
$categoriasGastronomicas = array_values(array_unique(array_filter(array_map(
    static fn (array $propuesta): string => trim((string) ($propuesta['categoria'] ?? '')),
    $propuestasGastronomicas
))));
foreach ($propuestasGastronomicas as &$propuesta) {
    $propuesta['imagen'] = sigtur_url_imagen_subida((string) $propuesta['imagen'], 'contenido-publico', '../img/gastronomia.jpg');
}
unset($propuesta);
?>
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
    <link rel="stylesheet" href="../css/gastronomia.css?v=20261019-2">
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
            <img src="../img/gastronomia.png" class="bottom-nav-icon" alt="Gastronomía">
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
            <div><strong><?= count($propuestasGastronomicas) ?></strong><span>lugares para descubrir</span></div>
            <div><strong><?= count($categoriasGastronomicas) ?></strong><span>categorías gastronómicas</span></div>
            <div><strong>Salto</strong><span>propuestas locales</span></div>
            <div><strong>Mapa</strong><span>ubicación en cada ficha</span></div>
        </section>

        <section class="seccion-gastronomia" id="propuestas-gastronomicas">
            <div class="seccion-gastronomia__encabezado">
                <span class="gastronomia-kicker">Elegí tu plan</span>
                <h2>Sabores para cada momento</h2>
                <p>Conocé los establecimientos gastronómicos publicados en SIGTUR, consultá la información de cada uno y encontrá su ubicación.</p>
            </div>
            <div class="gastronomia-filtros" role="tablist" aria-label="Filtrar propuestas gastronómicas">
                <label class="gastronomia-buscador">
                    <span class="visually-hidden">Buscar propuestas</span>
                    <input type="search" id="buscar-gastronomia" placeholder="Buscar una propuesta..." autocomplete="off">
                </label>
                <button class="gastronomia-filtro activo" type="button" data-gastronomia-filtro="todos" aria-pressed="true">Todos</button>
                <?php foreach ($categoriasGastronomicas as $categoria): ?>
                    <button class="gastronomia-filtro" type="button" data-gastronomia-filtro="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>" aria-pressed="false"><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?></button>
                <?php endforeach; ?>
            </div>
            <div class="gastronomia-grid" id="gastronomia-grid" aria-label="Lugares gastronómicos">
                <?php foreach ($propuestasGastronomicas as $propuesta):
                    $nombre = (string) $propuesta['nombre'];
                    $categoria = trim((string) ($propuesta['categoria'] ?? '')) ?: 'Gastronomía';
                    $descripcion = trim((string) ($propuesta['descripcion'] ?? ''));
                    if ($descripcion === '') {
                        $descripcion = 'Conocé ' . $nombre . ', una propuesta gastronómica de Salto. Consultá directamente con el establecimiento para obtener información actualizada.';
                    }
                    $urlMapa = trim((string) ($propuesta['url_mapa'] ?? ''));
                    if (filter_var($urlMapa, FILTER_VALIDATE_URL) === false || !str_starts_with(strtolower($urlMapa), 'https://')) {
                        $urlMapa = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($nombre . ', Salto, Uruguay');
                    }
                ?>
                    <article class="tarjeta-gastronomia" data-gastronomia-categoria="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= htmlspecialchars($propuesta['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        <div class="tarjeta-gastronomia__contenido">
                            <span class="tarjeta-gastronomia__meta"><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="tarjeta-gastronomia__nota">Para consultar la propuesta vigente, servicios o disponibilidad, comunicate directamente con el establecimiento.</p>
                            <a href="<?= htmlspecialchars($urlMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Ver ubicación <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="gastronomia-nota" id="gastronomia-sin-resultados" hidden>No se encontraron lugares con esos criterios.</p>
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
                    <span class="recorrido-gastronomia__duracion">Plan tranquilo</span>
                </article>
                <article class="recorrido-gastronomia recorrido-gastronomia--green">
                    <span class="recorrido-gastronomia__numero">02</span>
                    <h3>Río y sabores locales</h3>
                    <p>Atardecer en la Costanera, paseo al aire libre y una cena con parrilla o cocina regional.</p>
                    <span class="recorrido-gastronomia__duracion">Para compartir</span>
                </article>
                <article class="recorrido-gastronomia recorrido-gastronomia--blue">
                    <span class="recorrido-gastronomia__numero">03</span>
                    <h3>Plan familiar</h3>
                    <p>Salto Shopping, entretenimiento y opciones variadas para que cada persona elija su plato.</p>
                    <span class="recorrido-gastronomia__duracion">Una salida en familia</span>
                </article>
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

    </main>

    <script src="../js/script.js" defer></script>
    <script src="../js/gastronomia.js?v=20261019-2" defer></script>
</body>

</html>
