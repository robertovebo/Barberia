<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
$rol = isset($_GET['rol']) ? $_GET['rol'] : (isset($_POST['rol']) ? $_POST['rol'] : '');

$tablas_permitidas = ['administrador', 'recepcionista', 'barbero'];
if (!in_array($rol, $tablas_permitidas) || $id === 0) {
    die("Datos de seguridad inválidos.");
}

// Armamos el nombre correcto de la llave primaria de tu base de datos
$columna_id = 'id_' . $rol;

// ======================================================================
// FASE 2: GUARDAR CAMBIOS (UPDATE)
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $turno = $_POST['turno'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql_update = "UPDATE $rol SET nombre=?, apellidos=?, telefono=?, turno=?, password=? WHERE $columna_id=?";
        $stmt = $conexion->prepare($sql_update);
        $stmt->bind_param("sssssi", $nombre, $apellidos, $telefono, $turno, $password, $id);
    } else {
        $sql_update = "UPDATE $rol SET nombre=?, apellidos=?, telefono=?, turno=? WHERE $columna_id=?";
        $stmt = $conexion->prepare($sql_update);
        $stmt->bind_param("ssssi", $nombre, $apellidos, $telefono, $turno, $id);
    }

    if ($stmt->execute()) {
        // REDIRECCIÓN LIMPIA PARA ACTIVAR SWEETALERT EN EL PANEL
        header("Location: paginaAdministrador.php?busqueda=&mensaje=editado_exito");
        exit;
    } else {
        echo "<script>alert('Error al actualizar. Posiblemente el teléfono ya pertenece a otra persona.'); window.history.back();</script>";
        exit;
    }
}

// ======================================================================
// FASE 1: OBTENER LOS DATOS (SELECT)
// ======================================================================
$sql_select = "SELECT nombre, apellidos, telefono, turno FROM $rol WHERE $columna_id = ?";
$stmt_select = $conexion->prepare($sql_select);
$stmt_select->bind_param("i", $id);
$stmt_select->execute();
$resultado = $stmt_select->get_result();
$empleado = $resultado->fetch_assoc();

if (!$empleado) {
    die("Empleado no encontrado en la base de datos.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>

    <div class="contenedor-formulario">
        <h2>Editar <?php echo ucfirst($rol); ?></h2>
        
        <form action="editarPersonal.php" method="POST">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="rol" value="<?php echo $rol; ?>">

            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="nombre">Nombre(s):</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>

                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($empleado['apellidos']); ?>" required>
                </div>

                <div class="columna-mitad">
                    <label for="telefono">Teléfono (Usuario):</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empleado['telefono']); ?>" required>

                    <label for="turno">Turno:</label>
                    <select id="turno" name="turno" required>
                        <option value="Matutino" <?php if($empleado['turno'] == 'Matutino') echo 'selected'; ?>>Matutino</option>
                        <option value="Vespertino" <?php if($empleado['turno'] == 'Vespertino') echo 'selected'; ?>>Vespertino</option>
                    </select>
                </div>
            </div>

            <label for="password" style="text-align: left; display: block;">Nueva Contraseña (Opcional):</label>
            <input type="password" id="password" name="password" placeholder="Deja en blanco para no cambiarla">

            <button class="boton-registrar" type="submit">Guardar Cambios</button>

            <a href="paginaAdministrador.php?busqueda=" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>

        </form>
    </div>

</body>
</html>