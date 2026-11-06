(() => {
    let manualActivo = null;
    let dialogActivo = null;
    let elementoDisparador = null;

    const cerrarManual = (devolverFoco = true) => {
        if (!manualActivo) return;
        manualActivo.hidden = true;
        document.body.classList.remove('manual-usuario-abierto');
        if (devolverFoco) elementoDisparador?.focus();
        manualActivo = null;
        dialogActivo = null;
        elementoDisparador = null;
    };

    document.addEventListener('click', (evento) => {
        if (!(evento.target instanceof Element)) return;

        const botonAbrir = evento.target.closest('[data-open-manual]');
        if (botonAbrir) {
            const manualId = botonAbrir.dataset.openManual || 'manual-usuario';
            const manual = document.getElementById(manualId);
            const dialog = manual?.querySelector('.manual-usuario__dialog');
            if (!manual || !dialog) return;

            evento.preventDefault();
            cerrarManual(false);
            manualActivo = manual;
            dialogActivo = dialog;
            elementoDisparador = botonAbrir;
            manual.hidden = false;
            document.body.classList.add('manual-usuario-abierto');
            dialog.focus();
            return;
        }

        if (manualActivo && manualActivo.contains(evento.target)
            && evento.target.closest('[data-manual-close], .manual-usuario__backdrop')) {
            cerrarManual();
        }
    });

    document.addEventListener('keydown', (evento) => {
        if (!manualActivo || !dialogActivo) return;

        if (evento.key === 'Escape') {
            evento.preventDefault();
            cerrarManual();
            return;
        }

        if (evento.key !== 'Tab') return;
        const elementosEnfocables = [...dialogActivo.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')];
        if (!elementosEnfocables.length) {
            evento.preventDefault();
            dialogActivo.focus();
            return;
        }

        const primero = elementosEnfocables[0];
        const ultimo = elementosEnfocables[elementosEnfocables.length - 1];
        if (evento.shiftKey && (document.activeElement === primero || document.activeElement === dialogActivo)) {
            evento.preventDefault();
            ultimo.focus();
        } else if (!evento.shiftKey && document.activeElement === ultimo) {
            evento.preventDefault();
            primero.focus();
        }
    });
})();
