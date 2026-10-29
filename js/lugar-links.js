(function () {
    const placeSlugs = {
        'Costanera Norte': 'costanera-norte',
        'BaSalto': 'basalto',
        'Parque Benito Solari': 'parque-benito-solari',
        'Plaza Artigas': 'plaza-artigas',
        'Plaza Treinta y Tres Orientales': 'plaza-treinta-y-tres-orientales',
        'La Trouville': 'la-trouville',
        'Cine Sarandi': 'cine-sarandi',
        'Cine Sarandí': 'cine-sarandi',
        'La Fosa': 'la-fosa',
        'Salto Shopping': 'salto-shopping',
        'Termas del Dayman': 'termas-del-dayman',
        'Acuamania': 'acuamania',
        'Termas de la Arapey': 'termas-de-la-arapey',
        'Agua Clara': 'agua-clara',
        'Plaza Roosvelt': 'plaza-roosvelt',
        'Muelle Negro': 'muelle-negro',
    };

    document.addEventListener('DOMContentLoaded', () => {
        const origenTurismo = window.location.pathname.endsWith('/turismo.php');
        const origenLugares = window.location.pathname.endsWith('/lugares.php');
        const initialMapPlaces = 5;
        const setupMapToggle = (listId, toggleId) => {
            const mapList = document.getElementById(listId);
            const mapPlaces = mapList ? [...mapList.querySelectorAll('a')] : [];
            const mapToggle = document.getElementById(toggleId);
            if (!mapList) return;
            const search = mapList.querySelector('[data-map-search]');
            const emptyState = mapList.querySelector('[data-map-empty]');
            const normalize = (value) => String(value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLocaleLowerCase()
                .trim();

            const updatePlaces = () => {
                const query = normalize(search?.value);
                const matches = mapPlaces.filter((place) => normalize(place.textContent).includes(query));
                const expanded = mapToggle?.getAttribute('aria-expanded') === 'true';

                mapPlaces.forEach((place) => {
                    const matchIndex = matches.indexOf(place);
                    place.hidden = matchIndex < 0 || (!query && !expanded && matchIndex >= initialMapPlaces);
                });
                if (mapToggle) {
                    mapToggle.hidden = Boolean(query) || matches.length <= initialMapPlaces;
                    if (query && mapToggle.getAttribute('aria-expanded') === 'true') {
                        mapToggle.setAttribute('aria-expanded', 'false');
                        mapToggle.textContent = 'Ver más';
                    }
                }
                if (emptyState) emptyState.hidden = !query || matches.length > 0;
            };

            mapToggle?.addEventListener('click', () => {
                const expanded = mapToggle.getAttribute('aria-expanded') === 'true';
                mapToggle.setAttribute('aria-expanded', String(!expanded));
                mapToggle.textContent = expanded ? 'Ver más' : 'Ver menos';
                updatePlaces();
            });
            search?.addEventListener('input', updatePlaces);
            updatePlaces();

            const mapFrame = mapList.closest('.mapa-contenido')?.querySelector('iframe');
            mapList.addEventListener('click', (event) => {
                const clickedElement = event.target instanceof Element ? event.target : null;
                const link = clickedElement?.closest('a[data-map-query]');
                if (!link || !mapFrame) return;
                mapFrame.src = `https://www.google.com/maps?q=${encodeURIComponent(link.dataset.mapQuery)}&output=embed`;
            });
        };

        setupMapToggle('mapa-lugares-lista', 'mapa-lugares-toggle');
        setupMapToggle('mapa-turismo-lista', 'mapa-turismo-toggle');

        document.querySelectorAll('.tarjeta-destino, .tarjeta-ruta, .tarjeta-lugar, .tarjeta-interes, .tarjeta-galeria').forEach((card) => {
            const title = card.querySelector('h3')?.textContent.trim();
            const slug = placeSlugs[title];
            if (!slug) return;
            const origen = origenTurismo ? 'turismo' : origenLugares ? 'lugares' : '';
            const href = `lugar.php?id=${encodeURIComponent(slug)}${origen ? `&origen=${origen}` : ''}`;
            card.setAttribute('role', 'link');
            card.setAttribute('tabindex', '0');
            card.dataset.lugarHref = href;
            card.addEventListener('click', () => { window.location.href = href; });
            card.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location.href = href; }
            });
        });
    });
}());