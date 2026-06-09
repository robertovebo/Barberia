<?php
// 1. MANDAMOS LLAMAR A NUESTRO ARCHIVO CENTRAL DE CONEXIÓN AL INICIO
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
    <style>
        /* Estilos específicos para el formulario de reseñas */
        .caja-resena { width: 80%; margin: 0 auto 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: left; }
        .input-resena { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border-radius: 6px; border: 1px solid #cbd5e1; box-sizing: border-box; }
        .btn-enviar-resena { background-color: #1A3A52; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-enviar-resena:hover { background-color: #0f172a; }
        .comentario-publicado { width: 80%; margin: 0 auto 15px auto; background: #f8fafc; padding: 15px; border-left: 4px solid #f59e0b; border-radius: 4px; text-align: left; }
        /* =========================================================
           ESTILOS PARA EL INICIO (HERO Y TARJETAS)
           ========================================================= */
        .hero-banner { 
            text-align: center; 
            padding: 40px 20px; 
            background-color: #1A3A52; 
            color: white; 
            border-radius: 8px; 
            margin-bottom: 30px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        }
        .hero-banner h1 { margin-top: 0; font-size: 32px; }
        .hero-banner p { font-size: 18px; font-weight: 300; color: #e2e8f0; }
        
        .contenedor-imagenes { 
            display: flex; 
            gap: 20px; 
            justify-content: space-between; 
            flex-wrap: wrap; 
            margin-bottom: 30px; 
        }
        .tarjeta-imagen { 
            flex: 1; 
            min-width: 300px; 
            background: white; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            text-align: center; 
            padding-bottom: 15px; 
        }
        .img-inicio { 
            width: 100%; 
            height: 250px; 
            object-fit: cover; /* Asegura que la imagen no se deforme */
            background-color: #e2e8f0; /* Fondo gris temporal si no hay imagen */
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
        }
        .tarjeta-imagen h3 { color: #1A3A52; margin: 15px 0 5px 0; }
        .tarjeta-imagen p { color: #555; font-size: 14px; padding: 0 15px; }



    </style>
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
                <span class="texto-barra-izq">Precios y Productos</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('resenas')">
                <i class="fa-regular fa-star icono-barra-izq"></i>
                <span class="texto-barra-izq">Reseñas</span>
            </button>
        </div>

        <div class="contenedor-secundario">

           <div id="inicio" class="seccion-dinamica">
                
                <div class="hero-banner">
                    <h1><i class="fa-solid fa-scissors"></i> Bienvenido a la Barbería</h1>
                    <p>Somos un negocio de cortes de caballero que busca reflejar profesionalidad a su disposición.</p>
                </div>

                <div class="contenedor-imagenes">
                    
                    <div class="tarjeta-imagen">
                        <img src="imag/instalaciones.jpg" alt="Nuestras Instalaciones" class="img-inicio" 
                             onerror="this.outerHTML='<div class=\'img-inicio\'><i class=\'fa-solid fa-image fa-2x\'></i></div>'">
                        <h3>Nuestras Instalaciones</h3>
                        <p>Un ambiente diseñado para tu máxima comodidad y relajación total mientras esperas.</p>
                    </div>

                    <div class="tarjeta-imagen">
                        <img src="imag/catalogoCorte.jpeg" alt="Nuestro Trabajo" class="img-inicio"
                             onerror="this.outerHTML='<div class=\'img-inicio\'><i class=\'fa-solid fa-image fa-2x\'></i></div>'">
                        <h3>Cortes de Excelencia</h3>
                        <p>Nuestros barberos dominan desde los estilos clásicos hasta las tendencias más modernas.</p>
                    </div>

                </div>
                
            </div>

            <!-- CITAS -->
            <div id="citas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Horarios Ocupados</h1>
                <h5 style="margin-bottom: 20px; color: #555;">Consulta la agenda. (Los espacios mostrados ya están reservados)</h5>

                <?php
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
                        $inicio_obj = new DateTime($agenda['fecha_hora']);
                        $fecha_formato = $inicio_obj->format('d/m/Y');
                        $hora_inicio = $inicio_obj->format('h:i A');
                        
                        $minutos = $agenda['duracion_total'] > 0 ? $agenda['duracion_total'] : 30;
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

            <!-- PRECIOS Y PRODUCTOS -->
            <div id="precios" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal"><i class="fa-solid fa-scissors"></i> Tablas de precios por servicio</h1>
                
                <?php
                // TABLA DE SERVICIOS
                $sql = "SELECT nombre, descripcion, precio, duracion_minutos FROM servicios ORDER BY precio ASC";
                $resultado = $conexion->query($sql);

                if ($resultado && $resultado->num_rows > 0) {
                    echo "<table style='width:80%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;'>";
                    echo "<tr style='background-color: #1A3A52; color: white; text-align: left;'>
                            <th style='padding: 12px;'>Servicio</th>
                            <th style='padding: 12px;'>Descripción</th>
                            <th style='padding: 12px;'>Duración</th>
                            <th style='padding: 12px;'>Precio</th>
                          </tr>";

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
                    echo "<p style='text-align:center; color:#777;'>Aún no hay servicios registrados.</p>";
                }

                // TABLA DE PRODUCTOS (NUEVO)
                echo "<h1 class='titulo-principal' style='margin-top: 50px;'><i class='fa-solid fa-box-open'></i> Productos a la Venta</h1>";
                
                // Asegúrate de que la columna estatus existe, si no quita el "WHERE estatus = 'Activo'"
                $sql_prod = "SELECT nombre, marca, precio_venta FROM productos WHERE estatus = 'Activo' ORDER BY nombre ASC";
                $resultado_prod = $conexion->query($sql_prod);

                if ($resultado_prod && $resultado_prod->num_rows > 0) {
                    echo "<table style='width:80%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;'>";
                    echo "<tr style='background-color: #1A3A52; color: white; text-align: left;'>
                            <th style='padding: 12px;'>Producto</th>
                            <th style='padding: 12px;'>Marca</th>
                            <th style='padding: 12px;'>Precio</th>
                          </tr>";

                    while ($prod = $resultado_prod->fetch_assoc()) {
                        echo "<tr style='border-bottom: 1px solid #dddddd;'>";
                        echo "<td style='padding: 12px; font-weight: bold; color: #1A3A52;'>" . htmlspecialchars($prod['nombre']) . "</td>";
                        echo "<td style='padding: 12px; color: #555;'>" . htmlspecialchars($prod['marca']) . "</td>";
                        echo "<td style='padding: 12px; font-weight: bold; color: #C41E3A; font-size: 18px;'>$" . number_format($prod['precio_venta'], 2) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; color:#777;'>Aún no hay productos registrados en la tienda.</p>";
                }
                ?>
            </div>

            <!-- RESEÑAS PÚBLICAS (NUEVO) -->
            <div id="resenas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Opiniones de Nuestros Clientes</h1>
                <h5 style="margin-bottom: 25px;">¿Nos visitaste recientemente? ¡Déjanos tu reseña!</h5>

                <!-- Formulario Público -->
                <div class="caja-resena">
                    <h3 style="margin-top:0; color:#1A3A52;"><i class="fa-solid fa-pen"></i> Escribir Reseña</h3>
                    <form action="procesarResena.php" method="POST">
                        <label><b>Tu Nombre:</b></label>
                        <input type="text" name="nombre" class="input-resena" required placeholder="Ej. Juan Pérez">
                        
                        <label><b>Calificación:</b></label>
                        <select name="calificacion" class="input-resena" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5/5) ¡Excelente servicio!</option>
                            <option value="4">⭐⭐⭐⭐ (4/5) Muy bueno</option>
                            <option value="3">⭐⭐⭐ (3/5) Bueno</option>
                            <option value="2">⭐⭐ (2/5) Regular</option>
                            <option value="1">⭐ (1/5) Malo</option>
                        </select>

                        <label><b>Comentario:</b></label>
                        <textarea name="comentario" class="input-resena" rows="3" required placeholder="Cuéntanos tu experiencia..."></textarea>

                        <button type="submit" class="btn-enviar-resena">Publicar Reseña</button>
                    </form>
                </div>

                <!-- Historial de Reseñas de la Base de Datos -->
                <?php
                $sql_resenas = "SELECT nombre_cliente, comentario, calificacion, fecha FROM resenas ORDER BY fecha DESC LIMIT 20";
                $res_resenas = $conexion->query($sql_resenas);
                
                if ($res_resenas && $res_resenas->num_rows > 0) {
                    while ($row = $res_resenas->fetch_assoc()) {
                        $estrellas = str_repeat('⭐', $row['calificacion']);
                        $fecha_formateada = date('d/m/Y', strtotime($row['fecha']));
                        
                        echo "<div class='comentario-publicado'>";
                        echo "<div style='display: flex; justify-content: space-between; align-items: center;'>
                                <span>$estrellas</span>
                                <span style='color: #94a3b8; font-size: 13px;'>$fecha_formateada</span>
                              </div>";
                        echo "<p style='margin: 10px 0; font-style: italic; color: #334155;'>\"" . htmlspecialchars($row['comentario']) . "\"</p>";
                        echo "<b><i class='fa-regular fa-user'></i> " . htmlspecialchars($row['nombre_cliente']) . "</b>";
                        echo "</div>";
                    }
                } else {
                    echo "<p style='text-align:center; color:#777;'>Aún no hay reseñas. ¡Sé el primero en opinar!</p>";
                }
                
                // CERRAR CONEXIÓN HASTA EL FINAL
                $conexion->close();
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

        // Script para detectar si venimos de enviar una reseña y abrir esa pestaña
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('seccion') === 'resenas') {
                mostrarSeccion('resenas');
                if (urlParams.get('mensaje') === 'gracias') {
                    alert('¡Gracias por tu reseña! Ha sido publicada con éxito.');
                    window.history.replaceState(null, null, window.location.pathname);
                }
            }
        };
    </script>

</body>

</html>