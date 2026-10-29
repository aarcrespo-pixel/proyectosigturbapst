/*
  Aca definimos los datos que se muestran en cada carrusel.
  Cada propiedad como "destacados", "deportivos" o "discotecas"
  coincide con un bloque HTML que tiene un contenedor llamado
  `${nombre}-contenedor` y un indicador `${nombre}-indicadores`.
*/
const eventosDestacadosBase = [
    { nombre: "Carrera de Bicicleta", categoria: "Carreras", lugar: "Costanera Norte", imagen: "../img/moto_cross.jpg" },
    { nombre: "La Ferne", categoria: "Discoteca", lugar: "Costanera Norte", imagen: "../img/porco.avif" },
    { nombre: "Porco Negro", categoria: "Discoteca", lugar: "Av. Apolón de Mirbek esquina Av. José Enrique Rodó.", imagen: "../img/porco.avif" },
    { nombre: "ExpoSalto", categoria: "Feria", lugar: "Hipódromo de Salto", imagen: "../img/exposalto.jpeg" },
    { nombre: "LaFosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "../img/fosa.webp" }
];

const eventosDestacados = Array.isArray(window.eventosDestacadosDesdeBd)
    ? window.eventosDestacadosDesdeBd.map((evento) => ({
        id: Number(evento.id),
        nombre: evento.titulo,
        slug: evento.slug,
        categoria: evento.categoria,
        lugar: evento.ubicacion,
        imagen: evento.imagen_portada || '../img/porco.avif'
    }))
    : eventosDestacadosBase;

const listadoEventos = {
    deportivos: [
        { nombre: "Surf y Kayak", categoria: "Aventura", lugar: "Playa Salto", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Beach Volley", categoria: "Competencia", lugar: "Playa Salto", imagen: "../img/TER%202.jpg" },
        { nombre: "Circuito en Bicicleta", categoria: "Deportivo", lugar: "Calle Artigas", imagen: "../img/evento2.jpg" },
        { nombre: "Rally", categoria: "Carrera", lugar: "Av. Horacio Quiroga 9382", imagen: "../img/moto_cross.jpg" },
        { nombre: "Futbol X5", categoria: "Deportivo", lugar: "Plaza de Deportes Salto", imagen: "../img/lsf.jpg" }
    ],
    discotecas: [
        { nombre: "Halloween en Polo", categoria: "Disfraces", lugar: "Polo Club", imagen: "../img/porco.avif" },
        { nombre: "La Bambola", categoria: "Discoteca", lugar: "Costanera Sur 1535", imagen: "../img/bambi.jpg" },
    ],
    competencias: [
        { nombre: "Streetball Salto", categoria: "Deportivo", lugar: "Polideportivo Círculo SP", imagen: "../img/lsb.jpg" },
        { nombre: "Campeonato de Pesca", categoria: "Naturaleza", lugar: "Costanera Sur", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Ajedrez", categoria: "Mental", lugar: "Plaza de Deportes", imagen: "https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg" },
        { nombre: "Copa de Natación", categoria: "Acuatico", lugar: "Playa Salto", imagen: "../img/TER%202.jpg" }
    ],
    anteriores: [
        { nombre: "Festival de la Naranja", categoria: "Cultura", lugar: "Plaza Artigas", imagen: "../img/festival_naranja.jpg" },
        { nombre: "Carrera Nocturna", categoria: "Deportivo", lugar: "Costanera Norte", imagen: "../img/carrera_noche.webp" },
        { nombre: "Muestra de Danza", categoria: "Cultura", lugar: "Centro Cultural", imagen: "../img/danza.jpg" },
        { nombre: "Feria de Emprendedores", categoria: "Local", lugar: "Mercado Central", imagen: "../img/feria_emprendedores.jpg" },
        { nombre: "La Fosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "../img/fosa.webp" }
    ]
};

const datos = {
    destacados: eventosDestacados,
    ...listadoEventos
};

/* Normalizamos las filas de MySQL al mismo contrato visual de las tarjetas;
   así el grid puede renderizar eventos nuevos sin cambiar su maquetación. */
const eventosPersistidos = Array.isArray(window.eventosDesdeBd) ? window.eventosDesdeBd.map((evento) => ({
    id: Number(evento.id),
    nombre: evento.titulo,
    slug: evento.slug,
    descripcion: evento.descripcion,
    categoria: evento.categoria,
    tipoEntrada: evento.tipo_entrada || 'Gratuito',
    precio: evento.precio,
    precioTexto: evento.precio_texto || (evento.precio !== null && evento.precio !== undefined ? `$${evento.precio}` : ''),
    lugar: evento.ubicacion,
    fecha: evento.fecha,
    imagen: evento.imagen_portada || '../img/porco.avif'
})) : [];

const eventosAnterioresPersistidos = Array.isArray(window.eventosAnterioresDesdeBd) ? window.eventosAnterioresDesdeBd.map((evento) => ({
    id: Number(evento.id),
    nombre: evento.titulo,
    slug: evento.slug,
    categoria: evento.categoria,
    lugar: evento.ubicacion,
    fecha: evento.fecha,
    imagen: evento.imagen_portada || '../img/porco.avif'
})) : [];

const galeria = [
    // Cada foto lleva metadatos propios para que el lightbox pueda renderizar
    // el título y la descripción en lugar de mostrar un panel vacío.
    { imagen: "https://upload.wikimedia.org/wikipedia/commons/e/ef/Parque_Solari_Estatua.JPG", titulo: "Parque Benito Solari", descripcion: "Un espacio verde de Salto para caminar, descansar y disfrutar al aire libre." },
    { imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwYxQbLi8Q2nRVZPDP9CwDkcYwQsKZ1R2EBF2XXy8nGdPUTnexH4SCTce8&s=10", titulo: "Costanera Norte", descripcion: "Un recorrido junto al río para disfrutar el paisaje y los atardeceres de la ciudad." },
    { imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg", titulo: "Salto Shopping", descripcion: "Un punto de encuentro urbano con propuestas para pasear, comprar y compartir." },
    { imagen: "https://alba-uy-sarandi.cdn.mediatiquepress.com/wp-content/uploads/2024/08/Life_Cinemas_2.webp", titulo: "Cine Sarandí", descripcion: "Una sala histórica para disfrutar cine, espectáculos y actividades culturales." },
    { imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg", titulo: "Actividades en Salto", descripcion: "Propuestas y encuentros para vivir la ciudad y compartir con la comunidad." },
    { imagen: "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/84/ae/39/trouville-pizzeria-y.jpg?w=1200&h=1200&s=1", titulo: "La Trouville", descripcion: "Una propuesta gastronómica para descubrir sabores locales y disfrutar una buena salida." }
];

const fotosPersistidasGaleria = Array.isArray(window.galeriaDesdeBd)
    ? window.galeriaDesdeBd.map((foto) => ({
        id: Number(foto.id),
        imagen: foto.imagen,
        titulo: foto.titulo || 'Foto de evento',
        descripcion: [foto.descripcion, foto.tipo_evento ? `Tipo de evento: ${foto.tipo_evento}` : ''].filter(Boolean).join(' · '),
        categoria: foto.tipo_evento || '',
        fecha: foto.fecha || '',
        usuarioId: Number(foto.usuario_id),
        usuarioNombre: foto.usuario_nombre || 'Usuario',
        puedeEliminar: Boolean(foto.puede_eliminar)
    }))
    : [];
galeria.push(...fotosPersistidasGaleria);

function escaparHTML(valor) {
    return String(valor ?? '').replace(/[&<>"']/g, (caracter) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[caracter]);
}

// Construir lista principal con slugs únicos para cada evento
let eventosPrincipales = (() => {
    /* Unimos filas persistidas y catálogo legacy, eliminando duplicados por
       slug para que el listado no repita un evento ya migrado a MySQL. */
    const eventosEstaticos = [
    ...eventosDestacadosBase,
    ...datos.deportivos,
    ...datos.discotecas,
    ...datos.competencias
    ];
    const slugsPersistidos = new Set([...eventosPersistidos, ...eventosAnterioresPersistidos].map((evento) => evento.slug));
    const list = [...eventosPersistidos, ...eventosEstaticos.filter((evento) => !slugsPersistidos.has(slugify(evento.nombre)))];
  const slugCounts = {};
  const fechasProtagonistas = [
    { mes: 'ENE', dia: '05' },
    { mes: 'FEB', dia: '14' },
    { mes: 'MAR', dia: '21' },
    { mes: 'ABR', dia: '03' },
    { mes: 'MAY', dia: '09' },
    { mes: 'JUN', dia: '18' },
    { mes: 'JUL', dia: '27' },
    { mes: 'AGO', dia: '11' },
    { mes: 'SEP', dia: '04' },
    { mes: 'OCT', dia: '16' },
    { mes: 'NOV', dia: '22' },
    { mes: 'DIC', dia: '30' },
    { mes: 'ENE', dia: '12' },
    { mes: 'FEB', dia: '28' },
    { mes: 'MAR', dia: '08' },
    { mes: 'ABR', dia: '25' }
  ];
  const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
  return list.map((e, index) => {
    const base = (e.nombre || '')
      .toString()
      .toLowerCase()
      .normalize('NFD')
      .replace(/\p{Diacritic}/gu, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');
    let slug = base || 'evento';
    if (slugCounts[slug]) {
      slugCounts[slug] += 1;
      slug = `${slug}-${slugCounts[slug]}`;
    } else {
      slugCounts[slug] = 1;
    }
    const coincidenciaFecha = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(e.fecha || ''));
    const mesNumero = coincidenciaFecha ? Number(coincidenciaFecha[2]) : 0;
    const diaNumero = coincidenciaFecha ? Number(coincidenciaFecha[3]) : 0;
    const fechaValida = mesNumero >= 1
      && mesNumero <= 12
      && diaNumero >= 1
      && diaNumero <= new Date(Date.UTC(Number(coincidenciaFecha[1]), mesNumero, 0)).getUTCDate();
    const fecha = fechaValida
      ? { mes: meses[mesNumero - 1], dia: String(diaNumero).padStart(2, '0') }
      : (fechasProtagonistas[index % fechasProtagonistas.length] || { mes: 'AGO', dia: '07' });
    return Object.assign({}, e, { slug, fechaMes: fecha.mes, fechaDia: fecha.dia });
  });
})();

let eventoFiltro = 'todos';
let galeriaFiltro = 'todos';
let galeriaBusqueda = '';
const maxEventosVisibles = 4;
const listasEventosExpandidas = {
    principales: false,
    anteriores: false
};

const pos = {}; // Crea un objeto vacío.
// // Inicializa la posición actual para cada categoría (índice inicial = 0)
for (const nombre in datos) pos[nombre] = 0; // Recorre todas las categorías que existen dentro de datos.
const maxPorPagina = 3;

/*
  Crea el HTML de cada tarjeta de evento.
  El parámetro activa se usa para marcar visualmente el evento seleccionado.
*/
// // Plantilla compleja: template literal con condicional (evento.lugar) y atributo inline onerror
const tarjeta = (evento, activa = false) => `
    <a class="tarjeta${activa ? ' activa' : ''}" href="${escaparHTML(obtenerRutaEvento(evento))}" data-engagement="none" aria-label="Evento ${escaparHTML(evento.nombre)}">
    <div class="imagen-tarjeta">
      <img src="${escaparHTML(evento.imagen)}" alt="${escaparHTML(evento.nombre)}" loading="lazy" onerror="this.onerror=null;this.src='/img/porco.avif';">
    </div>
    <div class="detalles-tarjeta">
      <span class="categoria">${escaparHTML(evento.categoria)}</span>
      <h3>${escaparHTML(evento.nombre)}</h3>
      ${evento.lugar ? `<span class="lugar">${escaparHTML(evento.lugar)}</span>` : ""}
    </div>
    </a>
`;

/*
  Actualiza el contenido del carrusel para la sección indicada.
  La variable nombre viene de los bloques HTML que usan ids como
  `destacados-contenedor`, `deportivos-contenedor`, etc.
*/
function renderSeccion(nombre) {
    /* El carrusel calcula una ventana visible de tres elementos y reemplaza
       solo el contenedor correspondiente, manteniendo los indicadores sincronizados. */
    const lista = datos[nombre];
    const actual = pos[nombre];
    const contenedor = document.getElementById(`${nombre}-contenedor`);
    if (!contenedor) return;
    if (lista.length === 0) {
        contenedor.innerHTML = '<p class="destacados-vacio">No hay eventos destacados por ahora.</p>';
        document.querySelector(`.flecha.izquierda[data-carrusel="${nombre}"]`)?.classList.add("oculta");
        document.querySelector(`.flecha.derecha[data-carrusel="${nombre}"]`)?.classList.add("oculta");
        const indicadoresVacios = document.getElementById(`${nombre}-indicadores`);
        if (indicadoresVacios) indicadoresVacios.innerHTML = "";
        return;
    }
  // // Cálculo de ventana visible con protección de límites (Math.max/Math.min)
  const inicio = Math.max(0, Math.min(actual, lista.length - maxPorPagina));
  // // Uso de slice + map + join para construir HTML de las tarjetas visibles
  const html = lista.slice(inicio, inicio + maxPorPagina)
    .map((evento, index) => tarjeta(evento, inicio + index === actual))
    .join("");

    contenedor.innerHTML = html;

    if (typeof window.initInteractiveCards === 'function') {
      window.initInteractiveCards(contenedor);
    }

    // // Optional chaining + toggle: ocultar flechas cuando estamos en los extremos
    document.querySelector(`.flecha.izquierda[data-carrusel="${nombre}"]`)?.classList.toggle("oculta", actual === 0);
    document.querySelector(`.flecha.derecha[data-carrusel="${nombre}"]`)?.classList.toggle("oculta", actual === lista.length - 1);

    const indicadores = document.getElementById(`${nombre}-indicadores`);
    if (!indicadores) return;

    // // Construcción de indicadores: dataset usado para navegación directa
    indicadores.innerHTML = lista
      .map((_, i) => `<span class="indicador-netflix${i === actual ? " activo" : ""}" data-carrusel="${nombre}" data-index="${i}"></span>`)
      .join("");

    // // Bind click en indicadores: parseo de dataset (Number) y llamada a setPos
    indicadores.querySelectorAll(".indicador-netflix").forEach((item) => {
      item.addEventListener("click", () => setPos(nombre, Number(item.dataset.index)));
    });
}

/*
  Cambia el índice activo del carrusel y re-renderiza.
  Se asegura de que el índice siempre esté dentro del rango válido.
*/
function setPos(nombre, indice) {
    const lista = datos[nombre];
  // // Clamp del índice para que no salga del rango válido
  const siguiente = Math.max(0, Math.min(indice, lista.length - 1));
    if (siguiente === pos[nombre]) return;

    pos[nombre] = siguiente;
    renderSeccion(nombre);
}

/*
  Inicializa la página de eventos.
  Asocia los botones de flecha con su carrusel usando el atributo
  HTML `data-carrusel="nombre"`, que debe coincidir con las secciones
  definidas en el objeto datos.
*/
function renderGaleria() {
    // Filtramos fotos antes de construir HTML para que búsqueda y categoría compartan el mismo renderer.
    const contenedor = document.getElementById("galeria-contenedor");
    if (!contenedor) return;

    const textoBusqueda = galeriaBusqueda.trim().toLowerCase();
    const fotosFiltradas = galeria
        .map((item, index) => ({ ...item, originalIndex: index }))
        .filter((item) => {
            const contenidoFoto = `${item.titulo || ''} ${item.descripcion || ''} ${item.categoria || ''}`.toLowerCase();
            const coincideCategoria = galeriaFiltro === 'todos' || (item.categoria && item.categoria.toLowerCase().includes(galeriaFiltro));
            const coincideTexto = !textoBusqueda || contenidoFoto.includes(textoBusqueda);
            return coincideCategoria && coincideTexto;
        });

    contenedor.innerHTML = fotosFiltradas
        .map((item, index) => `
            <article class="imagen-galeria galeria-item" data-index="${index}" data-original-index="${item.originalIndex}">
                <img src="${escaparHTML(item.imagen)}" alt="${escaparHTML(item.titulo || `Galería ${index + 1}`)}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
                ${item.puedeEliminar ? `<button class="galeria-foto-eliminar" type="button" data-gallery-delete="${Number(item.id)}" aria-label="Eliminar ${escaparHTML(item.titulo)}">Eliminar</button>` : ''}
            </article>
    `)
        .join("") + (window.gestionGaleria?.puedeSubir
            ? '<button class="imagen-galeria galeria-agregar-foto" type="button" data-gallery-open aria-label="Agregar fotos a la galería"><span aria-hidden="true">+</span><small>Agregar fotos</small></button>'
            : '');

    if (typeof window.initInteractiveCards === 'function') {
        window.initInteractiveCards(contenedor);
    }

    document.querySelectorAll(".galeria-item").forEach((boton) => {
        boton.addEventListener("click", (event) => {
            if (event.target.closest('[data-gallery-delete]')) return;
            const indice = Number(boton.dataset.originalIndex || boton.dataset.index);
            abrirLightbox(indice);
        });
    });
    contenedor.querySelectorAll('[data-gallery-delete]').forEach((boton) => {
        boton.addEventListener('click', async (event) => {
            event.stopPropagation();
            if (!window.confirm('¿Querés eliminar esta foto de la galería?')) return;
            boton.disabled = true;
            try {
                const response = await fetch('api/eliminar_foto_galeria.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                    body: new URLSearchParams({
                        foto_id: boton.dataset.galleryDelete,
                        csrf_token: window.gestionGaleria?.csrfToken || ''
                    })
                });
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo eliminar la foto.');
                const index = galeria.findIndex((foto) => Number(foto.id) === Number(boton.dataset.galleryDelete));
                if (index !== -1) galeria.splice(index, 1);
                renderGaleria();
                if (data.warning) window.sigturAlert?.(data.warning);
            } catch (error) {
                window.sigturAlert?.(error.message || 'No se pudo eliminar la foto.');
                boton.disabled = false;
            }
        });
    });
}

function configurarSubidaGaleria() {
    const overlay = document.getElementById('form-galeria-overlay');
    const form = document.getElementById('form-galeria-fotos');
    const input = form?.querySelector('[data-gallery-files]');
    const uploadZone = form?.querySelector('[data-gallery-upload-zone]');
    const fileNames = form?.querySelector('[data-gallery-file-names]');
    const submitButton = form?.querySelector('[data-gallery-submit]');
    const errorBox = form?.querySelector('[data-gallery-error]');
    const status = form?.querySelector('[data-gallery-status]');
    const contenedor = document.getElementById('galeria-contenedor');
    if (!overlay || !form || !input) return;

    const cerrar = () => {
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
    };
    const mostrarError = (mensaje) => {
        errorBox.textContent = mensaje;
        errorBox.classList.toggle('show', Boolean(mensaje));
    };
    const abrir = () => {
        mostrarError('');
        if (status) status.textContent = '';
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        form.querySelector('[name="titulo"]')?.focus();
    };

    contenedor?.addEventListener('click', (event) => {
        if (event.target.closest('[data-gallery-open]')) abrir();
    });
    overlay.querySelectorAll('[data-gallery-close]').forEach((button) => button.addEventListener('click', cerrar));
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay) cerrar();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay.classList.contains('open')) cerrar();
    });
    form.querySelector('[data-gallery-file-select]')?.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        const files = [...(input.files || [])];
        fileNames.textContent = files.length ? files.map((file) => file.name).join(' · ') : 'Sin archivos seleccionados';
        uploadZone.classList.toggle('has-file', files.length > 0);
        mostrarError(files.length > 10 ? 'Podés subir hasta 10 fotos por vez.' : '');
    });
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const files = [...(input.files || [])];
        if (!files.length) {
            mostrarError('Seleccioná al menos una foto.');
            return;
        }
        if (files.length > 10) {
            mostrarError('Podés subir hasta 10 fotos por vez.');
            return;
        }
        if (files.some((file) => file.size > 5 * 1024 * 1024)) {
            mostrarError('Cada foto debe pesar como máximo 5 MB.');
            return;
        }

        mostrarError('');
        if (status) status.textContent = 'Subiendo fotos…';
        submitButton.disabled = true;
        try {
            const body = new FormData(form);
            body.set('ajax', '1');
            const response = await fetch('agregar-foto-galeria.php', {
                method: 'POST',
                body,
                headers: { Accept: 'application/json' }
            });
            const responseText = await response.text();
            let data;
            try {
                data = JSON.parse(responseText);
            } catch {
                throw new Error(`El servidor devolvió una respuesta inesperada: ${responseText.slice(0, 180)}`);
            }
            if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudieron subir las fotos.');
            const agregadas = (data.fotos || []).map((foto) => ({
                ...foto,
                descripcion: [foto.descripcion, foto.categoria ? `Tipo de evento: ${foto.categoria}` : ''].filter(Boolean).join(' · '),
                usuarioId: Number(foto.usuario_id),
                puedeEliminar: window.gestionGaleria?.rol === 'administrador'
                    || Number(foto.usuario_id) === Number(window.gestionGaleria?.usuarioId)
            }));
            galeria.unshift(...agregadas);
            form.reset();
            fileNames.textContent = 'Sin archivos seleccionados';
            uploadZone.classList.remove('has-file');
            cerrar();
            renderGaleria();
            if ((data.results || []).some((resultado) => !resultado.success)) {
                window.sigturAlert?.(`${data.message} Algunas imágenes no se pudieron subir.`);
            }
        } catch (error) {
            mostrarError(error.message || 'No se pudieron subir las fotos.');
            if (status) status.textContent = '';
        } finally {
            submitButton.disabled = false;
        }
    });
}

