<?php
// 🔐 CANDADO DE SEGURIDAD: Solo el Administrador puede registrar personal
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    // Si no ha iniciado sesión o no es admin, lo expulsamos al login
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Personal - Barbería</title>
    <link rel="stylesheet" href="styles-formularios.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="contenedor-formulario">
        <h2>Registrar Personal</h2>

        <form action="procesarRegistro.php" method="POST">

            <div class="contenedor-columnas">
                
                <div class="columna-mitad">
                    <label for="rol">Rol del Empleado:</label>
                    <select id="rol" name="rol" required>
                        <option value="" disabled selected>Elige un cargo...</option>
                        <option value="administrador">Administrador</option>
                        <option value="recepcionista">Recepcionista</option>
                        <option value="barbero">Barbero</option>
                    </select>

                    <label for="nombre">Nombres:</label>
                    <input type="text" id="nombre" name="nombre" required>

                    <label for="telefono">Teléfono (Será su Usuario):</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>

                <div class="columna-mitad">
                    <label for="turno">Turno:</label>
                    <select id="turno" name="turno" required>
                        <option value="" disabled selected>Selecciona un turno...</option>
                        <option value="Matutino">Matutino</option>
                        <option value="Vespertino">Vespertino</option>
                    </select>

                    <label for="apellidos">Apellidos:</label>
                    <input type="text" id="apellidos" name="apellidos" required>

                    <label for="password">Contraseña de acceso:</label>
                    <input type="password" id="password" name="password" required>
                </div>

            </div> 
            <div class="contenedor-formulario-botones">
                <button class="boton-registrar" type="submit">Guardar Registro</button>
            </div>

            <a href="paginaAdministrador.php">
                <button class="contenedor-formulario-salir" type="button">Cancelar y Volver</button>
            </a>

        </form>
    </div>
</body>

</html>