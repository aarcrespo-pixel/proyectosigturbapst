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
    error_log("[{$type}] Directorio configurado: {$dirUpload}");

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
    error_log("[{$type}] Archivo detectado: " . json_encode([
        'name' => $file['name'] ?? 'N/A',
        'size' => $file['size'] ?? 0,
        'error' => $file['error'] ?? 'N/A',
        'tmp_name' => $file['tmp_name'] ?? 'N/A',
    ]));

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
    error_log("[{$type}] ✓ Extensión válida: {$extension}");

    // ===== CREAR DIRECTORIO SI NO EXISTE =====
    if (!is_dir($dirUpload)) {
        error_log("[{$type}] Directorio no existe: {$dirUpload}");

        if (!@mkdir($dirUpload, 0755, true)) {
            error_log("[{$type}] ERROR: No se pudo crear directorio");
            return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
        }
        error_log("[{$type}] ✓ Directorio creado: {$dirUpload}");
    } else {
        error_log("[{$type}] ✓ Directorio existe: {$dirUpload}");
    }

    // ===== VERIFICAR PERMISOS =====
    if (!is_writable($dirUpload)) {
        error_log("[{$type}] ERROR: Directorio no escribible. Permisos: " . substr(sprintf('%o', fileperms($dirUpload)), -4));
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }
    error_log("[{$type}] ✓ Directorio escribible");

    // ===== GENERAR NOMBRE ÚNICO =====
    $nombreArchivo = $type . '_' . $userId . '_' . time() . '.' . $extension;
    $rutaDestino = $dirUpload . $nombreArchivo;
    error_log("[{$type}] Nombre de archivo: {$nombreArchivo}");
    error_log("[{$type}] Ruta destino: {$rutaDestino}");

    // ===== VALIDAR TMP_NAME =====
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        error_log("[{$type}] ERROR: tmp_name inválido o no es archivo subido");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }
    error_log("[{$type}] ✓ tmp_name válido: " . $file['tmp_name']);

    // ===== MOVER ARCHIVO =====
    // move_uploaded_file verifica que el origen provenga del mecanismo de upload de PHP.
    if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        error_log("[{$type}] ERROR: move_uploaded_file() falló");
        error_log("[{$type}] - Fuente: " . $file['tmp_name']);
        error_log("[{$type}] - Destino: " . $rutaDestino);
        error_log("[{$type}] - Destino existe antes: " . (file_exists($rutaDestino) ? 'SÍ' : 'NO'));
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }
    error_log("[{$type}] ✓ Archivo movido correctamente");

    // ===== VERIFICAR QUE SE GUARDÓ =====
    if (!file_exists($rutaDestino)) {
        error_log("[{$type}] ERROR: Archivo no encontrado después de move_uploaded_file");
        return $_SESSION[$sessionKey] ?? ('default-' . $type . '.png');
    }

    $tamaño = filesize($rutaDestino);
    error_log("[{$type}] ✓ Archivo verificado. Tamaño: {$tamaño} bytes");

    // ===== ELIMINAR ARCHIVO ANTERIOR =====
    $archivoAnterior = $_SESSION[$sessionKey] ?? 'default-' . $type . '.png';

    if (!empty($archivoAnterior) && strpos($archivoAnterior, 'default-') === false) {
        $rutaAnterior = $dirUpload . basename($archivoAnterior);

        if (file_exists($rutaAnterior)) {
            error_log("[{$type}] Eliminando archivo anterior: {$archivoAnterior}");
            if (@unlink($rutaAnterior)) {
                error_log("[{$type}] ✓ Archivo anterior eliminado");
            } else {
                error_log("[{$type}] ⚠ No se pudo eliminar archivo anterior");
            }
        } else {
            error_log("[{$type}] Archivo anterior no encontrado: {$rutaAnterior}");
        }
    }

    error_log("[{$type}] ✓✓✓ ÉXITO: {$nombreArchivo}");
    return $nombreArchivo;
}

