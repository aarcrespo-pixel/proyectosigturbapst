(() => {
    'use strict';

    if (window.sigturBackPositionEnabled) return;
    window.sigturBackPositionEnabled = true;

    const scrollKey = `sigtur-scroll-position:${window.location.href}`;
    const previousPageKey = 'sigtur-previous-page-url';
    let saveTimer;

    const guardarPosicion = () => {
        window.clearTimeout(saveTimer);
        sessionStorage.setItem(scrollKey, String(window.scrollY));
    };

    if ('scrollRestoration' in window.history) {
        window.history.scrollRestoration = 'manual';
    }

    window.addEventListener('scroll', () => {
        window.clearTimeout(saveTimer);
        saveTimer = window.setTimeout(guardarPosicion, 100);
    }, { passive: true });
    window.addEventListener('pagehide', () => {
        guardarPosicion();
        sessionStorage.setItem(previousPageKey, window.location.href);
    });

    const restaurarPosicion = (evento) => {
        const navegacion = performance.getEntriesByType('navigation')[0];
        if (!evento.persisted && navegacion?.type !== 'back_forward') return;

        const posicionGuardada = sessionStorage.getItem(scrollKey);
        if (posicionGuardada === null) return;

        window.requestAnimationFrame(() => {
            window.scrollTo({ top: Number(posicionGuardada), behavior: 'instant' });
        });
    };

    window.addEventListener('pageshow', restaurarPosicion);

    document.addEventListener('click', (evento) => {
        if (evento.defaultPrevented || evento.button !== 0 || evento.metaKey || evento.ctrlKey || evento.shiftKey || evento.altKey) return;

        const enlace = evento.target instanceof Element ? evento.target.closest('a[href]') : null;
        if (!enlace || enlace.target && enlace.target !== '_self' || enlace.hasAttribute('download')) return;

        const etiqueta = `${enlace.getAttribute('aria-label') || ''} ${enlace.textContent || ''}`.trim();
        if (!/\b(volver|regresar|retroceder)\b|salir del evento/i.test(etiqueta)) return;

        const destino = new URL(enlace.href, window.location.href);
        if (destino.origin !== window.location.origin || window.history.length < 2) return;

        const origen = document.referrer ? new URL(document.referrer) : null;
        const paginaAnterior = sessionStorage.getItem(previousPageKey);
        const origenMismoSitio = document.referrer
            ? origen?.origin === window.location.origin
            : paginaAnterior !== null && new URL(paginaAnterior).origin === window.location.origin;
        if (!origenMismoSitio) return;

        evento.preventDefault();
        guardarPosicion();
        window.history.back();
    });
})();
