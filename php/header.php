<?php
require_once __DIR__ . '/helpers/autorizacion.php';
/* El header es un componente compartido: se ejecuta dentro de varias páginas,
   por eso primero garantiza una sesión disponible sin iniciar una duplicada. */
if (session_status() === PHP_SESSION_NONE) {
    sigtur_iniciar_sesion_segura();
}

if (!defined('BASE_URL')) {
    /* Calculamos la ruta pública del proyecto a partir del document root para
       que el mismo header funcione desde index.php y desde /php/*.php. */
    $documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    $projectBasePath = $documentRoot !== '' ? str_ireplace($documentRoot, '', $projectRoot) : '';
    define('BASE_URL', rtrim($projectBasePath, '/') . '/');
}

/* PHP_SELF identifica desde qué carpeta se incluyó el componente. Con ese dato
   calculamos prefijos relativos para que imágenes y enlaces funcionen tanto en
   la raíz como dentro de /php/ o /php/eventos/. */
$headerPageBase = $_SERVER['PHP_SELF'] ?? '';
// Centralizamos datos de sesión para que el menú refleje el usuario actual.
$headerUserLoggedIn = isset($_SESSION['usuario_id']);
$headerUserName = $_SESSION['usuario_nombre'] ?? 'Usuario';
$headerUserEmail = $_SESSION['usuario_email'] ?? '';
$headerUserRole = sigtur_normalizar_rol($_SESSION['rol'] ?? null);
$headerUserNickname = $_SESSION['usuario_nickname'] ?? (!empty($headerUserEmail) ? strtolower(strstr($headerUserEmail, '@', true) ?: $headerUserEmail) : 'usuario');
$headerUserAvatar = $_SESSION['usuario_avatar'] ?? 'user-default.png';
$headerAvatarCropX = max(0, min(100, (float) ($_SESSION['usuario_avatar_crop_x'] ?? 50)));
$headerAvatarCropZoom = max(1, min(2.5, (float) ($_SESSION['usuario_avatar_crop_zoom'] ?? 1)));
$headerAvatarCropStyle = 'object-position: ' . number_format($headerAvatarCropX, 2, '.', '') . '% 50%; transform: scale(' . number_format($headerAvatarCropZoom, 2, '.', '') . ');';

/* El avatar se resuelve con basename y file_exists: nunca usamos una ruta
    completa enviada por el usuario directamente en el atributo src. */
$headerAssetPrefix = basename(dirname($headerPageBase)) === 'php' ? '../' : '';
/* Solo permitimos nombres de archivo saneados para el avatar y comprobamos su
    existencia antes de construir la URL pública. */
$headerAvatarRoute = $headerAssetPrefix . 'img/user.png';
if (!empty($headerUserAvatar) && $headerUserAvatar !== 'user.png' && $headerUserAvatar !== 'user-default.png') {
    $avatarFilename = basename($headerUserAvatar);
    $avatarAbsolutePath = __DIR__ . '/../uploads/avatars/' . $avatarFilename;
    if (file_exists($avatarAbsolutePath)) {
        $headerAvatarRoute = $headerAssetPrefix . 'uploads/avatars/' . $avatarFilename;
    }
}

$headerCurrentPage = $headerCurrentPage ?? '';
$headerActivePage = $headerActivePage ?? '';
$headerMostrarClima = $headerCurrentPage !== 'perfil';
/* La navegación se modela como datos para poder iterarla y marcar la página
    activa sin duplicar cuatro bloques de HTML casi idénticos. */
