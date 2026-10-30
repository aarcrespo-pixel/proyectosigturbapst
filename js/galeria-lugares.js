(() => {
    'use strict';

    const galerias = document.querySelectorAll('.gallery-photo-grid');
    if (!galerias.length) return;

    const dialog = document.createElement('dialog');
    dialog.className = 'place-gallery-lightbox';
    dialog.setAttribute('aria-label', 'Imagen ampliada de la galería');
    dialog.innerHTML = `
        <button class="place-gallery-lightbox__close" type="button" aria-label="Cerrar imagen">×</button>
        <figure>
            <img class="place-gallery-lightbox__image" alt="">
            <figcaption class="place-gallery-lightbox__caption"></figcaption>
        </figure>`;
    document.body.append(dialog);

    const imagenAmpliada = dialog.querySelector('.place-gallery-lightbox__image');
    const tituloAmpliado = dialog.querySelector('.place-gallery-lightbox__caption');

    galerias.forEach((galeria) => {
        galeria.querySelectorAll('.tarjeta-galeria').forEach((tarjeta) => {
            const imagen = tarjeta.querySelector('img');
            if (!imagen || imagen.parentElement.matches('button')) return;

            const titulo = tarjeta.querySelector('h3')?.textContent.trim() || imagen.alt;
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'place-gallery-lightbox__trigger';
            boton.setAttribute('aria-label', `Ampliar imagen: ${titulo}`);
            imagen.before(boton);
            boton.append(imagen);

            boton.addEventListener('click', () => {
                imagenAmpliada.src = imagen.currentSrc || imagen.src;
                imagenAmpliada.alt = titulo;
                tituloAmpliado.textContent = titulo;
                dialog.showModal();
            });
        });
    });

    dialog.querySelector('.place-gallery-lightbox__close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
})();
