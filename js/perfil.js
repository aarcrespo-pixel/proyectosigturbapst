(() => {
    const profile = document.querySelector('.perfil-publico');
    const followButton = document.getElementById('followButton');
    const messageButton = document.getElementById('messageButton');
    if (!profile) return;

    const userId = profile.dataset.userId;
    const phpBase = window.location.pathname.includes('/php/') ? `${window.location.pathname.split('/php/')[0]}/php/` : 'php/';

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
