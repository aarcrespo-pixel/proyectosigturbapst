document.addEventListener('DOMContentLoaded', () => {
    const bell = document.querySelector('[data-notificaciones-toggle]');
    if (!bell) {
        return;
    }

    const panel = document.getElementById('notificaciones-panel');
    const list = document.getElementById('notificaciones-lista');
    const badge = document.getElementById('notificaciones-badge');
    const estadoPermiso = document.getElementById('notificaciones-estado');
    const marcarTodasBtn = document.querySelector('[data-notificaciones-mark-all]');
    const activarDesactivarBtn = document.querySelector('[data-notificaciones-toggle-permission]');
    const apiUrl = bell.dataset.api || '/php/api/notificaciones.php';
    const serviceWorkerUrl = bell.dataset.serviceWorker || '/sw.js';
    const csrfToken = bell.dataset.csrf || '';
    const avisosMostrados = new Set();
    const notificationPreferenceKey = 'sigtur-notificaciones-activadas';
    let initialLoad = true;
    const serviceWorkerScope = (() => {
        try {
            const parsed = new URL(serviceWorkerUrl, window.location.href);
            const pathname = parsed.pathname;
            if (pathname.endsWith('/sw.js')) {
                return pathname.slice(0, pathname.lastIndexOf('/') + 1) || '/';
            }
        } catch (error) {
            console.warn('No se pudo resolver el scope del Service Worker', error);
        }
        return '/';
    })();

    const actualizarBadge = (count) => {
        if (!badge) return;
        const total = Number(count || 0);
        badge.textContent = String(total);
        badge.hidden = total <= 0;
    };

    const renderLista = (items = []) => {
        if (!list) return;
        if (!items.length) {
            list.innerHTML = '<li class="notificaciones-item notificaciones-item--empty">No hay notificaciones nuevas.</li>';
            return;
        }

        if (avisosActivados() && 'Notification' in window && Notification.permission === 'granted') {
            items.filter((item) => Number(item.leida) === 0).slice(0, 3).forEach((item) => {
                const itemId = String(item.id);
                if (!initialLoad && !avisosMostrados.has(itemId)) {
                    new Notification(item.titulo || 'SIGTUR', { body: item.mensaje || 'Tenés una nueva notificación.' });
                }
                avisosMostrados.add(itemId);
            });
        }

        list.innerHTML = items.map((item) => {
            const leida = Number(item.leida) === 1;
            const fecha = item.fecha_formateada || item.fecha_creacion || '';
            return `
                <li class="notificaciones-item ${leida ? '' : 'notificaciones-item--unread'}" data-id="${Number(item.id)}">
                    <div class="notificaciones-item__head">
                        <strong>${(item.titulo || 'Notificación').replace(/</g, '&lt;')}</strong>
                        <span>${fecha}</span>
                    </div>
                    <p>${(item.mensaje || '').replace(/</g, '&lt;')}</p>
                    <div class="notificaciones-item__footer">
                        <small>${leida ? 'Leída' : 'No leída'}</small>
                        ${leida ? '' : '<button type="button" class="notificaciones-item__mark-read">Marcar</button>'}
                    </div>
                </li>
            `;
        }).join('');

        list.querySelectorAll('.notificaciones-item__mark-read').forEach((button) => {
            button.addEventListener('click', async (event) => {
                const item = event.target.closest('.notificaciones-item');
                const id = item?.dataset?.id;
                if (!id) return;
                await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    credentials: 'same-origin',
                    body: new URLSearchParams({ accion: 'leer', id, csrf_token: csrfToken })
                });
                await cargarNotificaciones();
            });
        });
    };

    const cargarNotificaciones = async () => {
        try {
            const respuesta = await fetch(`${apiUrl}?accion=lista`, { credentials: 'same-origin' });
            const datos = await respuesta.json();
            if (!datos.success) {
                return;
            }
            renderLista(datos.items || []);
            const total = (datos.items || []).filter((item) => Number(item.leida) === 0).length;
            actualizarBadge(total);
            initialLoad = false;
        } catch (error) {
            console.warn('No se pudieron cargar las notificaciones', error);
        }
    };

    const contarSinLeer = async () => {
        try {
            const respuesta = await fetch(`${apiUrl}?accion=contar`, { credentials: 'same-origin' });
            const datos = await respuesta.json();
            if (datos.success) {
                actualizarBadge(datos.count || 0);
            }
        } catch (error) {
            console.warn('No se pudo actualizar el contador', error);
        }
    };

    const actualizarEstadoPermiso = () => {
        const activadas = avisosActivados();
        if (estadoPermiso) {
            estadoPermiso.textContent = activadas ? 'Activadas' : 'Desactivadas';
        }
        if (activarDesactivarBtn) {
            activarDesactivarBtn.textContent = activadas ? 'Desactivar notificaciones' : 'Activar notificaciones';
        }
        if (!activadas || !('Notification' in window)) return;
        if (Notification.permission === 'granted') {
            if (estadoPermiso) estadoPermiso.textContent = 'Activadas';
        } else if (Notification.permission === 'denied') {
            if (estadoPermiso) estadoPermiso.textContent = 'Bloqueadas';
        }
    };

    const avisosActivados = () => localStorage.getItem(notificationPreferenceKey) === '1';

    const activarAvisosDesdeBoton = async () => {
        if (!('Notification' in window)) {
            if (estadoPermiso) estadoPermiso.textContent = 'No compatible';
            return;
        }

        if (Notification.permission === 'default') {
            try {
                await Notification.requestPermission();
            } catch (error) {
                console.warn('No se pudo solicitar el permiso de notificaciones', error);
            }
        }

        if (Notification.permission === 'granted' && 'serviceWorker' in navigator) {
            try {
                await navigator.serviceWorker.register(serviceWorkerUrl, { scope: serviceWorkerScope });
            } catch (error) {
                console.warn('No se pudo registrar el Service Worker', error);
            }
        }
        actualizarEstadoPermiso();
    };

    const cambiarEstadoAvisos = async () => {
        if (avisosActivados()) {
            localStorage.setItem(notificationPreferenceKey, '0');
        } else {
            localStorage.setItem(notificationPreferenceKey, '1');
            await activarAvisosDesdeBoton();
        }
        actualizarEstadoPermiso();
    };

    bell.addEventListener('click', async () => {
        if (!panel) return;
        if (avisosActivados()) {
            await activarAvisosDesdeBoton();
        }
        const isOpen = !panel.hidden;
        panel.hidden = isOpen;
        if (!isOpen) {
            await cargarNotificaciones();
        }
    });

    document.addEventListener('click', (event) => {
        if (!panel) return;
        if (!bell.contains(event.target) && !panel.contains(event.target)) {
            panel.hidden = true;
        }
    });

    if (marcarTodasBtn) {
        marcarTodasBtn.addEventListener('click', async () => {
            await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                credentials: 'same-origin',
                body: new URLSearchParams({ accion: 'marcar_todas', csrf_token: csrfToken })
            });
            await cargarNotificaciones();
        });
    }

    if (activarDesactivarBtn) {
        activarDesactivarBtn.addEventListener('click', cambiarEstadoAvisos);
    }

    contarSinLeer();
    cargarNotificaciones();
    actualizarEstadoPermiso();
    window.setInterval(cargarNotificaciones, 30000);
});
