<?php
// Parámetros de conexión
$servidor = "sql3.freesqldatabase.com";
$usuario = "sql3829815";
$password = "SzxM1INd2c";
$base_datos = "sql3829815";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

// Configurar el formato de texto para que acepte acentos y eñes sin problemas
$conexion->set_charset("utf8mb4");
?>