<?php
// CANDADO DE SEGURIDAD
session_start();
// Expulsar si no hay sesión o si el rol NO es barbero
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'barbero') {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
require_once 'conexion.php';

// Obtener las variables de búsqueda si existen
$busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';
$fecha_filtro = isset($_GET['fecha_filtro']) ? $conexion->real_escape_string($_GET['fecha_filtro']) : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Barbero - Barbería</title>
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
            <button class="boton-barra-izq" onclick="mostrarSeccion('citas')" style="background-color: rgba(255,255,255,0.1);">
                <i class="fa-regular fa-calendar-days icono-barra-izq"></i>
                <span class="texto-barra-izq">Mis Citas</span>
            </button>
        </div>

        <div class="contenedor-secundario">

            <div id="citas" class="seccion-dinamica" style="display: block;">
                <h1 class="titulo-principal">Mis Citas Agendadas</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Consulta los servicios y horarios que tienes asignados.</h5>

                <div class="crud-encabezado" style="justify-content: flex-end;">
                    <form method="GET" action="" class="crud-buscador">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por cliente o servicio...">
                        <input type="date" name="fecha_filtro" class="crud-input" value="<?php echo htmlspecialchars($fecha_filtro); ?>" style="max-width: 150px;">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Filtrar</button>
                        <a href="paginaBarbero.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                // 1. FILTRO DE SEGURIDAD: Solo mostramos citas del barbero logueado
                $id_barbero_sesion = intval($_SESSION['id_usuario']);
                $filtro_citas = " WHERE ci.id_barbero = $id_barbero_sesion";

                // 2. Filtros de búsqueda del usuario
                if ($busqueda != '') {
                    $filtro_citas .= " AND (c.nombre LIKE '%$busqueda%' OR c.apellidos LIKE '%$busqueda%' OR s.nombre LIKE '%$busqueda%')";
                }
                
                if ($fecha_filtro != '') {
                    $filtro_citas .= " AND DATE(ci.fecha_hora) = '$fecha_filtro'";
                }

                // 3. Consulta a la base de datos (Excluimos los datos del barbero porque es él mismo)
                $sql_citas = "SELECT ci.id_cita, ci.fecha_hora, ci.estado, 
                                     c.nombre AS cliente_nom, c.apellidos AS cliente_ape, c.telefono,
                                     GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios_lista,
                                     IFNULL(SUM(s.duracion_minutos), 0) AS duracion_total
                              FROM citas ci
                              JOIN cliente c ON ci.id_cliente = c.id_cliente
                              LEFT JOIN detalle_cita dc ON ci.id_cita = dc.id_cita
                              LEFT JOIN servicios s ON dc.id_servicio = s.id_servicio
                              $filtro_citas
                              GROUP BY ci.id_cita
                              ORDER BY ci.fecha_hora ASC"; // Ordenamos ascendente para ver la agenda del día en orden

                $resultado_citas = $conexion->query($sql_citas);

                if ($resultado_citas && $resultado_citas->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>Fecha y Hora</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Servicios Requeridos</th>
                            <th>Tiempo Aprox.</th>
                            <th>Estado</th>
                          </tr>";

                    while ($cita = $resultado_citas->fetch_assoc()) {
                        echo "<tr>";
                        
                        // Formatear fecha y hora
                        $fecha_format = date("d/m/Y", strtotime($cita['fecha_hora']));
                        $hora_format = date("h:i A", strtotime($cita['fecha_hora']));
                        
                        echo "<td><b>$fecha_format</b><br><span style='color: #C41E3A;'>$hora_format</span></td>";
                        
                        echo "<td>" . htmlspecialchars($cita['cliente_nom'] . " " . $cita['cliente_ape']) . "</td>";
                        
                        echo "<td><i class='fa-solid fa-phone' style='font-size: 12px; color: #64748b;'></i> " . htmlspecialchars($cita['telefono']) . "</td>";
                        
                        echo "<td><span style='font-size: 13px; color: #475569;'>" . htmlspecialchars($cita['servicios_lista']) . "</span></td>";
                        
                        echo "<td><b>" . $cita['duracion_total'] . " min</b></td>";
                        
                        // Lógica visual del Estado
                        if ($cita['estado'] === 'Pendiente') {
                            echo "<td><span style='background-color: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Pendiente</span></td>";
                        } elseif ($cita['estado'] === 'Completada') {
                            echo "<td><span style='background-color: #dcfce3; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Completada</span></td>";
                        } else {
                            echo "<td><span style='background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Cancelada</span></td>";
                        }
                        
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777; font-size: 16px;'><i class='fa-regular fa-calendar-xmark'></i> No tienes citas agendadas con estos filtros.</p>";
                }
                ?>
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