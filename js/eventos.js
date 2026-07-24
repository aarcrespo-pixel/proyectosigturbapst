/*
  Aca definimos los datos que se muestran en cada carrusel.
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
        { nombre: "Surf y Kayak", categoria: "Aventura", lugar: "Playa Salto", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Beach Volley", categoria: "Competencia", lugar: "Playa Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTturliqhgPakucL8C4kedxXyT6XEhQKkcXrZRh-af6MZf5zDZvjFQPZmV2TLieDskgPNvK3PyipLTjJc6wCuBY4T-08gd08muSeZ8sHo8&s=10" },
        { nombre: "Circuito en Bicicleta", categoria: "Deportivo", lugar: "Calle Artigas", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ7b44_MpWq3Wzray5_wVaB-4meo0Tam3gwbPNK_1AJ74I1Xo5EhmUKhTIE&s=10" },
        { nombre: "Rally", categoria: "Carrera", lugar: "Av. Horacio Quiroga 9382", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvOwKgEnUpg8aNJEn8g9CuGqAW1ofcfyKSsld8e55fDCk-2MzS0IOWpOI&s=10" },
        { nombre: "Futbol X5", categoria: "Deportivo", lugar: "Plaza de Deportes Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLGcgcjLaLtUFzXSfs5EzS0x3c23cfZtXgew3MzIyfJWBgxce4Bah7vhA&s=10" }
    ],
    discotecas: [
        { nombre: "Noche Electro", categoria: "Música", lugar: "Salto Electronic", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSdHgNP1Vnx8SMPSV-1X3F7vHo5SnJbXLajcDXHOGPueg56Yg7uuRtYDok&s=10" },
        { nombre: "Polo", categoria: "Disfraces", lugar: "Polo Club", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrj22POvl9MEEF961dr53OM-Hpxz75raAMOqjgqS_fNJ15QnNkQmBHkx_z&s=10" },
        { nombre: "Bambola", categoria: "Discoteca", lugar: "Costanera Sur 1535", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRh8BnVQ1qdPDXqoHVHylKrGhwk3guImN0d1ZRYbBNmSXoKPhqmCE-pMsE&s=10" },
        { nombre: "Porco Negro", categoria: "Discoteca", lugar: "Av. Apolón de Mirbek esquina Av. José Enrique Rodó.", imagen: "/img/porco.avif" },
        { nombre: "La Ferne", categoria: "Discoteca", lugar: "Costanera Norte", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSzXMkEsvlia-KLamghIoD7xL9Rpz72SnkxikCvS_uH0Q&s" }
    ],
    competencias: [
        { nombre: "Streetball Salto", categoria: "Deportivo", lugar: "Polideportivo Círculo SP", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0pAIE9pTlfySurzoR4nHmaJctr45FdL9iY9xVQXMfhhcFj8Gn_pXJ5z99&s=10" },
        { nombre: "Festival de Skate", categoria: "Urbano", lugar: "La Fosa", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiTNSyh7DAtTH9WjZxDPJquTN8Jeb_LMwPwTsRyRUuWjg_Z10exHPw0jaF&s=10" },
        { nombre: "Campeonato de Pesca", categoria: "Naturaleza", lugar: "Costanera Sur", imagen: "https://www.opp.gub.uy/sites/default/files/noticias/2025-03/whatsapp-image-2025-03-17-92805-am-2.jpeg" },
        { nombre: "Torneo de Ajedrez", categoria: "Mental", lugar: "Plaza de Deportes", imagen: "https://www.clarin.com/2024/10/10/IUl8ywHqRO_2000x1500__1.jpg" },
        { nombre: "Copa de Natación", categoria: "Acuatico", lugar: "Playa Salto", imagen: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTfYqzP-3hljVNvWo0X2mA8fxJY9EOZKFXquVDdbwRnvST-uFmxjLEum-d&s=10" }
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
  <article class="tarjeta${activa ? ' activa' : ''}" role="button" tabindex="0" aria-label="Evento ${evento.nombre}">
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

    // // Construye botones de galería con atributo data-index y fallback de imagen (onerror)
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

    renderGaleria();

    document.getElementById("lightbox-close")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-backdrop")?.addEventListener("click", cerrarLightbox);
    document.getElementById("lightbox-prev")?.addEventListener("click", () => cambiarImagen(-1));
    document.getElementById("lightbox-next")?.addEventListener("click", () => cambiarImagen(1));
}

document.addEventListener("DOMContentLoaded", init);
