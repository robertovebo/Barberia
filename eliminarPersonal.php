<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}

require_once 'conexion.php';

if (isset($_GET['id']) && isset($_GET['rol']) && isset($_GET['accion'])) {
    $id = intval($_GET['id']);
    $rol = $_GET['rol'];
    $accion = $_GET['accion'];

    // =================================================================
    // ESCUDO ANTI-SUICIDIO DIGITAL
    // Evita que te borres o suspendas a ti mismo
    // =================================================================
    if ($rol === 'administrador' && $id === $_SESSION['id_usuario']) {
        echo "<script>alert('ERROR DE SEGURIDAD: No puedes suspender ni borrar tu propia cuenta mientras estás en sesión.'); window.history.back();</script>";
        exit;
    }

    $tablas_permitidas = ['administrador', 'recepcionista', 'barbero'];
    
    if (in_array($rol, $tablas_permitidas)) {
        $columna_id = 'id_' . $rol; 
        
        // UN SOLO BLOQUE DE ACCIONES LIMPIO
        if ($accion === 'baja') {
            $sql = "UPDATE $rol SET estatus = 'Inactivo' WHERE $columna_id = ?";
            $tipo_mensaje = "baja_exitosa";
        } elseif ($accion === 'alta') { 
            $sql = "UPDATE $rol SET estatus = 'Activo' WHERE $columna_id = ?";
            $tipo_mensaje = "alta_exitosa";
        } elseif ($accion === 'borrar') {
            $sql = "DELETE FROM $rol WHERE $columna_id = ?";
            $tipo_mensaje = "borrado_exitoso";
        } else {
            die("Acción desconocida.");
        }

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Redirección limpia enviando el éxito por la URL
            header("Location: paginaAdministrador.php?busqueda=&mensaje=" . $tipo_mensaje);
        } else {
            header("Location: paginaAdministrador.php?busqueda=&mensaje=error_bd");
        }
        $stmt->close();
        exit;
    }
}
header("Location: paginaAdministrador.php");
exit;
?>