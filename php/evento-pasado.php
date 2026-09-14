<?php
session_start();
require_once __DIR__ . '/conexion.php';

$eventosPasados = [
    'festival-de-la-naranja' => ['Festival de la Naranja', 'Cultura', 'Plaza Artigas', '2025-11-08', '../../img/festival_naranja.jpg', 'Feria, degustaciones, música y actividades para celebrar la producción local.', ['../../img/festival_naranja.jpg', '../../img/Plaza_artigas.webp']],
    'carrera-nocturna' => ['Carrera Nocturna', 'Deportivo', 'Costanera Norte', '2025-10-04', '../../img/carrera_noche.webp', 'Una carrera urbana que reunió a la comunidad de Salto.', ['../../img/carrera_noche.webp', '../../img/Costanera_Norte.jpeg']],
    'muestra-de-danza' => ['Muestra de Danza', 'Cultura', 'Centro Cultural', '2025-12-06', '../../img/danza.jpg', 'Una muestra de talento y expresión local.', ['../../img/danza.jpg', '../../img/Shopping_Salto.jpg']],
    'feria-de-emprendedores' => ['Feria de Emprendedores', 'Local', 'Mercado Central', '2025-10-25', '../../img/feria_emprendedores.jpg', 'Productos y proyectos de emprendedores salteños.', ['../../img/feria_emprendedores.jpg', '../../img/Plaza_33.webp']],
    'lafosa-bike' => ['LaFosa Bike', 'Deportivo', 'La Fosa', '2025-09-13', '../../img/fosa.webp', 'Circuito de bicicleta, skate y calistenia.', ['../../img/fosa.webp', '../../img/moto_cross.jpg']],
];

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['id'] ?? ''));
$evento = $eventosPasados[$slug] ?? null;
if (!$evento) {
    http_response_code(404);
    exit('Evento histórico no encontrado.');
}
[$nombre, $categoria, $lugar, $fecha, $imagen, $descripcion, $galeria] = $evento;
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
    <style>
        body.past-event{background:#eeeae3;color:#20221f}.past-hero{min-height:58vh;display:flex;align-items:flex-end;padding:5rem clamp(1.25rem,7vw,7rem);background:linear-gradient(180deg,rgba(0,0,0,.08),rgba(0,0,0,.82)),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>') center/cover;color:#fff}.past-hero__content{max-width:760px;align-items:flex-start}.past-hero h1{margin:.5rem 0;font-size:clamp(2.5rem,7vw,5.5rem);line-height:.98}.past-hero p{max-width:650px;line-height:1.6;color:rgba(255,255,255,.86)}.past-meta{display:flex;flex-wrap:wrap;gap:.55rem}.past-badge{display:inline-flex;padding:.45rem .8rem;border-radius:999px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.34);font-size:.84rem}.past-status{width:max-content;display:inline-flex;margin-top:1.2rem;padding:.7rem 1rem;background:#173f35;color:#f4d27b;border-radius:8px;font-weight:800}.past-main{width:min(1120px,92%);margin:0 auto;padding:5rem 0 6rem}.past-intro{display:grid;grid-template-columns:1fr 1.3fr;gap:3rem;align-items:start}.past-intro h2{margin-top:0}.past-intro p{line-height:1.7}.past-gallery{margin-top:4rem}.past-gallery h2{margin-bottom:1rem}.past-gallery-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}.past-gallery-grid button{border:0;padding:0;aspect-ratio:4/3;overflow:hidden;cursor:pointer;background:#222;border-radius:8px}.past-gallery-grid img{width:100%;height:100%;object-fit:cover;transition:transform .25s}.past-gallery-grid button:hover img{transform:scale(1.04)}@media(max-width:700px){.past-intro{grid-template-columns:1fr}.past-gallery-grid{grid-template-columns:repeat(2,1fr)}}
    </style>
</head>
<body class="past-event event-detail event-finished">
    <?php $navBase = ''; $activePage = 'eventos'; require __DIR__ . '/nav.php'; ?>
    <main>
        <section class="past-hero"><div class="past-hero__content"><div class="past-meta"><span class="past-badge"><?= htmlspecialchars($fechaLegible) ?></span><span class="past-badge"><?= htmlspecialchars($categoria) ?></span></div><h1><?= htmlspecialchars($nombre) ?></h1><p><?= htmlspecialchars($descripcion) ?></p><strong class="past-status">Evento Finalizado</strong></div></section>
        <section class="past-main">
            <div class="past-intro"><div><h2>Memoria del evento</h2><p>Este evento ya se realizó. Reviví sus momentos destacados en la galería y compartí tus recuerdos.</p></div><article class="card-engagement" data-item-key="<?= htmlspecialchars($itemKey, ENT_QUOTES, 'UTF-8') ?>"></article></div>
            <section class="past-gallery"><h2>Galería de Fotos</h2><div class="past-gallery-grid"><?php foreach ($galeria as $indice => $foto): ?><button type="button" data-past-photo="<?= $indice ?>"><img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre . ' foto ' . ($indice + 1), ENT_QUOTES, 'UTF-8') ?>" onerror="this.onerror=null;this.src='../img/porco.avif'"></button><?php endforeach; ?></div></section>
        </section>
    </main>
    <div class="lightbox" id="lightbox">
        <div class="lightbox-backdrop" id="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <button class="lightbox-close" id="lightbox-close" type="button" aria-label="Cerrar imagen">×</button>
            <button class="lightbox-arrow left" id="lightbox-prev" type="button" aria-label="Imagen anterior">❮</button>
            <div class="lightbox-media"><img id="lightbox-image" src="" alt="Vista ampliada de galería"></div>
            <button class="lightbox-arrow right" id="lightbox-next" type="button" aria-label="Siguiente imagen">❯</button>
            <aside class="lightbox-comments" aria-label="Comentarios de la imagen">
                <div class="lightbox-info"><span class="lightbox-kicker">Archivo del evento</span><h2 id="lightbox-title"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h2><p id="lightbox-description">Cobertura fotográfica del evento.</p><div class="lightbox-meta"><span id="lightbox-date"><?= htmlspecialchars($fechaLegible, ENT_QUOTES, 'UTF-8') ?></span><span id="lightbox-caption"></span></div></div>
                <article class="gallery-engagement-card" id="lightbox-engagement-card"></article>
            </aside>
        </div>
    </div>
    <script src="../js/interacciones-tarjetas.js"></script>
    <script>
        /* El lightbox mantiene un índice circular y recrea el módulo de
           interacción al cambiar de foto para aislar los comentarios por imagen. */
        const pastPhotos = <?= json_encode($galeria, JSON_UNESCAPED_SLASHES) ?>;
        let pastPhotoIndex = 0;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const engagementCard = document.getElementById('lightbox-engagement-card');
        const renderPastPhoto = () => {
            /* Actualizamos imagen, contador y clave de comentarios al navegar,
               manteniendo el mismo feed interactivo para cada foto. */
            lightboxImage.src = pastPhotos[pastPhotoIndex];
            lightboxImage.alt = `Foto ${pastPhotoIndex + 1} de <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>`;
            document.getElementById('lightbox-caption').textContent = `Foto ${pastPhotoIndex + 1} de ${pastPhotos.length}`;
            engagementCard.dataset.itemKey = `evento-<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>-foto-${pastPhotoIndex}`;
            engagementCard.querySelector('.card-engagement')?.remove();
            window.initInteractiveCards?.(engagementCard);
        };
        const closeLightbox = () => { lightbox.classList.remove('open'); document.body.classList.remove('lightbox-open'); };
        document.querySelectorAll('[data-past-photo]').forEach((button) => button.addEventListener('click', () => { pastPhotoIndex = Number(button.dataset.pastPhoto); renderPastPhoto(); lightbox.classList.add('open'); document.body.classList.add('lightbox-open'); }));
        document.getElementById('lightbox-close').addEventListener('click', closeLightbox);
        document.getElementById('lightbox-backdrop').addEventListener('click', closeLightbox);
        document.getElementById('lightbox-prev').addEventListener('click', () => { pastPhotoIndex = (pastPhotoIndex + pastPhotos.length - 1) % pastPhotos.length; renderPastPhoto(); });
        document.getElementById('lightbox-next').addEventListener('click', () => { pastPhotoIndex = (pastPhotoIndex + 1) % pastPhotos.length; renderPastPhoto(); });
    </script>
</body>
</html>