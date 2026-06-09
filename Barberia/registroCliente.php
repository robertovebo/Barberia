<?php
session_start();
// Permite el paso si el usuario es administrador O recepcionista
if (!isset($_SESSION['rol_usuario']) || ($_SESSION['rol_usuario'] !== 'administrador' && $_SESSION['rol_usuario'] !== 'recepcionista')) {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';
// ... el resto de tu código sigue igual

// 2. PROCESAR EL GUARDADO (Cuando se envía el formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);

    // Insertamos al nuevo cliente (el estatus 'Activo' se pone por defecto en la base de datos)
    $sql_insert = "INSERT INTO cliente (nombre, apellidos, telefono) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("sss", $nombre, $apellidos, $telefono);

    if ($stmt->execute()) {
        // Regresamos al panel con la alerta de éxito
        header("Location: paginaAdministrador.php?busqueda=&mensaje=registro_exito");
        exit;
    } else {
        echo "<script>alert('Error al registrar. Es posible que el teléfono ya exista en el sistema.'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Cliente</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>

    <div class="contenedor-formulario">
        <h2>Registrar Nuevo Cliente</h2>
        
        <form action="registroCliente.php" method="POST">
            
            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="nombre">Nombre(s):</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Juan">
                </div>

                <div class="columna-mitad">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required placeholder="Ej. Pérez Gómez">
                </div>
            </div>

            <label for="telefono">Número de Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required placeholder="A 10 dígitos">

            <button class="boton-registrar" type="submit">Guardar Cliente</button>

            <a href="paginaAdministrador.php?busqueda=" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>

        </form>
    </div>

</body>
</html>