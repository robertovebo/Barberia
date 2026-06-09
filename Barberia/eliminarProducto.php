<?php
session_start();

// Validamos que exclusivamente el administrador pueda hacer estos cambios
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

if ($id_producto > 0) {
    if ($accion === 'baja') {
        // Cambia el estatus a Inactivo
        $stmt = $conexion->prepare("UPDATE productos SET estatus = 'Inactivo' WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        header("Location: paginaAdministrador.php?seccion=inventario&mensaje=baja_exitosa");
        exit;
        
    } elseif ($accion === 'alta') {
        // Devuelve el estatus a Activo
        $stmt = $conexion->prepare("UPDATE productos SET estatus = 'Activo' WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        header("Location: paginaAdministrador.php?seccion=inventario&mensaje=alta_exitosa");
        exit;
        
    } elseif ($accion === 'borrar') {
        // Borra permanentemente de la base de datos
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        header("Location: paginaAdministrador.php?seccion=inventario&mensaje=borrado_exitoso");
        exit;
    }
}

// Si falla algo, regresa al inventario
header("Location: paginaAdministrador.php?seccion=inventario&mensaje=error_bd");
exit;
?>