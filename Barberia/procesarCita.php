<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = intval($_POST['id_cliente']);
    $id_barbero = intval($_POST['id_barbero']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $servicios = $_POST['servicios'] ?? [];
    
    $id_recepcionista = null;
    if (isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'recepcionista') {
        $id_recepcionista = intval($_SESSION['id_usuario']);
    }

    if ($id_cliente === 0 || empty($servicios)) {
        echo "<script>alert('Error: Datos incompletos.'); window.history.back();</script>";
        exit;
    }

    // Unir fecha y hora para el formato DATETIME de MySQL
    $fecha_hora_inicio = $fecha . " " . $hora . ":00";

    $conexion->begin_transaction();

    try {
        // 1. Calcular duración total de la cita propuesta
        $duracion_total_minutos = 0;
        foreach ($servicios as $id_serv) {
            $stmt_s = $conexion->prepare("SELECT duracion_minutos FROM servicios WHERE id_servicio = ?");
            $stmt_s->bind_param("i", $id_serv);
            $stmt_s->execute();
            $duracion_total_minutos += $stmt_s->get_result()->fetch_assoc()['duracion_minutos'] ?? 0;
        }
        
        // 2. Comprobar superposición de citas (Choques de horario)
        // Buscamos todas las citas del barbero para ese mismo día que no estén canceladas
        $sql_choque = "SELECT ci.id_cita, ci.fecha_hora, 
                       IFNULL(SUM(s.duracion_minutos), 0) as duracion_existente
                       FROM citas ci
                       LEFT JOIN detalle_cita dc ON ci.id_cita = dc.id_cita
                       LEFT JOIN servicios s ON dc.id_servicio = s.id_servicio
                       WHERE ci.id_barbero = ? AND DATE(ci.fecha_hora) = ? AND ci.estado != 'Cancelada'
                       GROUP BY ci.id_cita";
        $stmt_ch = $conexion->prepare($sql_choque);
        $stmt_ch->bind_param("is", $id_barbero, $fecha);
        $stmt_ch->execute();
        $citas_existentes = $stmt_ch->get_result();

        $inicio_nuevo = strtotime($fecha_hora_inicio);
        $fin_nuevo = strtotime("+$duracion_total_minutos minutes", $inicio_nuevo);

        while ($cita_ex = $citas_existentes->fetch_assoc()) {
            $inicio_existente = strtotime($cita_ex['fecha_hora']);
            $fin_existente = strtotime("+" . $cita_ex['duracion_existente'] . " minutes", $inicio_existente);
            
            // Lógica de choque: (NuevoInicio < FinViejo) Y (NuevoFin > InicioViejo)
            if ($inicio_nuevo < $fin_existente && $fin_nuevo > $inicio_existente) {
                throw new Exception("El barbero ya tiene una cita ocupada en ese rango horario.");
            }
        }

        // 3. Insertar encabezado de cita (AQUÍ SE DISPARA EL TRIGGER DE VALIDACIÓN DE TURNO)
        $sql_cita = "INSERT INTO citas (id_cliente, id_barbero, id_recepcionista, fecha_hora, estado) VALUES (?, ?, ?, ?, 'Pendiente')";
        $stmt_c = $conexion->prepare($sql_cita);
        $stmt_c->bind_param("iiis", $id_cliente, $id_barbero, $id_recepcionista, $fecha_hora_inicio);
        $stmt_c->execute();
        $id_cita = $conexion->insert_id;

        // 4. Insertar servicios múltiples en la tabla de detalle_cita
        foreach ($servicios as $id_serv) {
            $sql_det = "INSERT INTO detalle_cita (id_cita, id_servicio) VALUES (?, ?)";
            $stmt_det = $conexion->prepare($sql_det);
            $stmt_det->bind_param("ii", $id_cita, $id_serv);
            $stmt_det->execute();
        }

        $conexion->commit();
        header("Location: paginaAdministrador.php?seccion=citas&mensaje=registro_exito");
        exit;

    } catch (Exception $e) {
        $conexion->rollback();
        echo style_error($e->getMessage());
        exit;
    }
}

function style_error($mensaje) {
    return "
    <div style='font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #991b1b; background-color: #fee2e2; border-radius: 8px; color: #991b1b;'>
        <h3 style='margin-top:0;'>❌ Error al Agendar Cita</h3>
        <p>No se pudo guardar la cita por la siguiente razón:</p>
        <code style='background: rgba(0,0,0,0.05); padding: 4px 8px; display: block; margin: 10px 0; border-radius: 4px;'>$mensaje</code>
        <button onclick='window.history.back()' style='background:#991b1b; color:white; border:none; padding: 8px 15px; border-radius: 4px; cursor:pointer; font-weight:bold; margin-top: 10px;'>Regresar al formulario</button>
    </div>";
}
?>