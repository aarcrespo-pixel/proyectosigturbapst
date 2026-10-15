<?php
/* El buffer se inicia antes de cargar PDO para capturar warnings de conexión
   y evitar que PHP anteponga HTML a la respuesta JSON del frontend. */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

function responderMensajes(array $payload, int $status = 200): void
{
    http_response_code($status);
    /* CAMBIO APLICADO: limpiamos cualquier warning o espacio producido antes
       del JSON para que response.json() nunca intente parsear HTML/PHP. */
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    // DIAGNÓSTICO: iniciamos sesión antes de cargar cualquier dato para no
    // confundir una sesión vacía con un error de INSERT o de esquema.
    session_start();
    require_once __DIR__ . '/../conexion.php';
    require_once __DIR__ . '/../helpers/autorizacion.php';
    require_once __DIR__ . '/../helpers/censura.php';
    $usuarioActual = sigtur_usuario_actual($pdo);
    if (!$usuarioActual) responderMensajes(['success' => false, 'status' => 'error', 'message' => 'Sesión no iniciada'], 401);
    $usuarioId = (int) $usuarioActual['id'];

    $accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'conversaciones');
    if ($accion !== 'consulta' && !in_array($usuarioActual['rol'], ['administrador', 'organizador'], true)) {
        responderMensajes(['success' => false, 'status' => 'error', 'message' => 'La mensajería directa está disponible para administradores y organizadores.'], 403);
    }
    if (in_array($accion, ['consulta', 'enviar'], true) && !sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        responderMensajes(['success' => false, 'status' => 'error', 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], 403);
    }
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    // CAMBIO APLICADO: aceptamos los nombres usados por el widget y por los
    // formularios de soporte/eventos, normalizándolos a un único destinatario.
    $otroUsuarioId = (int) ($_GET['usuario_id'] ?? $_POST['usuario_id'] ?? $_GET['destinatario_id'] ?? $_POST['destinatario_id'] ?? $_POST['destinatario'] ?? $_GET['receptor_id'] ?? $_POST['receptor_id'] ?? 0);
    if (!isset($_GET['accion']) && !isset($_POST['accion']) && $otroUsuarioId > 0) {
        $accion = 'conversacion';
    }
    if ($accion === 'consulta') {
        // CAMBIO APLICADO: las consultas de soporte/eventos siempre llegan a
        // una cuenta administrativa, sin pedirle al usuario que conozca su ID.
        $textoConsulta = censurarTexto(trim((string) ($_POST['mensaje'] ?? $_POST['texto'] ?? '')));
        $eventoId = trim((string) ($_POST['evento_id'] ?? ''));
        $tipoConsulta = trim((string) ($_POST['tipo_consulta'] ?? ''));
        $contexto = trim((string) ($_POST['contexto'] ?? ($tipoConsulta !== '' ? $tipoConsulta : 'Soporte general')));
        if ($eventoId !== '') $contexto .= ' #' . $eventoId;
        if ($textoConsulta === '' || mb_strlen($textoConsulta) > 500) {
            // FIX RESPUESTA: todos los errores usan status, success y message.
            responderMensajes(['status' => 'error', 'success' => false, 'message' => 'La consulta debe tener entre 1 y 500 caracteres.'], 422);
        }
        $admin = $pdo->query("SELECT id FROM usuarios WHERE rol IN ('administrador', 'admin', 'moderador') ORDER BY FIELD(rol, 'administrador', 'admin', 'moderador'), id ASC LIMIT 1")->fetchColumn();
        if (!$admin) {
            responderMensajes(['status' => 'error', 'success' => false, 'message' => 'No hay una cuenta de soporte disponible.'], 503);
        }
        $mensajeContextual = '[Consulta ' . $contexto . ']: ' . $textoConsulta;
        $insertarConsulta = $pdo->prepare('INSERT INTO mensajes (remitente_id, destinatario_id, mensaje, contexto) VALUES (:remitente, :destinatario, :mensaje, :contexto)');
        $insertarConsulta->execute([':remitente' => $usuarioId, ':destinatario' => (int) $admin, ':mensaje' => $mensajeContextual, ':contexto' => $contexto]);
        // FIX RESPUESTA: soporte comparte el contrato JSON del envío directo.
        responderMensajes(['status' => 'success', 'success' => true, 'id' => (int) $pdo->lastInsertId(), 'hora' => date('H:i'), 'message' => 'Consulta enviada correctamente.'], 201);
    }

    if ($otroUsuarioId > 0 && in_array($accion, ['enviar', 'leer', 'conversacion'], true)) {
        $destinatario = $pdo->prepare('SELECT rol FROM usuarios WHERE id = :id LIMIT 1');
        $destinatario->execute([':id' => $otroUsuarioId]);
        $rolDestinatario = $destinatario->fetchColumn();
        $puedeContactar = $rolDestinatario !== false
            && ($usuarioActual['rol'] === 'administrador'
                ? in_array(strtolower((string) $rolDestinatario), ['administrador', 'admin', 'moderador', 'organizador'], true)
                : in_array(strtolower((string) $rolDestinatario), ['administrador', 'admin', 'moderador'], true));
        if (!$puedeContactar || $otroUsuarioId === $usuarioId) {
            responderMensajes(['success' => false, 'status' => 'error', 'message' => 'Solo puedes chatear con una cuenta autorizada para mensajería.'], 403);
        }
    }

    if ($accion === 'enviar') {
        $texto = censurarTexto(trim((string) ($_POST['mensaje'] ?? $_POST['texto'] ?? '')));
        $contextoMensaje = trim((string) ($_POST['contexto'] ?? 'Consulta general'));
        $fotoUrl = null;
        $videoUrl = null;
        $audioUrl = null;
        $directorioChat = __DIR__ . '/../../uploads/chat';
        $archivosGuardados = [];
        $limpiarAdjuntos = static function () use (&$archivosGuardados): void {
            foreach ($archivosGuardados as $rutaGuardada) {
                if (is_file($rutaGuardada) && !unlink($rutaGuardada)) {
                    error_log('[mensajeria] No se pudo limpiar un archivo adjunto no persistido.');
                }
            }
            $archivosGuardados = [];
        };
        if (!is_dir($directorioChat) && !mkdir($directorioChat, 0775, true) && !is_dir($directorioChat)) {
            error_log('[mensajeria] No se pudo crear uploads/chat.');
            responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo preparar el almacenamiento de archivos.'], 500);
        }

        $adjunto = $_FILES['adjunto'] ?? $_FILES['foto'] ?? null;
        if (is_array($adjunto) && ($adjunto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($adjunto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $estadoSubida = (int) $adjunto['error'];
                $mensajeError = in_array($estadoSubida, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? 'El archivo supera el límite de tamaño permitido por el servidor.'
                    : 'No se pudo recibir el archivo adjunto.';
                responderMensajes(['success' => false, 'status' => 'error', 'message' => $mensajeError], 413);
            }

            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($adjunto['tmp_name']);
            $tiposPermitidos = [
                'image/jpeg' => ['jpg', 'foto', 10 * 1024 * 1024],
                'image/png' => ['png', 'foto', 10 * 1024 * 1024],
                'image/webp' => ['webp', 'foto', 10 * 1024 * 1024],
                'image/gif' => ['gif', 'foto', 10 * 1024 * 1024],
                'video/mp4' => ['mp4', 'video', 25 * 1024 * 1024],
                'video/webm' => ['webm', 'video', 25 * 1024 * 1024],
                'video/quicktime' => ['mov', 'video', 25 * 1024 * 1024],
            ];
            if (!isset($tiposPermitidos[$mime])) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'Adjunta una imagen JPG, PNG, WEBP o GIF, o un video MP4, WEBM o MOV.'], 422);
            }
            [$extension, $tipoAdjunto, $tamanoMaximo] = $tiposPermitidos[$mime];
            if ((int) ($adjunto['size'] ?? 0) <= 0 || (int) $adjunto['size'] > $tamanoMaximo || !is_uploaded_file($adjunto['tmp_name'])) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'El archivo está vacío o supera el tamaño máximo permitido.'], 413);
            }
            if ($tipoAdjunto === 'foto') {
                $datosImagen = @getimagesize($adjunto['tmp_name']);
                if ($datosImagen === false || !in_array($datosImagen[2] ?? null, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
                    responderMensajes(['success' => false, 'status' => 'error', 'message' => 'El contenido del archivo no es una imagen válida.'], 422);
                }
            }

            $nombreArchivo = 'adjunto_' . bin2hex(random_bytes(16)) . '.' . $extension;
            $rutaArchivo = $directorioChat . DIRECTORY_SEPARATOR . $nombreArchivo;
            if (!move_uploaded_file($adjunto['tmp_name'], $rutaArchivo)) {
                error_log('[mensajeria] No se pudo mover un adjunto a uploads/chat.');
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo guardar el archivo adjunto.'], 500);
            }
            $archivosGuardados[] = $rutaArchivo;
            $rutaPublica = 'uploads/chat/' . $nombreArchivo;
            if ($tipoAdjunto === 'foto') $fotoUrl = $rutaPublica;
            if ($tipoAdjunto === 'video') $videoUrl = $rutaPublica;
        }

        if (!empty($_FILES['audio']) && ($_FILES['audio']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $archivoAudio = $_FILES['audio'];
            if (($archivoAudio['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                $limpiarAdjuntos();
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo recibir el audio. Inténtalo nuevamente.'], 422);
            }
            $mimeAudio = (new finfo(FILEINFO_MIME_TYPE))->file($archivoAudio['tmp_name']);
            $audiosPermitidos = [
                'audio/webm' => 'webm',
                'audio/ogg' => 'ogg',
                'audio/mpeg' => 'mp3',
                'audio/wav' => 'wav',
                'audio/mp4' => 'm4a',
                'audio/x-m4a' => 'm4a',
            ];
            if (!isset($audiosPermitidos[$mimeAudio]) || (int) ($archivoAudio['size'] ?? 0) <= 0 || (int) $archivoAudio['size'] > 10 * 1024 * 1024 || !is_uploaded_file($archivoAudio['tmp_name'])) {
                $limpiarAdjuntos();
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'El audio no es válido o supera los 10 MB.'], 422);
            }
            $nombreAudio = 'audio_' . bin2hex(random_bytes(16)) . '.' . $audiosPermitidos[$mimeAudio];
            $rutaAudio = $directorioChat . DIRECTORY_SEPARATOR . $nombreAudio;
            if (!move_uploaded_file($archivoAudio['tmp_name'], $rutaAudio)) {
                $limpiarAdjuntos();
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo guardar el audio.'], 500);
            }
            $archivosGuardados[] = $rutaAudio;
            $audioUrl = 'uploads/chat/' . $nombreAudio;
        }

        if ($otroUsuarioId <= 0 || $otroUsuarioId === $usuarioId || ($texto === '' && !$fotoUrl && !$videoUrl && !$audioUrl) || mb_strlen($texto) > 500) {
            foreach ($archivosGuardados as $rutaGuardada) if (is_file($rutaGuardada)) unlink($rutaGuardada);
            responderMensajes(['status' => 'error', 'success' => false, 'message' => 'Revisá el destinatario y el mensaje.'], 422);
        }
        /* El esquema se asegura una vez en conexion.php, por lo que el INSERT
           usa directamente las columnas estables y evita introspección SQL. */
        try {
            $insertar = $pdo->prepare('INSERT INTO `mensajes` (`remitente_id`, `destinatario_id`, `mensaje`, `contexto`, `foto_url`, `video_url`, `audio_url`) VALUES (:remitente, :destinatario, :texto, :contexto, :foto, :video, :audio)');
            $insertar->execute([
                ':remitente' => $usuarioId,
                ':destinatario' => $otroUsuarioId,
                ':texto' => $texto,
                ':contexto' => $contextoMensaje,
                ':foto' => $fotoUrl,
                ':video' => $videoUrl,
                ':audio' => $audioUrl,
            ]);
        } catch (PDOException $exception) {
            $limpiarAdjuntos();
            error_log('[mensajeria] Falló el INSERT: ' . $exception->getMessage());
            responderMensajes([
                'success' => false,
                'status' => 'error',
                'message' => 'No se pudo guardar el mensaje en la base de datos. No se envió al destinatario.',
                'sql_state' => $exception->getCode()
            ], 500);
        }
        $mensajeId = (int) $pdo->lastInsertId();
        $horaGuardada = date('H:i');
        // CAMBIO APLICADO: el envío termina aquí con un contrato explícito.
        // Antes continuaba hasta conversaciones y no devolvía el ID insertado.
        responderMensajes([
            'success' => true,
            'status' => 'success',
            'id' => $mensajeId,
            // CAMBIO APLICADO: devolvemos la hora guardada por MySQL para que
            // la burbuja optimista y el historial usen el mismo timestamp.
            'fecha' => $horaGuardada,
            'mensaje' => [
                'id' => $mensajeId,
                'remitente_id' => $usuarioId,
                'destinatario_id' => $otroUsuarioId,
                'mensaje' => $texto,
                'foto_url' => $fotoUrl,
                'video_url' => $videoUrl,
                'audio_url' => $audioUrl,
                'fecha' => $horaGuardada,
                'hora' => $horaGuardada,
            ],
            'message' => 'Mensaje enviado correctamente.'
        ], 201);
    }
    if ($accion === 'leer' && $otroUsuarioId > 0) {
        $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :otro AND destinatario_id = :usuario');
        $marcar->execute([':otro' => $otroUsuarioId, ':usuario' => $usuarioId]);
    }
    if ($accion === 'conversacion' && $otroUsuarioId > 0) {
        $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :otro AND destinatario_id = :usuario AND leido = 0');
        $marcar->execute([':otro' => $otroUsuarioId, ':usuario' => $usuarioId]);
        /* CAMBIO APLICADO: cada placeholder tiene nombre propio. PDO con
           prepares nativos no permite reutilizar el mismo parámetro nombrado. */
        $consulta = $pdo->prepare("SELECT m.id, m.remitente_id, m.destinatario_id, m.mensaje, m.contexto, m.foto_url, m.video_url, m.audio_url, m.fecha_creacion, DATE_FORMAT(m.fecha_creacion, '%H:%i') AS hora, m.leido, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil FROM mensajes m INNER JOIN usuarios u ON u.id = m.remitente_id WHERE (m.remitente_id = :usuario_emisor AND m.destinatario_id = :otro_receptor) OR (m.remitente_id = :otro_emisor AND m.destinatario_id = :usuario_receptor) ORDER BY m.fecha_creacion ASC, m.id ASC LIMIT 100");
        $consulta->execute([':usuario_emisor' => $usuarioId, ':otro_receptor' => $otroUsuarioId, ':otro_emisor' => $otroUsuarioId, ':usuario_receptor' => $usuarioId]);
        $mensajes = $consulta->fetchAll();
        responderMensajes(['success' => true, 'data' => $mensajes, 'messages' => $mensajes]);
    }
     /* CAMBIO APLICADO: el inbox devuelve último texto, fecha y pendientes,
         agrupando cada contacto una sola vez para la columna izquierda. */
    $rolesVisibles = $usuarioActual['rol'] === 'administrador'
        ? "('administrador', 'admin', 'moderador', 'organizador')"
        : "('administrador', 'admin', 'moderador')";
    $consulta = $pdo->prepare("SELECT u.id AS usuario_id, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil, MAX(m.fecha_creacion) AS ultima_fecha, SUBSTRING_INDEX(GROUP_CONCAT(CASE WHEN m.mensaje <> '' THEN m.mensaje WHEN m.video_url IS NOT NULL THEN '[Video]' WHEN m.foto_url IS NOT NULL THEN '[Foto]' WHEN m.audio_url IS NOT NULL THEN '[Audio]' ELSE '' END ORDER BY m.fecha_creacion DESC, m.id DESC SEPARATOR '||'), '||', 1) AS ultimo_mensaje, SUM(CASE WHEN m.destinatario_id = :usuario_no_leido AND m.leido = 0 THEN 1 ELSE 0 END) AS no_leidos FROM mensajes m INNER JOIN usuarios u ON u.id = CASE WHEN m.remitente_id = :usuario_contacto THEN m.destinatario_id ELSE m.remitente_id END WHERE (m.remitente_id = :usuario_emisor OR m.destinatario_id = :usuario_receptor) AND u.rol IN {$rolesVisibles} GROUP BY u.id, u.nombre_completo, u.nickname, u.foto_perfil, u.avatar ORDER BY ultima_fecha DESC");
     $consulta->execute([':usuario_no_leido' => $usuarioId, ':usuario_contacto' => $usuarioId, ':usuario_emisor' => $usuarioId, ':usuario_receptor' => $usuarioId]);
    $conversaciones = $consulta->fetchAll();
    $contactosPermitidos = $pdo->prepare("SELECT id AS usuario_id, nombre_completo, nickname, COALESCE(foto_perfil, avatar) AS foto_perfil, '' AS ultimo_mensaje, 0 AS no_leidos FROM usuarios WHERE rol IN {$rolesVisibles} AND id <> :usuario_excluido ORDER BY nombre_completo ASC");
    $contactosPermitidos->execute([':usuario_excluido' => $usuarioId]);
    $conversacionIds = array_fill_keys(array_map(static fn ($conversacion) => (int) $conversacion['usuario_id'], $conversaciones), true);
    foreach ($contactosPermitidos->fetchAll() as $contactoPermitido) {
        if (!isset($conversacionIds[(int) $contactoPermitido['usuario_id']])) {
            $conversaciones[] = $contactoPermitido;
        }
    }
    responderMensajes(['success' => true, 'data' => $conversaciones, 'conversations' => $conversaciones]);
} catch (Throwable $exception) {
    error_log('[mensajeria] Error no controlado: ' . $exception->getMessage());
    /* El buffer se limpia también en errores de MySQL para que el cliente reciba
       JSON válido en lugar del HTML generado por un warning o notice. */
    responderMensajes(['success' => false, 'status' => 'error', 'message' => 'Ocurrió un error al procesar la mensajería. El mensaje no se confirmó como enviado.'], 500);
}
