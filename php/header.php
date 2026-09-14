<?php
/* El header es un componente compartido: se ejecuta dentro de varias páginas,
   por eso primero garantiza una sesión disponible sin iniciar una duplicada. */
if (session_status() === PHP_SESSION_NONE) {
    // Iniciamos la sesión solo si el archivo padre todavía no la abrió.
    session_start();
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
$headerUserNickname = $_SESSION['usuario_nickname'] ?? (!empty($headerUserEmail) ? strtolower(strstr($headerUserEmail, '@', true) ?: $headerUserEmail) : 'usuario');
$headerUserAvatar = $_SESSION['usuario_avatar'] ?? 'user-default.png';

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
/* La navegación se modela como datos para poder iterarla y marcar la página
    activa sin duplicar cuatro bloques de HTML casi idénticos. */
$headerLinks = [
    ['label' => 'Eventos', 'url' => BASE_URL . 'php/eventos.php', 'page' => 'eventos'],
    ['label' => 'Turismo', 'url' => BASE_URL . 'php/turismo.php', 'page' => 'turismo'],
    ['label' => 'Lugares', 'url' => BASE_URL . 'php/lugares.php', 'page' => 'lugares'],
    ['label' => 'Gastronomía', 'url' => BASE_URL . 'php/gastronomia.php', 'page' => 'gastronomia'],
];
?>
<header class="menu-principal">
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

    <div class="weather-widget" id="weather-widget" aria-live="polite" aria-label="Clima actual en Salto">
        <span class="weather-icon" id="weather-icon" aria-hidden="true">☁️</span>
        <span class="weather-condition" id="weather-condition">Cargando...</span>
        <span class="weather-temperature" id="weather-temperature">--°C</span>
        <span class="weather-humidity" id="weather-humidity">Humedad: --%</span>
        <span class="weather-ith ith-badge" id="weather-ith">ITH: --</span>
    </div>

    <div class="perfil">
        <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
            <img src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Perfil">
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
                <a class="perfil-item" href="<?= BASE_URL ?>php/personalizar-perfil.php" data-open-profile-modal>Personalizar Perfil</a>
                <a class="perfil-item" href="<?= BASE_URL ?>php/mis-eventos.php">Mis Eventos / Favoritos</a>
                <a class="perfil-item" href="<?= BASE_URL ?>php/configuracion.php" data-i18n="navSettings">Configuración</a>
                <a class="perfil-item" href="<?= BASE_URL ?>php/soporte.php" data-i18n="navSupport">Soporte</a>
                <div class="perfil-divider"></div>
                <a class="perfil-item perfil-item-logout" href="<?= BASE_URL ?>php/cerrar-sesion.php" data-i18n="navLogout">Cerrar Sesión</a>
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
                <a class="perfil-item" href="<?= BASE_URL ?>php/configuracion.php" data-i18n="navSettings">Configuración</a>
                <a class="perfil-item" href="<?= BASE_URL ?>php/soporte.php" data-i18n="navSupport">Soporte</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script src="<?= BASE_URL ?>js/weather.js" defer></script>
<script src="<?= BASE_URL ?>js/translator.js" defer></script>
<script src="<?= BASE_URL ?>js/search.js" defer></script>
<?php $mensajesWidgetRoot = BASE_URL; include __DIR__ . '/mensajes-widget.php'; ?>

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
