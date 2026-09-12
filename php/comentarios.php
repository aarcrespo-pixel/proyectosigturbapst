<?php
session_start();
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$itemKey = trim($_GET['item_key'] ?? $_POST['item_key'] ?? '');
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));
if ($itemKey === '' || strlen($itemKey) > 255) {
    http_response_code(400);
    echo json_encode(['error' => 'Elemento inválido']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
    $texto = trim($_POST['texto'] ?? '');

    if ($usuarioId <= 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Debes iniciar sesión para comentar']);
        exit;
    }

    if ($texto === '' || strlen($texto) > 140) {
        http_response_code(422);
        echo json_encode(['error' => 'El comentario debe tener entre 1 y 140 caracteres']);
        exit;
    }

    $insertar = $pdo->prepare(
        'INSERT INTO comentarios (item_key, usuario_id, texto) VALUES (:item_key, :usuario_id, :texto)'
    );
    $insertar->execute([
        ':item_key' => $itemKey,
        ':usuario_id' => $usuarioId,
        ':texto' => $texto,
    ]);
}

$consulta = $pdo->prepare(
    'SELECT comentarios.id, comentarios.texto, comentarios.fecha_creacion,
            usuarios.nombre_completo, usuarios.nickname, usuarios.avatar
     FROM comentarios
     INNER JOIN usuarios ON comentarios.usuario_id = usuarios.id
     WHERE comentarios.item_key = :item_key
    ORDER BY comentarios.fecha_creacion ASC, comentarios.id ASC
    LIMIT :limit OFFSET :offset'
);
$consulta->bindValue(':item_key', $itemKey, PDO::PARAM_STR);
$consulta->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
$consulta->bindValue(':offset', $offset, PDO::PARAM_INT);
$consulta->execute();

$filas = $consulta->fetchAll();
$hayMasComentarios = count($filas) > $limit;
if ($hayMasComentarios) {
    array_pop($filas);
}

$comentarios = array_map(static function (array $comentario): array {
    $avatarFilename = basename((string) ($comentario['avatar'] ?? ''));
    $avatarComentario = '../img/user.png';
    if (
        $avatarFilename !== ''
        && $avatarFilename !== 'default-avatar.png'
        && $avatarFilename !== 'user-default.png'
        && file_exists(__DIR__ . '/../uploads/avatars/' . $avatarFilename)
    ) {
        $avatarComentario = '../uploads/avatars/' . $avatarFilename;
    }

    return [
        'id' => (int) $comentario['id'],
        'name' => $comentario['nickname'] ?: $comentario['nombre_completo'],
        'avatar_url' => $avatarComentario,
        'text' => $comentario['texto'],
        'likes' => 0,
        'replies' => [],
    ];
}, $filas);

echo json_encode([
    'comments' => $comentarios,
    'has_more' => $hayMasComentarios,
], JSON_UNESCAPED_UNICODE);
