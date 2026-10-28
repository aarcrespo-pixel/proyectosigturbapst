(() => {
    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function mostrarTexto(element, value) {
        element.replaceChildren(document.createTextNode(value));
        element.style.whiteSpace = 'pre-line';
    }

    function mostrarError(mensaje) {
        if (typeof window.sigturAlert === 'function') window.sigturAlert(mensaje);
        else window.alert(mensaje);
    }

    async function enviarAccion(item, accion, texto = '') {
        const esDirecto = item.dataset.messageType === 'direct';
        const endpoint = item.dataset.messageApi || (esDirecto ? 'api/mensajes.php' : 'api/gestionar_mensaje.php');
        const csrf = document.querySelector('input[name="csrf_token"]')?.value
            || document.querySelector('[data-csrf]')?.dataset.csrf
            || '';
        const parametros = new URLSearchParams({
            accion,
            id: item.dataset.messageId,
            texto,
            csrf_token: csrf,
        });
        if (!esDirecto) parametros.set('tipo', item.dataset.messageType || '');

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
            body: parametros,
        });
        const data = await response.json();
        if (!response.ok || data.success !== true) {
            throw new Error(data.error || data.message || 'No se pudo actualizar el mensaje.');
        }
        return data;
    }

    document.addEventListener('click', async (event) => {
        const boton = event.target.closest('[data-message-action]');
        if (!boton) return;
        const item = boton.closest('[data-message-item]');
        if (!item) return;

        if (boton.dataset.messageAction === 'cancel') {
            const text = item.querySelector('[data-message-text]');
            if (text) {
                mostrarTexto(text, text.dataset.originalText || '');
                text.hidden = false;
            }
            item.querySelector('.message-edit-form')?.remove();
            return;
        }
        if (boton.dataset.messageAction === 'edit') {
            if (item.dataset.canEdit !== '1') return;
            const text = item.querySelector('[data-message-text]');
            if (!text || item.querySelector('.message-edit-form')) return;
            const originalText = text.innerText.trim();
            text.dataset.originalText = originalText;
            const form = document.createElement('form');
            form.className = 'message-edit-form';
            const maxLength = item.dataset.messageType === 'direct' ? 500 : item.dataset.messageType === 'evento' ? 2000 : 4000;
            form.innerHTML = `<textarea maxlength="${maxLength}" required>${escapeHtml(originalText)}</textarea><div><button type="submit">Guardar</button><button type="button" data-message-action="cancel">Cancelar</button></div>`;
            text.hidden = true;
            const editContainer = item.querySelector('[data-message-edit-container]');
            if (editContainer) editContainer.append(form);
            else text.after(form);
            item.querySelector('textarea')?.focus();
            boton.closest('details')?.removeAttribute('open');
            return;
        }
        const accion = boton.dataset.messageAction;
        if (accion === 'delete-me' || accion === 'delete-everyone') {
            const paraTodos = accion === 'delete-everyone';
            if (paraTodos && item.dataset.canDelete !== '1') return;
            if (!window.confirm(paraTodos ? '¿Eliminar este mensaje para todos los participantes?' : '¿Eliminar este mensaje solo de tu chat?')) return;
            boton.disabled = true;
            try {
                const data = await enviarAccion(item, paraTodos ? 'eliminar_para_todos' : 'eliminar_para_mi');
                if (!paraTodos) {
                    if (item.dataset.messageHideLabel) {
                        const text = item.querySelector('[data-message-text]');
                        if (text) mostrarTexto(text, item.dataset.messageHideLabel);
                        item.querySelector('.message-actions')?.remove();
                        item.dataset.canEdit = '0';
                        item.dataset.canDelete = '0';
                    } else {
                        item.remove();
                    }
                } else {
                    const text = item.querySelector('[data-message-text]');
                    if (text) {
                        mostrarTexto(text, 'Mensaje eliminado para todos');
                        text.hidden = false;
                    }
                    item.querySelector('.message-edit-form')?.remove();
                    item.dataset.canEdit = '0';
                    item.querySelectorAll('img, video, audio').forEach(media => media.remove());
                    const menu = item.querySelector('.message-actions__menu');
                    if (menu) menu.innerHTML = '<button type="button" data-message-action="delete-me">Eliminar para mí</button>';
                }
            } catch (error) {
                boton.disabled = false;
                mostrarError(error.message);
            }
            return;
        }
        if (accion !== 'delete' || item.dataset.canDelete !== '1') return;
        if (!window.confirm('¿Eliminar este mensaje?')) return;
        boton.disabled = true;
        try {
            const data = await enviarAccion(item, 'eliminar');
            const text = item.querySelector('[data-message-text]');
            if (item.dataset.messageType === 'direct') {
                item.remove();
            } else {
                if (text) {
                    text.hidden = false;
                    mostrarTexto(text, data.texto || 'Mensaje eliminado');
                }
                item.querySelector('.message-edit-form')?.remove();
                item.querySelector('.message-actions')?.remove();
                item.dataset.canEdit = '0';
                item.dataset.canDelete = '0';
            }
        } catch (error) {
            boton.disabled = false;
            mostrarError(error.message);
        }
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('.message-edit-form');
        if (!form) return;
        event.preventDefault();
        const item = form.closest('[data-message-item]');
        const text = item?.querySelector('[data-message-text]');
        const input = form.querySelector('textarea');
        const nuevoTexto = input?.value.trim() || '';
        if (!item || !text || !nuevoTexto) return;
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        try {
            const data = await enviarAccion(item, 'editar', nuevoTexto);
            mostrarTexto(text, data.text || data.mensaje || nuevoTexto);
            text.hidden = false;
            form.remove();
        } catch (error) {
            submit.disabled = false;
            mostrarError(error.message);
        }
    });
})();
