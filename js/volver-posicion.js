(() => {
    'use strict';

    if (window.sigturBackPositionEnabled) return;
    window.sigturBackPositionEnabled = true;

    const scrollKeyForPath = (pathname) => `sigtur-scroll-position:${window.location.origin}${pathname}`;
    const scrollKey = scrollKeyForPath(window.location.pathname);
    const pendingReturnKey = 'sigtur-pending-category-return';
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
    window.addEventListener('pagehide', guardarPosicion);

    const restaurarPosicion = (evento) => {
        const retornoPendiente = sessionStorage.getItem(pendingReturnKey);
        if (retornoPendiente !== null) {
            sessionStorage.removeItem(pendingReturnKey);
            try {
                const retorno = JSON.parse(retornoPendiente);
                const posicion = Number(retorno.posicion);
                if (retorno.pathname === window.location.pathname && Number.isFinite(posicion) && posicion >= 0) {
                    window.requestAnimationFrame(() => {
                        window.scrollTo({ top: posicion, behavior: 'instant' });
                    });
                    return;
                }
            } catch (error) {
                console.error('No se pudo restaurar la posición anterior', error);
            }
        }

        const navegacion = performance.getEntriesByType('navigation')[0];
        if (!evento.persisted && navegacion?.type !== 'back_forward') return;

        const posicionGuardada = sessionStorage.getItem(scrollKey);
        const posicion = Number(posicionGuardada);
        if (posicionGuardada === null || !Number.isFinite(posicion)) return;

        window.requestAnimationFrame(() => {
            window.scrollTo({ top: posicion, behavior: 'instant' });
        });
    };

    window.addEventListener('pageshow', restaurarPosicion);

    document.addEventListener('click', (evento) => {
        if (evento.defaultPrevented || evento.button !== 0 || evento.metaKey || evento.ctrlKey || evento.shiftKey || evento.altKey) return;

        const enlace = evento.target instanceof Element ? evento.target.closest('a[href]') : null;
        if (!enlace || (enlace.target && enlace.target !== '_self') || enlace.hasAttribute('download')) return;

        const etiqueta = `${enlace.getAttribute('aria-label') || ''} ${enlace.textContent || ''}`;
        if (!/\bvolver\b/i.test(etiqueta)) return;

        const destino = new URL(enlace.href, window.location.href);
        const paginasCategoria = ['eventos.php', 'turismo.php', 'lugares.php', 'gastronomia.php'];
        const paginaDestino = destino.pathname.split('/').pop();
        if (destino.origin !== window.location.origin || !paginasCategoria.includes(paginaDestino)) return;

        const posicion = sessionStorage.getItem(scrollKeyForPath(destino.pathname));
        if (posicion === null) return;
        sessionStorage.setItem(pendingReturnKey, JSON.stringify({
            pathname: destino.pathname,
            posicion
        }));
    });
})();
