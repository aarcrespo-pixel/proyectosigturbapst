(function () {
    const STORAGE_KEY = 'sigtur-card-engagement';
    const USER_STORAGE_KEY = 'sigtur-usuario';
    const projectPhpPath = window.location.pathname.includes('/php/')
        ? `${window.location.pathname.split('/php/')[0]}/php/`
        : 'php/';
    const COMMENTS_API_URL = `${projectPhpPath}comentarios.php`;
    const cardSelectors = '.tarjeta, .evento-card, .anteriores-card, .tarjeta-lugar, .tarjeta-interes, .tarjeta-galeria, .tarjeta-destino, .tarjeta-ruta, .gallery-engagement-card';
    const commentsDisabled = document.querySelector('main.pagina-turismo, main.pagina-lugares');

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
        state.comments = (state.comments || []).map((comment) => typeof comment === 'string'
            ? { name: 'Usuario SIGTUR', text: comment, likes: 0, replies: [] }
            : { name: comment.name || 'Usuario SIGTUR', text: comment.text || '', likes: comment.likes || 0, replies: comment.replies || [] });
        return state;
    }

    function writeState(key, state) {
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
                                : card.classList.contains('anteriores-card')
                                    ? 'anterior'
                                    : 'tarjeta';

        return `${prefix}-${slugifyText(text)}`;
    }

    function renderComments(list, container, expanded) {
        if (!container) return;
        if (!list.length) {
            container.innerHTML = '<li class="card-engagement__placeholder">Sé el primero en dejar un comentario.</li>';
            return;
        }

        const visibleComments = (expanded ? list : list.slice(0, 1)).map((comment, index) => ({
            comment,
            index: expanded ? index : list.indexOf(comment)
        }));
        container.innerHTML = visibleComments
            .map(({ comment, index }) => `
                <li class="card-engagement__comment-item">
                    <img class="card-engagement__comment-avatar" src="${escapeHtml(getCommentAvatar(comment))}" alt="Avatar de ${escapeHtml(comment.name)}">
                    <div class="card-engagement__comment-body">
                        <strong class="card-engagement__comment-name">${escapeHtml(comment.name)}</strong>
                        <span>${escapeHtml(comment.text)}</span>
                        <div class="card-engagement__comment-actions">
                            <button type="button" class="card-engagement__comment-action ${comment.likes ? 'active' : ''}" data-comment-action="like" data-index="${index}">♥ ${comment.likes || 0}</button>
                            <button type="button" class="card-engagement__comment-action" data-comment-action="reply" data-index="${index}">Comentar</button>
                            <button type="button" class="card-engagement__delete" data-index="${index}" aria-label="Eliminar comentario">Eliminar</button>
                        </div>
                    </div>
                    <div class="card-engagement__replies">${(comment.replies || []).map((reply) => `<div class="card-engagement__reply"><strong>${escapeHtml(reply.name || 'Usuario SIGTUR')}</strong> ${escapeHtml(reply.text || reply)}</div>`).join('')}</div>
                    <form class="card-engagement__reply-form" data-index="${index}">
                        <input class="card-engagement__reply-input" maxlength="140" placeholder="Responder a este comentario">
                        <button class="card-engagement__reply-submit" type="submit">Responder</button>
                    </form>
                </li>`)
            .join('');
    }

    function attachCardInteractions(card) {
        if (!card || card.querySelector('.card-engagement')) return;
        if (commentsDisabled) return;

        const key = getItemKey(card);
        const state = readState(key);
        const likeOnly = card.dataset.engagement === 'like-only';
        const commentsOnly = card.dataset.engagement === 'comments-only';
        let commentsOffset = 0;
        let commentsHasMore = true;
        let commentsLoading = false;

        const wrapper = document.createElement('div');
        wrapper.className = 'card-engagement';
        const commentControls = likeOnly || commentsDisabled ? '' : `
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
                    updateView();
                    actionButton.closest('.card-engagement__comment-item')?.querySelector('.card-engagement__reply-form')?.classList.toggle('open');
                }
                return;
            }
            const button = event.target.closest('.card-engagement__delete');
            if (!button) return;
            const index = Number(button.dataset.index);
            const currentState = readState(key);
            currentState.comments = currentState.comments.filter((_, i) => i !== index);
            writeState(key, currentState);
            updateView();
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
            comment.replies = [...(comment.replies || []), { name: getCurrentUserName(), text }];
            writeState(key, currentState);
            updateView();
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
                window.alert(error.message);
            }
        });

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
