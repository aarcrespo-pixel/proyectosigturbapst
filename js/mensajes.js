/* State Pattern: el estado de la conversación vive en window para sobrevivir
    a callbacks asíncronos, polling y re-renderizados de componentes del widget. */
window.chatActivoId = window.chatActivoId || null;
window.chatIntervalId = window.chatIntervalId || null;

(() => {
    const widget = document.getElementById('mensajesWidget');
    /* CAMBIO APLICADO: el formulario de Soporte General también usa este
       módulo aunque la página no monte el widget flotante de chats. */
    const soporteForm = document.querySelector('[data-soporte-mensajes]');
    if (soporteForm) {
        soporteForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const texto = soporteForm.querySelector('[name="mensaje"]')?.value.trim() || '';
            const contexto = soporteForm.querySelector('[name="contexto"]')?.value.trim() || 'Soporte general';
            const csrfToken = soporteForm.querySelector('[name="csrf_token"]')?.value || '';
            const estado = soporteForm.querySelector('[data-soporte-estado]');
            const boton = soporteForm.querySelector('button[type="submit"]');
            if (!texto) {
                if (estado) estado.textContent = 'Escribí el problema antes de enviarlo.';
                return;
            }
            if (boton) boton.disabled = true;
            if (estado) estado.textContent = 'Enviando reporte...';
            try {
                const respuesta = await fetch(soporteForm.action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
                    body: new URLSearchParams({ accion: 'consulta', mensaje: texto, contexto, csrf_token: csrfToken })
                });
                const datos = await respuesta.json().catch(() => ({}));
                if (!respuesta.ok || datos.success !== true) throw new Error(datos.message || datos.error || 'No se pudo enviar la consulta.');
                soporteForm.reset();
                if (estado) estado.textContent = datos.message || 'Consulta enviada.';
            } catch (error) {
                if (estado) estado.textContent = error.message;
            } finally {
                if (boton) boton.disabled = false;
            }
        });
    }
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
    let activeContext = 'Consulta de soporte';

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
    // CAMBIO APLICADO: priorizamos la hora SQL y solo usamos el parseo local
    // como respaldo para mensajes optimistas o respuestas antiguas.
    function obtenerHora(item) {
        if (item.hora) return String(item.hora).slice(0, 5);
        const fecha = item.fecha || item.created_at || item.fecha_creacion;
        if (!fecha) return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
        const parsed = new Date(String(fecha).replace(' ', 'T'));
        return Number.isNaN(parsed.getTime()) ? String(fecha).slice(0, 5) : parsed.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
    }
    function renderBubble(item) {
        const media = `${item.foto_url ? `<img class="mensajes-widget__photo" src="${escapeHtml(getMediaUrl(item.foto_url))}" alt="Imagen adjunta" onerror="this.remove()">` : ''}${item.audio_url ? `<audio class="mensajes-widget__audio" controls src="${escapeHtml(getMediaUrl(item.audio_url))}"></audio>` : ''}`;
        const emisorId = item.remitente_id ?? item.emisor_id ?? item.emisor;
        const textoMensaje = item.mensaje ?? item.texto ?? '';
        return `<div class="mensajes-widget__message ${Number(emisorId) === Number(window.sigturUserId) ? 'mine' : ''}">${textoMensaje ? `<span>${escapeHtml(textoMensaje)}</span>` : ''}${media}<time class="mensajes-widget__time" datetime="${escapeHtml(item.fecha || item.created_at || '')}">${escapeHtml(obtenerHora(item))} hs</time></div>`;
    }

    function obtenerContexto(texto) {
        const coincidencia = String(texto || '').match(/^\[Consulta ([^\]]+)\]:/i);
        return coincidencia ? coincidencia[1] : 'Consulta de soporte';
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
                    // FIX REFRESCO / SINCRONIZACIÓN: una respuesta vacía no
                    // puede borrar un historial que ya se ve en pantalla.
                    if (!mensajes.length && areaMensajes.querySelector('.mensajes-widget__message')) {
                        console.warn('[mensajeria] El servidor devolvió cero mensajes; se conserva el historial visible.', idValido);
                        return true;
                    }
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
            conversations.innerHTML = items.length ? items.map(item => { const avatarUrl = getAvatarUrl(item); const nombre = item.nickname || item.nombre_completo || 'Usuario'; const ultimo = item.ultimo_mensaje || 'Sin mensajes'; const estado = item.no_leidos ? `${item.no_leidos} sin leer` : 'Respondido'; const contexto = obtenerContexto(ultimo); return `<button type="button" class="mensajes-widget__conversation item-contacto${item.no_leidos ? ' has-unread' : ''}" data-id="${item.usuario_id}" data-nombre="${escapeHtml(nombre)}" data-contexto="${escapeHtml(contexto)}" data-user-id="${item.usuario_id}" data-user-name="${escapeHtml(nombre)}" data-user-avatar="${escapeHtml(avatarUrl)}"><img src="${escapeHtml(avatarUrl)}" onerror="this.onerror=null;this.src='${escapeHtml(avatarFallback)}'" alt="Avatar" class="chat-user-avatar"><span><strong>${escapeHtml(nombre)}</strong><small>${escapeHtml(ultimo)}</small><em>${escapeHtml(contexto)} · ${estado}</em></span></button>`; }).join('') : '<p class="mensajes-widget__empty">Todavía no tenés conversaciones.</p>';
        } catch (error) { conversations.innerHTML = `<p class="mensajes-widget__empty">${escapeHtml(error.message)}</p>`; }
    }
    async function abrirChat(userId, name, avatar = '../img/user.png', contexto = 'Consulta de soporte') {
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
        activeContext = contexto || 'Consulta de soporte';
        /* Solo seleccionar otro contacto reemplaza la estructura principal.
           El polling nunca entra aquí: actualiza únicamente las burbujas. */
        // CAMBIO APLICADO: al seleccionar un contacto se inyecta la estructura
        // completa del panel derecho, incluido el contexto visible y el área
        // de respuesta que queda fija al pie mediante CSS.
        chat.innerHTML = `<div class="mensajes-widget__chat-header chat-header"><img src="${escapeHtml(activeAvatar)}" onerror="this.onerror=null;this.src='${escapeHtml(avatarFallback)}'" alt="Avatar" class="chat-user-avatar"><span><strong>${escapeHtml(activeName)}</strong><small class="mensajes-widget__contexto">🛠️ ${escapeHtml(activeContext)}</small></span></div><div class="mensajes-widget__messages chat-mensajes-list"><p class="mensajes-widget__empty">Cargando...</p></div>`;
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
        // CAMBIO APLICADO: el formulario se inyecta con un estado visual propio
        // para informar éxito/error sin borrar el texto antes de persistirlo.
        form.innerHTML = '<label class="btn-chat-icon" title="Adjuntar imagen">📷<input type="file" name="foto" accept="image/*" hidden></label><button type="button" class="btn-chat-icon" data-audio-button title="Grabar audio">🎙️</button><textarea id="mensaje-texto" name="mensaje" rows="1" maxlength="500" placeholder="Escribí un mensaje..."></textarea><button id="btn-enviar-mensaje" type="submit">Enviar</button><div class="mensajes-widget__audio-status" aria-live="polite"></div><div class="mensajes-widget__preview"></div><div class="mensajes-widget__send-status" data-send-status role="status" aria-live="polite"></div>';
        chat.appendChild(form);
        const input = form.querySelector('textarea');
        const foto = form.querySelector('input[type="file"]');
        const preview = form.querySelector('.mensajes-widget__preview');
        const sendStatus = form.querySelector('[data-send-status]');
        const sendButton = form.querySelector('#btn-enviar-mensaje');
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
            const datos = new FormData(form); datos.set('accion', 'enviar'); datos.set('usuario_id', String(window.chatActivoId)); datos.set('destinatario_id', String(window.chatActivoId)); datos.set('texto', input.value.trim()); datos.set('contexto', activeContext || 'Consulta general');
            if (form.audioBlob) datos.append('audio', form.audioBlob, 'nota.webm');
            if (!input.value.trim() && !foto.files?.length && !form.audioBlob) return;
            // DIAGNÓSTICO: mostramos exactamente las claves y valores enviados;
            // no se imprime el contenido binario de fotos/audio en consola.
            console.log('[mensajeria] POST antes de fetch', Object.fromEntries([...datos.entries()].filter(([clave]) => !['foto', 'audio'].includes(clave))));
            if (sendButton) sendButton.disabled = true;
            if (sendStatus) sendStatus.textContent = 'Enviando...';
            try {
                const response = await fetch(api, { method: 'POST', body: datos, headers: { Accept: 'application/json' } });
                const data = await response.json();
                // CAMBIO APLICADO: solo status=success confirma persistencia;
                // sin esa confirmación no se limpia ni se recarga el historial.
                // FIX RESPUESTA: alcanza con que una de las dos llaves confirme
                // éxito, así se aceptan contratos PHP antiguos y actuales.
                const envioCorrecto = data.status === 'success' || data.success === true;
                if (!response.ok || !envioCorrecto) {
                    // FIX RESPUESTA: el JSON completo queda visible en DevTools
                    // y el input no se limpia cuando el servidor rechaza el POST.
                    console.log('[mensajeria] Respuesta de error', data);
                    throw new Error(data.message || data.error || 'No se pudo guardar el mensaje.');
                }
                // CAMBIO APLICADO: la respuesta aparece de inmediato en el
                // historial; luego el servidor vuelve a ser la fuente final.
                const areaMensajes = chat.querySelector('.mensajes-widget__messages');
                if (areaMensajes) {
                    areaMensajes.insertAdjacentHTML('beforeend', renderBubble({ remitente_id: window.sigturUserId, mensaje: input.value.trim(), hora: data.fecha || obtenerHora({}) }));
                    areaMensajes.scrollTop = areaMensajes.scrollHeight;
                }
                input.value = ''; form.reset(); form.audioBlob = null; preview.textContent = '';
                if (sendStatus) sendStatus.textContent = 'Mensaje enviado.';
                // FIX REFRESCO / SINCRONIZACIÓN: se refresca con exactamente
                // el mismo ID que se envió; nunca se deriva de otro contacto.
                const receptorEnviado = Number(window.chatActivoId);
                await cargarHistorialMensajes(receptorEnviado, false);
                loadConversations();
            }
            catch (error) {
                // CAMBIO APLICADO: ante un fallo se conserva el texto escrito y
                // se evita cargarMensajes(), que era lo que borraba la burbuja.
                if (sendStatus) sendStatus.textContent = `Error: ${error.message}`;
                console.error('[mensajeria] Fallo de envío', error);
                window.sigturAlert?.(error.message);
            } finally {
                if (sendButton) sendButton.disabled = false;
            }
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
        abrirChat(receptorId, button.dataset.userName || button.dataset.nombre || 'Usuario', button.dataset.userAvatar, button.dataset.contexto);
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
    if (document.body.classList.contains('pagina-mensajes-admin')) openWidget();
    window.setInterval(() => { if (!widget.classList.contains('is-open')) return; loadConversations(); }, 15000);
    window.addEventListener('beforeunload', () => { if (window.chatIntervalId) window.clearInterval(window.chatIntervalId); });
})();
