<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/notificaciones.php';

$usuarioActual = sigtur_requerir_rol($pdo, ['turista'], 'login.php', 'eventos.php');
$usuarioId = (int) $usuarioActual['id'];
$csrfToken = sigtur_csrf_token();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!sigtur_validar_csrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
        http_response_code(403);
        exit('La sesión expiró. Recargá la página e intentá nuevamente.');
    }

    $accion = (string) ($_POST['accion'] ?? '');
    if (!in_array($accion, ['leer', 'eliminar', 'marcar_todas'], true)) {
        http_response_code(400);
        exit('La acción solicitada no es válida.');
    }

    if ($accion === 'marcar_todas') {
        $marcarTodas = $pdo->prepare("UPDATE notificaciones SET leida = 1 WHERE usuario_id = :usuario_id AND tipo = 'aviso' AND leida = 0");
        $marcarTodas->execute([':usuario_id' => $usuarioId]);
        header('Location: notificaciones.php?leidos=1');
        exit;
    }

    $notificacionId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$notificacionId) {
        http_response_code(422);
        exit('El aviso seleccionado no es válido.');
    }

    $verificarAviso = $pdo->prepare("SELECT 1 FROM notificaciones
        WHERE id = :id AND usuario_id = :usuario_id AND tipo = 'aviso'
        LIMIT 1");
    $verificarAviso->execute([
        ':id' => $notificacionId,
        ':usuario_id' => $usuarioId,
    ]);
    if (!$verificarAviso->fetchColumn()) {
        http_response_code(404);
        exit('No se encontró el aviso solicitado.');
    }

    if ($accion === 'leer') {
        $actualizar = $pdo->prepare("UPDATE notificaciones SET leida = 1 WHERE id = :id AND usuario_id = :usuario_id AND tipo = 'aviso' LIMIT 1");
        $actualizar->execute([
            ':id' => $notificacionId,
            ':usuario_id' => $usuarioId,
        ]);
        header('Location: notificaciones.php?leido=1');
        exit;
    }

    $eliminar = $pdo->prepare("DELETE FROM notificaciones WHERE id = :id AND usuario_id = :usuario_id AND tipo = 'aviso' LIMIT 1");
    $eliminar->execute([
        ':id' => $notificacionId,
        ':usuario_id' => $usuarioId,
    ]);
    header('Location: notificaciones.php?eliminado=1');
    exit;
}

