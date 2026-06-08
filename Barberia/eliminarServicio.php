<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Preparamos la consulta con el nombre correcto de la tabla (servicios)
    $sql = "DELETE FROM servicios WHERE id_servicio = ?";
    
    // 2. ESTA ES LA LÍNEA QUE FALTABA: Creamos la variable $stmt
    $stmt = $conexion->prepare($sql);
    
    // 3. Ahora sí podemos amarrar el parámetro sin que marque error de "null"
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirección con éxito
        header("Location: paginaAdministrador.php?busqueda=&mensaje=borrado_exitoso");
    } else {
        // Redirección con error
        header("Location: paginaAdministrador.php?busqueda=&mensaje=error_bd");
    }
    $stmt->close();
    exit;
}

header("Location: paginaAdministrador.php");
exit;
?>