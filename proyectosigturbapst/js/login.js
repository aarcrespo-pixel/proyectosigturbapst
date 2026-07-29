// Al hacer click en "Registrarse" valida y guarda los datos
document.getElementById('btnAgregar').onclick = function () {

    // Obtiene valores del formulario
    const usuario = document.getElementById('Usuario').value.trim(); // Nombre de usuario
    const correo = document.getElementById('Correo Electronico').value.trim(); // Email
    const contraseña = document.getElementById('Contraseña').value.trim(); // Contraseña
    const telefono = document.getElementById('Telefono').value.trim(); // Teléfono

    // Valida que todos los campos estén completos
    if (!usuario || !contraseña || !correo || !telefono) {
        alert('Completa todos los campos para registrar al usuario.');
        return;
    }

    // Agrega los datos al campo de comentario (almacenamiento temporal)
    document.getElementById('comentario').value +=
        `${usuario} | ${correo} | ${contraseña} | ${telefono}\n`;

    // Limpia los campos después de registrar
    document.getElementById('Usuario').value = '';
    document.getElementById('Contraseña').value = '';
    document.getElementById('Correo Electronico').value = '';
    document.getElementById('Telefono').value = '';

};