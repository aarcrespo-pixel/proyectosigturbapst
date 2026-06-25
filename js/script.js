const perfil = document.querySelector(".perfil");
const botonPerfil = document.querySelector(".perfil-btn");
const hamburguesa = document.querySelector(".hamburguesa");
const menu = document.querySelector(".menu");

if (botonPerfil && perfil) {
    botonPerfil.addEventListener("click", () => {
        perfil.classList.toggle("activo");
    });
}

if (hamburguesa && menu) {
    hamburguesa.addEventListener("click", () => {
        menu.classList.toggle("activo");
    });
}

document.addEventListener("click", (e) => {
    if (perfil && botonPerfil && !perfil.contains(e.target) && !botonPerfil.contains(e.target)) {
        perfil.classList.remove("activo");
    }

    if (menu && hamburguesa && !menu.contains(e.target) && !hamburguesa.contains(e.target)) {
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

/* Toggle feature accordion text */
const toggles = document.querySelectorAll('.feature-toggle');

toggles.forEach((toggle) => {
    toggle.addEventListener('click', (event) => {
        const button = event.currentTarget;
        const feature = button.closest('.feature');
        const expanded = button.getAttribute('aria-expanded') === 'true';
        const shouldExpand = !expanded;

        if (feature) {
            feature.classList.toggle('activo', shouldExpand);
            feature.classList.toggle('open', shouldExpand);
            button.classList.toggle('open', shouldExpand);
            button.setAttribute('aria-expanded', String(shouldExpand));
        }
    });
});

/* Toggle footer sections */
const footerToggles = document.querySelectorAll('.footer-toggle');

footerToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const footerCol = toggle.closest('.footer-col');
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        const shouldExpand = !expanded;

        if (footerCol) {
            footerCol.classList.toggle('open', shouldExpand);
            toggle.classList.toggle('open', shouldExpand);
            toggle.setAttribute('aria-expanded', String(shouldExpand));
        }
    });
});

/* Información de SIGTUR */
const infoButton = document.querySelector('.info-btn');
const infoPanel = document.querySelector('.info-panel');

if (infoButton && infoPanel) {
    infoButton.addEventListener('click', () => {
        const expanded = infoButton.getAttribute('aria-expanded') === 'true';
        const shouldExpand = !expanded;

        infoButton.setAttribute('aria-expanded', String(shouldExpand));
        infoPanel.classList.toggle('open', shouldExpand);
    });
}
