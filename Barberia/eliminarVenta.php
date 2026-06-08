<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_venta > 0) {
    $stmt = $conexion->prepare("DELETE FROM ventas WHERE id_venta = ?");
    $stmt->bind_param("i", $id_venta);
    $stmt->execute();
    header("Location: paginaAdministrador.php?seccion=ventas&mensaje=borrado_exitoso");
    exit;
} else {
    header("Location: paginaAdministrador.php?seccion=ventas&mensaje=error_bd");
    exit;
}
?>