/* Priorizamos ID para registros de MySQL y usamos slugify como fallback para
    tarjetas legacy que todavía solo tienen nombre. */
function obtenerRutaEvento(evento) {
    const slugEvento = evento.slug || slugify(evento.nombre);
    const idCatalogado = evento.id || window.sigturEventoIds?.[slugEvento];
    if (idCatalogado) {
        const pathname = window.location.pathname || '';
        const projectBase = pathname.includes('/php/') ? pathname.split('/php/')[0] : pathname.replace(/\/[^/]*$/, '');
        return `${projectBase}/php/evento.php?id=${encodeURIComponent(idCatalogado)}`;
    }
    /* Los destacados base se renderizan antes de enriquecer el catálogo con
       slug; derivamos la ruta desde el nombre para impedir undefined.php. */
    const slug = evento.slug || slugify(evento.nombre);
    const pathname = window.location.pathname || '';
    const enSubcarpetaEventos = pathname.includes('/php/eventos/');
    const prefijo = enSubcarpetaEventos ? '' : 'eventos/';
    return `${prefijo}${slug}.php`;
}

function tarjetaPrincipal(evento) {
    const slug = evento.slug || slugify(evento.nombre);
    const idCatalogado = evento.id || window.sigturEventoIds?.[slug];
    const itemKey = `evento-${idCatalogado || slug}`;
    return `
    <article class="evento-card" data-item-key="${itemKey}" data-engagement="like-only">
      <div class="evento-card-media">
        <img src="${evento.imagen}" alt="${evento.nombre}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
      </div>
      <div class="evento-card-body">
        <span class="categoria">${evento.categoria}${evento.tipoEntrada ? ` · ${evento.tipoEntrada}` : ''}</span>
        <h3>${evento.nombre}</h3>
        ${evento.lugar ? `<p class="lugar">${evento.lugar}</p>` : ""}
        ${evento.tipoEntrada === 'De Pago' && evento.precioTexto ? `<p class="precio-evento">${escaparHTML(evento.precioTexto)}</p>` : ""}
      </div>
      <div class="evento-card-action">
        <div class="fecha-protagonista" aria-label="Fecha del evento">
          <span class="fecha-mes">${evento.fechaMes || 'AGO'}</span>
          <span class="fecha-dia">${evento.fechaDia || '07'}</span>
        </div>
        <a href="${obtenerRutaEvento(evento)}" class="boton-amarillo">Ver mas</a>
      </div>
    </article>
  `;
}

