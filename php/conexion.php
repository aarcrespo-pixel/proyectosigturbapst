<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "bapst";

$conexion = @new mysqli($servidor, $usuario, $password, $baseDatos);

if ($conexion->connect_error) {
    http_response_code(503);
    echo "No se pudo conectar a la base de datos.";
    exit;
}

echo "Conexión realizada correctamente.";
?>