document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-support-chat-toggle]').forEach((button) => {
    const chat = document.getElementById(button.getAttribute('aria-controls'));
    if (!chat) return;

    button.addEventListener('click', () => {
      const abrir = chat.hidden;
      chat.hidden = !abrir;
      button.setAttribute('aria-expanded', String(abrir));
      button.textContent = abrir ? 'Ocultar chat' : 'Ver chat';
    });
  });
});
