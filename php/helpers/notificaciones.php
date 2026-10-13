<?php

function sigtur_crear_notificacion(PDO $pdo, int $usuarioId, string $titulo, string $mensaje, string $tipo = 'general'): int
{
    if ($usuarioId <= 0) {
        return 0;
    }

    $titulo = trim($titulo);
    $mensaje = trim($mensaje);
    if ($titulo === '' || $mensaje === '') {
        return 0;
    }

    $stmt = $pdo->prepare('INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, leida, fecha_creacion) VALUES (:usuario_id, :titulo, :mensaje, :tipo, 0, NOW())');
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':titulo' => $titulo,
        ':mensaje' => $mensaje,
        ':tipo' => $tipo,
    ]);

    return (int) $pdo->lastInsertId();
}

function sigtur_notificar_turistas(PDO $pdo, string $titulo, string $mensaje, string $tipo = 'general'): int
{
    $usuarios = $pdo->query("SELECT id FROM usuarios WHERE LOWER(rol) = 'turista'")->fetchAll(PDO::FETCH_COLUMN);
    $cantidad = 0;

    foreach ($usuarios as $usuarioId) {
        if (sigtur_crear_notificacion($pdo, (int) $usuarioId, $titulo, $mensaje, $tipo) > 0) {
            $cantidad++;
        }
    }

    return $cantidad;
}

