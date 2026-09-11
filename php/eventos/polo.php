<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Polo - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2024 | Disfraces | +0 | Polo Club</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Polo</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Evento temático en el Polo Club con actividades y espectáculos para todas las edades.</p>

            <div class="detalle-acciones">
                <button class="btn btn-outline">Añadir a calendario</button>
                <button class="btn btn-outline">Compartir</button>
                <button class="btn btn-primary">Inscribirse</button>
            </div>
        </div>

    </main>

    <script src="../../js/eventos.js"></script>
    <script src="../../js/detalle-evento.js"></script>

</body>
</html>