function slugify(texto) {
    return (texto || '')
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '') || 'evento';
}

let calendarioMes = new Date();
let calendarioSeleccionado = null;
let calendarioAbierto = false;

function formatearFechaVisible(fecha) {
    return fecha.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatearFechaIso(fecha) {
    const year = fecha.getFullYear();
    const month = `${fecha.getMonth() + 1}`.padStart(2, '0');
    const day = `${fecha.getDate()}`.padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function obtenerIndicePrimerDia(dia) {
    return (dia.getDay() + 6) % 7;
}

function renderCalendario() {
    const label = document.getElementById('calendar-month-label');
    const days = document.getElementById('calendar-days');
    if (!label || !days) return;

    const año = calendarioMes.getFullYear();
    const mes = calendarioMes.getMonth();
    const primerDia = new Date(año, mes, 1);
    const ultimoDia = new Date(año, mes + 1, 0);
    const totalDias = ultimoDia.getDate();
    const espaciosIniciales = obtenerIndicePrimerDia(primerDia);

    label.textContent = primerDia.toLocaleDateString('es-ES', {
        month: 'long',
        year: 'numeric'
    }).replace(/^./, (char) => char.toUpperCase());

    const fragment = document.createDocumentFragment();

    for (let i = 0; i < espaciosIniciales; i += 1) {
        const vacio = document.createElement('button');
        vacio.type = 'button';
        vacio.className = 'calendar-day empty';
        vacio.disabled = true;
        fragment.appendChild(vacio);
    }

    for (let dia = 1; dia <= totalDias; dia += 1) {
        const botonDia = document.createElement('button');
        botonDia.type = 'button';
        botonDia.className = 'calendar-day';
        botonDia.textContent = dia;

        const fechaActual = new Date(año, mes, dia);
        if (calendarioSeleccionado && fechaActual.toDateString() === calendarioSeleccionado.toDateString()) {
            botonDia.classList.add('selected');
        }

        botonDia.addEventListener('click', () => {
            calendarioSeleccionado = fechaActual;
            document.getElementById('fecha-evento').value = formatearFechaIso(fechaActual);
            document.getElementById('fecha-visual').value = formatearFechaVisible(fechaActual);
            renderCalendario();
            cerrarCalendario();
        });

        fragment.appendChild(botonDia);
    }

    days.innerHTML = '';
    days.appendChild(fragment);
}

function actualizarFechaInput() {
    const inputVisible = document.getElementById('fecha-visual');
    const inputOculto = document.getElementById('fecha-evento');
    if (!inputVisible || !inputOculto) return;
    if (calendarioSeleccionado) {
        inputVisible.value = formatearFechaVisible(calendarioSeleccionado);
        inputOculto.value = formatearFechaIso(calendarioSeleccionado);
    } else {
        inputVisible.value = '';
        inputOculto.value = '';
    }
}

function abrirCalendario() {
    const dropdown = document.getElementById('calendar-dropdown');
    if (!dropdown) return;
    dropdown.classList.add('open');
    calendarioAbierto = true;
    renderCalendario();
}

function cerrarCalendario() {
    const dropdown = document.getElementById('calendar-dropdown');
    if (!dropdown) return;
    dropdown.classList.remove('open');
    calendarioAbierto = false;
}

function configurarFormularioEventos() {
    /* Este controlador coordina overlay, calendario, validaciones y envío al
       endpoint PHP; cada subcontrol actualiza una parte del formulario. */
    const overlay = document.getElementById('form-evento-overlay');
    const botonAbrir = document.getElementById('btn-crear-evento');
    const botonCerrar = document.getElementById('form-evento-close');
    const botonCancelar = document.getElementById('btn-cancelar-form');
    const tipoEntrada = document.getElementById('tipo-entrada');
    const precioField = document.getElementById('precio-field');
    const form = document.getElementById('form-nuevo-evento');
    const toggle = document.getElementById('date-picker-toggle');
    const prev = document.getElementById('calendar-prev');
    const next = document.getElementById('calendar-next');
    const descripcionToggle = document.getElementById('btn-agregar-descripcion');
    const fieldDescripcion = document.getElementById('field-descripcion');
    const descripcionInput = document.getElementById('evento-descripcion');
    const uploadInput = document.getElementById('evento-imagen');
    const uploadButton = document.getElementById('upload-button');
    const uploadZone = document.getElementById('upload-zone');
    const uploadPreview = document.getElementById('upload-preview');
    const uploadName = document.getElementById('upload-name');
    const errorBox = document.getElementById('form-error');
    const canalInput = document.getElementById('canal-notificacion');
    const canalButtons = document.querySelectorAll('.segmented-option');
    let uploadPreviewUrl = '';

    const abrirFormulario = () => {
        overlay?.classList.add('open');
        overlay?.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const cerrarFormulario = () => {
        overlay?.classList.remove('open');
        overlay?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    const mostrarError = (mensaje) => {
        if (errorBox) {
            errorBox.textContent = mensaje;
            errorBox.classList.add('show');
        }
    };

    const limpiarError = () => {
        errorBox?.classList.remove('show');
        errorBox && (errorBox.textContent = '');
    };

    const resetFormulario = () => {
        form?.reset();
        if (fieldDescripcion) fieldDescripcion.hidden = true;
        if (descripcionInput) descripcionInput.value = '';
        if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl);
        uploadPreviewUrl = '';
        if (uploadPreview) {
            uploadPreview.removeAttribute('src');
            uploadPreview.hidden = true;
        }
        if (uploadName) uploadName.textContent = 'Sin archivo seleccionado';
        uploadZone?.classList.remove('has-file');
        canalInput && (canalInput.value = 'Email');
        canalButtons.forEach((boton) => boton.classList.toggle('active', boton.dataset.channel === 'Email'));
        calendarioSeleccionado = null;
        calendarioMes = new Date();
        actualizarFechaInput();
        if (precioField) precioField.hidden = true;
        limpiarError();
    };

    botonAbrir?.addEventListener('click', () => {
        limpiarError();
        abrirFormulario();
    });
    botonCerrar?.addEventListener('click', cerrarFormulario);
    botonCancelar?.addEventListener('click', () => {
        resetFormulario();
        cerrarFormulario();
    });
    overlay?.addEventListener('click', (event) => {
        if (event.target === overlay) {
            resetFormulario();
            cerrarFormulario();
        }
    });

    descripcionToggle?.addEventListener('click', () => {
        const mostrar = fieldDescripcion?.hidden;
        if (fieldDescripcion) {
            fieldDescripcion.hidden = !mostrar;
        }
        if (mostrar) {
            descripcionInput?.focus();
            descripcionToggle.textContent = '- Ocultar descripción';
        } else {
            descripcionToggle.textContent = '+ Agregar descripción';
        }
    });

    canalButtons.forEach((boton) => {
        boton.addEventListener('click', () => {
            canalButtons.forEach((item) => item.classList.toggle('active', item === boton));
            if (canalInput) canalInput.value = boton.dataset.channel || 'Email';
        });
    });

    tipoEntrada?.addEventListener('change', () => {
        if (precioField) {
            precioField.hidden = tipoEntrada.value !== 'De Pago';
            if (tipoEntrada.value !== 'De Pago') {
                document.getElementById('evento-precio').value = '';
            }
        }
    });

    uploadButton?.addEventListener('click', () => uploadInput?.click());
    uploadInput?.addEventListener('change', () => {
        const file = uploadInput.files?.[0];
        if (uploadPreviewUrl) URL.revokeObjectURL(uploadPreviewUrl);
        uploadPreviewUrl = '';
        if (uploadName) {
            uploadName.textContent = file ? file.name : 'Sin archivo seleccionado';
        }
        uploadZone?.classList.toggle('has-file', Boolean(file));
        if (uploadPreview) {
            if (file) {
                uploadPreviewUrl = URL.createObjectURL(file);
                uploadPreview.src = uploadPreviewUrl;
                uploadPreview.hidden = false;
            } else {
                uploadPreview.removeAttribute('src');
                uploadPreview.hidden = true;
            }
        }
    });

    toggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        if (calendarioAbierto) {
            cerrarCalendario();
        } else {
            abrirCalendario();
        }
    });

    prev?.addEventListener('click', (event) => {
        event.stopPropagation();
        calendarioMes = new Date(calendarioMes.getFullYear(), calendarioMes.getMonth() - 1, 1);
        renderCalendario();
    });

    next?.addEventListener('click', (event) => {
        event.stopPropagation();
        calendarioMes = new Date(calendarioMes.getFullYear(), calendarioMes.getMonth() + 1, 1);
        renderCalendario();
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.date-picker')) {
            cerrarCalendario();
        }
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        limpiarError();

        const titulo = document.getElementById('evento-titulo')?.value.trim();
        const descripcion = document.getElementById('evento-descripcion')?.value.trim();
        const ubicacion = document.getElementById('evento-ubicacion')?.value.trim();
        const fecha = document.getElementById('fecha-evento')?.value;
        const hora = document.getElementById('evento-hora')?.value;
        const edadMinima = Number(document.getElementById('edad-minima')?.value || 0);
        const edadMaxima = Number(document.getElementById('edad-maxima')?.value || 99);
        const categoria = document.getElementById('categoria-evento')?.value || 'General';
        const tipoEntrada = document.getElementById('tipo-entrada')?.value || 'Gratuito';
        const precio = document.getElementById('evento-precio')?.value.trim() || '';
        const canal = document.getElementById('canal-notificacion')?.value || 'Email';
        const recordatorio = document.getElementById('recordatorio')?.value || '1 hora antes';
        const cuerpoCorreo = document.getElementById('correo-cuerpo')?.value.trim() || '';
        const archivo = document.getElementById('evento-imagen')?.files?.[0];

        if (!titulo) {
            mostrarError('El título del evento es obligatorio.');
            document.getElementById('evento-titulo')?.focus();
            return;
        }

        if (!ubicacion) {
            mostrarError('La ubicación del evento es obligatoria.');
            document.getElementById('evento-ubicacion')?.focus();
            return;
        }

        if (!fecha) {
            mostrarError('Debes seleccionar una fecha válida para el evento.');
            document.getElementById('fecha-visual')?.focus();
            return;
        }

        if (!hora) {
            mostrarError('Debes indicar la hora del evento.');
            document.getElementById('evento-hora')?.focus();
            return;
        }

        if (edadMinima > edadMaxima) {
            mostrarError('La edad mínima no puede ser mayor que la máxima.');
            document.getElementById('edad-minima')?.focus();
            return;
        }

        if (tipoEntrada === 'De Pago' && !precio) {
            mostrarError('Escribe el precio o valor de la entrada cuando eliges entrada de pago.');
            document.getElementById('evento-precio')?.focus();
            return;
        }

        // FormData conserva nombres y tipos de inputs para enviar el formulario sin serialización manual.
        const datosFormulario = new FormData(form);
        try {
            const respuesta = await fetch(form.action, { method: 'POST', body: datosFormulario });
            if (!respuesta.ok) throw new Error((await respuesta.text()) || 'No se pudo guardar el evento.');
            window.location.assign(respuesta.url);
        } catch (error) {
            mostrarError(error.message);
        }
    });
}

