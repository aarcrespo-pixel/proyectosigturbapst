<?php
$navBase = '../';
$activePage = 'eventos';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrera en Bicicleta - Evento</title>
    <link rel="stylesheet" href="../../css/estilos.css">
    <link rel="stylesheet" href="../../css/detalle-evento.css">
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="detalle-hero" id="detalle-hero" style="background-image:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10')">
        <div class="overlay"></div>
        <div class="detalle-contenido">
            <div id="meta-tags" class="meta-tags">2026 | Deportivo | +16 | Circuito Artigas</div>
            <h1 id="detalle-titulo" class="detalle-titulo">Carrera en Bicicleta</h1>
            <p id="detalle-descripcion" class="detalle-descripcion">Recorrido en bicicleta por la ciudad, apto para corredores y ciclistas de distintos niveles.</p>

            <div class="detalle-acciones">
                <button class="btn btn-outline">AÃ±adir a calendario</button>
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
