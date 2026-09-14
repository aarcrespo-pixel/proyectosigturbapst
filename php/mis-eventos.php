<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    // La agenda personal requiere identidad para filtrar inscripciones por usuario.
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/conexion.php';
$usuarioId = (int) $_SESSION['usuario_id'];

$catalogo = [
    ['bambola', 'La Bambola', 'Discoteca', 'Costanera Sur 1535', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh8BnVQ1qdPDXqoHVHylKrGhwk3guImN0d1ZRYbBNmSXoKPhqmCE-pMsE&s', '2026-09-19'],
    ['campeonato-de-pesca', 'Campeonato de Pesca', 'Naturaleza', 'Costanera Sur', 'https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg', '2026-09-20'],
    ['carrera-de-bicicleta', 'Carrera en Bicicleta', 'Deportivo', 'Circuito Artigas', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10', '2026-09-26'],
    ['carrera-nocturna', 'Carrera Nocturna', 'Deportivo', 'Costanera Norte', '../img/carrera_noche.webp', '2026-10-03'],
    ['circuito-en-bicicleta', 'Circuito en Bicicleta', 'Deportivo', 'Calle Artigas', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10', '2026-10-10'],
    ['copa-de-natacion', 'Copa de Natación', 'Acuático', 'Playa Salto', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTfYqzP-3hljVNvWo0X2mA8fxJY9EOZKFXquVDdbwRnvST-uFmxjLEum-d&s=10', '2026-10-17'],
    ['costanera', 'Costanera', 'Naturaleza', 'Costanera Norte', '../img/Costanera_Norte.jpeg', '2026-09-27'],
    ['exposalto', 'ExpoSalto', 'Feria', 'Hipódromo de Salto', '../img/exposalto.jpeg', '2026-10-24'],
    ['feria-de-emprendedores', 'Feria de Emprendedores', 'Local', 'Mercado Central', '../img/feria_emprendedores.jpg', '2026-10-31'],
    ['festival-de-la-naranja', 'Festival de la Naranja', 'Cultura', 'Plaza Artigas', '../img/festival_naranja.jpg', '2026-11-07'],
    ['futbol-x5', 'Fútbol X5', 'Deportivo', 'Plaza de Deportes Salto', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLGcgcjLaLtUFzXSfs5EzS0x3c23cfZtXgew3MzIyfJWBgxce4Bah7vhA&s=10', '2026-11-14'],
    ['la-ferne', 'La Ferne', 'Discoteca', 'Costanera Norte', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzXMkEsvlia-KLamghIoD7xL9Rpz72SnkxikCvS_uH0Q&s', '2026-11-21'],
    ['lafosa-bike', 'LaFosa Bike', 'Deportivo', 'La Fosa', '../img/fosa.webp', '2026-11-28'],
    ['muestra-de-danza', 'Muestra de Danza', 'Cultura', 'Centro Cultural', '../img/danza.jpg', '2026-12-05'],
    ['polo', 'Halloween en Polo', 'Disfraces', 'Polo Club', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10', '2026-12-12'],
    ['porco-negro', 'Porco Negro', 'Discoteca', 'Av. Apolón de Mirbek', '../img/porco.avif', '2026-12-19'],
    ['rally', 'Rally', 'Carrera', 'Av. Horacio Quiroga', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10', '2027-01-09'],
    ['streetball-salto', 'Streetball Salto', 'Deportivo', 'Polideportivo Círculo SP', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0pAIE9pTlfySurzoR4nHmaJctr45FdL9iY9xVQXMfhhcFj8Gn_pXJ5z99&s=10', '2027-01-16'],
    ['surf-y-kayak', 'Surf y Kayak', 'Aventura', 'Playa Salto', 'https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg', '2027-01-23'],
    ['torneo-de-ajedrez', 'Torneo de Ajedrez', 'Mental', 'Plaza de Deportes', 'https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg', '2027-01-30'],
    ['torneo-de-beach-volley', 'Torneo de Beach Volley', 'Competencia', 'Playa Salto', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTturliqhgPakucL8C4kedxXyT6XEhQKkcXrZRh-af6MZf5zDZvjFQPZmV2TLieDskgPNvK3PyipLTjJc6wCuBY4T-08gd08muSeZ8sHo8&s=10', '2027-02-06'],
];

/* La tabla de inscripciones relaciona usuarios y eventos con una clave única
    para impedir reservas duplicadas. */
$pdo->exec("CREATE TABLE IF NOT EXISTS inscripciones_eventos (id INT AUTO_INCREMENT PRIMARY KEY, evento_slug VARCHAR(120) NOT NULL, usuario_id INT NOT NULL, fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unica_inscripcion (evento_slug, usuario_id), INDEX (evento_slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$guardarEvento = $pdo->prepare('INSERT INTO eventos (slug, titulo, descripcion, fecha, ubicacion, categoria, tipo_entrada, precio, imagen_portada, es_pasado, organizador_id) VALUES (:slug, :titulo, :descripcion, :fecha, :ubicacion, :categoria, :tipo_entrada, :precio, :imagen, :pasado, :organizador) ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), descripcion = VALUES(descripcion), categoria = VALUES(categoria), ubicacion = VALUES(ubicacion), imagen_portada = VALUES(imagen_portada), fecha = VALUES(fecha), es_pasado = VALUES(es_pasado)');
foreach ($catalogo as [$slug, $titulo, $categoria, $ubicacion, $imagen, $fecha]) {
    // Sincronizamos el catálogo legacy con el esquema persistente actual.
    $guardarEvento->execute([
        ':slug' => $slug,
        ':titulo' => $titulo,
        ':descripcion' => 'Evento de SIGTUR: ' . $titulo,
        ':fecha' => $fecha,
        ':ubicacion' => $ubicacion,
        ':categoria' => $categoria,
        ':tipo_entrada' => 'Gratuito',
        ':precio' => null,
        ':imagen' => $imagen,
        ':pasado' => strtotime($fecha) < strtotime(date('Y-m-d')) ? 1 : 0,
        ':organizador' => $usuarioId,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cancelar') {
    /* El endpoint responde JSON para que la tarjeta desaparezca o se actualice
       sin recargar toda la agenda personal. */
    /* El DELETE incluye usuario_id para impedir que una persona cancele una
       inscripción ajena aunque conozca su identificador. */
    header('Content-Type: application/json; charset=utf-8');
    $stmt = $pdo->prepare('DELETE FROM inscripciones_eventos WHERE id = :id AND usuario_id = :usuario');
    $stmt->execute([':id' => (int) ($_POST['inscripcion_id'] ?? 0), ':usuario' => $usuarioId]);
    echo json_encode(['ok' => $stmt->rowCount() > 0]);
    exit;
}

// JOIN devuelve únicamente los eventos que pertenecen a la agenda del usuario.
$stmt = $pdo->prepare('SELECT e.*, i.id AS inscripcion_id FROM eventos e INNER JOIN inscripciones_eventos i ON i.evento_slug = e.slug WHERE i.usuario_id = :usuario ORDER BY e.fecha ASC');
$stmt->execute([':usuario' => $usuarioId]);
$inscripciones = $stmt->fetchAll();
/* Esta vista vive dentro de /php/. Por eso los enlaces del nav deben resolverse
    desde esa carpeta: usar ../eventos.php saldría del directorio PHP y produce
    un 404. Dejamos el prefijo vacío para generar /php/eventos.php, /php/turismo.php,
    /php/lugares.php y /php/gastronomia.php mediante nav.php. */
$navBase = '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Mis Eventos | SIGTUR</title>
<link rel="stylesheet" href="../css/estilos.css">
<style>
body.mis-eventos-page{background:#f3f5f7;color:#151b22}.mis-eventos-wrap{max-width:1240px;margin:0 auto;padding:7rem 1.25rem 5rem}.mis-eventos-head{display:flex;justify-content:space-between;align-items:end;gap:1rem;margin-bottom:2rem}.mis-eventos-head h1{margin:0;font-size:clamp(2rem,4vw,3.5rem);letter-spacing:-.04em}.mis-eventos-head p{margin:.5rem 0 0;color:#6b7480}.mis-eventos-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.25rem}.mis-evento-card{overflow:hidden;background:white;border:1px solid #e1e6eb;border-radius:20px;box-shadow:0 14px 35px rgba(21,36,50,.07);transition:transform .2s,box-shadow .2s}.mis-evento-card:hover{transform:translateY(-4px);box-shadow:0 20px 42px rgba(21,36,50,.12)}.mis-evento-card img{display:block;width:100%;height:190px;object-fit:cover}.mis-evento-info{padding:1.1rem}.mis-evento-info h2{margin:0 0 .65rem;font-size:1.2rem}.mis-evento-meta{display:grid;gap:.35rem;color:#6b7480;font-size:.9rem}.mis-evento-actions{display:flex;gap:.55rem;margin-top:1rem}.mis-evento-actions button,.mis-evento-actions a{flex:1;border:0;border-radius:10px;padding:.7rem .55rem;text-align:center;font:inherit;font-size:.82rem;font-weight:700;cursor:pointer;text-decoration:none}.mis-pase{background:#151b22;color:white}.mis-cancelar{background:#fff1ef;color:#b43d31}.mis-empty{max-width:560px;margin:5rem auto;text-align:center;background:white;border:1px solid #e1e6eb;border-radius:24px;padding:3rem 1.5rem;box-shadow:0 14px 35px rgba(21,36,50,.06)}.mis-empty h2{margin:0 0 .6rem}.mis-empty p{color:#6b7480}.mis-explorar{display:inline-block;margin-top:1.2rem;border-radius:999px;background:#151b22;color:white;text-decoration:none;padding:.8rem 1.3rem;font-weight:700}.mis-qr{position:fixed;inset:0;z-index:20;display:grid;place-items:center;background:rgba(4,8,12,.78);backdrop-filter:blur(8px)}.mis-qr[hidden]{display:none}.mis-qr-box{width:min(92vw,380px);padding:1.5rem;border-radius:22px;background:#121820;color:white;text-align:center}.mis-qr-box img{width:190px;height:190px;margin:1rem auto;background:white;padding:.5rem}.mis-qr-close{float:right;border:0;background:none;color:white;font-size:1.5rem;cursor:pointer}@media(max-width:900px){.mis-eventos-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.mis-eventos-head{display:block}.mis-eventos-grid{grid-template-columns:1fr}}
/* La agenda usa estilos locales para sus tarjetas, así que este bloque
   restablece la cascada del fondo blanco al activar el tema oscuro global. */
body.dark-mode .mis-evento-card,
body[data-tema="oscuro"] .mis-evento-card,
[data-theme="dark"] .mis-evento-card,
body.dark-mode .mis-empty,
body[data-tema="oscuro"] .mis-empty,
[data-theme="dark"] .mis-empty,
body.dark-mode .mis-evento-info input,
body[data-tema="oscuro"] .mis-evento-info input,
[data-theme="dark"] .mis-evento-info input {
    background-color: #27272a;
    color: #f9fafb;
    border-color: #3f3f46;
}

body.dark-mode .mis-evento-meta,
body[data-tema="oscuro"] .mis-evento-meta,
[data-theme="dark"] .mis-evento-meta,
body.dark-mode .mis-empty p,
body[data-tema="oscuro"] .mis-empty p,
[data-theme="dark"] .mis-empty p {
    color: #9ca3af;
}
</style>
</head>
<body class="mis-eventos-page">
<?php $activePage = 'eventos';
include __DIR__ . '/nav.php'; ?>
<main class="mis-eventos-wrap"><header class="mis-eventos-head"><div><h1>Mis Eventos</h1><p>Tu agenda de experiencias, encuentros y actividades.</p></div><a class="mis-explorar" href="../index.php">Explorar eventos</a></header>
<?php if (!$inscripciones): ?><section class="mis-empty"><h2>Aún no te has inscripto a ningún evento</h2><p>Explorá la agenda y reservá tu próxima experiencia en Salto.</p><a class="mis-explorar" href="eventos.php">Explorar eventos</a></section><?php else: ?><section class="mis-eventos-grid" id="misEventosGrid"><?php foreach ($inscripciones as $evento): $imagen = str_starts_with($evento['imagen'], '../../') ? substr($evento['imagen'], 1) : $evento['imagen'];
    $qr = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode('SIGTUR|' . $evento['slug'] . '|' . $usuarioId); ?><article class="mis-evento-card" data-inscripcion-id="<?= (int) $evento['inscripcion_id'] ?>"><img src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>"><div class="mis-evento-info"><h2><?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?></h2><div class="mis-evento-meta"><span>📅 <?= htmlspecialchars(date('d/m/Y', strtotime($evento['fecha']))) ?></span><span>📍 <?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?></span></div><div class="mis-evento-actions"><button class="mis-pase" type="button" data-qr="<?= htmlspecialchars($qr, ENT_QUOTES, 'UTF-8') ?>" data-title="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>">Ver Pase / QR</button><button class="mis-cancelar" type="button" data-cancelar>Cancelar inscripción</button></div></div></article><?php endforeach; ?></section><?php endif; ?></main>
<div class="mis-qr" id="misQrModal" hidden><div class="mis-qr-box"><button class="mis-qr-close" type="button" data-cerrar-qr>×</button><h2 id="misQrTitle">Pase digital</h2><img id="misQrImage" src="" alt="Código QR de acreditación"><p><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></p></div></div>
<script src="../js/translator.js"></script>
<script src="../js/script.js" defer></script>
<script>
const grid=document.getElementById('misEventosGrid');const modal=document.getElementById('misQrModal');document.querySelectorAll('[data-qr]').forEach(button=>button.addEventListener('click',()=>{document.getElementById('misQrTitle').textContent=button.dataset.title;document.getElementById('misQrImage').src=button.dataset.qr;modal.hidden=false}));document.querySelector('[data-cerrar-qr]')?.addEventListener('click',()=>modal.hidden=true);modal?.addEventListener('click',event=>{if(event.target===modal)modal.hidden=true});grid?.addEventListener('click',async event=>{const button=event.target.closest('[data-cancelar]');if(!button)return;const card=button.closest('[data-inscripcion-id]');if(!card||!confirm('¿Cancelar esta inscripción?'))return;button.disabled=true;const response=await fetch('mis-eventos.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({accion:'cancelar',inscripcion_id:card.dataset.inscripcionId})});const data=await response.json();if(data.ok){card.remove();if(!grid.children.length)location.reload()}else{button.disabled=false;alert('No se pudo cancelar la inscripción')}});
</script></body></html>
