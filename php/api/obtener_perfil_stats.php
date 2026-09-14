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

    /* COUNT(*) sobre la relación seguido_id cuenta únicamente las filas que
       apuntan al perfil consultado; es una agregación relacional eficiente y
       evita cargar seguidores completos solo para obtener un número. */
    $stmtSeguidores = $pdo->prepare('SELECT COUNT(*) FROM seguidores WHERE seguido_id = :id');
    $stmtSeguidores->execute([':id' => $perfilId]);
    $totalSeguidores = (int) $stmtSeguidores->fetchColumn();

    /* La unión relaciona cada like con su publicación y luego con su dueño:
       COUNT(*) suma solo los likes recibidos por contenido de este usuario. */
    $stmtLikes = $pdo->prepare('SELECT COUNT(*) FROM likes_publicaciones lp INNER JOIN publicaciones p ON p.id = lp.publicacion_id WHERE p.usuario_id = :id');
    $stmtLikes->execute([':id' => $perfilId]);
    $totalLikes = (int) $stmtLikes->fetchColumn();

    responderPerfilStats([
        'success' => true,
        'seguidores' => $totalSeguidores,
        'likes' => $totalLikes,
    ]);
} catch (Throwable $exception) {
    responderPerfilStats(['success' => false, 'message' => 'No se pudieron cargar las estadísticas'], 500);
}
