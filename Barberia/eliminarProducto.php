<?php
session_start();
require_once 'conexion.php';
if (isset($_GET['id'])) {
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    header("Location: paginaAdministrador.php?busqueda=&mensaje=borrado_exitoso");
}
?>