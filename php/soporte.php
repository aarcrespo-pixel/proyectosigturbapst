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
          <a class="back-pill" href="index.php" aria-label="Volver al inicio" data-i18n-aria-label="supportBack" data-i18n="supportBack">← Volver</a>
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
          <div class="faq-list">
            <div class="faq-item">
              <strong data-i18n="supportFaq1Question">¿Cómo accedo al panel?</strong>
              <span data-i18n="supportFaq1Answer">Inicia sesión con tus credenciales de usuario.</span>
            </div>
            <div class="faq-item">
              <strong data-i18n="supportFaq2Question">¿Puedo cambiar la configuración?</strong>
              <span data-i18n="supportFaq2Answer">Sí, desde la sección de configuración del sitio.</span>
            </div>
            <div class="faq-item">
              <strong data-i18n="supportFaq3Question">¿Cómo reporto un error?</strong>
              <span data-i18n="supportFaq3Answer">Usa el formulario de reportes en esta misma página.</span>
            </div>
          </div>
        </article>

        <article class="card">
          <h2 data-i18n="supportReportTitle">Reportes</h2>
          <p data-i18n="supportReportText">Describe el problema para que el equipo lo revise.</p>
          <div class="report-box">
            <input type="text" placeholder="Asunto del reporte" data-i18n-placeholder="supportReportSubject" />
            <input type="email" placeholder="Tu correo" data-i18n-placeholder="supportReportEmail" />
            <textarea placeholder="Describe el problema o la incidencia..." data-i18n-placeholder="supportReportTextarea"></textarea>
            <button class="btn" type="button" data-i18n="supportReportButton">Enviar reporte</button>
          </div>
        </article>

        <article class="card">
          <h2 data-i18n="supportContactTitle">Contacto</h2>
          <p data-i18n="supportContactText">Opciones para comunicarte con soporte.</p>
          <div class="contact-list">
            <div class="contact-item" data-i18n="supportContactEmail">Correo: soporte@sigtur.com</div>
            <div class="contact-item" data-i18n="supportContactWhatsapp">WhatsApp: +598 99 999 999</div>
            <div class="contact-item" data-i18n="supportContactHours">Horario: Lunes a Viernes, 9:00 a 18:00</div>
          </div>
        </article>

        <article class="card">
          <h2 data-i18n="supportHelpTitle">Ayuda rápida</h2>
          <p data-i18n="supportHelpText">Guías breves para resolver tareas simples.</p>
          <div class="help-list">
            <div class="help-item">
              <strong data-i18n="supportHelp1Title">Actualizar datos</strong>
              <span data-i18n="supportHelp1Text">Ve a configuración y guarda los cambios.</span>
            </div>
            <div class="help-item">
              <strong data-i18n="supportHelp2Title">Revisar eventos</strong>
              <span data-i18n="supportHelp2Text">Consulta la sección de eventos y filtros disponibles.</span>
            </div>
            <div class="help-item">
              <strong data-i18n="supportHelp3Title">Solucionar acceso</strong>
              <span data-i18n="supportHelp3Text">Verifica tu usuario, contraseña y conexión.</span>
            </div>
          </div>
        </article>
      </section>
    </section>
  </main>
  <script src="../js/script.js"></script>
</body>
</html>
