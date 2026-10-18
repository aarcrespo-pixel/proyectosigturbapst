(() => {
    const profile = document.querySelector('.perfil-publico');
    const followButton = document.getElementById('followButton');
    const messageButton = document.getElementById('messageButton');
    if (!profile) return;

    const profileOptions = profile.querySelector('[data-profile-options]');
    const optionsToggle = profileOptions?.querySelector('[data-profile-options-toggle]');
    const optionsMenu = profileOptions?.querySelector('.profile-options__menu');
    const privacyToggle = profileOptions?.querySelector('[data-profile-privacy-toggle]');
    const optionsStatus = profileOptions?.querySelector('[data-profile-options-status]');
    const followRequestsToggle = profileOptions?.querySelector('[data-follow-requests-toggle]');
    const followRequestsPanel = profileOptions?.querySelector('[data-follow-requests-panel]');

    optionsToggle?.addEventListener('click', () => {
        const abrir = optionsToggle.getAttribute('aria-expanded') !== 'true';
        optionsToggle.setAttribute('aria-expanded', String(abrir));
        optionsMenu.hidden = !abrir;
    });

    followRequestsToggle?.addEventListener('click', () => {
        const abrir = followRequestsToggle.getAttribute('aria-expanded') !== 'true';
        followRequestsToggle.setAttribute('aria-expanded', String(abrir));
        followRequestsPanel.hidden = !abrir;
    });

    document.addEventListener('click', (event) => {
        if (profileOptions && !profileOptions.contains(event.target)) {
            optionsMenu.hidden = true;
            optionsToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && optionsMenu && !optionsMenu.hidden) {
            optionsMenu.hidden = true;
            optionsToggle?.setAttribute('aria-expanded', 'false');
            optionsToggle?.focus();
        }
    });

    privacyToggle?.addEventListener('click', async () => {
        const perfilPublico = profile.dataset.profilePublic === '1';
        privacyToggle.disabled = true;
        if (optionsStatus) optionsStatus.textContent = 'Guardando…';
        try {
            const response = await fetch(profileOptions.dataset.api, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                body: new URLSearchParams({
                    perfil_publico: perfilPublico ? '0' : '1',
                    csrf_token: profileOptions.dataset.csrf || ''
                })
            });
            const data = await response.json();
            if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo cambiar la privacidad.');
            profile.dataset.profilePublic = data.perfil_publico ? '1' : '0';
            privacyToggle.textContent = data.perfil_publico ? 'Hacer perfil privado' : 'Hacer perfil público';
            if (optionsStatus) optionsStatus.textContent = data.message;
            window.location.reload();
        } catch (error) {
            if (optionsStatus) optionsStatus.textContent = error.message || 'No se pudo cambiar la privacidad.';
        } finally {
            privacyToggle.disabled = false;
        }
    });

    const userId = profile.dataset.userId;
    const phpBase = window.location.pathname.includes('/php/') ? `${window.location.pathname.split('/php/')[0]}/php/` : 'php/';
    const publicationUploader = profile.querySelector('[data-profile-publish]');
    const publicationInput = publicationUploader?.querySelector('[data-publication-input]');
    const publicationOpen = publicationUploader?.querySelector('[data-publication-open]');
    const publicationStatus = publicationUploader?.querySelector('[data-publication-status]');

    publicationOpen?.addEventListener('click', () => publicationInput?.click());
    publicationInput?.addEventListener('change', async () => {
        const image = publicationInput.files?.[0];
        if (!image) return;
        publicationOpen.disabled = true;
        if (publicationStatus) publicationStatus.textContent = 'Publicando…';
        const body = new FormData();
        body.append('accion', 'publicar');
        body.append('csrf_token', profile.dataset.csrf || '');
        body.append('imagen', image);
        try {
            const response = await fetch(`${phpBase}api/perfil_publicaciones.php`, { method: 'POST', body, headers: { Accept: 'application/json' } });
            const data = await response.json();
            if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo publicar la imagen.');
            window.location.reload();
        } catch (error) {
            if (publicationStatus) publicationStatus.textContent = error.message || 'No se pudo publicar la imagen.';
            publicationInput.value = '';
        } finally {
            publicationOpen.disabled = false;
        }
    });

    profile.querySelectorAll('[data-publication-delete]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!window.confirm('¿Eliminar esta publicación? También se borrarán sus comentarios y likes.')) return;
            button.disabled = true;
            try {
                const response = await fetch(`${phpBase}api/perfil_publicaciones.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                    body: new URLSearchParams({
                        accion: 'eliminar',
                        publicacion_id: button.dataset.publicationId,
                        csrf_token: profile.dataset.csrf || ''
                    })
                });
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo eliminar la publicación.');
                window.location.reload();
            } catch (error) {
                window.sigturAlert?.(error.message || 'No se pudo eliminar la publicación.');
                button.disabled = false;
            }
        });
    });

    profile.querySelectorAll('[data-publication-like]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (button.dataset.requiresLogin === '1') {
                window.location.href = `${phpBase}login.php`;
                return;
            }
            button.disabled = true;
            try {
                const response = await fetch(`${phpBase}api/perfil_publicaciones.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                    body: new URLSearchParams({
                        accion: 'like',
                        publicacion_id: button.dataset.publicationId,
                        csrf_token: profile.dataset.csrf || ''
                    })
                });
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo registrar el like.');
                button.classList.toggle('is-liked', data.liked);
                button.setAttribute('aria-pressed', String(data.liked));
                button.setAttribute('aria-label', `Me gusta, ${data.likes}`);
                button.querySelector('[data-like-icon]').textContent = data.liked ? '♥' : '♡';
                button.querySelector('[data-like-count]').textContent = data.likes;
                cargarEstadisticas();
            } catch (error) {
                window.sigturAlert?.(error.message || 'No se pudo registrar el like.');
            } finally {
                button.disabled = false;
            }
        });
    });

    const photoButtons = [...document.querySelectorAll('.perfil-gallery__open')];
    const lightbox = document.getElementById('perfil-lightbox');
    const lightboxImage = document.getElementById('perfil-lightbox-image');
    const lightboxTitle = document.getElementById('perfil-lightbox-title');
    const lightboxDescription = document.getElementById('perfil-lightbox-description');
    const lightboxDate = document.getElementById('perfil-lightbox-date');
    const lightboxCount = document.getElementById('perfil-lightbox-count');
    const connectionsModal = document.getElementById('perfil-connections-modal');
    const connectionsTitle = document.getElementById('perfil-connections-title');
    const connectionsStatus = connectionsModal?.querySelector('[data-connections-status]');
    const connectionsList = connectionsModal?.querySelector('[data-connections-list]');
    let connectionsReturnFocus = null;
    let activePhotoIndex = 0;

    function closeConnectionsModal() {
        if (!connectionsModal) return;
        connectionsModal.hidden = true;
        connectionsModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('profile-connections-open');
        connectionsReturnFocus?.focus();
    }

    connectionsModal?.querySelectorAll('[data-connections-close]').forEach((button) => {
        button.addEventListener('click', closeConnectionsModal);
    });
    connectionsModal?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeConnectionsModal();
    });

    profile.querySelectorAll('[data-connection-list]').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!connectionsModal || !connectionsList || !connectionsStatus) return;
            const tipoLista = button.dataset.connectionList;
            connectionsReturnFocus = button;
            connectionsTitle.textContent = tipoLista === 'seguidores' ? 'Seguidores' : 'Seguidos';
            connectionsStatus.textContent = 'Cargando usuarios…';
            connectionsList.replaceChildren();
            connectionsModal.hidden = false;
            connectionsModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('profile-connections-open');
            connectionsModal.querySelector('[data-connections-close]')?.focus();

            try {
                const response = await fetch(`${phpBase}api/obtener_perfil_stats.php?id=${encodeURIComponent(userId)}&lista=${encodeURIComponent(tipoLista)}`);
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudo cargar la lista.');
                const usuarios = Array.isArray(data.usuarios) ? data.usuarios : [];
                if (!usuarios.length) {
                    connectionsStatus.textContent = tipoLista === 'seguidores' ? 'Todavía no tiene seguidores.' : 'Todavía no sigue a nadie.';
                    return;
                }
                connectionsStatus.textContent = '';
                usuarios.forEach((usuario) => {
                    const item = document.createElement('li');
                    const link = document.createElement('a');
                    link.className = 'perfil-connections-list__user';
                    link.href = `${phpBase}perfil.php?id=${encodeURIComponent(usuario.id)}`;
                    const avatar = document.createElement('img');
                    avatar.src = usuario.avatar_url || '../img/user.png';
                    avatar.alt = '';
                    const identity = document.createElement('span');
                    identity.className = 'perfil-connections-list__identity';
                    const name = document.createElement('strong');
                    name.textContent = usuario.nickname || usuario.nombre || 'Usuario';
                    const detail = document.createElement('small');
                    detail.textContent = usuario.nickname ? (usuario.nombre || '') : '';
                    identity.append(name, detail);
                    link.append(avatar, identity);
                    item.append(link);
                    connectionsList.append(item);
                });
            } catch (error) {
                connectionsStatus.textContent = error.message || 'No se pudo cargar la lista.';
            }
        });
    });

    function renderProfilePhoto(index) {
        if (!lightbox || !photoButtons.length) return;
        activePhotoIndex = (index + photoButtons.length) % photoButtons.length;
        const photo = photoButtons[activePhotoIndex];
        const description = photo.dataset.description || '';
        lightboxImage.src = photo.dataset.image;
        lightboxImage.alt = description || `Foto compartida por ${profile.querySelector('h1')?.textContent?.trim() || 'usuario'}`;
        lightboxTitle.textContent = profile.querySelector('h1')?.textContent?.trim() || 'Foto compartida';
        lightboxDescription.textContent = description || 'Foto compartida en SIGTUR.';
        lightboxDate.textContent = photo.dataset.date || '';
        lightboxCount.textContent = `${activePhotoIndex + 1} de ${photoButtons.length}`;

        const previousCard = document.getElementById('perfil-lightbox-engagement');
        const engagementCard = document.createElement('article');
        engagementCard.className = 'gallery-engagement-card';
        engagementCard.id = 'perfil-lightbox-engagement';
        engagementCard.dataset.itemKey = photo.dataset.itemKey;
        if (photo.dataset.itemKey.startsWith('publicacion-')) engagementCard.dataset.engagement = 'comments-only';
        previousCard?.replaceWith(engagementCard);
        window.initInteractiveCards?.(engagementCard);
    }

    function openProfilePhoto(index) {
        if (!lightbox) return;
        renderProfilePhoto(index);
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lightbox-open');
        lightbox.querySelector('[data-profile-gallery-close]')?.focus();
    }

    function closeProfilePhoto() {
        if (!lightbox) return;
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lightbox-open');
        photoButtons[activePhotoIndex]?.focus();
    }

    photoButtons.forEach((button, index) => button.addEventListener('click', () => openProfilePhoto(index)));
    lightbox?.querySelectorAll('[data-profile-gallery-close]').forEach((button) => button.addEventListener('click', closeProfilePhoto));
    lightbox?.querySelector('.perfil-lightbox__arrow--previous')?.addEventListener('click', () => renderProfilePhoto(activePhotoIndex - 1));
    lightbox?.querySelector('.perfil-lightbox__arrow--next')?.addEventListener('click', () => renderProfilePhoto(activePhotoIndex + 1));
    document.addEventListener('keydown', (event) => {
        if (!lightbox?.classList.contains('is-open')) return;
        if (event.key === 'Escape') closeProfilePhoto();
        if (event.key === 'ArrowLeft') renderProfilePhoto(activePhotoIndex - 1);
        if (event.key === 'ArrowRight') renderProfilePhoto(activePhotoIndex + 1);
    });

    async function cargarEstadisticas() {
        try {
            const response = await fetch(`${phpBase}api/obtener_perfil_stats.php?id=${encodeURIComponent(userId)}`);
            const data = await response.json();
            if (!response.ok || data.success !== true) throw new Error(data.message || 'No se pudieron cargar las estadísticas.');
            document.getElementById('cnt-seguidores').textContent = Number(data.seguidores || 0).toLocaleString('es-UY');
            document.getElementById('cnt-seguidos').textContent = Number(data.seguidos || 0).toLocaleString('es-UY');
            document.getElementById('cnt-likes').textContent = Number(data.likes || 0).toLocaleString('es-UY');
        } catch (error) {
            // Los contadores permanecen en cero si el endpoint no está disponible.
        }
    }
    cargarEstadisticas();

    const followStatus = profile.querySelector('[data-follow-status]');
    let pendingFollowPoll = null;

    const actualizarEstadoSeguimiento = (data) => {
        const estabaPendiente = profile.dataset.pendingFollow === '1';
        profile.dataset.following = data.following ? '1' : '0';
        profile.dataset.pendingFollow = data.pending ? '1' : '0';
        followButton.textContent = data.label;
        followButton.classList.toggle('is-following', data.following);
        followButton.disabled = data.pending;
        if (data.following) {
            if (followStatus) followStatus.textContent = estabaPendiente ? 'Solicitud aceptada.' : '';
            if (profile.dataset.profilePublic === '0') window.location.reload();
            cargarEstadisticas();
        } else if (!data.pending && estabaPendiente && followStatus) {
            followStatus.textContent = 'La solicitud ya no está pendiente.';
        }
    };

    const consultarEstadoSolicitud = async () => {
        if (!followButton || profile.dataset.pendingFollow !== '1') {
            window.clearInterval(pendingFollowPoll);
            pendingFollowPoll = null;
            return;
        }
        try {
            const response = await fetch(`${phpBase}api/seguir.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                body: new URLSearchParams({
                    accion: 'estado',
                    usuario_id: userId,
                    csrf_token: profile.dataset.csrf || ''
                })
            });
            const data = await response.json();
            if (!response.ok || data.success !== true) return;
            actualizarEstadoSeguimiento(data);
            if (!data.pending) {
                window.clearInterval(pendingFollowPoll);
                pendingFollowPoll = null;
            }
        } catch (error) {
            // La siguiente consulta vuelve a intentar sincronizar la solicitud.
        }
    };

    followButton?.addEventListener('click', async () => {
        if (followButton.dataset.requiresLogin === '1') { window.location.href = `${phpBase}login.php`; return; }
        followButton.disabled = true;
        if (followStatus) followStatus.textContent = '';
        try {
            const response = await fetch(`${phpBase}api/seguir.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                body: new URLSearchParams({
                    accion: 'toggle',
                    usuario_id: userId,
                    csrf_token: profile.dataset.csrf || ''
                })
            });
            const data = await response.json();
            if (!response.ok || data.success !== true) throw new Error(data.error || 'No se pudo actualizar el seguimiento.');
            actualizarEstadoSeguimiento(data);
            if (data.pending) {
                if (followStatus) followStatus.textContent = 'Tu solicitud está pendiente de aprobación.';
                if (!pendingFollowPoll) pendingFollowPoll = window.setInterval(consultarEstadoSolicitud, 4000);
            }
        } catch (error) {
            followButton.disabled = false;
            if (followStatus) followStatus.textContent = error.message || 'No se pudo actualizar el seguimiento.';
            else window.sigturAlert?.(error.message);
        }
    });

    profile.querySelectorAll('[data-follow-request-action]').forEach((button) => {
        button.addEventListener('click', async () => {
            const requestCard = button.closest('[data-follow-request]');
            if (!requestCard) return;
            const actionButtons = requestCard.querySelectorAll('[data-follow-request-action]');
            actionButtons.forEach((actionButton) => { actionButton.disabled = true; });
            try {
                const response = await fetch(`${phpBase}api/seguir.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                    body: new URLSearchParams({
                        accion: button.dataset.followRequestAction,
                        solicitante_id: requestCard.dataset.requesterId,
                        csrf_token: profile.dataset.csrf || ''
                    })
                });
                const data = await response.json();
                if (!response.ok || data.success !== true) throw new Error(data.error || 'No se pudo procesar la solicitud.');
                requestCard.remove();
                const requestCount = profileOptions.querySelector('[data-follow-request-count]');
                if (requestCount) requestCount.textContent = String(Math.max(0, Number(requestCount.textContent) - 1));
                if (button.dataset.followRequestAction === 'aceptar') cargarEstadisticas();
                const requestList = profile.querySelector('.perfil-follow-requests__list');
                if (requestList && !requestList.children.length) {
                    requestList.outerHTML = '<p class="perfil-follow-requests__empty">No hay solicitudes pendientes.</p>';
                }
            } catch (error) {
                actionButtons.forEach((actionButton) => { actionButton.disabled = false; });
                window.sigturAlert?.(error.message || 'No se pudo procesar la solicitud.');
            }
        });
    });

    if (followButton && profile.dataset.pendingFollow === '1') {
        pendingFollowPoll = window.setInterval(consultarEstadoSolicitud, 4000);
    }

    // El widget global concentra las conversaciones y recibe el usuario desde el perfil.
    messageButton?.addEventListener('click', () => {
        if (messageButton.dataset.requiresLogin) { window.location.href = `${phpBase}login.php`; return; }
        window.dispatchEvent(new CustomEvent('sigtur:abrir-mensajes', { detail: { userId, name: profile.querySelector('h1')?.textContent?.trim() || 'Usuario' } }));
    });
})();
