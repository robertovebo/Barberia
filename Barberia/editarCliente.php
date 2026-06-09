<?php
session_start();
// Permite el paso si el usuario es administrador O recepcionista
if (!isset($_SESSION['rol_usuario']) || ($_SESSION['rol_usuario'] !== 'administrador' && $_SESSION['rol_usuario'] !== 'recepcionista')) {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';
// ... el resto de tu código sigue igual

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);

if ($id === 0) {
    die("ID de cliente inválido.");
}

// ======================================================================
// FASE 2: GUARDAR CAMBIOS (UPDATE)
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];

    $sql_update = "UPDATE cliente SET nombre=?, apellidos=?, telefono=? WHERE id_cliente=?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param("sssi", $nombre, $apellidos, $telefono, $id);

    if ($stmt->execute()) {
        header("Location: paginaAdministrador.php?busqueda=&mensaje=editado_exito");
        exit;
    } else {
        echo "<script>alert('Error al actualizar. Posiblemente el teléfono ya esté registrado a otro cliente.'); window.history.back();</script>";
        exit;
    }
}

// ======================================================================
// FASE 1: OBTENER LOS DATOS (SELECT)
// ======================================================================
$sql_select = "SELECT nombre, apellidos, telefono FROM cliente WHERE id_cliente = ?";
$stmt_select = $conexion->prepare($sql_select);
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$cliente = $resultado->fetch_assoc();

if (!$cliente) {
    die("Cliente no encontrado en la base de datos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>

    <div class="contenedor-formulario">
        <h2>Editar Cliente</h2>
        
        <form action="editarCliente.php" method="POST">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="nombre">Nombre(s):</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
                </div>

                <div class="columna-mitad">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($cliente['apellidos']); ?>" required>
                </div>
            </div>

            <label for="telefono">Número de Teléfono:</label>
            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>

            <button class="boton-registrar" type="submit">Guardar Cambios</button>

            <a href="paginaAdministrador.php?busqueda=" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>

        </form>
    </div>

</body>
</html>