/* El filtro rápido compara texto, categoría, tipo de entrada y ubicación; el
    drawer usa además el endpoint SQL para filtros de rango. */
function filtrarEventosPrincipales() {
    const busqueda = document.getElementById('buscador-eventos')?.value.trim().toLowerCase() || '';
    return eventosPrincipales.filter((evento) => {
        const categoria = evento.categoria?.toString().toLowerCase() || '';
        const tipoEntrada = evento.tipoEntrada?.toString().toLowerCase() || '';
        const nombre = evento.nombre?.toString().toLowerCase() || '';
        const lugar = evento.lugar?.toString().toLowerCase() || '';
        const matchesFiltro = eventoFiltro === 'todos' || categoria.includes(eventoFiltro) || tipoEntrada.includes(eventoFiltro.replace('-', ' ')) || nombre.includes(eventoFiltro) || lugar.includes(eventoFiltro);
        const matchesBusqueda = !busqueda || nombre.includes(busqueda) || categoria.includes(busqueda) || lugar.includes(busqueda);
        return matchesFiltro && matchesBusqueda;
    });
}

function renderEventosPrincipales() {
    // El grid se vuelve a pintar después de búsqueda, filtros rápidos o respuesta AJAX del drawer.
    const contenedor = document.getElementById('eventos-principales-contenedor');
    if (!contenedor) return;

    const eventos = filtrarEventosPrincipales();
    if (!eventos.length) {
        contenedor.innerHTML = '<p class="sin-resultados">No se encontraron eventos con esa búsqueda.</p>';
        actualizarVisibilidadListaEventos(contenedor, document.getElementById('eventos-principales-toggle'), false);
        return;
    }

    contenedor.innerHTML = eventos.map((evento) => tarjetaPrincipal(evento)).join('');
    actualizarVisibilidadListaEventos(
        contenedor,
        document.getElementById('eventos-principales-toggle'),
        listasEventosExpandidas.principales,
        'principales'
    );
    if (typeof window.initInteractiveCards === 'function') {
        window.initInteractiveCards(contenedor);
    }
}