function sigtur_generar_recordatorios_eventos(PDO $pdo, int $usuarioId): int
{
    if ($usuarioId <= 0) {
        return 0;
    }

    $usuario = $pdo->prepare("SELECT rol FROM usuarios WHERE id = :id LIMIT 1");
    $usuario->execute([':id' => $usuarioId]);
    if (sigtur_normalizar_rol((string) $usuario->fetchColumn()) !== 'turista') {
        return 0;
    }

    $eventos = $pdo->prepare("SELECT DISTINCT e.id, e.titulo, e.fecha, e.ubicacion,
        CASE WHEN f.id IS NOT NULL OR i.id IS NOT NULL THEN 1 ELSE 0 END AS es_personal,
        CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS es_favorito,
        CASE WHEN i.id IS NOT NULL THEN 1 ELSE 0 END AS esta_inscripto
        FROM eventos e
        LEFT JOIN favoritos_eventos f ON (f.evento_id = e.id OR f.evento_slug = e.slug) AND f.usuario_id = :favorito_usuario
        LEFT JOIN inscripciones_eventos i ON i.evento_slug = e.slug AND i.usuario_id = :inscripcion_usuario
        WHERE e.es_pasado = 0
          AND e.fecha >= CURDATE()
          AND e.fecha <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
          AND (e.fecha <= DATE_ADD(CURDATE(), INTERVAL 3 DAY) OR f.id IS NOT NULL OR i.id IS NOT NULL)
        ORDER BY e.fecha ASC, e.id DESC");
    $eventos->execute([
        ':favorito_usuario' => $usuarioId,
        ':inscripcion_usuario' => $usuarioId,
    ]);

    $reservar = $pdo->prepare('INSERT IGNORE INTO notificaciones_recordatorios (usuario_id, evento_id, tipo) VALUES (:usuario, :evento, :tipo)');
    $creados = 0;
    foreach ($eventos->fetchAll() as $evento) {
        $dias = (int) floor((strtotime((string) $evento['fecha']) - strtotime(date('Y-m-d'))) / 86400);
        $tipo = $dias <= 0 ? 'evento_ultimo_dia' : ($evento['es_personal'] ? 'evento_personal_proximo' : 'evento_proximo');
        $reservar->execute([
            ':usuario' => $usuarioId,
            ':evento' => (int) $evento['id'],
            ':tipo' => $tipo,
        ]);
        if ($reservar->rowCount() !== 1) {
            continue;
        }

        $titulo = $dias <= 0 ? 'Último día del evento' : ($evento['es_personal'] ? 'Tu evento está cerca' : 'Evento próximo en SIGTUR');
        $detalle = $dias <= 0 ? 'Es hoy: ' : 'Faltan ' . $dias . ' día' . ($dias === 1 ? '' : 's') . ': ';
        $preferencia = ((int) $evento['es_favorito'] === 1 || (int) $evento['esta_inscripto'] === 1) ? ' Lo tenés guardado o estás inscripto.' : '';
        $mensaje = $detalle . $evento['titulo'] . ' en ' . $evento['ubicacion'] . '.' . $preferencia;
        if (sigtur_crear_notificacion($pdo, $usuarioId, $titulo, $mensaje, 'recordatorio_evento') > 0) {
            $creados++;
        }
    }

    return $creados;
}

function sigtur_listar_notificaciones(PDO $pdo, int $usuarioId, int $limite = 20): array
{
    if ($usuarioId <= 0) {
        return [];
    }

    $stmt = $pdo->prepare('SELECT id, usuario_id, titulo, mensaje, tipo, leida, fecha_creacion FROM notificaciones WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC, id DESC LIMIT :limite');
    $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
    $stmt->bindValue(':limite', max(1, $limite), PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function sigtur_contar_notificaciones_no_leidas(PDO $pdo, int $usuarioId): int
{
    if ($usuarioId <= 0) {
        return 0;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM notificaciones WHERE usuario_id = :usuario_id AND leida = 0');
    $stmt->execute([':usuario_id' => $usuarioId]);

    return (int) $stmt->fetchColumn();
}

function sigtur_marcar_notificacion_leida(PDO $pdo, int $usuarioId, int $notificacionId): bool
{
    if ($usuarioId <= 0 || $notificacionId <= 0) {
        return false;
    }

    $stmt = $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE usuario_id = :usuario_id AND id = :id LIMIT 1');
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':id' => $notificacionId,
    ]);

    return $stmt->rowCount() > 0;
}

function sigtur_marcar_todas_las_notificaciones_leidas(PDO $pdo, int $usuarioId): void
{
    if ($usuarioId <= 0) {
        return;
    }

    $stmt = $pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE usuario_id = :usuario_id AND leida = 0');
    $stmt->execute([':usuario_id' => $usuarioId]);
}

function sigtur_guardar_suscripcion(PDO $pdo, int $usuarioId, string $endpoint, string $p256dh, string $auth, ?string $userAgent = null): bool
{
    if ($usuarioId <= 0 || $endpoint === '' || $p256dh === '' || $auth === '') {
        return false;
    }

    $stmt = $pdo->prepare('SELECT id FROM notificaciones_suscripciones WHERE usuario_id = :usuario_id AND endpoint = :endpoint LIMIT 1');
    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':endpoint' => $endpoint,
    ]);

    if ($stmt->fetch()) {
        $stmtUpdate = $pdo->prepare('UPDATE notificaciones_suscripciones SET p256dh = :p256dh, auth = :auth, user_agent = :user_agent, activo = 1, actualizado_en = NOW() WHERE usuario_id = :usuario_id AND endpoint = :endpoint');
        $stmtUpdate->execute([
            ':usuario_id' => $usuarioId,
            ':endpoint' => $endpoint,
            ':p256dh' => $p256dh,
            ':auth' => $auth,
            ':user_agent' => $userAgent,
        ]);
        return true;
    }

    $insert = $pdo->prepare('INSERT INTO notificaciones_suscripciones (usuario_id, endpoint, p256dh, auth, user_agent, activo, creado_en) VALUES (:usuario_id, :endpoint, :p256dh, :auth, :user_agent, 1, NOW())');
    $insert->execute([
        ':usuario_id' => $usuarioId,
        ':endpoint' => $endpoint,
        ':p256dh' => $p256dh,
        ':auth' => $auth,
        ':user_agent' => $userAgent,
    ]);

    return true;
}

function sigtur_obtener_suscripciones_usuario(PDO $pdo, int $usuarioId): array
{
    if ($usuarioId <= 0) {
        return [];
    }

    $stmt = $pdo->prepare('SELECT id, endpoint, p256dh, auth, user_agent FROM notificaciones_suscripciones WHERE usuario_id = :usuario_id AND activo = 1 ORDER BY creado_en DESC');
    $stmt->execute([':usuario_id' => $usuarioId]);

    return $stmt->fetchAll();
}
