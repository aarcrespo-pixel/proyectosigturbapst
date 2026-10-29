(function () {
    const nuevaClaveEnvio = () => {
        const bytes = new Uint8Array(16);
        window.crypto.getRandomValues(bytes);
        return Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');
    };

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('faq-form');
        const submit = document.getElementById('faq-submit');
        const label = submit?.querySelector('.faq-submit-label');
        const feedback = document.getElementById('faq-feedback');
        const toast = document.getElementById('faq-toast');
        if (!form || !submit) return;
        let sending = false;

        /* Interceptamos submit para mantener al usuario en la página: FormData
           conserva textarea/email y fetch envía el POST sin recargar la vista. */
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (sending) return;
            if (!form.reportValidity()) return;

            sending = true;
            submit.disabled = true;
            submit.classList.add('is-loading');
            submit.setAttribute('aria-busy', 'true');
            if (label) label.textContent = 'Enviando...';
            if (feedback) {
                feedback.textContent = '';
                feedback.className = 'faq-feedback';
            }

            let timeout;
            try {
                const controller = new AbortController();
                timeout = window.setTimeout(() => controller.abort(), 15000);
                /* La respuesta JSON permite diferenciar validación del servidor,
                   error HTTP y confirmación sin depender de alertas nativas. */
                const response = await fetch('api/enviar_faq.php', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json' },
                    signal: controller.signal
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'No se pudo enviar la pregunta.');

                form.reset();
                form.elements.namedItem('submission_key').value = nuevaClaveEnvio();
                if (toast) {
                    toast.textContent = '¡Tu pregunta ha sido enviada! Un administrador la revisará pronto.';
                    toast.hidden = false;
                    window.setTimeout(() => { toast.hidden = true; }, 5000);
                }
            } catch (error) {
                if (feedback) {
                    feedback.textContent = error.name === 'AbortError'
                        ? 'El envío tardó demasiado. Comprueba tu conexión e inténtalo nuevamente.'
                        : error.message || 'No se pudo enviar la pregunta.';
                    feedback.className = 'faq-feedback is-error';
                }
            } finally {
                if (timeout) window.clearTimeout(timeout);
                sending = false;
                submit.disabled = false;
                submit.classList.remove('is-loading');
                submit.removeAttribute('aria-busy');
                if (label) label.textContent = 'Enviar pregunta';
            }
        });
    });
}());