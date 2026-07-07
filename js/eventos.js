/*
  Aquí definimos los datos que se muestran en cada carrusel.
  Cada propiedad como "destacados", "deportivos" o "discotecas"
  coincide con un bloque HTML que tiene un contenedor llamado
  `${nombre}-contenedor` y un indicador `${nombre}-indicadores`.
*/
const datos = {
    destacados: [
        { nombre: "MotoCross", categoria: "Carrera", lugar: "Costanera Norte", imagen: "/img/moto_cross.jpg" },
        { nombre: "La Ferne", categoria: "Discoteca", lugar: "Costanera Norte", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDFLlNliF-KGMj2Lga3JGKfp0Hk3AyFP84cyTbf776SuztpI-AGEg7Ca0&s=10" },
        { nombre: "Porco Negro", categoria: "Discoteca", lugar: "Av. Apolón de Mirbek esquina Av. José Enrique Rodó.", imagen: "/img/porco.avif" },
        { nombre: "ExpoSalto", categoria: "Feria", lugar: "Hipódromo de Salto", imagen: "/img/exposalto.jpeg" },
        { nombre: "LaFosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "/img/fosa.webp" }
    ],
    deportivos: [
        { nombre: "Maratón de la Ciudad", categoria: "Carrera", imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg" },
        { nombre: "Surf y Kayak", categoria: "Aventura", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Beach Volley", categoria: "Competencia", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDFLlNliF-KGMj2Lga3JGKfp0Hk3AyFP84cyTbf776SuztpI-AGEg7Ca0&s=10" },
        { nombre: "Circuito en Bicicleta", categoria: "Deportivo", imagen: "https://upload.wikimedia.org/wikipedia/commons/e/ef/Parque_Solari_Estatua.JPG" },
        { nombre: "Triatlón Familiar", categoria: "Resistencia", imagen: "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/84/ae/39/trouville-pizzeria-y.jpg?w=1200&h=1200&s=1" },
        { nombre: "Clase de Yoga", categoria: "Salud", imagen: "https://alba-uy-sarandi.cdn.mediatiquepress.com/wp-content/uploads/2024/08/Life_Cinemas_2.webp" }
    ],
    discotecas: [
        { nombre: "Noche Electro", categoria: "Música", imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg" },
        { nombre: "Fiesta Tropical", categoria: "Baile", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "DJ en Vivo", categoria: "Discoteca", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDFLlNliF-KGMj2Lga3JGKfp0Hk3AyFP84cyTbf776SuztpI-AGEg7Ca0&s=10" },
        { nombre: "Porco Negro", categoria: "Discoteca", imagen: "/img/porco.avif" },
        { nombre: "Ferne", categoria: "Discoteca", imagen: "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/84/ae/39/trouville-pizzeria-y.jpg?w=1200&h=1200&s=1" }
    ],
    competencias: [
        { nombre: "Streetball Salto", categoria: "Deportivo", imagen: "https://alba-uy-sarandi.cdn.mediatiquepress.com/wp-content/uploads/2024/08/Life_Cinemas_2.webp" },
        { nombre: "Festival de Skate", categoria: "Urbano", imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg" },
        { nombre: "Campeonato de Pesca", categoria: "Naturaleza", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Ajedrez", categoria: "Mental", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTDFLlNliF-KGMj2Lga3JGKfp0Hk3AyFP84cyTbf776SuztpI-AGEg7Ca0&s=10" },
        { nombre: "Copa de Natación", categoria: "Acuatico", imagen: "https://upload.wikimedia.org/wikipedia/commons/e/ef/Parque_Solari_Estatua.JPG" }
    ],
    anteriores: [
        { nombre: "Festival de la Naranja", categoria: "Cultura", lugar: "Plaza Artigas", imagen: "/img/festival_naranja.jpg" },
        { nombre: "Carrera Nocturna", categoria: "Deportivo", lugar: "Costanera Norte", imagen: "/img/carrera_noche.webp" },
        { nombre: "Muestra de Danza", categoria: "Cultura", lugar: "Centro Cultural", imagen: "/img/danza.jpg" },
        { nombre: "Feria de Emprendedores", categoria: "Local", lugar: "Mercado Central", imagen: "/img/feria_emprendedores.jpg" },
        { nombre: "Toy Story 4", categoria: "Arte", lugar: "Cine Sarandi", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLYlimLpyzEAGbkjhkZr3cPP7pON89wJlOJPhKhGMLPM6xUx1nw3Pfj4s&s=10" }
    ]
};

const galeria = [
    { imagen: "https://upload.wikimedia.org/wikipedia/commons/e/ef/Parque_Solari_Estatua.JPG" },
    { imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwYxQbLi8Q2nRVZPDP9CwDkcYwQsKZ1R2EBF2XXy8nGdPUTnexH4SCTce8&s=10" },
    { imagen: "https://www.infoturismo19.com.uy/wp-content/uploads/2023/07/SHOPPING-4-2.jpg" },
    { imagen: "https://alba-uy-sarandi.cdn.mediatiquepress.com/wp-content/uploads/2024/08/Life_Cinemas_2.webp" },
    { imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
    { imagen: "https://dynamic-media-cdn.tripadvisor.com/media/photo-o/23/84/ae/39/trouville-pizzeria-y.jpg?w=1200&h=1200&s=1" }
];

const pos = {};
for (const nombre in datos) pos[nombre] = 0;
const maxPorPagina = 3;

/*
  Crea el HTML de cada tarjeta de evento.
  El parámetro activa se usa para marcar visualmente el evento seleccionado.
*/
const tarjeta = (evento, activa = false) => `
  <article class="tarjeta${activa ? ' activa' : ''}">
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
    const inicio = Math.max(0, Math.min(actual, lista.length - maxPorPagina));
    const html = lista.slice(inicio, inicio + maxPorPagina)
        .map((evento, index) => tarjeta(evento, inicio + index === actual))
        .join("");

    const contenedor = document.getElementById(`${nombre}-contenedor`);
    if (!contenedor) return;

    contenedor.innerHTML = html;
    document.querySelector(`.flecha.izquierda[data-carrusel="${nombre}"]`)?.classList.toggle("oculta", actual === 0);
    document.querySelector(`.flecha.derecha[data-carrusel="${nombre}"]`)?.classList.toggle("oculta", actual === lista.length - 1);

    const indicadores = document.getElementById(`${nombre}-indicadores`);
    if (!indicadores) return;

    indicadores.innerHTML = lista
        .map((_, i) => `<span class="indicador-netflix${i === actual ? " activo" : ""}" data-carrusel="${nombre}" data-index="${i}"></span>`)
        .join("");

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

    contenedor.innerHTML = galeria
        .map((item, index) => `
      <button class="imagen-galeria galeria-item" type="button" data-index="${index}">
        <img src="${item.imagen}" alt="Galería ${index + 1}" loading="lazy" onerror="this.onerror=null;this.src='/img/porco.avif';">
      </button>
    `)
        .join("");

    document.querySelectorAll(".galeria-item").forEach((boton) => {
        boton.addEventListener("click", () => {
            const indice = Number(boton.dataset.index);
            abrirLightbox(indice);
        });
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
}

function cerrarLightbox() {
    document.getElementById("lightbox").classList.remove("open");
}

function cambiarImagen(delta) {
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

    renderGaleria();

    document.getElementById("lightbox-close")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-backdrop")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-prev")?.addEventListener("click", () => cambiarImagen(-1));
    document.getElementById("lightbox-next")?.addEventListener("click", () => cambiarImagen(1));
}

document.addEventListener("DOMContentLoaded", init);
