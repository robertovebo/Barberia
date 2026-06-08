<?php
// 1. Encendemos la sesión para poder acceder a ella
session_start();

// 2. Vaciamos todas las variables de sesión
$_SESSION = array();

// 3. Destruimos la sesión en el servidor
session_destroy();

// 4. Redirigimos al usuario de vuelta a la página principal pública
header("Location: paginaPrincipal.php");
exit;
?>