$headerLinks = [
    ['label' => 'Eventos', 'url' => BASE_URL . 'php/eventos.php', 'page' => 'eventos'],
    ['label' => 'Turismo', 'url' => BASE_URL . 'php/turismo.php', 'page' => 'turismo'],
    ['label' => 'Lugares', 'url' => BASE_URL . 'php/lugares.php', 'page' => 'lugares'],
    ['label' => 'Gastronomía', 'url' => BASE_URL . 'php/gastronomia.php', 'page' => 'gastronomia'],
];
?>
<script src="<?= htmlspecialchars(BASE_URL . 'js/tema.js', ENT_QUOTES, 'UTF-8') ?>" defer></script>
<header class="menu-principal" id="menu-principal">
    <button class="hamburguesa" aria-label="Abrir menú" data-i18n-aria-label="navMenuOpen">
        ☰
    </button>

    <!-- El nav comparte BASE_URL para evitar rutas rotas en páginas profundas. -->
    <nav class="menu" aria-label="Menú principal">
        <a href="<?= BASE_URL ?>index.php" class="logo-link">
            <img src="<?= BASE_URL ?>img/logoblanco.png" class="logo-menu" alt="SIGTUR">
        </a>

        <?php /* Escapamos cada URL y etiqueta antes de imprimirlas porque vienen
             de una estructura reutilizable y forman HTML dinámico. */
        foreach ($headerLinks as $link): ?>
            <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>"
               class="<?php echo ($headerActivePage === $link['page'] || $headerCurrentPage === $link['page']) ? 'pagina-activa' : ''; ?>"
               data-i18n="nav<?php echo ucfirst($link['page']); ?>">
                <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- search.js convierte este control compartido en búsqueda live en todas las vistas. -->
    <div class="barra-busqueda" data-search-endpoint="<?= BASE_URL ?>php/api/buscar.php" data-search-results="<?= BASE_URL ?>php/buscar.php">
        <input id="global-search" type="search" autocomplete="off" data-i18n-placeholder="searchPlaceholder" placeholder="Buscar eventos" aria-label="Buscar eventos">
        <img src="<?= BASE_URL ?>img/lupa.png" alt="Buscar">
    </div>

    <?php if ($headerMostrarClima): ?>
    <div class="weather-widget" id="weather-widget" aria-live="polite" aria-label="Clima actual en Salto">
        <span class="weather-icon" id="weather-icon" aria-hidden="true">☁️</span>
        <span class="weather-condition" id="weather-condition">Cargando...</span>
        <span class="weather-temperature" id="weather-temperature">--°C</span>
        <span class="weather-humidity" id="weather-humidity">Humedad: --%</span>
        <span class="weather-ith ith-badge" id="weather-ith">ITH: --</span>
    </div>
    <?php endif; ?>

    <div class="perfil">
        <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
            <img src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Perfil" style="<?= htmlspecialchars($headerAvatarCropStyle, ENT_QUOTES, 'UTF-8') ?>">
        </button>

        <!-- El contenido del menú cambia según exista o no usuario autenticado. -->
        <div class="perfil-menu">
            <?php if ($headerUserLoggedIn): ?>
                <div class="perfil-user-header">
                    <img class="perfil-user-avatar" src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar del usuario">
                    <div class="perfil-user-meta">
                        <strong><?php echo htmlspecialchars($headerUserName, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="perfil-nickname">@<?php echo htmlspecialchars($headerUserNickname, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="perfil-email"><?php echo htmlspecialchars($headerUserEmail, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <a class="perfil-item" href="<?= BASE_URL ?>php/perfil.php?id=<?= (int) ($_SESSION['usuario_id'] ?? 0) ?>">Ver mi perfil</a>
                <a class="perfil-item" href="<?= BASE_URL ?>php/mis-eventos.php">Mis Eventos / Favoritos</a>
                <?php if ($headerUserRole === 'turista'): ?><a class="perfil-item" href="<?= BASE_URL ?>php/notificaciones.php">Avisos de administradores</a><?php endif; ?>
                <?php if (in_array($headerUserRole, ['turista', 'organizador'], true)): ?><a class="perfil-item" href="<?= BASE_URL ?>php/solicitud-rango.php"><?= $headerUserRole === 'organizador' ? 'Solicitud de cambio de rango' : 'Solicitud de rango' ?></a><?php endif; ?>
                <?php if (($_SESSION['rol'] ?? '') === 'administrador'): ?><a class="perfil-item" href="<?= BASE_URL ?>php/panel-admin.php">Panel de administración</a><?php elseif (($_SESSION['rol'] ?? '') === 'organizador'): ?><a class="perfil-item" href="<?= BASE_URL ?>php/panel-organizador.php">Panel de organizador</a><?php endif; ?>
            <?php else: ?>
                <div class="perfil-user-header">
                    <img class="perfil-user-avatar" src="<?php echo htmlspecialchars($headerAssetPrefix . 'img/user.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar invitado">
                    <div class="perfil-user-meta">
                        <strong>Invitado</strong>
                        <span class="perfil-nickname">@visitante</span>
                        <span class="perfil-email">invitado@ejemplo.com</span>
                    </div>
                </div>
                <a class="perfil-item" href="<?= BASE_URL ?>php/login.php" data-i18n="navLogin">Ingresar Usuario</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<button class="nav-reveal-hint" type="button" aria-controls="menu-principal" aria-expanded="true" aria-label="Mostrar secciones, clima y buscador" data-i18n-aria-label="navRevealHintLabel" hidden>
    <span class="nav-reveal-hint__arrow" aria-hidden="true">↑</span>
</button>
<button class="scroll-top-button" type="button" aria-label="Volver arriba" data-i18n-aria-label="scrollTopLabel" hidden>
    <span aria-hidden="true">↑</span>
</button>
<script src="<?= BASE_URL ?>js/volver-posicion.js" defer></script>

<?php if ($headerUserLoggedIn): ?>
<button
    type="button"
    hidden
    data-notificaciones-toggle
    data-user-id="<?= (int) ($_SESSION['usuario_id'] ?? 0) ?>"
    data-api="<?= BASE_URL ?>php/api/notificaciones.php"
    data-service-worker="<?= BASE_URL ?>sw.js"
    data-csrf="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>"
    aria-hidden="true"
    tabindex="-1"></button>
<?php endif; ?>

<?php if ($headerMostrarClima): ?><script src="<?= BASE_URL ?>js/weather.js" defer></script><?php endif; ?>
<script src="<?= BASE_URL ?>js/translator.js?v=20261019-i18n-4" defer></script>
<script src="<?= BASE_URL ?>js/search.js" defer></script>
<script src="<?= BASE_URL ?>js/notificaciones.js" defer></script>
<?php if ($headerCurrentPage === 'index'): ?>
<?php if ($headerUserLoggedIn && $headerUserRole === 'turista'): ?>
<script src="<?= BASE_URL ?>js/copilot_ai.js?v=20261019-history-delete-1" defer></script>
<?php endif; ?>
<?php $mensajesWidgetRoot = BASE_URL; include __DIR__ . '/mensajes-widget.php'; ?>
<?php endif; ?>

<?php if ($headerCurrentPage === 'index' && $headerUserLoggedIn && $headerUserRole === 'turista'): ?>
<div class="copilot-assistant" id="copilot-assistant" data-api="<?= BASE_URL ?>php/api/copilot_ai.php" data-csrf="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <button type="button" class="copilot-assistant__toggle" id="copilot-assistant-toggle" aria-label="Abrir asistente de IA" aria-expanded="false" aria-controls="copilot-assistant-panel" title="Abrir asistente Sigtur">
        <svg aria-hidden="true" viewBox="0 0 24 24" focusable="false">
            <path d="M12 3a2 2 0 0 1 2 2h2.2A3.8 3.8 0 0 1 20 8.8v6.4a3.8 3.8 0 0 1-3.8 3.8h-.7l-3.5 2.6-3.5-2.6h-.7A3.8 3.8 0 0 1 4 15.2V8.8A3.8 3.8 0 0 1 7.8 5H10a2 2 0 0 1 2-2Zm-4.2 5A.8.8 0 0 0 7 8.8v6.4a.8.8 0 0 0 .8.8h1.7l2.5 1.9 2.5-1.9h1.7a.8.8 0 0 0 .8-.8V8.8a.8.8 0 0 0-.8-.8H7.8ZM9 10a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm-6 4h6v1.5H9V14Z"/>
        </svg>
    </button>

    <div class="copilot-assistant__panel" id="copilot-assistant-panel" hidden>
        <div class="copilot-assistant__header">
            <div>
                <strong>Asistente IA</strong>
                <small>Tu guía de Salto</small>
            </div>
            <div class="copilot-assistant__header-actions">
                <button type="button" id="copilot-assistant-history" title="Ver chat anterior">Historial</button>
                <button type="button" id="copilot-assistant-clear" title="Limpiar chat actual">Limpiar chat</button>
            </div>
            <button type="button" class="copilot-assistant__close" id="copilot-assistant-close" aria-label="Cerrar asistente">×</button>
        </div>

        <div class="copilot-assistant__history" id="copilot-assistant-history-panel" hidden></div>

        <div class="copilot-assistant__messages" id="copilot-assistant-messages">
            <div class="copilot-assistant__message copilot-assistant__message--bot">
                Hola 👋 Soy el asistente virtual de Sigtur Salto. ¿En qué puedo ayudarte?
            </div>
        </div>

        <form class="copilot-assistant__form" id="copilot-assistant-form">
            <textarea id="copilot-assistant-input" rows="3" maxlength="800" placeholder="Escribí tu pregunta..." required></textarea>
            <button type="submit" id="copilot-assistant-submit">Enviar</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php /* El modal solo se monta para usuarios autenticados; así no se exponen
         controles de edición de perfil a visitantes anónimos. */
if ($headerUserLoggedIn): ?>
<div class="perfil-modal-backdrop" id="perfilModal" aria-hidden="true">
    <div class="perfil-modal" role="dialog" aria-modal="true" aria-labelledby="perfilModalTitle">
        <div class="perfil-modal-header">
            <div>
                <p class="modal-kicker">Cuenta</p>
                <h2 id="perfilModalTitle">Personalizar Perfil</h2>
            </div>
            <button type="button" class="perfil-modal-close" data-close-profile-modal aria-label="Cerrar">×</button>
        </div>

        <!-- multipart/form-data permite enviar avatar y banner junto con campos de texto. -->
        <form class="perfil-form" method="post" action="<?= BASE_URL ?>php/personalizar-perfil.php" enctype="multipart/form-data">
            <div class="perfil-avatar-row">
                <div class="avatar-preview-wrap">
                    <img id="avatarPreview" src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Previsualización del avatar">
                </div>
                <label class="upload-box" for="avatarInput">
                    <input id="avatarInput" type="file" name="avatar" accept="image/*">
                    <span>Cambiar foto</span>
                </label>
            </div>

            <div class="perfil-form-grid">
                <label>
                    <span>Nombre de usuario</span>
                    <input type="text" name="nickname" maxlength="50" value="<?php echo htmlspecialchars($_SESSION['usuario_nickname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="@tuusuario">
                </label>
                <label>
                    <span>Ubicación</span>
                    <input type="text" name="ubicacion" maxlength="100" value="<?php echo htmlspecialchars($_SESSION['usuario_ubicacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Salto, Uruguay">
                </label>
                <label class="full-width">
                    <span>Biografía</span>
                    <textarea name="biografia" maxlength="150" rows="4" placeholder="Cuéntanos algo sobre vos..."><?php echo htmlspecialchars($_SESSION['usuario_biografia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>
                <label class="full-width">
                    <span>Enlace web / red social</span>
                    <input type="url" name="sitio_web" value="<?php echo htmlspecialchars($_SESSION['usuario_sitio_web'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://tuweb.com">
                </label>
            </div>

            <div class="perfil-modal-actions">
                <button type="button" class="perfil-btn-secondary" data-close-profile-modal>Cancelar</button>
                <button type="submit" class="perfil-btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