$notificaciones = sigtur_listar_notificaciones($pdo, $usuarioId, 50, false, 'aviso');
$sinLeer = sigtur_contar_notificaciones_no_leidas($pdo, $usuarioId, false, 'aviso');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        body { background: #f5f7fb; color: #1b2430; }
        .notificaciones-page { max-width: 980px; margin: 0 auto; padding: 40px 20px 80px; }
        .notificaciones-page__header { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
        .notificaciones-page__header h1 { margin: 0; font-size: clamp(2rem, 2.8vw, 3rem); }
        .notificaciones-page__actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .notificaciones-page__button { border: none; border-radius: 999px; padding: 10px 16px; background: #162b3d; color: #fff; cursor: pointer; font-weight: 700; }
        .notificaciones-page__button--secondary { display: inline-block; background: #eaedf2; color: #1b2430; text-decoration: none; }
        .notificaciones-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 16px; }
        .notificacion-item { background: #fff; border: 1px solid #e5eaf0; border-radius: 18px; padding: 18px 20px; box-shadow: 0 8px 18px rgba(18, 24, 33, 0.05); }
        .notificacion-item--unread { border-color: rgba(34, 124, 201, 0.45); background: linear-gradient(180deg, #f2f8ff 0%, #fff 100%); }
        .notificacion-item__top { display: flex; justify-content: space-between; gap: 12px; align-items: center; }
        .notificacion-item__type { display: inline-flex; padding: 5px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.03em; text-transform: uppercase; background: #ebf5ff; color: #145ca3; }
        .notificacion-item__time { color: #617585; font-size: 0.8rem; }
        .notificacion-item h2 { margin: 12px 0 8px; font-size: 1.2rem; }
        .notificacion-item p { margin: 0; line-height: 1.6; color: #2b3d4f; }
        .notificacion-item__footer { margin-top: 12px; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .notificacion-item__actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .notificaciones-page__button--delete { background: #b42318; }
        .notificacion-item__status { font-size: 0.8rem; font-weight: 700; color: #4a667b; }
        .notificacion-item__status--read { color: #72839a; }
        .notificacion-empty { background: rgba(255,255,255,0.72); border: 1px dashed #d7dde5; border-radius: 18px; padding: 22px; color: #56677a; }
        body[data-tema="oscuro"] { background: #151a1f; color: #edf2f5; color-scheme: dark; }
        body[data-tema="oscuro"] .notificacion-item,
        body[data-tema="oscuro"] .notificacion-empty { border-color: #3b454d; background: #20262c; color: #edf2f5; }
        body[data-tema="oscuro"] .notificacion-item--unread { background: linear-gradient(180deg, #202e3b, #20262c); }
        body[data-tema="oscuro"] .notificacion-item p,
        body[data-tema="oscuro"] .notificacion-item__time,
        body[data-tema="oscuro"] .notificacion-item__status,
        body[data-tema="oscuro"] .notificacion-empty { color: #b5c0c7; }
        body[data-tema="oscuro"] .notificacion-item__type { background: #25394a; color: #a9d6ff; }
        body[data-tema="oscuro"] .notificaciones-page__button--secondary { background: #303941; color: #edf2f5; }
        @media (max-width: 700px) { .notificaciones-page__header { flex-direction: column; align-items: flex-start; } }
    </style>
    <script src="../js/volver-posicion.js" defer></script>
</head>
<body>
<main class="notificaciones-page">
    <div class="notificaciones-page__header">
        <div>
            <p class="muted">SIGTUR · Avisos de administradores</p>
            <h1>Avisos de administradores</h1>
        </div>
        <div class="notificaciones-page__actions">
            <?php if ($sinLeer > 0): ?>
                <form method="post" action="notificaciones.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="accion" value="marcar_todas">
                    <button type="submit" class="notificaciones-page__button notificaciones-page__button--secondary">Marcar todas como leídas</button>
                </form>
            <?php endif; ?>
            <a href="../index.php" class="notificaciones-page__button notificaciones-page__button--secondary">Volver al inicio</a>
        </div>
    </div>

    <?php if (($_GET['eliminado'] ?? '') === '1'): ?>
        <p role="status">Aviso eliminado.</p>
    <?php endif; ?>
    <?php if (($_GET['leido'] ?? '') === '1'): ?>
        <p role="status">Aviso marcado como leído.</p>
    <?php endif; ?>
    <?php if (($_GET['leidos'] ?? '') === '1'): ?>
        <p role="status">Todos los avisos fueron marcados como leídos.</p>
    <?php endif; ?>

    <?php if (empty($notificaciones)): ?>
        <div class="notificacion-empty">Todavía no tenés notificaciones. Cuando haya un aviso importante, aparecerá acá.</div>
    <?php else: ?>
        <ul class="notificaciones-list">
            <?php foreach ($notificaciones as $notificacion): ?>
                <li class="notificacion-item <?= (int) $notificacion['leida'] === 0 ? 'notificacion-item--unread' : '' ?>">
                    <div class="notificacion-item__top">
                        <span class="notificacion-item__type"><?= htmlspecialchars($notificacion['tipo'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="notificacion-item__time"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $notificacion['fecha_creacion'])), ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <h2><?= htmlspecialchars($notificacion['titulo'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?= nl2br(htmlspecialchars($notificacion['mensaje'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <div class="notificacion-item__footer">
                        <span class="notificacion-item__status <?= (int) $notificacion['leida'] === 0 ? '' : 'notificacion-item__status--read' ?>"><?= (int) $notificacion['leida'] === 0 ? 'No leída' : 'Leída' ?></span>
                        <div class="notificacion-item__actions">
                        <?php if ((int) $notificacion['leida'] === 0): ?>
                            <form method="post" action="notificaciones.php">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="accion" value="leer">
                                <input type="hidden" name="id" value="<?= (int) $notificacion['id'] ?>">
                                <button type="submit" class="notificaciones-page__button">Marcar como leída</button>
                            </form>
                        <?php endif; ?>
                            <form method="post" action="notificaciones.php" onsubmit="return confirm('¿Eliminar este aviso?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int) $notificacion['id'] ?>">
                                <button type="submit" class="notificaciones-page__button notificaciones-page__button--delete">Eliminar aviso</button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>
<script src="../js/translator.js" defer></script>
</body>
</html>
