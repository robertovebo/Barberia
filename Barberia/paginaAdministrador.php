<?php
// CANDADO DE SEGURIDAD DE MÁXIMO NIVEL
session_start();
// Expulsar si no hay sesión o si el rol NO es administrador
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

// 1. CONEXIÓN A LA BASE DE DATOS PARA TODO EL PANEL
require_once 'conexion.php';

// 2. DETECTAR BÚSQUEDA Y MENSAJES DE ÉXITO/ERROR
$busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';
$mensaje_alerta = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <div class="barra-sup">

        <h1 class="titulo-barra-sup">Administrador: <?php echo $_SESSION['nombre_usuario']; ?></h1>

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

            <button class="boton-barra-izq" onclick="mostrarSeccion('inventario')">
                <i class="fa-solid fa-warehouse icono-barra-izq"></i>
                <span class="texto-barra-izq">Inventario</span>
            </button>

            <button class="boton-barra-izq" onclick="mostrarSeccion('dashboard')">
                <i class="fa-solid fa-chart-pie icono-barra-izq"></i>
                <span class="texto-barra-izq">Dashboard</span>
            </button>
        </div>

        <div class="contenedor-secundario">

            <div id="citas" class="seccion-dinamica" style="display: <?php echo ($busqueda == '' && (!isset($_GET['seccion']) || $_GET['seccion'] == 'citas')) ? 'block' : 'none'; ?>;">
                <h1 class="titulo-principal">Control Global de Citas</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Gestión de agenda, asignación de barberos y estados.</h5>

                <div class="crud-encabezado">
                    <a href="registrarCita.php" class="btn btn-agregar">
                        <i class="fa-regular fa-calendar-plus"></i> Agendar Cita
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="citas">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar cliente, barbero o servicio...">
                        <input type="date" name="fecha_filtro" class="crud-input" value="<?php echo isset($_GET['fecha_filtro']) ? htmlspecialchars($_GET['fecha_filtro']) : ''; ?>" style="max-width: 150px;">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Filtrar</button>
                        <a href="paginaAdministrador.php?seccion=citas" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_citas = " WHERE 1=1"; 
                $fecha_filtro = isset($_GET['fecha_filtro']) ? $_GET['fecha_filtro'] : '';

                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'citas') {
                    $filtro_citas .= " AND (c.nombre LIKE '%$busqueda%' OR c.apellidos LIKE '%$busqueda%' OR b.nombre LIKE '%$busqueda%' OR s.nombre LIKE '%$busqueda%')";
                }
                
                if ($fecha_filtro != '') {
                    $filtro_citas .= " AND DATE(ci.fecha_hora) = '$fecha_filtro'";
                }

                // Consulta que agrupa los servicios utilizando GROUP_CONCAT
                $sql_citas = "SELECT ci.id_cita, ci.fecha_hora, ci.estado, 
                                     c.nombre AS cliente_nom, c.apellidos AS cliente_ape, 
                                     b.nombre AS barbero_nom, b.apellidos AS barbero_ape,
                                     GROUP_CONCAT(s.nombre SEPARATOR ', ') AS servicios_lista
                              FROM citas ci
                              JOIN cliente c ON ci.id_cliente = c.id_cliente
                              JOIN barbero b ON ci.id_barbero = b.id_barbero
                              LEFT JOIN detalle_cita dc ON ci.id_cita = dc.id_cita
                              LEFT JOIN servicios s ON dc.id_servicio = s.id_servicio
                              $filtro_citas
                              GROUP BY ci.id_cita
                              ORDER BY ci.fecha_hora DESC";

                $resultado_citas = $conexion->query($sql_citas);

                if ($resultado_citas && $resultado_citas->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Fecha y Hora</th>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Servicios</th>
                            <th>Estado</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($cita = $resultado_citas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $cita['id_cita'] . "</b></td>";
                        
                        // Formatear fecha y hora visualmente
                        $fecha_format = date("d/m/Y h:i A", strtotime($cita['fecha_hora']));
                        echo "<td>" . $fecha_format . "</td>";
                        
                        echo "<td>" . htmlspecialchars($cita['cliente_nom'] . " " . $cita['cliente_ape']) . "</td>";
                        echo "<td>" . htmlspecialchars($cita['barbero_nom'] . " " . $cita['barbero_ape']) . "</td>";
                        echo "<td><span style='font-size: 13px; color: #475569;'>" . htmlspecialchars($cita['servicios_lista']) . "</span></td>";
                        
                        // Lógica visual del Estado (Pendiente, Completada, Cancelada)
                        if ($cita['estado'] === 'Pendiente') {
                            echo "<td><span style='background-color: #fef08a; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Pendiente</span></td>";
                        } elseif ($cita['estado'] === 'Completada') {
                            echo "<td><span style='background-color: #dcfce3; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Completada</span></td>";
                        } else {
                            echo "<td><span style='background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Cancelada</span></td>";
                        }
                        
                        // Botones de acción dinámicos según el estado
                        echo "<td style='text-align: center;'>";
                        
                        if ($cita['estado'] === 'Pendiente') {
                            // NUEVO BOTÓN DE EDITAR
                            echo "<a href='editarCita.php?id=" . $cita['id_cita'] . "' class='accion-editar' style='color: #2563eb;'>
                                    <i class='fa-solid fa-pen-to-square'></i> Editar
                                  </a>
                                  
                                  <a href='estadoCita.php?id=" . $cita['id_cita'] . "&accion=completar' class='accion-editar' style='color: #166534;' onclick=\"return confirm('¿Marcar cita como Completada?');\">
                                    <i class='fa-solid fa-check-circle'></i> Completar
                                  </a>
                                  
                                  <a href='estadoCita.php?id=" . $cita['id_cita'] . "&accion=cancelar' class='accion-eliminar' style='background: transparent; color: #dc2626;' onclick=\"return confirm('¿Cancelar esta cita? El espacio volverá a estar disponible.');\">
                                    <i class='fa-solid fa-ban'></i> Cancelar
                                  </a>";
                        } elseif ($cita['estado'] === 'Cancelada') {
                            echo "<a href='estadoCita.php?id=" . $cita['id_cita'] . "&accion=reactivar' class='accion-editar' style='color: #ca8a04;' onclick=\"return confirm('¿Reactivar cita a estado Pendiente?');\">
                                    <i class='fa-solid fa-rotate-left'></i> Reactivar
                                  </a>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron citas con esos filtros.</p>";
                }
                ?>
            </div>

            <div id="ventas" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Control de Ventas</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Registro de ventas, productos y servicios.</h5>

                <div class="crud-encabezado">
                    <a href="registrarVenta.php" class="btn btn-agregar">
                        <i class="fa-solid fa-cart-plus"></i> Nueva Venta
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="ventas">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar por cliente o método de pago...">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        <a href="paginaAdministrador.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_ventas = "";
                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'ventas') {
                    $filtro_ventas = " WHERE c.nombre LIKE '%$busqueda%' OR c.apellidos LIKE '%$busqueda%' OR v.metodo_pago LIKE '%$busqueda%'";
                }

                $sql_ventas = "SELECT v.id_venta, c.nombre, c.apellidos, v.total, v.metodo_pago, v.fecha_hora 
                               FROM ventas v 
                               LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                               $filtro_ventas
                               ORDER BY v.fecha_hora DESC";
                $resultado_ventas = $conexion->query($sql_ventas);

                if ($resultado_ventas && $resultado_ventas->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Método</th>
                            <th>Total</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($v = $resultado_ventas->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $v['id_venta'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($v['nombre'] . " " . $v['apellidos']) . "</td>";
                        echo "<td>" . $v['fecha_hora'] . "</td>";
                        echo "<td>" . $v['metodo_pago'] . "</td>";
                        echo "<td style='font-weight: bold; color: #166534;'>$" . number_format($v['total'], 2) . "</td>";
                        echo "<td style='text-align: center;'>
                                <a href='detalleVenta.php?id=" . $v['id_venta'] . "' class='accion-editar'>
                                    <i class='fa-solid fa-eye'></i> Detalle
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron ventas registradas.</p>";
                }
                ?>
            </div>

            <div id="servicios" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Configuración de Servicios y Precios</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Catálogo de cortes, diseños de barba y tratamientos.</h5>

                <div class="crud-encabezado">
                    <a href="registroServicio.php" class="btn btn-agregar">
                        <i class="fa-solid fa-scissors"></i> Agregar Servicio
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="servicios">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar servicio...">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        <a href="paginaAdministrador.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_servicios = "";
                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'servicios') {
                    $filtro_servicios = " WHERE nombre LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%'";
                }

                $sql_servicios = "SELECT id_servicio AS id, nombre, descripcion, precio, duracion_minutos FROM servicios $filtro_servicios ORDER BY nombre ASC";
                $resultado_servicios = $conexion->query($sql_servicios);

                if ($resultado_servicios && $resultado_servicios->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Servicio / Corte</th>
                            <th>Descripción</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($servicio = $resultado_servicios->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $servicio['id'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($servicio['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($servicio['descripcion']) . "</td>";
                        echo "<td>" . htmlspecialchars($servicio['duracion_minutos']) . " min</td>";
                        echo "<td style='font-weight: bold; color: #166534;'>$" . number_format($servicio['precio'], 2) . "</td>";
                        
                        echo "<td style='text-align: center;'>
                                <a href='editarServicio.php?id=" . $servicio['id'] . "' class='accion-editar'>
                                    <i class='fa-solid fa-pen-to-square'></i> Editar
                                </a>

                                <a href='eliminarServicio.php?id=" . $servicio['id'] . "' class='accion-eliminar' onclick=\"return confirm('¡Peligro! ¿Eliminar este servicio del catálogo permanentemente?');\">
                                    <i class='fa-solid fa-trash'></i> Eliminar
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron servicios en el catálogo.</p>";
                }
                ?>
            </div>

            <div id="clientes" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Control de Clientes</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Registrar, consultar, editar y dar de baja a los clientes de la barbería.</h5>

                <div class="crud-encabezado">
                    <a href="registroCliente.php" class="btn btn-agregar">
                        <i class="fa-solid fa-user-plus"></i> Registrar Cliente
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="clientes">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar cliente por nombre o teléfono...">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        <a href="paginaAdministrador.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_clientes = "";
                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'clientes') {
                    $filtro_clientes = " WHERE nombre LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%'";
                }

                $sql_clientes = "SELECT id_cliente AS id, nombre, apellidos, telefono, estatus FROM cliente $filtro_clientes ORDER BY nombre ASC";
                $resultado_clientes = $conexion->query($sql_clientes);

                if ($resultado_clientes && $resultado_clientes->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Estatus</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($cliente = $resultado_clientes->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $cliente['id'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($cliente['nombre'] . " " . $cliente['apellidos']) . "</td>";
                        echo "<td>" . htmlspecialchars($cliente['telefono']) . "</td>";
                        
                        if ($cliente['estatus'] === 'Activo') {
                            echo "<td><span style='background-color: #dcfce3; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Activo</span></td>";
                        } else {
                            echo "<td><span style='background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Inactivo</span></td>";
                        }
                        
                        echo "<td style='text-align: center;'>
                                <a href='editarCliente.php?id=" . $cliente['id'] . "' class='accion-editar'>
                                    <i class='fa-solid fa-pen-to-square'></i> Editar
                                </a>";
                                
                        if ($cliente['estatus'] === 'Activo') {
                            echo "<a href='eliminarCliente.php?id=" . $cliente['id'] . "&accion=baja' class='accion-editar' style='color: #d97706;' onclick=\"return confirm('¿Inactivar Cliente? El cliente dejará de estar activo en el sistema.');\">
                                    <i class='fa-solid fa-user-slash'></i> Baja
                                  </a>";
                        } else {
                            echo "<a href='eliminarCliente.php?id=" . $cliente['id'] . "&accion=alta' class='accion-editar' style='color: #166534;' onclick=\"return confirm('¿Reactivar Cliente? El cliente podrá agendar citas de nuevo.');\">
                                    <i class='fa-solid fa-user-check'></i> Activar
                                  </a>";
                        }

                        echo "  <a href='eliminarCliente.php?id=" . $cliente['id'] . "&accion=borrar' class='accion-eliminar' onclick=\"return confirm('¡Peligro: Borrado Definitivo! Esto borrará al cliente y su historial. ¿Continuar?');\">
                                    <i class='fa-solid fa-trash'></i> Borrar
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron clientes registrados.</p>";
                }
                ?>
            </div>

            <div id="personal" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Administración de Personal</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Alta, baja, edición y consulta de todos los empleados.</h5>

                <div class="crud-encabezado">
                    <a href="registroPersonal.php" class="btn btn-agregar">
                        <i class="fa-solid fa-plus"></i> Agregar Empleado
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="personal">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar nombre, teléfono, turno...">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        <a href="paginaAdministrador.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_sql = "";
                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'personal') {
                    $filtro_sql = " WHERE nombre LIKE '%$busqueda%' OR apellidos LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%' OR turno LIKE '%$busqueda%'";
                }
                
                $sql_personal = "
                    SELECT 'administrador' AS rol, id_administrador AS id, nombre, apellidos, telefono, turno, estatus FROM administrador $filtro_sql
                    UNION ALL
                    SELECT 'recepcionista' AS rol, id_recepcionista AS id, nombre, apellidos, telefono, turno, estatus FROM recepcionista $filtro_sql
                    UNION ALL
                    SELECT 'barbero' AS rol, id_barbero AS id, nombre, apellidos, telefono, turno, estatus FROM barbero $filtro_sql
                    ORDER BY nombre ASC
                ";

                $resultado_personal = $conexion->query($sql_personal);

                if ($resultado_personal && $resultado_personal->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Rol</th>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Turno</th>
                            <th>Estatus</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($empleado = $resultado_personal->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $empleado['id'] . "</b></td>";
                        echo "<td style='text-transform: capitalize;'>" . $empleado['rol'] . "</td>";
                        echo "<td>" . htmlspecialchars($empleado['nombre'] . " " . $empleado['apellidos']) . "</td>";
                        echo "<td>" . htmlspecialchars($empleado['telefono']) . "</td>";
                        echo "<td>" . htmlspecialchars($empleado['turno']) . "</td>";
                        
                        if ($empleado['estatus'] === 'Activo') {
                            echo "<td><span style='background-color: #dcfce3; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Activo</span></td>";
                        } else {
                            echo "<td><span style='background-color: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Inactivo</span></td>";
                        }
                        
                        echo "<td style='text-align: center;'>
                                <a href='editarPersonal.php?id=" . $empleado['id'] . "&rol=" . $empleado['rol'] . "' class='accion-editar'>
                                    <i class='fa-solid fa-pen-to-square'></i> Editar
                                </a>";
                                
                        if ($empleado['estatus'] === 'Activo') {
                            echo "<a href='eliminarPersonal.php?id=" . $empleado['id'] . "&rol=" . $empleado['rol'] . "&accion=baja' class='accion-editar' style='color: #d97706;' onclick=\"return confirm('¿Suspender a este empleado? Ya no podrá iniciar sesión en el sistema.');\">
                                    <i class='fa-solid fa-user-slash'></i> Baja
                                  </a>";
                        } else {
                            echo "<a href='eliminarPersonal.php?id=" . $empleado['id'] . "&rol=" . $empleado['rol'] . "&accion=alta' class='accion-editar' style='color: #166534;' onclick=\"return confirm('¿Reactivar empleado? Volverá a tener acceso al sistema.');\">
                                    <i class='fa-solid fa-user-check'></i> Activar
                                  </a>";
                        }

                        echo "  <a href='eliminarPersonal.php?id=" . $empleado['id'] . "&rol=" . $empleado['rol'] . "&accion=borrar' class='accion-eliminar' onclick=\"return confirm('¡PELIGRO! ¿Borrar PERMANENTEMENTE a este empleado? Esto podría afectar el historial de ventas.');\">
                                    <i class='fa-solid fa-trash'></i> Borrar
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron empleados con esos datos.</p>";
                }
                ?>
            </div>
            
            <div id="inventario" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Control de Inventario</h1>
                <h5 class="titulo-secundario" style="margin-bottom: 20px;">Gestión de productos, marcas y existencias.</h5>

                <div class="crud-encabezado">
                    <a href="registroProducto.php" class="btn btn-agregar">
                        <i class="fa-solid fa-box-open"></i> Agregar Producto
                    </a>

                    <form method="GET" action="" class="crud-buscador">
                        <input type="hidden" name="seccion" value="inventario">
                        <input type="text" name="busqueda" class="crud-input" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar producto o marca...">
                        <button type="submit" class="btn btn-buscar"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
                        <a href="paginaAdministrador.php" class="btn btn-limpiar">Limpiar</a>
                    </form>
                </div>

                <?php
                $filtro_inv = "";
                if ($busqueda != '' && isset($_GET['seccion']) && $_GET['seccion'] === 'inventario') {
                    $filtro_inv = " WHERE nombre LIKE '%$busqueda%' OR marca LIKE '%$busqueda%'";
                }

                $sql_inv = "SELECT id_producto AS id, nombre, marca, precio_venta, stock FROM productos $filtro_inv ORDER BY nombre ASC";
                $resultado_inv = $conexion->query($sql_inv);

                if ($resultado_inv && $resultado_inv->num_rows > 0) {
                    echo "<table class='tabla-crud'>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Marca</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th style='text-align: center;'>Acciones</th>
                          </tr>";

                    while ($prod = $resultado_inv->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><b>" . $prod['id'] . "</b></td>";
                        echo "<td>" . htmlspecialchars($prod['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($prod['marca']) . "</td>";
                        echo "<td style='font-weight: bold; color: #166534;'>$" . number_format($prod['precio_venta'], 2) . "</td>";
                        
                        if ($prod['stock'] <= 3) {
                            echo "<td><b style='color: #991b1b; background-color: #fee2e2; padding: 4px 8px; border-radius: 4px;'>" . $prod['stock'] . " ¡Bajo!</b></td>";
                        } else {
                            echo "<td><span style='color: #166534; font-weight: bold;'>" . $prod['stock'] . " pzas</span></td>";
                        }
                        
                        echo "<td style='text-align: center;'>
                                <a href='editarProducto.php?id=" . $prod['id'] . "' class='accion-editar'>
                                    <i class='fa-solid fa-pen-to-square'></i> Editar
                                </a>
                                <a href='eliminarProducto.php?id=" . $prod['id'] . "' class='accion-eliminar' onclick=\"return confirm('¿Seguro que deseas eliminar este producto?');\">
                                    <i class='fa-solid fa-trash'></i> Borrar
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='text-align:center; padding: 20px; color:#777;'>No se encontraron productos.</p>";
                }
                ?>
            </div>

            <div id="dashboard" class="seccion-dinamica" style="display: none;">
                <h1 class="titulo-principal">Dashboard Administrativo</h1>
                <h5 style="margin-top: 15px;">Gráficas de rendimiento general del negocio</h5>
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

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // CONTROL INTELIGENTE DE PESTAÑAS ACTIVAS
            if (urlParams.has('seccion')) {
                mostrarSeccion(urlParams.get('seccion'));
            } else if (urlParams.has('mensaje')) {
                let m = urlParams.get('mensaje');
                if (m.includes('venta')) {
                    mostrarSeccion('ventas');
                } else if (m.includes('cliente')) {
                    mostrarSeccion('clientes');
                } else if (m.includes('servicio')) {
                    mostrarSeccion('servicios');
                } else if (m.includes('prod') || m.includes('inv')) {
                    mostrarSeccion('inventario');
                } else {
                    mostrarSeccion('personal');
                }
            } else {
                mostrarSeccion('citas'); 
            }

            let msj = "<?php echo isset($_GET['mensaje']) ? $_GET['mensaje'] : ''; ?>";
            
            if (msj === 'baja_exitosa') {
                alert('¡Suspendido! El registro cambió a Inactivo correctamente.');
            } else if (msj === 'alta_exitosa') {
                alert('¡Reactivado! El registro vuelve a estar Activo.');
            } else if (msj === 'borrado_exitoso') {
                alert('¡Eliminado! El registro fue borrado físicamente de la base de datos.');
            } else if (msj === 'editado_exito') {
                alert('¡Cambios Guardados! Los datos fueron actualizados correctamente.');
            } else if (msj === 'registro_exito') {
                alert('¡Registrado! El nuevo registro se guardó con éxito.');
            } else if (msj === 'error_bd') {
                alert('Error Crítico: No se pudo completar la operación en la base de datos.');
            }

            if (msj !== '') {
                window.history.replaceState(null, null, window.location.pathname);
            }
        };
    </script>

</body>

</html>