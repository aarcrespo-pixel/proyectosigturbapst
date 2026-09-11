/*
  Aca definimos los datos que se muestran en cada carrusel.
  Cada propiedad como "destacados", "deportivos" o "discotecas"
  coincide con un bloque HTML que tiene un contenedor llamado
  `${nombre}-contenedor` y un indicador `${nombre}-indicadores`.
*/
const eventosDestacados = [
    { nombre: "Carrera de Bicicleta", categoria: "Carreras", lugar: "Costanera Norte", imagen: "../img/moto_cross.jpg" },
    { nombre: "La Ferne", categoria: "Discoteca", lugar: "Costanera Norte", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzXMkEsvlia-KLamghIoD7xL9Rpz72SnkxikCvS_uH0Q&s" },
    { nombre: "Porco Negro", categoria: "Discoteca", lugar: "Av. Apolón de Mirbek esquina Av. José Enrique Rodó.", imagen: "../img/porco.avif" },
    { nombre: "ExpoSalto", categoria: "Feria", lugar: "Hipódromo de Salto", imagen: "../img/exposalto.jpeg" },
    { nombre: "LaFosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "../img/fosa.webp" }
];

const listadoEventos = {
    deportivos: [
        { nombre: "Surf y Kayak", categoria: "Aventura", lugar: "Playa Salto", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Beach Volley", categoria: "Competencia", lugar: "Playa Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTturliqhgPakucL8C4kedxXyT6XEhQKkcXrZRh-af6MZf5zDZvjFQPZmV2TLieDskgPNvK3PyipLTjJc6wCuBY4T-08gd08muSeZ8sHo8&s=10" },
        { nombre: "Circuito en Bicicleta", categoria: "Deportivo", lugar: "Calle Artigas", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10" },
        { nombre: "Rally", categoria: "Carrera", lugar: "Av. Horacio Quiroga 9382", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10" },
        { nombre: "Futbol X5", categoria: "Deportivo", lugar: "Plaza de Deportes Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLGcgcjLaLtUFzXSfs5EzS0x3c23cfZtXgew3MzIyfJWBgxce4Bah7vhA&s=10" }
    ],
    discotecas: [
        { nombre: "Halloween en Polo", categoria: "Disfraces", lugar: "Polo Club", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10" },
        { nombre: "La Bambola", categoria: "Discoteca", lugar: "Costanera Sur 1535", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh8BnVQ1qdPDXqoHVHylKrGhwk3guImN0d1ZRYbBNmSXoKPhqmCE-pMsE&s=10" },
    ],
    competencias: [
        { nombre: "Streetball Salto", categoria: "Deportivo", lugar: "Polideportivo Círculo SP", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0pAIE9pTlfySurzoR4nHmaJctr45FdL9iY9xVQXMfhhcFj8Gn_pXJ5z99&s=10" },
        { nombre: "Campeonato de Pesca", categoria: "Naturaleza", lugar: "Costanera Sur", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Ajedrez", categoria: "Mental", lugar: "Plaza de Deportes", imagen: "https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg" },
        { nombre: "Copa de Natación", categoria: "Acuatico", lugar: "Playa Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTfYqzP-3hljVNvWo0X2mA8fxJY9EOZKFXquVDdbwRnvST-uFmxjLEum-d&s=10" }
    ],
    anteriores: [
        { nombre: "Festival de la Naranja", categoria: "Cultura", lugar: "Plaza Artigas", imagen: "../img/festival_naranja.jpg" },
        { nombre: "Carrera Nocturna", categoria: "Deportivo", lugar: "Costanera Norte", imagen: "../img/carrera_noche.webp" },
        { nombre: "Muestra de Danza", categoria: "Cultura", lugar: "Centro Cultural", imagen: "../img/danza.jpg" },
        { nombre: "Feria de Emprendedores", categoria: "Local", lugar: "Mercado Central", imagen: "../img/feria_emprendedores.jpg" },
        { nombre: "La Fosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLYlimLpyzEAGbkjhkZr3cPP7pON89wJlOJPhKhGMLPM6xUx1nw3Pfj4s&s=10" }
    ]
};

const datos = {
    destacados: eventosDestacados,
    ...listadoEventos
};

const galeria = [
    { imagen: "https://upload.wikimedia.org/wikipedia/commons/e/ef/Parque_Solari_Estatua.JPG" },
    { imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwYxQbLi8Q2nRVZPDP9CwDkcYwQsKZ1R2EBF2XXy8nGdPUTnexH4SCTce8&s=10" },
    { imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg" },
    { imagen: "https://alba-uy-sarandi.cdn.mediatiquepress.com/wp-content/uploads/2024/08/Life_Cinemas_2.webp" },
    { imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
    { imagen: "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/84/ae/39/trouville-pizzeria-y.jpg?w=1200&h=1200&s=1" }
];

// Construir lista principal con slugs únicos para cada evento
let eventosPrincipales = (() => {
  const list = [
    ...datos.destacados,
    ...datos.deportivos,
    ...datos.discotecas,
    ...datos.competencias
  ];
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
    const fecha = fechasProtagonistas[index % fechasProtagonistas.length] || { mes: 'AGO', dia: '07' };
    return Object.assign({}, e, { slug, fechaMes: fecha.mes, fechaDia: fecha.dia });
  });
})();

let eventoFiltro = 'todos';
let galeriaFiltro = 'todos';
let galeriaBusqueda = '';

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
    <article class="tarjeta${activa ? ' activa' : ''}" data-engagement="like-only" role="button" tabindex="0" aria-label="Evento ${evento.nombre}">
    <div class="imagen-tarjeta">
      <img src="${evento.imagen}" alt="${evento.nombre}" loading="lazy" onerror="this.onerror=null;this.src='/img/porco.avif';">
    </div>
    <div class="detalles-tarjeta">
      <span class="categoria">${evento.categoria}</span>
      <h3>${evento.nombre}</h3>
      ${evento.lugar ? `<span class="lugar">${evento.lugar}</span>` : ""}
    </div>
  </article>
`;

/*
  Actualiza el contenido del carrusel para la sección indicada.
  La variable nombre viene de los bloques HTML que usan ids como
  `destacados-contenedor`, `deportivos-contenedor`, etc.
*/
function renderSeccion(nombre) {
    const lista = datos[nombre];
    const actual = pos[nombre];
  // // Cálculo de ventana visible con protección de límites (Math.max/Math.min)
  const inicio = Math.max(0, Math.min(actual, lista.length - maxPorPagina));
  // // Uso de slice + map + join para construir HTML de las tarjetas visibles
  const html = lista.slice(inicio, inicio + maxPorPagina)
    .map((evento, index) => tarjeta(evento, inicio + index === actual))
    .join("");

    const contenedor = document.getElementById(`${nombre}-contenedor`);
    if (!contenedor) return;

    contenedor.innerHTML = html;

    if (typeof window.initInteractiveCards === 'function') {
      window.initInteractiveCards(contenedor);
    }

    contenedor.querySelectorAll('.tarjeta').forEach((card) => {
      const noAction = (event) => {
        event.preventDefault();
        event.stopPropagation();
      };

      card.addEventListener('click', noAction);
      card.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          noAction(event);
        }
      });
    });

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
    const contenedor = document.getElementById("galeria-contenedor");
    if (!contenedor) return;

    const textoBusqueda = galeriaBusqueda.trim().toLowerCase();
    const fotosFiltradas = galeria
        .map((item, index) => ({ ...item, originalIndex: index }))
        .filter((item) => {
            const descripcion = (item.imagen || "").toLowerCase();
            const coincideCategoria = galeriaFiltro === 'todos' || (item.categoria && item.categoria.toLowerCase().includes(galeriaFiltro));
            const coincideTexto = !textoBusqueda || descripcion.includes(textoBusqueda);
            return coincideCategoria && coincideTexto;
        });

    contenedor.innerHTML = fotosFiltradas
        .map((item, index) => `
            <article class="imagen-galeria galeria-item" data-index="${index}" data-original-index="${item.originalIndex}">
        <img src="${item.imagen}" alt="Galería ${index + 1}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
            </article>
    `)
        .join("");

    if (typeof window.initInteractiveCards === 'function') {
        window.initInteractiveCards(contenedor);
    }

    document.querySelectorAll(".galeria-item").forEach((boton) => {
        boton.addEventListener("click", () => {
            const indice = Number(boton.dataset.originalIndex || boton.dataset.index);
            abrirLightbox(indice);
        });
    });
}

function obtenerRutaEvento(evento) {
    const pathname = window.location.pathname || '';
    const enSubcarpetaEventos = pathname.includes('/php/eventos/');
    const prefijo = enSubcarpetaEventos ? '' : 'eventos/';
    return `${prefijo}${evento.slug}.php`;
}

function tarjetaPrincipal(evento) {
    const itemKey = evento.slug ? `evento-${evento.slug}` : `evento-${slugify(evento.nombre)}`;
    return `
    <article class="evento-card" data-item-key="${itemKey}" data-engagement="like-only">
      <div class="evento-card-media">
        <img src="${evento.imagen}" alt="${evento.nombre}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
      </div>
      <div class="evento-card-body">
        <span class="categoria">${evento.categoria}</span>
        <h3>${evento.nombre}</h3>
        ${evento.lugar ? `<p class="lugar">${evento.lugar}</p>` : ""}
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
    const uploadZone = document.getElementById('upload-zone');
    const uploadName = document.getElementById('upload-name');
    const errorBox = document.getElementById('form-error');
    const canalInput = document.getElementById('canal-notificacion');
    const canalButtons = document.querySelectorAll('.segmented-option');

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

    uploadInput?.addEventListener('change', () => {
        const file = uploadInput.files?.[0];
        if (uploadName) {
            uploadName.textContent = file ? file.name : 'Sin archivo seleccionado';
        }
        uploadZone?.classList.toggle('has-file', Boolean(file));
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

    form?.addEventListener('submit', (event) => {
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
        const imagenSrc = archivo ? URL.createObjectURL(archivo) : '';

        if (!titulo) {
            mostrarError('El título del evento es obligatorio.');
            document.getElementById('evento-titulo')?.focus();
            return;
        }

        if (!descripcion) {
            mostrarError('La descripción del evento es obligatoria.');
            descripcionInput?.focus();
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
            mostrarError('Escribe el precio del evento cuando eliges entrada de pago.');
            document.getElementById('evento-precio')?.focus();
            return;
        }

        eventosPrincipales.unshift({
            nombre: titulo,
            categoria,
            lugar: ubicacion,
            imagen: imagenSrc,
            fecha: `${fecha} · ${hora}`,
            descripcion,
            edadMinima,
            edadMaxima,
            canal,
            recordatorio,
            cuerpoCorreo,
            slug: slugify(titulo)
        });

        renderEventosPrincipales();
        resetFormulario();
        cerrarFormulario();
        window.alert('Evento creado con éxito.');
    });
}

function filtrarEventosPrincipales() {
    const busqueda = document.getElementById('buscador-eventos')?.value.trim().toLowerCase() || '';
    return eventosPrincipales.filter((evento) => {
        const categoria = evento.categoria?.toString().toLowerCase() || '';
        const nombre = evento.nombre?.toString().toLowerCase() || '';
        const lugar = evento.lugar?.toString().toLowerCase() || '';
        const matchesFiltro = eventoFiltro === 'todos' || categoria.includes(eventoFiltro) || nombre.includes(eventoFiltro) || lugar.includes(eventoFiltro);
        const matchesBusqueda = !busqueda || nombre.includes(busqueda) || categoria.includes(busqueda) || lugar.includes(busqueda);
        return matchesFiltro && matchesBusqueda;
    });
}

function renderEventosPrincipales() {
    const contenedor = document.getElementById('eventos-principales-contenedor');
    if (!contenedor) return;

    const eventos = filtrarEventosPrincipales();
    if (!eventos.length) {
        contenedor.innerHTML = '<p class="sin-resultados">No se encontraron eventos con esa búsqueda.</p>';
        return;
    }

    contenedor.innerHTML = eventos.map((evento) => tarjetaPrincipal(evento)).join('');
    if (typeof window.initInteractiveCards === 'function') {
        window.initInteractiveCards(contenedor);
    }
}

function tarjetaAnterior(evento) {
    const ruta = obtenerRutaEvento(evento);
    const itemKey = evento.slug ? `evento-${evento.slug}` : `evento-${slugify(evento.nombre)}`;
    return `
    <a href="${ruta}" class="anteriores-card" data-item-key="${itemKey}" data-engagement="like-only">
      <div class="anteriores-media">
        <img src="${evento.imagen}" alt="${evento.nombre}" loading="lazy" onerror="this.onerror=null;this.src='../img/porco.avif';">
      </div>
      <div class="anteriores-texto">
        <span class="categoria">${evento.categoria}</span>
        <h3>${evento.nombre}</h3>
        ${evento.lugar ? `<p class="lugar">${evento.lugar}</p>` : ""}
      </div>
            <div class="anteriores-action">
                <span class="boton-amarillo">Ver más</span>
            </div>
    </a>
  `;
}

function renderEventosAnteriores() {
    const contenedor = document.getElementById('anteriores-contenedor');
    if (!contenedor) return;

    contenedor.innerHTML = datos.anteriores.map((evento) => tarjetaAnterior(evento)).join('');
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
    if (!item || !imagen || !caption) return;
    imagen.src = item.imagen;
    imagen.alt = `Imagen de galería ${lightboxIndex + 1}`;
    caption.textContent = `Imagen ${lightboxIndex + 1} de ${galeria.length}`;

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

function init() {
    for (const nombre in datos) renderSeccion(nombre);

    document.querySelectorAll(".flecha").forEach((boton) => {
        boton.addEventListener("click", () => {
            const nombre = boton.dataset.carrusel;
            const direccion = boton.classList.contains("derecha") ? 1 : -1;
            setPos(nombre, pos[nombre] + direccion);
        });
    });

    configurarFiltrosEventos();
    configurarFiltrosGaleria();
    renderEventosPrincipales();
    renderEventosAnteriores();
    renderGaleria();

    document.getElementById("lightbox-close")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-backdrop")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-prev")?.addEventListener("click", () => cambiarImagen(-1));
    document.getElementById("lightbox-next")?.addEventListener("click", () => cambiarImagen(1));
    configurarFormularioEventos();
    renderCalendario();
    actualizarFechaInput();
}

document.addEventListener("DOMContentLoaded", init);
