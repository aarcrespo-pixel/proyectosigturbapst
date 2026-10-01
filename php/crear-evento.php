<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
<<<<<<< HEAD
require_once __DIR__ . '/helpers/notificaciones.php';
=======
>>>>>>> 00361ec4d7278fe96ed7b2843a726b6c83d0105b

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // El endpoint solo crea eventos por POST; cualquier acceso directo vuelve al listado.
    header('Location: eventos.php');
    exit;
}

$usuarioActual = sigtur_requerir_rol($pdo, ['organizador', 'administrador']);
$usuarioId = (int) $usuarioActual['id'];
$esAdministrador = $usuarioActual['rol'] === 'administrador';
if (!sigtur_validar_csrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
}
$accion = (string) ($_POST['accion'] ?? 'crear');
$eventoId = (int) ($_POST['evento_id'] ?? 0);

if (!in_array($accion, ['crear', 'editar', 'eliminar'], true)) {
    http_response_code(422);
    exit('La acción solicitada no es válida.');
}

if ($accion !== 'crear') {
    $consultaEventoExistente = $pdo->prepare('SELECT id, slug, organizador_id FROM eventos WHERE id = :id LIMIT 1');
    $consultaEventoExistente->execute([':id' => $eventoId]);
    $eventoExistente = $consultaEventoExistente->fetch();
    if (!$eventoExistente) {
        http_response_code(404);
        exit('El evento no existe.');
    }
    if (!$esAdministrador && (int) $eventoExistente['organizador_id'] !== $usuarioId) {
        http_response_code(403);
        exit('No tienes permiso para modificar este evento.');
    }
}
$nuevoOrganizadorId = $accion === 'crear' ? $usuarioId : (int) $eventoExistente['organizador_id'];
if ($accion === 'editar' && $esAdministrador && isset($_POST['organizador_id'])) {
    $nuevoOrganizadorId = (int) $_POST['organizador_id'];
    if ($nuevoOrganizadorId > 0) {
        $rolOrganizador = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id LIMIT 1');
        $rolOrganizador->execute([':id' => $nuevoOrganizadorId]);
        if (sigtur_normalizar_rol($rolOrganizador->fetchColumn() ?: null) !== 'organizador') {
            http_response_code(422);
            exit('El propietario asignado debe tener rol de organizador.');
        }
    }
}

