<?php
session_start();
if (!isset($_SESSION['rol_usuario']) || $_SESSION['rol_usuario'] !== 'administrador') {
    header("Location: login.php");
    exit;
}
require_once 'conexion.php';

// Obtener catálogos para JavaScript y HTML
$clientes = $conexion->query("SELECT id_cliente, nombre, apellidos, telefono FROM cliente WHERE estatus = 'Activo'");
$clientes_array = array();
while($c = $clientes->fetch_assoc()) { $clientes_array[] = $c; }

$barberos = $conexion->query("SELECT id_barbero, nombre, apellidos, turno FROM barbero WHERE estatus = 'Activo'");

$servicios = $conexion->query("SELECT id_servicio, nombre, precio, duracion_minutos FROM servicios");
$serv_array = array();
while($s = $servicios->fetch_assoc()) { $serv_array[] = $s; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Nueva Cita</title>
    <link rel="stylesheet" href="styles-formularios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .bloque-buscador { display: flex; gap: 10px; margin-bottom: 8px; }
        .bloque-buscador input { flex: 1; margin-bottom: 0 !important; }
        .btn-buscar-tel { background-color: #0f172a; color: white; border: none; padding: 0 15px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .notificacion-cliente { font-size: 14px; font-weight: bold; padding: 10px; border-radius: 6px; margin-bottom: 15px; text-align: center; display: none; }
        .tabla-items { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .tabla-items th, .tabla-items td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        .btn-add { background: #2563eb; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-remove { background: #dc2626; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
        .info-tiempo { text-align: right; font-size: 16px; font-weight: bold; margin: 10px 0; color: #d97706; }
        /* Forzar a que el cuerpo de la página permita el desplazamiento vertical */
html, body {
    height: auto !important;
    min-height: 100vh !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    /* ESTAS 3 LÍNEAS SON LAS QUE TE FALTARON: */
    display: flex !important;
    align-items: flex-start !important; 
    padding-top: 20px !important; 
}

/* Asegurar que el contenedor no limite su altura ni oculte contenido */
.contenedor-formulario {
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
    margin-top: 40px !important;
    margin-bottom: 40px !important;
    padding-bottom: 30px !important; /* Espacio extra al final para que respire el botón */
}
    </style>
</head>
<body>
    <div class="contenedor-formulario" style="max-width: 750px;">
        <h2><i class="fa-regular fa-calendar-plus"></i> Agendar Nueva Cita</h2>
        
        <form action="procesarCita.php" method="POST">
            
            <label>Buscar Cliente (Celular):</label>
            <div class="bloque-buscador">
                <input type="text" id="celular_buscar" placeholder="Ej. 6121234567" autocomplete="off">
                <button type="button" class="btn-buscar-tel" onclick="filtrarCliente()"><i class="fa-solid fa-magnifying-glass"></i> Buscar</button>
            </div>
            <div id="status_cliente" class="notificacion-cliente"></div>
            <input type="hidden" name="id_cliente" id="id_cliente_real">

            <div style="display: flex; gap: 15px; margin-top: 15px;">
                <div style="flex: 1;">
                    <label>Fecha:</label>
                    <input type="date" name="fecha" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                </div>
                <div style="flex: 1;">
                    <label>Hora de Inicio:</label>
                    <input type="time" name="hora" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                </div>
            </div>

            <label style="margin-top: 15px; display: block;">Barbero Asignado:</label>
            <select name="id_barbero" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; margin-bottom: 15px;">
                <option value="">Seleccione al barbero...</option>
                <?php while($b = $barberos->fetch_assoc()) { ?>
                    <option value="<?php echo $b['id_barbero']; ?>">
                        <?php echo htmlspecialchars($b['nombre'] . " " . $b['apellidos']) . " (Turno: " . $b['turno'] . ")"; ?>
                    </option>
                <?php } ?>
            </select>

            <hr>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <h3>Servicios Requeridos</h3>
                <button type="button" class="btn-add" onclick="agregarServicio()"><i class="fa-solid fa-plus"></i> Añadir Servicio</button>
            </div>

            <table class="tabla-items" id="tabla_servicios">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th style="width: 120px;">Duración (min)</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>

            <div class="info-tiempo">
                Tiempo Total Estimado: <span id="txt_minutos">0</span> minutos
            </div>

            <button class="boton-registrar" type="submit" id="btn_guardar" disabled>Confirmar y Agendar</button>
            <a href="paginaAdministrador.php?seccion=citas" style="text-decoration: none;">
                <button class="contenedor-formulario-salir" type="button">Cancelar</button>
            </a>
        </form>
    </div>

    <script>
        const mapaClientes = <?php echo json_encode($clientes_array); ?>;
        const catServicios = <?php echo json_encode($serv_array); ?>;

        function filtrarCliente() {
            const tel = document.getElementById('celular_buscar').value.trim();
            const box = document.getElementById('status_cliente');
            const idReal = document.getElementById('id_cliente_real');
            const btn = document.getElementById('btn_guardar');

            box.style.display = "block";
            const match = mapaClientes.find(c => c.telefono === tel);

            if (match) {
                idReal.value = match.id_cliente;
                box.innerText = "✅ Cliente: " + match.nombre + " " + match.apellidos;
                box.style.cssText = "display:block; background-color:#dcfce3; color:#166534; border:1px solid #166534;";
                btn.disabled = false;
            } else {
                idReal.value = "";
                box.innerText = "❌ No se encontró cliente.";
                box.style.cssText = "display:block; background-color:#fee2e2; color:#991b1b; border:1px solid #991b1b;";
                btn.disabled = true;
            }
        }

        function agregarServicio() {
            const tbody = document.querySelector('#tabla_servicios tbody');
            const rowId = Date.now();
            let opciones = '<option value="">Seleccione servicio...</option>';

            catServicios.forEach(s => {
                opciones += `<option value="${s.id_servicio}" data-tiempo="${s.duracion_minutos}">${s.nombre} ($${s.precio})</option>`;
            });

            const filaHtml = `
                <tr id="row_${rowId}">
                    <td>
                        <select name="servicios[]" required onchange="actualizarTiempo(${rowId})" style="width:100%; padding:5px;">
                            ${opciones}
                        </select>
                    </td>
                    <td style="text-align: center;"><span id="tiempo_${rowId}" class="tiempo-fila">0</span></td>
                    <td><button type="button" class="btn-remove" onclick="removerFila(${rowId})"><i class="fa-solid fa-trash"></i></button></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', filaHtml);
        }

        function removerFila(id) {
            document.getElementById(`row_${id}`).remove();
            calcularTiempoTotal();
        }

        function actualizarTiempo(id) {
            const select = document.querySelector(`#row_${id} select`);
            const option = select.options[select.selectedIndex];
            const tiempo = option.value ? parseInt(option.getAttribute('data-tiempo')) : 0;
            document.getElementById(`tiempo_${id}`).innerText = tiempo;
            calcularTiempoTotal();
        }

        function calcularTiempoTotal() {
            let totalMinutos = 0;
            document.querySelectorAll('.tiempo-fila').forEach(span => {
                totalMinutos += parseInt(span.innerText) || 0;
            });
            document.getElementById('txt_minutos').innerText = totalMinutos;
        }

        // Agregar la primera fila automáticamente
        window.onload = function() { agregarServicio(); };
    </script>
</body>
</html>