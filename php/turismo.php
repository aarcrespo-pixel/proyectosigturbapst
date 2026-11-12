<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/eventos_mapa.php';
require_once __DIR__ . '/helpers/contenido_publico.php';

sigtur_inicializar_contenido_publico($pdo);
$usuarioActual = sigtur_usuario_actual($pdo);
$eventosMapa = sigtur_eventos_visibles_en_mapa($pdo);
$puedeAgregarFotosTurismo = $usuarioActual && $usuarioActual['rol'] === 'organizador';
$slugsTuristicos = [
    'costanera-norte',
    'costanera-sur',
    'muelle-negro',
    'parque-benito-solari',
    'plaza-artigas',
    'plaza-treinta-y-tres-orientales',
    'biblioteca-municipal-salto',
    'museo-del-hombre-y-la-tecnologia',
    'termas-del-dayman',
    'termas-de-la-arapey',
    'acuamania',
];
$parametrosSlugsTuristicos = implode(',', array_fill(0, count($slugsTuristicos), '?'));
$lugaresTuristicosStmt = $pdo->prepare("SELECT slug, nombre, categoria, direccion, imagen, descripcion
    FROM lugares
    WHERE seccion_publica <> 'oculto' AND slug IN ({$parametrosSlugsTuristicos})
    ORDER BY FIELD(slug, {$parametrosSlugsTuristicos})");
$lugaresTuristicosStmt->execute(array_merge($slugsTuristicos, $slugsTuristicos));
$lugaresTuristicos = $lugaresTuristicosStmt->fetchAll();
foreach ($lugaresTuristicos as &$lugarTuristico) {
    $lugarTuristico['imagen_url'] = sigtur_url_imagen_subida((string) $lugarTuristico['imagen'], 'contenido-publico', '../img/porco.avif');
}
unset($lugarTuristico);

$notasImagenTurismo = [
    'biblioteca-municipal-salto' => 'Imagen ilustrativa de una biblioteca universitaria en Finlandia; no corresponde a la sede de Salto.',
    'muelle-negro' => 'Vista del río Uruguay desde Salto; no es una toma específica del muelle.',
];
$fotosTurismoStmt = $pdo->prepare("SELECT imagen, titulo FROM galeria_eventos WHERE tipo_evento = 'Turismo' ORDER BY fecha_creacion DESC, id DESC");
$fotosTurismoStmt->execute();
$fotosTurismo = $fotosTurismoStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="../css/turismo.css?v=20261112-tourism-catalog-1">
    <link rel="stylesheet" href="../css/galeria-lugares.css">
    <link rel="stylesheet" href="../css/legal.css">
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
        <a href="eventos.php" class="bottom-nav-item">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos" data-i18n-alt="navEvents">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item active" aria-current="page">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo" data-i18n-alt="navTourism">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item">
            <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares" data-i18n-alt="navPlaces">
            <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
        </a>
        <a href="gastronomia.php" class="bottom-nav-item">
            <img src="../img/gastronomia.png" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label">Gastronomía</span>
        </a>
    </nav>

    <main class="pagina-turismo">
        <section class="hero-turismo">
            <div class="hero-texto">
                <span>Descubrí el turismo en Salto</span>
                <h1>TURISMO</h1>
                <p>Termas, parques, plazas, museos, bibliotecas y paseos junto al río para conocer Salto a tu ritmo.</p>
            </div>
        </section>

        <section class="seccion-turismo" id="destinos">
            <div class="turismo-catalogo-heading">
                <div>
                    <span class="gallery-section-kicker">Guía local</span>
                    <h2>Destinos y experiencias</h2>
                    <p>Elegí un lugar para ver su información y planificar cómo llegar.</p>
                </div>
                <span class="gallery-section-count"><?= count($lugaresTuristicos) ?> destinos</span>
            </div>
            <div class="destinos-grid turismo-destinos-grid">
                <?php foreach ($lugaresTuristicos as $lugarTuristico):
                    $slugLugarTuristico = (string) $lugarTuristico['slug'];
                    $atribucionFoto = sigtur_atribucion_imagen_lugar($slugLugarTuristico);
                    $notaFoto = $notasImagenTurismo[$slugLugarTuristico] ?? '';
                    $textoAlternativo = $slugLugarTuristico === 'biblioteca-municipal-salto'
                        ? 'Estanterías de una biblioteca universitaria en Finlandia, imagen ilustrativa y no correspondiente a la sede de Salto'
                        : (string) $lugarTuristico['nombre'];
                ?>
                    <article class="tarjeta-destino turismo-destino-card" data-lugar-slug="<?= htmlspecialchars($slugLugarTuristico, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="tarjeta-destino__imagen">
                            <img src="<?= htmlspecialchars((string) $lugarTuristico['imagen_url'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($textoAlternativo, ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
                            <span class="tarjeta-destino__indicacion" aria-hidden="true">Tocá para conocer</span>
                        </div>
                        <div class="contenido">
                            <span class="turismo-tipo"><?= htmlspecialchars((string) $lugarTuristico['categoria'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h3><?= htmlspecialchars((string) $lugarTuristico['nombre'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p><?= htmlspecialchars((string) $lugarTuristico['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if ($notaFoto !== ''): ?>
                                <p class="turismo-imagen-nota"><?= htmlspecialchars($notaFoto, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                            <?php if ($atribucionFoto): ?>
                                <small class="turismo-imagen-credito">
                                    Foto: <a href="<?= htmlspecialchars($atribucionFoto['fuente'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($atribucionFoto['texto'], ENT_QUOTES, 'UTF-8') ?></a>
                                </small>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="seccion-turismo" id="galeria-turistica">
            <div class="gallery-section-heading">
                <div>
                    <span class="gallery-section-kicker">Fotos compartidas</span>
                    <h2>Galería de la comunidad</h2>
                    <p>Imágenes recientes de paseos y experiencias turísticas en Salto.</p>
                </div>
                <?php if ($fotosTurismo): ?>
                    <span class="gallery-section-count"><?= count($fotosTurismo) ?> fotos</span>
                <?php endif; ?>
            </div>
            <?php if (isset($_GET['fotos_agregadas'])): ?>
                <p class="turismo-galeria-feedback" role="status">
                    Se agregaron <?= max(1, (int) $_GET['fotos_agregadas']) ?> foto<?= (int) $_GET['fotos_agregadas'] === 1 ? '' : 's' ?> a la galería.
                    <?php if (isset($_GET['algunas_fotos_fallaron'])): ?>Algunas fotos no se pudieron subir.<?php endif; ?>
                </p>
            <?php endif; ?>
            <div class="galeria-grid gallery-photo-grid" id="galeria-turistica-grid">
                <?php foreach ($fotosTurismo as $fotoTurismo): ?>
                    <?php $fotoTurismoUrl = sigtur_url_imagen_subida((string) $fotoTurismo['imagen'], 'galeria-eventos', '../img/porco.avif'); ?>
                    <article class="tarjeta-galeria">
                        <img src="<?= htmlspecialchars($fotoTurismoUrl, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) $fotoTurismo['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" decoding="async">
                        <div class="contenido"><h3><?= htmlspecialchars((string) $fotoTurismo['titulo'], ENT_QUOTES, 'UTF-8') ?></h3></div>
                    </article>
                <?php endforeach; ?>
                <?php if ($puedeAgregarFotosTurismo): ?>
                    <form class="turismo-galeria-add" method="post" action="api/agregar_foto_galeria_publica.php" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="tipo_galeria" value="Turismo">
                        <input class="turismo-galeria-add__input" id="fotos-turismo" type="file" name="fotos[]" accept="image/jpeg,image/png,image/webp" multiple required>
                        <button class="turismo-galeria-add__button" type="button" aria-label="Agregar fotos a la galería turística" title="Agregar fotos">+</button>
                        <span class="turismo-galeria-add__hint">Compartí una foto de Salto</span>
                    </form>
                <?php elseif (!$fotosTurismo): ?>
                    <p class="turismo-galeria-vacia">Todavía no hay fotos compartidas. Los destinos destacados de arriba ya tienen imágenes asociadas a cada lugar.</p>
                <?php endif; ?>
            </div>
            <?php if ($fotosTurismo): ?>
                <button class="place-gallery-toggle" type="button" data-gallery-toggle aria-expanded="false" aria-controls="galeria-turistica-grid" hidden>Ver más</button>
            <?php endif; ?>
        </section>

        <section class="seccion-turismo mapa-seccion" aria-labelledby="mapa-turismo-titulo">
            <div class="mapa-encabezado">
                <span class="mapa-kicker">Ubicaciones</span>
                <h2 id="mapa-turismo-titulo">Planificá tu recorrido</h2>
                <p>Buscá los destinos turísticos confirmados y abrí su ubicación en el mapa.</p>
            </div>
            <div class="mapa-contenido">
                <iframe src="https://www.google.com/maps?q=Salto%2C%20Uruguay&output=embed" loading="lazy" title="Mapa turístico de Salto"></iframe>
                <div class="mapa-lugares" id="mapa-turismo-lista">
                    <label class="mapa-lugares__buscador">
                        <span class="visually-hidden">Buscar un lugar o evento en el mapa</span>
                        <input type="search" data-map-search placeholder="Buscar un lugar o evento..." autocomplete="off">
                    </label>
                    <?php foreach ($lugaresTuristicos as $lugarTuristico):
                        $consultaMapa = (string) $lugarTuristico['direccion'];
                        $urlMapa = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($consultaMapa);
                    ?>
                        <a href="<?= htmlspecialchars($urlMapa, ENT_QUOTES, 'UTF-8') ?>" data-map-query="<?= htmlspecialchars($consultaMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars((string) $lugarTuristico['nombre'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                    <?php foreach ($eventosMapa as $eventoMapa):
                        $ubicacionMapa = trim((string) $eventoMapa['ubicacion']);
                        $consultaMapa = $ubicacionMapa . ', Salto, Uruguay';
                        $urlMapaEvento = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($consultaMapa);
                    ?>
                        <a href="<?= htmlspecialchars($urlMapaEvento, ENT_QUOTES, 'UTF-8') ?>" data-map-query="<?= htmlspecialchars($consultaMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                            Evento: <?= htmlspecialchars((string) $eventoMapa['titulo'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($ubicacionMapa, ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                    <p class="mapa-lugares__sin-resultados" data-map-empty hidden>No se encontraron ubicaciones.</p>
                    <button class="mapa-lugares__toggle" id="mapa-turismo-toggle" type="button" aria-expanded="false" aria-controls="mapa-turismo-lista" hidden>Ver más</button>
                </div>
            </div>
        </section>
    </main>
    <footer class="legal-page-link"><a href="terminos-privacidad.php" data-i18n="legalPageLink">Términos, privacidad y seguridad</a></footer>

    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
    <script src="../js/lugar-links.js?v=20261112-tourism-catalog-1" defer></script>
    <script src="../js/galeria-lugares.js?v=20261103-gallery-more-1" defer></script>
    <?php if ($puedeAgregarFotosTurismo): ?>
        <script>
            const botonAgregarFotosTurismo = document.querySelector('.turismo-galeria-add__button');
            const selectorFotosTurismo = document.getElementById('fotos-turismo');
            botonAgregarFotosTurismo?.addEventListener('click', () => selectorFotosTurismo?.click());
            selectorFotosTurismo?.addEventListener('change', () => {
                if (selectorFotosTurismo.files.length > 0) selectorFotosTurismo.form.submit();
            });
        </script>
    <?php endif; ?>
</body>

</html>
