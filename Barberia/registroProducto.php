<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $marca = trim($_POST['marca']);
    $precio = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);
    $desc = trim($_POST['descripcion']);

    $sql = "INSERT INTO productos (nombre, marca, precio_venta, stock, descripcion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdis", $nombre, $marca, $precio, $stock, $desc);

    if ($stmt->execute()) {
        header("Location: paginaAdministrador.php?busqueda=&mensaje=registro_exito");
    } else {
        echo "<script>alert('Error al registrar producto.'); window.history.back();</script>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>
    <div class="contenedor-formulario">
        <h2>Registrar Producto</h2>
        <form action="registroProducto.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre del producto" required>
            <input type="text" name="marca" placeholder="Marca" required>
            <input type="number" step="0.01" name="precio_venta" placeholder="Precio de venta" required>
            <input type="number" name="stock" placeholder="Cantidad en stock" required>
            <input type="text" name="descripcion" placeholder="Descripción breve">
            <button class="boton-registrar" type="submit">Guardar Producto</button>
            <a href="paginaAdministrador.php?busqueda="><button class="contenedor-formulario-salir" type="button">Cancelar</button></a>
        </form>
    </div>
</body>
</html>