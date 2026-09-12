<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    $documentRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    $projectBasePath = $documentRoot !== '' ? str_ireplace($documentRoot, '', $projectRoot) : '';
    define('BASE_URL', rtrim($projectBasePath, '/') . '/');
}

$headerPageBase = $_SERVER['PHP_SELF'] ?? '';
$headerUserLoggedIn = isset($_SESSION['usuario_id']);
$headerUserName = $_SESSION['usuario_nombre'] ?? 'Usuario';
$headerUserEmail = $_SESSION['usuario_email'] ?? '';
$headerUserNickname = $_SESSION['usuario_nickname'] ?? (!empty($headerUserEmail) ? strtolower(strstr($headerUserEmail, '@', true) ?: $headerUserEmail) : 'usuario');
$headerUserAvatar = $_SESSION['usuario_avatar'] ?? 'user-default.png';

$headerAssetPrefix = basename(dirname($headerPageBase)) === 'php' ? '../' : '';
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

    <nav class="menu" aria-label="Menú principal">
        <a href="<?= BASE_URL ?>index.php" class="logo-link">
            <img src="<?= BASE_URL ?>img/logoblanco.png" class="logo-menu" alt="SIGTUR">
        </a>

        <?php foreach ($headerLinks as $link): ?>
            <a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>"
               class="<?php echo ($headerActivePage === $link['page'] || $headerCurrentPage === $link['page']) ? 'pagina-activa' : ''; ?>"
               data-i18n="nav<?php echo ucfirst($link['page']); ?>">
                <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="barra-busqueda">
        <input type="text" data-i18n-placeholder="searchPlaceholder" placeholder="Buscar eventos">
        <img src="<?= BASE_URL ?>img/lupa.png" alt="Buscar">
    </div>

    <div class="perfil">
        <button class="perfil-btn" aria-label="Abrir perfil" data-i18n-aria-label="navProfile">
            <img src="<?php echo htmlspecialchars($headerAvatarRoute, ENT_QUOTES, 'UTF-8'); ?>" alt="Perfil">
        </button>

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

<?php if ($headerUserLoggedIn): ?>
<div class="perfil-modal-backdrop" id="perfilModal" aria-hidden="true">
    <div class="perfil-modal" role="dialog" aria-modal="true" aria-labelledby="perfilModalTitle">
        <div class="perfil-modal-header">
            <div>
                <p class="modal-kicker">Cuenta</p>
                <h2 id="perfilModalTitle">Personalizar Perfil</h2>
            </div>
            <button type="button" class="perfil-modal-close" data-close-profile-modal aria-label="Cerrar">×</button>
        </div>

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
