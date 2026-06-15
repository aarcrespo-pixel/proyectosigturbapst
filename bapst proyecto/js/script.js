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