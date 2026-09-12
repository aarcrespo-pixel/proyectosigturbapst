<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';

$eventos = [
    'bambola' => ['La Bambola', 'Discoteca', '+18', 'Costanera Sur 1535', 'Todos los fines de semana.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh8BnVQ1qdPDXqoHVHylKrGhwk3guImN0d1ZRYbBNmSXoKPhqmCE-pMsE&s', '2026-09-19', false, -31.3921, -57.9606],
    'campeonato-de-pesca' => ['Campeonato de Pesca', 'Naturaleza', '+13', 'Costanera Sur', 'Una jornada al aire libre junto al río.', 'https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg', '2026-09-20', true, -31.3908, -57.9589],
    'carrera-de-bicicleta' => ['Carrera en Bicicleta', 'Deportivo', '+16', 'Circuito Artigas', 'Recorrido en bicicleta por la ciudad.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10', '2026-09-26', true, -31.383, -57.962],
    'carrera-nocturna' => ['Carrera Nocturna', 'Deportivo', '+13', 'Costanera Norte', 'Una carrera urbana para disfrutar Salto de noche.', '../../img/carrera_noche.webp', '2026-10-03', true, -31.377, -57.954],
    'circuito-en-bicicleta' => ['Circuito en Bicicleta', 'Deportivo', '+13', 'Calle Artigas', 'Circuito para pedalear y descubrir la ciudad.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10', '2026-10-10', true, -31.386, -57.963],
    'copa-de-natacion' => ['Copa de Natación', 'Acuático', '+13', 'Playa Salto', 'Competencia abierta para nadadores.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTfYqzP-3hljVNvWo0X2mA8fxJY9EOZKFXquVDdbwRnvST-uFmxjLEum-d&s=10', '2026-10-17', true, -31.389, -57.948],
    'costanera' => ['Costanera', 'Naturaleza', 'Todo público', 'Costanera Norte', 'Música, paseo y actividades frente al río.', '../../img/Costanera_Norte.jpeg', '2026-09-27', true, -31.377, -57.954],
    'exposalto' => ['ExpoSalto', 'Feria', 'Todo público', 'Hipódromo de Salto', 'Feria local con propuestas para toda la familia.', '../../img/exposalto.jpeg', '2026-10-24', false, -31.370, -57.955],
    'feria-de-emprendedores' => ['Feria de Emprendedores', 'Local', 'Todo público', 'Mercado Central', 'Productos y proyectos de emprendedores salteños.', '../../img/feria_emprendedores.jpg', '2026-10-31', false, -31.383, -57.962],
    'festival-de-la-naranja' => ['Festival de la Naranja', 'Cultura', 'Todo público', 'Plaza Artigas', 'Feria, degustaciones, música y actividades.', '../../img/festival_naranja.jpg', '2026-11-07', false, -31.384, -57.961],
    'futbol-x5' => ['Fútbol X5', 'Deportivo', '+13', 'Plaza de Deportes Salto', 'Competición de fútbol salteño.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLGcgcjLaLtUFzXSfs5EzS0x3c23cfZtXgew3MzIyfJWBgxce4Bah7vhA&s=10', '2026-11-14', true, -31.389, -57.966],
    'la-ferne' => ['La Ferne', 'Discoteca', '+18', 'Costanera Norte', 'Una noche de música y baile.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzXMkEsvlia-KLamghIoD7xL9Rpz72SnkxikCvS_uH0Q&s', '2026-11-21', false, -31.377, -57.954],
    'lafosa-bike' => ['LaFosa Bike', 'Deportivo', '+13', 'La Fosa', 'Circuito de skate, bicicleta y calistenia.', '../../img/fosa.webp', '2026-11-28', true, -31.382, -57.971],
    'muestra-de-danza' => ['Muestra de Danza', 'Cultura', 'Todo público', 'Centro Cultural', 'Una muestra de talento y expresión local.', '../../img/danza.jpg', '2026-12-05', false, -31.383, -57.962],
    'polo' => ['Halloween en Polo', 'Disfraces', '+18', 'Polo Club', 'Una noche temática para celebrar.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10', '2026-12-12', false, -31.39, -57.97],
    'porco-negro' => ['Porco Negro', 'Discoteca', '+18', 'Av. Apolón de Mirbek esquina Av. José Enrique Rodó', 'Noche de música y baile en un ambiente exclusivo.', '../../img/porco.avif', '2026-12-19', false, -31.386, -57.963],
    'rally' => ['Rally', 'Carrera', '+13', 'Av. Horacio Quiroga 9382', 'Velocidad, técnica y competencia.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10', '2027-01-09', true, -31.35, -57.94],
    'streetball-salto' => ['Streetball Salto', 'Deportivo', '+13', 'Polideportivo Círculo SP', 'Basket y comunidad en una jornada abierta.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0pAIE9pTlfySurzoR4nHmaJctr45FdL9iY9xVQXMfhhcFj8Gn_pXJ5z99&s=10', '2027-01-16', false, -31.39, -57.966],
    'surf-y-kayak' => ['Surf y Kayak', 'Aventura', '+13', 'Playa Salto', 'Experiencias acuáticas para descubrir el río.', 'https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg', '2027-01-23', true, -31.389, -57.948],
    'torneo-de-ajedrez' => ['Torneo de Ajedrez', 'Mental', 'Todo público', 'Plaza de Deportes', 'Estrategia y competencia para todos los niveles.', 'https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg', '2027-01-30', false, -31.389, -57.966],
    'torneo-de-beach-volley' => ['Torneo de Beach Volley', 'Competencia', '+13', 'Playa Salto', 'Deporte, playa y equipos.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTturliqhgPakucL8C4kedxXyT6XEhQKkcXrZRh-af6MZf5zDZvjFQPZmV2TLieDskgPNvK3PyipLTjJc6wCuBY4T-08gd08muSeZ8sHo8&s=10', '2027-02-06', true, -31.389, -57.948],
];

