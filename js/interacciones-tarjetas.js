(function () {
    const STORAGE_KEY = 'sigtur-card-engagement';
    const USER_STORAGE_KEY = 'sigtur-usuario';
    const projectPhpPath = window.location.pathname.includes('/php/')
        ? `${window.location.pathname.split('/php/')[0]}/php/`
        : 'php/';
            /* Derivamos la base PHP desde la URL actual porque este script se carga
               desde index.php, /php/ y subcarpetas de eventos. */
    const COMMENTS_API_URL = `${projectPhpPath}comentarios.php`;
    const DELETE_COMMENT_API_URL = `${projectPhpPath}comentarios.php`;
    const cardSelectors = '.tarjeta, .evento-card, .evento-card-pasado, .tarjeta-lugar, .tarjeta-interes, .tarjeta-galeria, .tarjeta-destino, .tarjeta-ruta, .gallery-engagement-card';

    function slugifyText(value) {
        return (value || '')
            .toString()
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(/\p{Diacritic}/gu, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '') || 'item';
    }

    function getStoredData() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        } catch (error) {
            return {};
        }
    }
                // localStorage conserva likes y una copia de UI, pero la BD sigue siendo la fuente de comentarios.

    function saveStoredData(data) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (error) {
            // Ignorar fallos de almacenamiento local.
        }
    }

    function getCurrentUserName() {
        return localStorage.getItem(USER_STORAGE_KEY)?.trim() || 'Usuario SIGTUR';
    }

    function getCommentAvatar(comment) {
        return comment.avatar_url || '../img/user.png';
    }

    function readState(key) {
        const storage = getStoredData();
        const state = storage[key] || { likes: 0, comments: [] };
        /* Conservamos id y avatar al hidratar el estado: el ID vincula el
           nodo visual con la fila exacta que debe borrar el backend. */
        state.comments = (state.comments || []).map((comment) => typeof comment === 'string'
            ? { name: 'Usuario SIGTUR', text: comment, likes: 0, replies: [] }
            : { id: comment.id || null, user_id: comment.user_id || null, profile_url: comment.profile_url || '', name: comment.name || 'Usuario SIGTUR', avatar_url: comment.avatar_url || '', text: comment.text || '', likes: comment.likes || 0, replies: comment.replies || [] });
        return state;
    }

    function writeState(key, state) {
        /* Guardamos la representación de UI; cada recarga vuelve a pedir
           comentarios al backend y corrige cualquier estado obsoleto. */
        const storage = getStoredData();
        storage[key] = state;
        saveStoredData(storage);
    }

    function escapeHtml(value) {
        return value
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function getItemKey(card) {
        if (card.getAttribute('data-item-key')) {
            return card.getAttribute('data-item-key');
        }

        const heading = card.querySelector('h1, h2, h3, h4, .card-title, .titulo')?.textContent?.trim() || '';
        const text = heading || card.textContent?.trim() || 'item';
        const prefix = card.classList.contains('tarjeta-galeria')
            ? 'galeria'
            : card.classList.contains('tarjeta-lugar')
                ? 'lugar'
                : card.classList.contains('tarjeta-interes')
                    ? 'interes'
                    : card.classList.contains('tarjeta-destino')
                        ? 'destino'
                        : card.classList.contains('tarjeta-ruta')
                            ? 'ruta'
                            : card.classList.contains('evento-card')
                                ? 'evento'
                                : card.classList.contains('evento-card-pasado')
                                    ? 'anterior'
                                    : 'tarjeta';

        return `${prefix}-${slugifyText(text)}`;
    }
                /* Cada tarjeta necesita una clave estable para que sus comentarios no
                   se mezclen con los de otra página o fotografía. */

    function renderComments(list, container, expanded) {
        if (!container) return;
        if (!list.length) {
            container.innerHTML = '<li class="card-engagement__placeholder">Sé el primero en dejar un comentario.</li>';
            return;
        }

        const comentariosPorId = new Map(list.filter(comment => comment.id).map(comment => [comment.id, { ...comment, replies: [] }]));
        comentariosPorId.forEach(comment => {
            if (comment.parent_id && comentariosPorId.has(comment.parent_id)) comentariosPorId.get(comment.parent_id).replies.push(comment);
        });
        const arbol = [...comentariosPorId.values()].filter(comment => !comment.parent_id);
        const visibles = expanded ? arbol : arbol.slice(0, 1);
        const renderOne = (comment, index, isReply = false) => `
            <li id="comentario-${comment.id || ''}" class="card-engagement__comment-item comentario-item ${isReply ? 'comentario-item--respuesta' : ''}" data-comment-id="${comment.id || ''}">
                <div class="comentario-header-user">
                    <a class="comentario-usuario-link" href="${escapeHtml(comment.profile_url || `${projectPhpPath}perfil.php?id=${encodeURIComponent(comment.user_id || '')}`)}" aria-label="Ver perfil de ${escapeHtml(comment.name)}">
                        <img class="comentario-avatar card-engagement__comment-avatar" src="${escapeHtml(getCommentAvatar(comment))}" alt="Avatar de ${escapeHtml(comment.name)}">
                        <span class="comentario-nombre">${escapeHtml(comment.name)}</span>
                    </a>
                </div>
                <div class="comentario-cuerpo card-engagement__comment-body">
                    <div class="comentario-texto">${escapeHtml(comment.text)}</div>
                    <div class="comentario-footer-actions comentario-footer comentario-actions">
                        <button type="button" class="card-engagement__comment-action ${comment.likes ? 'active' : ''}" data-comment-action="like" data-index="${index}">♥ ${comment.likes || 0}</button>
                        ${!isReply ? `<button type="button" class="card-engagement__comment-action" data-comment-action="reply" data-index="${index}">Responder</button>` : ''}
                        <button type="button" class="card-engagement__delete" data-index="${index}" aria-label="Eliminar comentario">Eliminar</button>
                    </div>
                    ${!isReply ? `<form class="card-engagement__reply-form" data-index="${index}"><textarea class="card-engagement__reply-input" rows="1" maxlength="140" placeholder="Escribe una respuesta..."></textarea><button type="submit" class="card-engagement__reply-submit">Responder</button></form>` : ''}
                </div>
            </li>
            ${(comment.replies || []).map(reply => renderOne(reply, list.indexOf(reply), true)).join('')}`;
        /* El árbol se reconstruye desde parent_id; la consulta SQL sigue siendo
           plana y esta relación visualiza cada respuesta junto a su comentario. */
        container.innerHTML = visibles.map(comment => renderOne(comment, list.findIndex(item => item.id === comment.id))).join('');
    }

    function attachCardInteractions(card) {
        if (!card || card.querySelector('.card-engagement')) return;
        if (card.dataset.engagement === 'none') return;

        const key = getItemKey(card);
        const state = readState(key);
        const likeOnly = card.dataset.engagement === 'like-only';
        const commentsOnly = card.dataset.engagement === 'comments-only';
        let commentsOffset = 0;
        let commentsHasMore = true;
        let commentsLoading = false;

        const wrapper = document.createElement('div');
        wrapper.className = 'card-engagement';
        const commentControls = likeOnly ? '' : `
            <button type="button" class="card-engagement__button card-engagement__button--comment" data-action="comment" aria-expanded="false">
                <span class="card-engagement__icon">💬</span>
                <span class="card-engagement__count">${state.comments.length}</span>
            </button>
            <div class="card-engagement__panel" hidden>
                <ul class="card-engagement__comments"></ul>
                <p class="card-engagement__loading" hidden>Cargando comentarios...</p>
                <form class="card-engagement__form">
                    <textarea class="card-engagement__textarea" rows="1" maxlength="140" placeholder="Escribe un comentario..."></textarea>
                    <button type="submit" class="card-engagement__submit">Comentar</button>
                </form>
            </div>`;

        wrapper.innerHTML = `
            <div class="card-engagement__actions">
                ${commentsOnly ? '' : `<button type="button" class="card-engagement__button card-engagement__button--like" data-action="like" aria-pressed="${state.likes > 0}">
                    <span class="card-engagement__icon">${state.likes > 0 ? '♥' : '♡'}</span>
                    <span class="card-engagement__count">${state.likes}</span>
                </button>`}
            </div>${commentControls}`;

        card.appendChild(wrapper);

        const likeButton = wrapper.querySelector('[data-action="like"]');
        const commentButton = wrapper.querySelector('[data-action="comment"]');
        const panel = wrapper.querySelector('.card-engagement__panel');
        const commentsList = wrapper.querySelector('.card-engagement__comments');
        const commentsLoadingLabel = wrapper.querySelector('.card-engagement__loading');
        const form = wrapper.querySelector('.card-engagement__form');
        const textarea = wrapper.querySelector('.card-engagement__textarea');

        const updateView = () => {
            /* Una única función deriva contadores, likes y lista visible para
               mantener consistencia después de cualquier interacción. */
            const currentState = readState(key);
            const likes = currentState.likes || 0;
            const comments = currentState.comments || [];

            if (likeButton) {
                likeButton.innerHTML = `
                    <span class="card-engagement__icon">${likes > 0 ? '♥' : '♡'}</span>
                    <span class="card-engagement__count">${likes}</span>`;
                likeButton.classList.toggle('active', likes > 0);
                likeButton.setAttribute('aria-pressed', String(likes > 0));
            }
            if (commentButton) {
                commentButton.innerHTML = `
                    <span class="card-engagement__icon">💬</span>
                    <span class="card-engagement__count">${comments.length}</span>`;
                commentButton.setAttribute('aria-expanded', String(!panel.hidden));
            }

            const scrollTop = commentsList?.scrollTop || 0;
            renderComments(comments, commentsList, true);
            if (commentsList) commentsList.scrollTop = scrollTop;
        };

        const applyCommentsPage = (data, reset) => {
            const currentState = readState(key);
            const nuevosComentarios = Array.isArray(data.comments) ? data.comments : [];
            currentState.comments = reset
                ? nuevosComentarios
                : [...currentState.comments, ...nuevosComentarios];
            commentsOffset = reset ? nuevosComentarios.length : commentsOffset + nuevosComentarios.length;
            commentsHasMore = data.has_more === true;
            writeState(key, currentState);
            updateView();
        };

        const loadComments = async (reset = false) => {
            if (commentsLoading || (!reset && !commentsHasMore)) return;
            commentsLoading = true;
            if (commentsLoadingLabel) commentsLoadingLabel.hidden = false;

            try {
                // Fetch obtiene la página persistida y reemplaza la copia local cuando reset es true.
                const offset = reset ? 0 : commentsOffset;
                const response = await fetch(`${COMMENTS_API_URL}?item_key=${encodeURIComponent(key)}&limit=10&offset=${offset}`);
                if (!response.ok) throw new Error('No se pudieron cargar los comentarios');
                applyCommentsPage(await response.json(), reset);
            } catch (error) {
                // Mantener la vista local si el endpoint no está disponible.
            } finally {
                commentsLoading = false;
                if (commentsLoadingLabel) commentsLoadingLabel.hidden = true;
            }
        };

        likeButton?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const currentState = readState(key);
            currentState.likes = currentState.likes > 0 ? 0 : 1;
            writeState(key, currentState);
            updateView();
        });

        commentButton?.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const isHidden = panel.hidden;
            panel.hidden = !isHidden;
            if (!panel.hidden) {
                textarea?.focus();
            }
            updateView();
        });

        commentsList?.addEventListener('scroll', () => {
            const cercaDelFinal = commentsList.scrollTop + commentsList.clientHeight >= commentsList.scrollHeight - 20;
            if (cercaDelFinal) loadComments();
        });

        commentsList?.addEventListener('click', (event) => {
            const actionButton = event.target.closest('[data-comment-action]');
            if (actionButton) {
                const index = Number(actionButton.dataset.index);
                const currentState = readState(key);
                if (actionButton.dataset.commentAction === 'like') {
                    currentState.comments[index].likes = currentState.comments[index].likes ? 0 : 1;
                    writeState(key, currentState);
                    updateView();
                } else {
                    const commentIndex = Number(actionButton.dataset.index);
                    updateView();
                    commentsList.querySelector(`.card-engagement__reply-form[data-index="${commentIndex}"]`)?.classList.toggle('open');
                }
                return;
            }
            const button = event.target.closest('.card-engagement__delete');
            if (!button) return;
            const index = Number(button.dataset.index);
            const comment = readState(key).comments[index];
            if (!comment?.id) return;
            const comentarioElement = button.closest('.card-engagement__comment-item');
            button.disabled = true;
                /* Confirmamos el borrado en el servidor antes de tocar el estado
                    local; así una respuesta fallida no oculta datos persistidos. */
                fetch(DELETE_COMMENT_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: new URLSearchParams({ accion: 'eliminar', id: String(comment.id), item_key: key })
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok || data.success !== true) throw new Error(data.error || 'No se pudo eliminar el comentario');
                    document.getElementById(`comentario-${comment.id}`)?.remove();
                    comentarioElement?.remove();
                    const currentState = readState(key);
                    currentState.comments = currentState.comments.filter((item) => item.id !== comment.id);
                    writeState(key, currentState);
                    commentsOffset = Math.max(0, commentsOffset - 1);
                    updateView();
                })
                .catch((error) => {
                    button.disabled = false;
                    window.sigturAlert?.(error.message);
                });
        });

        commentsList?.addEventListener('submit', (event) => {
            const replyForm = event.target.closest('.card-engagement__reply-form');
            if (!replyForm) return;
            event.preventDefault();
            event.stopPropagation();
            const input = replyForm.querySelector('.card-engagement__reply-input');
            const text = input?.value?.trim();
            if (!text) return;
            const currentState = readState(key);
            const comment = currentState.comments[Number(replyForm.dataset.index)];
            if (!comment) return;
            fetch(COMMENTS_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: new URLSearchParams({ item_key: key, texto: text, parent_id: String(comment.id) })
            }).then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'No se pudo publicar la respuesta');
                applyCommentsPage(data, true);
            }).catch(error => window.sigturAlert?.(error.message));
        });

        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            event.stopPropagation();
            const text = textarea?.value?.trim();
            if (!text) return;
            const datos = new URLSearchParams({ item_key: key, texto: text });

            try {
                const response = await fetch(COMMENTS_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body: datos
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'No se pudo publicar el comentario');

                applyCommentsPage(data, true);
                textarea.value = '';
            } catch (error) {
                window.sigturAlert?.(error.message);
            }
        });
                        /* Publicamos por AJAX y reemplazamos el estado local con la respuesta
                           del servidor para evitar que comentarios viejos reaparezcan. */

        updateView();
        loadComments(true);
    }

    function initInteractiveCards(root = document) {
        if (!root || typeof root.querySelectorAll !== 'function') return;

        if (root.matches?.(cardSelectors)) {
            attachCardInteractions(root);
        }

        root.querySelectorAll(cardSelectors).forEach((card) => {
            attachCardInteractions(card);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initInteractiveCards(document));
    } else {
        initInteractiveCards(document);
    }

    window.initInteractiveCards = initInteractiveCards;
})();
