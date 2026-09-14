(function () {
    const SUPPORTED_LANGUAGES = new Set(['es', 'en', 'pt']);
    const LANGUAGE_STORAGE_KEY = 'sigtur-idioma';
    const CACHE_PREFIX = 'i18n_';
    const pendingTranslations = new Map();
    let scanTimer = null;

    /* Convertimos cada frase en una clave estable y corta para que la caché
       distinga idioma y texto sin guardar contenido directamente en el nombre
       de la propiedad de localStorage. */
    function createCacheKey(language, source) {
        let hash = 0;
        for (let index = 0; index < source.length; index += 1) {
            hash = ((hash << 5) - hash) + source.charCodeAt(index);
            hash |= 0;
        }
        return `${CACHE_PREFIX}${language}_${Math.abs(hash)}`;
    }

    function readCache(language, source) {
        try {
            return localStorage.getItem(createCacheKey(language, source));
        } catch (error) {
            return null;
        }
    }

    function writeCache(language, source, translation) {
        try {
            localStorage.setItem(createCacheKey(language, source), translation);
        } catch (error) {
            // La traducción sigue funcionando aunque el navegador bloquee el almacenamiento.
        }
    }

    function getActiveLanguage() {
        const documentLanguage = document.documentElement.dataset.lang;
        const storedLanguage = localStorage.getItem(LANGUAGE_STORAGE_KEY);
        const language = documentLanguage || storedLanguage || 'es';
        return SUPPORTED_LANGUAGES.has(language) ? language : 'es';
    }

    /* Capturamos el español original durante la fase defer, antes de que
       script.js aplique sus traducciones locales. Así la API siempre recibe
       una frase fuente estable aunque el usuario cambie de idioma. */
    function captureOriginalValues(root = document) {
        const selector = root === document ? 'body *' : ':scope, :scope *';
        root.querySelectorAll?.(selector).forEach((element) => {
            if (element.matches('script, style, noscript, svg, .weather-widget, [data-translator-ignore]')) return;
            if (!element.dataset.translatorSource && element.children.length === 0 && element.textContent.trim()) {
                element.dataset.translatorSource = element.textContent.trim();
            }
            ['placeholder', 'alt', 'title'].forEach((attribute) => {
                if (element.hasAttribute(attribute) && !element.dataset[`translator${attribute}Source`]) {
                    element.dataset[`translator${attribute}Source`] = element.getAttribute(attribute);
                }
            });
        });
    }

    function isUsefulSource(source) {
        return Boolean(source && source.trim() && /[A-Za-zÁÉÍÓÚáéíóúÑñ]/.test(source));
    }

    function canUseLocalTranslation(element, source, language) {
        const hasLocalKey = ['data-i18n', 'data-i18n-placeholder', 'data-i18n-alt', 'data-i18n-title']
            .some((attribute) => element.hasAttribute(attribute));
        return hasLocalKey && element.dataset.translatorLanguage !== language && element.textContent.trim() !== source;
    }

    /* Esta función concentra la llamada fetch: primero reutiliza memoria o
       localStorage y solo abre una petición asíncrona si la frase aún no fue
       traducida. El mapa pending evita dos requests simultáneos idénticos. */
    async function translatePhrase(source, language) {
        const normalizedSource = source.trim();
        const cachedTranslation = readCache(language, normalizedSource);
        if (cachedTranslation) return cachedTranslation;

        const pendingKey = `${language}:${normalizedSource}`;
        if (pendingTranslations.has(pendingKey)) return pendingTranslations.get(pendingKey);

        const request = fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(normalizedSource)}&langpair=es|${language}`)
            .then((response) => {
                if (!response.ok) throw new Error(`MyMemory respondió ${response.status}`);
                return response.json();
            })
            .then((payload) => payload.responseData?.translatedText || normalizedSource)
            .then((translation) => {
                writeCache(language, normalizedSource, translation);
                return translation;
            })
            .catch(() => normalizedSource)
            .finally(() => pendingTranslations.delete(pendingKey));

        pendingTranslations.set(pendingKey, request);
        return request;
    }

    async function translateElement(element, language) {
        if (element.dataset.translatorLanguage === language) return;
        const source = element.dataset.translatorSource;
        if (element.children.length === 0 && isUsefulSource(source) && !canUseLocalTranslation(element, source, language)) {
            element.textContent = await translatePhrase(source, language);
            element.dataset.translatorLanguage = language;
        }

        for (const attribute of ['placeholder', 'alt', 'title']) {
            const sourceAttribute = element.dataset[`translator${attribute}Source`];
            if (!isUsefulSource(sourceAttribute)) continue;
            const hasLocalKey = ['data-i18n', `data-i18n-${attribute}`].some((key) => element.hasAttribute(key));
            if (hasLocalKey && element.dataset.translatorLanguage !== language && element.getAttribute(attribute) !== sourceAttribute) continue;
            element.setAttribute(attribute, await translatePhrase(sourceAttribute, language));
            element.dataset.translatorLanguage = language;
        }
    }

    function restoreSpanish() {
        document.querySelectorAll('[data-translator-language]').forEach((element) => {
            if (element.children.length === 0 && element.dataset.translatorSource) {
                element.textContent = element.dataset.translatorSource;
            }
            ['placeholder', 'alt', 'title'].forEach((attribute) => {
                const source = element.dataset[`translator${attribute}Source`];
                if (source) element.setAttribute(attribute, source);
            });
            delete element.dataset.translatorLanguage;
        });
    }

    async function translateDocument() {
        const language = getActiveLanguage();
        captureOriginalValues();
        if (language === 'es') {
            restoreSpanish();
            return;
        }

        const elements = [...document.querySelectorAll('[data-translator-source], [placeholder], [alt], [title]')]
            .filter((element) => !element.matches('script, style, noscript, svg, .weather-widget, [data-translator-ignore]'));
        await Promise.all(elements.map((element) => translateElement(element, language)));
    }

    function scheduleTranslation() {
        clearTimeout(scanTimer);
        scanTimer = setTimeout(() => translateDocument(), 80);
    }

    /* Las alertas nativas no son nodos del DOM, por eso ofrecemos una pequeña
       API global que traduce su mensaje con la misma caché antes de mostrarlo. */
    window.sigturTranslate = async (source) => {
        const language = getActiveLanguage();
        return language === 'es' ? source : translatePhrase(source, language);
    };
    window.sigturAlert = async (source) => {
        window.alert(await window.sigturTranslate(source));
    };

    /* MutationObserver vuelve a ejecutar el traductor cuando un fetch local,
       un filtro o un modal agrega nodos: de esta forma el DOM dinámico obtiene
       exactamente la misma caché y el mismo idioma que la vista inicial. */
    function observeDynamicContent() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) captureOriginalValues(node);
                });
            });
            scheduleTranslation();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Se ejecuta mientras los scripts defer aún conservan el HTML fuente en español.
    captureOriginalValues();

    document.addEventListener('DOMContentLoaded', () => {
        translateDocument();
        observeDynamicContent();
    });
    document.addEventListener('sigtur:language-changed', scheduleTranslation);
}());