<?php
/* Compatibilidad con clientes antiguos: el endpoint histórico conserva su URL,
   pero reutiliza el controlador central que procesa multipart, censura y JSON. */
$_POST['accion'] = 'enviar';
require __DIR__ . '/mensajes.php';