$slug = basename($_SERVER['SCRIPT_FILENAME'] ?? 'detalle-evento.php', '.php');
$evento = $eventos[$slug] ?? ['Evento recomendado', 'Experiencia', 'Todo público', 'Salto', 'Descubrí propuestas para disfrutar la ciudad.', '../../img/porco.avif', '2026-12-31', false, -31.383, -57.962];
[$nombre, $categoria, $edad, $lugar, $descripcion, $imagen, $fecha, $aireLibre, $latitud, $longitud] = $evento;
$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);

$pdo->exec("CREATE TABLE IF NOT EXISTS eventos (id INT AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(120) NOT NULL UNIQUE, titulo VARCHAR(180) NOT NULL, categoria VARCHAR(100) NOT NULL, ubicacion VARCHAR(255) NOT NULL, imagen VARCHAR(500) NOT NULL, fecha DATE NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS inscripciones_eventos (id INT AUTO_INCREMENT PRIMARY KEY, evento_slug VARCHAR(120) NOT NULL, usuario_id INT NOT NULL, fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unica_inscripcion (evento_slug, usuario_id), INDEX (evento_slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS preguntas_eventos (id INT AUTO_INCREMENT PRIMARY KEY, evento_slug VARCHAR(120) NOT NULL, usuario_id INT NOT NULL, pregunta TEXT NOT NULL, respuesta TEXT NULL, fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX (evento_slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$catalogo = $pdo->prepare('INSERT INTO eventos (slug, titulo, categoria, ubicacion, imagen, fecha) VALUES (:slug, :titulo, :categoria, :ubicacion, :imagen, :fecha) ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), categoria = VALUES(categoria), ubicacion = VALUES(ubicacion), imagen = VALUES(imagen), fecha = VALUES(fecha)');
$catalogo->execute([':slug' => $slug, ':titulo' => $nombre, ':categoria' => $categoria, ':ubicacion' => $lugar, ':imagen' => $imagen, ':fecha' => $fecha]);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['accion'])) {
    header('Content-Type: application/json; charset=utf-8');
    if (!$usuarioId) {
        http_response_code(401);
        echo json_encode(['error' => 'Iniciá sesión para continuar']);
        exit;
    }
    if ($_POST['accion'] === 'toggle_inscripcion') {
        $stmt = $pdo->prepare('SELECT id FROM inscripciones_eventos WHERE evento_slug = :evento AND usuario_id = :usuario');
        $stmt->execute([':evento' => $slug, ':usuario' => $usuarioId]);
        $id = $stmt->fetchColumn();
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM inscripciones_eventos WHERE id = :id AND usuario_id = :usuario');
            $stmt->execute([':id' => $id, ':usuario' => $usuarioId]);
            echo json_encode(['ok' => true, 'registered' => false]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO inscripciones_eventos (evento_slug, usuario_id) VALUES (:evento, :usuario)');
            $stmt->execute([':evento' => $slug, ':usuario' => $usuarioId]);
            echo json_encode(['ok' => true, 'registered' => true]);
        }
        exit;
    }
    if ($_POST['accion'] === 'pregunta') {
        $pregunta = trim($_POST['pregunta'] ?? '');
        if ($pregunta === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Escribí una pregunta']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO preguntas_eventos (evento_slug, usuario_id, pregunta) VALUES (:evento, :usuario, :pregunta)');
        $stmt->execute([':evento' => $slug, ':usuario' => $usuarioId, ':pregunta' => $pregunta]);
        echo json_encode(['ok' => true]);
        exit;
    }
}

