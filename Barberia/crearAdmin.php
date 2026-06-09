<?php
require_once 'conexion.php';

$nombre = 'Administrador';
$apellidos = 'Principal';
$telefono = '6121234567';
$password_plana = 'admin123';
$turno = 'Matutino';

// Encriptamos la contraseña con el mismo algoritmo seguro que usa tu login
$password_encriptada = password_hash($password_plana, PASSWORD_DEFAULT);

$sql = "INSERT INTO administrador (nombre, apellidos, password, telefono, turno, estatus) 
        VALUES (?, ?, ?, ?, ?, 'Activo')";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssss", $nombre, $apellidos, $password_encriptada, $telefono, $turno);

if ($stmt->execute()) {
    echo "<div style='font-family: Arial; text-align: center; margin-top: 50px;'>";
    echo "<h2 style='color: #166534;'>✅ ¡Administrador Maestro Creado!</h2>";
    echo "<p><b>Usuario (Teléfono):</b> 6121234567</p>";
    echo "<p><b>Contraseña:</b> admin123</p>";
    echo "<a href='login.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #1A3A52; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
    echo "<p style='color: #991b1b; margin-top: 30px;'><b>⚠️ IMPORTANTE:</b> Por seguridad, borra este archivo (crearAdmin.php) de tu carpeta en este momento.</p>";
    echo "</div>";
} else {
    echo "Error al crear el usuario: " . $conexion->error;
}
?>