<?php
session_start();
require_once __DIR__ . '/conexion.php';

$usuarioId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$usuarioId) {
    http_response_code(400);
    exit('Perfil no especificado.');
}

$consultaUsuario = $pdo->prepare('SELECT id, nombre_completo, nickname, biografia, avatar, banner, fecha_registro FROM usuarios WHERE id = :id LIMIT 1');
$consultaUsuario->execute([':id' => $usuarioId]);
$usuario = $consultaUsuario->fetch();
if (!$usuario) {
    http_response_code(404);
    exit('Usuario no encontrado.');
}

/* La relación usuario-galería usa una consulta parametrizada y ordena por fecha:
   así cada perfil solo expone sus publicaciones, sin mezclar fotos ajenas. */
$consultaGaleria = $pdo->prepare('SELECT id, imagen, descripcion, fecha_creacion FROM publicaciones WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC, id DESC');
$consultaGaleria->execute([':usuario_id' => $usuarioId]);
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
$esPropio = !empty($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === $usuarioId;
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
                    $imagen = perfilAssetUrl((string) $publicacion['imagen'], 'banners', '../img/porco.avif'); ?>
                    <article class="perfil-gallery__item"><img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($publicacion['descripcion'] ?: 'Foto compartida por ' . $nombre, ENT_QUOTES, 'UTF-8') ?>"><div><p><?= htmlspecialchars($publicacion['descripcion'] ?: '', ENT_QUOTES, 'UTF-8') ?></p><time datetime="<?= htmlspecialchars($publicacion['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(date('d/m/Y', strtotime($publicacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></time></div></article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<script src="../js/perfil.js" defer></script>
</body>
</html>
