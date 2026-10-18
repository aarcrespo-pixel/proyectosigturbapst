<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_requerir_rol($pdo, ['administrador'], 'login.php', '../index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administración | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
</head>
<body class="admin-panel-page admin-dashboard-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · Administración</p>
                <h1>Panel de administración</h1>
            </div>
            <nav class="admin-nav" aria-label="Accesos de administración">
                <a class="admin-nav__link" href="admin-usuarios.php">Gestionar usuarios</a>
                <a class="admin-nav__link" href="admin-solicitudes-rango.php">Solicitud de rango</a>
                <a class="admin-nav__link" href="mensajes.php">Gestionar mensajes</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>
        <section class="admin-section admin-role-intro" aria-labelledby="admin-role-title">
            <p class="admin-eyebrow">Tu rol en SIGTUR</p>
            <h2 id="admin-role-title">Administrador</h2>
            <p>Desde este panel tienes a tu cargo el acompañamiento y la supervisión general de la comunidad: puedes consultar los roles de los usuarios, revisar las solicitudes de cambio de rango y atender las preguntas y reportes que llegan a través de los mensajes. Tu trabajo ayuda a mantener la información organizada, dar respuesta a quienes visitan la plataforma y asegurar que cada persona tenga los permisos adecuados. Utiliza estos accesos para gestionar cada tarea y vuelve al inicio cuando quieras recorrer el sitio como visitante.</p>
        </section>
    </main>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
