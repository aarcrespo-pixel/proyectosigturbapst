<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/notificaciones.php';
require_once __DIR__ . '/helpers/consultas_eventos.php';
require_once __DIR__ . '/helpers/censura.php';

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

$eventoId = (int) ($_GET['id'] ?? 0);
$esEventoDinamico = $eventoId > 0;
if ($esEventoDinamico) {
    /* La variante dinámica busca por ID y corta con 404 antes de renderizar
       si el recurso no existe, evitando una página con datos inventados. */
    $consultaEvento = $pdo->prepare('SELECT * FROM eventos WHERE id = :id LIMIT 1');
    $consultaEvento->execute([':id' => $eventoId]);
    $eventoDb = $consultaEvento->fetch();
    if (!$eventoDb) {
        http_response_code(404);
        ?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Evento no encontrado | SIGTUR</title><link rel="stylesheet" href="../css/estilos.css"><style>body{min-height:100vh;display:grid;place-items:center;background:#111827;color:#f8fafc;font-family:Arial,sans-serif;text-align:center}.error-box{max-width:520px;padding:3rem}.error-box a{color:#fbbf24}</style><script src="../js/volver-posicion.js" defer></script></head><body><main class="error-box"><h1>Evento no encontrado</h1><p>El evento solicitado no existe o ya no está disponible.</p><a href="eventos.php">Volver a eventos</a></main><script src="../js/translator.js" defer></script></body></html><?php
        exit;
    }
    $slug = $eventoDb['slug'];
    $nombre = $eventoDb['titulo'];
    $categoria = $eventoDb['categoria'];
    $edad = 'Todo público';
    $lugar = $eventoDb['ubicacion'];
    $descripcion = $eventoDb['descripcion'];
    $imagen = sigtur_url_imagen_subida((string) ($eventoDb['imagen_portada'] ?? ''), 'eventos', '../img/porco.avif');
    $fecha = $eventoDb['fecha'];
    $valorEvento = trim((string) ($eventoDb['precio_texto'] ?? $eventoDb['precio'] ?? ''));
    $tipoEntradaEvento = trim((string) ($eventoDb['tipo_entrada'] ?? 'Consultar'));
    $aireLibre = false;
    $latitud = -31.383;
    $longitud = -57.962;
} else {
    $slug = basename($_SERVER['SCRIPT_FILENAME'] ?? 'detalle-evento.php', '.php');
    $evento = $eventos[$slug] ?? ['Evento recomendado', 'Experiencia', 'Todo público', 'Salto', 'Descubrí propuestas para disfrutar la ciudad.', '../../img/porco.avif', '2026-12-31', false, -31.383, -57.962];
    [$nombre, $categoria, $edad, $lugar, $descripcion, $imagen, $fecha, $aireLibre, $latitud, $longitud] = $evento;
    $valorEvento = '';
    $tipoEntradaEvento = 'Consultar con la organización';
}
$descripcion = trim((string) $descripcion);
$imagen = sigtur_url_imagen_evento($slug, (string) $imagen);
$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
$usuarioRolEvento = 'turista';
if ($usuarioId > 0) {
    $rolEventoStmt = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id LIMIT 1');
    $rolEventoStmt->execute([':id' => $usuarioId]);
    $usuarioRolEvento = sigtur_normalizar_rol((string) ($rolEventoStmt->fetchColumn() ?: 'turista'));
}
$favoritoQuery = $pdo->prepare('SELECT 1 FROM favoritos_eventos WHERE usuario_id = :usuario AND evento_slug = :slug');
$favoritoQuery->execute([':usuario' => $usuarioId, ':slug' => $slug]);
$esFavorito = $usuarioId > 0 && (bool) $favoritoQuery->fetchColumn();
$esPasado = strtotime($fecha) < strtotime(date('Y-m-d')) || (!empty($eventoDb['es_pasado'] ?? 0));
$assetBase = $esEventoDinamico ? '../' : '../../';

$pdo->exec("CREATE TABLE IF NOT EXISTS inscripciones_eventos (id INT AUTO_INCREMENT PRIMARY KEY, evento_slug VARCHAR(120) NOT NULL, usuario_id INT NOT NULL, fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unica_inscripcion (evento_slug, usuario_id), INDEX (evento_slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$pdo->exec("CREATE TABLE IF NOT EXISTS preguntas_eventos (id INT AUTO_INCREMENT PRIMARY KEY, evento_slug VARCHAR(120) NOT NULL, usuario_id INT NOT NULL, pregunta TEXT NOT NULL, respuesta TEXT NULL, fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX (evento_slug)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
sigtur_asegurar_mensajes_consulta_evento($pdo);
if (!$esEventoDinamico) {
    // Los wrappers legacy se catalogan al vuelo para convivir con la tabla nueva.
    $catalogo = $pdo->prepare('INSERT INTO eventos (slug, titulo, descripcion, fecha, ubicacion, categoria, imagen_portada, es_pasado, organizador_id) VALUES (:slug, :titulo, :descripcion, :fecha, :ubicacion, :categoria, :imagen, :pasado, :organizador) ON DUPLICATE KEY UPDATE slug = VALUES(slug)');
    $catalogo->execute([':slug' => $slug, ':titulo' => $nombre, ':descripcion' => $descripcion, ':fecha' => $fecha, ':ubicacion' => $lugar, ':categoria' => $categoria, ':imagen' => $imagen, ':pasado' => $esPasado ? 1 : 0, ':organizador' => 0]);
    $consultaIdEvento = $pdo->prepare('SELECT id FROM eventos WHERE slug = :slug LIMIT 1');
    $consultaIdEvento->execute([':slug' => $slug]);
    $eventoId = (int) $consultaIdEvento->fetchColumn();
}

$apiBase = $esEventoDinamico ? 'api/' : '../api/';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['accion'])) {
    /* Las acciones AJAX responden JSON y se procesan antes del HTML para que
       inscripción y preguntas no mezclen cabeceras con la vista. */
    header('Content-Type: application/json; charset=utf-8');
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        echo json_encode(['error' => 'La sesión expiró. Recargá la página e intentá nuevamente']);
        exit;
    }
    if (!$usuarioId) {
        http_response_code(401);
        echo json_encode(['error' => 'Iniciá sesión para continuar']);
        exit;
    }
    if ($_POST['accion'] === 'toggle_inscripcion' && !$esPasado) {
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
        $pregunta = censurarComentario(trim((string) ($_POST['pregunta'] ?? '')));
        if ($pregunta === '' || mb_strlen($pregunta) > 500) {
            http_response_code(422);
            echo json_encode(['error' => 'La pregunta debe tener entre 1 y 500 caracteres']);
            exit;
        }
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO preguntas_eventos (evento_slug, usuario_id, pregunta) VALUES (:evento, :usuario, :pregunta)');
        $stmt->execute([':evento' => $slug, ':usuario' => $usuarioId, ':pregunta' => $pregunta]);
        $mensaje = $pdo->prepare("INSERT INTO preguntas_eventos_mensajes (pregunta_id, autor_id, autor_rol, mensaje, clave_migracion)
            VALUES (:pregunta_id, :autor_id, 'turista', :mensaje, 'pregunta_inicial')");
        $mensaje->execute([':pregunta_id' => (int) $pdo->lastInsertId(), ':autor_id' => $usuarioId, ':mensaje' => $pregunta]);
        $pdo->commit();
        sigtur_notificar_consulta_evento($pdo, $usuarioId, $nombre, $pregunta);
        echo json_encode(['ok' => true]);
        exit;
    }
}

// Estas consultas preparan el estado de inscripción, asistentes, avatares y FAQ.
$stmt = $pdo->prepare('SELECT 1 FROM inscripciones_eventos WHERE evento_slug = :evento AND usuario_id = :usuario');
$stmt->execute([':evento' => $slug, ':usuario' => $usuarioId]);
$inscripto = (bool) $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT COUNT(*) FROM inscripciones_eventos WHERE evento_slug = :evento');
$stmt->execute([':evento' => $slug]);
$asistentes = (int) $stmt->fetchColumn();
$stmt = $pdo->prepare('SELECT u.nombre_completo, u.avatar FROM inscripciones_eventos i INNER JOIN usuarios u ON u.id = i.usuario_id WHERE i.evento_slug = :evento ORDER BY i.fecha_registro DESC LIMIT 4');
$stmt->execute([':evento' => $slug]);
$avatares = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT p.id AS pregunta_id, p.usuario_id, p.pregunta,
        pregunta_msg.id AS pregunta_mensaje_id, pregunta_msg.eliminado AS pregunta_eliminada,
        respuesta_msg.id AS respuesta_mensaje_id, respuesta_msg.mensaje AS respuesta,
        respuesta_msg.autor_id AS respuesta_autor_id, respuesta_msg.eliminado AS respuesta_eliminada,
        0 AS pregunta_oculta, 0 AS respuesta_oculta
    FROM preguntas_eventos p
    INNER JOIN preguntas_eventos_mensajes pregunta_msg
        ON pregunta_msg.pregunta_id = p.id
        AND pregunta_msg.clave_migracion = 'pregunta_inicial'
        AND pregunta_msg.eliminado = 0
    INNER JOIN preguntas_eventos_mensajes respuesta_msg
        ON respuesta_msg.id = (
            SELECT respuesta.id
            FROM preguntas_eventos_mensajes respuesta
            WHERE respuesta.pregunta_id = p.id
                AND respuesta.autor_rol IN ('organizador', 'administrador')
                AND respuesta.eliminado = 0
            ORDER BY respuesta.id DESC
            LIMIT 1
        )
    WHERE p.evento_slug = :evento
    ORDER BY p.fecha_creacion DESC");
$stmt->execute([':evento' => $slug]);
$preguntasRespondidas = $stmt->fetchAll();
$preguntasRespondidas = array_map(static function (array $preguntaRespondida): array {
    $preguntaRespondida['pregunta'] = censurarComentario((string) $preguntaRespondida['pregunta']);
    $preguntaRespondida['respuesta'] = censurarComentario((string) $preguntaRespondida['respuesta']);
    return $preguntaRespondida;
}, $preguntasRespondidas);
$destinoMapa = $esEventoDinamico ? $lugar . ', Salto, Uruguay' : $latitud . ',' . $longitud;
$mapUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($destinoMapa);
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode('SIGTUR|' . $slug . '|' . $usuarioId);
$fechaLegible = date('d/m/Y', strtotime($fecha));
$itemKeyComentarios = 'evento-' . ($eventoId > 0 ? $eventoId : $slug);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> | SIGTUR</title>
<link rel="stylesheet" href="<?= $assetBase ?>css/estilos.css?v=20261028-comment-options-5"><link rel="stylesheet" href="<?= $assetBase ?>css/detalle-evento.css"><link rel="stylesheet" href="<?= $assetBase ?>css/evento-form.css"><link rel="stylesheet" href="<?= $assetBase ?>css/enlaces-texto.css"><?php if ($esPasado): ?><link rel="stylesheet" href="<?= $assetBase ?>css/evento-finalizado.css"><?php endif; ?>
<script src="../js/volver-posicion.js" defer></script>
<style>
:root{--event-accent:#ffb84d;--event-ink:#11151b;--event-muted:#66717d}body.event-detail{background:#f4f6f8;color:var(--event-ink)}.event-detail .menu-principal{position:absolute;top:1.25rem;left:50%;right:auto;transform:translateX(-50%);width:min(92%,var(--site-max-width));z-index:5;background:linear-gradient(180deg,rgba(0,0,0,.48),transparent)}.event-hero{position:relative;min-height:min(760px,88vh);display:flex;align-items:flex-end;padding:clamp(8rem,18vh,13rem) clamp(1.25rem,6vw,6rem) clamp(3rem,8vw,6rem);background-image:linear-gradient(180deg,rgba(0,0,0,.2),rgba(0,0,0,.85)),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center}.event-hero__content{position:relative;z-index:1;max-width:850px;color:white}.event-back,.event-exit{position:absolute;top:6rem;color:rgba(255,255,255,.72);text-decoration:none;font-size:.85rem}.event-back{left:clamp(1.25rem,6vw,6rem)}.event-exit{right:clamp(1.25rem,6vw,6rem)}.event-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:1.1rem}.event-badge{display:inline-flex;gap:8px;align-items:center;padding:4px 12px;border-radius:20px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);font-size:.8rem;color:white;text-decoration:none}.event-hero h1{margin:0 0 1rem;font-size:clamp(2.2rem,5vw,3.8rem);font-weight:800;line-height:1;text-shadow:0 2px 10px rgba(0,0,0,.4)}.event-hero p{max-width:680px;color:rgba(255,255,255,.86);font-size:1.08rem;line-height:1.65}.event-social{display:flex;align-items:center;gap:.8rem;margin:1.2rem 0;color:rgba(255,255,255,.85)}.event-avatars{display:flex;padding-left:10px}.event-avatar{width:32px;height:32px;border:2px solid white;border-radius:50%;object-fit:cover;margin-left:-10px;background:#dce2e8}.event-actions{display:flex;flex-wrap:wrap;align-items:center;gap:.7rem}.event-actions button,.event-actions a{border:0;border-radius:999px;padding:.78rem 1.15rem;font:inherit;font-weight:700;text-decoration:none;cursor:pointer}.event-primary{background:var(--event-accent);color:#17110a;transition:transform .2s,background .2s}.event-primary:hover{transform:translateY(-2px)}.event-primary.is-registered{background:#b7e4c7;color:#143d24}.event-primary.pulse{animation:event-pulse .45s ease}@keyframes event-pulse{50%{transform:scale(.95)}100%{transform:scale(1)}}.event-secondary{background:rgba(255,255,255,.14);color:white;border:1px solid rgba(255,255,255,.35)!important}.event-main{max-width:1180px;margin:0 auto;padding:clamp(2rem,5vw,4.5rem) 1.25rem}.event-grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(300px,.8fr);gap:2rem}.event-panel{background:white;border:1px solid #e6e9ed;border-radius:22px;padding:clamp(1.25rem,3vw,2rem);box-shadow:0 14px 38px rgba(22,32,44,.06)}.event-panel h2{margin:0 0 1rem;font-size:1.35rem}.event-facts{display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;margin-top:1.2rem}.event-fact{padding:.9rem;background:#f6f8fa;border-radius:14px}.event-fact strong{display:block;font-size:.75rem;text-transform:uppercase;color:var(--event-muted);margin-bottom:.25rem}.event-forecast{margin-top:1rem;display:none}.event-forecast.visible{display:flex;justify-content:space-between;align-items:center}.event-faq{margin-top:2rem}.event-faq details{border-bottom:1px solid #e4e7eb;padding:1rem 0}.event-faq summary{cursor:pointer;font-weight:700}.event-question{display:flex;gap:.6rem;margin-top:1.5rem}.event-question input{flex:1;padding:.8rem;border:1px solid #d8dde3;border-radius:10px}.event-question button{border:0;border-radius:10px;background:var(--event-ink);color:white;padding:0 1rem;cursor:pointer}.event-qr-backdrop{position:fixed;inset:0;z-index:20;display:grid;place-items:center;background:rgba(4,8,12,.78);backdrop-filter:blur(8px)}.event-qr-backdrop[hidden]{display:none}.event-ticket{width:min(92vw,420px);background:#121820;color:white;border:1px solid #34404c;border-radius:24px;padding:1.5rem;box-shadow:0 30px 80px #000}.event-ticket img{display:block;width:180px;height:180px;margin:1rem auto;background:white;padding:.5rem}.event-ticket header{display:flex;justify-content:space-between}.event-ticket-close{border:0;background:none;color:white;font-size:1.5rem;cursor:pointer}.event-ticket dl{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}.event-ticket dt{font-size:.7rem;color:#93a1ae;text-transform:uppercase}.event-ticket dd{margin:.2rem 0 0;font-weight:700}@media(max-width:760px){.event-grid{grid-template-columns:1fr}.event-hero{min-height:760px}.event-facts{grid-template-columns:1fr}.event-question{flex-direction:column}.event-question button{padding:.8rem}}
.event-hero{min-height:100svh;width:100vw;margin-left:calc(50% - 50vw);background-image:none}
.event-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(180deg,rgba(0,0,0,.28) 0%,rgba(0,0,0,.48) 45%,rgba(0,0,0,.9) 100%),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center;z-index:0}
.event-back{top:6rem;display:inline-flex;align-items:center;padding:.65rem 1rem;border:1px solid rgba(255,255,255,.38);border-radius:999px;background:rgba(15,20,24,.35);backdrop-filter:blur(10px);color:rgba(255,255,255,.9);transition:background .2s ease,transform .2s ease}
.event-back:hover{background:rgba(255,255,255,.18);transform:translateY(-1px)}
.event-hero{background-image:none}
.event-hero{background:transparent!important}
.event-hero::before{content:'';position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;background-image:linear-gradient(180deg,rgba(0,0,0,.35) 0%,rgba(0,0,0,.65) 60%,rgba(0,0,0,.75) 100%),url('<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>');background-size:cover;background-position:center}
.event-back{left:clamp(1.25rem,6vw,6rem);right:auto;top:1.5rem;z-index:3}
.event-hero__content{position:relative;z-index:2}
.event-hero h1,.event-hero p,.event-badge,.event-social{position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,.8)}
.event-actions{position:relative;z-index:2}
.event-favorite{background:#fff;color:#17110a!important}.event-favorite.is-favorite{background:#ffe4dc;color:#8f3024!important}.event-social{display:none!important}
</style>
<style>
.event-hero{width:min(1180px,94%);min-height:0;margin:0 auto;display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);align-items:center;gap:clamp(1.5rem,4vw,4rem);padding:clamp(7rem,15vh,9rem) 1.5rem 3rem;background:#f4f6f8!important;color:#11151b}
.event-hero::before{display:none}
.event-hero__image{position:relative;z-index:1;display:block;width:100%;height:clamp(360px,55vh,520px);margin:0;object-fit:contain;object-position:center;border-radius:20px;background:#e5e7eb}
body.event-detail .event-hero__content{width:100%;max-width:850px;margin:0;padding:0;display:flex;flex-direction:column;gap:12px;color:#11151b}
body.event-detail .event-hero h1,body.event-detail .event-hero p,body.event-detail .event-badge,body.event-detail .event-back{color:#11151b;text-shadow:none}
body.event-detail .event-badge{background:#fff;border-color:#d1d5db}
body.event-detail .event-back{background:#fff;border-color:#d1d5db}
body.event-detail .event-hero .event-actions .event-secondary{background:#176b87;color:#fff;border:1px solid #176b87!important;box-shadow:0 4px 12px rgba(23,107,135,.2)}
body.event-detail .event-hero .event-actions .event-secondary:hover{background:#0f526a;border-color:#0f526a!important}
body.event-detail .event-about{margin-top:.5rem;padding:clamp(1.1rem,2.5vw,1.5rem);border:1px solid #dfe3e8;border-radius:20px;background:#fff;color:#11151b;box-shadow:0 10px 28px rgba(17,21,27,.06)}
body.event-detail .event-about h2{margin:0 0 .75rem;color:#11151b}
body.event-detail .event-about p{margin:0;color:#374151}
body.event-detail .event-about__tickets{margin-top:1rem;padding-top:1rem;border-top:1px solid #e5e7eb}
body.event-detail .event-about__tickets strong{display:block;margin-bottom:.35rem;color:#11151b}
<?php if ($esPasado): ?>
body.event-finished,body.event-finished main,body.event-finished .event-hero,body.event-finished .event-main,body.event-finished .event-about{background:#f4f6f8!important;color:#11151b!important}
body.event-finished .event-meta::before,body.event-finished .event-actions::before{display:none!important;content:none!important}
body.event-finished .event-actions,body.event-finished .event-comments,body.event-finished .event-about__tickets{display:none!important}
<?php endif; ?>
@media(max-width:760px){.event-hero{grid-template-columns:minmax(0,1fr);gap:1.25rem;padding:6.5rem 0 2rem}.event-hero__image{height:clamp(250px,38vh,340px)}body.event-detail .event-hero__content{max-width:none}}
@media(max-width:760px){body.event-detail .event-hero__content{grid-row:1}.event-hero__image{grid-row:2}}
</style>
</head>
<body class="event-detail <?= $esPasado ? 'event-finished' : '' ?>" data-csrf="<?= htmlspecialchars(sigtur_csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
<main>
<section class="event-hero"><a class="event-back" href="<?= $esEventoDinamico ? 'eventos.php' : '../eventos.php' ?>">← Volver a eventos</a><img class="event-hero__image" src="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"><div class="event-hero__content"><div class="event-meta"><?php if ($esPasado): ?><span class="event-badge event-badge--finished">EVENTO FINALIZADO</span><?php endif; ?><span class="event-badge"><?= htmlspecialchars($fechaLegible) ?></span><span class="event-badge"><?= htmlspecialchars($categoria) ?></span><span class="event-badge"><?= htmlspecialchars($edad) ?></span><?php if (!$esPasado): ?><a class="event-badge" href="<?= htmlspecialchars($mapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">📍 <?= htmlspecialchars($lugar) ?></a><?php endif; ?></div><h1><?= htmlspecialchars($nombre) ?></h1><div class="event-social"><div class="event-avatars"><?php foreach ($avatares as $avatar):$avatarFile = basename($avatar['avatar'] ?? '');
    $avatarSrc = ($avatarFile && $avatarFile !== 'default-avatar.png' && file_exists(__DIR__.'/../uploads/avatars/'.$avatarFile)) ? '../uploads/avatars/'.$avatarFile : '../img/user.png'; ?><img class="event-avatar" src="<?= htmlspecialchars($avatarSrc, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($avatar['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>"><?php endforeach; ?></div><span><?= $asistentes ?> <?= $asistentes === 1 ? 'persona asistirá' : 'personas asistirán' ?></span></div><div class="event-actions"><button id="favoriteEventButton" class="event-primary event-favorite<?= $esFavorito ? ' is-favorite' : '' ?>" type="button" aria-pressed="<?= $esFavorito ? 'true' : 'false' ?>" data-endpoint="<?= htmlspecialchars($apiBase . 'favoritos_eventos.php', ENT_QUOTES, 'UTF-8') ?>" data-slug="<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" data-evento-id="<?= (int) $eventoId ?>" data-titulo="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>" data-descripcion="<?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?>" data-fecha="<?= htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8') ?>" data-ubicacion="<?= htmlspecialchars($lugar, ENT_QUOTES, 'UTF-8') ?>" data-categoria="<?= htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') ?>" data-imagen="<?= htmlspecialchars($imagen, ENT_QUOTES, 'UTF-8') ?>"><?= $esFavorito ? '★ Guardado en favoritos' : '☆ Guardar en favoritos' ?></button><?php if ($inscripto): ?><button id="ticketButton" class="event-secondary" type="button">Ver mi Pase / QR</button><?php endif; ?><a class="event-secondary" href="<?= htmlspecialchars($mapUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Cómo llegar</a></div><article class="event-about"><h2>Sobre el evento</h2><?php if ($descripcion !== ''): ?><p><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p><?php else: ?><p>El organizador no agregó una descripción para este evento.</p><?php endif; ?><div class="event-about__tickets"><strong>Entradas</strong><p><?php if ($tipoEntradaEvento === 'Gratuito'): ?>Entrada gratuita.<?php elseif ($valorEvento !== ''): ?><?= htmlspecialchars($valorEvento, ENT_QUOTES, 'UTF-8') ?>. Consultá disponibilidad y compra con la organización del evento.<?php else: ?>Consultá el precio y la disponibilidad de entradas con la organización del evento.<?php endif; ?></p></div></article></div></section>
<?php if (!$esPasado): ?>
<section class="event-main">
    <article class="event-panel event-comments">
        <h2>Comentarios del evento</h2>
        <div class="gallery-engagement-card" data-item-key="<?= htmlspecialchars($itemKeyComentarios, ENT_QUOTES, 'UTF-8') ?>"></div>
    </article>
</section>
<?php endif; ?>
</main>
<?php if ($inscripto): ?><div class="event-qr-backdrop" id="ticketModal" hidden><article class="event-ticket"><header><strong>Pase digital</strong><button class="event-ticket-close" type="button" id="ticketClose">×</button></header><img src="<?= htmlspecialchars($qrUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Código QR de acreditación"><h2><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h2><dl><div><dt>Fecha</dt><dd><?= htmlspecialchars($fechaLegible) ?></dd></div><div><dt>Lugar</dt><dd><?= htmlspecialchars($lugar) ?></dd></div><div><dt>Asistente</dt><dd><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd></div></dl></article></div><?php endif; ?>
<script src="<?= $assetBase ?>js/tema.js" defer></script>
<script src="<?= $assetBase ?>js/interacciones-tarjetas.js?v=20261028-comment-options-2"></script>
<script>
(() => {
    const preguntas = <?= json_encode(array_map(static fn (array $pregunta): array => [
        'preguntaId' => (int) ($pregunta['pregunta_mensaje_id'] ?? 0),
        'autorPreguntaId' => (int) $pregunta['usuario_id'],
        'preguntaEliminada' => (bool) $pregunta['pregunta_eliminada'],
        'preguntaOculta' => (bool) $pregunta['pregunta_oculta'],
        'respuestaId' => (int) ($pregunta['respuesta_mensaje_id'] ?? 0),
        'autorRespuestaId' => (int) ($pregunta['respuesta_autor_id'] ?? 0),
        'respuestaEliminada' => (bool) $pregunta['respuesta_eliminada'],
        'respuestaOculta' => (bool) $pregunta['respuesta_oculta'],
    ], $preguntasRespondidas), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const usuarioId = <?= $usuarioId ?>;
    const rol = <?= json_encode($usuarioRolEvento, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const api = <?= json_encode($apiBase . 'gestionar_mensaje.php', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const moderador = ['administrador', 'organizador'].includes(rol);
    const seccion = document.getElementById('preguntas-respondidas');
    const detalles = seccion ? [...seccion.querySelectorAll('details')] : [];

    function agregarAcciones(contenedor, objetivoTexto, id, autorId, propietarioId, eliminado, oculto, esPregunta) {
        if (!id) return;
        const propio = usuarioId > 0 && usuarioId === autorId;
        const participa = usuarioId > 0 && usuarioId === propietarioId;
        const puedeEditar = propio && !eliminado;
        const puedeEliminarTodos = !eliminado && propio;
        const puedeEliminarMi = propio || (eliminado && (participa || moderador));
        contenedor.dataset.messageItem = '';
        contenedor.dataset.messageType = 'evento';
        contenedor.dataset.messageId = String(id);
        contenedor.dataset.messageApi = api;
        contenedor.dataset.canEdit = puedeEditar ? '1' : '0';
        contenedor.dataset.canDelete = puedeEliminarTodos ? '1' : '0';
        contenedor.dataset.messageHideLabel = esPregunta ? 'Pregunta oculta para ti' : 'Respuesta oculta para ti';
        const textoOriginal = objetivoTexto.textContent.trim();
        const texto = document.createElement('span');
        texto.dataset.messageText = '';
        texto.textContent = oculto
            ? (esPregunta ? 'Pregunta oculta para ti' : 'Respuesta oculta para ti')
            : eliminado ? 'Mensaje eliminado para todos' : textoOriginal;
        if (eliminado && !oculto) contenedor.classList.add('is-deleted');
        objetivoTexto.replaceChildren(texto);
        if (oculto) return;
        const acciones = [];
        if (puedeEditar) acciones.push(['edit', 'Editar']);
        acciones.push(['delete-me', 'Eliminar para mí', !puedeEliminarMi]);
        if (puedeEliminarTodos) acciones.push(['delete-everyone', 'Eliminar para todos']);
        if (!acciones.length) return;
        const menu = document.createElement('details');
        menu.className = 'message-actions';
        const resumen = document.createElement('summary');
        resumen.setAttribute('aria-label', 'Opciones del mensaje');
        resumen.textContent = '⋮';
        const contenido = document.createElement('div');
        contenido.className = 'message-actions__menu';
        acciones.forEach(([accion, etiqueta, deshabilitada]) => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.dataset.messageAction = accion;
            boton.disabled = Boolean(deshabilitada);
            boton.textContent = etiqueta;
            contenido.append(boton);
        });
        menu.append(resumen, contenido);
        menu.addEventListener('click', event => event.stopPropagation());
        contenedor.append(menu);
    }

    detalles.forEach((detalle, indice) => {
        const pregunta = preguntas[indice];
        if (!pregunta) return;
        const resumen = detalle.querySelector('summary');
        const respuesta = detalle.querySelector('p');
        if (resumen && pregunta.preguntaId) {
            const preguntaItem = document.createElement('div');
            preguntaItem.className = 'event-qa-message';
            detalle.before(preguntaItem);
            preguntaItem.append(detalle);
            const editContainer = document.createElement('div');
            editContainer.dataset.messageEditContainer = '';
            preguntaItem.append(editContainer);
            agregarAcciones(preguntaItem, resumen, pregunta.preguntaId, pregunta.autorPreguntaId, pregunta.autorPreguntaId, pregunta.preguntaEliminada, pregunta.preguntaOculta, true);
        }
        if (respuesta && pregunta.respuestaId) {
            const contenedor = document.createElement('div');
            contenedor.className = 'event-qa-answer';
            respuesta.replaceWith(contenedor);
            contenedor.append(respuesta);
            respuesta.dataset.messageText = '';
            agregarAcciones(contenedor, respuesta, pregunta.respuestaId, pregunta.autorRespuestaId, pregunta.autorPreguntaId, pregunta.respuestaEliminada, pregunta.respuestaOculta, false);
        }
    });
})();
</script>
<script src="<?= $assetBase ?>js/mensajes-acciones.js"></script>
<script src="<?= $assetBase ?>js/evento.js" defer></script>
<script>
const favoriteEventButton = document.getElementById('favoriteEventButton');
favoriteEventButton?.addEventListener('click', async () => {
    if (favoriteEventButton.disabled) return;
    const guardado = favoriteEventButton.getAttribute('aria-pressed') === 'true';
    const datos = new URLSearchParams({
        accion: guardado ? 'quitar' : 'guardar',
        slug: favoriteEventButton.dataset.slug,
        evento_id: favoriteEventButton.dataset.eventoId,
        titulo: favoriteEventButton.dataset.titulo,
        descripcion: favoriteEventButton.dataset.descripcion,
        fecha: favoriteEventButton.dataset.fecha,
        ubicacion: favoriteEventButton.dataset.ubicacion,
        categoria: favoriteEventButton.dataset.categoria,
        imagen: favoriteEventButton.dataset.imagen
    });
    favoriteEventButton.disabled = true;
    try {
        const response = await fetch(favoriteEventButton.dataset.endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: datos
        });
        const result = await response.json();
        if (response.status === 401) {
            alert(result.error || 'Iniciá sesión para guardar favoritos.');
            window.location.href = 'login.php';
            return;
        }
        if (!response.ok) throw new Error(result.error || 'No se pudo actualizar el favorito.');
        favoriteEventButton.setAttribute('aria-pressed', String(result.favorito));
        favoriteEventButton.classList.toggle('is-favorite', result.favorito);
        favoriteEventButton.textContent = result.favorito ? '★ Guardado en favoritos' : '☆ Guardar en favoritos';
    } catch (error) {
        alert(error.message);
    } finally {
        favoriteEventButton.disabled = false;
    }
});
</script>
<script src="<?= $assetBase ?>js/translator.js" defer></script>
<script src="<?= $assetBase ?>js/enlaces-texto.js" defer></script>
</body></html>
