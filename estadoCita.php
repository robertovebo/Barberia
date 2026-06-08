<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

$id_cita = isset($_GET['id']) ? intval($_GET['id']) : 0;
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

if ($id_cita > 0 && in_array($accion, ['completar', 'cancelar', 'reactivar'])) {
    $nuevo_estado = '';
    if ($accion === 'completar') $nuevo_estado = 'Completada';
    if ($accion === 'cancelar') $nuevo_estado = 'Cancelada';
    if ($accion === 'reactivar') $nuevo_estado = 'Pendiente';

    $stmt = $conexion->prepare("UPDATE citas SET estado = ? WHERE id_cita = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_cita);
    
    if($stmt->execute()){
        header("Location: paginaAdministrador.php?seccion=citas&mensaje=editado_exito");
    } else {
        header("Location: paginaAdministrador.php?seccion=citas&mensaje=error_bd");
    }
} else {
    header("Location: paginaAdministrador.php?seccion=citas&mensaje=error_bd");
}
exit;
?>