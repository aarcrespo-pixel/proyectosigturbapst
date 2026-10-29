<?php

session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/censura.php';
require_once __DIR__ . '/helpers/autorizacion.php';

header('Content-Type: application/json; charset=utf-8');

$itemKey = trim($_GET['item_key'] ?? $_POST['item_key'] ?? '');
$usuarioIdActual = (int) ($_SESSION['usuario_id'] ?? 0);
$rolActual = 'turista';
if ($usuarioIdActual > 0) {
    $rolConsulta = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :usuario_id LIMIT 1');
    $rolConsulta->execute([':usuario_id' => $usuarioIdActual]);
    $rolActual = sigtur_normalizar_rol((string) ($rolConsulta->fetchColumn() ?: 'turista'));
}
// Limitamos la paginación para evitar respuestas excesivas y mantener el feed fluido.
$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit = min(20, max(1, (int) ($_GET['limit'] ?? 10)));
/* El borrado identifica el registro por ID y autorización; la lectura y alta
    sí requieren item_key para aislar los comentarios del recurso correcto. */
$accionesComentario = ['eliminar', 'eliminar_para_mi', 'eliminar_para_todos', 'editar'];
if ($itemKey === '' || strlen($itemKey) > 255) {
    http_response_code(400);
    echo json_encode(['error' => 'Elemento inválido']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        echo json_encode(['error' => 'La sesión expiró. Recargá la página e intentá nuevamente']);
        exit;
    }

    if (in_array($_POST['accion'] ?? '', $accionesComentario, true)) {
        $comentarioId = (int) ($_POST['id'] ?? $_POST['id_comentario'] ?? 0);
        if ($usuarioId <= 0 || $comentarioId <= 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Solicitud no autorizada']);
            exit;
        }
        $comentarioStmt = $pdo->prepare('SELECT usuario_id, item_key, parent_id, eliminado_para_todos FROM comentarios WHERE id = :id LIMIT 1');
        $comentarioStmt->execute([':id' => $comentarioId]);
        $comentario = $comentarioStmt->fetch(PDO::FETCH_ASSOC);
        if (!$comentario || !hash_equals((string) $comentario['item_key'], $itemKey)) {
            http_response_code(404);
            echo json_encode(['error' => 'Comentario no encontrado']);
            exit;
        }
        $autorId = (int) $comentario['usuario_id'];
        $esPropio = $autorId === $usuarioId;
        if ($_POST['accion'] === 'editar') {
            if (!$esPropio || (int) $comentario['eliminado_para_todos'] === 1) {
                http_response_code(403);
                echo json_encode(['error' => 'Solo puedes editar tus propios comentarios activos']);
                exit;
            }
            $textoEditado = censurarComentario(trim((string) ($_POST['texto'] ?? '')));
            if ($textoEditado === '' || strlen($textoEditado) > 140) {
                http_response_code(422);
                echo json_encode(['error' => 'El comentario debe tener entre 1 y 140 caracteres']);
                exit;
            }
            $actualizar = $pdo->prepare('UPDATE comentarios SET texto = :texto WHERE id = :id AND usuario_id = :usuario_id AND eliminado_para_todos = 0');
            $actualizar->execute([':texto' => $textoEditado, ':id' => $comentarioId, ':usuario_id' => $usuarioId]);
            if ($actualizar->rowCount() !== 1) {
                $verificar = $pdo->prepare('SELECT 1 FROM comentarios WHERE id = :id AND usuario_id = :usuario_id');
                $verificar->execute([':id' => $comentarioId, ':usuario_id' => $usuarioId]);
                if (!$verificar->fetchColumn()) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Comentario no encontrado o sin permisos']);
                    exit;
                }
            }
            echo json_encode(['success' => true, 'id' => $comentarioId, 'text' => $textoEditado]);
            exit;
        }

        $accionEliminar = $_POST['accion'] === 'eliminar' ? 'eliminar_para_todos' : $_POST['accion'];
        if ($accionEliminar === 'eliminar_para_mi') {
            if (!$esPropio && (int) $comentario['eliminado_para_todos'] !== 1) {
                http_response_code(403);
                echo json_encode(['error' => 'Solo puedes quitar de tu vista un comentario ajeno después de que su autor lo haya eliminado']);
                exit;
            }
            $ocultar = $pdo->prepare('INSERT IGNORE INTO comentarios_ocultos_usuario (comentario_id, usuario_id) VALUES (:comentario_id, :usuario_id)');
            $ocultar->execute([':comentario_id' => $comentarioId, ':usuario_id' => $usuarioId]);
            echo json_encode(['success' => true, 'id' => $comentarioId, 'accion' => $accionEliminar]);
            exit;
        }
        if (!$esPropio) {
            http_response_code(403);
            echo json_encode(['error' => 'Solo quien escribió el comentario puede eliminarlo para todos']);
            exit;
        }
        $pdo->beginTransaction();
        $pdo->prepare('DELETE FROM comentarios_ocultos_usuario WHERE comentario_id = :id')
            ->execute([':id' => $comentarioId]);
        $promoverRespuestas = $pdo->prepare(
            'UPDATE comentarios SET parent_id = :parent_id WHERE parent_id = :id AND item_key = :item_key'
        );
        $promoverRespuestas->execute([
            ':parent_id' => $comentario['parent_id'],
            ':id' => $comentarioId,
            ':item_key' => $itemKey,
        ]);
        $eliminarComentario = $pdo->prepare(
            'DELETE FROM comentarios
             WHERE id = :id AND usuario_id = :usuario_id AND item_key = :item_key AND eliminado_para_todos = 0'
        );
        $eliminarComentario->execute([
            ':id' => $comentarioId,
            ':usuario_id' => $usuarioId,
            ':item_key' => $itemKey,
        ]);
        if ($eliminarComentario->rowCount() !== 1) {
            $pdo->rollBack();
            http_response_code(404);
            echo json_encode(['error' => 'El comentario ya no está disponible']);
            exit;
        }
        $pdo->commit();
        echo json_encode(['success' => true, 'id' => $comentarioId, 'accion' => $accionEliminar]);
        exit;
    }

    // CAMBIO APLICADO: parent_id identifica opcionalmente el comentario padre;
    // cero se convierte en NULL para conservar los comentarios raíz.
    $texto = censurarComentario(trim($_POST['texto'] ?? ''));
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
        // CAMBIO APLICADO: solo aceptamos padres del mismo hilo para impedir
        // respuestas cruzadas entre tarjetas o recursos distintos.
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

