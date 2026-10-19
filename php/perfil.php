<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$usuarioId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$usuarioId) {
    http_response_code(400);
    exit('Perfil no especificado.');
}

$consultaUsuario = $pdo->prepare('SELECT id, nombre_completo, nickname, verificado, biografia, avatar, avatar_crop_x, avatar_crop_zoom, banner, fecha_registro, perfil_publico FROM usuarios WHERE id = :id LIMIT 1');
$consultaUsuario->execute([':id' => $usuarioId]);
$usuario = $consultaUsuario->fetch();
if (!$usuario) {
    http_response_code(404);
    exit('Usuario no encontrado.');
}
$avatarCropX = max(0, min(100, (float) ($usuario['avatar_crop_x'] ?? 50)));
$avatarCropY = 50.0;
$avatarCropZoom = max(1, min(2.5, (float) ($usuario['avatar_crop_zoom'] ?? 1)));

$esPropio = !empty($_SESSION['usuario_id']) && (int) $_SESSION['usuario_id'] === $usuarioId;
$perfilPuedeGestionar = $esPropio && in_array(strtolower((string) ($_SESSION['rol'] ?? '')), ['turista', 'administrador', 'organizador'], true);
$seguido = false;
$solicitudPendiente = false;
$solicitudesRecibidas = [];
if (!empty($_SESSION['usuario_id']) && !$esPropio) {
    $consultaSeguimiento = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
    $consultaSeguimiento->execute([':seguidor' => (int) $_SESSION['usuario_id'], ':seguido' => $usuarioId]);
    $seguido = (bool) $consultaSeguimiento->fetchColumn();
    if (!$seguido) {
        $consultaSolicitudPendiente = $pdo->prepare('SELECT 1 FROM solicitudes_seguimiento WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
        $consultaSolicitudPendiente->execute([':seguidor' => (int) $_SESSION['usuario_id'], ':seguido' => $usuarioId]);
        $solicitudPendiente = (bool) $consultaSolicitudPendiente->fetchColumn();
    }
}
if ($perfilPuedeGestionar) {
    $consultaSolicitudesRecibidas = $pdo->prepare('SELECT s.seguidor_id, s.fecha_solicitud, u.nombre_completo, u.nickname, u.avatar FROM solicitudes_seguimiento s INNER JOIN usuarios u ON u.id = s.seguidor_id WHERE s.seguido_id = :seguido ORDER BY s.fecha_solicitud ASC');
    $consultaSolicitudesRecibidas->execute([':seguido' => $usuarioId]);
    $solicitudesRecibidas = $consultaSolicitudesRecibidas->fetchAll();
}

if (!(bool) $usuario['perfil_publico'] && !$esPropio && !$seguido) {
    $handlePrivado = htmlspecialchars((string) ($usuario['nickname'] ?: strtolower(str_replace(' ', '', (string) $usuario['nombre_completo']))), ENT_QUOTES, 'UTF-8');
    $avatarPrivado = perfilAssetUrl((string) ($usuario['avatar'] ?? ''), 'avatars', '../img/user.png');
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Perfil privado | SIGTUR</title>
        <link rel="stylesheet" href="../css/estilos.css">
        <link rel="stylesheet" href="../css/perfil.css?v=20261019-2">
    </head>
    <body class="perfil-publico-page">
        <main class="perfil-publico" data-user-id="<?= $usuarioId ?>" data-own-profile="0" data-profile-public="0" data-following="0" data-pending-follow="<?= $solicitudPendiente ? '1' : '0' ?>" data-csrf="<?= !empty($_SESSION['usuario_id']) ? htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') : '' ?>">
            <section class="perfil-private-card" aria-label="Vista limitada del perfil privado">
                <img class="perfil-private-card__avatar" src="<?= htmlspecialchars($avatarPrivado, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil" style="object-position: <?= number_format($avatarCropX, 2, '.', '') ?>% <?= number_format($avatarCropY, 2, '.', '') ?>%; transform: scale(<?= number_format($avatarCropZoom, 2, '.', '') ?>)">
                <p class="perfil-publico__handle">@<?= $handlePrivado ?><?php if ((bool) $usuario['verificado']): ?> <svg class="verified-badge" viewBox="0 0 24 24" role="img" aria-label="Cuenta verificada" focusable="false"><path d="M12 1.7 14.5 3l2.8-.2 1.1 2.6 2.4 1.5-.6 2.8 1.1 2.5-1.8 2.2-.3 2.8-2.7.8-1.8 2.2-2.7-.9-2.7.9-1.8-2.2-2.7-.8-.3-2.8-1.8-2.2 1.1-2.5-.6-2.8 2.4-1.5 1.1-2.6 2.8.2L12 1.7Zm-1.2 13.9 6-6-1.4-1.4-4.6 4.6-2.2-2.2-1.4 1.4 3.6 3.6Z"/></svg><?php endif; ?></p>
                <p class="perfil-private-card__notice">Este perfil es privado</p>
                <div class="perfil-stats-container perfil-private-card__stats" aria-label="Estadísticas del perfil">
                    <div class="stat-item"><span class="stat-numero" id="cnt-seguidores">0</span><span class="stat-etiqueta">Seguidores</span></div>
                    <div class="stat-divider" aria-hidden="true"></div>
                    <div class="stat-item"><span class="stat-numero" id="cnt-seguidos">0</span><span class="stat-etiqueta">Seguidos</span></div>
                    <div class="stat-divider" aria-hidden="true"></div>
                    <div class="stat-item"><span class="stat-numero" id="cnt-likes">0</span><span class="stat-etiqueta">Me gusta</span></div>
                </div>
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <button type="button" id="followButton" class="perfil-action perfil-action--primary" data-requires-login="0" <?= $solicitudPendiente ? 'disabled' : '' ?>><?= $solicitudPendiente ? 'Pendiente' : 'Seguir' ?></button>
                    <p class="perfil-follow-status" data-follow-status role="status" aria-live="polite"></p>
                <?php else: ?>
                    <a class="perfil-action perfil-action--primary" href="login.php">Iniciar sesión para seguir</a>
                <?php endif; ?>
                <a class="perfil-private-card__back" href="../index.php">Volver al inicio</a>
            </section>
        </main>
        <script src="../js/tema.js" defer></script>
        <script src="../js/perfil.js?v=20261018-follow-requests-1" defer></script>
        <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
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
$likesPorPublicacion = [];
$publicacionesPropias = array_values(array_filter(
    $publicaciones,
    static fn (array $publicacion): bool => $publicacion['origen'] === 'publicacion'
));
if ($publicacionesPropias) {
    $marcadoresLikes = [];
    $parametrosLikes = [];
    foreach ($publicacionesPropias as $indice => $publicacionPropia) {
        $marcador = ':publicacion_' . $indice;
        $marcadoresLikes[] = $marcador;
        $parametrosLikes[$marcador] = (int) $publicacionPropia['id'];
    }
    $usuarioLikeActual = (int) ($_SESSION['usuario_id'] ?? 0);
    $campoLikeActual = $usuarioLikeActual > 0
        ? 'SUM(CASE WHEN usuario_id = :usuario_actual THEN 1 ELSE 0 END)'
        : '0';
    if ($usuarioLikeActual > 0) {
        $parametrosLikes[':usuario_actual'] = $usuarioLikeActual;
    }
    $consultaLikes = $pdo->prepare('SELECT publicacion_id, COUNT(*) AS total, ' . $campoLikeActual . ' AS marcado
        FROM likes_publicaciones
        WHERE publicacion_id IN (' . implode(', ', $marcadoresLikes) . ')
        GROUP BY publicacion_id');
    $consultaLikes->execute($parametrosLikes);
    foreach ($consultaLikes->fetchAll() as $likePublicacion) {
        $likesPorPublicacion[(int) $likePublicacion['publicacion_id']] = [
            'total' => (int) $likePublicacion['total'],
            'marcado' => (int) $likePublicacion['marcado'] > 0,
        ];
    }
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
    <link rel="stylesheet" href="../css/perfil.css?v=20261019-2">
</head>
<body class="perfil-publico-page">
<main class="perfil-publico" data-user-id="<?= $usuarioId ?>" data-following="<?= $seguido ? '1' : '0' ?>" data-pending-follow="<?= $solicitudPendiente ? '1' : '0' ?>" data-own-profile="<?= $esPropio ? '1' : '0' ?>" data-profile-public="<?= (bool) $usuario['perfil_publico'] ? '1' : '0' ?>" data-csrf="<?= !empty($_SESSION['usuario_id']) ? htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') : '' ?>">
    <div class="profile-header-actions">
        <a href="../index.php" class="btn-volver-glass">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            <span>Volver</span>
        </a>
    </div>
    <div class="profile-header-tools">
        <div class="profile-search" data-profile-search>
            <label class="visually-hidden" for="profile-search-input">Buscar perfiles</label>
            <input id="profile-search-input" type="search" placeholder="Buscar perfiles" autocomplete="off" aria-controls="profile-search-results" aria-expanded="false" data-profile-search-input>
            <div class="profile-search__results" id="profile-search-results" role="listbox" aria-label="Resultados de perfiles" hidden data-profile-search-results></div>
            <p class="visually-hidden" role="status" aria-live="polite" data-profile-search-status></p>
        </div>
    <?php if ($perfilPuedeGestionar): ?>
        <div class="profile-options" data-profile-options data-api="api/preferencias_privacidad.php" data-csrf="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <button class="profile-options__toggle" type="button" aria-label="Configuración del perfil" title="Configuración" aria-expanded="false" aria-controls="profile-options-menu" data-profile-options-toggle>
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M19.4 13a7.8 7.8 0 0 0 0-2l2-1.5-2-3.4-2.4 1a7.4 7.4 0 0 0-1.7-1L15 3.5h-4l-.4 2.6a7.4 7.4 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.5a7.8 7.8 0 0 0 0 2l-2 1.5 2 3.4 2.4-1a7.4 7.4 0 0 0 1.7 1l.4 2.6h4l.4-2.6a7.4 7.4 0 0 0 1.7-1l2.4 1 2-3.4-2.1-1.5ZM13 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Z"/></svg>
            </button>
            <div class="profile-options__menu" id="profile-options-menu" hidden>
                <a href="configuracion.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19.4 13a7.8 7.8 0 0 0 0-2l2-1.5-2-3.4-2.4 1a7.4 7.4 0 0 0-1.7-1L15 3.5h-4l-.4 2.6a7.4 7.4 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.5a7.8 7.8 0 0 0 0 2l-2 1.5 2 3.4 2.4-1a7.4 7.4 0 0 0 1.7 1l.4 2.6h4l.4-2.6a7.4 7.4 0 0 0 1.7-1l2.4 1 2-3.4-2.1-1.5ZM13 15.5a3.5 3.5 0 1 1 0-7 3.5 3.5 0 0 1 0 7Z"/></svg>Configuración</a>
                <a href="personalizar-perfil.php"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm8.2-3.5a8.5 8.5 0 0 0-.1-1.3l1.5-1.2-1.5-2.6-1.8.7a7.8 7.8 0 0 0-2.2-1.3L15.8 4h-3.1l-.3 2.1a7.8 7.8 0 0 0-2.2 1.3l-1.8-.7-1.5 2.6 1.5 1.2a8.5 8.5 0 0 0 0 2l-1.5 1.2 1.5 2.6 1.8-.7a7.8 7.8 0 0 0 2.2 1.3l.3 2.1h3.1l.3-2.1a7.8 7.8 0 0 0 2.2-1.3l1.8.7 1.5-2.6-1.5-1.2c.1-.4.1-.8.1-1.2Z"/></svg>Personalizar perfil</a>
                <button type="button" data-profile-privacy-toggle><?= (bool) $usuario['perfil_publico'] ? 'Hacer perfil privado' : 'Hacer perfil público' ?></button>
                <?php if (!(bool) $usuario['perfil_publico']): ?>
                    <button type="button" data-follow-requests-toggle aria-expanded="false" aria-controls="profile-follow-requests">
                        Solicitudes de usuario <span data-follow-request-count><?= count($solicitudesRecibidas) ?></span>
                    </button>
                    <div class="perfil-follow-requests perfil-follow-requests--menu" id="profile-follow-requests" data-follow-requests-panel hidden>
                        <?php if (!$solicitudesRecibidas): ?>
                            <p class="perfil-follow-requests__empty">No hay solicitudes pendientes.</p>
                        <?php else: ?>
                            <div class="perfil-follow-requests__list">
                                <?php foreach ($solicitudesRecibidas as $solicitud):
                                    $avatarSolicitante = perfilAssetUrl((string) ($solicitud['avatar'] ?? ''), 'avatars', '../img/user.png');
                                    $nombreSolicitante = $solicitud['nickname'] ?: $solicitud['nombre_completo']; ?>
                                    <article class="perfil-follow-request" data-follow-request data-requester-id="<?= (int) $solicitud['seguidor_id'] ?>">
                                        <img src="<?= htmlspecialchars($avatarSolicitante, ENT_QUOTES, 'UTF-8') ?>" alt="">
                                        <div class="perfil-follow-request__identity">
                                            <strong><?= htmlspecialchars($nombreSolicitante, ENT_QUOTES, 'UTF-8') ?></strong>
                                            <span>Quiere seguir tu perfil</span>
                                        </div>
                                        <div class="perfil-follow-request__actions">
                                            <button type="button" data-follow-request-action="aceptar">Aceptar</button>
                                            <button type="button" data-follow-request-action="rechazar">Rechazar</button>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <form class="profile-options__logout" method="post" action="cerrar-sesion.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit">Cerrar sesión</button>
                </form>
                <p class="profile-options__status" data-profile-options-status role="status" aria-live="polite"></p>
            </div>
        </div>
    <?php endif; ?>
    </div>
    <section class="perfil-publico__hero">
        <div class="perfil-publico__identity">
            <div class="perfil-publico__avatar-frame">
                <img class="perfil-publico__avatar" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" style="object-position: <?= number_format($avatarCropX, 2, '.', '') ?>% <?= number_format($avatarCropY, 2, '.', '') ?>%; transform: scale(<?= number_format($avatarCropZoom, 2, '.', '') ?>)">
            </div>
            <div class="perfil-publico__details">
                <p class="perfil-publico__eyebrow">Perfil de usuario</p>
                <p class="perfil-publico__handle">@<?= htmlspecialchars($handle, ENT_QUOTES, 'UTF-8') ?><?php if ((bool) $usuario['verificado']): ?> <svg class="verified-badge" viewBox="0 0 24 24" role="img" aria-label="Cuenta verificada" focusable="false"><path d="M12 1.7 14.5 3l2.8-.2 1.1 2.6 2.4 1.5-.6 2.8 1.1 2.5-1.8 2.2-.3 2.8-2.7.8-1.8 2.2-2.7-.9-2.7.9-1.8-2.2-2.7-.8-.3-2.8-1.8-2.2 1.1-2.5-.6-2.8 2.4-1.5 1.1-2.6 2.8.2L12 1.7Zm-1.2 13.9 6-6-1.4-1.4-4.6 4.6-2.2-2.2-1.4 1.4 3.6 3.6Z"/></svg><?php endif; ?></p>
                <h1><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="perfil-publico__bio"><?= nl2br(htmlspecialchars($usuario['biografia'] ?: 'Este usuario todavía no ha agregado una biografía.', ENT_QUOTES, 'UTF-8')) ?></p>
                <p class="perfil-publico__joined">En SIGTUR desde <?= htmlspecialchars(date('F Y', strtotime($usuario['fecha_registro'])), ENT_QUOTES, 'UTF-8') ?></p>
                <div class="perfil-stats-container" aria-label="Estadísticas del perfil">
                    <button class="stat-item profile-stat-button" type="button" data-connection-list="seguidores" aria-label="Ver seguidores">
                        <span class="stat-numero" id="cnt-seguidores">0</span><span class="stat-etiqueta">Seguidores</span>
                    </button>
                    <div class="stat-divider" aria-hidden="true"></div>
                    <button class="stat-item profile-stat-button" type="button" data-connection-list="seguidos" aria-label="Ver usuarios que sigue">
                        <span class="stat-numero" id="cnt-seguidos">0</span><span class="stat-etiqueta">Seguidos</span>
                    </button>
                    <div class="stat-divider" aria-hidden="true"></div>
                    <div class="stat-item"><span class="stat-numero" id="cnt-likes">0</span><span class="stat-etiqueta">Me gusta</span></div>
                </div>
            </div>
            <?php if (!$esPropio): ?>
                <div class="perfil-publico__actions">
                    <button type="button" class="perfil-action perfil-action--primary<?= $seguido ? ' is-following' : '' ?>" id="followButton" <?= empty($_SESSION['usuario_id']) ? 'data-requires-login="1"' : '' ?> <?= $solicitudPendiente ? 'disabled' : '' ?>><?= $seguido ? 'Siguiendo' : ($solicitudPendiente ? 'Pendiente' : 'Seguir') ?></button>
                    <span class="perfil-follow-status" data-follow-status role="status" aria-live="polite"></span>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="perfil-publico__content" aria-labelledby="galleryTitle">
        <div class="perfil-publico__section-heading">
            <div><p class="perfil-kicker">Actividad pública</p><h2 id="galleryTitle">Fotos compartidas</h2></div>
            <div class="perfil-publico__section-tools">
                <span><?= count($publicaciones) ?> publicaciones</span>
                <?php if ($perfilPuedeGestionar): ?>
                    <div class="perfil-publico__publish" data-profile-publish>
                        <input type="file" accept="image/jpeg,image/png,image/webp" data-publication-input hidden>
                        <button type="button" class="perfil-publico__publish-button" data-publication-open aria-label="Publicar una imagen" title="Publicar una imagen">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 5v14M5 12h14"/></svg>
                        </button>
                        <span class="perfil-publico__publish-status" data-publication-status role="status" aria-live="polite"></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if (!$publicaciones): ?>
            <div class="perfil-empty"><p>Este usuario aún no ha compartido fotos</p></div>
        <?php else: ?>
            <div class="perfil-gallery">
                <?php foreach ($publicaciones as $publicacion):
                    $carpetaImagen = $publicacion['origen'] === 'galeria_eventos'
                        ? 'galeria-eventos'
                        : ($publicacion['origen'] === 'publicacion' ? 'publicaciones' : 'banners');
                    $imagen = $publicacion['origen'] === 'publicacion'
                        ? perfilAssetUrl((string) $publicacion['imagen'], 'publicaciones', perfilAssetUrl((string) $publicacion['imagen'], 'banners', '../img/porco.avif'))
                        : perfilAssetUrl((string) $publicacion['imagen'], $carpetaImagen, '../img/porco.avif');
                    $itemKey = $publicacion['origen'] === 'galeria_eventos'
                        ? 'galeria-' . $publicacion['imagen']
                        : 'publicacion-' . (int) $publicacion['id']; ?>
                    <article class="perfil-gallery__item<?= $publicacion['origen'] === 'publicacion' ? ' gallery-engagement-card' : '' ?>"<?= $publicacion['origen'] === 'publicacion' ? ' data-item-key="' . htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') . '" data-engagement="comments-only"' : '' ?>>
                        <button class="perfil-gallery__open" type="button" data-image="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" data-description="<?= htmlspecialchars($publicacion['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" data-date="<?= htmlspecialchars(date('d/m/Y', strtotime($publicacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?>" data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>" aria-label="Ver foto de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>">
                            <img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($publicacion['descripcion'] ?: 'Foto compartida por ' . $nombre, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                        </button>
                        <footer class="perfil-gallery__footer">
                            <div class="perfil-gallery__caption"><p><?= htmlspecialchars($publicacion['descripcion'] ?: '', ENT_QUOTES, 'UTF-8') ?></p><time datetime="<?= htmlspecialchars($publicacion['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(date('d/m/Y', strtotime($publicacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8') ?></time></div>
                            <?php if ($publicacion['origen'] === 'publicacion'):
                                $likePublicacion = $likesPorPublicacion[(int) $publicacion['id']] ?? ['total' => 0, 'marcado' => false]; ?>
                                <div class="perfil-gallery__card-actions">
                                    <button class="perfil-gallery__like<?= $likePublicacion['marcado'] ? ' is-liked' : '' ?>" type="button" data-publication-like data-publication-id="<?= (int) $publicacion['id'] ?>" data-requires-login="<?= empty($_SESSION['usuario_id']) ? '1' : '0' ?>" aria-pressed="<?= $likePublicacion['marcado'] ? 'true' : 'false' ?>" aria-label="Me gusta, <?= $likePublicacion['total'] ?>">
                                        <span class="perfil-gallery__like-icon" data-like-icon aria-hidden="true"><?= $likePublicacion['marcado'] ? '♥' : '♡' ?></span>
                                        <span data-like-count><?= $likePublicacion['total'] ?></span>
                                    </button>
                                    <?php if ($perfilPuedeGestionar): ?>
                                        <button class="perfil-gallery__delete" type="button" data-publication-delete data-publication-id="<?= (int) $publicacion['id'] ?>">Eliminar</button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<div class="perfil-connections-modal" id="perfil-connections-modal" aria-hidden="true" hidden>
    <button class="perfil-connections-modal__backdrop" type="button" data-connections-close aria-label="Cerrar lista"></button>
    <section class="perfil-connections-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="perfil-connections-title">
        <header class="perfil-connections-modal__header">
            <h2 id="perfil-connections-title">Seguidores</h2>
            <button type="button" class="perfil-connections-modal__close" data-connections-close aria-label="Cerrar">×</button>
        </header>
        <p class="perfil-connections-modal__status" data-connections-status role="status" aria-live="polite">Cargando usuarios…</p>
        <ul class="perfil-connections-list" data-connections-list></ul>
    </section>
</div>

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
<script src="../js/tema.js" defer></script>
<script src="../js/perfil.js?v=20261018-1" defer></script>
<?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
