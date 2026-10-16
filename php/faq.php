<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas frecuentes | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <link rel="stylesheet" href="../css/faq.css">
</head>
<body class="faq-page">
<main class="faq-container">
    <section class="faq-hero">
        <p class="faq-kicker">Centro de ayuda</p>
        <h1>Preguntas frecuentes</h1>
        <p>¿No encuentras la respuesta? Envía tu consulta y nuestro equipo la revisará.</p>
    </section>

    <section class="faq-card" aria-labelledby="faq-form-title">
        <h2 id="faq-form-title">Envía tu pregunta</h2>
        <?php if (empty($_SESSION['usuario_id'])): ?>
            <p class="faq-login-required">Inicia sesión para enviar tu pregunta. El equipo te responderá en la página de Soporte, en la sección “Respuestas de administradores”.</p>
            <a class="faq-submit" href="login.php">Iniciar sesión</a>
        <?php else: ?>
            <form id="faq-form" class="faq-form" novalidate>
                <label for="faq-pregunta">Pregunta</label>
                <textarea id="faq-pregunta" name="pregunta" rows="5" minlength="5" maxlength="1000" placeholder="Escribe aquí tu pregunta..." required></textarea>
                <button id="faq-submit" class="faq-submit" type="submit"><span class="faq-submit-label">Enviar pregunta</span></button>
                <p id="faq-feedback" class="faq-feedback" role="status" aria-live="polite"></p>
            </form>
        <?php endif; ?>
    </section>
</main>
<div id="faq-toast" class="faq-toast" role="status" aria-live="polite" hidden></div>
<script src="../js/translator.js"></script>
<script src="../js/faq.js?v=20261013-1" defer></script>
</body>
</html>
