document.getElementById('btnAgregar').onclick = function () {

    const usuario = document.getElementById('Usuario').value.trim();
    const contraseña = document.getElementById('Contraseña').value.trim();
    const correo = document.getElementById('Correo Electronico').value.trim();
    const contacto = document.getElementById('Contacto').value.trim();
    const pais = document.getElementById('Pais').value.trim();

    if (!usuario || !contraseña || !correo || !contacto || !pais) {
        alert('Completa todos los campos para registrar al usuario.');
        return;
    }

    document.getElementById('comentario').value +=
        `${usuario} | ${correo} | ${contacto} | ${pais}\n`;

    document.getElementById('Usuario').value = '';
    document.getElementById('Contraseña').value = '';
    document.getElementById('Correo Electronico').value = '';
    document.getElementById('Contacto').value = '';
    document.getElementById('Pais').value = '';


};