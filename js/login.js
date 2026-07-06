/**
 * ARCHIVO: login.js - Gestiona registro y validación de datos de usuario
 */

// Al hacer click en "Registrarse" valida y guarda los datos
document.getElementById('btnAgregar').onclick = function () {

    // Obtiene valores del formulario
    const usuario = document.getElementById('Usuario').value.trim(); // Nombre de usuario
    const contraseña = document.getElementById('Contraseña').value.trim(); // Contraseña
    const correo = document.getElementById('Correo Electronico').value.trim(); // Email
    const contacto = document.getElementById('Contacto').value.trim(); // Teléfono
    const pais = document.getElementById('Pais').value.trim(); // País

    // Valida que todos los campos estén completos
    if (!usuario || !contraseña || !correo || !contacto || !pais) {
        alert('Completa todos los campos para registrar al usuario.');
        return;
    }

    // Agrega los datos al campo de comentario (almacenamiento temporal)
    document.getElementById('comentario').value +=
        `${usuario} | ${correo} | ${contacto} | ${pais}\n`;

    // Limpia los campos después de registrar
    document.getElementById('Usuario').value = '';
    document.getElementById('Contraseña').value = '';
    document.getElementById('Correo Electronico').value = '';
    document.getElementById('Contacto').value = '';
    document.getElementById('Pais').value = '';

};