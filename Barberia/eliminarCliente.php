<?php
session_start();
// Permite el paso si el usuario es administrador O recepcionista
if (!isset($_SESSION['rol_usuario']) || ($_SESSION['rol_usuario'] !== 'administrador' && $_SESSION['rol_usuario'] !== 'recepcionista')) {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';
// ... el resto de tu código sigue igual

// Verificamos que viajen las variables necesarias por la URL
if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];
    
    // UN SOLO BLOQUE DE ACCIONES LIMPIO PARA CLIENTES
    if ($accion === 'baja') {
        $sql = "UPDATE cliente SET estatus = 'Inactivo' WHERE id_cliente = ?";
        $tipo_mensaje = "baja_exitosa";
    } elseif ($accion === 'alta') { 
        $sql = "UPDATE cliente SET estatus = 'Activo' WHERE id_cliente = ?";
        $tipo_mensaje = "alta_exitosa";
    } elseif ($accion === 'borrar') {
        $sql = "DELETE FROM cliente WHERE id_cliente = ?";
        $tipo_mensaje = "borrado_exitoso";
    } else {
        die("Acción desconocida.");
    }

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirección limpia enviando el éxito por la URL para que salte la alerta nativa
        header("Location: paginaAdministrador.php?busqueda=&mensaje=" . $tipo_mensaje);
    } else {
        header("Location: paginaAdministrador.php?busqueda=&mensaje=error_bd");
    }
    $stmt->close();
    exit;
}

// Si alguien entra al archivo sin darle clic a un botón, lo regresamos al panel
header("Location: paginaAdministrador.php");
exit;
?>