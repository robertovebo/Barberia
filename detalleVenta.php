<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_venta === 0) die("ID de venta no válido.");

// Obtener datos del encabezado
$sql_mecanismo = "SELECT v.id_venta, v.total, v.metodo_pago, v.fecha_hora, c.nombre, c.apellidos 
                  FROM ventas v 
                  LEFT JOIN cliente c ON v.id_cliente = c.id_cliente 
                  WHERE v.id_venta = ?";
$stmt_m = $conexion->prepare($sql_mecanismo);
$stmt_m->bind_param("i", $id_venta);
$stmt_m->execute();
$venta = $stmt_m->get_result()->fetch_assoc();

if (!$venta) die("La venta no existe.");

// Obtener detalles combinados de productos y servicios
$sql_detalles = "SELECT d.cantidad, d.precio_unitario, p.nombre AS producto, s.nombre AS servicio
                 FROM detalle_venta d
                 LEFT JOIN productos p ON d.id_producto = p.id_producto
                 LEFT JOIN servicios s ON d.id_servicio = s.id_servicio
                 WHERE d.id_venta = ?";
$stmt_d = $conexion->prepare($sql_detalles);
$stmt_d->bind_param("i", $id_venta);
$stmt_d->execute();
$detalles_res = $stmt_d->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Venta #<?php echo $id_venta; ?></title>
    <link rel="stylesheet" href="styles-formularios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="contenedor-formulario" style="max-width: 600px;">
        <h2><i class="fa-solid fa-receipt"></i> Detalle de Venta #<?php echo $id_venta; ?></h2>
        
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; line-height: 1.6;">
            <b>Cliente:</b> <?php echo htmlspecialchars($venta['nombre'] . " " . $venta['apellidos']); ?><br>
            <b>Fecha y Hora:</b> <?php echo $venta['fecha_hora']; ?><br>
            <b>Método de Pago:</b> <?php echo $venta['metodo_pago']; ?>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #e2e8f0;">
                    <th style="padding: 10px; border: 1px solid #cbd5e1; text-align: left;">Ítem</th>
                    <th style="padding: 10px; border: 1px solid #cbd5e1; text-align: center;">Cant.</th>
                    <th style="padding: 10px; border: 1px solid #cbd5e1; text-align: right;">Precio</th>
                    <th style="padding: 10px; border: 1px solid #cbd5e1; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = $detalles_res->fetch_assoc()) { 
                    $nombre_item = $d['producto'] ? "[Prod] " . $d['producto'] : "[Serv] " . $d['servicio'];
                    $subtotal = $d['cantidad'] * $d['precio_unitario'];
                ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #cbd5e1;"><?php echo htmlspecialchars($nombre_item); ?></td>
                        <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: center;"><?php echo $d['cantidad']; ?></td>
                        <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right;">$<?php echo number_format($d['precio_unitario'], 2); ?></td>
                        <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">$<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                <?php } ?>
                <tr style="background: #f1f5f9; font-size: 16px;">
                    <td colspan="3" style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold;">Monto Total:</td>
                    <td style="padding: 10px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #166534;">$<?php echo number_format($venta['total'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <a href="eliminarVenta.php?id=<?php echo $id_venta; ?>" style="text-decoration: none;" onclick="return confirm('¿Seguro que deseas eliminar y cancelar permanentemente esta venta? Se restablecerá el inventario afectado.');">
            <button class="boton-registrar" type="button" style="background-color: #dc2626;"><i class="fa-solid fa-trash-can"></i> Eliminar / Cancelar Venta</button>
        </a>
        
        <a href="paginaAdministrador.php?seccion=ventas" style="text-decoration: none;">
            <button class="contenedor-formulario-salir" type="button" style="margin-top: 10px;">Regresar al Panel</button>
        </a>
    </div>
</body>
</html>