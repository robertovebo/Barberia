<?php
// CANDADO DE SEGURIDAD
session_start();
// Expulsar si no hay sesión o si el rol NO es barbero
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'barbero') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Barberos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="barra-sup">
        <h1 class="titulo-barra-sup">Barbero: <?php echo $_SESSION['nombre_usuario']; ?></h1>

        <a href="https://maps.app.goo.gl/x4VrjDAqCGPrazVo9" target="_blank" rel="noopener noreferrer"
            class="boton-ubicacion">
            <i class="fa-solid fa-map-location-dot"></i> Ver Ubicación
        </a>

        <a href="cerrarSesion.php" class="boton-iniciar-sesion">Cerrar Sesión</a>
    </div>

    <div class="contenedor-principal">

        <div class="barra-izq">
            <button class="boton-barra-izq" onclick="mostrarSeccion('citas')">
                <i class="fa-regular fa-calendar-days icono-barra-izq"></i>
                <span class="texto-barra-izq">Citas</span>
            </button>
        </div>

        <div class="contenedor-secundario">

            <div id="citas" class="seccion-dinamica">
                <h1 class="titulo-principal">Mis Citas Agendadas</h1>
                <h5 style="margin-top: 15px;">Mostrar solamente las citas programadas. no puede editar, agregar o
                    eliminar.</h5>
            </div>

        </div>

    </div>

    <script>
        function mostrarSeccion(idSeccion) {
            let secciones = document.querySelectorAll('.seccion-dinamica');

            secciones.forEach(function (seccion) {
                seccion.style.display = 'none';
            });

            document.getElementById(idSeccion).style.display = 'block';
        }
    </script>

</body>

</html>