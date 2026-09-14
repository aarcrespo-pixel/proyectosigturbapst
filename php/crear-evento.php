<?php
session_start();
require_once __DIR__ . '/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // El endpoint solo crea eventos por POST; cualquier acceso directo vuelve al listado.
    header('Location: eventos.php');
    exit;
}

$organizadorId = (int) ($_SESSION['usuario_id'] ?? 0);
if ($organizadorId <= 0) {
    // El organizador debe estar autenticado antes de insertar contenido persistente.
    http_response_code(401);
    exit('Debes iniciar sesión para crear un evento.');
}

$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$categoria = trim($_POST['categoria'] ?? 'General');
$tipoEntrada = trim($_POST['tipoEntrada'] ?? 'Gratuito');
$precioEntrada = trim($_POST['precio'] ?? '');
$imagen = trim($_POST['imagen_portada'] ?? 'default.jpg');

/* Los campos obligatorios se validan en servidor porque el formulario puede
    ser enviado fuera del navegador o manipulado desde las herramientas web. */
if ($titulo === '' || $descripcion === '' || $fecha === '' || $ubicacion === '') {
    http_response_code(422);
    exit('Completa título, descripción, fecha y ubicación.');
}

/* Convertimos el título en un slug estable y consultamos su disponibilidad
   mediante PDO para evitar colisiones y mantener la URL legible. */
$tituloNormalizado = iconv('UTF-8', 'ASCII//TRANSLIT', $titulo) ?: $titulo;
$slugBase = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9]+/', '-', strtolower($tituloNormalizado))) ?? '', '-');
$slugBase = $slugBase !== '' ? $slugBase : 'evento';
$slug = $slugBase;
$contador = 2;
$buscarSlug = $pdo->prepare('SELECT id FROM eventos WHERE slug = :slug LIMIT 1');
while (true) {
    $buscarSlug->execute([':slug' => $slug]);
    if (!$buscarSlug->fetchColumn()) {
        break;
    }
    $slug = $slugBase . '-' . $contador++;
}

/* El Prepared Statement separa datos y SQL, evitando inyección y dejando
   que lastInsertId() identifique el evento que se acaba de publicar. */
$precio = $tipoEntrada === 'De Pago' && is_numeric($precioEntrada) ? (float) $precioEntrada : null;
/* El precio solo se persiste para eventos pagos; los gratuitos quedan con NULL
    y el filtro SQL puede tratarlos como cero mediante COALESCE. */
/* Buscamos la ubicación normalizada antes del INSERT para que los eventos
    creados desde el formulario queden relacionados con lugar.php desde el
    primer momento; si es una ubicación nueva, lugar_id permanece NULL. */
$buscarLugar = $pdo->prepare('SELECT id FROM lugares WHERE nombre = :ubicacion OR direccion LIKE :direccion LIMIT 1');
$buscarLugar->execute([':ubicacion' => $ubicacion, ':direccion' => '%' . $ubicacion . '%']);
$lugarId = $buscarLugar->fetchColumn();

$insertar = $pdo->prepare('INSERT INTO eventos (titulo, slug, descripcion, fecha, ubicacion, lugar_id, categoria, tipo_entrada, precio, imagen_portada, es_pasado, organizador_id) VALUES (:titulo, :slug, :descripcion, :fecha, :ubicacion, :lugar_id, :categoria, :tipo_entrada, :precio, :imagen, :es_pasado, :organizador)');
$insertar->execute([
    ':titulo' => $titulo,
    ':slug' => $slug,
    ':descripcion' => $descripcion,
    ':fecha' => $fecha,
    ':ubicacion' => $ubicacion,
    ':lugar_id' => $lugarId ?: null,
    ':categoria' => $categoria,
    ':tipo_entrada' => $tipoEntrada === 'De Pago' ? 'De Pago' : 'Gratuito',
    ':precio' => $precio,
    ':imagen' => $imagen !== '' ? $imagen : 'default.jpg',
    ':es_pasado' => strtotime($fecha) < strtotime(date('Y-m-d')) ? 1 : 0,
    ':organizador' => $organizadorId,
]);

/* Redirigimos usando el ID generado por MySQL, no el slug recibido, para abrir
    exactamente la fila recién creada aunque el slug haya sido numerado. */
header('Location: evento.php?id=' . (int) $pdo->lastInsertId());
exit;