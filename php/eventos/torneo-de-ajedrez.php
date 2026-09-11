<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Torneo de Ajedrez - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2024 | Mental | +0 | Plaza de Deportes</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Torneo de Ajedrez</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Encuentro de jugadores de ajedrez con categorías por edad y niveles, abierto a la comunidad.</p>

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