$stmt = $pdo->prepare('SELECT 1 FROM inscripciones_eventos WHERE evento_slug = :evento AND usuario_id = :usuario');
$stmt->execute([':evento' => $slug, ':usuario' => $usuarioId]);
$inscripto = (bool) $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM inscripciones_eventos WHERE evento_slug = :evento');
$stmt->execute([':evento' => $slug]);
$asistentes = (int) $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT u.nombre_completo, u.avatar FROM inscripciones_eventos i INNER JOIN usuarios u ON u.id = i.usuario_id WHERE i.evento_slug = :evento ORDER BY i.fecha_registro DESC LIMIT 4');
$stmt->execute([':evento' => $slug]);
$avatares = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT pregunta, respuesta FROM preguntas_eventos WHERE evento_slug = :evento AND respuesta IS NOT NULL AND respuesta <> "" ORDER BY fecha_creacion DESC');
$stmt->execute([':evento' => $slug]);
$preguntasRespondidas = $stmt->fetchAll();
$mapUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($latitud . ',' . $longitud);
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode('SIGTUR|' . $slug . '|' . $usuarioId);
$fechaLegible = date('d/m/Y', strtotime($fecha));
$faq = [['¿Hay estacionamiento?', 'Sí, revisá las indicaciones del lugar antes de asistir.'], ['¿Cuál es la política de cancelación?', 'Podés cancelar tu inscripción desde esta misma página.'], ['¿El lugar es accesible?', 'Consultá al organizador mediante el formulario de preguntas.']];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> | SIGTUR</title>
<link rel="stylesheet" href="../../css/estilos.css"><link rel="stylesheet" href="../../css/detalle-evento.css">
<style>
:root{--event-accent:#ffb84d;--event-ink:#11151b;--event-muted:#66717d}body.event-detail{background:#f4f6f8;color:var(--event-ink)}.event-detail .menu-principal{position:absolute;top:1.25rem;left:50%;right:auto;transform:translateX(-50%);width:min(92%,var(--site-max-width));z-index:5;background:linear-gradient(180deg,rgba(0,0,0,.48),transparent)}.event-hero{position:relative;min-height:min(760px,88vh);display:flex;align-items:flex-end;padding:clamp(8rem,18vh,13rem) clamp(1.25rem,6vw,6rem) clamp(3rem,8vw,6rem);background-image:linear-gradient(180deg,rgba(0,0,0,.2),rgba(0,0,0,.85)),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center}.event-hero__content{position:relative;z-index:1;max-width:850px;color:white}.event-back,.event-exit{position:absolute;top:6rem;color:rgba(255,255,255,.72);text-decoration:none;font-size:.85rem}.event-back{left:clamp(1.25rem,6vw,6rem)}.event-exit{right:clamp(1.25rem,6vw,6rem)}.event-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:1.1rem}.event-badge{display:inline-flex;gap:8px;align-items:center;padding:4px 12px;border-radius:20px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);font-size:.8rem;color:white;text-decoration:none}.event-hero h1{margin:0 0 1rem;font-size:clamp(2.2rem,5vw,3.8rem);font-weight:800;line-height:1;text-shadow:0 2px 10px rgba(0,0,0,.4)}.event-hero p{max-width:680px;color:rgba(255,255,255,.86);font-size:1.08rem;line-height:1.65}.event-social{display:flex;align-items:center;gap:.8rem;margin:1.2rem 0;color:rgba(255,255,255,.85)}.event-avatars{display:flex;padding-left:10px}.event-avatar{width:32px;height:32px;border:2px solid white;border-radius:50%;object-fit:cover;margin-left:-10px;background:#dce2e8}.event-actions{display:flex;flex-wrap:wrap;align-items:center;gap:.7rem}.event-actions button,.event-actions a{border:0;border-radius:999px;padding:.78rem 1.15rem;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.event-primary{background:var(--event-accent);color:#17110a;transition:transform .2s,background .2s}.event-primary:hover{transform:translateY(-2px)}.event-primary.is-registered{background:#b7e4c7;color:#143d24}.event-primary.pulse{animation:event-pulse .45s ease}@keyframes event-pulse{50%{transform:scale(.95)}100%{transform:scale(1)}}.event-secondary{background:rgba(255,255,255,.14);color:white;border:1px solid rgba(255,255,255,.35)!important}.event-main{max-width:1180px;margin:0 auto;padding:clamp(2rem,5vw,4.5rem) 1.25rem}.event-grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,.8fr);gap:2rem}.event-panel{background:white;border:1px solid #e6e9ed;border-radius:22px;padding:clamp(1.25rem,3vw,2rem);box-shadow:0 14px 38px rgba(22,32,44,.06)}.event-panel h2{margin:0 0 1rem;font-size:1.35rem}.event-facts{display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-top:1.2rem}.event-fact{padding:.9rem;background:#f6f8fa;border-radius:14px}.event-fact strong{display:block;font-size:.75rem;text-transform:uppercase;color:var(--event-muted);margin-bottom:.25rem}.event-forecast{margin-top:1rem;display:none}.event-forecast.visible{display:flex;justify-content:space-between;align-items:center}.event-faq{margin-top:2rem}.event-faq details{border-bottom:1px solid #e4e7eb;padding:1rem 0}.event-faq summary{cursor:pointer;font-weight:700}.event-question{display:flex;gap:.6rem;margin-top:1.5rem}.event-question input{flex:1;padding:.8rem;border:1px solid #d8dde3;border-radius:10px}.event-question button{border:0;border-radius:10px;background:var(--event-ink);color:white;padding:0 1rem;cursor:pointer}.event-rec{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1rem}.event-rec a{overflow:hidden;border-radius:16px;background:white;color:inherit;text-decoration:none;border:1px solid #e6e9ed}.event-rec img{width:100%;height:130px;object-fit:cover}.event-rec span{display:block;padding:.8rem;font-weight:700}.event-qr-backdrop{position:fixed;inset:0;z-index:20;display:grid;place-items:center;background:rgba(4,8,12,.78);backdrop-filter:blur(8px)}.event-qr-backdrop[hidden]{display:none}.event-ticket{width:min(92vw,420px);background:#121820;color:white;border:1px solid #34404c;border-radius:24px;padding:1.5rem;box-shadow:0 30px 80px #000}.event-ticket img{display:block;width:180px;height:180px;margin:1rem auto;background:white;padding:.5rem}.event-ticket header{display:flex;justify-content:space-between}.event-ticket-close{border:0;background:none;color:white;font-size:1.5rem;cursor:pointer}.event-ticket dl{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}.event-ticket dt{font-size:.7rem;color:#93a1ae;text-transform:uppercase}.event-ticket dd{margin:.2rem 0 0;font-weight:700}@media(max-width:760px){.event-grid{grid-template-columns:1fr}.event-hero{min-height:760px}.event-rec{grid-template-columns:1fr}.event-facts{grid-template-columns:1fr}.event-question{flex-direction:column}.event-question button{padding:.8rem}}
.event-hero{min-height:100svh;width:100vw;margin-left:calc(50% - 50vw);background-image:none}
.event-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(180deg,rgba(0,0,0,.28) 0%,rgba(0,0,0,.48) 45%,rgba(0,0,0,.9) 100%),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center;z-index:0}
.event-back{top:6rem;display:inline-flex;align-items:center;padding:.65rem 1rem;border:1px solid rgba(255,255,255,.38);border-radius:999px;background:rgba(15,20,24,.35);backdrop-filter:blur(10px);color:rgba(255,255,255,.9);transition:background .2s ease,transform .2s ease}
.event-back:hover{background:rgba(255,255,255,.18);transform:translateY(-1px)}
.event-hero{background-image:none}
.event-hero{background:transparent!important}
.event-hero::before{content:'';position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;background-image:linear-gradient(180deg,rgba(0,0,0,.35) 0%,rgba(0,0,0,.65) 60%,rgba(0,0,0,.75) 100%),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center}
.event-detail .menu-principal{background:transparent!important}
.event-back{left:auto;right:clamp(1.25rem,6vw,6rem)}
.event-detail .menu-principal{position:absolute;z-index:2}
.event-hero__content{position:relative;z-index:2}
.event-hero h1,.event-hero p,.event-badge,.event-social{position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,.8)}
.event-actions{position:relative;z-index:2}
.event-exit{display:none!important}
</style>
</head>
<body class="event-detail">
<?php $navBase = '../';
$activePage = 'eventos';
require __DIR__.'/nav.php'; ?>
<main>
<section class="event-hero"><a class="event-back" href="../eventos.php">← Volver a eventos</a><a class="event-exit" href="../eventos.php">Salir del evento</a><div class="event-hero__content"><div class="event-meta"><span class="event-badge"><?= htmlspecialchars($fechaLegible) ?></span><span class="event-badge"><?= htmlspecialchars($categoria) ?></span><span class="event-badge"><?= htmlspecialchars($edad) ?></span><a class="event-badge" href="<?= htmlspecialchars($mapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">📍 <?= htmlspecialchars($lugar) ?></a></div><h1><?= htmlspecialchars($nombre) ?></h1><p><?= htmlspecialchars($descripcion) ?></p><div class="event-social"><div class="event-avatars"><?php foreach ($avatares as $avatar):$avatarFile = basename($avatar['avatar'] ?? '');
    $avatarSrc = ($avatarFile && $avatarFile !== 'default-avatar.png' && file_exists(__DIR__.'/../uploads/avatars/'.$avatarFile)) ? '../uploads/avatars/'.$avatarFile : '../img/user.png'; ?><img class="event-avatar" src="<?= htmlspecialchars($avatarSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($avatar['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>"><?php endforeach; ?></div><span><?= $asistentes ?> <?= $asistentes === 1 ? 'persona asistirá' : 'personas asistirán' ?></span></div><div class="event-actions"><button id="registerButton" class="event-primary <?= $inscripto ? 'is-registered' : '' ?>" data-registered="<?= $inscripto ? 'true' : 'false' ?>" type="button"><?= $inscripto ? 'Inscripto ✓' : 'Inscribirse' ?></button><?php if ($inscripto): ?><button id="ticketButton" class="event-secondary" type="button">Ver mi Pase / QR</button><?php endif; ?><a class="event-secondary" href="<?= htmlspecialchars($mapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Cómo llegar</a></div></div></section>
