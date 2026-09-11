<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Campeonato de Pesca - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2026 | Naturaleza | +13 | Costanera Sur</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Campeonato de Pesca</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Competencia de pesca deportiva en la Costanera Sur con premios y actividades paralelas.</p>

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
