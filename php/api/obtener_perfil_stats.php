<?php
/* El buffer protege el contrato JSON frente a warnings de PDO o migraciones
   ejecutadas durante la carga del perfil. */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderPerfilStats(array $payload, int $status = 200): void
{
    http_response_code($status);
    ob_end_clean();
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    session_start();
    require_once __DIR__ . '/../conexion.php';
    $perfilId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($perfilId <= 0) {
        responderPerfilStats(['success' => false, 'message' => 'Perfil inválido'], 422);
    }

    $consultaPrivacidad = $pdo->prepare('SELECT perfil_publico FROM usuarios WHERE id = :id LIMIT 1');
    $consultaPrivacidad->execute([':id' => $perfilId]);
    $perfilPublico = $consultaPrivacidad->fetchColumn();
    if ($perfilPublico === false) {
        responderPerfilStats(['success' => false, 'message' => 'Usuario no encontrado'], 404);
    }
    $viewerId = (int) ($_SESSION['usuario_id'] ?? 0);
    if (!(bool) $perfilPublico && $viewerId !== $perfilId) {
        $consultaSeguidor = $pdo->prepare('SELECT 1 FROM seguidores WHERE seguidor_id = :seguidor AND seguido_id = :seguido LIMIT 1');
        $consultaSeguidor->execute([':seguidor' => $viewerId, ':seguido' => $perfilId]);
        if (!$consultaSeguidor->fetchColumn()) {
            responderPerfilStats(['success' => false, 'message' => 'Este perfil es privado'], 403);
        }
    }

    $lista = (string) ($_GET['lista'] ?? '');
    if ($lista !== '') {
        if (!in_array($lista, ['seguidores', 'seguidos'], true)) {
            responderPerfilStats(['success' => false, 'message' => 'Tipo de lista inválido'], 422);
        }
        $sqlLista = $lista === 'seguidores'
            ? 'SELECT u.id, u.nombre_completo, u.nickname, u.avatar FROM seguidores s INNER JOIN usuarios u ON u.id = s.seguidor_id WHERE s.seguido_id = :perfil_id ORDER BY s.fecha_creacion DESC, s.id DESC'
            : 'SELECT u.id, u.nombre_completo, u.nickname, u.avatar FROM seguidores s INNER JOIN usuarios u ON u.id = s.seguido_id WHERE s.seguidor_id = :perfil_id ORDER BY s.fecha_creacion DESC, s.id DESC';
        $consultaLista = $pdo->prepare($sqlLista);
        $consultaLista->execute([':perfil_id' => $perfilId]);
        $usuarios = array_map(static function (array $usuarioLista): array {
            $avatarFilename = basename((string) ($usuarioLista['avatar'] ?? ''));
            $avatarUrl = '../img/user.png';
            if ($avatarFilename !== ''
                && !in_array($avatarFilename, ['default-avatar.png', 'user-default.png', 'user.png'], true)
                && is_file(__DIR__ . '/../../uploads/avatars/' . $avatarFilename)) {
                $avatarUrl = '../uploads/avatars/' . rawurlencode($avatarFilename);
            }
            return [
                'id' => (int) $usuarioLista['id'],
                'nombre' => (string) ($usuarioLista['nombre_completo'] ?? ''),
                'nickname' => (string) ($usuarioLista['nickname'] ?? ''),
                'avatar_url' => $avatarUrl,
            ];
        }, $consultaLista->fetchAll(PDO::FETCH_ASSOC));
        responderPerfilStats(['success' => true, 'lista' => $lista, 'usuarios' => $usuarios]);
    }

    /* COUNT(*) sobre la relación seguido_id cuenta únicamente las filas que
       apuntan al perfil consultado; es una agregación relacional eficiente y
       evita cargar seguidores completos solo para obtener un número. */
    $stmtSeguidores = $pdo->prepare('SELECT COUNT(*) FROM seguidores WHERE seguido_id = :id');
    $stmtSeguidores->execute([':id' => $perfilId]);
    $totalSeguidores = (int) $stmtSeguidores->fetchColumn();

    $stmtSeguidos = $pdo->prepare('SELECT COUNT(*) FROM seguidores WHERE seguidor_id = :id');
    $stmtSeguidos->execute([':id' => $perfilId]);
    $totalSeguidos = (int) $stmtSeguidos->fetchColumn();

    /* La unión relaciona cada like con su publicación y luego con su dueño:
       COUNT(*) suma solo los likes recibidos por contenido de este usuario. */
    $stmtLikes = $pdo->prepare('SELECT COUNT(*) FROM likes_publicaciones lp INNER JOIN publicaciones p ON p.id = lp.publicacion_id WHERE p.usuario_id = :id');
    $stmtLikes->execute([':id' => $perfilId]);
    $totalLikes = (int) $stmtLikes->fetchColumn();

    responderPerfilStats([
        'success' => true,
        'seguidores' => $totalSeguidores,
        'seguidos' => $totalSeguidos,
        'likes' => $totalLikes,
    ]);
} catch (Throwable $exception) {
    responderPerfilStats(['success' => false, 'message' => 'No se pudieron cargar las estadísticas'], 500);
}
