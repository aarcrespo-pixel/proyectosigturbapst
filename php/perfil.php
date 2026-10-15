<?php
session_start();
require_once __DIR__ . '/conexion.php';

$usuarioId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$usuarioId) {
    http_response_code(400);
    exit('Perfil no especificado.');
}

$consultaUsuario = $pdo->prepare('SELECT id, nombre_completo, nickname, biografia, avatar, banner, fecha_registro, perfil_publico FROM usuarios WHERE id = :id LIMIT 1');
$consultaUsuario->execute([':id' => $usuarioId]);
$usuario = $consultaUsuario->fetch();
if (!$usuario) {
    http_response_code(404);
    exit('Usuario no encontrado.');
}

$esPropio = !empty($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === $usuarioId;
if (!(bool) $usuario['perfil_publico'] && !$esPropio) {
    http_response_code(403);
    $nombrePrivado = htmlspecialchars((string) $usuario['nombre_completo'], ENT_QUOTES, 'UTF-8');
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Perfil privado | SIGTUR</title>
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/perfil.css?v=20261015-2">
    </head>
    <body class="perfil-publico-page">
        <?php $headerCurrentPage = 'perfil'; $headerActivePage = ''; include __DIR__ . '/header.php'; ?>
        <main class="perfil-publico">
            <section class="perfil-empty" aria-live="polite">
                <h1>Este perfil es privado</h1>
                <p><?= $nombrePrivado ?> eligió limitar la visibilidad de su perfil.</p>
                <a href="../index.php">Volver al inicio</a>
            </section>
        </main>
    </body>
    </html>
    <?php
    exit;
}

/* El perfil reúne tanto publicaciones como fotos subidas a la galería de
   eventos, manteniendo el filtro por autor para no mostrar fotos ajenas. */
$consultaGaleria = $pdo->prepare("SELECT id, imagen, descripcion, fecha_creacion, 'publicacion' AS origen
    FROM publicaciones
    WHERE usuario_id = :usuario_publicacion
    UNION ALL
    SELECT id, imagen, descripcion, fecha_creacion, 'galeria_eventos' AS origen
    FROM galeria_eventos
    WHERE usuario_id = :usuario_galeria
    ORDER BY fecha_creacion DESC, id DESC");
$consultaGaleria->execute([
    ':usuario_publicacion' => $usuarioId,
    ':usuario_galeria' => $usuarioId,
]);
$publicaciones = $consultaGaleria->fetchAll();

$seguido = false;
if (!empty($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] !== $usuarioId) {
    $consultaSeguimiento = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id LIMIT 1');
    $consultaSeguimiento->execute([
        ':seguidor_id' => (int) $_SESSION['usuario_id'],
        ':seguido_id' => $usuarioId,
    ]);
    $seguido = (bool) $consultaSeguimiento->fetchColumn();
}

function perfilAssetUrl(string $value, string $folder, string $fallback): string
{
    $filename = basename($value);
    $path = __DIR__ . '/../uploads/' . $folder . '/' . $filename;
    return $filename !== '' && file_exists($path) ? '../uploads/' . $folder . '/' . rawurlencode($filename) : $fallback;
}

$avatarUrl = perfilAssetUrl((string) ($usuario['avatar'] ?? ''), 'avatars', '../img/user.png');
$bannerUrl = perfilAssetUrl((string) ($usuario['banner'] ?? ''), 'banners', '');
$nombre = (string) $usuario['nombre_completo'];
$handle = $usuario['nickname'] ?: strtolower(str_replace(' ', '', $nombre));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/perfil.css">
</head>
<body class="perfil-publico-page">
<?php $headerCurrentPage = 'perfil'; $headerActivePage = ''; include __DIR__ . '/header.php'; ?>
<main class="perfil-publico" data-user-id="<?= $usuarioId ?>" data-following="<?= $seguido ? '1' : '0' ?>" data-own-profile="<?= $esPropio ? '1' : '0' ?>">
    <!-- history.back() devuelve al contexto anterior sin imponer una ruta fija;
         resulta útil cuando el perfil se abrió desde un comentario o una foto. -->
    <div class="profile-header-actions" style="position: absolute; top: 20px; left: 20px; z-index: 100;">
        <button type="button" onclick="window.history.back()" class="btn-volver-glass" style="display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.5); color: #fff; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(8px); padding: 8px 16px; border-radius: 20px; cursor: pointer;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            <span>Volver</span>
        </button>
    </div>
    <section class="perfil-publico__hero">
        <div class="perfil-publico__banner<?= $bannerUrl === '' ? ' perfil-publico__banner--default' : '' ?>"<?= $bannerUrl !== '' ? ' style="background-image: url(\'' . htmlspecialchars($bannerUrl, ENT_QUOTES, 'UTF-8') . '\')"' : '' ?>></div>
        <div class="perfil-publico__identity">
            <img class="perfil-publico__avatar" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
            <div class="perfil-publico__details">
                <p class="perfil-publico__handle">@<?= htmlspecialchars($handle, ENT_QUOTES, 'UTF-8') ?></p>
                <h1><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="perfil-publico__bio"><?= nl2br(htmlspecialchars($usuario['biografia'] ?: 'Este usuario todavía no ha agregado una biografía.', ENT_QUOTES, 'UTF-8')) ?></p>
                <p class="perfil-publico__joined">En SIGTUR desde <?= htmlspecialchars(date('F Y', strtotime($usuario['fecha_registro'])), ENT_QUOTES, 'UTF-8') ?></p>
                <div class="perfil-stats-container" aria-label="Estadísticas del perfil">
                    <div class="stat-item"><span class="stat-numero" id="cnt-seguidores">0</span><span class="stat-etiqueta">Seguidores</span></div>
                    <div class="stat-divider" aria-hidden="true"></div>
                    <div class="stat-item"><span class="stat-numero" id="cnt-likes">0</span><span class="stat-etiqueta">Me gusta</span></div>
                </div>
            </div>
            <?php if (!$esPropio): ?>
                <div class="perfil-publico__actions">
                    <button type="button" class="perfil-action perfil-action--primary" id="followButton" <?= empty($_SESSION['usuario_id']) ? 'data-requires-login="1"' : '' ?>><?= $seguido ? 'Siguiendo' : 'Seguir' ?></button>
                    <button type="button" class="perfil-action perfil-action--secondary" id="messageButton" <?= empty($_SESSION['usuario_id']) ? 'data-requires-login="1"' : '' ?>>Mensaje</button>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="perfil-publico__content" aria-labelledby="galleryTitle">
        <div class="perfil-publico__section-heading"><div><p class="perfil-kicker">Actividad pública</p><h2 id="galleryTitle">Fotos compartidas</h2></div><span><?= count($publicaciones) ?> publicaciones</span></div>
        <?php if (!$publicaciones): ?>
            <div class="perfil-empty"><span aria-hidden="true">◌</span><p>Este usuario aún no ha compartido fotos</p></div>
        <?php else: ?>
            <div class="perfil-gallery">
                <?php foreach ($publicaciones as $publicacion):
                    $carpetaImagen = $publicacion['origen'] === 'galeria_eventos' ? 'galeria-eventos' : 'banners';
                    $imagen = perfilAssetUrl((string) $publicacion['imagen'], $carpetaImagen, '../img/porco.avif');
                    $itemKey = $publicacion['origen'] === 'galeria_eventos'
                        ? 'galeria-' . $publicacion['imagen']
                        : 'publicacion-' . (int) $publicacion['id']; ?>
                    <article class="perfil-gallery__item">
                        <button class="perfil-gallery__open" type="button" data-image="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" data-description="<?= htmlspecialchars($publicacion['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-date="<?= htmlspecialchars(date('d/m/Y', strtotime($publicacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?>" data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>" aria-label="Ver foto de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($publicacion['descripcion'] ?: 'Foto compartida por ' . $nombre, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </button>
                        <div><p><?= htmlspecialchars($publicacion['descripcion'] ?: '', ENT_QUOTES, 'UTF-8') ?></p><time datetime="<?= htmlspecialchars($publicacion['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(date('d/m/Y', strtotime($publicacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></time></div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<div class="perfil-lightbox" id="perfil-lightbox" aria-hidden="true">
    <button class="perfil-lightbox__backdrop" type="button" data-profile-gallery-close aria-label="Cerrar foto"></button>
    <section class="perfil-lightbox__dialog" role="dialog" aria-modal="true" aria-label="Foto del perfil">
        <button class="perfil-lightbox__close" type="button" data-profile-gallery-close aria-label="Cerrar foto">&times;</button>
        <div class="perfil-lightbox__media">
            <button class="perfil-lightbox__arrow perfil-lightbox__arrow--previous" type="button" aria-label="Foto anterior">&lsaquo;</button>
            <img id="perfil-lightbox-image" src="" alt="">
            <button class="perfil-lightbox__arrow perfil-lightbox__arrow--next" type="button" aria-label="Foto siguiente">&rsaquo;</button>
        </div>
        <aside class="perfil-lightbox__details">
            <div class="perfil-lightbox__info">
                <span class="perfil-kicker">Fotos compartidas</span>
                <h2 id="perfil-lightbox-title"></h2>
                <p id="perfil-lightbox-description"></p>
                <div class="perfil-lightbox__meta"><time id="perfil-lightbox-date"></time><span id="perfil-lightbox-count"></span></div>
            </div>
            <article class="gallery-engagement-card" id="perfil-lightbox-engagement"></article>
        </aside>
    </section>
</div>

<script src="../js/interacciones-tarjetas.js" defer></script>
<script src="../js/perfil.js?v=20261015-2" defer></script>
</body>
</html>
