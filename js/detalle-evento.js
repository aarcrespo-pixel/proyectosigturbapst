(function () {
    'use strict';

    function agregarBotonSalir() {
        const acciones = document.querySelector('.detalle-acciones');
        if (!acciones || acciones.querySelector('.btn-salir-evento')) return;

        const boton = document.createElement('a');
        boton.className = 'btn btn-outline btn-salir-evento';
        boton.href = '../eventos.php';
        boton.textContent = 'Salir del evento';
        boton.setAttribute('aria-label', 'Volver al listado de eventos');
        acciones.appendChild(boton);
    }

    function agregarComentarios() {
        const detalle = document.querySelector('.detalle-hero');
        if (!detalle || document.querySelector('.detalle-comentarios')) return;

        const slug = window.location.pathname.split('/').pop().replace(/\.php$/, '') || 'evento';
        const section = document.createElement('section');
        section.className = 'detalle-comentarios';
        section.innerHTML = `
            <div class="detalle-comentarios__contenido">
                <h2>Comentarios del evento</h2>
                <article class="gallery-engagement-card" data-engagement="comments-only" data-item-key="detalle-${slug}"></article>
            </div>`;
        detalle.insertAdjacentElement('afterend', section);

        const iniciar = () => window.initInteractiveCards?.(section);
        if (window.initInteractiveCards) {
            iniciar();
            return;
        }

        const script = document.createElement('script');
        script.src = '../../js/interacciones-tarjetas.js';
        script.onload = iniciar;
        document.head.appendChild(script);
    }
                /* Inyectamos el módulo una sola vez y después delegamos la interacción
                   al motor común de tarjetas para conservar persistencia y paginación. */
    detalle.insertAdjacentElement('afterend', section);
    // Inicializamos el componente después de insertarlo para que encuentre su card.
    if (typeof window.initInteractiveCards === 'function') {

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            agregarBotonSalir();
            agregarComentarios();
        });
    } else {
        agregarBotonSalir();
        agregarComentarios();
    }
})();
