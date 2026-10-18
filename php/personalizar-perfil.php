<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/conexion.php';

// ========================
// FUNCIONES DE UTILIDAD
// ========================

/**
 * Procesa la subida de un archivo de imagen con logging detallado
 *
 * @param string $fileKey Clave del archivo en $_FILES
 * @param string $type Tipo de archivo ('avatar' o 'banner')
 * @param int $userId ID del usuario
 * @param string $sessionKey Clave de sesión actual
 * @return string Nombre del archivo guardado o valor anterior
 */
function processImageUpload($fileKey, $type, $userId, $sessionKey)
{
    /* Centralizamos avatar/banner para reutilizar validaciones, generar nombres
       únicos y evitar guardar rutas controladas por el usuario. */
    // Directorio según tipo
    $uploadDirs = [
        'avatar' => __DIR__ . '/../uploads/avatars/',
        'banner' => __DIR__ . '/../uploads/banners/',
    ];

    $dirUpload = $uploadDirs[$type] ?? null;

    if (!$dirUpload) {
        error_log("[{$type}] ERROR: Tipo de archivo inválido");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== VERIFICAR SI HAY ARCHIVO =====
    if (!isset($_FILES[$fileKey])) {
        error_log("[{$type}] No hay archivo en \$_FILES['{$fileKey}']");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    $file = $_FILES[$fileKey];

    // ===== VALIDAR ERRORES DE UPLOAD =====
    // El código de upload distingue archivo ausente, parcial, grande o bloqueado.
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE => 'Archivo supera post_max_size',
            UPLOAD_ERR_FORM_SIZE => 'Archivo supera max_file_size del formulario',
            UPLOAD_ERR_PARTIAL => 'Subida parcial',
            UPLOAD_ERR_NO_FILE => 'Sin archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Carpeta temporal no disponible',
            UPLOAD_ERR_CANT_WRITE => 'No se puede escribir en disco',
            UPLOAD_ERR_EXTENSION => 'Extensión bloqueada por PHP',
        ];
        $msgError = $errores[$file['error']] ?? 'Error desconocido (' . $file['error'] . ')';
        error_log("[{$type}] ERROR DE UPLOAD: {$msgError}");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== VALIDAR NOMBRE =====
    if (empty($file['name'])) {
        error_log("[{$type}] ERROR: Nombre de archivo vacío");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== VALIDAR EXTENSIÓN =====
    $nombreOriginal = basename($file['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // La whitelist de extensiones limita el tipo de contenido aceptado.
    if (!in_array($extension, $permitidas, true)) {
        error_log("[{$type}] ERROR: Extensión no permitida '{$extension}'. Permitidas: " . implode(', ', $permitidas));
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== CREAR DIRECTORIO SI NO EXISTE =====
    if (!is_dir($dirUpload)) {
        if (!@mkdir($dirUpload, 0755, true)) {
            error_log("[{$type}] ERROR: No se pudo crear directorio");
            return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
        }
    }

    // ===== VERIFICAR PERMISOS =====
    if (!is_writable($dirUpload)) {
        error_log("[{$type}] ERROR: Directorio no escribible");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== GENERAR NOMBRE ÚNICO =====
    $nombreArchivo = $type . '_' . $userId . '_' . time() . '.' . $extension;
    $rutaDestino = $dirUpload . $nombreArchivo;

    // ===== VALIDAR TMP_NAME =====
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        error_log("[{$type}] ERROR: tmp_name inválido o no es archivo subido");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== MOVER ARCHIVO =====
    // move_uploaded_file verifica que el origen provenga del mecanismo de upload de PHP.
    if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        error_log("[{$type}] ERROR: move_uploaded_file() falló");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    // ===== ELIMINAR ARCHIVO ANTERIOR =====
    $archivoAnterior = $_SESSION[$sessionKey] ?? 'default-' . $type . '.png';

    if (!empty($archivoAnterior) && strpos($archivoAnterior, 'default-') === false) {
        $rutaAnterior = $dirUpload . basename($archivoAnterior);

        if (file_exists($rutaAnterior)) {
            if (!@unlink($rutaAnterior)) {
                error_log("[{$type}] ERROR: No se pudo eliminar el archivo anterior");
            }
        }
    }

    return $nombreArchivo;
}

// ========================
// PROCESAR SOLICITUD POST
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) $_SESSION['usuario_id'];
    $consultaAvatarActual = $pdo->prepare('SELECT avatar, avatar_crop_x, avatar_crop_zoom FROM usuarios WHERE id = :id LIMIT 1');
    $consultaAvatarActual->execute([':id' => $userId]);
    $avatarActual = $consultaAvatarActual->fetch(PDO::FETCH_ASSOC);
    if (!$avatarActual) {
        http_response_code(404);
        exit('Usuario no encontrado.');
    }
    $_SESSION['usuario_avatar'] = (string) $avatarActual['avatar'];
    $avatarNombre = processImageUpload('avatar', 'avatar', $userId, 'usuario_avatar');
    $avatarCropX = max(0, min(100, (float) ($_POST['avatar_crop_x'] ?? $avatarActual['avatar_crop_x'])));
    $avatarCropY = 50.0;
    $avatarCropZoom = max(1, min(2.5, (float) ($_POST['avatar_crop_zoom'] ?? $avatarActual['avatar_crop_zoom'])));

    $nombre = trim($_POST['nombre'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');
    $biografia = trim($_POST['biografia'] ?? '');

    if ($nombre !== '' && strlen($nombre) > 100) {
        $nombre = substr($nombre, 0, 100);
    }

    if ($nickname !== '' && strlen($nickname) > 50) {
        $nickname = substr($nickname, 0, 50);
    }

    if ($biografia !== '' && strlen($biografia) > 150) {
        $biografia = substr($biografia, 0, 150);
    }

    $nombreFinal = $nombre !== '' ? $nombre : $_SESSION['usuario_nombre'];
    $nicknameFinal = $nickname !== '' ? $nickname : null;
    $biografiaFinal = $biografia !== '' ? $biografia : null;

     try {
         $stmt = $pdo->prepare(
            'UPDATE usuarios SET
                nombre_completo = :nombre,
                nickname = :nickname,
                biografia = :biografia,
                avatar = :avatar,
                avatar_crop_x = :avatar_crop_x,
                avatar_crop_y = :avatar_crop_y,
                avatar_crop_zoom = :avatar_crop_zoom
            WHERE id = :id'
        );

        $result = $stmt->execute([
            ':nombre' => $nombreFinal,
            ':nickname' => $nicknameFinal,
            ':biografia' => $biografiaFinal,
            ':avatar' => $avatarNombre,
            ':avatar_crop_x' => $avatarCropX,
            ':avatar_crop_y' => $avatarCropY,
            ':avatar_crop_zoom' => $avatarCropZoom,
            ':id' => $userId,
        ]);

        if (!$result) {
            throw new Exception("Error al ejecutar UPDATE: " . json_encode($stmt->errorInfo()));
        }

        // Actualizar sesión
        $_SESSION['usuario_nombre'] = $nombreFinal;
        $_SESSION['usuario_nickname'] = $nicknameFinal ?? strtolower(str_replace(' ', '', $nombreFinal ?? 'usuario'));
        $_SESSION['usuario_biografia'] = $biografiaFinal ?? '';
        $_SESSION['usuario_avatar'] = $avatarNombre;
        $_SESSION['usuario_avatar_crop_x'] = $avatarCropX;
        $_SESSION['usuario_avatar_crop_y'] = $avatarCropY;
        $_SESSION['usuario_avatar_crop_zoom'] = $avatarCropZoom;

        header('Location: perfil.php?id=' . $userId);
        exit;

    } catch (Exception $e) {
        error_log("EXCEPCIÓN: " . $e->getMessage());
        die("<h1>Error al guardar</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><p><a href='personalizar-perfil.php'>Volver</a></p>");
    }
}

// ========================
// CARGAR DATOS DEL USUARIO
// ========================
try {
    $userId = (int) $_SESSION['usuario_id'];

    // Cargamos el perfil con una consulta parametrizada para evitar exponer otro usuario.
    $stmt = $pdo->prepare('SELECT nombre_completo, nickname, biografia, avatar, avatar_crop_x, avatar_crop_zoom FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $userId]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        die("Error: Usuario no encontrado.");
    }

} catch (PDOException $e) {
    error_log("ERROR PDO al cargar usuario: " . $e->getMessage());
    die("Error al cargar datos del usuario.");
}

// Procesar y escapar datos
$nombre = htmlspecialchars($usuario['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8');
$nickname = htmlspecialchars($usuario['nickname'] ?? '', ENT_QUOTES, 'UTF-8');
$biografia = htmlspecialchars($usuario['biografia'] ?? '', ENT_QUOTES, 'UTF-8');
$avatarCropX = max(0, min(100, (float) ($usuario['avatar_crop_x'] ?? 50)));
$avatarCropY = 50.0;
$avatarCropZoom = max(1, min(2.5, (float) ($usuario['avatar_crop_zoom'] ?? 1)));
$avatarFilename = basename((string) ($usuario['avatar'] ?? ''));
$avatarUrl = $avatarFilename !== ''
    && $avatarFilename !== 'default-avatar.png'
    && $avatarFilename !== 'user-default.png'
    && is_file(__DIR__ . '/../uploads/avatars/' . $avatarFilename)
        ? '../uploads/avatars/' . rawurlencode($avatarFilename)
        : '../img/user.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Perfil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="../css/login.css?v=20261013-2">
    <link rel="stylesheet" href="../css/perfil.css?v=20261013-2">
</head>
<body>
    <main>
        <section class="contenedor">
            <h1>Editar Perfil</h1>
            <p class="intro">Actualiza tu foto, nombre, nombre de usuario y biografía.</p>

            <!-- Form -->
            <form method="POST" enctype="multipart/form-data" id="editForm" novalidate>
                <div class="profile-avatar-section">
                    <label class="avatar-container" for="avatarInput">
                        <img id="avatarImage" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Foto de perfil actual" class="avatar-image" width="100" height="100" style="object-position: <?= number_format($avatarCropX, 2, '.', '') ?>% <?= number_format($avatarCropY, 2, '.', '') ?>%; transform: scale(<?= number_format($avatarCropZoom, 2, '.', '') ?>)">
                        <span class="avatar-overlay"><span class="avatar-overlay-text">Cambiar foto</span></span>
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
                    <div class="avatar-adjust-controls" aria-label="Ajustar encuadre de la foto">
                        <div class="avatar-adjust-control">
                            <div><label for="avatarCropX">Encuadre horizontal</label><output id="avatarCropXValue"><?= (int) round($avatarCropX) ?>%</output></div>
                            <input type="range" id="avatarCropX" name="avatar_crop_x" min="0" max="100" step="1" value="<?= htmlspecialchars((string) $avatarCropX, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                        <div class="avatar-adjust-control">
                            <div><label for="avatarCropZoom">Zoom</label><output id="avatarCropZoomValue"><?= number_format($avatarCropZoom, 2, '.', '') ?>×</output></div>
                            <input type="range" id="avatarCropZoom" name="avatar_crop_zoom" min="1" max="2.5" step="0.05" value="<?= htmlspecialchars((string) $avatarCropZoom, ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    </div>
                </div>

                <!-- Nombre Real -->
                <div class="field-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" value="<?php echo $nombre; ?>" placeholder="Tu nombre completo" required>
                    <span class="char-counter"><span id="nombreCount">0</span>/100</span>
                </div>

                <div class="field-group">
                    <label for="nickname">Nombre de usuario</label>
                    <input type="text" id="nickname" name="nickname" maxlength="50" value="<?php echo $nickname; ?>" placeholder="@usuario">
                    <span class="char-counter"><span id="nicknameCount">0</span>/50</span>
                </div>

                <!-- Biografía -->
                <div class="field-group">
                    <label for="biografia">Biografía</label>
                    <textarea id="biografia" name="biografia" maxlength="150" placeholder="Cuéntanos algo sobre ti..."><?php echo $biografia; ?></textarea>
                    <span class="char-counter"><span id="biografiaCount">0</span>/150</span>
                </div>

                <!-- Botones -->
                <div class="button-group">
                    <a href="perfil.php?id=<?= (int) $_SESSION['usuario_id'] ?>" class="action-btn secondary back-link">Volver al perfil</a>
                    <button type="submit" class="action-btn primary">Guardar cambios</button>
                </div>
            </form>
        </section>
    </main>

    <script src="../js/translator.js" defer></script>
    <script src="../js/script.js" defer></script>
    <script>
        // Actualizar contadores de caracteres
        function setupCharCounter(inputId, countId, maxLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(countId);
            const counterElement = counter.parentElement;

            function updateCounter() {
                const length = input.value.length;
                counter.textContent = length;
                counterElement.classList.toggle('warning', length >= maxLength * 0.9);
            }

            input.addEventListener('input', updateCounter);
            updateCounter();
        }

        setupCharCounter('nombre', 'nombreCount', 100);
        setupCharCounter('nickname', 'nicknameCount', 50);
        setupCharCounter('biografia', 'biografiaCount', 150);

        const avatarInput = document.getElementById('avatarInput');
        const avatarImage = document.getElementById('avatarImage');
        const avatarCropX = document.getElementById('avatarCropX');
        const avatarCropZoom = document.getElementById('avatarCropZoom');
        let avatarPreviewUrl = '';
        const updateAvatarCropPreview = () => {
            avatarImage.style.objectPosition = `${avatarCropX.value}% 50%`;
            avatarImage.style.transform = `scale(${avatarCropZoom.value})`;
            document.getElementById('avatarCropXValue').textContent = `${avatarCropX.value}%`;
            document.getElementById('avatarCropZoomValue').textContent = `${Number(avatarCropZoom.value).toFixed(2)}×`;
        };
        [avatarCropX, avatarCropZoom].forEach((control) => control.addEventListener('input', updateAvatarCropPreview));
        avatarInput?.addEventListener('change', () => {
            const file = avatarInput.files?.[0];
            if (!file) return;
            if (avatarPreviewUrl) URL.revokeObjectURL(avatarPreviewUrl);
            avatarPreviewUrl = URL.createObjectURL(file);
            avatarImage.src = avatarPreviewUrl;
            updateAvatarCropPreview();
        });
    </script>
</body>
</html>
