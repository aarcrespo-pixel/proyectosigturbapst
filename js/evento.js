(() => {
    const form = document.getElementById('form-comentario-evento');
    if (!form) return;

    const feedback = document.getElementById('event-question-feedback');
    const mostrarToast = (mensaje, tipo) => {
        let toast = document.getElementById('event-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'event-toast';
            toast.className = 'event-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = mensaje;
        toast.className = `event-toast is-visible ${tipo}`;
        window.setTimeout(() => toast.classList.remove('is-visible'), 3600);
    };
    const mostrarToastExito = (mensaje) => mostrarToast(mensaje, 'is-success');
    const mostrarToastError = (mensaje) => mostrarToast(mensaje, 'is-error');

    window.cargarComentarios = () => {
          /* Dejamos un punto de extensión para refrescar el listado sin recargar:
              las preguntas pendientes no se muestran como respondidas hasta que el
              organizador las contesta, por eso el feedback permanece en el DOM. */
          form.dispatchEvent(new CustomEvent('evento:comentario-enviado'));
    };

    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const btnEnviar = this.querySelector('button[type="submit"]');
        if (!btnEnviar || btnEnviar.disabled) return;
        const textoOriginal = btnEnviar.innerHTML;

        /* e.preventDefault() evita la navegación automática y mantiene la vista
           estable mientras el POST viaja al servidor mediante fetch. */
        btnEnviar.disabled = true;
        btnEnviar.setAttribute('aria-busy', 'true');
        btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...';
        if (feedback) feedback.textContent = '';

        /* FormData conserva evento_id y comentario con sus nombres del formulario,
           permitiendo enviar el payload multipart sin serialización manual. */
        const formData = new FormData(this);

        try {
            const respuesta = await fetch(this.dataset.endpoint, {
                method: 'POST',
                body: formData,
                headers: { Accept: 'application/json' }
            });
            const datos = await respuesta.json();
            if (!respuesta.ok || !datos.success) {
                throw new Error(datos.message || 'Error al enviar.');
            }

            this.reset();
            mostrarToastExito(datos.message || '¡Pregunta/Comentario enviado con éxito!');
            if (typeof window.cargarComentarios === 'function') window.cargarComentarios();
        } catch (error) {
            console.error('Error en la petición:', error);
            mostrarToastError(error.message || 'No se pudo enviar.');
            if (feedback) feedback.textContent = error.message || 'No se pudo enviar.';
        } finally {
            /* El estado asíncrono final devuelve el control al usuario incluso si
               el servidor responde con error o la red interrumpe la petición. */
            btnEnviar.disabled = false;
            btnEnviar.removeAttribute('aria-busy');
            btnEnviar.innerHTML = textoOriginal;
        }
    });
})();
