<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }

    $accion = (string) ($_POST['accion'] ?? '');
    if ($accion === 'eliminar_publicacion') {
        $eliminar = $pdo->prepare('DELETE FROM publicaciones WHERE id = :id');
        $eliminar->execute([':id' => (int) ($_POST['publicacion_id'] ?? 0)]);
    } elseif (in_array($accion, ['crear_lugar', 'editar_lugar'], true)) {
        $datos = [
            ':slug' => strtolower(trim((string) ($_POST['slug'] ?? ''))),
            ':nombre' => trim((string) ($_POST['nombre'] ?? '')),
            ':categoria' => trim((string) ($_POST['categoria'] ?? '')),
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
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $datos[':slug']) || $datos[':nombre'] === '' || $datos[':categoria'] === '' || $datos[':direccion'] === '' || $datos[':imagen'] === '' || $datos[':descripcion'] === '' || $datos[':historia'] === '' || $datos[':atractivos'] === '' || $datos[':horarios'] === '' || $datos[':recomendaciones'] === '') {
            http_response_code(422);
            exit('Completa los datos obligatorios del lugar con un slug válido.');
        }

        if ($accion === 'crear_lugar') {
            $guardar = $pdo->prepare('INSERT INTO lugares (slug, nombre, categoria, direccion, imagen, descripcion, historia, atractivos, horarios, recomendaciones, latitud, longitud) VALUES (:slug, :nombre, :categoria, :direccion, :imagen, :descripcion, :historia, :atractivos, :horarios, :recomendaciones, :latitud, :longitud)');
        } else {
            $guardar = $pdo->prepare('UPDATE lugares SET slug = :slug, nombre = :nombre, categoria = :categoria, direccion = :direccion, imagen = :imagen, descripcion = :descripcion, historia = :historia, atractivos = :atractivos, horarios = :horarios, recomendaciones = :recomendaciones, latitud = :latitud, longitud = :longitud WHERE id = :id');
            $datos[':id'] = (int) ($_POST['lugar_id'] ?? 0);
        }
        $guardar->execute($datos);
    } else {
        http_response_code(422);
        exit('La acción solicitada no es válida.');
    }

    header('Location: admin-contenido.php?guardado=1');
    exit;
}

