<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
if ($id === 0) die("ID de servicio inválido.");

// 1. PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $duracion = intval($_POST['duracion_minutos']);

    $sql_update = "UPDATE servicios SET nombre=?, descripcion=?, precio=?, duracion_minutos=? WHERE id_servicio=?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $duracion, $id);

    if ($stmt->execute()) {
        header("Location: paginaAdministrador.php?busqueda=&mensaje=editado_exito");
        exit;
    } else {
        echo "<script>alert('Error al actualizar el servicio.'); window.history.back();</script>";
        exit;
    }
}

// 2. OBTENER DATOS ACTUALES (¡Aquí estaba el error de la variable!)
$sql_select = "SELECT nombre, descripcion, precio, duracion_minutos FROM servicios WHERE id_servicio = ?";
$stmt_select = $conexion->prepare($sql_select); // Esta línea crea la variable $stmt_select
$stmt_select->bind_param("i", $id); // Ahora bind_param encontrará la variable
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$servicio = $resultado->fetch_assoc();

if (!$servicio) die("Servicio no encontrado.");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Servicio</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>
    <div class="contenedor-formulario">
        <h2>Editar Servicio</h2>
        <form action="editarServicio.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="nombre">Nombre del Servicio:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($servicio['nombre']); ?>" required>
                </div>
                <div class="columna-mitad">
                    <label for="precio">Precio ($):</label>
                    <input type="number" step="0.01" id="precio" name="precio" value="<?php echo htmlspecialchars($servicio['precio']); ?>" required style="width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
            </div>

            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="duracion_minutos">Duración Aprox. (Minutos):</label>
                    <input type="number" id="duracion_minutos" name="duracion_minutos" value="<?php echo htmlspecialchars($servicio['duracion_minutos']); ?>" required style="width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div class="columna-mitad">
                    <label for="descripcion">Descripción breve:</label>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($servicio['descripcion']); ?>" required>
                </div>
            </div>

            <button class="boton-registrar" type="submit">Guardar Cambios</button>
            <a href="paginaAdministrador.php?busqueda=" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>
        </form>
    </div>
</body>
</html>