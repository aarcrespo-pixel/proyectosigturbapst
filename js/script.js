const perfil = document.querySelector(".perfil");
const botonPerfil = document.querySelector(".perfil-btn");
const hamburguesa = document.querySelector(".hamburguesa");
const menu = document.querySelector(".menu");

botonPerfil.addEventListener("click", () => {
    perfil.classList.toggle("activo");
});

hamburguesa.addEventListener("click", () => {
    menu.classList.toggle("activo");
});

document.addEventListener("click", (e) => {

    if (!perfil.contains(e.target)) {
        perfil.classList.remove("activo");
    }

    if (
        !menu.contains(e.target) &&
        !hamburguesa.contains(e.target)
    ) {
        menu.classList.remove("activo");
    }

});

/* Carrusel de eventos destacados */
const slides = document.querySelectorAll('.slide');
const indicadores = document.querySelectorAll('.indicador');
const flechaIzquierda = document.querySelector('.flecha.izquierda');
const flechaDerecha = document.querySelector('.flecha.derecha');
let indiceActivo = 0;

function actualizarCarrusel(nuevoIndice) {
    slides[indiceActivo].classList.remove('activo');
    indicadores[indiceActivo].classList.remove('activo');
    indiceActivo = (nuevoIndice + slides.length) % slides.length;
    slides[indiceActivo].classList.add('activo');
    indicadores[indiceActivo].classList.add('activo');
}

flechaIzquierda.addEventListener('click', () => {
    actualizarCarrusel(indiceActivo - 1);
});

flechaDerecha.addEventListener('click', () => {
    actualizarCarrusel(indiceActivo + 1);
});

indicadores.forEach((indicador, index) => {
    indicador.addEventListener('click', () => {
        actualizarCarrusel(index);
    });
});
