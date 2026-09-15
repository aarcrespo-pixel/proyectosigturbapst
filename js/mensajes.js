/* State Pattern: el estado de la conversación vive en window para sobrevivir
    a callbacks asíncronos, polling y re-renderizados de componentes del widget. */
window.chatActivoId = window.chatActivoId || null;
window.chatIntervalId = window.chatIntervalId || null;

(() => {
    const widget = document.getElementById('mensajesWidget');
    if (!widget) return;
    const api = widget.dataset.api;
    const toggle = widget.querySelector('.mensajes-widget__toggle');
    const close = widget.querySelector('.mensajes-widget__close');
    const conversations = widget.querySelector('.mensajes-widget__conversaciones');
    const chat = widget.querySelector('.mensajes-widget__chat');
    const badge = widget.querySelector('.mensajes-widget__badge');
    const avatarFallback = widget.dataset.avatarFallback || '../img/user.png';
    const historyApi = api.replace(/mensajes\.php$/, 'obtener_mensajes.php');
    let activeName = '';
    let activeAvatar = '../img/user.png';

    function normalizarReceptorId(value) {
        const rawValue = value?.dataset?.usuarioId ?? value?.dataset?.id ?? value;
        const idValido = Number.parseInt(String(rawValue ?? ''), 10);
        return Number.isInteger(idValido) && idValido > 0 ? idValido : null;
    }

    function openWidget(userId = 0, name = '') {
        widget.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        if (userId) abrirChat(userId, name);
        loadConversations();
    }
    function getMediaUrl(path) {
        if (!path) return '';
        return /^(https?:|\/)/i.test(path) ? path : `${avatarFallback.replace(/img\/user\.png$/, '')}${path}`;
    }
    function renderBubble(item) {
        const media = `${item.foto_url ? `<img class="mensajes-widget__photo" src="${escapeHtml(getMediaUrl(item.foto_url))}" alt="Imagen adjunta" onerror="this.remove()">` : ''}${item.audio_url ? `<audio class="mensajes-widget__audio" controls src="${escapeHtml(getMediaUrl(item.audio_url))}"></audio>` : ''}`;
        const emisorId = item.remitente_id ?? item.emisor_id ?? item.emisor;
        const textoMensaje = item.mensaje ?? item.texto ?? '';
        return `<div class="mensajes-widget__message ${Number(emisorId) === Number(window.sigturUserId) ? 'mine' : ''}">${textoMensaje ? `<span>${escapeHtml(textoMensaje)}</span>` : ''}${media}</div>`;
    }
    function closeWidget() {
        widget.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    }
    /* El estado desplegable se mantiene en una sola función para que el botón
       del perfil, la cápsula y los cambios de conversación compartan el mismo
       foco y no creen modales paralelos. */
    async function request(action, options = {}) {
        const params = new URLSearchParams(options.body || {});
        if (action) params.set('accion', action);
        const query = options.method === 'POST' ? (options.query || '') : `?${params.toString()}${options.query ? `&${options.query.replace(/^\?/, '')}` : ''}`;
        const response = await fetch(`${api}${query}`, { method: options.method || 'GET', headers: options.method === 'POST' ? { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' } : undefined, body: options.method === 'POST' ? params : undefined });
        const texto = await response.text();
        let data;
        try {
            data = JSON.parse(texto);
        } catch (error) {
            throw new Error(`Respuesta no JSON desde ${api}: ${texto.slice(0, 160)}`);
        }
        if (!response.ok) throw new Error(data.error || 'No se pudo actualizar la mensajería.');
        return data;
    }
    function mostrarErrorChat(ruta, detalle) {
        const areaMensajes = chat.querySelector('.mensajes-widget__messages') || chat;
        areaMensajes.innerHTML = `<div class="mensajes-widget__error"><strong>Error de Endpoint</strong><br>Se intentó llamar a: <code>${escapeHtml(ruta)}</code><br>${escapeHtml(detalle)}</div>`;
    }
    /* Leer primero como texto permite distinguir JSON válido de HTML generado
       por Apache/PHP en un 404, 500 o fatal error y mostrar la ruta exacta. */
    async function cargarHistorialMensajes(receptorId, reconstruir = false) {
        const idValido = normalizarReceptorId(receptorId);
        if (!idValido || window.chatActivoId !== idValido) {
            console.error('ID de receptor no válido:', receptorId);
            return false;
        }
        const areaMensajes = chat.querySelector('.mensajes-widget__messages') || document.getElementById('area-mensajes') || document.querySelector('.chat-mensajes-container');
        if (!areaMensajes) return false;

        const rutas = [
            `${historyApi}?receptor_id=${encodeURIComponent(idValido)}`,
            `${api}?accion=conversacion&receptor_id=${encodeURIComponent(idValido)}`,
        ].filter((ruta, indice, lista) => lista.indexOf(ruta) === indice);
        let ultimoError = 'No se pudo obtener una respuesta del servidor.';

        for (const rutaApi of rutas) {
            try {
                const respuesta = await fetch(rutaApi, { headers: { Accept: 'application/json' } });
                const textoBruto = await respuesta.text();
                // Una respuesta vieja no puede pintar encima del contacto elegido después.
                if (window.chatActivoId !== idValido) return false;
                const pareceHtml = textoBruto.trim().startsWith('<') || /<!doctype|<html[\s>]/i.test(textoBruto);
                if (pareceHtml) {
                    console.error('Respuesta HTML no esperada desde el servidor:', rutaApi, textoBruto);
                    ultimoError = `El servidor devolvió HTML (${respuesta.status || 'error HTTP'}) en vez de JSON.`;
                    continue;
                }
                let datos;
                try {
                    datos = JSON.parse(textoBruto);
                } catch (error) {
                    ultimoError = `JSON inválido: ${error.message}`;
                    continue;
                }
                if (!respuesta.ok || datos.success !== true) {
                    ultimoError = datos.message || datos.error || `HTTP ${respuesta.status}`;
                    continue;
                }
                if (window.chatActivoId !== idValido) return false;
                    /* El backend puede evolucionar de "mensajes" a "data".
                       Este desacoplamiento defensivo permite que el cliente
                       siga renderizando aunque cambie el nombre del payload. */
                    const mensajes = Array.isArray(datos.data)
                        ? datos.data
                        : (Array.isArray(datos.mensajes) ? datos.mensajes : (datos.messages || []));
                    const scrollEstabaAbajo = areaMensajes.scrollHeight - areaMensajes.scrollTop - areaMensajes.clientHeight < 80;
                    areaMensajes.innerHTML = mensajes.length
                    ? mensajes.map(renderBubble).join('')
                    : '<p class="mensajes-widget__empty">Iniciá la conversación.</p>';
                    if (reconstruir || scrollEstabaAbajo) areaMensajes.scrollTop = areaMensajes.scrollHeight;
                return true;
            } catch (error) {
                ultimoError = `Error de red o parseo: ${error.message}`;
            }
        }
          /* Fault tolerance: durante polling se conserva la última conversación
              válida; solo la carga manual inicial muestra el diagnóstico. */
          if (reconstruir) mostrarErrorChat(rutas[0], ultimoError);
        return false;
    }
    const cargarMensajes = cargarHistorialMensajes;
    // Alias legacy para páginas que todavía invocan cargarSoloMensajes().
    window.cargarSoloMensajes = cargarHistorialMensajes;
    function getAvatarUrl(contacto) {
        const foto = contacto?.foto_perfil?.trim();
        if (!foto) return avatarFallback;
        if (/^(https?:|\/|\.\.\/)/i.test(foto)) return foto;
        const projectRoot = avatarFallback.replace(/img\/user\.png$/, '');
        return `${projectRoot}uploads/avatars/${encodeURIComponent(foto)}`;
    }
    async function loadConversations() {
        try {
            const data = await request('conversaciones');
            const items = data.conversations || [];
            const unread = items.reduce((total, item) => total + Number(item.no_leidos || 0), 0);
            badge.textContent = unread;
            badge.hidden = unread === 0;
            conversations.innerHTML = items.length ? items.map(item => { const avatarUrl = getAvatarUrl(item); const nombre = item.nickname || item.nombre_completo || 'Usuario'; return `<button type="button" class="mensajes-widget__conversation item-contacto" data-id="${item.usuario_id}" data-nombre="${escapeHtml(nombre)}" data-user-id="${item.usuario_id}" data-user-name="${escapeHtml(nombre)}" data-user-avatar="${escapeHtml(avatarUrl)}"><img src="${escapeHtml(avatarUrl)}" onerror="this.onerror=null;this.src='${escapeHtml(avatarFallback)}'" alt="Avatar" class="chat-user-avatar"><span><strong>${escapeHtml(nombre)}</strong><small>${item.no_leidos ? `${item.no_leidos} sin leer` : 'Conversación activa'}</small></span></button>`; }).join('') : '<p class="mensajes-widget__empty">Todavía no tenés conversaciones.</p>';
        } catch (error) { conversations.innerHTML = `<p class="mensajes-widget__empty">${escapeHtml(error.message)}</p>`; }
    }
    async function abrirChat(userId, name, avatar = '../img/user.png') {
        const receptorId = normalizarReceptorId(userId);
        if (!receptorId) {
            console.error('ID de receptor no válido.');
            return;
        }
        if (window.chatActivoId === receptorId && chat.querySelector('.mensajes-widget__composer')) {
            widget.classList.add('is-open');
            chat.querySelector('textarea, input[type="text"]')?.focus();
            return;
        }
        window.chatActivoId = receptorId;
        activeName = name || 'Usuario';
        activeAvatar = avatar || avatarFallback;
        /* Solo seleccionar otro contacto reemplaza la estructura principal.
           El polling nunca entra aquí: actualiza únicamente las burbujas. */
        chat.innerHTML = `<div class="mensajes-widget__chat-header chat-header"><img src="${escapeHtml(activeAvatar)}" onerror="this.onerror=null;this.src='${escapeHtml(avatarFallback)}'" alt="Avatar" class="chat-user-avatar"><strong>${escapeHtml(activeName)}</strong></div><div class="mensajes-widget__messages chat-mensajes-list"><p class="mensajes-widget__empty">Cargando...</p></div>`;
        addComposer();
        try {
            const cargado = await cargarHistorialMensajes(window.chatActivoId, true);
            if (cargado) await request('leer', { method: 'POST', body: { usuario_id: window.chatActivoId } });
            loadConversations();
            if (window.chatIntervalId) clearInterval(window.chatIntervalId);
            window.chatIntervalId = setInterval(() => {
                if (window.chatActivoId === receptorId) cargarHistorialMensajes(receptorId, false);
            }, 3000);
        } catch (error) {
            const areaMensajes = chat.querySelector('.mensajes-widget__messages');
            if (areaMensajes) areaMensajes.innerHTML = `<p class="mensajes-widget__error">${escapeHtml(error.message)}</p>`;
        }
    }
    function addComposer() {
        if (chat.querySelector('.mensajes-widget__composer')) return;
        const form = document.createElement('form');
        form.className = 'mensajes-widget__composer';
        form.enctype = 'multipart/form-data';
        form.innerHTML = '<label class="btn-chat-icon" title="Adjuntar imagen">📷<input type="file" name="foto" accept="image/*" hidden></label><button type="button" class="btn-chat-icon" data-audio-button title="Grabar audio">🎙️</button><textarea name="mensaje" rows="1" maxlength="500" placeholder="Escribí un mensaje..."></textarea><button type="submit">Enviar</button><div class="mensajes-widget__audio-status" aria-live="polite"></div><div class="mensajes-widget__preview"></div>';
        chat.appendChild(form);
        const input = form.querySelector('textarea');
        const foto = form.querySelector('input[type="file"]');
        const preview = form.querySelector('.mensajes-widget__preview');
        foto.addEventListener('change', () => { const archivo = foto.files?.[0]; preview.textContent = archivo ? archivo.name : ''; });
        let grabador = null;
        let partesAudio = [];
        form.querySelector('[data-audio-button]').addEventListener('click', async () => {
            const estado = form.querySelector('.mensajes-widget__audio-status');
            if (grabador?.state === 'recording') { grabador.stop(); return; }
            if (!navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) { estado.textContent = 'Audio no disponible en este navegador.'; return; }
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            grabador = new MediaRecorder(stream); partesAudio = [];
            grabador.ondataavailable = event => { if (event.data.size) partesAudio.push(event.data); };
            grabador.onstop = () => { stream.getTracks().forEach(track => track.stop()); form.audioBlob = new Blob(partesAudio, { type: 'audio/webm' }); estado.textContent = 'Audio listo para enviar.'; };
            grabador.start(); estado.textContent = 'Grabando... pulsa para detener.';
        });
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (!window.chatActivoId) return;
            const datos = new FormData(form); datos.set('accion', 'enviar'); datos.set('usuario_id', String(window.chatActivoId));
            if (form.audioBlob) datos.append('audio', form.audioBlob, 'nota.webm');
            if (!input.value.trim() && !foto.files?.length && !form.audioBlob) return;
            try { const response = await fetch(api, { method: 'POST', body: datos }); const data = await response.json(); if (!response.ok || data.success !== true) throw new Error(data.error || data.message || 'No se pudo enviar el mensaje.'); input.value = ''; form.reset(); form.audioBlob = null; preview.textContent = ''; await cargarHistorialMensajes(window.chatActivoId, false); }
            catch (error) { window.sigturAlert?.(error.message); }
        });
        input.focus();
    }
    function escapeHtml(value) { return String(value || '').replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;'); }
    toggle.addEventListener('click', () => openWidget());
    close.addEventListener('click', closeWidget);
    conversations.addEventListener('click', event => {
        const button = event.target.closest('.item-contacto, [data-user-id], [data-usuario-id], [data-id]');
        if (!button) return;
        const receptorId = normalizarReceptorId(button);
        if (!receptorId) {
            console.error('Contacto sin ID válido:', button.dataset);
            return;
        }
        abrirChat(receptorId, button.dataset.userName || button.dataset.nombre || 'Usuario', button.dataset.userAvatar);
    });
    window.addEventListener('sigtur:abrir-mensajes', event => {
        const receptorId = normalizarReceptorId(event.detail?.userId ?? event.detail?.receptorId);
        if (receptorId) openWidget(receptorId, event.detail?.name);
        else console.error('Evento de mensajería sin receptor válido:', event.detail);
    });
    window.cargarMensajes = cargarHistorialMensajes;
    window.abrirChat = abrirChat;
    window.sigturUserId = Number(widget.dataset.userId || 0);
    widget.hidden = false;
    window.setInterval(() => { if (!widget.classList.contains('is-open')) return; loadConversations(); }, 15000);
    window.addEventListener('beforeunload', () => { if (window.chatIntervalId) window.clearInterval(window.chatIntervalId); });
})();
