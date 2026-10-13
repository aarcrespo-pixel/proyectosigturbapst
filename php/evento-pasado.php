<?php
session_start();
require_once __DIR__ . '/conexion.php';

$eventosPasados = [
    'festival-de-la-naranja' => ['Festival de la Naranja', 'Cultura', 'Plaza Artigas', '2025-11-08', '../../img/festival_naranja.jpg', 'Feria, degustaciones, música y actividades para celebrar la producción local.'],
    'carrera-nocturna' => ['Carrera Nocturna', 'Deportivo', 'Costanera Norte', '2025-10-04', '../../img/carrera_noche.webp', 'Una carrera urbana que reunió a la comunidad de Salto.'],
    'muestra-de-danza' => ['Muestra de Danza', 'Cultura', 'Centro Cultural', '2025-12-06', '../../img/danza.jpg', 'Una muestra de talento y expresión local.'],
    'feria-de-emprendedores' => ['Feria de Emprendedores', 'Local', 'Mercado Central', '2025-10-25', '../../img/feria_emprendedores.jpg', 'Productos y proyectos de emprendedores salteños.'],
    'lafosa-bike' => ['LaFosa Bike', 'Deportivo', 'La Fosa', '2025-09-13', '../../img/fosa.webp', 'Circuito de bicicleta, skate y calistenia.'],
];

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['id'] ?? ''));
$evento = $eventosPasados[$slug] ?? null;
if (!$evento) {
    http_response_code(404);
    exit('Evento histórico no encontrado.');
}
[$nombre, $categoria, $lugar, $fecha, $imagen, $descripcion] = $evento;
$fechaLegible = date('d/m/Y', strtotime($fecha));
$itemKey = 'evento-' . $slug;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> | Histórico | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/detalle-evento.css">
    <link rel="stylesheet" href="../css/eventos.css">
    <link rel="stylesheet" href="../css/evento-finalizado.css">
</head>
<body class="past-event event-detail event-finished">
    <?php $navBase = ''; $activePage = 'eventos'; require __DIR__ . '/nav.php'; ?>
    <main>
        <section class="past-hero" style="background-image: linear-gradient(180deg, rgba(12, 18, 28, .12) 0%, rgba(12, 18, 28, .9) 100%), url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>')">
            <div class="past-hero__content">
                <div class="past-meta">
                    <span class="past-badge"><?= htmlspecialchars($fechaLegible) ?></span>
                    <span class="past-badge"><?= htmlspecialchars($categoria) ?></span>
                    <span class="past-badge"><?= htmlspecialchars($lugar) ?></span>
                </div>
                <h1><?= htmlspecialchars($nombre) ?></h1>
                <p><?= htmlspecialchars($descripcion) ?></p>
                <strong class="past-status">Evento finalizado</strong>
            </div>
        </section>
        <section class="past-main">
            <section class="past-intro" aria-labelledby="past-summary-title">
                <div class="past-intro__copy">
                    <span class="past-kicker">Archivo de eventos</span>
                    <h2 id="past-summary-title">Sobre este evento</h2>
                    <p><?= htmlspecialchars($descripcion) ?></p>
                    <dl class="past-details">
                        <div><dt>Fecha</dt><dd><?= htmlspecialchars($fechaLegible) ?></dd></div>
                        <div><dt>Lugar</dt><dd><?= htmlspecialchars($lugar) ?></dd></div>
                        <div><dt>Categoría</dt><dd><?= htmlspecialchars($categoria) ?></dd></div>
                    </dl>
                </div>
                <article class="past-engagement" aria-label="Interacciones del evento">
                    <h2>Compartí tu recuerdo</h2>
                    <p>Dejá un me gusta o un comentario sobre <?= htmlspecialchars($nombre) ?>.</p>
                    <div class="card-engagement" data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>"></div>
                </article>
            </section>
        </section>
    </main>
    <script src="../js/interacciones-tarjetas.js"></script>
</body>
</html>