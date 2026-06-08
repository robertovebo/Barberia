<?php
// CANDADO DE SEGURIDAD
session_start();
// Si no hay sesión iniciada, o si el que entró NO es recepcionista, lo expulsamos al login
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'recepcionista') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Recepcionista - Barbería</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="barra-sup">
        <h1 class="titulo-barra-sup">Recepcionista: <?php echo $_SESSION['nombre_usuario']; ?></h1>

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

            <button class="boton-barra-izq" onclick="mostrarSeccion('ventas')">
                <i class="fa-solid fa-cash-register icono-barra-izq"></i>
                <span class="texto-barra-izq">Ventas</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('servicios')">
                <i class="fa-solid fa-scissors icono-barra-izq"></i>
                <span class="texto-barra-izq">Servicios</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('clientes')">
                <i class="fa-solid fa-users icono-barra-izq"></i>
                <span class="texto-barra-izq">Clientes</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('personal')">
                <i class="fa-solid fa-user-tie icono-barra-izq"></i>
                <span class="texto-barra-izq">Personal</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('dashboard')">
                <i class="fa-solid fa-chart-pie icono-barra-izq"></i>
                <span class="texto-barra-izq">Dashboard</span>
            </button>
        </div>

        <div class="contenedor-secundario">

            <div id="dashboard" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Dashboard del Día</h1>
                <h5 style="margin-top: 15px;">Poner dashboard</h5>
            </div>

            <div id="citas" class="seccion-dinamica">
                <h1 class="titulo-principal">Gestión de Citas</h1>
                <h5 style="margin-top: 15px;">El recepcionista podrá agregar, consultar, editar y cancelar citas.</h5>
            </div>

            <div id="ventas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Gestión de Ventas</h1>
                <h5 style="margin-top: 15px;">Podra registrar y consultar, no eliminar.</h5>
            </div>

            <div id="servicios" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Catálogo de Servicios</h1>
                <h5 style="margin-top: 15px;">Podra consultar los servicios disponibles.</h5>
            </div>

            <div id="clientes" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Clientes</h1>
                <h5 style="margin-top: 15px;">Registrar nuevos clientes, consultar clientes, editar, eliminar, dar de
                    baja a clientes.</h5>
            </div>

            <div id="personal" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Gestión de Barberos</h1>
                <h5 style="margin-top: 15px;">Puede consultar/buscar a los barberos</h5>
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