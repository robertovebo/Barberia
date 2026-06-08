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
    $metodo_pago = $_POST['metodo_pago'];
    
    $tipos = $_POST['tipo'] ?? [];
    $item_ids = $_POST['item_id'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if ($id_cliente === 0 || empty($item_ids)) {
        echo "<script>alert('Error: Debe seleccionar un cliente y al menos un artículo.'); window.history.back();</script>";
        exit;
    }

    $conexion->begin_transaction();

    try {
        // 1. Crear el registro maestro de la venta
        $sql_venta = "INSERT INTO ventas (id_cliente, id_recepcionista, metodo_pago, total) VALUES (?, NULL, ?, 0.00)";
        $stmt_v = $conexion->prepare($sql_venta);
        $stmt_v->bind_param("is", $id_cliente, $metodo_pago);
        $stmt_v->execute();
        $id_venta = $conexion->insert_id;

        $total_venta = 0.00;

        // 2. Iterar sobre todos los artículos agregados en la vista
        for ($i = 0; $i < count($item_ids); $i++) {
            $tipo = $tipos[$i];
            $item_id = intval($item_ids[$i]);
            $cantidad = intval($cantidades[$i]);
            $precio_unitario = 0.00;

            if ($item_id === 0 || $cantidad <= 0) continue;

            if ($tipo === 'producto') {
                $stmt_p = $conexion->prepare("SELECT precio_venta FROM productos WHERE id_producto = ?");
                $stmt_p->bind_param("i", $item_id);
                $stmt_p->execute();
                $precio_unitario = $stmt_p->get_result()->fetch_assoc()['precio_venta'] ?? 0.00;

                $sql_det = "INSERT INTO detalle_venta (id_venta, id_producto, id_servicio, cantidad, precio_unitario) VALUES (?, ?, NULL, ?, ?)";
                $stmt_det = $conexion->prepare($sql_det);
                $stmt_det->bind_param("iiid", $id_venta, $item_id, $cantidad, $precio_unitario);
            } else {
                $stmt_s = $conexion->prepare("SELECT precio FROM servicios WHERE id_servicio = ?");
                $stmt_s->bind_param("i", $item_id);
                $stmt_s->execute();
                $precio_unitario = $stmt_s->get_result()->fetch_assoc()['precio'] ?? 0.00;

                $sql_det = "INSERT INTO detalle_venta (id_venta, id_producto, id_servicio, cantidad, precio_unitario) VALUES (?, NULL, ?, ?, ?)";
                $stmt_det = $conexion->prepare($sql_det);
                $stmt_det->bind_param("iiid", $id_venta, $item_id, $cantidad, $precio_unitario);
            }

            $stmt_det->execute();
            $total_venta += ($precio_unitario * $cantidad);
        }

        // 3. Actualizar el total general calculado
        $sql_up = "UPDATE ventas SET total = ? WHERE id_venta = ?";
        $stmt_up = $conexion->prepare($sql_up);
        $stmt_up->bind_param("di", $total_venta, $id_venta);
        $stmt_up->execute();

        $conexion->commit();
        header("Location: paginaAdministrador.php?seccion=ventas&mensaje=registro_exito");
        exit;

    } catch (Exception $e) {
        $conexion->rollback();
        echo "Error crítico durante el guardado: " . $e->getMessage();
        exit;
    }
}
?>