<?php
session_start();
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';

$configUsuario = sigtur_usuario_actual($pdo);
$configBaseUrl = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/') . '/';
$configCsrfToken = sigtur_csrf_token();
$configUrlVolver = $configUsuario
    ? 'perfil.php?id=' . (int) $configUsuario['id']
    : '../index.php';
?>
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
  <link rel="stylesheet" href="../css/soporte.css?v=<?= time() ?>" />
  <script src="../js/volver-posicion.js" defer></script>
</head>
<body>
  <main class="support-page">
    <section class="support-container">
      <header class="hero">
        <div class="hero-top">
          <a class="back-pill" href="<?= htmlspecialchars($configUrlVolver, ENT_QUOTES, 'UTF-8') ?>" aria-label="Volver al perfil" data-config-exit data-i18n-aria-label="configBack" data-i18n="configBack">← Volver</a>
          <span class="eyebrow" data-i18n="configEyebrow">⚙️ Configuración</span>
        </div> 
        <h1 data-i18n="configTitle">Personaliza tu experiencia en la página</h1>
        <p data-i18n="configDescription">
          Aquí tienes la seccion en donde podras configurar las preferencias de usuario y las opciones de apariencia a tu gusto.
        </p>
      </header>
      <section class="grid">
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
          </select>

          <label for="tamano-letra">Tamaño de letra</label>
          <select id="tamano-letra" class="config-select">
            <option value="pequeno">Pequeña</option>
            <option value="normal">Normal</option>
            <option value="grande">Grande</option>
            <option value="muyGrande">Muy grande</option>
          </select>

        </article>

        <article class="card">
          <h2 data-i18n="configNotificationsTitle">Notificaciones</h2>
          <p data-i18n="configNotificationsText">Activá los avisos del navegador y enterate cuando se publique un evento nuevo en SIGTUR.</p>

          <?php if ($configUsuario): ?>
            <div class="config-notificaciones" data-config-notificaciones>
              <div class="config-row config-notificaciones__summary">
                <div class="config-label">
                  <strong class="sub-title">Avisos del sitio</strong>
                  <span>Recibí novedades de eventos, respuestas y comunicados importantes.</span>
                </div>
                <span class="config-chip" id="notificaciones-estado" aria-live="polite">Desactivadas</span>
              </div>
              <p class="config-notificaciones__help" id="notificaciones-ayuda" aria-live="polite">Las notificaciones están desactivadas.</p>
              <div class="config-notificaciones__toolbar config-notificaciones__toolbar--right">
                <button
                  type="button"
                  class="btn btn-primary"
                  data-notificaciones-toggle
                  data-user-id="<?= (int) $configUsuario['id'] ?>"
                  data-api="<?= htmlspecialchars($configBaseUrl . 'php/api/notificaciones.php', ENT_QUOTES, 'UTF-8') ?>"
                  data-service-worker="<?= htmlspecialchars($configBaseUrl . 'sw.js', ENT_QUOTES, 'UTF-8') ?>"
                  data-csrf="<?= htmlspecialchars($configCsrfToken, ENT_QUOTES, 'UTF-8') ?>">
                  Notificaciones
                  <span class="config-notificaciones__badge" id="notificaciones-badge" hidden>0</span>
                </button>
                <button type="button" class="btn btn-secondary" data-notificaciones-mark-all>Marcar todas como leídas</button>
                <button type="button" class="btn btn-secondary" data-notificaciones-toggle-permission>Activar notificaciones</button>
              </div>

              <p class="config-notificaciones__action-status" data-notificaciones-action-status aria-live="polite"></p>
              <div class="config-notificaciones__panel" id="notificaciones-panel" hidden>
                <ul class="config-notificaciones__lista" id="notificaciones-lista">
                  <li class="config-notificaciones__empty">Cargando notificaciones...</li>
                </ul>
              </div>
            </div>
          <?php else: ?>
            <div class="config-notificaciones__login">
              <p>Iniciá sesión para activar las notificaciones y ver tus avisos personales.</p>
              <a class="btn btn-primary" href="login.php">Iniciar sesión</a>
            </div>
          <?php endif; ?>
        </article>

        <article class="card">
          <h2 data-i18n="configSecurityTitle">Seguridad y acceso</h2>
          <p data-i18n="configSecurityText">Gestiona los accesos y el control de la plataforma.</p>

          <div class="config-row">
            <div class="config-label">
              <strong class="sub-title" data-i18n="configAdminPanelTitle">Panel de administración</strong>
              <span data-i18n="configAdminPanelText">Acceso privado para editar contenido y configuraciones. Siempre protegido por permisos.</span>
            </div>
            <span class="config-chip">Protegido</span>
          </div>
        </article>
      </section>

      <div class="config-actions">
        <button class="btn btn-primary" type="button" data-i18n="configSave">Guardar cambios</button>
        <button class="btn btn-secondary" type="button" data-config-reset data-i18n="configReset">Restablecer</button>
        <a class="btn btn-secondary" href="../index.php" data-config-exit>Salir sin guardar</a>
      </div>
    </section>
  </main>
  <div class="config-exit-modal" data-config-exit-modal hidden>
    <div class="config-exit-modal__backdrop" data-config-exit-cancel></div>
    <section class="config-exit-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="config-exit-title" aria-describedby="config-exit-description">
      <h2 id="config-exit-title">¿Estás seguro de que quieres salir sin guardar los cambios?</h2>
      <p id="config-exit-description">Los cambios que hiciste se perderán.</p>
      <div class="config-exit-modal__actions">
        <button class="btn btn-secondary" type="button" data-config-exit-cancel>Quedarme</button>
        <button class="btn" type="button" data-config-exit-confirm>Salir de todos modos</button>
      </div>
    </section>
  </div>
  <script src="../js/translator.js?v=20261019-i18n-4"></script>
  <script src="../js/script.js?v=20261019-i18n-4"></script>
  <script src="../js/notificaciones.js?v=<?= time() ?>"></script>
  <script>
    /* Estos selectores conectan preferencias visuales, idioma y tamaño de
       fuente con las funciones compartidas del script global. */
    const selectorTema = document.getElementById('tema');
    const selectorIdioma = document.getElementById('idioma');
    const selectorTamanoLetra = document.getElementById('tamano-letra');
    const botonGuardar = document.querySelector('.config-actions .btn-primary');
    const botonRestablecer = document.querySelector('[data-config-reset]');
    const enlacesSalida = document.querySelectorAll('[data-config-exit]');
    const modalSalida = document.querySelector('[data-config-exit-modal]');
    const botonConfirmarSalida = document.querySelector('[data-config-exit-confirm]');
    const botonCancelarSalida = document.querySelector('[data-config-exit-cancel]');
    let destinoSalida = '../index.php';
    let elementoEnfocadoAntesDelModal = null;

    const aplicarTemaDesdeConfiguracion = (oscuro, persistir = false) => {
      if (persistir) guardarTema(oscuro);
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
        aplicarIdioma(selectorIdioma.value, false);
      });
    }

    if (selectorTamanoLetra) {
      selectorTamanoLetra.value = leerTamanoLetra();
      selectorTamanoLetra.addEventListener('change', () => {
        aplicarTamanoLetra(selectorTamanoLetra.value);
      });
    }

    if (botonGuardar) {
      botonGuardar.addEventListener('click', async () => {
        botonGuardar.disabled = true;
        try {
          aplicarTemaDesdeConfiguracion(selectorTema && selectorTema.value === 'Oscuro', true);
          if (selectorIdioma) {
            guardarIdioma(selectorIdioma.value);
            aplicarIdioma(selectorIdioma.value, false);
          }
          if (selectorTamanoLetra) {
            guardarTamanoLetra(selectorTamanoLetra.value);
          }
          window.location.assign('../index.php');
        } catch (error) {
          console.error('No se pudieron guardar las preferencias de configuración', error);
          botonGuardar.disabled = false;
        }
      });
    }

    if (botonRestablecer) {
      botonRestablecer.addEventListener('click', () => {
        if (selectorTema) selectorTema.value = 'Claro';
        if (selectorIdioma) selectorIdioma.value = 'es';
        if (selectorTamanoLetra) selectorTamanoLetra.value = 'normal';
        aplicarTemaDesdeConfiguracion(false);
        aplicarIdioma('es', false);
        aplicarTamanoLetra('normal');
      });
    }

    if (selectorTema) selectorTema.value = leerTema() ? 'Oscuro' : 'Claro';
    if (selectorIdioma) {
      const idiomaGuardado = leerIdioma();
      selectorIdioma.value = ['es', 'en', 'pt'].includes(idiomaGuardado) ? idiomaGuardado : 'es';
    }

    const preferenciasIniciales = {
      tema: selectorTema ? selectorTema.value : '',
      idioma: selectorIdioma ? selectorIdioma.value : '',
      tamanoLetra: selectorTamanoLetra ? selectorTamanoLetra.value : ''
    };

    const hayCambiosSinGuardar = () =>
      (selectorTema && selectorTema.value !== preferenciasIniciales.tema) ||
      (selectorIdioma && selectorIdioma.value !== preferenciasIniciales.idioma) ||
      (selectorTamanoLetra && selectorTamanoLetra.value !== preferenciasIniciales.tamanoLetra);

    const cerrarModalSalida = () => {
      if (!modalSalida) return;
      modalSalida.hidden = true;
      if (elementoEnfocadoAntesDelModal instanceof HTMLElement) {
        elementoEnfocadoAntesDelModal.focus();
      }
    };

    enlacesSalida.forEach((enlace) => {
      enlace.addEventListener('click', (evento) => {
        if (!hayCambiosSinGuardar() || !modalSalida) return;
        evento.preventDefault();
        destinoSalida = enlace.href;
        elementoEnfocadoAntesDelModal = enlace;
        modalSalida.hidden = false;
        botonCancelarSalida?.focus();
      });
    });

    botonConfirmarSalida?.addEventListener('click', () => {
      window.location.assign(destinoSalida);
    });

    modalSalida?.addEventListener('click', (evento) => {
      if (evento.target.closest('[data-config-exit-cancel]')) {
        cerrarModalSalida();
      }
    });

    document.addEventListener('keydown', (evento) => {
      if (evento.key === 'Escape' && modalSalida && !modalSalida.hidden) {
        cerrarModalSalida();
      }
    });
  </script>
</body>
</html>
