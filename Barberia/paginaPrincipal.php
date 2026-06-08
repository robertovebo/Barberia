<?php
// 1. MANDAMOS LLAMAR A NUESTRO ARCHIVO CENTRAL DE CONEXIÓN AL INICIO
// Así estará disponible para todas las secciones dinámicas (Citas y Precios)
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal - Barbería</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="barra-sup">
        <a href="https://maps.app.goo.gl/x4VrjDAqCGPrazVo9" target="_blank" rel="noopener noreferrer"
            class="boton-ubicacion boton-ubicacion-izq">
            <i class="fa-solid fa-map-location-dot"></i> Ver Ubicación
        </a>

        <a href="login.php" class="boton-iniciar-sesion">Iniciar Sesión</a>
    </div>

    <div class="contenedor-principal">

        <div class="barra-izq">
            <button class="boton-barra-izq" onclick="mostrarSeccion('inicio')">
                <i class="fa-solid fa-house icono-barra-izq"></i>
                <span class="texto-barra-izq">Inicio</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('citas')">
                <i class="fa-regular fa-calendar-days icono-barra-izq"></i>
                <span class="texto-barra-izq">Citas</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('precios')">
                <i class="fa-solid fa-tags icono-barra-izq"></i>
                <span class="texto-barra-izq">Precios</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('resenas')">
                <i class="fa-regular fa-star icono-barra-izq"></i>
                <span class="texto-barra-izq">Reseñas</span>
            </button>
        </div>

        <div class="contenedor-secundario">

            <div id="inicio" class="seccion-dinamica">
                <h1 class="titulo-principal">Bienvenido a la Barbería</h1>
                <i class="fa-solid fa-scissors iconos-titulo"></i>
                <h5>Somos un negocio de cortes de caballero que busca reflejar profesionalidad a su disposición</h5>
            </div>

            <div id="citas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Horarios Ocupados</h1>
                <h5 style="margin-bottom: 20px; color: #555;">Consulta la agenda. (Los espacios mostrados ya están reservados)</h5>

                <?php
                // Consultamos solo citas pendientes desde hoy en adelante.
                // Usamos SUM para calcular cuánto durará la cita y ocultamos los datos del cliente.
                $sql_agenda = "SELECT ci.fecha_hora, b.nombre AS barbero, 
                                      IFNULL(SUM(s.duracion_minutos), 0) AS duracion_total
                               FROM citas ci
                               JOIN barbero b ON ci.id_barbero = b.id_barbero
                               LEFT JOIN detalle_cita dc ON ci.id_cita = dc.id_cita
                               LEFT JOIN servicios s ON dc.id_servicio = s.id_servicio
                               WHERE ci.estado = 'Pendiente' AND DATE(ci.fecha_hora) >= CURDATE()
                               GROUP BY ci.id_cita
                               ORDER BY ci.fecha_hora ASC";
                
                $resultado_agenda = $conexion->query($sql_agenda);

                if ($resultado_agenda && $resultado_agenda->num_rows > 0) {
                    echo "<table style='width:80%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;'>";
                    echo "<tr style='background-color: #1A3A52; color: white; text-align: center;'>
                            <th style='padding: 12px;'>Fecha</th>
                            <th style='padding: 12px;'>Bloque Reservado</th>
                            <th style='padding: 12px;'>Barbero</th>
                          </tr>";

                    while ($agenda = $resultado_agenda->fetch_assoc()) {
                        // Procesamos la hora de inicio
                        $inicio_obj = new DateTime($agenda['fecha_hora']);
                        $fecha_formato = $inicio_obj->format('d/m/Y');
                        $hora_inicio = $inicio_obj->format('h:i A');
                        
                        // Calculamos la hora de fin sumando la duración de los servicios
                        $minutos = $agenda['duracion_total'] > 0 ? $agenda['duracion_total'] : 30; // 30 min por defecto
                        $fin_obj = clone $inicio_obj;
                        $fin_obj->modify("+$minutos minutes");
                        $hora_fin = $fin_obj->format('h:i A');

                        echo "<tr style='border-bottom: 1px solid #dddddd; text-align: center;'>";
                        echo "<td style='padding: 12px; font-weight: bold; color: #1A3A52;'>" . $fecha_formato . "</td>";
                        echo "<td style='padding: 12px; color: #C41E3A; font-weight: bold;'><i class='fa-regular fa-clock'></i> " . $hora_inicio . " - " . $hora_fin . "</td>";
                        echo "<td style='padding: 12px; color: #555;'><i class='fa-solid fa-user-tie'></i> " . htmlspecialchars($agenda['barbero']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; color:#166534; font-weight:bold; margin-top: 20px;'>¡Toda la agenda está libre! Ven y sé el primero en apartar tu lugar.</p>";
                }
                ?>
            </div>

            <div id="precios" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Tablas de precios por servicio</h1>
                
                <?php
                // Usamos la misma conexión de arriba
                $sql = "SELECT nombre, descripcion, precio, duracion_minutos FROM servicios ORDER BY precio ASC";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    echo "<table style='width:80%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;'>";
                    
                    // Encabezados
                    echo "<tr style='background-color: #1A3A52; color: white; text-align: left;'>
                            <th style='padding: 12px;'>Servicio</th>
                            <th style='padding: 12px;'>Descripción</th>
                            <th style='padding: 12px;'>Duración</th>
                            <th style='padding: 12px;'>Precio</th>
                          </tr>";

                    // Filas automáticas
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr style='border-bottom: 1px solid #dddddd;'>";
                        echo "<td style='padding: 12px; font-weight: bold; color: #1A3A52;'>" . htmlspecialchars($fila['nombre']) . "</td>";
                        echo "<td style='padding: 12px; color: #555;'>" . htmlspecialchars($fila['descripcion']) . "</td>";
                        echo "<td style='padding: 12px; color: #777;'>" . htmlspecialchars($fila['duracion_minutos']) . " min</td>";
                        echo "<td style='padding: 12px; font-weight: bold; color: #C41E3A; font-size: 18px;'>$" . number_format($fila['precio'], 2) . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; color:#777;'>Aún no hay servicios registrados en la barbería.</p>";
                }
                
                // 4. AHORA SÍ, CERRAMOS LA CONEXIÓN AL FINAL DE TODO
                $conexion->close();
                ?>
                
            </div>

            <div id="resenas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Reseñas de Clientes</h1>
                <h5 style="margin-top: 15px;">⭐⭐⭐⭐⭐ "El mejor corte de la ciudad, excelente ambiente." - Juan P.</h5>
                <h5 style="margin-top: 10px;">⭐⭐⭐⭐⭐ "Muy puntuales con la cita y muy limpios." - Carlos M.</h5>
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