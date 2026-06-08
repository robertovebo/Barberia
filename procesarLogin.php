<?php
// 1. INICIAR SESIÓN
session_start();

// 2. CONEXIÓN A LA BASE DE DATOS
require_once 'conexion.php';

// 3. CAPTURAR DATOS DEL FORMULARIO
$telefono = trim($_POST['telefono']);
$password_ingresada = $_POST['password'];
$usuario_encontrado = null; // Inicializamos la variable por seguridad

// 4. BUSCAR EN LA TABLA ADMINISTRADOR (¡Solo si está Activo!)
$sql_admin = "SELECT id_administrador AS id, nombre, password FROM administrador WHERE telefono = ? AND estatus = 'Activo'";
$stmt = $conexion->prepare($sql_admin);
$stmt->bind_param("s", $telefono);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $usuario_encontrado = $resultado->fetch_assoc();
    $rol_detectado = 'administrador';
}
$stmt->close();

// 5. BUSCAR EN RECEPCIONISTA (Si no fue admin y está Activo)
if (!$usuario_encontrado) {
    $sql_recep = "SELECT id_recepcionista AS id, nombre, password FROM recepcionista WHERE telefono = ? AND estatus = 'Activo'";
    $stmt = $conexion->prepare($sql_recep);
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario_encontrado = $resultado->fetch_assoc();
        $rol_detectado = 'recepcionista';
    }
    $stmt->close();
}

// 6. BUSCAR EN BARBERO (Si no fue admin ni recepcionista y está Activo)
if (!$usuario_encontrado) {
    $sql_barbero = "SELECT id_barbero AS id, nombre, password FROM barbero WHERE telefono = ? AND estatus = 'Activo'";
    $stmt = $conexion->prepare($sql_barbero);
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario_encontrado = $resultado->fetch_assoc();
        $rol_detectado = 'barbero';
    }
    $stmt->close();
}

// 7. VERIFICACIÓN Y REDIRECCIÓN DIRECTA AL PANEL CORRESPONDIENTE
if ($usuario_encontrado) {
    if (password_verify($password_ingresada, $usuario_encontrado['password'])) {
        
        // Guardamos sus datos en la memoria (¡Esto es vital para el escudo anti-suicidio!)
        $_SESSION['id_usuario'] = $usuario_encontrado['id'];
        $_SESSION['nombre_usuario'] = $usuario_encontrado['nombre'];
        $_SESSION['rol_usuario'] = $rol_detectado;

        // Decidimos a qué pantalla enviarlo directamente sin pasar por el inicio
        $pagina_destino = "";
        if ($rol_detectado === 'administrador') {
            $pagina_destino = "paginaAdministrador.php";
        } elseif ($rol_detectado === 'recepcionista') {
            $pagina_destino = "paginaRecepcionista.php";
        } elseif ($rol_detectado === 'barbero') {
            $pagina_destino = "paginaBarbero.php";
        }

        // Lo enviamos a su panel de trabajo
        echo "<script>
                window.location.href = '" . $pagina_destino . "'; 
              </script>";
        exit;
    } else {
        echo "<script>alert('Error: La contraseña es incorrecta.'); window.history.back();</script>";
    }
} else {
    // Si el usuario no existe, o si existe pero su estatus es 'Inactivo', caerá aquí
    echo "<script>alert('Error: El teléfono no está registrado o la cuenta ha sido suspendida.'); window.history.back();</script>";
}

$conexion->close();
?>