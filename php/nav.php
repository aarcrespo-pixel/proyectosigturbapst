<?php
$scriptPath = ltrim($_SERVER['PHP_SELF'] ?? '', '/');
$scriptDir = dirname($scriptPath);
$segments = array_values(array_filter(explode('/', $scriptDir), fn ($segment) => $segment !== ''));

$documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
$projectWebPath = $documentRoot !== '' ? trim(str_ireplace($documentRoot, '', $projectRoot), '/') : '';
$projectSegments = $projectWebPath === '' ? [] : array_values(array_filter(explode('/', $projectWebPath), fn ($segment) => $segment !== ''));
$rootBase = str_repeat('../', max(0, count($segments) - count($projectSegments)));
$phpBase = $segments[0] === 'php'
    ? str_repeat('../', max(0, count($segments) - 1))
    : '';

$navBase = $navBase ?? $phpBase;
$activePage = $activePage ?? '';
?>
<header class="menu-principal">
    <button class="hamburguesa" aria-label="Abrir menú" data-i18n-aria-label="navMenuOpen">
        ☰
    </button>
    <nav class="menu">
        <a href="<?= $rootBase ?>index.php" class="logo-link">
            <img src="<?= $rootBase ?>img/logoblanco.png" class="logo-menu" alt="SIGTUR">
        </a>
        <a href="<?= $navBase ?>eventos.php" class="<?= $activePage === 'eventos' ? 'pagina-activa' : '' ?>" data-i18n="navEvents">Eventos</a>
        <a href="<?= $navBase ?>turismo.php" data-i18n="navTourism">Turismo</a>
        <a href="<?= $navBase ?>lugares.php" data-i18n="navPlaces">Lugares</a>
        <a href="<?= $navBase ?>gastronomia.php" class="<?= $activePage === 'gastronomia' ? 'pagina-activa' : '' ?>" data-i18n="navGastronomy">Gastronomía</a>
    </nav>

    <div class="barra-busqueda" data-search-endpoint="<?= $rootBase ?>php/api/buscar.php" data-search-results="<?= $rootBase ?>php/buscar.php">
        <input id="global-search" type="search" autocomplete="off" data-i18n-placeholder="searchPlaceholder" placeholder="Buscar eventos" aria-label="Buscar eventos">
        <img src="<?= $rootBase ?>img/lupa.png" alt="Buscar">
    </div>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
$usuarioActual = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuarioEmailActual = $_SESSION['usuario_email'] ?? '';
$usuarioNicknameActual = $_SESSION['usuario_nickname'] ?? (!empty($usuarioEmailActual) ? strtolower(strstr($usuarioEmailActual, '@', true) ?: $usuarioEmailActual) : 'usuario');
$usuarioAvatarActual = $_SESSION['usuario_avatar'] ?? 'default-avatar.png';
$avatarFilename = basename($usuarioAvatarActual);
$avatarActualRuta = (!empty($usuarioAvatarActual) && $usuarioAvatarActual !== 'default-avatar.png' && $usuarioAvatarActual !== 'user-default.png' && file_exists(__DIR__ . '/../uploads/avatars/' . $avatarFilename))
    ? $rootBase . 'uploads/avatars/' . $avatarFilename
    : $rootBase . 'img/user.png';
