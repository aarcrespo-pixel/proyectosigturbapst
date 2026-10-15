document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('.tarjeta-evento'));
    const searchInput = document.getElementById('busquedaInput');
    const categorySelect = document.getElementById('categoriaSelect');
    const filterToggle = document.getElementById('filtroToggle');
    const filterMenu = document.getElementById('menuFiltro');
    const emptyState = document.getElementById('estadoVacio');
    const carouselButtons = Array.from(document.querySelectorAll('.flecha-carrusel'));

    const categorias = [...new Set(cards.map((card) => card.dataset.categoria.trim()))].sort((a, b) => a.localeCompare(b));
    categorias.forEach((categoria) => {
        const option = document.createElement('option');
        option.value = categoria.toLowerCase();
        option.textContent = categoria;
        categorySelect.appendChild(option);
    });

    const aplicarFiltros = () => {
        const textoBusqueda = searchInput.value.trim().toLowerCase();
        const categoriaSeleccionada = categorySelect.value;
        let visibles = 0;

        cards.forEach((card) => {
            const titulo = card.dataset.titulo.toLowerCase();
            const categoria = card.dataset.categoria.toLowerCase();
            const cumpleBusqueda = titulo.includes(textoBusqueda) || categoria.includes(textoBusqueda);
            const cumpleCategoria = categoriaSeleccionada === 'all' || categoria === categoriaSeleccionada;
            const visible = cumpleBusqueda && cumpleCategoria;

            card.style.display = visible ? '' : 'none';
            if (visible) visibles += 1;
        });

        emptyState.style.display = visibles > 0 ? 'none' : 'block';
    };

    const moverCarrusel = (button) => {
        const row = button.closest('.fila-categoria');
        const track = row.querySelector('.pista-carrusel');
        const cardsInRow = track.querySelectorAll('.tarjeta-evento');
        const firstVisibleCard = cardsInRow[0];
        if (!firstVisibleCard || !track) return;

        const gap = 18;
        const scrollAmount = firstVisibleCard.getBoundingClientRect().width + gap;
        const direction = button.dataset.direction === 'left' ? -1 : 1;
        const nextScroll = track.scrollLeft + (scrollAmount * 3 * direction);

        if (direction > 0 && nextScroll >= track.scrollWidth - track.clientWidth - 5) {
            track.scrollTo({ left: 0, behavior: 'smooth' });
            return;
        }

        if (direction < 0 && nextScroll <= 5) {
            track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' });
            return;
        }

        track.scrollBy({ left: scrollAmount * 3 * direction, behavior: 'smooth' });
    };

    carouselButtons.forEach((button) => {
        button.addEventListener('click', () => moverCarrusel(button));
    });

    searchInput.addEventListener('input', aplicarFiltros);
    categorySelect.addEventListener('change', aplicarFiltros);

    filterToggle.addEventListener('click', () => {
        const isOpen = filterMenu.classList.toggle('open');
        filterToggle.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.contenedor-menu-filtro')) {
            filterMenu.classList.remove('open');
            filterToggle.setAttribute('aria-expanded', 'false');
        }
    });

    aplicarFiltros();
});
