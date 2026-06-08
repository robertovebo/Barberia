<?php
// 1. Iniciamos la sesión para poder revisar "los gafetes"
session_start();

// 2. Si el usuario YA tiene una sesión iniciada, lo sacamos de aquí
if (isset($_SESSION['rol_usuario'])) {
    if ($_SESSION['rol_usuario'] === 'administrador') {
        header("Location: paginaAdministrador.php");
        exit;
    } elseif ($_SESSION['rol_usuario'] === 'recepcionista') {
        header("Location: paginaRecepcionista.php");
        exit;
    } elseif ($_SESSION['rol_usuario'] === 'barbero') {
        header("Location: paginaBarbero.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Barbería</title>
    <link rel="stylesheet" href="styles-formularios.css">
</head>

<body>

    <div class="contenedor-formulario" style="max-width: 400px;">
        <h2>Iniciar Sesión</h2>
        
        <form action="procesarLogin.php" method="POST">

            <label for="telefono">Teléfono (Usuario):</label>
            <input type="text" id="telefono" name="telefono" required autofocus>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <div class="contenedor-formulario-botones">
                <button class="boton-registrar" type="submit">Entrar al Sistema</button>
            </div>

            <a href="paginaPrincipal.php">
                <button class="contenedor-formulario-salir" type="button">Volver al Inicio</button>
            </a>

        </form>
    </div>

</body>

</html>