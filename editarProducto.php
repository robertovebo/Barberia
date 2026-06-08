<?php
session_start();
require_once 'conexion.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE productos SET nombre=?, marca=?, precio_venta=?, stock=?, descripcion=? WHERE id_producto=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdisi", $_POST['nombre'], $_POST['marca'], $_POST['precio_venta'], $_POST['stock'], $_POST['descripcion'], $id);
    $stmt->execute();
    header("Location: paginaAdministrador.php?busqueda=&mensaje=editado_exito");
    exit;
}

$prod = $conexion->query("SELECT * FROM productos WHERE id_producto = $id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>
    <div class="contenedor-formulario">
        <h2>Editar Producto</h2>
        <form action="editarProducto.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>" required>
            <input type="text" name="marca" value="<?php echo htmlspecialchars($prod['marca']); ?>" required>
            <input type="number" step="0.01" name="precio_venta" value="<?php echo $prod['precio_venta']; ?>" required>
            <input type="number" name="stock" value="<?php echo $prod['stock']; ?>" required>
            <input type="text" name="descripcion" value="<?php echo htmlspecialchars($prod['descripcion']); ?>">
            <button class="boton-registrar" type="submit">Actualizar</button>
            <a href="paginaAdministrador.php?busqueda="><button class="contenedor-formulario-salir" type="button">Cancelar</button></a>
        </form>
    </div>
</body>
</html>