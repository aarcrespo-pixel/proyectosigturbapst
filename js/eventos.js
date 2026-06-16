const datos = {
    destacados: [
        { nombre: "Semana de la Cerveza", categoria: "Festival", lugar: "en psdu pero ta", imagen: "../img/evento1.jpg" },
        { nombre: "La Ferne", categoria: "Discoteca", lugar: "Costanera Norte", imagen: "../img/images (1).jfif" },
        { nombre: "Porco Negro", categoria: "Discoteca", lugar: "lejos", imagen: "../img/porco.avif" },
        { nombre: "ExpoSalto", categoria: "Feria", lugar: "mas lejos todavia", imagen: "../img/exposalto.jpeg" },
        { nombre: "LaFosa Bike", categoria: "Deportivo", lugar: "La Fosa", imagen: "../img/evento2.jpg" }
    ],
    deportivos: [
        { nombre: "Maratón de la Ciudad", categoria: "Carrera", imagen: "../img/evento2.jpg" },
        { nombre: "Surf y Kayak", categoria: "Aventura", imagen: "../img/evento1.jpg" },
        { nombre: "Torneo de Beach Volley", categoria: "Competencia", imagen: "../img/eventos.jpeg" },
        { nombre: "Circuito en Bicicleta", categoria: "Deportivo", imagen: "../img/lugares.jpeg" },
        { nombre: "Triatlón Familiar", categoria: "Resistencia", imagen: "../img/turismo.jpeg" },
        { nombre: "Clase de Yoga al Río", categoria: "Salud", imagen: "../img/eventos.webp" }
    ],
    discotecas: [
        { nombre: "Noche Electro", categoria: "Música", imagen: "../img/turismo.jpeg" },
        { nombre: "Fiesta Tropical", categoria: "Baile", imagen: "../img/evento1.jpg" },
        { nombre: "DJ en Vivo", categoria: "Discoteca", imagen: "../img/eventos.jpeg" },
        { nombre: "After Party VIP", categoria: "Electrónica", imagen: "../img/lugares.jpeg" },
        { nombre: "Ritmos Urbanos", categoria: "Reggaetón", imagen: "../img/evento2.jpg" }
    ],
    competencias: [
        { nombre: "Streetball Salto", categoria: "Deportivo", imagen: "../img/evento2.jpg" },
        { nombre: "Festival de Skate", categoria: "Urbano", imagen: "../img/lugares.jpeg" },
        { nombre: "Campeonato de Pesca", categoria: "Naturaleza", imagen: "../img/eventos.jpeg" },
        { nombre: "Torneo de Ajedrez", categoria: "Mental", imagen: "../img/evento1.jpg" },
        { nombre: "Copa de Natación", categoria: "Acua", imagen: "../img/turismo.jpeg" }
    ],
    anteriores: [
        { nombre: "Festival de la Naranja", categoria: "Cultura", lugar: "Plaza Artigas", imagen: "../img/evento2.jpg" },
        { nombre: "Carrera Nocturna", categoria: "Deportivo", lugar: "Costanera Norte", imagen: "../img/turismo.jpeg" },
        { nombre: "Muestra de Danza", categoria: "Cultura", lugar: "Centro Cultural", imagen: "../img/evento1.jpg" },
        { nombre: "Feria de Emprendedores", categoria: "Local", lugar: "Mercado Central", imagen: "../img/lugares.jpeg" },
        { nombre: "Noche de Cine al Aire Libre", categoria: "Arte", lugar: "Parque de la Memoria", imagen: "../img/eventos.webp" }
    ]
};

const galeria = [
    { imagen: "../img/evento1.jpg" },
    { imagen: "../img/evento2.jpg" },
    { imagen: "../img/eventos.jpeg" },
    { imagen: "../img/turismo.jpeg" },
    { imagen: "../img/lugares.jpeg" },
    { imagen: "../img/eventos.webp" }
];

const pos = {};
for (const nombre in datos) pos[nombre] = 0;
const maxPorPagina = 3;

const tarjeta = (evento) => `
  <article class="tarjeta">
    <div class="imagen-tarjeta">
      <img src="${evento.imagen}" alt="${evento.nombre}">
    </div>
    <div class="detalles-tarjeta">
      <span class="categoria">${evento.categoria}</span>
      <h3>${evento.nombre}</h3>
      ${evento.lugar ? `<span class="lugar">${evento.lugar}</span>` : ""}
    </div>
  </article>
`;

function renderSeccion(nombre) {
    const lista = datos[nombre];
    const actual = pos[nombre];
    const inicio = Math.max(0, Math.min(actual - 1, lista.length - maxPorPagina));
    const html = lista.slice(inicio, inicio + maxPorPagina).map(tarjeta).join("");

    document.getElementById(`${nombre}-contenedor`).innerHTML = html;
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

function setPos(nombre, indice) {
    const lista = datos[nombre];
    const siguiente = Math.max(0, Math.min(indice, lista.length - 1));
    if (siguiente === pos[nombre]) return;

    pos[nombre] = siguiente;
    renderSeccion(nombre);
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

    document.getElementById("galeria-contenedor").innerHTML = galeria
        .map((item, index) => `
      <div class="imagen-galeria">
        <img src="${item.imagen}" alt="Galería ${index + 1}">
      </div>
    `)
        .join("");
}

document.addEventListener("DOMContentLoaded", init);