$usuarioLogueadoActual = isset($_SESSION['usuario_id']);
?>
    <div class="perfil">
        <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
            <img src="<?= htmlspecialchars($avatarActualRuta, ENT_QUOTES, 'UTF-8'); ?>" alt="Perfil" data-i18n-alt="navProfile">
        </button>

        <div class="perfil-menu">
            <?php if ($usuarioLogueadoActual): ?>
                <div class="perfil-user-header">
                    <img class="perfil-user-avatar" src="<?= htmlspecialchars($avatarActualRuta, ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar del usuario">
                    <div class="perfil-user-meta">
                        <strong><?= htmlspecialchars($usuarioActual, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="perfil-nickname">@<?= htmlspecialchars($usuarioNicknameActual, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="perfil-email"><?= htmlspecialchars($usuarioEmailActual, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
                <a class="perfil-item" href="#" data-open-profile-modal>Personalizar Perfil</a>
                <a class="perfil-item" href="<?= $navBase ?>mis-eventos.php">Mis Eventos / Favoritos</a>
                <a class="perfil-item" href="<?= $navBase ?>configuracion.php" data-i18n="navSettings">Configuración</a>
                <a class="perfil-item" href="<?= $navBase ?>soporte.php" data-i18n="navSupport">Soporte</a>
                <div class="perfil-divider"></div>
                <a class="perfil-item perfil-item-logout" href="<?= $navBase ?>cerrar-sesion.php" data-i18n="navLogout">Cerrar Sesión</a>
            <?php else: ?>
                <div class="perfil-user-header">
                    <img class="perfil-user-avatar" src="<?= htmlspecialchars($rootBase . 'img/user.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar invitado">
                    <div class="perfil-user-meta">
                        <strong>Invitado</strong>
                        <span class="perfil-nickname">@visitante</span>
                        <span class="perfil-email">invitado@ejemplo.com</span>
                    </div>
                </div>
                <a class="perfil-item" href="<?= $navBase ?>login.php" data-i18n="navLogin">Ingresar Usuario</a>
                <a class="perfil-item" href="<?= $navBase ?>configuracion.php" data-i18n="navSettings">Configuración</a>
                <a class="perfil-item" href="<?= $navBase ?>soporte.php" data-i18n="navSupport">Soporte</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="weather-widget" id="weather-widget" aria-live="polite" aria-label="Clima actual en Salto">
        <span class="weather-icon" id="weather-icon" aria-hidden="true">☁️</span>
        <span class="weather-condition" id="weather-condition">Cargando...</span>
        <span class="weather-temperature" id="weather-temperature">--°C</span>
        <span class="weather-humidity" id="weather-humidity">Humedad: --%</span>
        <span class="weather-ith ith-badge" id="weather-ith">ITH: --</span>
    </div>
</header>

<script src="<?= $rootBase ?>js/weather.js" defer></script>
<script src="<?= $rootBase ?>js/translator.js" defer></script>
<script src="<?= $rootBase ?>js/search.js" defer></script>
<?php $mensajesWidgetRoot = $rootBase; include __DIR__ . '/mensajes-widget.php'; ?>

<div class="perfil-modal-backdrop" id="perfilModal" aria-hidden="true">
    <div class="perfil-modal" role="dialog" aria-modal="true" aria-labelledby="perfilModalTitle">
        <div class="perfil-modal-header">
            <div>
                <p class="modal-kicker">Cuenta</p>
                <h2 id="perfilModalTitle">Personalizar Perfil</h2>
            </div>
            <button type="button" class="perfil-modal-close" data-close-profile-modal aria-label="Cerrar">×</button>
        </div>

        <form class="perfil-form" method="post" action="personalizar-perfil.php" enctype="multipart/form-data">
            <div class="perfil-avatar-row">
                <div class="avatar-preview-wrap">
                    <img id="avatarPreview" src="<?= htmlspecialchars($avatarActualRuta, ENT_QUOTES, 'UTF-8'); ?>" alt="Previsualización del avatar">
                </div>
                <label class="upload-box" for="avatarInput">
                    <input id="avatarInput" type="file" name="avatar" accept="image/*">
                    <span>Cambiar foto</span>
                </label>
            </div>

            <div class="perfil-form-grid">
                <label>
                    <span>Nombre de usuario</span>
                    <input type="text" name="nickname" maxlength="50" value="<?= htmlspecialchars($_SESSION['usuario_nickname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="@tuusuario">
                </label>
                <label>
                    <span>Ubicación</span>
                    <input type="text" name="ubicacion" maxlength="100" value="<?= htmlspecialchars($_SESSION['usuario_ubicacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Salto, Uruguay">
                </label>
                <label class="full-width">
                    <span>Biografía</span>
                    <textarea name="biografia" maxlength="150" rows="4" placeholder="Cuéntanos algo sobre vos..."><?= htmlspecialchars($_SESSION['usuario_biografia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>
                <label class="full-width">
                    <span>Enlace web / red social</span>
                    <input type="url" name="sitio_web" value="<?= htmlspecialchars($_SESSION['usuario_sitio_web'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="https://tuweb.com">
                </label>
            </div>

            <div class="perfil-modal-actions">
                <button type="button" class="perfil-btn-secondary" data-close-profile-modal>Cancelar</button>
                <button type="submit" class="perfil-btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<nav class="bottom-nav" aria-label="Navegación inferior">
    <a href="<?= $rootBase ?>index.php" class="bottom-nav-item">
        <img src="<?= $rootBase ?>img/logoazul.png" class="bottom-nav-icon" alt="Inicio" data-i18n-alt="navHome">
        <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
    </a>
    <a href="<?= $navBase ?>eventos.php" class="bottom-nav-item <?= $activePage === 'eventos' ? 'active' : '' ?>">
        <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos" data-i18n-alt="navEvents">
        <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
    </a>
    <a href="<?= $navBase ?>turismo.php" class="bottom-nav-item">
        <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo" data-i18n-alt="navTourism">
        <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
    </a>
    <a href="<?= $navBase ?>lugares.php" class="bottom-nav-item">
        <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares" data-i18n-alt="navPlaces">
        <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
    </a>
    <a href="<?= $navBase ?>gastronomia.php" class="bottom-nav-item <?= $activePage === 'gastronomia' ? 'active' : '' ?>">
        <img src="../img/gastronomia.jpg" class="bottom-nav-icon" alt="Gastronomía">
        <span class="bottom-nav-label" data-i18n="navGastronomy">Gastronomía</span>
    </a>
</nav>