// ========================
// PROCESAR SOLICITUD POST
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) $_SESSION['usuario_id'];

    // LOGGING: Inicio de procesamiento
    error_log("=== INICIO PROCESAMIENTO POST - Usuario: {$userId} ===");
    error_log("_FILES: " . json_encode(array_keys($_FILES)));
    error_log("_POST keys: " . json_encode(array_keys($_POST)));

    // Validar y limpiar datos de texto
    $nombre = trim($_POST['nombre'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');
    $biografia = trim($_POST['biografia'] ?? '');
    $ubicacion = trim($_POST['ubicacion'] ?? '');
    $sitioWeb = trim($_POST['sitio_web'] ?? '');

    // Aplicar límites de caracteres
    if ($nombre !== '' && strlen($nombre) > 100) {
        $nombre = substr($nombre, 0, 100);
    }

    if ($nickname !== '' && strlen($nickname) > 50) {
        $nickname = substr($nickname, 0, 50);
    }

    if ($ubicacion !== '' && strlen($ubicacion) > 100) {
        $ubicacion = substr($ubicacion, 0, 100);
    }

    if ($biografia !== '' && strlen($biografia) > 150) {
        $biografia = substr($biografia, 0, 150);
    }

    // Validar URL
    if ($sitioWeb !== '' && !filter_var($sitioWeb, FILTER_VALIDATE_URL)) {
        $sitioWeb = '';
    }

    // Procesar subidas de archivos
    error_log("Procesando avatar...");
    $avatarNombre = processImageUpload('avatar', 'avatar', $userId, 'usuario_avatar');
    error_log("Avatar resultante: {$avatarNombre}");

    error_log("Procesando banner...");
    $bannerNombre = processImageUpload('banner', 'banner', $userId, 'usuario_banner');
    error_log("Banner resultante: {$bannerNombre}");

    // Obtener valores actuales para la BD
    $nombreFinal = $nombre !== '' ? $nombre : $_SESSION['usuario_nombre'];
    $nicknameFinal = $nickname !== '' ? $nickname : null;
    $biografiaFinal = $biografia !== '' ? $biografia : null;
    $ubicacionFinal = $ubicacion !== '' ? $ubicacion : null;
    $sitioWebFinal = $sitioWeb !== '' ? $sitioWeb : null;

     /* El UPDATE usa parámetros nombrados para actualizar perfil y archivos en
         una sola operación; después sincronizamos la sesión con esos valores. */
     // Actualizar base de datos
    try {
        error_log("Preparando UPDATE SQL...");

        $stmt = $pdo->prepare(
            'UPDATE usuarios SET
                nombre_completo = :nombre,
                avatar = :avatar,
                banner = :banner,
                nickname = :nickname,
                biografia = :biografia,
                ubicacion = :ubicacion,
                sitio_web = :sitio_web
            WHERE id = :id'
        );

        error_log("Valores a actualizar: " . json_encode([
            ':nombre' => $nombreFinal,
            ':avatar' => $avatarNombre,
            ':banner' => $bannerNombre,
            ':nickname' => $nicknameFinal,
            ':biografia' => $biografiaFinal,
            ':ubicacion' => $ubicacionFinal,
            ':sitio_web' => $sitioWebFinal,
            ':id' => $userId,
        ]));

        $result = $stmt->execute([
            ':nombre' => $nombreFinal,
            ':avatar' => $avatarNombre,
            ':banner' => $bannerNombre,
            ':nickname' => $nicknameFinal,
            ':biografia' => $biografiaFinal,
            ':ubicacion' => $ubicacionFinal,
            ':sitio_web' => $sitioWebFinal,
            ':id' => $userId,
        ]);

        if (!$result) {
            error_log("ERROR en execute(): " . json_encode($stmt->errorInfo()));
            throw new Exception("Error al ejecutar UPDATE: " . json_encode($stmt->errorInfo()));
        }

        error_log("UPDATE ejecutado correctamente. Filas afectadas: " . $stmt->rowCount());

        // Actualizar sesión
        $_SESSION['usuario_nombre'] = $nombreFinal;
        $_SESSION['usuario_avatar'] = $avatarNombre;
        $_SESSION['usuario_banner'] = $bannerNombre;
        $_SESSION['usuario_nickname'] = $nicknameFinal ?? strtolower(str_replace(' ', '', $nombreFinal ?? 'usuario'));
        $_SESSION['usuario_biografia'] = $biografiaFinal ?? '';
        $_SESSION['usuario_ubicacion'] = $ubicacionFinal ?? '';
        $_SESSION['usuario_sitio_web'] = $sitioWebFinal ?? '';

        error_log("Sesión actualizada correctamente.");
        error_log("=== FIN PROCESAMIENTO EXITOSO ===\n");

        // Redirigir al inicio
        header('Location: ../index.php');
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
    error_log("=== CARGANDO PERFIL - Usuario ID: {$userId} ===");

    // Cargamos el perfil con una consulta parametrizada para evitar exponer otro usuario.
    $stmt = $pdo->prepare('SELECT nombre_completo, nickname, biografia, ubicacion, sitio_web, avatar, banner FROM usuarios WHERE id = :id');
    $stmt->execute([':id' => $userId]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        error_log("ERROR: Usuario no encontrado en BD");
        die("Error: Usuario no encontrado.");
    }

    error_log("✓ Usuario cargado. Datos: " . json_encode([
        'nombre_completo' => $usuario['nombre_completo'] ?? 'NULL',
        'avatar' => $usuario['avatar'] ?? 'NULL',
        'banner' => $usuario['banner'] ?? 'NULL',
        'nickname' => $usuario['nickname'] ?? 'NULL',
    ]));

} catch (PDOException $e) {
    error_log("ERROR PDO al cargar usuario: " . $e->getMessage());
    die("Error al cargar datos del usuario.");
}

// Procesar y escapar datos
$nombre = htmlspecialchars($usuario['nombre_completo'] ?? '', ENT_QUOTES, 'UTF-8');
$nickname = htmlspecialchars($usuario['nickname'] ?? '', ENT_QUOTES, 'UTF-8');
$biografia = htmlspecialchars($usuario['biografia'] ?? '', ENT_QUOTES, 'UTF-8');
$ubicacion = htmlspecialchars($usuario['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8');
$sitioWeb = htmlspecialchars($usuario['sitio_web'] ?? '', ENT_QUOTES, 'UTF-8');
$avatar = htmlspecialchars($usuario['avatar'] ?? 'default-avatar.png', ENT_QUOTES, 'UTF-8');
$banner = htmlspecialchars($usuario['banner'] ?? 'default-banner.png', ENT_QUOTES, 'UTF-8');

// URLs de los archivos
$avatarUrl = '../uploads/avatars/' . $avatar;
$bannerUrl = '../uploads/banners/' . $banner;

error_log("URLs: Avatar={$avatarUrl}, Banner={$bannerUrl}");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personalizar Perfil</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        /* Override y estilos adicionales para el perfil */
        main {
            align-items: center;
        }

        h1 {
            text-align: center;
            margin-bottom: 12px;
        }

        .intro {
            text-align: center;
            margin-bottom: 28px;
        }

        /* Banner Section */
        .banner-section {
            position: relative;
            width: 100%;
            height: 140px;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 8px;
            background-color: rgba(148, 163, 184, 0.2);
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .banner-section:hover {
            transform: translateY(-2px);
        }

        .banner-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .banner-section:hover .banner-overlay {
            background: rgba(0, 0, 0, 0.4);
        }

        .banner-overlay-text {
            color: white;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            opacity: 0;
            transition: opacity 0.3s;
            line-height: 1.4;
        }

        .banner-section:hover .banner-overlay-text {
            opacity: 1;
        }

        /* Avatar Section */
        .profile-avatar-section {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
            position: relative;
            z-index: 10;
        }

        .avatar-container {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background-color: rgba(148, 163, 184, 0.2);
            border: 4px solid var(--card);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
        }

        .avatar-container:hover .avatar-overlay {
            background: rgba(0, 0, 0, 0.4);
        }

        .avatar-overlay-text {
            color: white;
            font-size: 12px;
            font-weight: 700;
            opacity: 0;
            transition: opacity 0.3s;
            text-align: center;
            line-height: 1.3;
        }

        .avatar-container:hover .avatar-overlay-text {
            opacity: 1;
        }

        /* Form adjustments */
        .field-group {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 2px;
        }

        .form-row-2 .field-group {
            margin-bottom: 0;
        }

        .char-counter {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 4px;
            display: block;
        }

        .char-counter.warning {
            color: var(--danger);
            font-weight: 600;
        }

        .action-btn.primary {
            width: 100%;
            margin-top: 12px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .button-group .action-btn {
            flex: 1;
        }

        .button-group .action-btn.primary {
            margin-top: 0;
        }

        .back-link {
            text-decoration: none;
            color: var(--text);
        }

        #avatarInput,
        #bannerInput {
            display: none;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .contenedor {
                padding: 20px;
                border-radius: 18px;
            }

            .banner-section {
                height: 110px;
                border-radius: 12px;
                margin-bottom: 6px;
            }

            .avatar-image {
                border-width: 3px;
            }

            .form-row-2 {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <a class="logo-back" href="../index.php" aria-label="Volver al inicio">
        <img src="../img/logoblanco.png" alt="Volver al inicio">
    </a>

    <main>
        <section class="contenedor">
            <h1>Editar Perfil</h1>
            <p class="intro">Personaliza tu información y fotos</p>

            <!-- Banner -->
            <div class="banner-section" id="bannerContainer" onclick="document.getElementById('bannerInput').click()">
                <img id="bannerImage" src="<?php echo $bannerUrl; ?>" alt="Banner de perfil" class="banner-image" onerror="this.src='../uploads/banners/default-banner.png'">
                <div class="banner-overlay">
                    <div class="banner-overlay-text">
                        <div style="font-size: 24px; margin-bottom: 2px;">📷</div>
                        <div>Cambiar portada</div>
                    </div>
                </div>
            </div>

            <!-- Avatar -->
            <div class="profile-avatar-section">
                <div class="avatar-container" id="avatarContainer" onclick="document.getElementById('avatarInput').click()">
                    <img id="avatarImage" src="<?php echo $avatarUrl; ?>" alt="Avatar de perfil" class="avatar-image" onerror="this.src='../uploads/avatars/default-avatar.png'">
                    <div class="avatar-overlay">
                        <div class="avatar-overlay-text">
                            <div style="font-size: 20px; margin-bottom: 1px;">📷</div>
                            <div>Cambiar foto</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" enctype="multipart/form-data" id="editForm" novalidate>
                <!-- Nombre Real -->
                <div class="field-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" value="<?php echo $nombre; ?>" placeholder="Tu nombre completo" required>
                    <span class="char-counter"><span id="nombreCount">0</span>/100</span>
                </div>

                <!-- Nickname y Ubicación (2 columnas) -->
                <div class="form-row-2">
                    <div class="field-group">
                        <label for="nickname">Nickname</label>
                        <input type="text" id="nickname" name="nickname" maxlength="50" value="<?php echo $nickname; ?>" placeholder="@usuario">
                        <span class="char-counter"><span id="nicknameCount">0</span>/50</span>
                    </div>
                    <div class="field-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" maxlength="100" value="<?php echo $ubicacion; ?>" placeholder="Ciudad o país">
                        <span class="char-counter"><span id="ubicacionCount">0</span>/100</span>
                    </div>
                </div>

                <!-- Biografía -->
                <div class="field-group">
                    <label for="biografia">Biografía</label>
                    <textarea id="biografia" name="biografia" maxlength="150" placeholder="Cuéntanos algo sobre ti..."><?php echo $biografia; ?></textarea>
                    <span class="char-counter"><span id="biografiaCount">0</span>/150</span>
                </div>

                <!-- Sitio Web -->
                <div class="field-group">
                    <label for="sitio_web">Sitio Web</label>
                    <input type="url" id="sitio_web" name="sitio_web" value="<?php echo $sitioWeb; ?>" placeholder="https://ejemplo.com">
                </div>

                <!-- Botones -->
                <div class="button-group">
                    <a href="../index.php" class="action-btn secondary back-link">Cancelar</a>
                    <button type="submit" class="action-btn primary">Guardar cambios</button>
                </div>

                <!-- Input files DENTRO del formulario (CRÍTICO) -->
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;">
                <input type="file" id="bannerInput" name="banner" accept="image/*" style="display:none;">
            </form>
        </section>
    </main>

    <script src="../js/translator.js"></script>
    <script src="../js/script.js" defer></script>
    <script src="../js/login.js" defer></script>
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
        setupCharCounter('ubicacion', 'ubicacionCount', 100);
        setupCharCounter('biografia', 'biografiaCount', 150);

        // Previsualizar avatar
        document.getElementById('avatarInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('avatarImage').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Previsualizar banner
        document.getElementById('bannerInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('bannerImage').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
