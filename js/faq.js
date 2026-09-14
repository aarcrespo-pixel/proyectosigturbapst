(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('faq-form');
        const submit = document.getElementById('faq-submit');
        const label = submit?.querySelector('.faq-submit-label');
        const spinner = submit?.querySelector('.faq-spinner');
        const feedback = document.getElementById('faq-feedback');
        const toast = document.getElementById('faq-toast');
        if (!form || !submit) return;

        /* Interceptamos submit para mantener al usuario en la página: FormData
           conserva textarea/email y fetch envía el POST sin recargar la vista. */
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;

            submit.disabled = true;
            submit.classList.add('is-loading');
            submit.setAttribute('aria-busy', 'true');
            label.textContent = 'Enviando...';
            spinner.hidden = false;
            feedback.textContent = '';

            try {
                /* La respuesta JSON permite diferenciar validación del servidor,
                   error HTTP y confirmación sin depender de alertas nativas. */
                const response = await fetch('api/enviar_faq.php', {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json' }
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'No se pudo enviar la pregunta.');

                form.reset();
                feedback.textContent = '';
                toast.textContent = '¡Tu pregunta ha sido enviada! Un administrador la revisará pronto.';
                toast.hidden = false;
                window.setTimeout(() => { toast.hidden = true; }, 5000);
            } catch (error) {
                feedback.textContent = error.message;
                feedback.className = 'faq-feedback is-error';
            } finally {
                submit.disabled = false;
                submit.classList.remove('is-loading');
                submit.removeAttribute('aria-busy');
                label.textContent = 'Enviar pregunta';
                spinner.hidden = true;
            }
        });
    });
}());