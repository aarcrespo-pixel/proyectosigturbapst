(() => {
    const profile = document.querySelector('.perfil-publico');
    const followButton = document.getElementById('followButton');
    const messageButton = document.getElementById('messageButton');
    if (!profile) return;

    const userId = profile.dataset.userId;
    const phpBase = window.location.pathname.includes('/php/') ? `${window.location.pathname.split('/php/')[0]}/php/` : 'php/';
    const photoButtons = [...document.querySelectorAll('.perfil-gallery__open')];
    const lightbox = document.getElementById('perfil-lightbox');
    const lightboxImage = document.getElementById('perfil-lightbox-image');
    const lightboxTitle = document.getElementById('perfil-lightbox-title');
    const lightboxDescription = document.getElementById('perfil-lightbox-description');
    const lightboxDate = document.getElementById('perfil-lightbox-date');
    const lightboxCount = document.getElementById('perfil-lightbox-count');
    let activePhotoIndex = 0;

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
            document.getElementById('cnt-likes').textContent = Number(data.likes || 0).toLocaleString('es-UY');
        } catch (error) {
            // Los contadores permanecen en cero si el endpoint no está disponible.
        }
    }
    cargarEstadisticas();

    // La acción de seguir usa el estado devuelto por el servidor para que el botón nunca quede desincronizado.
    followButton?.addEventListener('click', async () => {
        if (followButton.dataset.requiresLogin) { window.location.href = `${phpBase}login.php`; return; }
        followButton.disabled = true;
        try {
            const response = await fetch(`${phpBase}api/seguir.php`, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' }, body: new URLSearchParams({ usuario_id: userId }) });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'No se pudo actualizar el seguimiento.');
            followButton.textContent = data.label;
            followButton.classList.toggle('is-following', data.following);
            cargarEstadisticas();
        } catch (error) {
            window.sigturAlert?.(error.message);
        } finally {
            followButton.disabled = false;
        }
    });

    // El widget global concentra las conversaciones y recibe el usuario desde el perfil.
    messageButton?.addEventListener('click', () => {
        if (messageButton.dataset.requiresLogin) { window.location.href = `${phpBase}login.php`; return; }
        window.dispatchEvent(new CustomEvent('sigtur:abrir-mensajes', { detail: { userId, name: profile.querySelector('h1')?.textContent?.trim() || 'Usuario' } }));
    });
})();