if ($accion === 'eliminar') {
    $pdo->beginTransaction();
    try {
        foreach (['inscripciones_eventos' => 'evento_slug', 'preguntas_eventos' => 'evento_slug', 'favoritos_eventos' => 'evento_slug'] as $tabla => $columna) {
            $borrarRelacionados = $pdo->prepare("DELETE FROM `{$tabla}` WHERE `{$columna}` = :slug");
            $borrarRelacionados->execute([':slug' => $eventoExistente['slug']]);
        }
        $borrarEvento = $pdo->prepare('DELETE FROM eventos WHERE id = :id');
        $borrarEvento->execute([':id' => $eventoId]);
        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
    header('Location: ' . ($esAdministrador ? 'panel-admin.php' : 'panel-organizador.php'));
    exit;
}

$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$fecha = trim($_POST['fecha'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$categoria = trim($_POST['categoria'] ?? 'General');
$tipoEntrada = trim($_POST['tipoEntrada'] ?? 'Gratuito');
$precioEntrada = trim($_POST['precio'] ?? '');
$imagen = trim($_POST['imagen_portada'] ?? 'default.jpg');

/* Los campos obligatorios se validan en servidor porque el formulario puede
    ser enviado fuera del navegador o manipulado desde las herramientas web. */
if ($titulo === '' || $descripcion === '' || $fecha === '' || $ubicacion === '') {
    http_response_code(422);
    exit('Completa título, descripción, fecha y ubicación.');
}

/* Convertimos el título en un slug estable y consultamos su disponibilidad
   mediante PDO para evitar colisiones y mantener la URL legible. */
$tituloNormalizado = iconv('UTF-8', 'ASCII//TRANSLIT', $titulo) ?: $titulo;
$slugBase = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9]+/', '-', strtolower($tituloNormalizado))) ?? '', '-');
$slugBase = $slugBase !== '' ? $slugBase : 'evento';
$slug = $slugBase;
$contador = 2;
$buscarSlug = $pdo->prepare('SELECT id FROM eventos WHERE slug = :slug AND id <> :evento_id LIMIT 1');
if ($accion === 'crear') {
    while (true) {
        $buscarSlug->execute([':slug' => $slug, ':evento_id' => 0]);
        if (!$buscarSlug->fetchColumn()) {
            break;
        }
        $slug = $slugBase . '-' . $contador++;
    }
} else {
    $slug = $eventoExistente['slug'];
}

/* El Prepared Statement separa datos y SQL, evitando inyección y dejando
   que lastInsertId() identifique el evento que se acaba de publicar. */
$precio = $tipoEntrada === 'De Pago' && is_numeric($precioEntrada) ? (float) $precioEntrada : null;
/* El precio solo se persiste para eventos pagos; los gratuitos quedan con NULL
    y el filtro SQL puede tratarlos como cero mediante COALESCE. */
/* Buscamos la ubicación normalizada antes del INSERT para que los eventos
    creados desde el formulario queden relacionados con lugar.php desde el
    primer momento; si es una ubicación nueva, lugar_id permanece NULL. */
$buscarLugar = $pdo->prepare('SELECT id FROM lugares WHERE nombre = :ubicacion OR direccion LIKE :direccion LIMIT 1');
$buscarLugar->execute([':ubicacion' => $ubicacion, ':direccion' => '%' . $ubicacion . '%']);
$lugarId = $buscarLugar->fetchColumn();

$parametrosEvento = [
    ':titulo' => $titulo,
    ':slug' => $slug,
    ':descripcion' => $descripcion,
    ':fecha' => $fecha,
    ':ubicacion' => $ubicacion,
    ':lugar_id' => $lugarId ?: null,
    ':categoria' => $categoria,
    ':tipo_entrada' => $tipoEntrada === 'De Pago' ? 'De Pago' : 'Gratuito',
    ':precio' => $precio,
    ':imagen' => $imagen !== '' ? $imagen : 'default.jpg',
    ':es_pasado' => strtotime($fecha) < strtotime(date('Y-m-d')) ? 1 : 0,
    ':organizador' => $accion === 'crear' ? $usuarioId : $nuevoOrganizadorId,
];

if ($accion === 'editar') {
    $actualizar = $pdo->prepare('UPDATE eventos SET titulo = :titulo, descripcion = :descripcion, fecha = :fecha, ubicacion = :ubicacion, lugar_id = :lugar_id, categoria = :categoria, tipo_entrada = :tipo_entrada, precio = :precio, imagen_portada = :imagen, es_pasado = :es_pasado, organizador_id = :organizador WHERE id = :id');
    $parametrosEvento[':id'] = $eventoId;
    unset($parametrosEvento[':slug']);
    $actualizar->execute($parametrosEvento);
    header('Location: evento.php?id=' . $eventoId);
    exit;
}

$insertar = $pdo->prepare('INSERT INTO eventos (titulo, slug, descripcion, fecha, ubicacion, lugar_id, categoria, tipo_entrada, precio, imagen_portada, es_pasado, organizador_id) VALUES (:titulo, :slug, :descripcion, :fecha, :ubicacion, :lugar_id, :categoria, :tipo_entrada, :precio, :imagen, :es_pasado, :organizador)');
$insertar->execute($parametrosEvento);

<<<<<<< HEAD
sigtur_notificar_turistas(
    $pdo,
    'Nuevo evento en SIGTUR',
    $titulo . ' se agregó a la agenda de Salto. Fecha: ' . $fecha . '. Lugar: ' . $ubicacion . '.',
    'evento'
);

=======
>>>>>>> 00361ec4d7278fe96ed7b2843a726b6c83d0105b
/* Redirigimos usando el ID generado por MySQL, no el slug recibido, para abrir
    exactamente la fila recién creada aunque el slug haya sido numerado. */
header('Location: evento.php?id=' . (int) $pdo->lastInsertId());
exit;