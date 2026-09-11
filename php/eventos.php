<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Metaetiquetas y configuración -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos | SIGTUR</title>
    <link rel="icon" href="../img/logoblanco.png"> <!-- Ícono de pestaña -->
    <!-- Importar fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../css/estilos.css?v=<?= time(); ?>"> <!-- Estilos generales -->
    <link rel="stylesheet" href="../css/eventos.css"> <!-- Estilos específicos de eventos -->
</head>

<body class="page-eventos">
    <?php
    $headerCurrentPage = 'eventos';
    $headerActivePage = 'eventos';
    include __DIR__ . '/header.php';
    ?>

    <nav class="bottom-nav" aria-label="Navegación inferior">
        <a href="../index.php" class="bottom-nav-item">
            <img src="../img/logoazul.png" class="bottom-nav-icon" alt="Inicio" data-i18n-alt="navHome">
            <span class="bottom-nav-label" data-i18n="navHome">Inicio</span>
        </a>
        <a href="eventos.php" class="bottom-nav-item active">
            <img src="../img/nav-eventos.png" class="bottom-nav-icon" alt="Eventos" data-i18n-alt="navEvents">
            <span class="bottom-nav-label" data-i18n="navEvents">Eventos</span>
        </a>
        <a href="turismo.php" class="bottom-nav-item">
            <img src="../img/turismo.png" class="bottom-nav-icon" alt="Turismo" data-i18n-alt="navTourism">
            <span class="bottom-nav-label" data-i18n="navTourism">Turismo</span>
        </a>
        <a href="lugares.php" class="bottom-nav-item">
            <img src="../img/lugares.png" class="bottom-nav-icon" alt="Lugares" data-i18n-alt="navPlaces">
            <span class="bottom-nav-label" data-i18n="navPlaces">Lugares</span>
        </a>
        <a href="gastronomia.php" class="bottom-nav-item">
            <img src="../img/gastronomia.jpg" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label">Gastronomía</span>
        </a>
    </nav>

    <main class="pagina-eventos">
        <!-- Encabezado con imagen de fondo -->
        <section class="encabezado-eventos">
            <div class="fondo-encabezado"></div>
            <div class="texto-encabezado">
                <span class="subtitulo-encabezado">Eventos en Salto</span>
                <h1 data-i18n="eventsHeroTitle">EVENTOS</h1>
                <p data-i18n="eventsHeroText">Este apartado reúne los diferentes eventos turísticos, deportivos y culturales de Salto para que puedas informarte y participar en actividades divertidas.</p>
            </div>
        </section>

        <section class="seccion seccion-foco destacados-section" id="eventos-destacados">
            <div class="titulo-seccion">
                <h2>Eventos Destacados</h2>
            </div>
            <div class="carrusel-eventos">
                <button class="flecha izquierda" data-carrusel="destacados" aria-label="Anterior evento destacado"></button>
                <div class="visor-carrusel">
                    <div class="contenedor-tarjetas" id="destacados-contenedor"></div>
                </div>
                <button class="flecha derecha" data-carrusel="destacados" aria-label="Siguiente evento destacado"></button>
            </div>
            <div class="indicadores-carrusel" id="destacados-indicadores"></div>
        </section>

        <section class="seccion filtros-eventos">
            <div class="filtros-top">
                <div class="buscador-eventos">
                    <img src="../img/lupa.png" alt="Buscar eventos">
                    <input id="buscador-eventos" type="search" placeholder="Buscar entre los eventos..." aria-label="Buscar eventos">
                </div>
                <div class="botones-filtro" role="tablist" aria-label="Filtrar eventos principales">
                    <button type="button" class="filtro-pill activa" data-filtro="todos">Todos</button>
                    <button type="button" class="filtro-pill" data-filtro="competencia">Competencias</button>
                    <button type="button" class="filtro-pill" data-filtro="carrera">Carreras</button>
                    <button type="button" class="filtro-pill" data-filtro="deportivo">Deportivo</button>
                    <button type="button" class="filtro-pill" data-filtro="discoteca">Discotecas</button>
                </div>
            </div>
        </section>

        <section class="seccion eventos-principales">
            <div class="titulo-seccion eventos-header">
                <h2>Listado de Eventos</h2>
                <button type="button" class="btn-crear-evento" id="btn-crear-evento">Crear Evento +</button>
            </div>
            <div class="eventos-lista" id="eventos-principales-contenedor"></div>
        </section>

        <div class="form-evento-overlay" id="form-evento-overlay" aria-hidden="true">
            <div class="form-evento-modal" role="dialog" aria-modal="true" aria-labelledby="form-evento-title">
                <button class="form-evento-close" id="form-evento-close" type="button" aria-label="Cerrar formulario">×</button>
                <div class="form-evento-header">
                    <h3 id="form-evento-title">Crear nuevo evento</h3>
                    <p>Completa los datos para agregar un nuevo evento a la plataforma.</p>
                </div>
                <form id="form-nuevo-evento" class="form-evento" novalidate>
                    <div class="form-error" id="form-error" role="alert"></div>

                    <div class="form-section">
                        <label class="field">
                            <span>Título del evento</span>
                            <div class="title-input-group">
                                <input type="text" id="evento-titulo" name="titulo" placeholder="Ej. Festival de la Naranja" required>
                                <button type="button" class="description-toggle" id="btn-agregar-descripcion">+ Agregar descripción</button>
                            </div>
                        </label>

                        <label class="field field-description" id="field-descripcion" hidden>
                            <span>Descripción</span>
                            <textarea id="evento-descripcion" name="descripcion" rows="4" placeholder="Describe la propuesta, el ambiente o los puntos destacados del evento."></textarea>
                        </label>
                    </div>

                    <div class="form-grid">
                        <label class="field">
                            <span>Ubicación</span>
                            <input type="text" id="evento-ubicacion" name="ubicacion" placeholder="Ej. Plaza Artigas" required>
                        </label>
                        <label class="field field-date">
                            <span>Fecha del evento</span>
                            <div class="date-picker">
                                <input type="text" id="fecha-visual" name="fechaVisual" readonly value="" placeholder="Selecciona una fecha">
                                <input type="hidden" id="fecha-evento" name="fecha">
                                <button type="button" class="date-picker-toggle" id="date-picker-toggle" aria-label="Abrir calendario">▾</button>
                                <div class="calendar-dropdown" id="calendar-dropdown">
                                    <div class="calendar-header">
                                        <button type="button" class="calendar-nav" id="calendar-prev" aria-label="Mes anterior">‹</button>
                                        <div class="calendar-month" id="calendar-month-label"></div>
                                        <button type="button" class="calendar-nav" id="calendar-next" aria-label="Mes siguiente">›</button>
                                    </div>
                                    <div class="calendar-weekdays">
                                        <span>Lu</span>
                                        <span>Ma</span>
                                        <span>Mi</span>
                                        <span>Ju</span>
                                        <span>Vi</span>
                                        <span>Sa</span>
                                        <span>Do</span>
                                    </div>
                                    <div class="calendar-days" id="calendar-days"></div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="form-grid">
                        <label class="field">
                            <span>Edad mínima</span>
                            <input type="number" id="edad-minima" name="edadMinima" min="0" max="100" value="0">
                        </label>
                        <label class="field">
                            <span>Edad máxima</span>
                            <input type="number" id="edad-maxima" name="edadMaxima" min="0" max="100" value="99">
                        </label>
                    </div>

                    <div class="form-grid">
                        <label class="field">
                            <span>Categoría del evento</span>
                            <select id="categoria-evento" name="categoria">
                                <option value="Competencias">Competencias</option>
                                <option value="Carreras">Carreras</option>
                                <option value="Deportivo">Deportivo</option>
                                <option value="Discotecas">Discotecas</option>
                                <option value="Cultural">Cultural</option>
                                <option value="Feria">Feria</option>
                            </select>
                        </label>
                        <label class="field">
                            <span>Hora del evento</span>
                            <input type="time" id="evento-hora" name="hora" required>
                        </label>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <span>Canal de notificación</span>
                            <div class="segmented-group" role="tablist" aria-label="Canal de notificación">
                                <button type="button" class="segmented-option active" data-channel="Email">Email</button>
                                <button type="button" class="segmented-option" data-channel="Slack">Slack</button>
                            </div>
                            <input type="hidden" id="canal-notificacion" name="canal" value="Email">
                        </div>
                        <label class="field">
                            <span>Cuándo notificar</span>
                            <select id="recordatorio" name="recordatorio">
                                <option value="1 hora antes">1 hora antes</option>
                                <option value="1 día antes">1 día antes</option>
                                <option value="3 días antes">3 días antes</option>
                                <option value="1 semana antes">1 semana antes</option>
                            </select>
                        </label>
                    </div>

                    <label class="field">
                        <span>Cuerpo del correo</span>
                        <textarea id="correo-cuerpo" name="correoCuerpo" rows="4" placeholder="Redacta el mensaje que se enviará automáticamente a los usuarios."></textarea>
                    </label>

                    <div class="form-grid">
                        <label class="field upload-field">
                            <span>Imagen del evento</span>
                            <div class="upload-zone" id="upload-zone">
                                <input type="file" id="evento-imagen" name="imagen" accept="image/*" hidden>
                                <button type="button" class="upload-button" id="upload-button">Subir archivo</button>
                                <p>Selecciona una imagen en formato JPG, PNG o WEBP.</p>
                                <p class="upload-name" id="upload-name">Sin archivo seleccionado</p>
                            </div>
                        </label>

                        <div class="field">
                            <span>Tipo de entrada</span>
                            <select id="tipo-entrada" name="tipoEntrada">
                                <option value="Gratuito">Gratuito</option>
                                <option value="De Pago">De Pago</option>
                            </select>
                            <label class="field field-price" id="precio-field" hidden>
                                <span>Precio</span>
                                <input type="text" id="evento-precio" name="precio" placeholder="$250">
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancelar" id="btn-cancelar-form">Cancelar</button>
                        <button type="submit" class="btn-crear-form">Crear</button>
                    </div>
                </form>
            </div>
        </div>

        <section class="seccion anteriores-eventos">
            <div class="titulo-seccion">
                <h2>Eventos Anteriores</h2>
            </div>
            <div class="anteriores-grid" id="anteriores-contenedor"></div>
        </section>

        <section class="seccion galeria-eventos">
            <div class="galeria-header">
                <div class="titulo-seccion">
                    <h2>Galería General</h2>
                </div>
                <div class="galeria-controls">
                    <div class="buscador-eventos buscador-galeria">
                        <img src="../img/lupa.png" alt="Buscar galería">
                        <input id="galeria-search" type="search" placeholder="Buscar en galería..." aria-label="Buscar galería">
                    </div>
                    <div class="botones-filtro galeria-pills" role="tablist" aria-label="Filtrar galería">
                        <button type="button" class="filtro-pill activa" data-galeria-filtro="todos">Todos</button>
                        <button type="button" class="filtro-pill" data-galeria-filtro="competencia">Competencias</button>
                        <button type="button" class="filtro-pill" data-galeria-filtro="carrera">Carreras</button>
                        <button type="button" class="filtro-pill" data-galeria-filtro="deportivo">Deportivo</button>
                        <button type="button" class="filtro-pill" data-galeria-filtro="discoteca">Discotecas</button>
                    </div>
                </div>
            </div>
            <div class="grilla-galeria" id="galeria-contenedor"></div>
        </section>

        <section class="seccion mapa-seccion" aria-labelledby="mapa-eventos-titulo">
            <div class="mapa-encabezado">
                <span class="mapa-kicker">Ubicaciones</span>
                <h2 id="mapa-eventos-titulo">Encontrá cada evento</h2>
                <p>Consultá el mapa para ubicar los principales puntos de encuentro en Salto.</p>
            </div>
            <div class="mapa-contenido">
                <iframe src="https://www.google.com/maps?q=Salto%2C%20Uruguay&output=embed" loading="lazy" title="Mapa de eventos en Salto"></iframe>
                <div class="mapa-lugares">
                    <a href="https://www.google.com/maps/search/?api=1&query=Costanera%20Norte%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Costanera Norte</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20Artigas%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Plaza Artigas</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Plaza%20de%20Deportes%2C%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Plaza de Deportes</a>
                    <a href="https://www.google.com/maps/search/?api=1&query=Playa%20Salto%2C%20Uruguay" target="_blank" rel="noopener">Playa Salto</a>
                </div>
            </div>
        </section>
    </main>

    <div class="lightbox" id="lightbox">
        <div class="lightbox-backdrop" id="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <button class="lightbox-close" id="lightbox-close" aria-label="Cerrar imagen">×</button>
            <button class="lightbox-arrow left" id="lightbox-prev" aria-label="Imagen anterior">❮</button>
            <div class="lightbox-media">
                <img id="lightbox-image" src="" alt="Vista ampliada de galería">
            </div>
            <button class="lightbox-arrow right" id="lightbox-next" aria-label="Siguiente imagen">❯</button>
            <aside class="lightbox-comments" aria-label="Comentarios de la imagen">
                <div class="lightbox-caption" id="lightbox-caption"></div>
                <article class="gallery-engagement-card" id="lightbox-engagement-card"></article>
            </aside>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>EVENTOS</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="eventos.php" class="footer-link">Eventos destacados</a>
                    <a href="eventos.php" class="footer-link">Próximos eventos</a>
                    <a href="eventos.php" class="footer-link">Eventos anteriores</a>
                    <a href="eventos.php" class="footer-link">Todos los eventos</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>LUGARES</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="lugares.php" class="footer-link">Plaza Artigas</a>
                    <a href="lugares.php" class="footer-link">Plaza Treinta y Tres</a>
                    <a href="lugares.php" class="footer-link">Costanera Norte</a>
                    <a href="lugares.php" class="footer-link">Costanera Sur</a>
                    <a href="lugares.php" class="footer-link">Parque Benito Solari</a>
                </div>
            </div>

            <div class="footer-col">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>TURISMO</h4>
                    <span class="footer-toggle-icon">▾</span>
                </button>
                <div class="footer-links">
                    <a href="turismo.php" class="footer-link">Actividades</a>
                    <a href="turismo.php" class="footer-link">Lugares turísticos</a>
                    <a href="turismo.php" class="footer-link">Experienciaxz|    s</a>
                </div>
            </div>

            <div class="footer-col footer-col--social">
                <button class="footer-toggle" type="button" aria-expanded="false">
                    <h4>SIGTUR</h4>
                    <span class="footer-toggle-icon"></span>
                </button>
                    <a href="https://instagram.com/bapstuy" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">IG</span>Instagram
                    </a>
                    <a href="https://facebook.com/sigtur" target="_blank" class="footer-link footer-link--icon">
                        <span class="social-icon">FB</span>Facebook
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <a href="https://bapst.netlify.app">SIGTUR</a> © 2026 by <a href="https://instagram.com/bapstuy">Bapst</a>
            is licensed under <a href="https://creativecommons.org/licenses/by-nc-nd/4.0/">CC BY-NC-ND 4.0</a><img
                src="https://mirrors.creativecommons.org/presskit/icons/cc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/by.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nc.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;"><img
                src="https://mirrors.creativecommons.org/presskit/icons/nd.svg" alt=""
                style="max-width: 1em;max-height:1em;margin-left: .2em;">
        </div>
    </footer>

    <script src="../js/script.js" defer></script>
    <script src="../js/interacciones-tarjetas.js" defer></script>
    <script src="../js/eventos.js" defer></script>
</body>

</html>