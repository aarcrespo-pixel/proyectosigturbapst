document.addEventListener('DOMContentLoaded', () => {
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

    syncVisibility(false);
    toggle.addEventListener('click', () => {
        syncVisibility(toggle.getAttribute('aria-expanded') !== 'true');
    });
});
