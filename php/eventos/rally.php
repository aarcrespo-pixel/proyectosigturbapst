<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rally - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2024 | Carrera | +0 | Av. Horacio Quiroga 9382</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Rally</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Competencia de rally con salida en Av. Horacio Quiroga. Requiere inscripción previa.</p>

            <div class="detalle-acciones">
                <button class="btn btn-outline">Añadir a calendario</button>
                <button class="btn btn-outline">Compartir</button>
                <button class="btn btn-primary">Inscribirse</button>
            </div>
        </div>

    </main>

    <script src="../../js/eventos.js"></script>
    <script src="../../js/detalle-evento.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/ith.js"></script>
</body>
</html>
