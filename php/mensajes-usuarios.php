<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$usuario = sigtur_requerir_rol($pdo, ['administrador', 'organizador'], 'login.php', '../index.php');
$csrfToken = sigtur_csrf_token();
$chatAbiertoId = filter_var($_GET['chat'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$soporteStmt = $pdo->prepare('SELECT p.id, p.usuario_id, p.email, p.pregunta, p.pregunta_eliminada, p.respuesta, p.estado, p.fecha_creacion,
        u.nombre_completo,
        EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
            WHERE oculto.tipo = \'soporte_pregunta\' AND oculto.mensaje_id = p.id AND oculto.usuario_id = :usuario_oculto) AS pregunta_oculta
    FROM preguntas_frecuentes p
    LEFT JOIN usuarios u ON u.id = p.usuario_id
    ORDER BY p.fecha_creacion DESC, p.id DESC');
$soporteStmt->execute([':usuario_oculto' => (int) $usuario['id']]);
$soporte = $soporteStmt->fetchAll();
$reportesStmt = $pdo->prepare("SELECT m.id, m.remitente_id, m.mensaje, m.eliminado_para_todos, m.contexto, m.fecha_creacion,
        u.nombre_completo, u.email
    FROM mensajes m
    INNER JOIN usuarios u ON u.id = m.remitente_id
    WHERE m.destinatario_id = :destinatario AND m.mensaje LIKE '[Consulta %'
      AND NOT EXISTS (SELECT 1 FROM mensajes_ocultos_usuario oculto WHERE oculto.mensaje_id = m.id AND oculto.usuario_id = :oculto_destinatario)
    ORDER BY m.fecha_creacion DESC, m.id DESC
    LIMIT 100");
$reportesStmt->execute([':destinatario' => (int) $usuario['id'], ':oculto_destinatario' => (int) $usuario['id']]);
$reportes = $reportesStmt->fetchAll();

$idsSoporte = array_column($soporte, 'id');
$mensajesSoporte = [];
if ($idsSoporte) {
    $placeholders = implode(',', array_fill(0, count($idsSoporte), '?'));
    $stmt = $pdo->prepare("SELECT m.id, m.pregunta_id, m.autor_id, m.autor_rol, m.mensaje, m.fecha_creacion, m.eliminado
        FROM preguntas_frecuentes_mensajes m WHERE m.pregunta_id IN ($placeholders)
          AND NOT EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
              WHERE oculto.tipo = 'soporte' AND oculto.mensaje_id = m.id AND oculto.usuario_id = ?)
        ORDER BY m.id ASC");
    $stmt->execute([...$idsSoporte, (int) $usuario['id']]);
    foreach ($stmt->fetchAll() as $mensaje) {
        $mensajesSoporte[(int) $mensaje['pregunta_id']][] = $mensaje;
    }
}

$respuestasReporte = [];
if ($reportes) {
    $contextosReporte = [];
    foreach ($reportes as $reporte) {
        $contexto = (string) ($reporte['contexto'] ?? '');
        $contextosReporte[] = preg_match('/^soporte:reporte:\d+$/', $contexto) === 1
            ? $contexto
            : 'soporte:reporte:' . (int) $reporte['id'];
    }
    $contextosReporte = array_values(array_unique($contextosReporte));
    $marcadoresReporte = [];
    $parametrosReporte = [];
    foreach ($contextosReporte as $indice => $contexto) {
        $marcador = ':contexto_reporte_' . $indice;
        $marcadoresReporte[] = $marcador;
        $parametrosReporte[$marcador] = $contexto;
    }
    $stmt = $pdo->prepare("SELECT m.id, m.contexto, m.mensaje, m.eliminado_para_todos, m.fecha_creacion, m.remitente_id,
            u.nombre_completo, u.rol
        FROM mensajes m
        INNER JOIN usuarios u ON u.id = m.remitente_id
        WHERE m.contexto IN (" . implode(',', $marcadoresReporte) . ")
          AND m.mensaje NOT LIKE '[Consulta %'
          AND NOT EXISTS (SELECT 1 FROM mensajes_ocultos_usuario oculto WHERE oculto.mensaje_id = m.id AND oculto.usuario_id = :oculto_usuario)
        ORDER BY m.id ASC");
    $parametrosReporte[':oculto_usuario'] = (int) $usuario['id'];
    $stmt->execute($parametrosReporte);
    foreach ($stmt->fetchAll() as $respuesta) {
        $respuestasReporte[(string) $respuesta['contexto']][] = $respuesta;
    }
}

$cantidadPendiente = count(array_filter($soporte, static fn ($item) => $item['estado'] !== 'respondida'))
    + count(array_filter($reportes, static function ($item) use ($respuestasReporte): bool {
        $contexto = (string) ($item['contexto'] ?? '');
        $clave = preg_match('/^soporte:reporte:\d+$/', $contexto) === 1
            ? $contexto
            : 'soporte:reporte:' . (int) $item['id'];
        return empty($respuestasReporte[$clave]);
    }));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mensajes de usuarios | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/panel-admin.css">
    <script src="../js/volver-posicion.js" defer></script>
</head>
<body class="admin-panel-page">
    <main class="admin-panel">
        <header class="admin-hero">
            <div class="admin-hero__copy">
                <p class="admin-eyebrow">SIGTUR · <?= htmlspecialchars(ucfirst($usuario['rol']), ENT_QUOTES, 'UTF-8') ?></p>
                <h1>Mensajes de usuarios</h1>
                <p><?= $cantidadPendiente ?> mensajes pendientes de respuesta. Las consultas llegan a todos los administradores y organizadores.</p>
            </div>
            <nav class="admin-nav" aria-label="Navegación">
                <a class="admin-nav__link admin-nav__link--subtle" href="<?= $usuario['rol'] === 'administrador' ? 'panel-admin.php' : 'panel-organizador.php' ?>">Volver al panel</a>
                <a class="admin-nav__link admin-nav__link--subtle" href="../index.php">Inicio</a>
            </nav>
        </header>

        <?php foreach ([
            ['soporte', 'Preguntas de soporte', $soporte],
            ['reporte', 'Consultas generales', $reportes],
        ] as [$tipo, $titulo, $items]): ?>
            <section class="admin-section" aria-labelledby="<?= $tipo ?>-messages-title">
                <div class="admin-section__heading">
                    <div><p class="admin-eyebrow">Bandeja compartida</p><h2 id="<?= $tipo ?>-messages-title"><?= $titulo ?></h2></div>
                    <span class="admin-count"><?= count($items) ?> mensajes</span>
                </div>
                <?php if (!$items): ?>
                    <p class="admin-empty">No hay mensajes en esta categoría.</p>
                <?php else: ?>
                    <div class="admin-question-list">
                        <?php foreach ($items as $item): ?>
                            <?php
                            $id = (int) $item['id'];
                            $contextoReporte = (string) ($item['contexto'] ?? '');
                            $claveReporte = preg_match('/^soporte:reporte:\d+$/', $contextoReporte) === 1
                                ? $contextoReporte
                                : 'soporte:reporte:' . $id;
                            $mensajeReporte = $tipo === 'reporte' ? (string) ($item['mensaje'] ?? '') : '';
                            $contextoVisible = preg_match('/^\[Consulta ([^\]]+)\]:/', $mensajeReporte, $coincidenciaContexto) === 1
                                ? $coincidenciaContexto[1]
                                : ($contextoReporte !== '' && !str_starts_with($contextoReporte, 'soporte:reporte:') ? $contextoReporte : 'Soporte');
                            $autor = $tipo === 'reporte' ? $item['nombre_completo'] : ($item['nombre_completo'] ?? $item['email'] ?? 'Usuario');
                            ?>
                            <article class="admin-question" id="<?= $tipo ?>-<?= $id ?>">
                                <div class="admin-question__header">
                                    <strong><?= htmlspecialchars($tipo === 'soporte' ? 'Pregunta de soporte' : $contextoVisible, ENT_QUOTES, 'UTF-8') ?></strong>
                                    <?php if ($tipo === 'soporte' && $usuario['rol'] === 'administrador'): ?>
                                        <div class="admin-support-chat__actions">
                                            <form method="post" action="api/responder_mensaje_usuario.php" onsubmit="return confirm('¿Vaciar los mensajes de esta conversación? La pregunta original se conservará y quedará pendiente.');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="tipo" value="soporte">
                                                <input type="hidden" name="accion" value="vaciar">
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <button class="admin-button admin-button--secondary" type="submit">Vaciar chat</button>
                                            </form>
                                            <form method="post" action="api/responder_mensaje_usuario.php" onsubmit="return confirm('¿Eliminar esta pregunta y todo su chat? Esta acción no se puede deshacer.');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="tipo" value="soporte">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <button class="admin-button admin-button--danger" type="submit">Eliminar chat</button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p><strong><?= htmlspecialchars((string) $autor, ENT_QUOTES, 'UTF-8') ?></strong></p>
                                <?php if ($tipo === 'soporte'): ?>
                                    <details class="admin-support-chat"<?= (int) $chatAbiertoId === $id ? ' open' : '' ?>>
                                        <summary><span data-chat-label-open>Ocultar chat</span><span data-chat-label-closed>Ver chat</span></summary>
                                        <div class="admin-question-chat">
                                        <?php $puedeEditarPreguntaSoporte = (int) ($item['usuario_id'] ?? 0) === (int) $usuario['id'] && empty($item['pregunta_eliminada']);
                                        $esPreguntaPropiaSoporte = (int) ($item['usuario_id'] ?? 0) === (int) $usuario['id'];
                                        $preguntaSoporteEliminada = !empty($item['pregunta_eliminada']);
                                        ?>
                                        <?php if (empty($item['pregunta_oculta'])): ?>
                                        <div class="admin-question-chat__message admin-question-chat__message--visitor" data-message-item data-message-type="soporte_pregunta" data-message-id="<?= $id ?>" data-can-edit="<?= $puedeEditarPreguntaSoporte ? '1' : '0' ?>" data-can-delete="<?= $esPreguntaPropiaSoporte ? '1' : '0' ?>">
                                            <strong><?= htmlspecialchars((string) $autor, ENT_QUOTES, 'UTF-8') ?></strong>
                                            <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if ($puedeEditarPreguntaSoporte): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $esPreguntaPropiaSoporte || $preguntaSoporteEliminada ? '' : ' disabled' ?>>Eliminar para mí</button><?php if ($esPreguntaPropiaSoporte && !$preguntaSoporteEliminada): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                                            <p data-message-text><?= empty($item['pregunta_eliminada']) ? nl2br(htmlspecialchars($item['pregunta'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado' ?></p>
                                            <small><?= htmlspecialchars($item['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                        <?php endif; ?>
                                        <?php foreach ($mensajesSoporte[$id] ?? [] as $mensaje):
                                            $puedeEditarMensajeSoporte = (int) ($mensaje['autor_id'] ?? 0) === (int) $usuario['id'] && empty($mensaje['eliminado']);
                                            $mensajeSoporteEliminado = !empty($mensaje['eliminado']);
                                            $esMensajePropioSoporte = (int) ($mensaje['autor_id'] ?? 0) === (int) $usuario['id']; ?>
                                            <div class="admin-question-chat__message <?= in_array($mensaje['autor_rol'], ['administrador', 'organizador'], true) ? 'admin-question-chat__message--organizer' : 'admin-question-chat__message--visitor' ?>" data-message-item data-message-type="soporte" data-message-id="<?= (int) $mensaje['id'] ?>" data-can-edit="<?= $puedeEditarMensajeSoporte ? '1' : '0' ?>" data-can-delete="<?= $esMensajePropioSoporte ? '1' : '0' ?>">
                                                <strong><?= in_array($mensaje['autor_rol'], ['administrador', 'organizador'], true) ? htmlspecialchars(ucfirst($mensaje['autor_rol']), ENT_QUOTES, 'UTF-8') : 'Usuario' ?></strong>
                                                <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (!$mensajeSoporteEliminado && $puedeEditarMensajeSoporte): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $esMensajePropioSoporte || $mensajeSoporteEliminado ? '' : ' disabled' ?>>Eliminar para mí</button><?php if (!$mensajeSoporteEliminado && $esMensajePropioSoporte): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                                                <p data-message-text><?= !$mensajeSoporteEliminado ? nl2br(htmlspecialchars($mensaje['mensaje'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado para todos' ?></p>
                                                <small><?= htmlspecialchars($mensaje['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                        </div>
                                    </details>
                                <?php else: ?>
                                    <div class="admin-question-chat__message admin-question-chat__message--visitor" data-message-item data-message-type="direct" data-message-api="api/mensajes.php" data-message-id="<?= $id ?>" data-can-edit="0" data-can-delete="0">
                                        <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><button type="button" data-message-action="delete-me"<?= !empty($item['eliminado_para_todos']) ? '' : ' disabled' ?>>Eliminar para mí</button></div></details>
                                        <p data-message-text><?= !empty($item['eliminado_para_todos']) ? 'Mensaje eliminado para todos' : nl2br(htmlspecialchars(preg_replace('/^\[Consulta [^\]]+\]:\s*/', '', $item['mensaje']) ?? $item['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                                    </div>
                                    <small><?= htmlspecialchars($contextoVisible, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($item['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                                    <?php foreach ($respuestasReporte[$claveReporte] ?? [] as $respuesta):
                                        $puedeEditarRespuestaReporte = (int) $respuesta['remitente_id'] === (int) $usuario['id'];
                                        $mensajeRespuestaEliminado = !empty($respuesta['eliminado_para_todos']); ?>
                                        <div class="admin-mensajes-respuesta" data-message-item data-message-type="direct" data-message-api="api/mensajes.php" data-message-id="<?= (int) $respuesta['id'] ?>" data-can-edit="<?= $puedeEditarRespuestaReporte && !$mensajeRespuestaEliminado ? '1' : '0' ?>" data-can-delete="<?= $puedeEditarRespuestaReporte ? '1' : '0' ?>">
                                            <strong><?= in_array($respuesta['rol'], ['administrador', 'organizador'], true)
                                                ? 'Respuesta de ' . htmlspecialchars(ucfirst($respuesta['rol']), ENT_QUOTES, 'UTF-8')
                                                : htmlspecialchars($respuesta['nombre_completo'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if ($puedeEditarRespuestaReporte && !$mensajeRespuestaEliminado): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $puedeEditarRespuestaReporte || $mensajeRespuestaEliminado ? '' : ' disabled' ?>>Eliminar para mí</button><?php if (!$mensajeRespuestaEliminado && $puedeEditarRespuestaReporte): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                                            <p data-message-text><?= !empty($respuesta['eliminado_para_todos']) ? 'Mensaje eliminado para todos' : nl2br(htmlspecialchars($respuesta['mensaje'], ENT_QUOTES, 'UTF-8')) ?></p>
                                            <small><?= htmlspecialchars($respuesta['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <form class="admin-form admin-reply-form" method="post" action="api/responder_mensaje_usuario.php">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="tipo" value="<?= $tipo ?>">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <input type="hidden" name="submission_key" value="<?= htmlspecialchars(bin2hex(random_bytes(16)), ENT_QUOTES, 'UTF-8') ?>">
                                    <label>Responder al usuario<textarea name="respuesta" maxlength="2000" required></textarea></label>
                                    <button class="admin-button" type="submit">Enviar respuesta</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </main>
    <script src="../js/tema.js" defer></script>
    <script src="../js/mensajes-acciones.js"></script>
    <?php $mensajesWidgetRoot = '../'; include __DIR__ . '/mensajes-widget.php'; ?>
</body>
</html>