<section class="event-main"><div class="event-grid"><div><article class="event-panel"><h2>Sobre el evento</h2><p><?= htmlspecialchars($descripcion) ?> Fecha: <?= htmlspecialchars($fechaLegible) ?>. Encontrá todos los detalles y preparate para vivir la experiencia.</p><div class="event-facts"><div class="event-fact"><strong>Fecha</strong><?= htmlspecialchars($fechaLegible) ?></div><div class="event-fact"><strong>Ubicación</strong><?= htmlspecialchars($lugar) ?></div></div><div id="forecast" class="event-panel event-forecast"><span id="forecastIcon">☀️</span><strong id="forecastText">Clima estimado</strong></div></article><article class="event-panel event-faq"><h2>Preguntas frecuentes</h2><?php foreach ($faq as [$pregunta,$respuesta]): ?><details><summary><?= htmlspecialchars($pregunta) ?></summary><p><?= htmlspecialchars($respuesta) ?></p></details><?php endforeach; ?><form class="event-question" id="questionForm"><input name="pregunta" maxlength="500" placeholder="¿Tenés otra pregunta?" <?= !$usuarioId ? 'disabled' : '' ?>><button type="submit" <?= !$usuarioId ? 'disabled' : '' ?>>Enviar</button></form><?php if (!$usuarioId): ?><small>Iniciá sesión para preguntar al organizador.</small><?php endif; ?></article><?php if ($preguntasRespondidas): ?><article class="event-panel event-faq"><h2>Preguntas respondidas</h2><?php foreach ($preguntasRespondidas as $q): ?><details><summary><?= htmlspecialchars($q['pregunta']) ?></summary><p><?= htmlspecialchars($q['respuesta']) ?></p></details><?php endforeach; ?></article><?php endif; ?></div><aside><article class="event-panel"><h2>Planificá tu visita</h2><p>Guardá el evento, consultá el mapa y revisá el pronóstico antes de salir.</p><a class="event-secondary" style="display:inline-block;background:var(--event-ink);color:white" href="<?= htmlspecialchars($mapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Abrir en Google Maps</a></article></aside></div><section style="margin-top:3rem"><h2>Eventos similares que te pueden interesar</h2><div class="event-rec"><?php $recs = array_values(array_filter($eventos, fn ($item) => $item[1] === $categoria || $item[3] === $lugar));
foreach (array_slice($recs, 0, 3) as $rec):$recSlug = array_search($rec, $eventos, true); ?><a href="<?= htmlspecialchars($recSlug, ENT_QUOTES, 'UTF-8') ?>.php"><img src="<?= htmlspecialchars($rec[5], ENT_QUOTES, 'UTF-8') ?>" alt=""><span><?= htmlspecialchars($rec[0], ENT_QUOTES, 'UTF-8') ?></span></a><?php endforeach; ?></div></section></section>
</main>
<?php if ($inscripto): ?><div class="event-qr-backdrop" id="ticketModal" hidden><article class="event-ticket"><header><strong>Pase digital</strong><button class="event-ticket-close" type="button" id="ticketClose">×</button></header><img src="<?= htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Código QR de acreditación"><h2><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h2><dl><div><dt>Fecha</dt><dd><?= htmlspecialchars($fechaLegible) ?></dd></div><div><dt>Lugar</dt><dd><?= htmlspecialchars($lugar) ?></dd></div><div><dt>Asistente</dt><dd><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd></div></dl></article></div><?php endif; ?>
<script>
const registerButton=document.getElementById('registerButton');registerButton?.addEventListener('click',async()=>{if(registerButton.dataset.busy==='true')return;registerButton.dataset.busy='true';registerButton.classList.add('pulse');try{const r=await fetch(location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({accion:'toggle_inscripcion'})});const d=await r.json();if(!r.ok)throw new Error(d.error);registerButton.textContent=d.registered?'Inscripto ✓':'Inscribirse';registerButton.classList.toggle('is-registered',d.registered);registerButton.dataset.registered=d.registered?'true':'false';setTimeout(()=>location.reload(),450)}catch(e){registerButton.dataset.busy='false';registerButton.classList.remove('pulse');alert(e.message)}});registerButton?.addEventListener('mouseenter',()=>{if(registerButton.dataset.registered==='true')registerButton.textContent='Cancelar inscripción'});registerButton?.addEventListener('mouseleave',()=>{if(registerButton.dataset.registered==='true')registerButton.textContent='Inscripto ✓'});const ticketModal=document.getElementById('ticketModal');document.getElementById('ticketButton')?.addEventListener('click',()=>ticketModal.hidden=false);document.getElementById('ticketClose')?.addEventListener('click',()=>ticketModal.hidden=true);ticketModal?.addEventListener('click',e=>{if(e.target===ticketModal)ticketModal.hidden=true});document.getElementById('questionForm')?.addEventListener('submit',async e=>{e.preventDefault();const r=await fetch(location.href,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(new FormData(e.currentTarget))});if(r.ok){e.currentTarget.reset();alert('Pregunta enviada al organizador')}else alert('No se pudo enviar')});const eventDate='<?= $fecha ?>',openAir=<?= $aireLibre ? 'true' : 'false'?>,lat=<?= $latitud ?>,lon=<?= $longitud ?>,days=(new Date(eventDate)-new Date())/86400000;if(openAir&&days>=0&&days<=7)fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code`).then(r=>r.json()).then(d=>{document.getElementById('forecast').classList.add('visible');document.getElementById('forecastText').textContent=`${Math.round(d.current.temperature_2m)}°C · Pronóstico estimado`;document.getElementById('forecastIcon').textContent=d.current.weather_code<3?'☀️':d.current.weather_code<60?'🌥️':'🌧️'}).catch(()=>{});
</script>
</body></html>