/* CAMBIO APLICADO: parent_id se devuelve junto con cada fila. El frontend arma
    el árbol anidado sin una segunda consulta y conserva la relación padre-hijo. */
$consulta = $pdo->prepare(
        'SELECT comentarios.id, comentarios.usuario_id, comentarios.parent_id, comentarios.texto,
            comentarios.eliminado_para_todos, comentarios.fecha_creacion,
            usuarios.nombre_completo, usuarios.nickname, COALESCE(usuarios.foto_perfil, usuarios.avatar) AS avatar
     FROM comentarios
     INNER JOIN usuarios ON comentarios.usuario_id = usuarios.id
     WHERE comentarios.item_key = :item_key
       AND comentarios.eliminado_para_todos = 0
       AND NOT EXISTS (SELECT 1 FROM comentarios_ocultos_usuario oculto
           WHERE oculto.comentario_id = comentarios.id AND oculto.usuario_id = :usuario_id)
    ORDER BY comentarios.fecha_creacion ASC, comentarios.id ASC
    LIMIT :limit OFFSET :offset'
);
$consulta->bindValue(':item_key', $itemKey, PDO::PARAM_STR);
$consulta->bindValue(':usuario_id', $usuarioIdActual, PDO::PARAM_INT);
$consulta->bindValue(':limit', $limit + 1, PDO::PARAM_INT);
$consulta->bindValue(':offset', $offset, PDO::PARAM_INT);
$consulta->execute();

$filas = $consulta->fetchAll();
$hayMasComentarios = count($filas) > $limit;
if ($hayMasComentarios) {
    array_pop($filas);
}

// Adaptamos las filas SQL al contrato simple que consume el renderer JavaScript.
$comentarios = array_map(static function (array $comentario) use ($usuarioIdActual, $rolActual): array {
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
        'text' => (int) $comentario['eliminado_para_todos'] === 1 ? '' : censurarComentario((string) $comentario['texto']),
        'deleted' => (int) $comentario['eliminado_para_todos'] === 1,
        'can_edit' => (int) $comentario['usuario_id'] === $usuarioIdActual && (int) $comentario['eliminado_para_todos'] !== 1,
        'can_delete_everyone' => (int) $comentario['eliminado_para_todos'] !== 1
            && (int) $comentario['usuario_id'] === $usuarioIdActual,
        'can_delete_me' => (int) $comentario['usuario_id'] === $usuarioIdActual || (int) $comentario['eliminado_para_todos'] === 1,
        'likes' => 0,
        'replies' => [],
    ];
}, $filas);

echo json_encode([
    'comments' => $comentarios,
    'has_more' => $hayMasComentarios,
    'viewer_id' => $usuarioIdActual,
    'viewer_role' => $rolActual,
], JSON_UNESCAPED_UNICODE);
