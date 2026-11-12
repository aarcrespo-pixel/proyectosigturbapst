<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/helpers/autorizacion.php';
require_once __DIR__ . '/helpers/imagenes.php';
require_once __DIR__ . '/helpers/contenido_publico.php';

sigtur_inicializar_contenido_publico($pdo);

$usuarioActual = sigtur_usuario_actual($pdo);
$puedeGestionarEventos = $usuarioActual && in_array($usuarioActual['rol'], ['organizador', 'administrador'], true);
$puedeVerEstadisticas = $puedeGestionarEventos;
$puedeCrearEvento = $usuarioActual && $usuarioActual['rol'] === 'organizador';
$csrfToken = $puedeGestionarEventos ? sigtur_csrf_token() : '';
$fotosGaleriaEventos = $pdo->query("SELECT g.id, g.imagen, g.titulo, g.descripcion, g.tipo_evento, g.usuario_id,
        u.nombre_completo AS usuario_nombre, u.perfil_publico AS usuario_perfil_publico, g.fecha_creacion
    FROM galeria_eventos g
    INNER JOIN usuarios u ON u.id = g.usuario_id
    ORDER BY g.fecha_creacion DESC, g.id DESC")->fetchAll();
foreach ($fotosGaleriaEventos as &$fotoGaleriaEvento) {
    $fotoGaleriaEvento['imagen'] = sigtur_url_imagen_subida((string) $fotoGaleriaEvento['imagen'], 'galeria-eventos', '../img/porco.avif');
    $fotoGaleriaEvento['fecha'] = date('d/m/Y', strtotime($fotoGaleriaEvento['fecha_creacion']));
    $fotoGaleriaEvento['puede_eliminar'] = $usuarioActual
        && ($usuarioActual['rol'] === 'administrador' || (int) $fotoGaleriaEvento['usuario_id'] === (int) $usuarioActual['id']);
    unset($fotoGaleriaEvento['fecha_creacion']);
}
unset($fotoGaleriaEvento);

/* Esta página tiene dos salidas: HTML para la vista normal y JSON cuando
    ajax=1. Mantener ambas en el mismo controlador evita duplicar filtros. */
/* El listado muestra próximos eventos; el calendario conserva todo el historial. */
$eventosDesdeBd = $pdo->query('SELECT * FROM eventos WHERE es_pasado = 0 AND fecha >= CURDATE() AND fecha <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH) ORDER BY fecha ASC, id DESC')->fetchAll();
/* El calendario es el acceso histórico completo: no filtra por estado ni por
   antigüedad para que los eventos pasados sigan disponibles por fecha. */
$eventosCalendarioDesdeBd = $pdo->query('SELECT id, titulo, slug, descripcion, fecha, ubicacion, categoria, imagen_portada
    FROM eventos ORDER BY fecha ASC, id ASC')->fetchAll();
/* Deduplicamos destacados y completamos con los próximos eventos para conservar
   tres tarjetas distintas aun cuando haya menos de tres marcados. */
$eventosDestacadosDesdeBd = $pdo->query('SELECT e.id, e.titulo, e.slug, e.categoria, e.ubicacion, e.imagen_portada
    FROM eventos e
    LEFT JOIN (
        SELECT evento_id, MIN(orden) AS orden, MIN(fecha_destacado) AS fecha_destacado
        FROM eventos_destacados
        GROUP BY evento_id
    ) d ON d.evento_id = e.id
    WHERE e.es_pasado = 0 AND e.fecha >= CURDATE() AND e.fecha <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
    ORDER BY CASE WHEN d.evento_id IS NULL THEN 1 ELSE 0 END ASC,
        d.orden ASC, d.fecha_destacado ASC, e.fecha ASC, e.id DESC
    LIMIT 3')->fetchAll();
foreach ($eventosDesdeBd as &$evento) {
    $evento['imagen_portada'] = sigtur_url_imagen_evento((string) $evento['slug'], (string) ($evento['imagen_portada'] ?? ''));
}
unset($evento);
foreach ($eventosDestacadosDesdeBd as &$eventoDestacado) {
    $eventoDestacado['imagen_portada'] = sigtur_url_imagen_evento((string) $eventoDestacado['slug'], (string) ($eventoDestacado['imagen_portada'] ?? ''));
}
unset($eventoDestacado);
foreach ($eventosCalendarioDesdeBd as &$eventoCalendario) {
    $eventoCalendario['imagen_portada'] = sigtur_url_imagen_evento((string) $eventoCalendario['slug'], (string) ($eventoCalendario['imagen_portada'] ?? ''));
}
unset($eventoCalendario);
$ubicacionesEventosMapa = [];
foreach ($eventosCalendarioDesdeBd as $eventoMapa) {
    $ubicacionMapa = trim((string) ($eventoMapa['ubicacion'] ?? ''));
    if ($ubicacionMapa === '' || stripos($ubicacionMapa, 'a completar') !== false) {
        continue;
    }
    $ubicacionNormalizada = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $ubicacionMapa) ?: $ubicacionMapa;
    $claveUbicacionMapa = strtolower(preg_replace('/\s+/', ' ', $ubicacionNormalizada) ?? $ubicacionNormalizada);
    $ubicacionesEventosMapa[$claveUbicacionMapa] = $ubicacionesEventosMapa[$claveUbicacionMapa] ?? $ubicacionMapa;
}
asort($ubicacionesEventosMapa, SORT_LOCALE_STRING);
$eventoIdsPorSlug = [];
foreach ($pdo->query('SELECT id, slug FROM eventos')->fetchAll() as $eventoCatalogo) {
    $eventoIdsPorSlug[(string) $eventoCatalogo['slug']] = (int) $eventoCatalogo['id'];
}
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    /* Los parámetros del drawer llegan por query string; se normalizan antes
       de formar las condiciones SQL para evitar comparar valores ambiguos. */
    header('Content-Type: application/json; charset=utf-8');
    $texto = trim($_GET['texto'] ?? '');
    $categoria = trim($_GET['categoria'] ?? '');
    $tipoEntrada = trim($_GET['tipo_entrada'] ?? '');
    $desde = trim($_GET['desde'] ?? '');
    $hasta = trim($_GET['hasta'] ?? '');
    $minPrecio = $_GET['min_precio'] ?? '';
    $maxPrecio = $_GET['max_precio'] ?? '';

    /* Cada filtro se agrega con parámetros nombrados para mantener la consulta
       segura y permitir que el drawer reutilice este mismo controlador. */
    $condiciones = ['es_pasado = 0', 'fecha >= CURDATE()', 'fecha <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)'];
    $parametros = [];
     /* Cada condición es opcional: el array solo recibe filtros que el usuario
         completó, y luego se unen con AND para obtener el resultado final. */
     if ($texto !== '') { $condiciones[] = '(titulo LIKE :texto OR ubicacion LIKE :texto)'; $parametros[':texto'] = '%' . $texto . '%'; }
    if ($categoria !== '') { $condiciones[] = 'categoria = :categoria'; $parametros[':categoria'] = $categoria; }
    if ($tipoEntrada !== '') { $condiciones[] = 'tipo_entrada = :tipo_entrada'; $parametros[':tipo_entrada'] = $tipoEntrada; }
    if ($desde !== '') { $condiciones[] = 'fecha >= :desde'; $parametros[':desde'] = $desde; }
    if ($hasta !== '') { $condiciones[] = 'fecha <= :hasta'; $parametros[':hasta'] = $hasta; }
    if (is_numeric($minPrecio)) { $condiciones[] = 'COALESCE(precio, 0) >= :min_precio'; $parametros[':min_precio'] = (float) $minPrecio; }
    if (is_numeric($maxPrecio)) { $condiciones[] = 'COALESCE(precio, 0) <= :max_precio'; $parametros[':max_precio'] = (float) $maxPrecio; }
    $consultaFiltrada = $pdo->prepare('SELECT * FROM eventos WHERE ' . implode(' AND ', $condiciones) . ' ORDER BY fecha ASC, id DESC');
    $consultaFiltrada->execute($parametros);
    $eventosFiltrados = $consultaFiltrada->fetchAll();
    foreach ($eventosFiltrados as &$eventoFiltrado) {
        $eventoFiltrado['imagen_portada'] = sigtur_url_imagen_evento((string) $eventoFiltrado['slug'], (string) ($eventoFiltrado['imagen_portada'] ?? ''));
    }
    unset($eventoFiltrado);
    echo json_encode($eventosFiltrados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
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
    <link rel="stylesheet" href="../css/eventos.css?v=20261112-calendar-removal-1"> <!-- Estilos específicos de eventos -->
    <link rel="stylesheet" href="../css/calendario-eventos.css?v=20261112-1">
    <link rel="stylesheet" href="../css/enlaces-texto.css?v=20261102-event-links-1">
    <?php if ($puedeVerEstadisticas): ?><link rel="stylesheet" href="../css/estadisticas-eventos.css?v=20261112-1"><?php endif; ?>
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
            <img src="../img/gastronomia.png" class="bottom-nav-icon" alt="Gastronomía">
            <span class="bottom-nav-label">Gastronomía</span>
        </a>
    </nav>

    <main class="pagina-eventos">
        <!-- Encabezado con imagen de fondo -->
        <section class="encabezado-eventos">
            <div class="fondo-encabezado"></div>
            <div class="texto-encabezado">
                <span class="subtitulo-encabezado">Eventos en Salto</span>
                <h1 data-i18n="eventsHeroTitle">Eventos</h1>
                <p data-i18n="eventsHeroText">Este apartado reúne los diferentes eventos turísticos, deportivos y culturales de Salto para que puedas informarte y participar en actividades divertidas.</p>
            </div>
        </section>

        <!-- El destacado permanece separado del listado dinámico general. -->
        <section class="seccion seccion-foco destacados-section" id="eventos-destacados">
            <div class="titulo-seccion">
                <h2>Eventos destacados</h2>
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
                    <button type="button" class="filtro-pill" data-filtro="gratuito">Gratuito</button>
                    <button type="button" class="filtro-pill" data-filtro="de-pago">De pago</button>
                    <button type="button" class="filtro-pill" data-filtro="discoteca">Discotecas</button>
                </div>
            </div>
        </section>

        <!-- El grid recibe un render PHP inicial y luego puede ser actualizado por JS. -->
        <section class="seccion eventos-principales">
            <div class="titulo-seccion eventos-header">
                <div class="eventos-header-copy">
                    <h2>Listado de eventos</h2>
                    <p class="eventos-header-description">Eventos próximos registrados en SIGTUR, hasta los siguientes seis meses.</p>
                </div>
                <div class="eventos-header-actions"><button type="button" class="btn-filtros" id="btn-filtros" aria-expanded="false">Filtros ⚙️</button><?php if ($puedeCrearEvento): ?><button type="button" class="btn-crear-evento" id="btn-crear-evento">Crear evento +</button><?php endif; ?></div>
            </div>
            <div class="eventos-lista" id="eventos-principales-contenedor">
                <?php foreach ($eventosDesdeBd as $evento):
                    $tipoEntradaEtiqueta = (string) ($evento['tipo_entrada'] ?? 'Gratuito');
                    if (strcasecmp($tipoEntradaEtiqueta, 'De Pago') === 0) {
                        $tipoEntradaEtiqueta = 'De pago';
                    }
                ?>
                    <article class="evento-card" data-item-key="evento-<?= (int) $evento['id'] ?>" data-engagement="like-only" tabindex="0" role="link">
                        <div class="evento-card-media"><img src="<?= htmlspecialchars($evento['imagen_portada'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy"></div>
                        <div class="evento-card-body"><span class="categoria"><?= htmlspecialchars($evento['categoria'] . ' · ' . $tipoEntradaEtiqueta, ENT_QUOTES, 'UTF-8') ?></span><h3><?= htmlspecialchars($evento['titulo'], ENT_QUOTES, 'UTF-8') ?></h3><p class="lugar"><?= htmlspecialchars($evento['ubicacion'], ENT_QUOTES, 'UTF-8') ?></p><?php $valorEntrada = trim((string) ($evento['precio_texto'] ?? $evento['precio'] ?? '')); ?><?php if (($evento['tipo_entrada'] ?? '') === 'De Pago'): ?><p class="precio-evento"><?= htmlspecialchars($valorEntrada !== '' ? $valorEntrada : 'Consultar con la organización', ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?></div>
                        <div class="evento-card-action"><div class="fecha-protagonista"><span class="fecha-mes"><?= htmlspecialchars(strtolower(date('M', strtotime($evento['fecha']))), ENT_QUOTES, 'UTF-8') ?></span><span class="fecha-dia"><?= htmlspecialchars(date('d', strtotime($evento['fecha'])), ENT_QUOTES, 'UTF-8') ?></span></div><a href="evento.php?id=<?= (int) $evento['id'] ?>" class="boton-amarillo">Ver más</a></div>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="eventos-list-toggle" id="eventos-principales-toggle" type="button" data-event-list="principales" aria-expanded="false" aria-controls="eventos-principales-contenedor" hidden>Ver más</button>
        </section>

        <?php if ($puedeCrearEvento): ?><div class="form-evento-overlay" id="form-evento-overlay" aria-hidden="true">
            <div class="form-evento-modal" role="dialog" aria-modal="true" aria-labelledby="form-evento-title">
                <button class="form-evento-close" id="form-evento-close" type="button" aria-label="Cerrar formulario">×</button>
                <div class="form-evento-header">
                    <h3 id="form-evento-title">Crear nuevo evento</h3>
                    <p>Completa los datos para agregar un nuevo evento a la plataforma.</p>
                </div>
                <form id="form-nuevo-evento" class="form-evento" method="post" action="crear-evento.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
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
                            <span>Descripción (opcional)</span>
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
                                <option value="Gratuito">Gratuito</option>
                                <option value="De Pago">De pago</option>
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
                                <input type="file" id="evento-imagen" name="imagen" accept="image/jpeg,image/png,image/webp" hidden>
                                <button type="button" class="upload-button" id="upload-button">Subir archivo</button>
                                <p>Selecciona una imagen en formato JPG, PNG o WEBP.</p>
                                <img class="upload-preview" id="upload-preview" alt="Vista previa de la portada del evento" hidden>
                                <p class="upload-name" id="upload-name">Sin archivo seleccionado</p>
                            </div>
                        </label>

                        <div class="field">
                            <span>Tipo de entrada</span>
                            <select id="tipo-entrada" name="tipoEntrada">
                                <option value="Gratuito">Gratuito</option>
                                <option value="De Pago">De pago</option>
                            </select>
                            <label class="field field-price" id="precio-field" hidden>
                                <span>Precio o dónde consultar</span>
                                <input type="text" id="evento-precio" name="precio" maxlength="120" placeholder="$250, a voluntad o WhatsApp 091 123 456">
                                <small>Indica el precio o un contacto para consultar. Si no lo sabes aún, se mostrará que consulten con la organización.</small>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancelar" id="btn-cancelar-form">Cancelar</button>
                        <button type="submit" class="btn-crear-form">Crear</button>
                    </div>
                </form>
            </div>
        </div><?php endif; ?>

        <?php if ($puedeCrearEvento): ?>
            <div class="form-evento-overlay" id="form-galeria-overlay" aria-hidden="true">
                <div class="form-evento-modal" role="dialog" aria-modal="true" aria-labelledby="form-galeria-title">
                    <button class="form-evento-close" type="button" data-gallery-close aria-label="Cerrar formulario">×</button>
                    <div class="form-evento-header">
                        <h3 id="form-galeria-title">Agregar fotos a la galería</h3>
                        <p>Elegí el título, el tipo de evento y hasta 10 fotos JPG, PNG o WEBP.</p>
                    </div>
                    <form class="form-evento" id="form-galeria-fotos" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="form-error" data-gallery-error role="alert"></div>
                        <label class="field">
                            <span>Título de las fotos</span>
                            <input type="text" name="titulo" maxlength="160" required placeholder="Ej. Festival de la Naranja">
                        </label>
                        <label class="field">
                            <span>Tipo de evento</span>
                            <select name="tipo_evento" required>
                                <option value="">Seleccioná un tipo</option>
                                <?php foreach (['Competencias', 'Carreras', 'Deportivo', 'Discotecas', 'Cultural', 'Feria', 'Otro'] as $tipoFoto): ?>
                                    <option value="<?= htmlspecialchars($tipoFoto, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tipoFoto, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="field upload-field">
                            <span>Fotos</span>
                            <div class="upload-zone" data-gallery-upload-zone>
                                <input type="file" name="foto[]" accept="image/jpeg,image/png,image/webp" multiple required hidden data-gallery-files>
                                <button type="button" class="upload-button" data-gallery-file-select>Elegir fotos</button>
                                <p>Podés seleccionar hasta 10 imágenes, de máximo 5 MB cada una.</p>
                                <p class="upload-name" data-gallery-file-names>Sin archivos seleccionados</p>
                            </div>
                        </label>
                        <p class="gallery-upload-status" data-gallery-status role="status" aria-live="polite"></p>
                        <div class="form-actions">
                            <button type="button" class="btn-cancelar" data-gallery-close>Cancelar</button>
                            <button type="submit" class="btn-crear-form" data-gallery-submit>Subir fotos</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- El drawer se mantiene fuera del flujo del grid para animarse sin desplazar tarjetas. -->
        <aside class="filtros-drawer" id="filtros-drawer" aria-hidden="true" aria-labelledby="filtros-title">
            <div class="filtros-drawer__backdrop" data-cerrar-filtros></div>
            <section class="filtros-drawer__panel" role="dialog" aria-modal="true">
                <header class="filtros-drawer__header"><h2 id="filtros-title">Filtrar eventos</h2><button type="button" data-cerrar-filtros aria-label="Cerrar filtros">×</button></header>
                <label class="drawer-field">Buscar<input id="filtro-texto" type="search" placeholder="Título o ubicación"></label>
                <label class="drawer-field">Categoría<select id="filtro-categoria"><option value="">Todas</option><option value="Competencias">Competencias</option><option value="Carreras">Carreras</option><option value="Discotecas">Discotecas</option><option value="Cultural">Cultural</option><option value="Feria">Feria</option></select></label>
                <label class="drawer-field">Tipo de entrada<select id="filtro-tipo"><option value="">Todos</option><option value="Gratuito">Gratuito</option><option value="De Pago">De pago</option></select></label>
                <div class="drawer-fields"><label class="drawer-field">Precio mínimo<input id="filtro-min-precio" type="number" min="0" placeholder="0"></label><label class="drawer-field">Precio máximo<input id="filtro-max-precio" type="number" min="0" placeholder="Sin límite"></label></div>
                <div class="drawer-fields"><label class="drawer-field">Desde<input id="filtro-desde" type="date"></label><label class="drawer-field">Hasta<input id="filtro-hasta" type="date"></label></div>
                <div class="filtros-drawer__actions"><button type="button" class="btn-crear-form" id="aplicar-filtros">Aplicar filtros</button><button type="button" class="btn-cancelar" id="limpiar-filtros">Limpiar</button></div>
            </section>
        </aside>

        <section class="seccion agenda-mensual" id="calendario-eventos" aria-labelledby="calendario-eventos-titulo">
            <div class="titulo-seccion agenda-mensual__encabezado">
                <span class="agenda-mensual__kicker">Calendario</span>
                <h2 id="calendario-eventos-titulo">Mes por mes</h2>
                <p>Tocá un día para ver qué hay.</p>
            </div>
            <div class="agenda-mensual__panel">
                <div class="agenda-mensual__navegacion">
                    <div class="agenda-mensual__acciones">
                        <button class="agenda-mensual__boton" id="calendario-anterior" type="button">&lt; Anterior</button>
                        <button class="agenda-mensual__boton" id="calendario-hoy" type="button">Hoy</button>
                    </div>
                    <h3 class="agenda-mensual__mes" id="calendario-mes" aria-live="polite"></h3>
                    <button class="agenda-mensual__boton" id="calendario-siguiente" type="button">Siguiente &gt;</button>
                </div>
                <div class="agenda-mensual__dias-semana" aria-hidden="true">
                    <span>lun</span><span>mar</span><span>mié</span><span>jue</span><span>vie</span><span>sáb</span><span>dom</span>
                </div>
                <div class="agenda-mensual__grilla" id="calendario-grilla" role="grid" aria-label="Calendario mensual de eventos"></div>
                <div class="agenda-mensual__leyenda" id="calendario-leyenda" aria-label="Categorías de eventos"></div>
            </div>
            <section class="agenda-mensual__detalle" id="calendario-detalle" aria-live="polite" aria-labelledby="calendario-detalle-titulo">
                <h3 id="calendario-detalle-titulo">Elegí un día</h3>
                <div id="calendario-eventos-dia"></div>
            </section>
        </section>

        <section class="seccion galeria-eventos">
            <div class="galeria-header">
                <div class="titulo-seccion">
                    <h2>Galería general</h2>
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
                <div class="mapa-lugares" id="mapa-eventos-lugares">
                    <?php foreach ($ubicacionesEventosMapa as $index => $ubicacionMapa): ?>
                        <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= rawurlencode($ubicacionMapa . ', Salto, Uruguay') ?>" target="_blank" rel="noopener" <?= $index >= 5 ? 'hidden' : '' ?>><?= htmlspecialchars($ubicacionMapa, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php endforeach; ?>
                    <button class="mapa-lugares__toggle" id="mapa-eventos-toggle" type="button" aria-expanded="false" aria-controls="mapa-eventos-lugares" <?= count($ubicacionesEventosMapa) <= 5 ? 'hidden' : '' ?>>Ver más</button>
                </div>
            </div>
        </section>

        <?php if ($puedeVerEstadisticas): ?>
            <section class="seccion estadisticas-eventos" id="estadisticas-eventos"
                data-endpoint="api/estadisticas_eventos.php"
                aria-labelledby="estadisticas-eventos-titulo">
                <header class="estadisticas-eventos__encabezado">
                    <div>
                        <span class="estadisticas-eventos__kicker">Análisis de actividad</span>
                        <h2 id="estadisticas-eventos-titulo">Estadísticas y rendimiento</h2>
                        <p><?= $usuarioActual['rol'] === 'administrador'
                            ? 'Un análisis general de la actividad y el rendimiento de los eventos en SIGTUR.'
                            : 'Un análisis de la actividad y el rendimiento de tus eventos.' ?></p>
                    </div>
                    <label class="estadisticas-eventos__filtro">
                        <span>Período de análisis</span>
                        <select id="estadisticas-periodo" aria-label="Período de análisis">
                            <option value="7">Últimos 7 días</option>
                            <option value="30" selected>Últimos 30 días</option>
                            <option value="6m">Últimos 6 meses</option>
                            <option value="todo">Todo el tiempo</option>
                        </select>
                    </label>
                </header>

                <p class="estadisticas-eventos__estado" id="estadisticas-estado" role="status" aria-live="polite">
                    Cargando estadísticas...
                </p>

                <div class="estadisticas-eventos__indicadores" aria-label="Indicadores principales">
                    <article class="estadistica-card" data-indicador="totalEventos">
                        <span class="estadistica-card__etiqueta">Eventos creados</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__nota">En total</span>
                    </article>
                    <article class="estadistica-card" data-indicador="eventosActivos">
                        <span class="estadistica-card__etiqueta">Eventos publicados y activos</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__nota">Hoy o próximos, no finalizados</span>
                    </article>
                    <article class="estadistica-card" data-indicador="visitas">
                        <span class="estadistica-card__etiqueta">Visitas a eventos</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__comparacion"></span>
                        <span class="estadistica-card__nota">En el período seleccionado</span>
                    </article>
                    <article class="estadistica-card" data-indicador="usuariosGuardaron">
                        <span class="estadistica-card__etiqueta"><?= $usuarioActual['rol'] === 'administrador' ? 'Usuarios que guardaron eventos' : 'Usuarios que guardaron tus eventos' ?></span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__comparacion"></span>
                        <span class="estadistica-card__nota">Usuarios distintos en el período</span>
                    </article>
                    <?php if ($usuarioActual['rol'] === 'administrador'): ?>
                        <article class="estadistica-card" data-indicador="registrados">
                            <span class="estadistica-card__etiqueta">Usuarios registrados</span>
                            <strong class="estadistica-card__valor">—</strong>
                            <span class="estadistica-card__comparacion"></span>
                            <span class="estadistica-card__nota">Cuentas en SIGTUR</span>
                        </article>
                    <?php endif; ?>
                    <article class="estadistica-card" data-indicador="promedioVisitas">
                        <span class="estadistica-card__etiqueta">Promedio de visitas por evento</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__nota">Según el período seleccionado</span>
                    </article>
                    <article class="estadistica-card" data-indicador="conversion">
                        <span class="estadistica-card__etiqueta">Conversión a guardados</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__nota">Visitas identificables seguidas de un guardado</span>
                    </article>
                    <article class="estadistica-card" data-indicador="crecimiento">
                        <span class="estadistica-card__etiqueta">Eventos creados en el período</span>
                        <strong class="estadistica-card__valor">—</strong>
                        <span class="estadistica-card__comparacion"></span>
                        <span class="estadistica-card__nota">Comparado con el período anterior</span>
                    </article>
                </div>

                <div class="estadisticas-eventos__graficas">
                    <article class="estadistica-panel estadistica-panel--amplio">
                        <header class="estadistica-panel__encabezado">
                            <div>
                                <span class="estadistica-panel__numero">01</span>
                                <h3>Evolución de visitas</h3>
                            </div>
                            <span class="estadistica-panel__detalle" id="estadisticas-tasa-visitas">Tendencia en preparación</span>
                        </header>
                        <div class="estadistica-grafica" id="estadisticas-visitas-grafica" role="img" aria-label="Gráfico de evolución de visitas a eventos"></div>
                        <p class="estadistica-panel__nota">La línea principal muestra registros reales. Tendencia lineal, tangente y ajuste cuadrático (si los datos lo justifican) son estimaciones, no visitas observadas.</p>
                    </article>
                    <article class="estadistica-panel">
                        <header class="estadistica-panel__encabezado">
                            <div>
                                <span class="estadistica-panel__numero">02</span>
                                <h3>Eventos creados</h3>
                            </div>
                            <span class="estadistica-panel__detalle">Por mes</span>
                        </header>
                        <div class="estadistica-grafica" id="estadisticas-creacion-grafica" role="img" aria-label="Gráfico de eventos creados por mes"></div>
                    </article>
                    <article class="estadistica-panel estadistica-panel--amplio">
                        <header class="estadistica-panel__encabezado">
                            <div>
                                <span class="estadistica-panel__numero">03</span>
                                <h3>Eventos más populares</h3>
                            </div>
                            <span class="estadistica-panel__detalle">Visitas y guardados</span>
                        </header>
                        <div class="estadistica-grafica" id="estadisticas-populares-grafica" role="img" aria-label="Comparación de visitas y guardados por evento"></div>
                    </article>
                    <article class="estadistica-panel">
                        <header class="estadistica-panel__encabezado">
                            <div>
                                <span class="estadistica-panel__numero">04</span>
                                <h3>Interacción de usuarios</h3>
                            </div>
                            <span class="estadistica-panel__detalle" id="estadisticas-conversion-detalle">Conversión no disponible</span>
                        </header>
                        <div class="estadistica-grafica" id="estadisticas-interaccion-grafica" role="img" aria-label="Comparación de visitas y guardados de eventos"></div>
                    </article>
                    <article class="estadistica-panel estadistica-panel--amplio">
                        <header class="estadistica-panel__encabezado">
                            <div>
                                <span class="estadistica-panel__numero">05</span>
                                <h3><?= $usuarioActual['rol'] === 'administrador' ? 'Actividad de la plataforma' : 'Actividad de tus eventos' ?></h3>
                            </div>
                            <span class="estadistica-panel__detalle">Eventos creados y visitas</span>
                        </header>
                        <div class="estadistica-grafica" id="estadisticas-actividad-grafica" role="img" aria-label="Gráfico de actividad de eventos y visitas en SIGTUR"></div>
                    </article>
                </div>
                <p class="estadisticas-eventos__privacidad">
                    El esquema no tiene un campo de publicación independiente: se consideran activos los eventos no marcados como pasados cuya fecha es hoy o futura. Las visitas se cuentan una vez por evento y sesión desde la activación de este registro; no se reconstruye ni inventa historial anterior. La conversión solo considera sesiones identificadas que visitaron un evento y luego lo guardaron. No se muestran datos personales.
                </p>
            </section>
        <?php endif; ?>
    </main>

    <div class="lightbox" id="lightbox">
        <div class="lightbox-backdrop" id="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <button class="lightbox-close" id="lightbox-close" aria-label="Cerrar imagen">×</button>
            <div class="lightbox-media">
                <button class="lightbox-arrow left" id="lightbox-prev" aria-label="Imagen anterior">❮</button>
                <img id="lightbox-image" src="" alt="Vista ampliada de galería">
                <button class="lightbox-arrow right" id="lightbox-next" aria-label="Siguiente imagen">❯</button>
            </div>
        </div>
    </div>

    <script src="../js/script.js?v=20261112-calendar-labels-1" defer></script>
    <script src="../js/enlaces-texto.js?v=20261102-event-links-1" defer></script>
    <script src="../js/interacciones-tarjetas.js?v=20261112-calendar-removal-1" defer></script>
    <?php if ($puedeVerEstadisticas): ?><script src="../js/estadisticas-eventos.js?v=20261112-1" defer></script><?php endif; ?>
    <script>window.sigturEventoIds = <?= json_encode($eventoIdsPorSlug, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script>window.eventosDesdeBd = <?= json_encode($eventosDesdeBd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script>window.eventosDestacadosDesdeBd = <?= json_encode($eventosDestacadosDesdeBd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script>window.eventosCalendarioDesdeBd = <?= json_encode($eventosCalendarioDesdeBd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;</script>
    <script>window.galeriaDesdeBd = <?= json_encode($fotosGaleriaEventos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;</script>
    <script>window.gestionGaleria = <?= json_encode([
        'puedeSubir' => (bool) $puedeCrearEvento,
        'usuarioId' => (int) ($usuarioActual['id'] ?? 0),
        'rol' => (string) ($usuarioActual['rol'] ?? ''),
        'csrfToken' => $csrfToken,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;</script>
    <script src="../js/eventos.js?v=20261112-empty-gallery-1" defer></script>
</body>

</html>