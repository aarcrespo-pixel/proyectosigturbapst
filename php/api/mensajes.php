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
    require_once __DIR__ . '/../helpers/imagenes.php';
    $usuarioActual = sigtur_usuario_actual($pdo);
    if (!$usuarioActual) responderMensajes(['success' => false, 'status' => 'error', 'message' => 'Sesión no iniciada'], 401);
    $usuarioId = (int) $usuarioActual['id'];

    $accion = (string) ($_GET['accion'] ?? $_POST['accion'] ?? 'conversaciones');
    if ($accion !== 'consulta' && !in_array($usuarioActual['rol'], ['administrador', 'organizador'], true)) {
        responderMensajes(['success' => false, 'status' => 'error', 'message' => 'La mensajería directa es exclusiva para administradores y organizadores.'], 403);
    }
    if ($accion === 'consulta' && !sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        responderMensajes(['success' => false, 'status' => 'error', 'message' => 'La sesión expiró. Recargá la página e intentá otra vez.'], 403);
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
        $destinatarioConsulta = $pdo->prepare('SELECT id, rol FROM usuarios WHERE id = :id LIMIT 1');
        $destinatarioConsulta->execute([':id' => $otroUsuarioId]);
        $destinatario = $destinatarioConsulta->fetch();
        $rolesPermitidos = $usuarioActual['rol'] === 'administrador'
            ? ['administrador', 'organizador']
            : ['administrador'];
        if (!$destinatario || !in_array(sigtur_normalizar_rol($destinatario['rol'] ?? null), $rolesPermitidos, true) || $otroUsuarioId === $usuarioId) {
            responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No tienes permiso para abrir esta conversación.'], 403);
        }
    }

    if ($accion === 'enviar') {
        $texto = censurarTexto(trim((string) ($_POST['mensaje'] ?? $_POST['texto'] ?? '')));
        $contextoMensaje = trim((string) ($_POST['contexto'] ?? 'Consulta general'));
        $textoLongitud = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
        if ($otroUsuarioId <= 0 || $otroUsuarioId === $usuarioId || $textoLongitud > 500) {
            responderMensajes(['status' => 'error', 'success' => false, 'message' => 'Revisá el destinatario y el mensaje.'], 422);
        }
        $media = ['foto' => null, 'video' => null, 'audio' => null];
        $directorioChat = __DIR__ . '/../../uploads/chat';
        if (!is_dir($directorioChat) && !mkdir($directorioChat, 0775, true) && !is_dir($directorioChat)) {
            responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo preparar el almacenamiento de archivos.'], 500);
        }
        /* multipart/form-data deja los archivos en $_FILES; validamos MIME y
           extensión en servidor antes de moverlos al directorio público. */
        $tiposPermitidos = [
            'foto' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'],
            'video' => ['video/mp4' => 'mp4', 'video/webm' => 'webm', 'video/quicktime' => 'mov'],
            'audio' => ['audio/webm' => 'webm', 'audio/ogg' => 'ogg', 'audio/mpeg' => 'mp3', 'audio/wav' => 'wav', 'audio/mp4' => 'm4a', 'audio/aac' => 'aac'],
        ];
        $limitesBytes = ['foto' => 8 * 1024 * 1024, 'video' => 40 * 1024 * 1024, 'audio' => 15 * 1024 * 1024];
        foreach ($tiposPermitidos as $campo => $mimes) {
            if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) continue;
            if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo recibir el archivo adjunto. Revisá el tamaño máximo permitido por el servidor.'], 422);
            }
            if ((int) $_FILES[$campo]['size'] > $limitesBytes[$campo]) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'El archivo adjunto supera el tamaño permitido.'], 422);
            }
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$campo]['tmp_name']);
            if (!isset($mimes[$mime])) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'El tipo de archivo adjunto no está permitido.'], 422);
            }
            $nombreArchivo = sigtur_nombre_imagen_subida($usuarioId, (string) ($_FILES[$campo]['name'] ?? $campo), $mimes[$mime]);
            if (!move_uploaded_file($_FILES[$campo]['tmp_name'], $directorioChat . '/' . $nombreArchivo)) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No se pudo guardar el archivo adjunto.'], 500);
            }
            $media[$campo] = 'uploads/chat/' . $nombreArchivo;
        }
        if ($texto === '' && !array_filter($media)) {
            responderMensajes(['status' => 'error', 'success' => false, 'message' => 'Revisá el destinatario y el mensaje.'], 422);
        }
        /* PDO separa valores del SQL y la validación limita la entrada antes de
           insertarla, evitando inyección y datos fuera del contrato. */
        try {
            // SOLUCIÓN: detectamos tabla y columnas compatibles en lugar de
            // asumir que todas las instalaciones usan el mismo esquema.
            $tablasDisponibles = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            $tablaMensajes = in_array('mensajes', $tablasDisponibles, true) ? 'mensajes' : (in_array('mensajes_soporte', $tablasDisponibles, true) ? 'mensajes_soporte' : null);
            if (!$tablaMensajes) {
                responderMensajes(['success' => false, 'status' => 'error', 'message' => 'No existe la tabla mensajes ni mensajes_soporte.', 'sql_sugerido' => 'CREATE TABLE mensajes (...)'], 500);
            }

            $columnasTabla = $pdo->query("SHOW COLUMNS FROM `{$tablaMensajes}`")->fetchAll(PDO::FETCH_COLUMN);
            $buscarColumna = static function (array $candidatas, array $existentes): ?string {
                foreach ($candidatas as $candidata) if (in_array($candidata, $existentes, true)) return $candidata;
                return null;
            };
            $colRemitente = $buscarColumna(['remitente_id', 'id_remitente'], $columnasTabla);
            $colDestinatario = $buscarColumna(['destinatario_id', 'id_destinatario'], $columnasTabla);
            $colTexto = $buscarColumna(['mensaje', 'texto', 'contenido'], $columnasTabla);
            $colContexto = $buscarColumna(['contexto', 'tipo_consulta'], $columnasTabla);
            $colFecha = $buscarColumna(['fecha_creacion', 'fecha', 'created_at'], $columnasTabla);
            if (!$colRemitente || !$colDestinatario || !$colTexto) {
                responderMensajes([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'La tabla no tiene columnas compatibles para guardar mensajes.',
                    'tabla' => $tablaMensajes,
                    'columnas_detectadas' => $columnasTabla,
                    'sql_sugerido' => 'ALTER TABLE mensajes ADD COLUMN contexto VARCHAR(255) NULL;'
                ], 500);
            }

            $campos = [$colRemitente, $colDestinatario, $colTexto];
            $valores = [':remitente', ':destinatario', ':texto'];
            $parametros = [':remitente' => $usuarioId, ':destinatario' => $otroUsuarioId, ':texto' => $texto];
            if ($colContexto) { $campos[] = $colContexto; $valores[] = ':contexto'; $parametros[':contexto'] = $contextoMensaje; }
            foreach (['foto' => 'foto_url', 'video' => 'video_url', 'audio' => 'audio_url'] as $tipo => $columna) {
                if ($media[$tipo] !== null) {
                    if (!in_array($columna, $columnasTabla, true)) {
                        responderMensajes(['success' => false, 'status' => 'error', 'message' => 'La base de datos todavía no está preparada para este tipo de archivo. Actualizá el esquema e intentá nuevamente.'], 500);
                    }
                    $campos[] = $columna;
                    $valores[] = ':' . $tipo;
                    $parametros[':' . $tipo] = $media[$tipo];
                }
            }
            $consultaSql = 'INSERT INTO `' . $tablaMensajes . '` (`' . implode('`, `', $campos) . '`) VALUES (' . implode(', ', $valores) . ')';
            // DIAGNÓSTICO: registramos nombres/tabla, nunca contraseñas ni
            // archivos; los valores siguen viajando mediante parámetros PDO.
            error_log('[mensajeria] INSERT: ' . $consultaSql . ' | tabla=' . $tablaMensajes);
            $insertar = $pdo->prepare($consultaSql);
            $insertar->execute($parametros);
        } catch (PDOException $exception) {
            // DIAGNÓSTICO: devolvemos la causa SQL exacta y la consulta para
            // identificar columna, tabla, FK o destinatario incorrectos.
            responderMensajes([
                'success' => false,
                'status' => 'error',
                'message' => $exception->getMessage(),
                'sql' => $consultaSql ?? null,
                'sql_state' => $exception->getCode()
            ], 500);
        }
        $mensajeId = (int) $pdo->lastInsertId();
        // SOLUCIÓN: la hora se lee usando la columna temporal detectada; si la
        // tabla no tiene una, devolvemos la hora del servidor como respaldo.
        $horaGuardada = date('H:i');
        if ($colFecha) {
            $horaConsulta = $pdo->prepare("SELECT DATE_FORMAT(`{$colFecha}`, '%H:%i') FROM `{$tablaMensajes}` WHERE id = :id");
            $horaConsulta->execute([':id' => $mensajeId]);
            $horaGuardada = (string) ($horaConsulta->fetchColumn() ?: $horaGuardada);
        }
        // CAMBIO APLICADO: el envío termina aquí con un contrato explícito.
        // Antes continuaba hasta conversaciones y no devolvía el ID insertado.
        responderMensajes([
            'success' => true,
            'status' => 'success',
            'id' => $mensajeId,
            'remitente_id' => $usuarioId,
            'mensaje' => $texto,
            'foto_url' => $media['foto'],
            'video_url' => $media['video'],
            'audio_url' => $media['audio'],
            // CAMBIO APLICADO: devolvemos la hora guardada por MySQL para que
            // la burbuja optimista y el historial usen el mismo timestamp.
            'fecha' => $horaGuardada,
            'message' => 'Mensaje enviado correctamente.'
        ], 201);
    }
    if ($accion === 'leer' && $otroUsuarioId > 0) {
        $marcar = $pdo->prepare('UPDATE mensajes SET leido = 1 WHERE remitente_id = :otro AND destinatario_id = :usuario');
        $marcar->execute([':otro' => $otroUsuarioId, ':usuario' => $usuarioId]);
    }
    if ($accion === 'conversacion' && $otroUsuarioId > 0) {
        /* CAMBIO APLICADO: cada placeholder tiene nombre propio. PDO con
           prepares nativos no permite reutilizar el mismo parámetro nombrado. */
        $consulta = $pdo->prepare("SELECT m.id, m.remitente_id, m.destinatario_id, m.mensaje, m.contexto, m.foto_url, m.video_url, m.audio_url, m.fecha_creacion, DATE_FORMAT(m.fecha_creacion, '%H:%i') AS hora, m.leido, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil FROM mensajes m INNER JOIN usuarios u ON u.id = m.remitente_id WHERE (m.remitente_id = :usuario_emisor AND m.destinatario_id = :otro_receptor) OR (m.remitente_id = :otro_emisor AND m.destinatario_id = :usuario_receptor) ORDER BY m.fecha_creacion ASC, m.id ASC LIMIT 100");
        $consulta->execute([':usuario_emisor' => $usuarioId, ':otro_receptor' => $otroUsuarioId, ':otro_emisor' => $otroUsuarioId, ':usuario_receptor' => $usuarioId]);
        $mensajes = $consulta->fetchAll();
        responderMensajes(['success' => true, 'data' => $mensajes, 'messages' => $mensajes]);
    }
     /* CAMBIO APLICADO: el inbox devuelve último texto, fecha y pendientes,
         agrupando cada contacto una sola vez para la columna izquierda. */
    $esAdministrador = $usuarioActual['rol'] === 'administrador' ? 1 : 0;
    $consulta = $pdo->prepare("SELECT u.id AS usuario_id, u.nombre_completo, u.nickname, COALESCE(u.foto_perfil, u.avatar) AS foto_perfil, MAX(m.fecha_creacion) AS ultima_fecha, SUBSTRING_INDEX(GROUP_CONCAT(m.mensaje ORDER BY m.fecha_creacion DESC, m.id DESC SEPARATOR '||'), '||', 1) AS ultimo_mensaje, SUM(CASE WHEN m.destinatario_id = :usuario_no_leido AND m.leido = 0 THEN 1 ELSE 0 END) AS no_leidos FROM mensajes m INNER JOIN usuarios u ON u.id = CASE WHEN m.remitente_id = :usuario_contacto THEN m.destinatario_id ELSE m.remitente_id END WHERE (m.remitente_id = :usuario_emisor OR m.destinatario_id = :usuario_receptor) AND (u.rol = 'administrador' OR (:es_administrador = 1 AND u.rol = 'organizador')) GROUP BY u.id, u.nombre_completo, u.nickname, u.foto_perfil, u.avatar ORDER BY ultima_fecha DESC");
    $consulta->execute([':usuario_no_leido' => $usuarioId, ':usuario_contacto' => $usuarioId, ':usuario_emisor' => $usuarioId, ':usuario_receptor' => $usuarioId, ':es_administrador' => $esAdministrador]);
    $conversaciones = $consulta->fetchAll();
    $contactosPermitidos = $pdo->prepare("SELECT id AS usuario_id, nombre_completo, nickname, COALESCE(foto_perfil, avatar) AS foto_perfil, '' AS ultimo_mensaje, 0 AS no_leidos FROM usuarios WHERE (rol = 'administrador' OR (:es_administrador = 1 AND rol = 'organizador')) AND id <> :usuario ORDER BY nombre_completo ASC");
    $contactosPermitidos->execute([':es_administrador' => $esAdministrador, ':usuario' => $usuarioId]);
    $conversacionIds = array_fill_keys(array_map(static fn ($conversacion) => (int) $conversacion['usuario_id'], $conversaciones), true);
    foreach ($contactosPermitidos->fetchAll() as $contactoPermitido) {
        if (!isset($conversacionIds[(int) $contactoPermitido['usuario_id']])) {
            $conversaciones[] = $contactoPermitido;
        }
    }
    responderMensajes(['success' => true, 'data' => $conversaciones, 'conversations' => $conversaciones]);
} catch (Throwable $exception) {
    /* El buffer se limpia también en errores de MySQL para que el cliente reciba
       JSON válido en lugar del HTML generado por un warning o notice. */
    responderMensajes(['success' => false, 'error' => 'No se pudo cargar la mensajería.'], 500);
}