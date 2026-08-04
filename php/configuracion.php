<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Configuración | Sigtur Salto</title>
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
          <a class="back-pill" href="index.php" aria-label="Volver al inicio" data-i18n-aria-label="configBack" data-i18n="configBack">← Volver</a>
          <span class="eyebrow" data-i18n="configEyebrow">⚙️ Configuración</span>
        </div> 
        <h1 data-i18n="configTitle">Personaliza la experiencia de tu sitio</h1>
        <p data-i18n="configDescription">
          Aquí tienes una base visual y estructural para administrar los datos del
          sitio, las preferencias de usuario y las opciones de apariencia.
        </p>
      </header>
      

      <section class="grid">
        <article class="card">
          <h2 data-i18n="configGeneralTitle">Datos generales</h2>
          <p data-i18n="configGeneralText">Define la identidad y la información principal de la página.</p>

          <label for="nombre-sitio" data-i18n="configLabelSiteName">Nombre del sitio</label>
          <input id="nombre-sitio" class="config-input" type="text" value="Sigtur Salto" />

          <label for="email-admin" data-i18n="configLabelEmail">Correo de contacto</label>
          <input id="email-admin" class="config-input" type="email" value="contacto@sigtur.com" />

          <label for="ciudad" data-i18n="configLabelCity">Ciudad</label>
          <input id="ciudad" class="config-input" type="text" value="Salto, Uruguay" />
        </article>

        <article class="card">
          <h2 data-i18n="configVisualTitle">Preferencias de visualización</h2>
          <p data-i18n="configVisualText">Controla la forma en que se presenta la información al usuario.</p>

          <label for="tema" data-i18n="configLabelTheme">Tema</label>
          <select id="tema" class="config-select">
            <option value="Oscuro" data-i18n="themeDark">Oscuro</option>
            <option value="Claro" data-i18n="themeLight">Claro</option>
          </select>

          <label for="idioma" data-i18n="configLabelLanguage">Idioma</label>
          <select id="idioma" class="config-select">
            <option value="es" data-i18n="languageOptionEs">Español</option>
            <option value="en" data-i18n="languageOptionEn">English</option>
            <option value="pt" data-i18n="languageOptionPt">Português</option>
            <option value="fr" data-i18n="languageOptionFr">Français</option>
            <option value="it" data-i18n="languageOptionIt">Italiano</option>
            <option value="de" data-i18n="languageOptionDe">Deutsch</option>
            <option value="ja" data-i18n="languageOptionJa">日本語</option>
            <option value="zh" data-i18n="languageOptionZh">中文</option>
            <option value="ar" data-i18n="languageOptionAr">العربية</option>
            <option value="ru" data-i18n="languageOptionRu">Русский</option>
          </select>

          <label for="descripcion" data-i18n="configLabelDescription">Descripción breve</label>
          <textarea id="descripcion" class="config-textarea">Explora eventos, turismo y recomendaciones de Salto en una experiencia moderna e intuitiva.</textarea>
        </article>

        <article class="card">
          <h2 data-i18n="configNotificationsTitle">Notificaciones</h2>
          <p data-i18n="configNotificationsText">Activa o desactiva los avisos importantes del proyecto.</p>

          <div class="config-row">
            <div class="config-label">
              <strong data-i18n="configNotificationEventsTitle">Eventos destacados</strong>
              <span data-i18n="configNotificationEventsText">Recibe alertas de actividades nuevas o próximas.</span>
            </div>
            <span class="config-chip">Activado</span>
          </div>
          <div class="config-row">
            <div class="config-label">
              <strong data-i18n="configNotificationRemindersTitle">Recordatorios</strong>
              <span data-i18n="configNotificationRemindersText">Envío de avisos de actualización o mantenimiento.</span>
            </div>
            <span class="config-chip">Opcional</span>
          </div>
        </article>

        <article class="card">
          <h2 data-i18n="configSecurityTitle">Seguridad y acceso</h2>
          <p data-i18n="configSecurityText">Gestiona los accesos y el control de la plataforma.</p>

          <div class="config-row">
            <div class="config-label">
              <strong data-i18n="configAccessPublicTitle">Acceso público</strong>
              <span data-i18n="configAccessPublicText">Permite ver el contenido sin iniciar sesión.</span>
            </div>
            <span class="config-chip">Sí</span>
          </div>

          <div class="config-row">
            <div class="config-label">
              <strong data-i18n="configAdminPanelTitle">Panel de administración</strong>
              <span data-i18n="configAdminPanelText">Acceso privado para editar contenido y configuraciones.</span>
            </div>
            <span class="config-chip">Protegido</span>
          </div>
        </article>
      </section>

      <div class="config-actions">
        <button class="btn btn-primary" type="button" data-i18n="configSave">Guardar cambios</button>
        <button class="btn btn-secondary" type="button" data-i18n="configReset">Restablecer</button>
      </div>
    </section>
  </main>
  <script src="../js/script.js"></script>
  <script>
    const selectorTema = document.getElementById('tema');
    const selectorIdioma = document.getElementById('idioma');
    const botonGuardar = document.querySelector('.btn-primary');

    const aplicarTemaDesdeConfiguracion = (oscuro) => {
      guardarTema(oscuro);
      aplicarTema(oscuro, null);
      if (selectorTema) {
        selectorTema.value = oscuro ? 'Oscuro' : 'Claro';
      }
    };

    if (selectorTema) {
      selectorTema.addEventListener('change', () => {
        aplicarTemaDesdeConfiguracion(selectorTema.value === 'Oscuro');
      });
    }

    if (selectorIdioma) {
      selectorIdioma.addEventListener('change', () => {
        guardarIdioma(selectorIdioma.value);
        aplicarIdioma(selectorIdioma.value);
      });
    }

    if (botonGuardar) {
      botonGuardar.addEventListener('click', () => {
        aplicarTemaDesdeConfiguracion(selectorTema && selectorTema.value === 'Oscuro');
        if (selectorIdioma) {
          guardarIdioma(selectorIdioma.value);
          aplicarIdioma(selectorIdioma.value);
        }
      });
    }

    aplicarTemaDesdeConfiguracion(leerTema());
    aplicarIdioma(leerIdioma());
  </script>
</body>
</html>