function actualizarVisibilidadListaEventos(contenedor, boton, expandida, lista = null) {
    if (!contenedor || !boton) return;
    const tarjetas = [...contenedor.querySelectorAll('.evento-card, .evento-card-pasado')];
    const mostrarBoton = tarjetas.length > maxEventosVisibles;
    boton.hidden = !mostrarBoton;
    if (!mostrarBoton) return;

    tarjetas.forEach((tarjeta, indice) => {
        tarjeta.hidden = !expandida && indice >= maxEventosVisibles;
    });
    boton.textContent = expandida ? 'Ver menos' : 'Ver más';
    boton.setAttribute('aria-expanded', String(expandida));
    if (lista) {
        boton.dataset.eventList = lista;
    }
}

function tarjetaAnterior(evento) {
    const ruta = evento.id
        ? `evento.php?id=${encodeURIComponent(evento.id)}`
        : `evento-pasado.php?id=${encodeURIComponent(evento.slug || slugify(evento.nombre))}`;
    const itemKey = `evento-${evento.id || evento.slug || slugify(evento.nombre)}`;
    return `
        <article class="evento-card-pasado" data-item-key="${itemKey}" data-engagement="like-only">
            <a href="${ruta}" class="evento-card-pasado__link">
            <div class="card-img-container">
        <img src="${evento.imagen}" alt="${evento.nombre}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
      </div>
      <div class="anteriores-texto">
        <span class="categoria">${evento.categoria}</span>
        <h3>${evento.nombre}</h3>
        ${evento.lugar ? `<p class="lugar">${evento.lugar}</p>` : ""}
      </div>
            <div class="anteriores-action">
                <span class="evento-status">FINALIZADO</span>
                <span class="boton-amarillo">Ver más</span>
            </div>
            </a>
        </article>
  `;
}

