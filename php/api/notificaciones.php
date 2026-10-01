<?php
session_start();
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../helpers/autorizacion.php';
require_once __DIR__ . '/../helpers/notificaciones.php';

header('Content-Type: application/json; charset=utf-8');

function responderNotificaciones(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$usuarioActual = sigtur_usuario_actual($pdo);
if (!$usuarioActual) {
    responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'Sesión no iniciada.'], 401);
}

$usuarioId = (int) $usuarioActual['id'];
$recordatoriosGenerados = sigtur_generar_recordatorios_eventos($pdo, $usuarioId);
$accion = (string) ($_POST['accion'] ?? $_GET['accion'] ?? 'lista');

$csrfToken = $_POST['csrf_token'] ?? null;
if (in_array($accion, ['leer', 'marcar_todas', 'suscribir', 'crear'], true) && !sigtur_validar_csrf(is_string($csrfToken) ? $csrfToken : null)) {
    responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($accion === 'contar') {
        responderNotificaciones([
            'success' => true,
            'count' => sigtur_contar_notificaciones_no_leidas($pdo, $usuarioId),
        ]);
    }

    $items = sigtur_listar_notificaciones($pdo, $usuarioId, 20);
    foreach ($items as &$item) {
        $item['fecha_formateada'] = date('d/m/Y H:i', strtotime((string) $item['fecha_creacion']));
    }
    unset($item);

    responderNotificaciones([
        'success' => true,
        'items' => $items,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'Método no permitido.'], 405);
}

if ($accion === 'leer') {
    $notificacionId = (int) ($_POST['id'] ?? 0);
    if ($notificacionId <= 0 || !sigtur_marcar_notificacion_leida($pdo, $usuarioId, $notificacionId)) {
        responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'No se pudo marcar la notificación.'], 400);
    }

    responderNotificaciones([
        'success' => true,
        'count' => sigtur_contar_notificaciones_no_leidas($pdo, $usuarioId),
    ]);
}

if ($accion === 'marcar_todas') {
    sigtur_marcar_todas_las_notificaciones_leidas($pdo, $usuarioId);
    responderNotificaciones([
        'success' => true,
        'count' => 0,
    ]);
}

if ($accion === 'suscribir') {
    $endpoint = trim((string) ($_POST['endpoint'] ?? ''));
    $p256dh = trim((string) ($_POST['p256dh'] ?? ''));
    $auth = trim((string) ($_POST['auth'] ?? ''));
    $userAgent = trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));

    if ($endpoint === '' || $p256dh === '' || $auth === '') {
        responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'Faltan datos de la suscripción.'], 422);
    }

    sigtur_guardar_suscripcion($pdo, $usuarioId, $endpoint, $p256dh, $auth, $userAgent);
    responderNotificaciones(['success' => true, 'status' => 'success', 'message' => 'Dispositivo registrado.']);
}

if ($accion === 'crear') {
    $rol = sigtur_normalizar_rol($usuarioActual['rol'] ?? null);
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
    $tipo = trim((string) ($_POST['tipo'] ?? 'general'));
    $destino = trim((string) ($_POST['destino'] ?? 'todos'));
    $destinatarioId = (int) ($_POST['usuario_id'] ?? 0);
    $eventoId = (int) ($_POST['evento_id'] ?? 0);

    if ($titulo === '' || $mensaje === '') {
        responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'El título y el mensaje son obligatorios.'], 422);
    }

    $destinatarios = [];
    if ($rol === 'administrador') {
        if ($destinatarioId > 0) {
            $destinatarios = [$destinatarioId];
        } elseif ($eventoId > 0) {
            $evento = $pdo->prepare('SELECT slug FROM eventos WHERE id = :id LIMIT 1');
            $evento->execute([':id' => $eventoId]);
            $slug = $evento->fetchColumn();
            if (!$slug) {
                responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'El evento no existe.'], 404);
            }
            $inscritos = $pdo->prepare('SELECT DISTINCT usuario_id FROM inscripciones_eventos WHERE evento_slug = :slug');
            $inscritos->execute([':slug' => $slug]);
            foreach ($inscritos->fetchAll() as $fila) {
                $destinatarios[] = (int) $fila['usuario_id'];
            }
        } else {
            $todosTuristas = $pdo->query("SELECT id FROM usuarios WHERE rol = 'turista' ORDER BY id ASC");
            foreach ($todosTuristas->fetchAll() as $fila) {
                $destinatarios[] = (int) $fila['id'];
            }
        }
    } elseif ($rol === 'organizador') {
        if ($eventoId <= 0) {
            responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'Debes indicar el evento para enviar la notificación.'], 422);
        }

        $evento = $pdo->prepare('SELECT slug, titulo FROM eventos WHERE id = :id AND organizador_id = :organizador LIMIT 1');
        $evento->execute([':id' => $eventoId, ':organizador' => $usuarioId]);
        $eventoData = $evento->fetch();
        if (!$eventoData) {
            responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'No tienes permisos sobre ese evento.'], 403);
        }

        $inscritos = $pdo->prepare('SELECT DISTINCT usuario_id FROM inscripciones_eventos WHERE evento_slug = :slug');
        $inscritos->execute([':slug' => $eventoData['slug']]);
        foreach ($inscritos->fetchAll() as $fila) {
            $destinatarios[] = (int) $fila['usuario_id'];
        }
    } else {
        responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'No tienes permisos para crear notificaciones.'], 403);
    }

    $createdCount = 0;
    foreach (array_unique($destinatarios) as $destinatarioId) {
        if ($destinatarioId > 0) {
            $createdCount += (int) sigtur_crear_notificacion($pdo, (int) $destinatarioId, $titulo, $mensaje, $tipo !== '' ? $tipo : 'general');
        }
    }

    responderNotificaciones([
        'success' => true,
        'status' => 'success',
        'created' => $createdCount,
        'message' => 'Notificaciones enviadas correctamente.',
    ]);
}

responderNotificaciones(['success' => false, 'status' => 'error', 'message' => 'Acción no soportada.'], 400);
