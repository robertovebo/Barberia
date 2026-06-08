<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $duracion = intval($_POST['duracion_minutos']);

    $sql_insert = "INSERT INTO servicios (nombre, descripcion, precio, duracion_minutos) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql_insert);
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $duracion);

    if ($stmt->execute()) {
        header("Location: paginaAdministrador.php?busqueda=&mensaje=registro_exito");
        exit;
    } else {
        echo "<script>alert('Error al registrar el servicio en la base de datos.'); window.history.back();</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Servicio</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>
<body>
    <div class="contenedor-formulario">
        <h2>Registrar Servicio</h2>
        <form action="registroServicio.php" method="POST">
            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="nombre">Nombre del Servicio:</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Corte Clásico">
                </div>
                <div class="columna-mitad">
                    <label for="precio">Precio ($):</label>
                    <input type="number" step="0.01" id="precio" name="precio" required placeholder="Ej. 150.00" style="width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
            </div>
            
            <div class="contenedor-columnas">
                <div class="columna-mitad">
                    <label for="duracion_minutos">Duración Aprox. (Minutos):</label>
                    <input type="number" id="duracion_minutos" name="duracion_minutos" required placeholder="Ej. 30" style="width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ccc; border-radius: 8px;">
                </div>
                <div class="columna-mitad">
                    <label for="descripcion">Descripción breve:</label>
                    <input type="text" id="descripcion" name="descripcion" required placeholder="Ej. Corte con tijera y máquina">
                </div>
            </div>

            <button class="boton-registrar" type="submit">Guardar Servicio</button>
            <a href="paginaAdministrador.php?busqueda=" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>
        </form>
    </div>
</body>
</html>