$lugares = $pdo->query('SELECT * FROM lugares ORDER BY nombre')->fetchAll();
$publicaciones = $pdo->query('SELECT p.id, p.imagen, p.descripcion, p.fecha_creacion, u.nombre_completo FROM publicaciones p INNER JOIN usuarios u ON u.id = p.usuario_id ORDER BY p.fecha_creacion DESC')->fetchAll();
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Contenido | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"><style>
body{background:#f4f6f8;color:#18212a}.panel{max-width:1180px;margin:0 auto;padding:2rem 1.25rem 5rem}.head{display:flex;justify-content:space-between;align-items:center;gap:1rem}.links,.actions{display:flex;flex-wrap:wrap;gap:.6rem}.links a,.save,.delete{display:inline-block;padding:.7rem 1rem;border:0;border-radius:8px;background:#152d3d;color:#fff;text-decoration:none;font-weight:700;cursor:pointer}.delete{background:#ffe9e5;color:#a53628}.place,.publication{margin:1rem 0;padding:1.1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px}.place-form{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.7rem}.place-form label{display:grid;gap:.3rem;font-weight:600}.place-form input,.place-form textarea{width:100%;padding:.6rem;border:1px solid #cbd4db;border-radius:6px;font:inherit}.place-form textarea{min-height:84px}.wide{grid-column:1/-1}.publication{display:grid;grid-template-columns:140px 1fr auto;align-items:center;gap:1rem}.publication img{width:140px;height:100px;object-fit:cover}.notice{padding:.8rem 1rem;background:#e8f6ee;color:#225b37;border-radius:8px}@media(max-width:680px){.head{align-items:flex-start;flex-direction:column}.place-form{grid-template-columns:1fr}.wide{grid-column:auto}.publication{grid-template-columns:80px 1fr}.publication img{width:80px;height:80px}.publication form{grid-column:1/-1}}
</style></head><body><main class="panel"><header class="head"><div><p>Administración</p><h1>Contenido de SIGTUR</h1><p>Gestiona lugares turísticos y publicaciones de usuarios.</p></div><nav class="links"><a href="panel-admin.php">Panel admin</a><a href="../index.php">Inicio</a></nav></header><?php if (isset($_GET['guardado'])): ?><p class="notice" role="status">Los cambios se guardaron.</p><?php endif; ?>
<section><h2>Lugares turísticos</h2><?php foreach ($lugares as $lugar): ?><article class="place"><h3><?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?></h3><form class="place-form" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="editar_lugar"><input type="hidden" name="lugar_id" value="<?= (int) $lugar['id'] ?>"><label>Slug<input name="slug" required value="<?= htmlspecialchars($lugar['slug'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Nombre<input name="nombre" maxlength="160" required value="<?= htmlspecialchars($lugar['nombre'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Categoría<input name="categoria" maxlength="80" required value="<?= htmlspecialchars($lugar['categoria'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Dirección<input name="direccion" maxlength="255" required value="<?= htmlspecialchars($lugar['direccion'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Imagen (ruta o URL)<input name="imagen" maxlength="255" required value="<?= htmlspecialchars($lugar['imagen'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Horarios<input name="horarios" maxlength="255" required value="<?= htmlspecialchars($lugar['horarios'], ENT_QUOTES, 'UTF-8') ?>"></label><label>Latitud<input name="latitud" type="number" step="any" value="<?= htmlspecialchars((string) ($lugar['latitud'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label><label>Longitud<input name="longitud" type="number" step="any" value="<?= htmlspecialchars((string) ($lugar['longitud'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label><label class="wide">Descripción<textarea name="descripcion" required><?= htmlspecialchars($lugar['descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea></label><label class="wide">Historia<textarea name="historia" required><?= htmlspecialchars($lugar['historia'], ENT_QUOTES, 'UTF-8') ?></textarea></label><label class="wide">Atractivos<textarea name="atractivos" required><?= htmlspecialchars($lugar['atractivos'], ENT_QUOTES, 'UTF-8') ?></textarea></label><label class="wide">Recomendaciones<textarea name="recomendaciones" required><?= htmlspecialchars($lugar['recomendaciones'], ENT_QUOTES, 'UTF-8') ?></textarea></label><div class="wide"><button class="save" type="submit">Guardar lugar</button></div></form></article><?php endforeach; ?>
<article class="place"><h3>Agregar lugar</h3><form class="place-form" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="crear_lugar"><label>Slug<input name="slug" placeholder="parque-nuevo" required></label><label>Nombre<input name="nombre" maxlength="160" required></label><label>Categoría<input name="categoria" maxlength="80" required></label><label>Dirección<input name="direccion" maxlength="255" required></label><label>Imagen<input name="imagen" maxlength="255" required></label><label>Horarios<input name="horarios" maxlength="255" required></label><label>Latitud<input name="latitud" type="number" step="any"></label><label>Longitud<input name="longitud" type="number" step="any"></label><label class="wide">Descripción<textarea name="descripcion" required></textarea></label><label class="wide">Historia<textarea name="historia" required></textarea></label><label class="wide">Atractivos<textarea name="atractivos" required></textarea></label><label class="wide">Recomendaciones<textarea name="recomendaciones" required></textarea></label><div class="wide"><button class="save" type="submit">Crear lugar</button></div></form></article></section>
<section><h2>Publicaciones</h2><?php if (!$publicaciones): ?><p>No hay publicaciones para mostrar.</p><?php endif; ?><?php foreach ($publicaciones as $publicacion): ?><article class="publication"><img src="<?= htmlspecialchars($publicacion['imagen'], ENT_QUOTES, 'UTF-8') ?>" alt=""><div><strong><?= htmlspecialchars($publicacion['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($publicacion['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></p></div><form method="post" onsubmit="return confirm('¿Retirar esta publicación?')"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="accion" value="eliminar_publicacion"><input type="hidden" name="publicacion_id" value="<?= (int) $publicacion['id'] ?>"><button class="delete" type="submit">Retirar</button></form></article><?php endforeach; ?></section></main></body></html>