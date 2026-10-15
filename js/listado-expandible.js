document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-lista-expandible]').forEach((lista) => {
        const elementos = [...lista.querySelectorAll('[data-lista-item]')];
        const boton = lista.nextElementSibling;
        if (!boton?.matches('[data-lista-boton]') || elementos.length <= 10) return;

        const cantidadInicial = Number.parseInt(lista.dataset.cantidadInicial || '10', 10);
        const textoMas = boton.dataset.textoMas || 'Ver más';
        const textoMenos = boton.dataset.textoMenos || 'Ver menos';
        elementos.forEach((elemento, indice) => {
            elemento.hidden = indice >= cantidadInicial;
        });

        boton.addEventListener('click', () => {
            const estaExpandida = boton.getAttribute('aria-expanded') === 'true';
            elementos.forEach((elemento, indice) => {
                elemento.hidden = !estaExpandida && indice >= cantidadInicial;
            });
            boton.setAttribute('aria-expanded', String(!estaExpandida));
            boton.textContent = estaExpandida ? textoMas : textoMenos;
        });
    });
});
