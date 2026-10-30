<?php
session_start();
require_once __DIR__ . '/conexion.php';

$eventosPasados = [
    'festival-de-la-naranja' => ['Festival de la Naranja', 'Cultura', 'Plaza Artigas', '2025-11-08', '../img/festival_naranja.jpg', 'Feria, degustaciones, música y actividades para celebrar la producción local.'],
    'carrera-nocturna' => ['Carrera Nocturna', 'Deportivo', 'Costanera Norte', '2025-10-04', '../img/carrera_noche.webp', 'Una carrera urbana que reunió a la comunidad de Salto.'],
    'muestra-de-danza' => ['Muestra de Danza', 'Cultura', 'Centro Cultural', '2025-12-06', '../img/danza.jpg', 'Una muestra de talento y expresión local.'],
    'feria-de-emprendedores' => ['Feria de Emprendedores', 'Local', 'Mercado Central', '2025-10-25', '../img/feria_emprendedores.jpg', 'Productos y proyectos de emprendedores salteños.'],
    'lafosa-bike' => ['LaFosa Bike', 'Deportivo', 'La Fosa', '2025-09-13', '../img/fosa.webp', 'Circuito de bicicleta, skate y calistenia.'],
];

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['id'] ?? ''));
$evento = $eventosPasados[$slug] ?? null;
if (!$evento) {
    http_response_code(404);
    exit('Evento histórico no encontrado.');
}
[$nombre, $categoria, $lugar, $fecha, $imagen, $descripcion] = $evento;
$fechaLegible = date('d/m/Y', strtotime($fecha));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> | Histórico | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css?v=nav-reveal-hint-20261029-1">
    <link rel="stylesheet" href="../css/detalle-evento.css">
    <link rel="stylesheet" href="../css/eventos.css">
    <link rel="stylesheet" href="../css/enlaces-texto.css">
    <style>
        body.past-event{background:#eeeae3;color:#20221f}.past-hero{min-height:58vh;display:flex;align-items:flex-end;padding:5rem clamp(1.25rem,7vw,7rem);background:linear-gradient(180deg,rgba(0,0,0,.08),rgba(0,0,0,.82)),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>') center/cover;color:#fff}.past-hero__content{max-width:760px;align-items:flex-start}.past-hero h1{margin:.5rem 0;font-size:clamp(2.5rem,7vw,5.5rem);line-height:.98}.past-hero p{max-width:650px;line-height:1.6;color:rgba(255,255,255,.86)}.past-meta{display:flex;flex-wrap:wrap;gap:.55rem}.past-badge{display:inline-flex;padding:.45rem .8rem;border-radius:999px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.34);font-size:.84rem}.past-status{width:max-content;display:inline-flex;margin-top:1.2rem;padding:.7rem 1rem;background:#173f35;color:#f4d27b;border-radius:8px;font-weight:800}.past-main{width:min(1120px,92%);margin:0 auto;padding:5rem 0 6rem}.past-intro{display:grid;grid-template-columns:1fr 1.3fr;gap:3rem;align-items:start}.past-intro h2{margin-top:0}.past-intro p{line-height:1.7}.past-gallery{margin-top:4rem}.past-gallery h2{margin-bottom:1rem}.past-gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}.past-gallery-grid button{border:0;padding:0;aspect-ratio:4/3;overflow:hidden;cursor:pointer;background:#222;border-radius:8px}.past-gallery-grid img{width:100%;height:100%;object-fit:cover;transition:transform .25s}.past-gallery-grid button:hover img{transform:scale(1.04)}@media(max-width:700px){.past-intro{grid-template-columns:1fr}.past-gallery-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
    <style>
        body.past-event,body.past-event main,body.past-event .past-main{background:#f4f6f8!important;color:#11151b!important}
        body.past-event .past-hero{position:relative;isolation:isolate;width:min(1180px,94%);min-height:0;margin:0 auto;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);align-items:center;gap:clamp(1.5rem,4vw,4rem);overflow:visible;background:#f4f6f8!important;color:#11151b;padding:clamp(7rem,15vh,9rem) 1.5rem 3rem}
        body.past-event .past-hero::before{display:none}
        .past-hero__image{position:relative;z-index:1;display:block;width:100%;height:clamp(360px,55vh,520px);margin:0;object-fit:contain;object-position:center;border-radius:20px;background:#e5e7eb}
        body.past-event .past-hero__content{position:relative;z-index:2;width:100%;max-width:760px;margin:0;padding:0;display:flex;flex-direction:column;gap:12px;color:#11151b}
        body.past-event .past-hero h1,body.past-event .past-hero p,body.past-event .past-badge{color:#11151b;text-shadow:none}
        body.past-event .past-badge{background:#fff;border-color:#d1d5db}
        .past-info{padding:clamp(1.25rem,3vw,2rem);border:1px solid #dfe3e8;border-radius:20px;background:#fff;color:#11151b;box-shadow:0 10px 28px rgba(17,21,27,.06)}
        .past-info h2{margin:0 0 1rem;color:#11151b}
        .past-info p{line-height:1.65;color:#374151}
        .past-status{display:inline-flex;align-items:center;width:max-content;padding:.55rem .9rem;border:1px solid #fca5a5;border-radius:999px;background:#fef2f2;color:#dc2626;font-size:.82rem;font-weight:800;letter-spacing:.04em}
        .past-back{display:inline-flex;align-items:center;justify-content:center;width:max-content;padding:.75rem 1rem;border-radius:999px;background:#176b87;color:#fff;font-weight:700;text-decoration:none;transition:background .2s ease,transform .2s ease}
        .past-back:hover{background:#0f526a;transform:translateY(-1px)}
        .past-back:focus-visible{outline:3px solid #f59e0b;outline-offset:3px}
        @media(max-width:700px){body.past-event .past-hero{grid-template-columns:minmax(0,1fr);gap:1.25rem;padding:6.5rem 0 2rem}body.past-event .past-hero__content{grid-row:1;max-width:none}.past-hero__image{grid-row:2;height:clamp(250px,38vh,340px)}}
    </style>
</head>
<body class="past-event event-detail">
    <?php $navBase = ''; $activePage = 'eventos'; require __DIR__ . '/nav.php'; ?>
    <main>
        <section class="past-hero"><img class="past-hero__image" src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"><div class="past-hero__content"><div class="past-meta"><span class="past-badge"><?= htmlspecialchars($fechaLegible) ?></span><span class="past-badge"><?= htmlspecialchars($categoria) ?></span></div><h1><?= htmlspecialchars($nombre) ?></h1><span class="past-status">EVENTO FINALIZADO</span><p><?= htmlspecialchars($descripcion) ?></p><a class="past-back" href="eventos.php">← Volver a eventos</a></div></section>
        <section class="past-main">
            <section class="past-info"><h2>Sobre el evento</h2><p><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p></section>
        </section>
    </main>
    <script src="../js/script.js?v=nav-reveal-hint-20261029-1" defer></script>
<script src="../js/translator.js" defer></script>
<script src="../js/enlaces-texto.js" defer></script>
</body>
</html>