function renderEventosAnteriores() {
    const contenedor = document.getElementById('anteriores-contenedor');
    if (!contenedor) return;

    contenedor.innerHTML = eventosAnterioresPersistidos.map((evento) => tarjetaAnterior(evento)).join('');
    actualizarVisibilidadListaEventos(
        contenedor,
        document.getElementById('eventos-anteriores-toggle'),
        listasEventosExpandidas.anteriores,
        'anteriores'
    );
    if (typeof window.initInteractiveCards === 'function') {
        window.initInteractiveCards(contenedor);
    }
}

function actualizarBotonesActivos(selector, filtro) {
    document.querySelectorAll(selector).forEach((boton) => {
        const valor = boton.dataset.filtro || boton.dataset.galeriaFiltro || 'todos';
        boton.classList.toggle('activa', valor === filtro);
    });
}

function configurarFiltrosEventos() {
    document.querySelectorAll('[data-filtro]').forEach((boton) => {
        boton.addEventListener('click', () => {
            eventoFiltro = boton.dataset.filtro || 'todos';
            actualizarBotonesActivos('[data-filtro]', eventoFiltro);
            renderEventosPrincipales();
        });
    });

    document.getElementById('buscador-eventos')?.addEventListener('input', () => {
        renderEventosPrincipales();
    });
}

