<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $calificacion = intval($_POST['calificacion']);
    $comentario = trim($_POST['comentario']);

    // Validamos que no envíen datos vacíos o manipulados
    if (!empty($nombre) && !empty($comentario) && $calificacion >= 1 && $calificacion <= 5) {
        $stmt = $conexion->prepare("INSERT INTO resenas (nombre_cliente, calificacion, comentario) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $nombre, $calificacion, $comentario);
        $stmt->execute();
    }
}

// Redirigir de vuelta a la página principal, abriendo automáticamente la pestaña de reseñas
header("Location: paginaPrincipal.php?seccion=resenas&mensaje=gracias");
exit;
?>