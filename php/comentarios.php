<?php

session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/censura.php';

header('Content-Type: application/json; charset=utf-8');

$itemKey = trim($_GET['item_key'] ?? $_POST['item_key'] ?? '');
// Limitamos la paginación para evitar respuestas excesivas y mantener el feed fluido.
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));
/* El borrado identifica el registro por ID y autorización; la lectura y alta
    sí requieren item_key para aislar los comentarios del recurso correcto. */
$esEliminacion = $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar';
if ((!$esEliminacion && $itemKey === '') || strlen($itemKey) > 255) {
    http_response_code(400);
    echo json_encode(['error' => 'Elemento inválido']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

    /* La eliminación comparte este controlador con la publicación: se valida
       sesión y autorización antes de ejecutar el DELETE parametrizado. */
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $comentarioId = (int) ($_POST['id'] ?? $_POST['id_comentario'] ?? 0);
        if ($usuarioId <= 0 || $comentarioId <= 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Solicitud no autorizada']);
            exit;
        }
          /* Leemos el rol vigente desde MySQL para que una sesión antigua no
              conserve permisos incorrectos después de la migración de usuarios. */
          $rolConsulta = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :usuario_id LIMIT 1');
          $rolConsulta->execute([':usuario_id' => $usuarioId]);
          $rol = (string) ($rolConsulta->fetchColumn() ?: 'usuario');
        $eliminar = $pdo->prepare("DELETE FROM comentarios WHERE id = :id AND (usuario_id = :usuario_id OR :rol = 'moderador')");
        $eliminar->execute([
            ':id' => $comentarioId,
            ':usuario_id' => $usuarioId,
            ':rol' => $rol,
        ]);
        if ($eliminar->rowCount() !== 1) {
            http_response_code(404);
            echo json_encode(['error' => 'Comentario no encontrado o sin permisos']);
            exit;
        }
        echo json_encode(['success' => true, 'id' => $comentarioId]);
        exit;
    }

    $texto = censurarTexto(trim($_POST['texto'] ?? ''));
    $parentId = max(0, (int) ($_POST['parent_id'] ?? 0));

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

    if ($parentId > 0) {
        $padre = $pdo->prepare('SELECT id FROM comentarios WHERE id = :id AND item_key = :item_key LIMIT 1');
        $padre->execute([':id' => $parentId, ':item_key' => $itemKey]);
        if (!$padre->fetchColumn()) {
            http_response_code(422);
            echo json_encode(['error' => 'El comentario padre no pertenece a este hilo']);
            exit;
        }
    }

     /* La inserción usa parámetros separados del SQL; después se consulta el
         feed completo para sincronizar DOM, localStorage y base de datos. */
     $insertar = $pdo->prepare(
        'INSERT INTO comentarios (item_key, usuario_id, parent_id, texto) VALUES (:item_key, :usuario_id, :parent_id, :texto)'
    );
    $insertar->execute([
        ':item_key' => $itemKey,
        ':usuario_id' => $usuarioId,
        ':parent_id' => $parentId > 0 ? $parentId : null,
        ':texto' => $texto,
    ]);

     /* Después de publicar devolvemos el feed completo desde MySQL para que el
         cliente nunca mezcle una página local obsoleta con datos nuevos. */
    $offset = 0;
    $limit = 1000;
}

/* JOIN combina comentario, usuario y avatar en una sola respuesta para que el
    frontend pueda renderizar cada ítem sin hacer consultas adicionales. */
$consulta = $pdo->prepare(
        'SELECT comentarios.id, comentarios.usuario_id, comentarios.parent_id, comentarios.texto, comentarios.fecha_creacion,
            usuarios.nombre_completo, usuarios.nickname, COALESCE(usuarios.foto_perfil, usuarios.avatar) AS avatar
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

// Adaptamos las filas SQL al contrato simple que consume el renderer JavaScript.
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
        'user_id' => (int) $comentario['usuario_id'],
        'parent_id' => $comentario['parent_id'] !== null ? (int) $comentario['parent_id'] : null,
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
