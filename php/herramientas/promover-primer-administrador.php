<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';

$email = trim((string) ($argv[1] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Uso: php php/herramientas/promover-primer-administrador.php correo@dominio.com\n");
    exit(2);
}

$administradorExistente = $pdo->query("SELECT id FROM usuarios WHERE rol = 'administrador' LIMIT 1")->fetchColumn();
if ($administradorExistente) {
    fwrite(STDERR, "Ya existe un administrador; el bootstrap inicial se cancela.\n");
    exit(3);
}

$consultaUsuario = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
$consultaUsuario->execute([':email' => $email]);
$usuarioId = $consultaUsuario->fetchColumn();
if (!$usuarioId) {
    fwrite(STDERR, "No existe una cuenta con ese correo. Crea primero la cuenta normal y vuelve a intentarlo.\n");
    exit(4);
}

$pdo->beginTransaction();
try {
    sigtur_liberar_catalogo_legacy($pdo, (int) $usuarioId);
    $promover = $pdo->prepare("UPDATE usuarios SET rol = 'administrador' WHERE id = :id AND rol <> 'administrador'");
    $promover->execute([':id' => (int) $usuarioId]);
    $pdo->commit();
} catch (Throwable $error) {
    $pdo->rollBack();
    throw $error;
}
fwrite(STDOUT, "La cuenta existente fue promovida a administrador.\n");
