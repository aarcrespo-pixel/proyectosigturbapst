<?php
$q = trim((string) ($_GET['q'] ?? ''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda | SIGTUR</title>
    <link rel="stylesheet" href="../css/estilos.css">
    <style>
        .search-page { max-width: 1100px; margin: 0 auto; padding: 9rem 24px 5rem; }
        .search-page h1 { color: var(--texto-oscuro, #172033); margin-bottom: 28px; }
        .search-page-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .search-page-card { overflow: hidden; border: 1px solid rgba(127,127,127,.18); border-radius: 16px; background: var(--bg-card, #fff); box-shadow: 0 12px 28px rgba(0,0,0,.08); color: inherit; text-decoration: none; }
        .search-page-card img { display: block; width: 100%; height: 160px; object-fit: cover; }
        .search-page-card__body { display: grid; gap: 8px; padding: 16px; }
        .search-page-card h2 { margin: 0; color: var(--text-primary, #172033); font-size: 1.1rem; }
        .search-page-card span { color: var(--text-secondary, #5f6b7a); font-size: .85rem; }
        .search-page-empty { color: var(--text-secondary, #5f6b7a); padding: 24px 0; }
        body[data-tema="oscuro"] .search-page h1 { color: #f9fafb; }
    </style>
</head>
<body>
<?php $headerCurrentPage = 'buscar'; $headerActivePage = ''; include __DIR__ . '/header.php'; ?>
<main class="search-page" data-query="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>" data-endpoint="api/buscar.php">
    <h1 data-i18n="searchResultsTitle">Resultados para: <?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="search-page-grid" id="search-page-results"><p class="search-page-empty">Buscando...</p></div>
</main>
<script>
/* La vista completa reutiliza el mismo endpoint del autocompletado para no
   mantener dos consultas distintas ni producir resultados inconsistentes. */
document.addEventListener('DOMContentLoaded', async () => {
    const page = document.querySelector('.search-page');
    const container = document.getElementById('search-page-results');
    const query = page?.dataset.query || '';
    if (!container || query.length < 3) {
        if (container) container.innerHTML = '<p class="search-page-empty">Escribe al menos 3 caracteres para buscar.</p>';
        return;
    }
    try {
        const response = await fetch(`${page.dataset.endpoint}?q=${encodeURIComponent(query)}`, { headers: { Accept: 'application/json' } });
        const results = await response.json();
        if (!results.length) {
            container.innerHTML = `<p class="search-page-empty">No se encontraron resultados para ${query.replace(/[<>]/g, '')}</p>`;
            return;
        }
        const endpointSuffix = 'api/buscar.php';
        const base = page.dataset.endpoint.endsWith(endpointSuffix)
            ? page.dataset.endpoint.slice(0, -endpointSuffix.length)
            : '';
        const escapeHtml = (value) => String(value).replace(/[&<>\"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character]));
        container.innerHTML = results.map((result) => {
            const target = result.tipo === 'evento' ? `evento.php?id=${encodeURIComponent(result.id)}` : result.tipo === 'lugar' ? `lugar.php?id=${encodeURIComponent(result.id)}` : 'gastronomia.php#propuestas-gastronomicas';
            return `<a class="search-page-card" href="${base}${target}"><img src="${escapeHtml(result.imagen)}" alt=""><div class="search-page-card__body"><span>${escapeHtml(result.tipoLabel || result.tipo)}</span><h2>${escapeHtml(result.nombre)}</h2><span>${escapeHtml(result.categoria || '')}</span></div></a>`;
        }).join('');
    } catch (error) {
        container.innerHTML = '<p class="search-page-empty">No se pudo completar la búsqueda.</p>';
    }
});
</script>
</body>
</html>