// URLSearchParams serializa controles vacíos sin concatenar query strings manualmente.
function obtenerFiltrosDrawer() {
    return new URLSearchParams({
        ajax: '1',
        texto: document.getElementById('filtro-texto')?.value.trim() || '',
        categoria: document.getElementById('filtro-categoria')?.value || '',
        tipo_entrada: document.getElementById('filtro-tipo')?.value || '',
        min_precio: document.getElementById('filtro-min-precio')?.value || '',
        max_precio: document.getElementById('filtro-max-precio')?.value || '',
        desde: document.getElementById('filtro-desde')?.value || '',
        hasta: document.getElementById('filtro-hasta')?.value || ''
    });
}

function mapearEventoPersistido(evento) {
    return {
        id: Number(evento.id),
        nombre: evento.titulo,
        slug: evento.slug,
        descripcion: evento.descripcion,
        categoria: evento.categoria,
        tipoEntrada: evento.tipo_entrada || 'Gratuito',
        precio: evento.precio,
        precioTexto: evento.precio_texto || (evento.precio !== null && evento.precio !== undefined ? `$${evento.precio}` : ''),
        lugar: evento.ubicacion,
        fecha: evento.fecha,
        imagen: evento.imagen_portada || '../img/porco.avif'
    };
}

function configurarDrawerFiltros() {
    /* El drawer mantiene el formulario montado y alterna una clase para animar
       transform; así no recalcula el layout de las tarjetas al abrirse. */
    const drawer = document.getElementById('filtros-drawer');
    const abrir = document.getElementById('btn-filtros');
    const cerrar = drawer?.querySelectorAll('[data-cerrar-filtros]') || [];
    const aplicar = document.getElementById('aplicar-filtros');
    const limpiar = document.getElementById('limpiar-filtros');
    if (!drawer || !abrir) return;

    const cambiarDrawer = (abierto) => {
        /* aria-hidden y la clase CSS sincronizan accesibilidad con la animación
           transform: el panel se desliza sin desmontarse del DOM. */
        drawer.classList.toggle('open', abierto);
        drawer.setAttribute('aria-hidden', String(!abierto));
        abrir.setAttribute('aria-expanded', String(abierto));
    };

    abrir.addEventListener('click', () => cambiarDrawer(true));
    cerrar.forEach((elemento) => elemento.addEventListener('click', () => cambiarDrawer(false)));
    aplicar?.addEventListener('click', async () => {
        try {
            /* Fetch consulta nuevamente eventos.php con filtros; el servidor
               aplica WHERE parametrizados y devuelve solo filas publicables. */
            const respuesta = await fetch(`eventos.php?${obtenerFiltrosDrawer().toString()}`, { headers: { Accept: 'application/json' } });
            if (!respuesta.ok) throw new Error('No se pudieron aplicar los filtros.');
            const resultados = (await respuesta.json()).map(mapearEventoPersistido);
            eventosPrincipales = resultados;
            renderEventosPrincipales();
            cambiarDrawer(false);
        } catch (error) {
            window.sigturAlert?.(error.message);
        }
    });
    limpiar?.addEventListener('click', () => {
        drawer.querySelectorAll('input, select').forEach((campo) => { campo.value = ''; });
        eventosPrincipales = (() => {
            const slugsPersistidos = new Set([...eventosPersistidos, ...eventosAnterioresPersistidos].map((evento) => evento.slug));
            return [...eventosPersistidos, ...[...datos.destacados, ...datos.deportivos, ...datos.discotecas, ...datos.competencias].filter((evento) => !slugsPersistidos.has(slugify(evento.nombre)))];
        })();
        renderEventosPrincipales();
    });
}

