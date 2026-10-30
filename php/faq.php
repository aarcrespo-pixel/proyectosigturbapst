<?php
require_once __DIR__ . '/helpers/autorizacion.php';
sigtur_iniciar_sesion_segura();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas frecuentes | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/faq.css?v=20261027-3">
    <script src="../js/volver-posicion.js" defer></script>
</head>
<body class="faq-page">
<main class="faq-container">
    <div class="faq-page-header">
        <a class="faq-back" href="soporte.php">← Volver a soporte</a>
    </div>
    <section class="faq-hero">
        <p class="faq-kicker">Centro de ayuda</p>
        <h1>Preguntas frecuentes</h1>
        <p>¿No encuentras la respuesta? Envía tu consulta y nuestro equipo la revisará.</p>
    </section>

    <section class="faq-card" aria-labelledby="faq-form-title">
        <h2 id="faq-form-title">Envía tu pregunta</h2>
        <form id="faq-form" class="faq-form" novalidate>
            <label for="faq-pregunta">Pregunta</label>
            <textarea id="faq-pregunta" name="pregunta" rows="5" minlength="5" maxlength="1000" placeholder="Escribe aquí tu pregunta..." required></textarea>
            <input type="hidden" name="submission_key" value="<?= htmlspecialchars(bin2hex(random_bytes(16)), ENT_QUOTES, 'UTF-8') ?>">
            <?php if (empty($_SESSION['usuario_id'])): ?>
                <label for="faq-email">Email para recibir respuesta</label>
                <input id="faq-email" type="email" name="email" placeholder="tu@email.com" required>
            <?php endif; ?>
            <button id="faq-submit" class="faq-submit" type="submit"><span class="faq-submit-label">Enviar pregunta</span></button>
            <p id="faq-feedback" class="faq-feedback" role="status" aria-live="polite"></p>
        </form>
    </section>
</main>
<div id="faq-toast" class="faq-toast" role="status" aria-live="polite" hidden></div>
<script src="../js/tema.js" defer></script>
<script src="../js/translator.js"></script>
<script src="../js/faq.js?v=20261027-3" defer></script>
</body>
</html>