/**
 * ARCHIVO: login.js - Gestiona registro y validación de datos de usuario
 */

const setMessage = (message, type = 'error') => {
    const container = document.getElementById('formMessage');
    if (!container) return;

    container.textContent = message;
    container.className = `form-message ${type}`;
    container.style.display = 'block';
};

const togglePasswordVisibility = (button) => {
    const targetId = button.dataset.target;
    const input = document.getElementById(targetId);
    if (!input) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
    const icon = button.querySelector('.toggle-icon');
    if (icon) {
        icon.textContent = isPassword ? '🙈' : '👁';
    }
};

document.querySelectorAll('.toggle-password').forEach((button) => {
    button.addEventListener('click', () => togglePasswordVisibility(button));
});

const loginForm = document.getElementById('loginForm');
loginForm?.addEventListener('submit', function (event) {
    event.preventDefault();

    const cedula = document.getElementById('cedula')?.value.trim();
    if (cedula) {
        localStorage.setItem('sigtur-usuario', cedula);
    }

    this.submit();
});

const registerForm = document.getElementById('registroForm');
const submitButton = document.getElementById('btnAgregar');

const setLoadingState = (isLoading) => {
    if (!submitButton) return;
    submitButton.disabled = isLoading;
    submitButton.textContent = isLoading ? 'Procesando...' : 'Registrarse';
};

registerForm?.addEventListener('submit', function (event) {
    event.preventDefault();

    const nombre = document.getElementById('Nombre')?.value.trim();
    const cedula = document.getElementById('Cedula')?.value.trim();
    const correo = document.getElementById('CorreoElectronico')?.value.trim();
    const telefono = document.getElementById('Telefono')?.value.trim();
    const contrasena = document.getElementById('Contraseña')?.value;
    const confirmarContrasena = document.getElementById('ConfirmarContraseña')?.value;
    const aceptaTerminos = document.getElementById('AceptarTerminos')?.checked;

    if (!nombre || !cedula || !correo || !contrasena || !confirmarContrasena || !aceptaTerminos) {
        setMessage('Completa todos los campos obligatorios y acepta los términos y condiciones.', 'error');
        return;
    }

    if (!/^\d{1,8}$/.test(cedula)) {
        setMessage('La cédula debe contener solo números y hasta 8 dígitos.', 'error');
        return;
    }

    if (!/^\S+@\S+\.\S+$/.test(correo)) {
        setMessage('Ingresa un correo electrónico válido.', 'error');
        return;
    }

    if (contrasena !== confirmarContrasena) {
        setMessage('Las contraseñas no coinciden.', 'error');
        return;
    }

    if (telefono && !/^[0-9+\-\s()]{6,15}$/.test(telefono)) {
        setMessage('El número telefónico no es válido.', 'error');
        return;
    }

    setLoadingState(true);
    setMessage('Validando información...', 'success');
    this.submit();
});