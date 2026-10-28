document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('gastronomia-grid');
    const search = document.getElementById('buscar-gastronomia');
    const filters = [...document.querySelectorAll('[data-gastronomia-filtro]')];
    const emptyState = document.getElementById('gastronomia-sin-resultados');
    const mapList = document.getElementById('gastronomia-mapa-lugares');
    const cardsToggle = document.getElementById('gastronomia-grid-toggle');
    const initialCardCount = 6;

    if (!grid) return;

    const normalize = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase()
        .trim();
    let activeCategory = 'todos';
    let cardsExpanded = false;
    let matchingCards = [];

    const updateCards = () => {
        const query = normalize(search?.value);
        matchingCards = [];

        grid.querySelectorAll('.tarjeta-gastronomia').forEach((card) => {
            const category = normalize(card.dataset.gastronomiaCategoria);
            const matchesCategory = activeCategory === 'todos' || category === normalize(activeCategory);
            const matchesQuery = !query || normalize(card.textContent).includes(query);
            if (matchesCategory && matchesQuery) matchingCards.push(card);
        });

        cardsExpanded = false;
        matchingCards.forEach((card, index) => {
            card.hidden = index >= initialCardCount;
        });
        const matchingSet = new Set(matchingCards);
        grid.querySelectorAll('.tarjeta-gastronomia').forEach((card) => {
            if (!matchingSet.has(card)) card.hidden = true;
        });
        if (cardsToggle) {
            cardsToggle.hidden = matchingCards.length <= initialCardCount;
            cardsToggle.setAttribute('aria-expanded', 'false');
            cardsToggle.textContent = 'Ver más';
        }
        if (emptyState) emptyState.hidden = matchingCards.length !== 0;
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

    cardsToggle?.addEventListener('click', () => {
        cardsExpanded = !cardsExpanded;
        matchingCards.forEach((card, index) => {
            card.hidden = !cardsExpanded && index >= initialCardCount;
        });
        cardsToggle.setAttribute('aria-expanded', String(cardsExpanded));
        cardsToggle.textContent = cardsExpanded ? 'Ver menos' : 'Ver más';
    });

    if (mapList) {
        const mapPlaces = [...mapList.querySelectorAll('a')];
        const mapToggle = document.getElementById('gastronomia-mapa-toggle');
        const initialMapPlaces = 5;

        if (mapToggle && mapPlaces.length > initialMapPlaces) {
            mapPlaces.forEach((place, index) => {
                place.hidden = index >= initialMapPlaces;
            });
            mapToggle.hidden = false;

            mapToggle.addEventListener('click', () => {
                const expanded = mapToggle.getAttribute('aria-expanded') === 'true';
                mapPlaces.forEach((place, index) => {
                    place.hidden = expanded && index >= initialMapPlaces;
                });
                mapToggle.setAttribute('aria-expanded', String(!expanded));
                mapToggle.textContent = expanded ? 'Ver más' : 'Ver menos';
            });
        }
    }
});
