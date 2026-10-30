(() => {
    'use strict';

    const excludedTags = 'A, BUTTON, CODE, INPUT, OPTION, PRE, SCRIPT, SELECT, STYLE, TEXTAREA';
    const tokenPattern = /(^|[\s([{])(@[A-Za-z0-9._]{1,30}|https?:\/\/[^\s<>"']+|www\.[^\s<>"']+)/g;
    const trailingPunctuation = /[.,!?;:)\]}]+$/;

    function crearEnlace(token) {
        const enlace = document.createElement('a');
        enlace.className = 'texto-auto-enlace';
        enlace.target = '_blank';
        enlace.rel = 'noopener noreferrer';

        if (token.startsWith('@')) {
            const usuario = token.slice(1).replace(/\.+$/, '');
            if (!usuario) return null;
            enlace.href = `https://www.instagram.com/${encodeURIComponent(usuario)}/`;
            enlace.textContent = `@${usuario}`;
            return { enlace, resto: token.slice(usuario.length + 1) };
        }

        const urlTexto = token.replace(trailingPunctuation, '');
        if (!urlTexto) return null;
        const url = urlTexto.startsWith('www.') ? `https://${urlTexto}` : urlTexto;
        try {
            const urlValidada = new URL(url);
            if (!['http:', 'https:'].includes(urlValidada.protocol)) return null;
            enlace.href = urlValidada.href;
            enlace.textContent = urlTexto;
        } catch {
            return null;
        }
        return { enlace, resto: token.slice(urlTexto.length) };
    }

    function enlazarTexto(nodo) {
        const texto = nodo.nodeValue;
        tokenPattern.lastIndex = 0;
        if (!tokenPattern.test(texto)) return;
        tokenPattern.lastIndex = 0;

        const fragmento = document.createDocumentFragment();
        let posicion = 0;
        let coincidencia;
        while ((coincidencia = tokenPattern.exec(texto)) !== null) {
            const inicioToken = coincidencia.index + coincidencia[1].length;
            const token = coincidencia[2];
            const enlaceData = crearEnlace(token);
            if (!enlaceData) continue;

            fragmento.append(document.createTextNode(texto.slice(posicion, inicioToken)));
            fragmento.append(enlaceData.enlace);
            if (enlaceData.resto) fragmento.append(document.createTextNode(enlaceData.resto));
            posicion = inicioToken + token.length;
        }
        if (posicion === 0) return;
        fragmento.append(document.createTextNode(texto.slice(posicion)));
        nodo.replaceWith(fragmento);
    }

    function enlazarContenido(raiz) {
        if (raiz.nodeType === Node.TEXT_NODE) {
            if (!raiz.parentElement?.closest(excludedTags)) enlazarTexto(raiz);
            return;
        }
        if (raiz.nodeType !== Node.ELEMENT_NODE || raiz.closest(excludedTags)) return;

        const walker = document.createTreeWalker(raiz, NodeFilter.SHOW_TEXT, {
            acceptNode(nodo) {
                return nodo.parentElement?.closest(excludedTags)
                    ? NodeFilter.FILTER_REJECT
                    : NodeFilter.FILTER_ACCEPT;
            }
        });
        const textos = [];
        while (walker.nextNode()) textos.push(walker.currentNode);
        textos.forEach(enlazarTexto);
    }

    function iniciar() {
        enlazarContenido(document.body);
        new MutationObserver((mutaciones) => {
            mutaciones.forEach((mutacion) => {
                mutacion.addedNodes.forEach(enlazarContenido);
            });
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciar, { once: true });
    } else {
        iniciar();
    }
})();
