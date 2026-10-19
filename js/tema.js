(() => {
    document.body.dataset.tema = localStorage.getItem('modo-oscuro') === '1' ? 'oscuro' : 'claro';
})();
