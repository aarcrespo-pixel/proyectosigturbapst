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
        document.querySelectorAll('.tarjeta-destino, .tarjeta-ruta, .tarjeta-lugar, .tarjeta-interes, .tarjeta-galeria').forEach((card) => {
            const title = card.querySelector('h3')?.textContent.trim();
            const slug = placeSlugs[title];
            if (!slug) return;
            const href = `lugar.php?id=${encodeURIComponent(slug)}`;
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