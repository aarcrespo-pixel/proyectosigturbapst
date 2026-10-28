<?php
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderBusquedaPerfiles(array $datos, int $estado = 200): void
{
    http_response_code($estado);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        responderBusquedaPerfiles(['success' => false, 'message' => 'Método no permitido.'], 405);
    }

    session_start();
    require_once __DIR__ . '/../conexion.php';
    $consulta = trim((string) ($_GET['q'] ?? ''));
    if (mb_strlen($consulta, 'UTF-8') < 2) {
        responderBusquedaPerfiles(['success' => true, 'usuarios' => []]);
    }

    $consulta = mb_substr($consulta, 0, 80, 'UTF-8');
    $termino = '%' . strtr($consulta, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
    $usuarioActual = (int) ($_SESSION['usuario_id'] ?? 0);
    $buscar = $pdo->prepare("SELECT id, nombre_completo, nickname, avatar,
            CASE WHEN LOWER(TRIM(rol)) IN ('administrador', 'admin', 'moderador', 'organizador')
                THEN 1 ELSE verificado END AS verificado
        FROM usuarios
        WHERE id <> :usuario_actual
          AND (nombre_completo LIKE :nombre ESCAPE '!' OR nickname LIKE :nickname ESCAPE '!')
        ORDER BY CASE WHEN nickname = :coincidencia THEN 0 WHEN nombre_completo = :coincidencia_nombre THEN 1 ELSE 2 END,
                 COALESCE(NULLIF(nickname, ''), nombre_completo) ASC
        LIMIT 12");
    $buscar->execute([
        ':usuario_actual' => $usuarioActual,
        ':nombre' => $termino,
        ':nickname' => $termino,
        ':coincidencia' => $consulta,
        ':coincidencia_nombre' => $consulta,
    ]);

    $usuarios = array_map(static function (array $usuario): array {
        $avatar = basename((string) ($usuario['avatar'] ?? ''));
        $avatarUrl = '../img/user.png';
        if ($avatar !== '' && !in_array($avatar, ['default-avatar.png', 'user-default.png', 'user.png'], true)
            && is_file(__DIR__ . '/../../uploads/avatars/' . $avatar)) {
            $avatarUrl = '../uploads/avatars/' . rawurlencode($avatar);
        }
        return [
            'id' => (int) $usuario['id'],
            'nombre' => (string) ($usuario['nombre_completo'] ?? ''),
            'nickname' => (string) ($usuario['nickname'] ?? ''),
            'verificado' => (bool) ($usuario['verificado'] ?? false),
            'avatar_url' => $avatarUrl,
        ];
    }, $buscar->fetchAll(PDO::FETCH_ASSOC));

    responderBusquedaPerfiles(['success' => true, 'usuarios' => $usuarios]);
} catch (Throwable $error) {
    error_log('Error al buscar perfiles: ' . $error->getMessage());
    responderBusquedaPerfiles(['success' => false, 'message' => 'No se pudieron buscar perfiles.'], 500);
}
