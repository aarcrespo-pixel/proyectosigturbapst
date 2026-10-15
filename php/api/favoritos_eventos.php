<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
if (!$usuarioId) {
    http_response_code(401);
    echo json_encode(['error' => 'Iniciá sesión para guardar eventos favoritos']);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../conexion.php';

$slug = trim((string) ($_POST['slug'] ?? ''));
$accion = (string) ($_POST['accion'] ?? '');
if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) || strlen($slug) > 255 || !in_array($accion, ['guardar', 'quitar'], true)) {
    http_response_code(422);
    echo json_encode(['error' => 'Los datos del evento no son válidos']);
    exit;
}

if ($accion === 'quitar') {
    $stmt = $pdo->prepare('DELETE FROM favoritos_eventos WHERE usuario_id = :usuario AND evento_slug = :slug');
    $stmt->execute([':usuario' => $usuarioId, ':slug' => $slug]);
    echo json_encode(['ok' => true, 'favorito' => false]);
    exit;
}

$titulo = trim((string) ($_POST['titulo'] ?? ''));
if ($titulo === '') {
    http_response_code(422);
    echo json_encode(['error' => 'El evento necesita un título']);
    exit;
}
$imagen = trim((string) ($_POST['imagen'] ?? ''));
if (str_starts_with($imagen, '../../')) {
    $imagen = substr($imagen, 1);
}

$fecha = trim((string) ($_POST['fecha'] ?? ''));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !checkdate((int) substr($fecha, 5, 2), (int) substr($fecha, 8, 2), (int) substr($fecha, 0, 4))) {
    $fecha = null;
}

$stmt = $pdo->prepare('INSERT INTO favoritos_eventos (usuario_id, evento_slug, evento_id, titulo, descripcion, fecha, ubicacion, categoria, imagen_portada) VALUES (:usuario, :slug, :evento_id, :titulo, :descripcion, :fecha, :ubicacion, :categoria, :imagen) ON DUPLICATE KEY UPDATE evento_id = VALUES(evento_id), titulo = VALUES(titulo), descripcion = VALUES(descripcion), fecha = VALUES(fecha), ubicacion = VALUES(ubicacion), categoria = VALUES(categoria), imagen_portada = VALUES(imagen_portada)');
$stmt->execute([
    ':usuario' => $usuarioId,
    ':slug' => $slug,
    ':evento_id' => (int) ($_POST['evento_id'] ?? 0) ?: null,
    ':titulo' => mb_substr($titulo, 0, 255),
    ':descripcion' => mb_substr(trim((string) ($_POST['descripcion'] ?? '')), 0, 5000),
    ':fecha' => $fecha,
    ':ubicacion' => mb_substr(trim((string) ($_POST['ubicacion'] ?? '')), 0, 255),
    ':categoria' => mb_substr(trim((string) ($_POST['categoria'] ?? '')), 0, 100),
    ':imagen' => mb_substr($imagen, 0, 255),
]);

echo json_encode(['ok' => true, 'favorito' => true]);