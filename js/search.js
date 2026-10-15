(function () {
    const MINIMUM_QUERY_LENGTH = 3;
    let debounceTimer = null;
    let requestController = null;

    /* Debounce agrupa las pulsaciones rápidas en una sola ejecución: mientras
       el usuario escribe se reinicia el temporizador y solo se consulta la API
       cuando pasan 300 ms sin nuevos cambios en el input. */
    function debounce(callback, delay = 300) {
        return (...args) => {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => callback(...args), delay);
        };
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, (character) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
        }[character]));
    }

    function getResultUrl(result, resultsBase) {
        if (result.tipo === 'evento') return `${resultsBase.replace(/buscar\.php$/, 'evento.php')}?id=${encodeURIComponent(result.id)}`;
        if (result.tipo === 'lugar') return `${resultsBase.replace(/buscar\.php$/, 'lugar.php')}?id=${encodeURIComponent(result.id)}`;
        return `${resultsBase.replace(/buscar\.php$/, 'gastronomia.php')}#propuestas-gastronomicas`;
    }

    function renderResults(dropdown, results, resultsBase) {
        /* El render crea enlaces reales, de modo que mouse, teclado, copiar
           enlace y lectores de pantalla reciben la misma navegación estándar. */
        dropdown.innerHTML = results.map((result) => `
            <a class="search-result-item" href="${getResultUrl(result, resultsBase)}">
                <img src="${escapeHtml(result.imagen || '../img/gastronomia.jpg')}" alt="">
                <span class="search-result-copy">
                    <span class="search-result-name">${escapeHtml(result.nombre)}</span>
                    <span class="search-result-badge">${escapeHtml(result.tipoLabel || result.tipo.toUpperCase())}</span>
                </span>
            </a>
        `).join('');
        dropdown.hidden = false;
    }

    async function loadResults(input, dropdown, endpoint, resultsBase) {
        const query = input.value.trim();
        if (query.length < MINIMUM_QUERY_LENGTH) {
            dropdown.hidden = true;
            dropdown.innerHTML = '';
            return;
        }

        if (requestController) requestController.abort();
        requestController = new AbortController();
        dropdown.innerHTML = '<div class="search-results-state">Buscando...</div>';
        dropdown.hidden = false;

        try {
            const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`, { signal: requestController.signal, headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`Búsqueda respondió ${response.status}`);
            const results = await response.json();
            renderResults(dropdown, results, resultsBase);
            if (!results.length) dropdown.innerHTML = `<div class="search-results-state">No se encontraron resultados para ${escapeHtml(query)}</div>`;
        } catch (error) {
            if (error.name !== 'AbortError') dropdown.innerHTML = '<div class="search-results-state">No se pudo completar la búsqueda.</div>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.barra-busqueda[data-search-endpoint]').forEach((searchBox) => {
            const input = searchBox.querySelector('input');
            if (!input || searchBox.dataset.searchReady) return;
            searchBox.dataset.searchReady = 'true';
            const dropdown = document.createElement('div');
            dropdown.className = 'search-results-dropdown';
            dropdown.hidden = true;
            searchBox.appendChild(dropdown);
            const updateResults = debounce(() => loadResults(input, dropdown, searchBox.dataset.searchEndpoint, searchBox.dataset.searchResults));
            input.addEventListener('input', updateResults);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && input.value.trim()) {
                    event.preventDefault();
                    window.location.href = `${searchBox.dataset.searchResults}?q=${encodeURIComponent(input.value.trim())}`;
                }
                if (event.key === 'Escape') dropdown.hidden = true;
            });
            document.addEventListener('click', (event) => {
                if (!searchBox.contains(event.target)) dropdown.hidden = true;
            });
        });
    });
}());