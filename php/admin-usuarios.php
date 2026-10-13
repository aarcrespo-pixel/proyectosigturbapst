<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
$csrfToken = sigtur_csrf_token();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
    }
    $usuarioId = (int) ($_POST['usuario_id'] ?? 0);
    $rolNuevo = (string) ($_POST['rol'] ?? '');
    if (!in_array($rolNuevo, ['turista', 'organizador'], true)) {
        http_response_code(422);
        exit('Solo se puede asignar el rol turista u organizador.');
    }
    $consultaUsuario = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id LIMIT 1');
    $consultaUsuario->execute([':id' => $usuarioId]);
    $rolActual = $consultaUsuario->fetchColumn();
    if ($rolActual === false) {
        http_response_code(404);
        exit('El usuario no existe.');
    }
    if (sigtur_normalizar_rol((string) $rolActual) === 'administrador') {
        http_response_code(403);
        exit('Los administradores no se pueden cambiar desde esta pantalla.');
    }
    if ($rolNuevo === 'organizador') {
        sigtur_liberar_catalogo_legacy($pdo, $usuarioId);
    }
    $actualizarRol = $pdo->prepare('UPDATE usuarios SET rol = :rol WHERE id = :id');
    $actualizarRol->execute([':rol' => $rolNuevo, ':id' => $usuarioId]);
    header('Location: admin-usuarios.php?guardado=1');
    exit;
}

$usuarios = $pdo->query('SELECT id, nombre_completo, email, rol, fecha_registro FROM usuarios ORDER BY nombre_completo ASC')->fetchAll();
if (isset($_GET['guardado'])) {
    $mensaje = 'El rol se actualizó correctamente.';
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Usuarios | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"><style>
body{background:#f4f6f8;color:#18212a}.panel{max-width:1100px;margin:0 auto;padding:2rem 1.25rem 5rem}.head{display:flex;justify-content:space-between;align-items:center;gap:1rem}.links{display:flex;gap:.6rem;flex-wrap:wrap}.links a,.save{display:inline-block;padding:.7rem 1rem;border:0;border-radius:8px;background:#152d3d;color:#fff;text-decoration:none;font-weight:700}.notice{padding:.8rem 1rem;background:#e8f6ee;color:#225b37;border-radius:8px}.user{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:center;margin:.75rem 0;padding:1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px}.user p{margin:.25rem 0;color:#65727d}.role-form{display:flex;align-items:center;gap:.5rem}.role-form select{padding:.65rem;border:1px solid #cbd4db;border-radius:6px;font:inherit}.save{cursor:pointer}@media(max-width:640px){.head,.user{display:flex;align-items:flex-start;flex-direction:column}.role-form{width:100%}.role-form select{flex:1}}
</style></head><body><main class="panel"><header class="head"><div><p>Administración</p><h1>Gestionar usuarios</h1><p>Solo se pueden asignar los roles turista y organizador.</p></div><nav class="links"><a href="panel-admin.php">Panel admin</a><a href="../index.php">Inicio</a></nav></header><?php if ($mensaje): ?><p class="notice" role="status"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><section><?php foreach ($usuarios as $usuario): $rol = sigtur_normalizar_rol((string) $usuario['rol']); ?><article class="user"><div><strong><?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?> · Rol actual: <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?></p></div><?php if ($rol === 'administrador'): ?><span>Cuenta administradora protegida</span><?php else: ?><form class="role-form" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="usuario_id" value="<?= (int) $usuario['id'] ?>"><label class="sr-only" for="rol-<?= (int) $usuario['id'] ?>">Rol de <?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></label><select id="rol-<?= (int) $usuario['id'] ?>" name="rol"><option value="turista" <?= $rol === 'turista' ? 'selected' : '' ?>>Turista</option><option value="organizador" <?= $rol === 'organizador' ? 'selected' : '' ?>>Organizador</option></select><button class="save" type="submit">Guardar rol</button></form><?php endif; ?></article><?php endforeach; ?></section></main></body></html>