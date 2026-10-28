<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/consultas_eventos.php';
require_once __DIR__ . '/helpers/notificaciones.php';
$soporteUsuario = sigtur_usuario_actual($pdo);
$soporteCsrfToken = $soporteUsuario ? sigtur_csrf_token() : '';
$soporteUrlVolver = $soporteUsuario
  ? 'perfil.php?id=' . (int) $soporteUsuario['id']
  : '../index.php';
$soportePreguntas = [];
$consultasEventos = [];
$mensajesPorConsultaEvento = [];
if ($soporteUsuario) {
    sigtur_asegurar_mensajes_consulta_evento($pdo);
    $consultasEventosStmt = $pdo->prepare('SELECT p.id, p.fecha_creacion, e.titulo
        FROM preguntas_eventos p
        INNER JOIN eventos e ON e.slug = p.evento_slug
        WHERE p.usuario_id = :usuario_id
        ORDER BY p.fecha_creacion DESC, p.id DESC');
    $consultasEventosStmt->execute([':usuario_id' => (int) $soporteUsuario['id']]);
    $consultasEventos = $consultasEventosStmt->fetchAll();
    if ($consultasEventos) {
        $idsConsultasEventos = array_column($consultasEventos, 'id');
        $marcadoresConsultasEventos = implode(',', array_fill(0, count($idsConsultasEventos), '?'));
        $mensajesEventosStmt = $pdo->prepare("SELECT m.id, m.pregunta_id, m.autor_id, m.autor_rol, m.mensaje, m.fecha_creacion, m.clave_migracion, m.eliminado
            FROM preguntas_eventos_mensajes m
            WHERE m.pregunta_id IN ($marcadoresConsultasEventos)
              AND NOT EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
                  WHERE oculto.tipo = 'evento' AND oculto.mensaje_id = m.id AND oculto.usuario_id = ?)
            ORDER BY m.id ASC");
        $mensajesEventosStmt->execute([...$idsConsultasEventos, (int) $soporteUsuario['id']]);
        foreach ($mensajesEventosStmt->fetchAll() as $mensajeEvento) {
            $mensajesPorConsultaEvento[(int) $mensajeEvento['pregunta_id']][] = $mensajeEvento;
        }
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            http_response_code(403);
            exit('La solicitud expiró. Recarga la página e inténtalo de nuevo.');
        }
        $accionConsultaEvento = (string) ($_POST['accion_consulta_evento'] ?? '');
        if (in_array($accionConsultaEvento, ['vaciar', 'eliminar'], true)) {
            $consultaEventoId = filter_var($_POST['pregunta_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$consultaEventoId || $consultaEventoId <= 0) {
                http_response_code(422);
                exit('La conversación seleccionada no es válida.');
            }

            $pdo->beginTransaction();
            try {
                $bloquearConsultaEvento = $pdo->prepare('SELECT id FROM preguntas_eventos WHERE id = :id AND usuario_id = :usuario_id FOR UPDATE');
                $bloquearConsultaEvento->execute([
                    ':id' => $consultaEventoId,
                    ':usuario_id' => (int) $soporteUsuario['id'],
                ]);
                if (!$bloquearConsultaEvento->fetchColumn()) {
                    $pdo->rollBack();
                    http_response_code(404);
                    exit('No se encontró la conversación solicitada.');
                }

                if ($accionConsultaEvento === 'vaciar') {
                    $vaciarMensajesEvento = $pdo->prepare("DELETE FROM preguntas_eventos_mensajes
                        WHERE pregunta_id = :id AND COALESCE(clave_migracion, '') <> 'pregunta_inicial'");
                    $vaciarMensajesEvento->execute([':id' => $consultaEventoId]);
                    $reiniciarConsultaEvento = $pdo->prepare('UPDATE preguntas_eventos SET respuesta = NULL WHERE id = :id AND usuario_id = :usuario_id');
                    $reiniciarConsultaEvento->execute([
                        ':id' => $consultaEventoId,
                        ':usuario_id' => (int) $soporteUsuario['id'],
                    ]);
                } else {
                    $eliminarConsultaEvento = $pdo->prepare('DELETE FROM preguntas_eventos WHERE id = :id AND usuario_id = :usuario_id');
                    $eliminarConsultaEvento->execute([
                        ':id' => $consultaEventoId,
                        ':usuario_id' => (int) $soporteUsuario['id'],
                    ]);
                }

                $pdo->commit();
                header('Location: soporte.php?chat=' . ($accionConsultaEvento === 'vaciar' ? 'vaciado' : 'eliminado') . '#chat-organizadores');
                exit;
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                error_log('Error al gestionar conversación con organizador: ' . $exception->getMessage());
                http_response_code(500);
                exit('No se pudo actualizar la conversación. Inténtalo nuevamente.');
            }
        }

      $accionSoporte = (string) ($_POST['accion_soporte'] ?? 'responder');
        $preguntaId = filter_var($_POST['pregunta_id'] ?? null, FILTER_VALIDATE_INT);
      if (in_array($accionSoporte, ['vaciar', 'eliminar'], true)) {
        if (!$preguntaId || $preguntaId <= 0) {
          http_response_code(422);
          exit('La conversación seleccionada no es válida.');
        }

        $pdo->beginTransaction();
        try {
          $bloquearPregunta = $pdo->prepare('SELECT id FROM preguntas_frecuentes WHERE id = :id AND usuario_id = :usuario_id FOR UPDATE');
          $bloquearPregunta->execute([
            ':id' => $preguntaId,
            ':usuario_id' => (int) $soporteUsuario['id'],
          ]);
          if (!$bloquearPregunta->fetchColumn()) {
            $pdo->rollBack();
            http_response_code(404);
            exit('No se encontró la conversación solicitada.');
          }

          $eliminarMensajes = $pdo->prepare('DELETE FROM preguntas_frecuentes_mensajes WHERE pregunta_id = :id');
          $eliminarMensajes->execute([':id' => $preguntaId]);
          if ($accionSoporte === 'vaciar') {
            $reiniciarPregunta = $pdo->prepare("UPDATE preguntas_frecuentes SET estado = 'pendiente', respuesta = NULL, respondido_por = NULL, fecha_respuesta = NULL WHERE id = :id AND usuario_id = :usuario_id");
            $reiniciarPregunta->execute([
              ':id' => $preguntaId,
              ':usuario_id' => (int) $soporteUsuario['id'],
            ]);
          } else {
            $eliminarPregunta = $pdo->prepare('DELETE FROM preguntas_frecuentes WHERE id = :id AND usuario_id = :usuario_id');
            $eliminarPregunta->execute([
              ':id' => $preguntaId,
              ':usuario_id' => (int) $soporteUsuario['id'],
            ]);
          }
          $pdo->commit();
          header('Location: soporte.php?chat=' . ($accionSoporte === 'vaciar' ? 'vaciado' : 'eliminado') . '#respuestas-administradores');
          exit;
        } catch (Throwable $exception) {
          if ($pdo->inTransaction()) {
            $pdo->rollBack();
          }
          error_log('Error al gestionar conversación de soporte: ' . $exception->getMessage());
          http_response_code(500);
          exit('No se pudo actualizar la conversación. Inténtalo nuevamente.');
        }
      }
        $respuesta = trim((string) ($_POST['respuesta'] ?? ''));
        if (!$preguntaId || $preguntaId <= 0 || $respuesta === '' || mb_strlen($respuesta, 'UTF-8') > 4000) {
            http_response_code(422);
            exit('La respuesta es obligatoria y no puede superar los 4000 caracteres.');
        }

        $pdo->beginTransaction();
        try {
            $bloquearPregunta = $pdo->prepare("SELECT id FROM preguntas_frecuentes
                WHERE id = :id AND usuario_id = :usuario_id AND estado = 'respondida'
                FOR UPDATE");
            $bloquearPregunta->execute([
                ':id' => $preguntaId,
                ':usuario_id' => (int) $soporteUsuario['id'],
            ]);
            if (!$bloquearPregunta->fetchColumn()) {
                $pdo->rollBack();
                http_response_code(403);
                exit('No puedes responder esta pregunta o ya tiene una respuesta pendiente del equipo.');
            }

            $ultimaRespuesta = $pdo->prepare('SELECT autor_rol FROM preguntas_frecuentes_mensajes WHERE pregunta_id = :id ORDER BY id DESC LIMIT 1 FOR UPDATE');
            $ultimaRespuesta->execute([':id' => $preguntaId]);
            if (!in_array($ultimaRespuesta->fetchColumn(), ['administrador', 'organizador'], true)) {
                $pdo->rollBack();
                http_response_code(409);
                exit('Solo puedes responder después de recibir un mensaje del equipo SIGTUR.');
            }

            $guardarRespuesta = $pdo->prepare('INSERT INTO preguntas_frecuentes_mensajes (pregunta_id, autor_id, autor_rol, mensaje)
                VALUES (:pregunta_id, :autor_id, :autor_rol, :mensaje)');
            $guardarRespuesta->execute([
                ':pregunta_id' => $preguntaId,
                ':autor_id' => (int) $soporteUsuario['id'],
                ':autor_rol' => $soporteUsuario['rol'],
                ':mensaje' => $respuesta,
            ]);
            $marcarPendiente = $pdo->prepare("UPDATE preguntas_frecuentes SET estado = 'pendiente' WHERE id = :id");
            $marcarPendiente->execute([':id' => $preguntaId]);
            $pdo->commit();
            sigtur_notificar_equipo_consulta(
                $pdo,
                (int) $soporteUsuario['id'],
                'Nuevo mensaje en Soporte',
                'Un usuario respondió una conversación de soporte.',
                'consulta_soporte'
            );
            header('Location: soporte.php?respuesta=1#respuestas-administradores');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('Error al responder conversación de soporte: ' . $exception->getMessage());
            http_response_code(500);
            exit('No se pudo enviar tu respuesta. Inténtalo nuevamente.');
        }
    }

    $preguntasSoporteStmt = $pdo->prepare('SELECT id, pregunta, pregunta_eliminada, respuesta, estado, fecha_creacion, fecha_respuesta,
            EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
                WHERE oculto.tipo = \'soporte_pregunta\' AND oculto.mensaje_id = preguntas_frecuentes.id
                  AND oculto.usuario_id = :usuario_oculto) AS pregunta_oculta
        FROM preguntas_frecuentes
        WHERE usuario_id = :usuario_id
        ORDER BY fecha_creacion DESC');
    $preguntasSoporteStmt->execute([
        ':usuario_oculto' => (int) $soporteUsuario['id'],
        ':usuario_id' => (int) $soporteUsuario['id'],
    ]);
    $soportePreguntas = $preguntasSoporteStmt->fetchAll();
    $idsSoporte = array_column($soportePreguntas, 'id');
    $respuestasPorPregunta = [];
    if ($idsSoporte) {
        $marcadoresSoporte = implode(',', array_fill(0, count($idsSoporte), '?'));
        $mensajesSoporte = $pdo->prepare("SELECT m.id, m.pregunta_id, m.autor_id, m.autor_rol, m.mensaje, m.fecha_creacion, m.eliminado
            FROM preguntas_frecuentes_mensajes m
            WHERE m.pregunta_id IN ($marcadoresSoporte)
              AND NOT EXISTS (SELECT 1 FROM mensajes_conversacion_ocultos oculto
                  WHERE oculto.tipo = 'soporte' AND oculto.mensaje_id = m.id
                    AND oculto.usuario_id = ?)
            ORDER BY m.id ASC");
        $mensajesSoporte->execute([...$idsSoporte, (int) $soporteUsuario['id']]);
        foreach ($mensajesSoporte->fetchAll() as $mensajeSoporte) {
            $respuestasPorPregunta[(int) $mensajeSoporte['pregunta_id']][] = $mensajeSoporte;
        }
        foreach ($soportePreguntas as &$preguntaSoporte) {
            $preguntaSoporte['mensajes'] = $respuestasPorPregunta[(int) $preguntaSoporte['id']] ?? [];
        }
        unset($preguntaSoporte);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title data-i18n-title="pageSupportTitle">Soporte | Sigtur Salto</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="../css/soporte.css" />
</head>
<body>
  <main class="support-page">
    <section class="support-container">
      <header class="hero">
        <div class="hero-top">
          <a class="back-pill" href="<?= htmlspecialchars($soporteUrlVolver, ENT_QUOTES, 'UTF-8') ?>" aria-label="Volver al perfil" data-i18n-aria-label="supportBack" data-i18n="supportBack">← Volver</a>
          <span class="eyebrow" data-i18n="supportEyebrow">Centro de soporte</span>
        </div>
        <h1 data-i18n="supportTitle">Estamos para ayudarte</h1>
        <p class="lead" data-i18n="supportLead">
          Encuentra respuestas rápidas, envía un reporte o contacta al equipo de soporte para resolver cualquier duda sobre el proyecto.
        </p>
      </header>

      <section class="grid">
        <article class="card">
          <h2 data-i18n="supportFaqTitle">Preguntas frecuentes</h2>
          <p data-i18n="supportFaqText">Respuestas rápidas a las dudas más comunes.</p>
          <a class="btn faq-link" href="faq.php">Enviar una pregunta</a>
          <div class="faq-list">
            <div class="faq-item">
              <strong class="supporttitle" data-i18n="supportFaq1Question">¿Cómo accedo al panel?</strong>
              <span data-i18n="supportFaq1Answer">Inicia sesión con tus credenciales de usuario.</span>
            </div>
            <div class="faq-item">
              <strong class="supporttitle" data-i18n="supportFaq2Question">¿Puedo cambiar la configuración?</strong>
              <span data-i18n="supportFaq2Answer">Sí, desde la sección de configuración del sitio.</span>
            </div>
            <div class="faq-item">
              <strong class="supporttitle" data-i18n="supportFaq3Question">¿Cómo reporto un error?</strong>
              <span data-i18n="supportFaq3Answer">Usa el formulario de reportes en esta misma página.</span>
            </div>
          </div>
        </article>

        <article class="card support-admin-replies" id="respuestas-administradores">
          <h2>Respuestas de administradores</h2>
          <p>Aquí puedes conversar con el equipo administrador sobre las preguntas que enviaste.</p>
          <?php if (!$soporteUsuario): ?>
            <div class="support-login-prompt">
              <p>Inicia sesión para consultar tus conversaciones con los administradores.</p>
              <a class="btn" href="login.php">Iniciar sesión</a>
            </div>
          <?php elseif (!$soportePreguntas): ?>
            <p class="support-replies-empty">Todavía no enviaste preguntas al equipo de soporte.</p>
          <?php else: ?>
            <div class="support-replies-list">
              <?php foreach ($soportePreguntas as $preguntaSoporte): ?>
                <article class="support-reply">
                  <div class="support-reply__header">
                    <strong>Conversación de soporte</strong>
                    <div class="support-reply__tools">
                      <span class="support-reply__status <?= $preguntaSoporte['estado'] === 'respondida' ? 'support-reply__status--answered' : '' ?>">
                        <?= $preguntaSoporte['estado'] === 'respondida' ? 'Respondida' : 'Pendiente' ?>
                      </span>
                      <form method="post" class="support-reply__action-form" onsubmit="return confirm('¿Vaciar los mensajes de este chat? La pregunta original se conservará y quedará pendiente.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $preguntaSoporte['id'] ?>">
                        <button class="btn btn-secondary support-reply__action" type="submit" name="accion_soporte" value="vaciar">Vaciar chat</button>
                      </form>
                      <form method="post" class="support-reply__action-form" onsubmit="return confirm('¿Eliminar esta conversación completa? Se borrarán la pregunta y todos sus mensajes.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $preguntaSoporte['id'] ?>">
                        <button class="btn support-reply__action support-reply__action--delete" type="submit" name="accion_soporte" value="eliminar">Eliminar chat</button>
                      </form>
                    </div>
                  </div>
                  <button class="btn btn-secondary support-chat-toggle" type="button"
                    aria-expanded="false" aria-controls="support-chat-<?= (int) $preguntaSoporte['id'] ?>"
                    data-support-chat-toggle>Ver chat</button>
                  <div class="support-chat-content" id="support-chat-<?= (int) $preguntaSoporte['id'] ?>" hidden>
                    <div class="support-conversation">
                    <?php if (empty($preguntaSoporte['pregunta_oculta'])): ?>
                    <div class="support-conversation__message support-conversation__message--turista" data-message-item data-message-type="soporte_pregunta" data-message-id="<?= (int) $preguntaSoporte['id'] ?>" data-can-edit="<?= empty($preguntaSoporte['pregunta_eliminada']) ? '1' : '0' ?>" data-can-delete="1">
                      <strong>Tu pregunta</strong>
                      <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (empty($preguntaSoporte['pregunta_eliminada'])): ?><button type="button" data-message-action="edit">Editar</button><button type="button" data-message-action="delete-me">Eliminar para mí</button><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php else: ?><button type="button" data-message-action="delete-me">Eliminar para mí</button><?php endif; ?></div></details>
                      <p data-message-text><?= empty($preguntaSoporte['pregunta_eliminada']) ? nl2br(htmlspecialchars($preguntaSoporte['pregunta'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado' ?></p>
                      <small><?= htmlspecialchars($preguntaSoporte['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                    </div>
                    <?php endif; ?>
                    <?php foreach ($preguntaSoporte['mensajes'] as $mensajeSoporte): ?>
                    <?php $esMensajePropioSoporte = (int) ($mensajeSoporte['autor_id'] ?? 0) === (int) $soporteUsuario['id'];
                    $puedeEditarMensajeSoporte = $esMensajePropioSoporte && empty($mensajeSoporte['eliminado']);
                    $mensajeSoporteEliminado = !empty($mensajeSoporte['eliminado']);
                    $puedeEliminarMiSoporte = $esMensajePropioSoporte || $mensajeSoporteEliminado;
                    ?>
                    <div class="support-conversation__message <?= in_array($mensajeSoporte['autor_rol'], ['administrador', 'organizador'], true) ? 'support-conversation__message--admin' : 'support-conversation__message--turista' ?>" data-message-item data-message-type="soporte" data-message-id="<?= (int) $mensajeSoporte['id'] ?>" data-can-edit="<?= $puedeEditarMensajeSoporte ? '1' : '0' ?>" data-can-delete="<?= $esMensajePropioSoporte ? '1' : '0' ?>">
                      <strong><?= in_array($mensajeSoporte['autor_rol'], ['administrador', 'organizador'], true) ? htmlspecialchars(ucfirst($mensajeSoporte['autor_rol']), ENT_QUOTES, 'UTF-8') : 'Tú' ?></strong>
                      <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (!$mensajeSoporteEliminado && $puedeEditarMensajeSoporte): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $puedeEliminarMiSoporte ? '' : ' disabled' ?>>Eliminar para mí</button><?php if (!$mensajeSoporteEliminado && $esMensajePropioSoporte): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                      <p data-message-text><?= !$mensajeSoporteEliminado ? nl2br(htmlspecialchars($mensajeSoporte['mensaje'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado para todos' ?></p>
                        <small><?= htmlspecialchars($mensajeSoporte['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                      </div>
                    <?php endforeach; ?>
                    </div>
                    <?php $ultimoMensajeSoporte = $preguntaSoporte['mensajes'] ? end($preguntaSoporte['mensajes']) : null; ?>
                    <?php if ($ultimoMensajeSoporte && in_array($ultimoMensajeSoporte['autor_rol'], ['administrador', 'organizador'], true)): ?>
                      <form class="support-reply-form" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $preguntaSoporte['id'] ?>">
                        <label for="respuesta-soporte-<?= (int) $preguntaSoporte['id'] ?>">Responder al administrador</label>
                        <textarea id="respuesta-soporte-<?= (int) $preguntaSoporte['id'] ?>" name="respuesta" maxlength="4000" rows="3" required placeholder="Escribe tu respuesta..."></textarea>
                        <button class="btn" type="submit">Enviar respuesta</button>
                      </form>
                    <?php elseif ($preguntaSoporte['estado'] === 'pendiente'): ?>
                      <p class="support-reply-pending">Tu mensaje está pendiente de respuesta del equipo administrador.</p>
                    <?php endif; ?>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>

        <article class="card support-admin-replies" id="chat-organizadores">
          <h2>Chat con organizadores</h2>
          <p>Aquí puedes consultar y responder los mensajes relacionados con tus preguntas sobre eventos.</p>
          <?php if (!$soporteUsuario): ?>
            <div class="support-login-prompt">
              <p>Inicia sesión para consultar tus conversaciones con los organizadores.</p>
              <a class="btn" href="login.php">Iniciar sesión</a>
            </div>
          <?php elseif (!$consultasEventos): ?>
            <p class="support-replies-empty">Todavía no tienes consultas de eventos.</p>
          <?php else: ?>
            <div class="support-replies-list">
              <?php foreach ($consultasEventos as $consultaEvento): ?>
                <article class="support-reply">
                  <div class="support-reply__header">
                    <strong><?= htmlspecialchars($consultaEvento['titulo'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div class="support-reply__tools">
                      <form method="post" class="support-reply__action-form" onsubmit="return confirm('¿Vaciar los mensajes de este chat? La pregunta original se conservará.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $consultaEvento['id'] ?>">
                        <button class="btn btn-secondary support-reply__action" type="submit" name="accion_consulta_evento" value="vaciar">Vaciar chat</button>
                      </form>
                      <form method="post" class="support-reply__action-form" onsubmit="return confirm('¿Eliminar esta conversación completa? Se borrarán la pregunta y todos sus mensajes.');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="pregunta_id" value="<?= (int) $consultaEvento['id'] ?>">
                        <button class="btn support-reply__action support-reply__action--delete" type="submit" name="accion_consulta_evento" value="eliminar">Eliminar chat</button>
                      </form>
                    </div>
                  </div>
                  <button class="btn btn-secondary support-chat-toggle" type="button"
                    aria-expanded="false" aria-controls="organizer-chat-<?= (int) $consultaEvento['id'] ?>"
                    data-support-chat-toggle>Ver chat</button>
                  <div class="support-chat-content" id="organizer-chat-<?= (int) $consultaEvento['id'] ?>" hidden>
                    <div class="support-conversation" aria-label="Conversación de la consulta">
                      <?php foreach ($mensajesPorConsultaEvento[(int) $consultaEvento['id']] ?? [] as $mensajeEvento):
                        $esMensajePropio = (int) ($mensajeEvento['autor_id'] ?? 0) === (int) $soporteUsuario['id'];
                        $puedeEditarMensajeEvento = $esMensajePropio && empty($mensajeEvento['eliminado']);
                        $mensajeEventoEliminado = !empty($mensajeEvento['eliminado']);
                        $puedeEliminarMiEvento = $esMensajePropio || $mensajeEventoEliminado;
                        $nombreAutor = $esMensajePropio ? 'Tú' : ($mensajeEvento['autor_rol'] === 'administrador' ? 'Administración' : 'Organizador'); ?>
                        <div class="support-conversation__message <?= $esMensajePropio ? 'support-conversation__message--turista' : 'support-conversation__message--admin' ?>" data-message-item data-message-type="evento" data-message-id="<?= (int) $mensajeEvento['id'] ?>" data-can-edit="<?= $puedeEditarMensajeEvento ? '1' : '0' ?>" data-can-delete="1">
                          <strong><?= $nombreAutor ?></strong>
                          <details class="message-actions"><summary aria-label="Opciones del mensaje">⋮</summary><div class="message-actions__menu"><?php if (!$mensajeEventoEliminado && $puedeEditarMensajeEvento): ?><button type="button" data-message-action="edit">Editar</button><?php endif; ?><button type="button" data-message-action="delete-me"<?= $puedeEliminarMiEvento ? '' : ' disabled' ?>>Eliminar para mí</button><?php if (!$mensajeEventoEliminado && $esMensajePropio): ?><button type="button" data-message-action="delete-everyone">Eliminar para todos</button><?php endif; ?></div></details>
                          <p data-message-text><?= !$mensajeEventoEliminado ? nl2br(htmlspecialchars($mensajeEvento['mensaje'], ENT_QUOTES, 'UTF-8')) : 'Mensaje eliminado para todos' ?></p>
                          <small><?= htmlspecialchars($mensajeEvento['fecha_creacion'], ENT_QUOTES, 'UTF-8') ?></small>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <form class="support-reply-form" method="post" action="api/responder_consulta_evento.php">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($soporteCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="accion" value="mensaje">
                      <input type="hidden" name="pregunta_id" value="<?= (int) $consultaEvento['id'] ?>">
                      <label for="mensaje-consulta-<?= (int) $consultaEvento['id'] ?>">Tu mensaje</label>
                      <textarea id="mensaje-consulta-<?= (int) $consultaEvento['id'] ?>" name="mensaje" maxlength="2000" required></textarea>
                      <button class="btn btn-primary" type="submit">Enviar mensaje</button>
                    </form>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>

        <article class="card">
          <h2 data-i18n="supportContactTitle">Contacto</h2>
          <p data-i18n="supportContactText">Opciones para comunicarte con soporte.</p>
          <div class="contact-list">
            <div class="contact-item" data-i18n="supportContactEmail">Correo: soporte@sigtur.com</div>
            <div class="contact-item"><a href="https://wa.me/59891661335" target="_blank" rel="noopener noreferrer" data-i18n="supportContactWhatsapp">WhatsApp: 091 661 335</a></div>
            <div class="contact-item"><a href="https://instagram.com/bapstuy" target="_blank" rel="noopener noreferrer" data-i18n="supportContactInstagram">Instagram: @bapstuy</a></div>
            <div class="contact-item" data-i18n="supportContactHours">Horario: Lunes a Viernes, 9:00 a 18:00</div>
          </div>
        </article>

        <article class="card">
          <h2 data-i18n="supportHelpTitle">Ayuda rápida</h2>
          <p data-i18n="supportHelpText">Guías breves para resolver tareas simples.</p>
          <div class="help-list">
            <div class="help-item">
              <strong class="supporttitle" data-i18n="supportHelp1Title">Actualizar datos</strong>
              <span data-i18n="supportHelp1Text">Ve a configuración y guarda los cambios.</span>
            </div>
            <div class="help-item">
              <strong class="supporttitle" data-i18n="supportHelp2Title">Revisar eventos</strong>
              <span data-i18n="supportHelp2Text">Consulta la sección de eventos y filtros disponibles.</span>
            </div>
            <div class="help-item">
              <strong class="supporttitle" data-i18n="supportHelp3Title">Solucionar acceso</strong>
              <span data-i18n="supportHelp3Text">Verifica tu usuario, contraseña y conexión.</span>
            </div>
          </div>
        </article>
      </section>
    </section>
  </main>
  <script src="../js/translator.js"></script>
  <script src="../js/script.js"></script>
  <script src="../js/soporte-chat.js"></script>
  <script src="../js/mensajes-acciones.js"></script>
</body>
</html>
