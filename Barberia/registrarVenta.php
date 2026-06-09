<?php
session_start();
// Permite el paso si el usuario es administrador O recepcionista
if (!isset($_SESSION['rol_usuario']) || ($_SESSION['rol_usuario'] !== 'administrador' && $_SESSION['rol_usuario'] !== 'recepcionista')) {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';
// ... el resto de tu código sigue igual

// Obtener clientes activos para el buscador
$clientes = $conexion->query("SELECT id_cliente, nombre, apellidos, telefono FROM cliente WHERE estatus = 'Activo'");
$clientes_array = array();
while($c = $clientes->fetch_assoc()) { $clientes_array[] = $c; }
$clientes_json = json_encode($clientes_array);

// Obtener catálogos para los select dinámicos
$productos = $conexion->query("SELECT id_producto, nombre, precio_venta, stock FROM productos WHERE stock > 0");
$prod_array = array();
while($p = $productos->fetch_assoc()) { $prod_array[] = $p; }

$servicios = $conexion->query("SELECT id_servicio, nombre, precio FROM servicios");
$serv_array = array();
while($s = $servicios->fetch_assoc()) { $serv_array[] = $s; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Nueva Venta</title>
    <link rel="stylesheet" href="styles-formularios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .bloque-buscador { display: flex; gap: 10px; margin-bottom: 8px; }
        .bloque-buscador input { flex: 1; margin-bottom: 0 !important; }
        .btn-buscar-tel { background-color: #0f172a; color: white; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .notificacion-cliente { font-size: 14px; font-weight: bold; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; display: none; }
        .tabla-items { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .tabla-items th, .tabla-items td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        .btn-add { background: #16a34a; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px; font-size: 13px; }
        .btn-remove { background: #dc2626; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .contenedor-total { text-align: right; font-size: 18px; font-weight: bold; margin: 15px 0; color: #166534; }
    </style>
</head>
<body>
    <div class="contenedor-formulario" style="max-width: 750px;">
        <h2>Registrar Nueva Venta</h2>
        <h5 style="margin-bottom: 20px; color: #555;">Datos necesarios:</h5>
        
        <form action="procesarVenta.php" method="POST">
            
            <label>Buscar Cliente (Número de Celular):</label>
            <div class="bloque-buscador">
                <input type="text" id="celular_buscar" placeholder="Ej. 6121234567" autocomplete="off">
                <button type="button" class="btn-buscar-tel" onclick="filtrarCliente()"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            </div>
            
            <div id="status_cliente" class="notificacion-cliente"></div>
            <input type="hidden" name="id_cliente" id="id_cliente_real" required>

            <label>Método de Pago:</label>
            <select name="metodo_pago" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Transferencia">Transferencia</option>
            </select>

            <hr>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <h3>Artículos de la Venta</h3>
                <div>
                    <button type="button" class="btn-add" onclick="agregarFila('producto')"><i class="fa-solid fa-box"></i> + Producto</button>
                    <button type="button" class="btn-add" style="background:#2563eb;" onclick="agregarFila('servicio')"><i class="fa-solid fa-scissors"></i> + Servicio</button>
                </div>
            </div>

            <table class="tabla-items" id="tabla_ventas">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción / Nombre</th>
                        <th style="width: 100px;">Cantidad</th>
                        <th style="width: 120px;">Precio Unitario</th>
                        <th style="width: 120px;">Subtotal</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>

            <div class="contenedor-total">
                Total a Pagar: $<span id="txt_total">0.00</span>
            </div>

            <button class="boton-registrar" type="submit" id="btn_finalizar" disabled>Finalizar Venta</button>
            <a href="paginaAdministrador.php?seccion=ventas" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>
        </form>
    </div>

    <script>
        const mapaClientes = <?php echo $clientes_json; ?>;
        const catProductos = <?php echo json_encode($prod_array); ?>;
        const catServicios = <?php echo json_encode($serv_array); ?>;

        function filtrarCliente() {
            const tel = document.getElementById('celular_buscar').value.trim();
            const box = document.getElementById('status_cliente');
            const idReal = document.getElementById('id_cliente_real');
            const btn = document.getElementById('btn_finalizar');

            box.style.display = "block";
            const match = mapaClientes.find(c => c.telefono === tel);

            if (match) {
                idReal.value = match.id_cliente;
                box.innerText = "✅ Cliente Encontrado: " + match.nombre + " " + match.apellidos;
                box.style.cssText = "display:block; background-color:#dcfce3; color:#166534; border:1px solid #166534;";
                btn.disabled = false;
            } else {
                idReal.value = "";
                box.innerText = "❌ No se encontró ningún cliente con ese celular.";
                box.style.cssText = "display:block; background-color:#fee2e2; color:#991b1b; border:1px solid #991b1b;";
                btn.disabled = true;
            }
        }

        function agregarFila(tipo) {
            const tbody = document.querySelector('#tabla_ventas tbody');
            const rowId = Date.now();
            let opciones = '<option value="">Seleccione...</option>';
            const catalogo = (tipo === 'producto') ? catProductos : catServicios;

            catalogo.forEach(item => {
                const id = item.id_producto || item.id_servicio;
                const precio = item.precio_venta || item.precio;
                const extra = item.stock ? ` (Stock: ${item.stock})` : '';
                opciones += `<option value="${id}" data-precio="${precio}">${item.nombre} - $${precio}${extra}</option>`;
            });

            const filaHtml = `
                <tr id="row_${rowId}">
                    <td style="text-transform: capitalize;"><b>${tipo}</b><input type="hidden" name="tipo[]" value="${tipo}"></td>
                    <td>
                        <select name="item_id[]" required onchange="actualizarPrecio(${rowId})" style="width:100%; padding:5px;">
                            ${opciones}
                        </select>
                    </td>
                    <td><input type="number" name="cantidad[]" value="1" min="1" oninput="calcularSubtotal(${rowId})" style="width:100%; padding:5px;" required></td>
                    <td>$<span id="precio_${rowId}">0.00</span></td>
                    <td>$<span id="subtotal_${rowId}" class="subtotal-fila">0.00</span></td>
                    <td><button type="button" class="btn-remove" onclick="removerFila(${rowId})"><i class="fa-solid fa-trash"></i></button></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', filaHtml);
        }

        function removerFila(id) {
            document.getElementById(`row_${id}`).remove();
            calcularTotalGeneral();
        }

        function actualizarPrecio(id) {
            const select = document.querySelector(`#row_${id} select`);
            const option = select.options[select.selectedIndex];
            const precio = option.value ? parseFloat(option.getAttribute('data-precio')) : 0;
            document.getElementById(`precio_${id}`).innerText = precio.toFixed(2);
            calcularSubtotal(id);
        }

        function calcularSubtotal(id) {
            const precio = parseFloat(document.getElementById(`precio_${id}`).innerText) || 0;
            const cant = parseInt(document.querySelector(`#row_${id} input[name="cantidad[]"]`).value) || 0;
            const subtotal = precio * cant;
            document.getElementById(`subtotal_${id}`).innerText = subtotal.toFixed(2);
            calcularTotalGeneral();
        }

        function calcularTotalGeneral() {
            let total = 0;
            document.querySelectorAll('.subtotal-fila').forEach(span => {
                total += parseFloat(span.innerText) || 0;
            });
            document.getElementById('txt_total').innerText = total.toFixed(2);
        }
    </script>
</body>
</html>