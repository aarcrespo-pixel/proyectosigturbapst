<?php
$mensajesWidgetRoot = $mensajesWidgetRoot ?? '';
$mensajesWidgetUserId = (int) ($_SESSION['usuario_id'] ?? 0);
if ($mensajesWidgetUserId <= 0) {
    return;
}
?>
<link rel="stylesheet" href="<?= htmlspecialchars($mensajesWidgetRoot, ENT_QUOTES, 'UTF-8') ?>css/mensajes.css">
<div class="mensajes-widget" id="mensajesWidget" data-user-id="<?= $mensajesWidgetUserId ?>" data-api="<?= htmlspecialchars($mensajesWidgetRoot, ENT_QUOTES, 'UTF-8') ?>php/api/mensajes.php" data-avatar-fallback="<?= htmlspecialchars($mensajesWidgetRoot, ENT_QUOTES, 'UTF-8') ?>img/user.png" hidden>
    <button class="mensajes-widget__toggle" type="button" aria-expanded="false" aria-controls="mensajesPanel">
        <span aria-hidden="true">&#128172;</span><span>Mensajes</span><b class="mensajes-widget__badge" hidden>0</b>
    </button>
    <section class="mensajes-widget__panel" id="mensajesPanel" aria-label="Mensajes directos">
        <header class="mensajes-widget__header"><strong>Mensajes</strong><button type="button" class="mensajes-widget__close" aria-label="Cerrar">&times;</button></header>
        <div class="mensajes-widget__body">
            <aside class="mensajes-widget__conversaciones" aria-label="Conversaciones"><p class="mensajes-widget__empty">Cargando conversaciones...</p></aside>
            <div class="mensajes-widget__chat chat-panel-derecho" aria-live="polite"><p class="mensajes-widget__empty">Elegí una conversación</p></div>
        </div>
    </section>
</div>
<script src="<?= htmlspecialchars($mensajesWidgetRoot, ENT_QUOTES, 'UTF-8') ?>js/mensajes.js?v=20260914-2" defer></script>
