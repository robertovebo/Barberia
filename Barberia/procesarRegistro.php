<?php
// 1. INCLUIMOS EL ARCHIVO DE CONEXIÓN
require_once 'conexion.php';

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 2. RECIBIR DATOS DEL FORMULARIO
$rol = $_POST['rol'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$telefono = $_POST['telefono'];
$turno = $_POST['turno'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptado seguro

// 3. SELECCIONAR TABLA Y QUERY SEGÚN EL ROL
if ($rol === 'administrador') {
    $sql = "INSERT INTO administrador (nombre, apellidos, password, telefono, turno) VALUES (?, ?, ?, ?, ?)";
} elseif ($rol === 'recepcionista') {
    $sql = "INSERT INTO recepcionista (nombre, apellidos, password, telefono, turno) VALUES (?, ?, ?, ?, ?)";
} elseif ($rol === 'barbero') {
    $sql = "INSERT INTO barbero (nombre, apellidos, password, telefono, turno) VALUES (?, ?, ?, ?, ?)";
} else {
    die("Rol no válido");
}

// 4. EJECUTAR CON SENTENCIA PREPARADA
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellidos, $password, $telefono, $turno);

if ($stmt->execute()) {
    // CORREGIDO: Redirige de vuelta al panel del administrador
    echo "<script>alert('Registro exitoso'); window.location.href='paginaAdministrador.php';</script>";
} else {
    echo "<script>alert('Error: El teléfono ya existe o datos inválidos.'); window.history.back();</script>";
}