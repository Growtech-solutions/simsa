<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facturas por proyectos</title>
    <style>
        .centrado {
            padding-top: 1rem;
            text-align: center;
        }
        .formulario_reporte_ot {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        table {
            border-collapse: collapse;
            width: 95%;
            margin: auto;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .oculto {
            display: none;
        }
        .dinero {
            text-align: right;
        }
        .total {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        /* Estilos para el modal */
        .modal {
            display: none; /* Ocultar modal por defecto */
            position: fixed; /* Posición fija */
            z-index: 1; /* Sobre todo */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Habilitar el desplazamiento */
            background-color: rgb(0,0,0); /* Fondo negro */
            background-color: rgba(0,0,0,0.9); /* Fondo negro con opacidad */
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 800px;
            max-height: 80%;
        }
        .close {
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 25px;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }
        .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
        
    </style>
</head>
<?php 
$header_loc= $_GET['header_loc'];
$pestaña= $_GET['pestaña'];  
?>

<body id="facturaspedidos">
    <div class="principal">
        <section class="mensaje">
            <div class="centrado">
                
                <h2>Reporte de Pedidos</h2>
                <form class="reporte_formulario" method="GET" action="">
                    <label for="pedido">Pedido:</label>
                    <input class="formulario_reporte_ot" type="text" id="pedido" name="pedido" placeholder="Buscar por Pedido">

                    <input type="hidden" name="pestaña" value="facturaspedidos">

                    <label for="facturacion">Estado:</label>
                    <select class="formulario_reporte_ot" name="facturacion" id="facturacion">
                        <option>Estado facturación</option>
                        <option value="terminado">Terminado</option>
                        <option value="pendiente">Pendiente</option>
                    </select>

                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo date('Y-m-d', strtotime('-2 years')); ?>">

                    <label for="fecha_final">Fecha Final:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_final" name="fecha_final" value="<?php echo date('Y-m-d'); ?>">

                    <label for="cliente">Cliente:</label>
                    <input class="formulario_reporte_ot" type="text" id="cliente" name="cliente" placeholder="Cliente" >

                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                    <input type="hidden" name="pestana" value="facturaspedidos">
                    <input type="submit" name="buscar" value="Buscar">
                </form>
            </div>
            
            <?php
            echo '<script>
                function toggleTabla(id) {
                    var tablas_ocultas = document.querySelectorAll(".tabla_oculta");
                    for (var i = 0; i < tablas_ocultas.length; i++) {
                        if (tablas_ocultas[i].id === "tabla_oculta_" + id) {
                            tablas_ocultas[i].classList.toggle("oculto");
                        } else {
                            tablas_ocultas[i].classList.add("oculto");
                        }
                    }
                }

                function mostrarImagen(imagenUrl) {
                    document.getElementById("imagen_pedido").src = imagenUrl;
                    document.getElementById("modal_imagen").style.display = "block";
                }

                function cerrarModal() {
                    document.getElementById("modal_imagen").style.display = "none";
                }
            </script>';
            

            $sql_pedido = 
            "SELECT 
                pedido.*, 
                pedido.valor_pesos as total_pedido, 
                SUM(facturas.valor_pesos) as total_facturas 
            FROM pedido 
            LEFT JOIN facturas ON pedido.id = facturas.id_pedido 
            WHERE 1=1 ";
        
        if (isset($_GET['pedido'])) {
            $pedido = $conexion->real_escape_string($_GET['pedido']);
            $sql_pedido .= " AND pedido.id LIKE '%$pedido%'";
        }
        
        if (isset($_GET['fecha_inicio'])) {
            $fecha_inicio = $_GET['fecha_inicio'];
            $sql_pedido .= " AND pedido.fecha_alta >= '$fecha_inicio'";
        }
        
        if (isset($_GET['fecha_final'])) {
            $fecha_final = $_GET['fecha_final'];
            $sql_pedido .= " AND pedido.fecha_alta <= '$fecha_final'";
        }
        
        if (isset($_GET['cliente']) && $_GET['cliente'] !== 'Seleccione cliente') {
            $cliente = $conexion->real_escape_string($_GET['cliente']);
            $sql_pedido .= " AND pedido.cliente LIKE '%" . $cliente . "%'";
        }
        
        // Aquí agrupamos por pedido.id para que funcione el SUM
        $sql_pedido .= " GROUP BY pedido.id";
        
        // Y aquí aplicamos HAVING según el estado
        if (isset($_GET['facturacion']) && $_GET['facturacion'] !== 'Estado facturación') {
            $estado = $_GET['facturacion'];
            if ($estado == 'terminado') {
                $sql_pedido .= " HAVING total_facturas = total_pedido";
            }
            if ($estado == 'pendiente') {
                $sql_pedido .= " HAVING total_facturas != total_pedido";
            }
        }
        
        // Finalmente el orden
        if (isset($_GET['buscar'])) {
            $sql_pedido .= " ORDER BY pedido.id DESC";
            $result_pedido = $conexion->query($sql_pedido);
        }
        
            if ($result_pedido && $result_pedido->num_rows > 0) {
                echo "<table>";
                while ($fila_pedido = $result_pedido->fetch_assoc()) {
                    $compras = $fila_pedido['compras'] ?? 0;
                    $pedido_cmo = $fila_pedido['id'];
                     $sql_mano_de_obra ="
                        SELECT 
                            trabajadores.id AS trabajador_id,
                            encargado.tiempo,
                            encargado.fecha,
                            (
                                SELECT hs.valor_actual
                                FROM transimex.historial_salarios hs
                                WHERE hs.id_trabajador = trabajadores.id
                                  AND hs.fecha_cambio <= encargado.fecha
                                ORDER BY hs.fecha_cambio DESC
                                LIMIT 1
                            ) AS salario_actual
                        FROM 
                            encargado
                        LEFT JOIN 
                            piezas ON encargado.id_pieza = piezas.id
                        LEFT JOIN
                            transimex.trabajadores ON encargado.id = trabajadores.id
                        LEFT JOIN
                            ot on piezas.ot = ot.ot
                        left join 
                            pedido on ot.id_pedido = pedido.id
                        WHERE 
                            pedido.id = '$pedido_cmo'";
                        
                        $result_mano_de_obra = $conexion->query($sql_mano_de_obra);
                        $costo_mano_de_obra = 0;
                        if ($result_mano_de_obra && $result_mano_de_obra->num_rows > 0) {
                            while ($row_cmo = $result_mano_de_obra->fetch_assoc()) {
                                $salario_actual = isset($row_cmo['salario_actual']) && is_numeric($row_cmo['salario_actual']) ? $row_cmo['salario_actual'] : 280;
                                $tiempo = isset($row_cmo['tiempo']) && is_numeric($row_cmo['tiempo']) ? $row_cmo['tiempo'] : 0;
                                $costo_mano = ($salario_actual / 8) * $tiempo;
                                $costo_mano_de_obra += $costo_mano * 3;
                            }
                        }
                        

                    echo "<tr style='background-color: #f2f2f2;'>";
                        echo "<td>" . $fila_pedido['fecha_alta'] . "</td>";
                        echo "<td>" . $fila_pedido['cliente'] . "</td>";
                        echo "<td>" . $fila_pedido['descripcion'] . "</td>";
                        echo "<td> Valor $" . $fila_pedido['total_pedido'] . "</td>";
                        echo "<td> Gastos $" . $costo_mano_de_obra . "</td>";
                        if ($fila_pedido['total_pedido'] > 0) {
                            $porcentaje = ($fila_pedido['total_facturas'] / $fila_pedido['total_pedido']) * 100;
                            echo "<td>" . round($porcentaje, 2) . " %</td>";
                        } else {
                            echo "<td>0 %</td>";
                        }
                        echo "<td class='toggle-btn' onclick='toggleTabla(" . $fila_pedido['id'] . ")'>▼</td>";
                    echo "</tr>";

                    echo "<tr>
                            <td colspan='7'>
                                <div id='tabla_oculta_" . $fila_pedido['id'] . "' class='tabla_oculta oculto'>";

                    echo "<table border='1'>
                            <tr>
                                <th>Factura</th>
                                <th>Descripción</th>
                                <th>Pedido</th>
                                <th>Valor</th>
                            </tr>";
                    $sql_facturas = "SELECT 
                                        facturas.id as factura_id,
                                        facturas.descripcion as descripcion,
                                        facturas.folio as factura_folio,
                                        facturas.valor_pesos as factura_valor,
                                        facturas.id_pedido as pedido_id
                                    FROM 
                                        facturas
                                    LEFT JOIN 
                                        pedido ON facturas.id_pedido = pedido.id
                                    WHERE facturas.id_pedido = '" . $fila_pedido['id'] . "'";
                    $resultado_facturas = $conexion->query($sql_facturas);
                    $total_facturas = 0;
                    while ($fila_facturas = $resultado_facturas->fetch_assoc()) {
                        $total_facturas += $fila_facturas['factura_valor'];
                        echo "<tr>";
                        echo "<td><a href='../documentos/finanzas/pedido/{$fila_pedido['id']}/{$fila_facturas['factura_folio']}.zip' target='_blank'>" . $fila_facturas['factura_folio'] . "</a></td>";
                        echo "<td>" . $fila_facturas['descripcion'] . "</td>";
                        echo "<td>" . $fila_facturas['pedido_id'] . "</td>";
                        echo "<td>" . $fila_facturas['factura_valor'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "</div></td></tr>";
                }
                echo "</table>";
            }
            ?>
        </section>
    </div>
</body>

</html>
