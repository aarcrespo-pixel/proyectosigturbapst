<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
$administrador = sigtur_requerir_rol($pdo, ['administrador'], 'login.php', 'eventos.php');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    exit('Esta pantalla solo permite consultar usuarios.');
}

$usuarios = $pdo->query('SELECT id, nombre_completo, email, rol, fecha_registro FROM usuarios ORDER BY nombre_completo ASC')->fetchAll();
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Usuarios | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"><style>
body{background:#f4f6f8;color:#18212a}.panel{max-width:1100px;margin:0 auto;padding:2rem 1.25rem 5rem}.head{display:flex;justify-content:space-between;align-items:center;gap:1rem}.links{display:flex;gap:.6rem;flex-wrap:wrap}.links a{display:inline-block;padding:.7rem 1rem;border:0;border-radius:8px;background:#152d3d;color:#fff;text-decoration:none;font-weight:700}.user{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:1rem;align-items:center;margin:.75rem 0;padding:1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px}.user p{margin:.25rem 0;color:#65727d}@media(max-width:640px){.head,.user{display:flex;align-items:flex-start;flex-direction:column}}
</style><link rel="stylesheet" href="../css/listado-expandible.css"></head><body><main class="panel"><header class="head"><div><p>Administración</p><h1>Gestionar usuarios</h1><p>Consulta los datos y el rol actual de cada usuario. Los roles no se editan desde esta pantalla.</p></div><nav class="links"><a href="panel-admin.php">Panel admin</a><a href="../index.php">Inicio</a></nav></header><section><div class="admin-lista-expandible" data-lista-expandible data-cantidad-inicial="10"><?php foreach ($usuarios as $usuario): $rol = sigtur_normalizar_rol((string) $usuario['rol']); ?><article class="user" data-lista-item><div><strong><?= htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong><p><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?> · Rol actual: <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?></p></div></article><?php endforeach; ?></div><?php if (count($usuarios) > 10): ?><button class="admin-lista-expandible__boton" type="button" data-lista-boton data-texto-mas="Ver más usuarios" data-texto-menos="Ver menos usuarios" aria-expanded="false">Ver más usuarios</button><?php endif; ?></section></main><script src="../js/listado-expandible.js" defer></script></body></html>