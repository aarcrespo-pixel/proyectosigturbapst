document.addEventListener('DOMContentLoaded', () => {
    const bell = document.querySelector('[data-notificaciones-toggle]');
    if (!bell) {
        return;
    }

    const panel = document.getElementById('notificaciones-panel');
    const list = document.getElementById('notificaciones-lista');
    const badge = document.getElementById('notificaciones-badge');
    const estadoPermiso = document.getElementById('notificaciones-estado');
    const ayudaPermiso = document.getElementById('notificaciones-ayuda');
    const marcarTodasBtn = document.querySelector('[data-notificaciones-mark-all]');
    const activarDesactivarBtn = document.querySelector('[data-notificaciones-toggle-permission]');
    const apiUrl = bell.dataset.api || '/php/api/notificaciones.php';
    const serviceWorkerUrl = bell.dataset.serviceWorker || '/sw.js';
    const csrfToken = bell.dataset.csrf || '';
    const usuarioId = bell.dataset.userId || 'anonimo';
    const notificationPreferenceKey = `sigtur-notificaciones-activadas:${usuarioId}`;
    const notificationSeenKey = `sigtur-notificaciones-vistas:${usuarioId}`;
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
        mostrarAvisosNuevos(items);
        if (!list) return;
        if (!items.length) {
            list.innerHTML = '<li class="notificaciones-item notificaciones-item--empty">No hay notificaciones nuevas.</li>';
            return;
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
            if (!respuesta.ok) {
                throw new Error(`El servidor respondió ${respuesta.status}.`);
            }
            const datos = await respuesta.json();
            if (!datos.success) {
                throw new Error(datos.message || 'No se pudieron cargar las notificaciones.');
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
        const permisoConcedido = 'Notification' in window && Notification.permission === 'granted';
        const activadas = avisosActivados();
        if (estadoPermiso) {
            estadoPermiso.textContent = activadas ? 'Activadas' : 'Desactivadas';
        }
        if (activarDesactivarBtn) {
            activarDesactivarBtn.textContent = activadas ? 'Desactivar notificaciones' : 'Activar notificaciones';
        }
        if (ayudaPermiso) {
            if (!('Notification' in window)) {
                ayudaPermiso.textContent = activadas
                    ? 'Los avisos de SIGTUR están activados. Este navegador no permite notificaciones del sistema.'
                    : 'Este navegador no permite notificaciones del sistema.';
            } else if (Notification.permission === 'denied') {
                ayudaPermiso.textContent = activadas
                    ? 'Los avisos de SIGTUR están activados. El navegador bloquea los avisos del sistema; habilitalos en los permisos del sitio para recibirlos.'
                    : 'El navegador bloquea los avisos del sistema. Aun así, podés activar los avisos dentro de SIGTUR.';
            } else if (activadas) {
                ayudaPermiso.textContent = permisoConcedido
                    ? 'Avisos de SIGTUR y del navegador activados; recibirás recordatorios de eventos próximos.'
                    : 'Avisos dentro de SIGTUR activados. Al habilitar el permiso del navegador también recibirás avisos del sistema.';
            } else {
                ayudaPermiso.textContent = 'Las notificaciones están desactivadas.';
            }
        }
    };

    const avisosActivados = () => localStorage.getItem(notificationPreferenceKey) === '1';

    const activarAvisosDesdeBoton = async () => {
        if (!('Notification' in window)) {
            actualizarEstadoPermiso();
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
    };

    const cambiarEstadoAvisos = async () => {
        if (activarDesactivarBtn) activarDesactivarBtn.disabled = true;
        if (avisosActivados()) {
            localStorage.setItem(notificationPreferenceKey, '0');
        } else {
            localStorage.setItem(notificationPreferenceKey, '1');
            await activarAvisosDesdeBoton();
            await cargarNotificaciones();
        }
        actualizarEstadoPermiso();
        if (activarDesactivarBtn) activarDesactivarBtn.disabled = false;
    };

    const mostrarAvisosNuevos = (items) => {
        const idsVistos = new Set(JSON.parse(localStorage.getItem(notificationSeenKey) || '[]'));
        const habilitadas = avisosActivados() && 'Notification' in window && Notification.permission === 'granted';
        const ahora = Date.now();

        items.filter((item) => Number(item.leida) === 0).forEach((item) => {
            const itemId = String(item.id);
            const fechaCreacion = Date.parse(String(item.fecha_creacion || '').replace(' ', 'T'));
            const recordatorioReciente = item.tipo === 'recordatorio_evento'
                && Number.isFinite(fechaCreacion)
                && ahora - fechaCreacion >= 0
                && ahora - fechaCreacion <= 120000;
            const debeMostrar = habilitadas
                && !idsVistos.has(itemId)
                && (!initialLoad || recordatorioReciente);

            if (debeMostrar) {
                const options = {
                    body: item.mensaje || 'Tenés una nueva notificación.',
                    icon: new URL('img/logoblanco.png', serviceWorkerUrl).href,
                    data: { url: window.location.href },
                };
                mostrarNotificacionSistema(item.titulo || 'SIGTUR', options);
            }
            idsVistos.add(itemId);
        });

        localStorage.setItem(notificationSeenKey, JSON.stringify([...idsVistos].slice(-100)));
    };

    const mostrarNotificacionSistema = (titulo, opciones) => {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready
                .then((registration) => registration.showNotification(titulo, opciones))
                .catch((error) => {
                    console.warn('No se pudo mostrar la notificación con el Service Worker', error);
                    new Notification(titulo, opciones);
                });
            return;
        }
        new Notification(titulo, opciones);
    };

    bell.addEventListener('click', async () => {
        if (!panel) return;
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
            const respuesta = await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                credentials: 'same-origin',
                body: new URLSearchParams({ accion: 'marcar_todas', csrf_token: csrfToken })
            });
            if (!respuesta.ok) {
                console.warn('No se pudieron marcar todas las notificaciones como leídas.');
                return;
            }
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
