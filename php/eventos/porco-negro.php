<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Porco Negro - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('../..//img/porco.avif')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2024 | Discoteca | +18 | Av. Apolón de Mirbek esquina Av. José Enrique Rodó.</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Porco Negro</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Evento en Porco Negro: noche de música y baile en un ambiente exclusivo.</p>

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
