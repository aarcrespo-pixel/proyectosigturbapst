document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('gastronomia-grid');
    const search = document.getElementById('buscar-gastronomia');
    const filters = [...document.querySelectorAll('[data-gastronomia-filtro]')];
    const emptyState = document.getElementById('gastronomia-sin-resultados');

    if (!grid) return;

    const normalize = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase()
        .trim();
    let activeCategory = 'todos';

    const updateCards = () => {
        const query = normalize(search?.value);
        let visibleCount = 0;

        grid.querySelectorAll('.tarjeta-gastronomia').forEach((card) => {
            const category = normalize(card.dataset.gastronomiaCategoria);
            const matchesCategory = activeCategory === 'todos' || category === normalize(activeCategory);
            const matchesQuery = !query || normalize(card.textContent).includes(query);
            const isVisible = matchesCategory && matchesQuery;
            card.hidden = !isVisible;
            if (isVisible) visibleCount += 1;
        });

        if (emptyState) emptyState.hidden = visibleCount !== 0;
    };

    filters.forEach((button) => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.gastronomiaFiltro || 'todos';
            filters.forEach((filter) => {
                const isActive = filter === button;
                filter.classList.toggle('activo', isActive);
                filter.setAttribute('aria-pressed', String(isActive));
            });
            updateCards();
        });
    });

    search?.addEventListener('input', updateCards);
});
