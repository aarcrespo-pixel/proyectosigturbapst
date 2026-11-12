<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/contenido_publico.php';
require_once __DIR__ . '/helpers/imagenes.php';

sigtur_inicializar_contenido_publico($pdo);
$identificador = trim((string) ($_GET['id'] ?? $_GET['slug'] ?? ''));
if ($identificador === '') {
    http_response_code(400);
    exit('Restaurante no especificado.');
}

$slug = strtolower(preg_replace('/[^a-z0-9-]/', '', $identificador));
$consulta = ctype_digit($identificador)
    ? $pdo->prepare('SELECT slug, nombre, imagen, descripcion, categoria, url_mapa FROM gastronomia_catalogo WHERE id = :id LIMIT 1')
    : $pdo->prepare('SELECT slug, nombre, imagen, descripcion, categoria, url_mapa FROM gastronomia_catalogo WHERE slug = :slug LIMIT 1');
$consulta->execute(ctype_digit($identificador) ? [':id' => (int) $identificador] : [':slug' => $slug]);
$restaurante = $consulta->fetch(PDO::FETCH_ASSOC);
if (!$restaurante) {
    http_response_code(404);
    exit('Restaurante no encontrado.');
}

$nombre = (string) $restaurante['nombre'];
$descripcion = trim((string) ($restaurante['descripcion'] ?? ''));
if ($descripcion === '') {
    $descripcion = 'Descripción pendiente de completar.';
}
$categoria = trim((string) ($restaurante['categoria'] ?? '')) ?: 'Restaurante';
$imagen = htmlspecialchars(sigtur_url_imagen_subida((string) $restaurante['imagen'], 'contenido-publico', '../img/gastronomia.jpg'), ENT_QUOTES, 'UTF-8');
$nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
$direccionPendiente = 'Completar dirección del restaurante.';
$horarioPendiente = 'Completar horario del restaurante.';
$urlMapa = trim((string) ($restaurante['url_mapa'] ?? ''));
if (filter_var($urlMapa, FILTER_VALIDATE_URL) === false || !str_starts_with(strtolower($urlMapa), 'https://')) {
    $urlMapa = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($nombre . ', Salto, Uruguay');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nombreSeguro ?> | SIGTUR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilos.css?v=nav-reveal-hint-20261029-1">
    <link rel="stylesheet" href="../css/lugar.css">
    <link rel="stylesheet" href="../css/restaurante.css">
</head>
<body class="lugar-page restaurante-page">
    <?php $navBase = ''; $activePage = 'gastronomia'; include __DIR__ . '/nav.php'; ?>
    <main>
        <section class="lugar-hero" style="--lugar-image: url('<?= $imagen ?>')">
            <div class="lugar-hero__overlay"></div>
            <div class="lugar-hero__content">
                <a class="lugar-back" href="gastronomia.php#propuestas-gastronomicas">← Volver a Gastronomía</a>
                <span class="lugar-badge"><?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?></span>
                <h1 class="lugar-hero-titulo"><?= $nombreSeguro ?></h1>
                <p class="lugar-location">⌖ <?= htmlspecialchars($direccionPendiente, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </section>

        <div class="lugar-content">
            <section class="lugar-copy">
                <article class="lugar-module">
                    <p class="lugar-kicker">Descubrí Salto</p>
                    <h2 class="lugar-seccion-titulo">Sobre este restaurante</h2>
                    <p class="lugar-descripcion"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            </section>
            <aside class="lugar-useful">
                <h2 class="lugar-seccion-titulo">Información y ubicación</h2>
                <div class="useful-item">
                    <strong>Dirección</strong>
                    <span><?= htmlspecialchars($direccionPendiente, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="useful-item">
                    <strong>Horario</strong>
                    <span><?= htmlspecialchars($horarioPendiente, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <p class="restaurante-pendiente">Estos datos no están disponibles en el catálogo actual. Completalos cuando tengas la información confirmada.</p>
                <div class="restaurante-acciones">
                    <a class="map-link" href="<?= htmlspecialchars($urlMapa, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">Ver ubicación</a>
                    <a class="restaurante-volver" href="gastronomia.php#propuestas-gastronomicas">Volver a gastronomía</a>
                </div>
            </aside>
        </div>
    </main>
    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
</body>
</html>
