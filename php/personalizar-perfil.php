<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/conexion.php';

$userId = (int) $_SESSION['usuario_id'];
$nickname = trim($_POST['nickname'] ?? '');
$biografia = trim($_POST['biografia'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$sitioWeb = trim($_POST['sitio_web'] ?? '');

if ($nickname !== '' && strlen($nickname) > 50) {
    $nickname = substr($nickname, 0, 50);
}

if ($ubicacion !== '' && strlen($ubicacion) > 100) {
    $ubicacion = substr($ubicacion, 0, 100);
}

if ($biografia !== '' && strlen($biografia) > 150) {
    $biografia = substr($biografia, 0, 150);
}

if ($sitioWeb !== '' && !filter_var($sitioWeb, FILTER_VALIDATE_URL)) {
    $sitioWeb = '';
}

$avatarNombre = $_SESSION['usuario_avatar'] ?? 'default-avatar.png';

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['avatar']['tmp_name'];
    $nombreOriginal = basename($_FILES['avatar']['name']);
    $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (in_array($extension, $permitidas, true)) {
        $nombreArchivo = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $destino = __DIR__ . '/../uploads/avatars/' . $nombreArchivo;

        if (move_uploaded_file($tmpName, $destino)) {
            $avatarNombre = $nombreArchivo;
            $avatarActual = $_SESSION['usuario_avatar'] ?? 'default-avatar.png';
            if (!empty($avatarActual) && $avatarActual !== 'default-avatar.png' && file_exists(__DIR__ . '/../uploads/avatars/' . basename($avatarActual))) {
                @unlink(__DIR__ . '/../uploads/avatars/' . basename($avatarActual));
            }
        }
    }
}

$stmt = $pdo->prepare(
    'UPDATE usuarios SET
        avatar = :avatar,
        nickname = :nickname,
        biografia = :biografia,
        ubicacion = :ubicacion,
        sitio_web = :sitio_web
    WHERE id = :id'
);

$stmt->execute([
    ':avatar' => $avatarNombre,
    ':nickname' => $nickname !== '' ? $nickname : null,
    ':biografia' => $biografia !== '' ? $biografia : null,
    ':ubicacion' => $ubicacion !== '' ? $ubicacion : null,
    ':sitio_web' => $sitioWeb !== '' ? $sitioWeb : null,
    ':id' => $userId,
]);

$_SESSION['usuario_avatar'] = $avatarNombre;
$_SESSION['usuario_nickname'] = $nickname !== '' ? $nickname : strtolower(str_replace(' ', '', $_SESSION['usuario_nombre'] ?? 'usuario'));
$_SESSION['usuario_biografia'] = $biografia;
$_SESSION['usuario_ubicacion'] = $ubicacion;
$_SESSION['usuario_sitio_web'] = $sitioWeb;

header('Location: ../index.php');
exit;
