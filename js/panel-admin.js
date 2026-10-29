document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.admin-event__image-input').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const preview = input.closest('.admin-event__image-field')?.querySelector('.admin-event__image-preview');
            if (!file || !preview) return;

            if (preview.dataset.previewUrl) {
                URL.revokeObjectURL(preview.dataset.previewUrl);
            }
            const previewUrl = URL.createObjectURL(file);
            preview.dataset.previewUrl = previewUrl;
            preview.src = previewUrl;
        });
    });

    document.querySelectorAll('[data-event-editor-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const editor = document.getElementById(button.getAttribute('aria-controls'));
            if (!editor) return;
            const abrir = editor.hidden;
            editor.hidden = !abrir;
            button.setAttribute('aria-expanded', String(abrir));
        });
    });

    const toggle = document.querySelector('[data-admin-events-toggle]');
    const events = [...document.querySelectorAll('.admin-event-list > .admin-event')];
    const groupTitles = [...document.querySelectorAll('.admin-event-group-title')];

    if (!toggle || events.length <= 4) return;

    const syncVisibility = (expanded) => {
        events.forEach((event, index) => {
            event.hidden = !expanded && index >= 4;
        });

        groupTitles.forEach((title) => {
            const firstEventInGroup = title.nextElementSibling;
            title.hidden = Boolean(firstEventInGroup?.hidden);
        });

        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.textContent = expanded ? 'Ver menos' : 'Ver más eventos';
    };

    const updatedEventId = document.querySelector('#admin-event-list')?.dataset.openEventId;
    syncVisibility(Boolean(updatedEventId && events.some((event) => event.id === `evento-${updatedEventId}`)));
    toggle.addEventListener('click', () => {
        syncVisibility(toggle.getAttribute('aria-expanded') !== 'true');
    });
});