function configurarFiltrosGaleria() {
    document.querySelectorAll('[data-galeria-filtro]').forEach((boton) => {
        boton.addEventListener('click', () => {
            galeriaFiltro = boton.dataset.galeriaFiltro || 'todos';
            actualizarBotonesActivos('[data-galeria-filtro]', galeriaFiltro);
            renderGaleria();
        });
    });

    document.getElementById('galeria-search')?.addEventListener('input', (event) => {
        galeriaBusqueda = event.target.value;
        renderGaleria();
    });
}

let lightboxIndex = 0;

function actualizarLightbox() {
    const item = galeria[lightboxIndex];
    const imagen = document.getElementById("lightbox-image");
    const caption = document.getElementById("lightbox-caption");
    const title = document.getElementById('lightbox-title');
    const description = document.getElementById('lightbox-description');
    const date = document.getElementById('lightbox-date');
    if (!item || !imagen || !caption) return;
    imagen.src = item.imagen;
    // Los datos viajan desde el objeto de galería hasta los nodos visibles del
    // modal en cada cambio de foto, evitando títulos y descripciones obsoletos.
    imagen.alt = item.titulo || `Imagen de galería ${lightboxIndex + 1}`;
    caption.textContent = `Imagen ${lightboxIndex + 1} de ${galeria.length}`;
    if (title) title.textContent = item.titulo || 'Cobertura de Salto';
    if (description) description.textContent = item.descripcion || 'Imágenes de lugares y actividades de la ciudad.';
    if (date) date.textContent = item.fecha || 'Publicado recientemente';

    const engagementCard = document.getElementById('lightbox-engagement-card');
    if (engagementCard) {
        engagementCard.dataset.itemKey = `galeria-${item.imagen}`;
        engagementCard.querySelector('.card-engagement')?.remove();
        window.initInteractiveCards?.(engagementCard);
    }
}

/*
  El lightbox abre la imagen seleccionada en un overlay oscuro.
  El CSS controla la altura máxima de la imagen, dejando espacio
  vertical arriba y abajo para que no ocupe todo el alto de la pantalla.
*/
function abrirLightbox(indice) {
    lightboxIndex = indice;
    actualizarLightbox();
    document.getElementById("lightbox").classList.add("open");
    document.body.classList.add('lightbox-open');
}

function cerrarLightbox() {
    document.getElementById("lightbox").classList.remove("open");
    document.body.classList.remove('lightbox-open');
}

function cambiarImagen(delta) {
  // // Uso de modulo para envolver índices (wrap-around circular)
  lightboxIndex = (lightboxIndex + galeria.length + delta) % galeria.length;
    actualizarLightbox();
}

function configurarMapaEventos() {
    const mapList = document.getElementById('mapa-eventos-lugares');
    const mapToggle = document.getElementById('mapa-eventos-toggle');
    if (!mapList || !mapToggle) return;

    const ubicaciones = new Map();
    const todosLosEventos = [
        ...eventosPersistidos,
        ...eventosAnterioresPersistidos
    ];

    todosLosEventos.forEach((evento) => {
        const lugar = String(evento.lugar || '').trim();
        const clave = lugar
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, ' ')
            .toLocaleLowerCase();
        if (lugar && !ubicaciones.has(clave)) {
            ubicaciones.set(clave, lugar);
        }
    });

    const nombresUbicaciones = [...ubicaciones.values()].sort((a, b) => a.localeCompare(b, 'es'));
    const lugares = nombresUbicaciones.map((nombre) => {
        const enlace = document.createElement('a');
        enlace.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`${nombre}, Salto, Uruguay}`)}`;
        enlace.target = '_blank';
        enlace.rel = 'noopener';
        enlace.textContent = nombre;
        return enlace;
    });

    mapList.replaceChildren(...lugares, mapToggle);
    if (lugares.length <= 5) return;

    lugares.forEach((lugar, index) => {
        lugar.hidden = index >= 5;
    });
    mapToggle.hidden = false;
    mapToggle.addEventListener('click', () => {
        const expanded = mapToggle.getAttribute('aria-expanded') === 'true';
        lugares.forEach((lugar, index) => {
            lugar.hidden = expanded && index >= 5;
        });
        mapToggle.setAttribute('aria-expanded', String(!expanded));
        mapToggle.textContent = expanded ? 'Ver más' : 'Ver menos';
    });
}

function init() {
    for (const nombre in datos) renderSeccion(nombre);

    configurarMapaEventos();
    document.querySelectorAll(".flecha").forEach((boton) => {
        boton.addEventListener("click", () => {
            const nombre = boton.dataset.carrusel;
            const direccion = boton.classList.contains("derecha") ? 1 : -1;
            setPos(nombre, pos[nombre] + direccion);
        });
    });

    configurarFiltrosEventos();
    document.querySelectorAll('[data-event-list]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const lista = boton.dataset.eventList;
            listasEventosExpandidas[lista] = !listasEventosExpandidas[lista];
            const esAnteriores = lista === 'anteriores';
            actualizarVisibilidadListaEventos(
                document.getElementById(esAnteriores ? 'anteriores-contenedor' : 'eventos-principales-contenedor'),
                boton,
                listasEventosExpandidas[lista],
                lista
            );
        });
    });
    configurarDrawerFiltros();
    configurarFiltrosGaleria();
    renderEventosPrincipales();
    renderEventosAnteriores();
    renderGaleria();
    configurarSubidaGaleria();

    document.getElementById("lightbox-close")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-backdrop")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-prev")?.addEventListener("click", () => cambiarImagen(-1));
    document.getElementById("lightbox-next")?.addEventListener("click", () => cambiarImagen(1));
    configurarFormularioEventos();
    renderCalendario();
    actualizarFechaInput();
}

document.addEventListener("DOMContentLoaded", init);
