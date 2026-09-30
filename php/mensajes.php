<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_requerir_rol($pdo, ['administrador'], 'login.php', '../index.php');
$reportesStmt = $pdo->query("SELECT m.id, m.mensaje, m.contexto, m.fecha_creacion, u.nombre_completo, u.email FROM mensajes m INNER JOIN usuarios u ON u.id = m.remitente_id WHERE m.mensaje LIKE '[Consulta %' ORDER BY m.fecha_creacion DESC LIMIT 100");
$reportes = $reportesStmt->fetchAll();
$headerCurrentPage = 'mensajes';
$headerActivePage = 'mensajes';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mensajes de administración | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .admin-mensajes-main{width:min(100% - 2rem,900px);margin:5rem auto;padding:2rem 0;color:#17232d}
        .admin-mensajes-main h1{margin:0 0 .6rem;font-size:2rem}
        .admin-mensajes-main p{max-width:650px;line-height:1.6;color:#586673}
        .admin-mensajes-main a{display:inline-block;margin-top:.75rem;color:#15526f;font-weight:700}
        .admin-mensajes-reportes{margin-top:3rem}.admin-mensajes-reporte{margin:.8rem 0;padding:1rem;background:#fff;border:1px solid #dce3e8;border-radius:8px}.admin-mensajes-reporte p{margin:.5rem 0}
    </style>
</head>
<body class="pagina-mensajes-admin">
    <?php include __DIR__ . '/header.php'; ?>
    <main class="admin-mensajes-main">
        <h1>Mensajes entre administradores</h1>
        <p>La bandeja muestra conversaciones únicamente entre cuentas administradoras.</p>
        <a href="panel-admin.php">Volver al panel de administración</a>
        <section class="admin-mensajes-reportes" aria-labelledby="reportesTitle">
            <h2 id="reportesTitle">Reportes de soporte recibidos</h2>
            <?php if (!$reportes): ?><p>No hay reportes pendientes de revisión.</p><?php endif; ?>
            <?php foreach ($reportes as $reporte): ?>
                <article class="admin-mensajes-reporte">
                    <strong><?= htmlspecialchars($reporte['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <span><?= htmlspecialchars($reporte['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    <p><?= nl2br(htmlspecialchars(preg_replace('/^\[Consulta [^\]]+\]:\s*/', '', $reporte['mensaje']) ?? $reporte['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                    <small><?= htmlspecialchars($reporte['contexto'] ?? 'Soporte', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($reporte['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>