<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/contenido_publico.php';
require_once __DIR__ . '/helpers/imagenes.php';

$usuario = sigtur_requerir_rol($pdo, ['organizador', 'administrador'], 'login.php', 'eventos.php');
sigtur_inicializar_contenido_publico($pdo);
$csrfToken = sigtur_csrf_token();
$secciones = ['turismo', 'lugares', 'gastronomia'];
$seccion = (string) ($_GET['seccion'] ?? $_POST['seccion'] ?? 'turismo');
if (!in_array($seccion, $secciones, true)) {
    $seccion = 'turismo';
}

function sigtur_contenido_error(string $mensaje, int $estado = 422): never
{
    http_response_code($estado);
    exit($mensaje);
}

function sigtur_contenido_imagen_valida(string $imagen): bool
{
    return filter_var($imagen, FILTER_VALIDATE_URL) !== false
        ? str_starts_with(strtolower($imagen), 'https://')
        : (bool) preg_match('/^(?:\.\.\/)?[A-Za-z0-9_./-]+$/', $imagen);
}

function sigtur_contenido_imagen_subida_presente(): bool
{
    return isset($_FILES['imagen_archivo'])
        && is_array($_FILES['imagen_archivo'])
        && ($_FILES['imagen_archivo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

function sigtur_contenido_resolver_imagen(string $imagen): array
{
    if (!sigtur_contenido_imagen_subida_presente()) {
        if (!sigtur_contenido_imagen_valida($imagen)) {
            sigtur_contenido_error('Ingresa una ruta de imagen válida o sube una imagen JPG, PNG o WEBP.');
        }
        return [sigtur_nombre_imagen_desde_ruta($imagen, 'uploads/contenido-publico'), null];
    }

    $archivo = $_FILES['imagen_archivo'];
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $estado = in_array($archivo['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 413 : 422;
        sigtur_contenido_error('No se pudo recibir la imagen. Comprueba el archivo e inténtalo de nuevo.', $estado);
    }
    if (
        (int) ($archivo['size'] ?? 0) <= 0
        || (int) $archivo['size'] > 10 * 1024 * 1024
        || !is_uploaded_file((string) ($archivo['tmp_name'] ?? ''))
    ) {
        sigtur_contenido_error('La imagen debe pesar más de 0 y no superar los 10 MB.', 413);
    }

    $extensionesPermitidas = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mimeImagen = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
    $datosImagen = @getimagesize($archivo['tmp_name']);
    if (
        !isset($extensionesPermitidas[$mimeImagen])
        || $datosImagen === false
        || !in_array($datosImagen[2] ?? null, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)
        || ($datosImagen[0] ?? 0) < 1
        || ($datosImagen[1] ?? 0) < 1
        || ($datosImagen[0] ?? 0) > 10000
        || ($datosImagen[1] ?? 0) > 10000
        || ($datosImagen[0] ?? 0) * ($datosImagen[1] ?? 0) > 40000000
    ) {
        sigtur_contenido_error('El archivo debe ser una imagen JPG, PNG o WEBP válida.');
    }

    $directorio = __DIR__ . '/../uploads/contenido-publico';
    if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
        sigtur_contenido_error('No se pudo preparar la carpeta para las imágenes.', 500);
    }
    $nombre = sigtur_nombre_imagen_subida((int) $usuario['id'], (string) ($archivo['name'] ?? 'contenido'), $extensionesPermitidas[$mimeImagen]);
    $rutaArchivo = $directorio . DIRECTORY_SEPARATOR . $nombre;
    if (!move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        sigtur_contenido_error('No se pudo guardar la imagen.', 500);
    }

    return [$nombre, $rutaArchivo];
}

function sigtur_contenido_ejecutar_guardado(PDOStatement $guardar, array $parametros, ?string $rutaImagenNueva): void
{
    try {
        $guardar->execute($parametros);
    } catch (Throwable $error) {
        if ($rutaImagenNueva !== null && is_file($rutaImagenNueva) && !unlink($rutaImagenNueva)) {
            error_log('No se pudo limpiar una imagen de contenido cuyo registro no se guardó.');
        }
        throw $error;
    }
}

function sigtur_contenido_slug_disponible(PDO $pdo, string $tabla, string $slug, int $id): bool
{
    if (!in_array($tabla, ['lugares', 'contenido_turistico', 'gastronomia_catalogo'], true)) {
        throw new InvalidArgumentException('Tabla de contenido no válida.');
    }
    $consulta = $pdo->prepare("SELECT 1 FROM `$tabla` WHERE slug = :slug AND id <> :id LIMIT 1");
    $consulta->execute([':slug' => $slug, ':id' => $id]);
    return !$consulta->fetchColumn();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
        sigtur_contenido_error('La solicitud expiró. Recarga la página e inténtalo de nuevo.', 403);
    }

    $accion = (string) ($_POST['accion'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    if (in_array($accion, ['editar', 'eliminar'], true) && $id <= 0) {
        sigtur_contenido_error('El registro seleccionado no es válido.');
    }
    if ($seccion === 'lugares') {
        if ($accion === 'eliminar') {
            $pdo->beginTransaction();
            $desvincular = $pdo->prepare('UPDATE eventos SET lugar_id = NULL WHERE lugar_id = :id');
            $desvincular->execute([':id' => $id]);
            $eliminar = $pdo->prepare('DELETE FROM lugares WHERE id = :id');
            $eliminar->execute([':id' => $id]);
            $pdo->commit();
        } elseif (in_array($accion, ['crear', 'editar'], true)) {
            $datos = [
                ':slug' => strtolower(trim((string) ($_POST['slug'] ?? ''))),
                ':nombre' => trim((string) ($_POST['nombre'] ?? '')),
                ':categoria' => trim((string) ($_POST['categoria'] ?? '')),
                ':seccion_publica' => (string) ($_POST['seccion_publica'] ?? 'oculto'),
                ':direccion' => trim((string) ($_POST['direccion'] ?? '')),
                ':imagen' => trim((string) ($_POST['imagen'] ?? '')),
                ':descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
                ':historia' => trim((string) ($_POST['historia'] ?? '')),
                ':atractivos' => trim((string) ($_POST['atractivos'] ?? '')),
                ':horarios' => trim((string) ($_POST['horarios'] ?? '')),
                ':recomendaciones' => trim((string) ($_POST['recomendaciones'] ?? '')),
                ':latitud' => is_numeric($_POST['latitud'] ?? null) ? (float) $_POST['latitud'] : null,
                ':longitud' => is_numeric($_POST['longitud'] ?? null) ? (float) $_POST['longitud'] : null,
            ];
            if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $datos[':slug'])
                || !in_array($datos[':seccion_publica'], ['emblematico', 'recreacion', 'oculto'], true)
                || $datos[':nombre'] === '' || $datos[':categoria'] === '' || $datos[':direccion'] === ''
                || (!sigtur_contenido_imagen_valida($datos[':imagen']) && !sigtur_contenido_imagen_subida_presente()) || $datos[':descripcion'] === ''
                || $datos[':historia'] === '' || $datos[':atractivos'] === '' || $datos[':horarios'] === ''
                || $datos[':recomendaciones'] === '') {
                sigtur_contenido_error('Completa los campos obligatorios e ingresa un slug e imagen válidos.');
            }
            if (!sigtur_contenido_slug_disponible($pdo, 'lugares', $datos[':slug'], $id)) {
                sigtur_contenido_error('Ya existe un lugar con ese slug.');
            }
            [$datos[':imagen'], $rutaImagenNueva] = sigtur_contenido_resolver_imagen($datos[':imagen']);
            if ($accion === 'crear') {
                $guardar = $pdo->prepare('INSERT INTO lugares (slug, nombre, categoria, seccion_publica, direccion, imagen, descripcion, historia, atractivos, horarios, recomendaciones, latitud, longitud) VALUES (:slug, :nombre, :categoria, :seccion_publica, :direccion, :imagen, :descripcion, :historia, :atractivos, :horarios, :recomendaciones, :latitud, :longitud)');
            } else {
                $guardar = $pdo->prepare('UPDATE lugares SET slug = :slug, nombre = :nombre, categoria = :categoria, seccion_publica = :seccion_publica, direccion = :direccion, imagen = :imagen, descripcion = :descripcion, historia = :historia, atractivos = :atractivos, horarios = :horarios, recomendaciones = :recomendaciones, latitud = :latitud, longitud = :longitud WHERE id = :id');
                $datos[':id'] = $id;
            }
            sigtur_contenido_ejecutar_guardado($guardar, $datos, $rutaImagenNueva);
        } else {
            sigtur_contenido_error('La acción solicitada no es válida.');
        }
    } else {
        $tabla = $seccion === 'turismo' ? 'contenido_turistico' : 'gastronomia_catalogo';
        if ($accion === 'eliminar') {
            $eliminar = $pdo->prepare("DELETE FROM `$tabla` WHERE id = :id");
            $eliminar->execute([':id' => $id]);
        } elseif (in_array($accion, ['crear', 'editar'], true)) {
            $slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
            $nombre = trim((string) ($_POST['nombre'] ?? ''));
            $imagen = trim((string) ($_POST['imagen'] ?? ''));
            $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
            $orden = max(0, (int) ($_POST['orden'] ?? 0));
            if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || $nombre === ''
                || (!sigtur_contenido_imagen_valida($imagen) && !sigtur_contenido_imagen_subida_presente()) || $descripcion === '') {
                sigtur_contenido_error('Completa los campos obligatorios e ingresa un slug e imagen válidos.');
            }
            if (!sigtur_contenido_slug_disponible($pdo, $tabla, $slug, $id)) {
                sigtur_contenido_error('Ya existe un registro con ese slug en esta sección.');
            }
            if ($seccion === 'turismo') {
                $tipo = (string) ($_POST['tipo'] ?? '');
                $detalle = trim((string) ($_POST['detalle'] ?? ''));
                $estrellasRecibidas = trim((string) ($_POST['estrellas'] ?? ''));
                if (!in_array($tipo, ['escapada', 'ruta', 'termas'], true)) {
                    sigtur_contenido_error('El tipo de propuesta turística no es válido.');
                }
                if ($estrellasRecibidas !== '' && (!ctype_digit($estrellasRecibidas) || (int) $estrellasRecibidas < 1 || (int) $estrellasRecibidas > 5)) {
                    sigtur_contenido_error('La valoración debe ser un número entre 1 y 5.');
                }
                $estrellas = $tipo === 'termas' && $estrellasRecibidas !== '' ? (int) $estrellasRecibidas : null;
                [$imagen, $rutaImagenNueva] = sigtur_contenido_resolver_imagen($imagen);
                if ($accion === 'crear') {
                    $guardar = $pdo->prepare('INSERT INTO contenido_turistico (slug, tipo, nombre, imagen, descripcion, detalle, estrellas, orden) VALUES (:slug, :tipo, :nombre, :imagen, :descripcion, :detalle, :estrellas, :orden)');
                } else {
                    $guardar = $pdo->prepare('UPDATE contenido_turistico SET slug = :slug, tipo = :tipo, nombre = :nombre, imagen = :imagen, descripcion = :descripcion, detalle = :detalle, estrellas = :estrellas, orden = :orden WHERE id = :id');
                    $idContenido = $id;
                }
                $parametros = [':slug' => $slug, ':tipo' => $tipo, ':nombre' => $nombre, ':imagen' => $imagen, ':descripcion' => $descripcion, ':detalle' => $detalle, ':estrellas' => $estrellas, ':orden' => $orden];
                if ($accion === 'editar') {
                    $parametros[':id'] = $idContenido;
                }
                sigtur_contenido_ejecutar_guardado($guardar, $parametros, $rutaImagenNueva);
            } else {
                $categoria = trim((string) ($_POST['categoria'] ?? 'Restaurante'));
                $urlMapa = trim((string) ($_POST['url_mapa'] ?? ''));
                if ($categoria === '' || ($urlMapa !== '' && (filter_var($urlMapa, FILTER_VALIDATE_URL) === false || !str_starts_with(strtolower($urlMapa), 'https://')))) {
                    sigtur_contenido_error('La categoría es obligatoria y el enlace del mapa debe ser HTTPS.');
                }
                [$imagen, $rutaImagenNueva] = sigtur_contenido_resolver_imagen($imagen);
                if ($accion === 'crear') {
                    $guardar = $pdo->prepare('INSERT INTO gastronomia_catalogo (slug, nombre, imagen, descripcion, categoria, url_mapa, orden) VALUES (:slug, :nombre, :imagen, :descripcion, :categoria, :url_mapa, :orden)');
                } else {
                    $guardar = $pdo->prepare('UPDATE gastronomia_catalogo SET slug = :slug, nombre = :nombre, imagen = :imagen, descripcion = :descripcion, categoria = :categoria, url_mapa = :url_mapa, orden = :orden WHERE id = :id');
                    $idContenido = $id;
                }
                $parametros = [':slug' => $slug, ':nombre' => $nombre, ':imagen' => $imagen, ':descripcion' => $descripcion, ':categoria' => $categoria, ':url_mapa' => $urlMapa, ':orden' => $orden];
                if ($accion === 'editar') {
                    $parametros[':id'] = $idContenido;
                }
                sigtur_contenido_ejecutar_guardado($guardar, $parametros, $rutaImagenNueva);
            }
        } else {
            sigtur_contenido_error('La acción solicitada no es válida.');
        }
    }

    header('Location: organizar-contenido.php?seccion=' . rawurlencode($seccion) . '&guardado=1');
    exit;
}

$lugares = $seccion === 'lugares' ? $pdo->query('SELECT * FROM lugares ORDER BY nombre')->fetchAll() : [];
$contenidos = $seccion === 'turismo'
    ? $pdo->query('SELECT * FROM contenido_turistico ORDER BY FIELD(tipo, "escapada", "ruta", "termas"), orden, nombre')->fetchAll()
    : ($seccion === 'gastronomia' ? $pdo->query('SELECT * FROM gastronomia_catalogo ORDER BY orden, nombre')->fetchAll() : []);
$escape = 'sigtur_texto_contenido';
foreach ($lugares as &$lugar) {
    $lugar['imagen'] = sigtur_url_imagen_subida((string) $lugar['imagen'], 'contenido-publico');
}
unset($lugar);
foreach ($contenidos as &$contenido) {
    $contenido['imagen'] = sigtur_url_imagen_subida((string) $contenido['imagen'], 'contenido-publico');
}
unset($contenido);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestionar <?= htmlspecialchars(ucfirst($seccion), ENT_QUOTES, 'UTF-8') ?> | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
</head>
<body class="admin-panel-page">
<main class="admin-panel">
    <header class="admin-hero">
        <div class="admin-hero__copy">
            <p class="admin-eyebrow">SIGTUR · Organizador</p>
            <h1>Gestionar <?= htmlspecialchars(ucfirst($seccion), ENT_QUOTES, 'UTF-8') ?></h1>
            <p>Crea, edita y elimina el contenido que aparece en las páginas públicas.</p>
        </div>
        <nav class="admin-nav" aria-label="Secciones de contenido">
            <a class="admin-nav__link" href="organizar-contenido.php?seccion=turismo">Turismo</a>
            <a class="admin-nav__link" href="organizar-contenido.php?seccion=lugares">Lugares</a>
            <a class="admin-nav__link" href="organizar-contenido.php?seccion=gastronomia">Gastronomía</a>
            <a class="admin-nav__link admin-nav__link--subtle" href="panel-organizador.php">Panel</a>
        </nav>
    </header>
    <?php if (isset($_GET['guardado'])): ?><p class="admin-notice" role="status">Los cambios se guardaron.</p><?php endif; ?>

    <section class="admin-section">
        <div class="admin-section__heading"><div><p class="admin-eyebrow">Contenido publicado</p><h2><?= htmlspecialchars(ucfirst($seccion), ENT_QUOTES, 'UTF-8') ?></h2></div></div>
        <?php if ($seccion === 'lugares'): ?>
            <details class="admin-content-item" open><summary>Agregar lugar</summary>
                <?php $lugar = ['id' => 0, 'slug' => '', 'nombre' => '', 'categoria' => '', 'seccion_publica' => 'emblematico', 'direccion' => '', 'imagen' => '', 'descripcion' => '', 'historia' => '', 'atractivos' => '', 'horarios' => '', 'recomendaciones' => '', 'latitud' => '', 'longitud' => '']; include __DIR__ . '/partials/formulario-lugar.php'; ?>
            </details>
            <?php foreach ($lugares as $lugar): ?><details class="admin-content-item"><summary><span><?= $escape($lugar['nombre']) ?></span><span class="admin-button">Editar</span></summary><?php include __DIR__ . '/partials/formulario-lugar.php'; ?>
                <form method="post" onsubmit="return confirm('¿Eliminar este lugar?');"><input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>"><input type="hidden" name="seccion" value="lugares"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int) $lugar['id'] ?>"><button class="admin-button admin-button--danger" type="submit">Eliminar lugar</button></form>
            </details><?php endforeach; ?>
        <?php else: ?>
            <details class="admin-content-item" open><summary>Agregar <?= $seccion === 'turismo' ? 'propuesta turística' : 'restaurante' ?></summary>
                <?php $contenido = ['id' => 0, 'slug' => '', 'tipo' => 'escapada', 'nombre' => '', 'imagen' => '', 'descripcion' => '', 'detalle' => '', 'estrellas' => null, 'categoria' => 'Restaurante', 'url_mapa' => '', 'orden' => 0]; include __DIR__ . '/partials/formulario-catalogo.php'; ?>
            </details>
            <?php foreach ($contenidos as $contenido): ?><details class="admin-content-item"><summary><span><?= $escape($contenido['nombre']) ?><?= $seccion === 'turismo' ? ' · ' . $escape($contenido['tipo']) : '' ?></span><span class="admin-button">Editar</span></summary><?php include __DIR__ . '/partials/formulario-catalogo.php'; ?>
                <form method="post" onsubmit="return confirm('¿Eliminar este contenido?');"><input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>"><input type="hidden" name="seccion" value="<?= $escape($seccion) ?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= (int) $contenido['id'] ?>"><button class="admin-button admin-button--danger" type="submit">Eliminar</button></form>
            </details><?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>
<script src="../js/tema.js" defer></script>
</body>
</html>
