<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>La Ferne - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDFLlNliF-KGMj2Lga3JGKfp0Hk3AyFP84cyTbf776SuztpI-AGEg7Ca0&s=10')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2024 | Discoteca | +18 | Costanera Norte</div>
            <h1 id="detalle-titulo" class="detalle-titulo">La Ferne</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Una noche con DJ en Costanera Norte. Ideal para quienes buscan música electrónica y ambiente nocturno.</p>

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
