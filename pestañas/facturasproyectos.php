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
            <form method="get" action="">
            <input type="hidden" name="pestaña" value="alta_proyectos">
            <button type="submit" style="padding: 10px 20px; border: none; color: white; background-color: #007bff; border-radius: 4px; cursor: pointer; font-size: 14px;">+ Nuevo proyecto</button>
        </form>
            <div class="centrado">
                
                <h2 class="text-2xl font-bold text-blue-600">Reporte de OT</h2>
                <br>
                <form class="reporte_formulario" method="GET" action="">
                    <label for="ot">OT:</label>
                    <input class="formulario_reporte_ot" type="text" id="ot" name="ot" placeholder="Buscar por OT">

                    <input type="hidden" name="pestaña" value="facturasproyectos">

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

                    <label for="responsable">Responsable:</label>
                    <select class="formulario_reporte_ot" name="responsable" id="responsable">
                        <option>Seleccione responsable</option>
                        <?php
                        $sql_responsables = "SELECT DISTINCT responsable FROM ot";
                        $result_responsables = $conexion->query($sql_responsables);

                        if ($result_responsables->num_rows > 0) {
                            while ($fila_responsable = $result_responsables->fetch_assoc()) {
                                echo '<option value="' . $fila_responsable['responsable'] . '">' . $fila_responsable['responsable'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay responsables disponibles</option>';
                        }
                        ?>
                    </select>

                    <label for="cliente">Cliente:</label>
                    <input class="formulario_reporte_ot" type="text" id="cliente" name="cliente" placeholder="Cliente" >

                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
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

                function actualizarEstado(ot, estado) {
                    const formData = new FormData();
                    formData.append("ot", ot);
                    formData.append("estado", estado);
                
                    fetch("../php/actualizar_estado.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Opcional: muestra el resultado en la consola
                        alert("Estado actualizado correctamente");
                         window.location.href = window.location.href;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Ocurrió un error al actualizar el estado");
                    });
                }

            </script>';
            
            // Este código debe estar incluido en tu archivo PHP para manejar la actualización
            if (isset($_POST['ot']) && isset($_POST['estado'])) {
                $ot = intval($_POST['ot']);
                $estado = $_POST['estado'];
            
                // Actualizar el estado
                $sql = "UPDATE ot SET estado = ? WHERE ot = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("si", $estado, $ot);
                if ($stmt->execute()) {
                    echo "Estado actualizado correctamente";
                } else {
                    echo "Error: " . $conexion->error;
                }
                $stmt->close();
                exit; // Finaliza el script para evitar mostrar contenido extra
            }

            $sql_ot = "SELECT ot.*, pedido.cliente as cliente FROM ot LEFT JOIN pedido ON ot.id_pedido = pedido.id WHERE 1=1";
             
            // Verificar si se enviaron los datos del formulario
            if (isset($_GET['ot'])) {
                $ot = $conexion->real_escape_string($_GET['ot']);
                $sql_ot .= " AND ot LIKE '%$ot%'";
            }

        

            if (isset($_GET['fecha_inicio'])) {
                $fecha_inicio = $_GET['fecha_inicio'];
                $sql_ot .= " AND ot.fecha_alta >= '$fecha_inicio'";
            }

            if (isset($_GET['fecha_final'])) {
                $fecha_final = $_GET['fecha_final'];
                $sql_ot .= " AND ot.fecha_alta <= '$fecha_final'";
            }

            if (isset($_GET['responsable']) && $_GET['responsable'] !== 'Seleccione responsable') {
                $responsable = $conexion->real_escape_string($_GET['responsable']);
                $sql_ot .= " AND responsable = '$responsable'";
            }

            if (isset($_GET['cliente']) && $_GET['cliente'] !== 'Seleccione cliente') {
                $cliente = $conexion->real_escape_string($_GET['cliente']);
                $sql_ot .= " AND cliente LIKE '%" . $cliente . "%'";
            }

            if (isset($_GET['buscar'])) {
                $sql_ot .= " ORDER BY ot DESC";
                $result_ot = $conexion->query($sql_ot);
            

                    // Query to calculate the sum of pedidos
                    $sql_pedido_pendiente = "SELECT valor as total_pedidos FROM ot WHERE ot.fecha_alta >= '$fecha_inicio' AND ot.fecha_alta <= '$fecha_final'";
                    $resultado_pedidos_pendientes = $conexion->query($sql_pedido_pendiente);
                    
                    // Query to calculate the sum of facturas
                    $sql_factura_pendiente = "SELECT SUM(facturas.valor_pesos) as total_facturas FROM facturas left join pedido on facturas.id_pedido = pedido.id left join ot on pedido.id = ot.id_pedido WHERE ot.fecha_alta >= '$fecha_inicio' AND ot.fecha_alta <= '$fecha_final'";
                    $resultado_facturas_pendientes = $conexion->query($sql_factura_pendiente);
                    
                    // Fetch the results as associative arrays
                    $total_pedidos = $resultado_pedidos_pendientes->fetch_assoc()['total_pedidos'];
                    $total_facturas = $resultado_facturas_pendientes->fetch_assoc()['total_facturas'];
                    
                    // Calculate the difference
                    $resultado_valor_futuro = $total_pedidos - $total_facturas;
                    
                    // Output the result
                    // echo "Pedidos pendiente = " . $resultado_valor_futuro;
            
            if ($result_ot && $result_ot->num_rows > 0) {
                echo "<table>";
                while ($fila_ot = $result_ot->fetch_assoc()) {
                    $compras = $fila_ot['compras'] ?? 0;
                    $ot_cmo=$fila_ot['ot'];
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
                        WHERE 
                            piezas.ot = '$ot_cmo' OR encargado.ot_tardia = '$ot_cmo';";
                        
                        $result_mano_de_obra = $conexion->query($sql_mano_de_obra);
                        $costo_mano_de_obra = 0;
                        $costo_mano=0;
                        if ($result_mano_de_obra && $result_mano_de_obra->num_rows > 0) {
                            while ($row_cmo = $result_mano_de_obra->fetch_assoc()) {
                                // Obtiene los valores del query
                                $salario_actual = isset($row_cmo['salario_actual']) && is_numeric($row_cmo['salario_actual']) ? $row_cmo['salario_actual'] : 280; // Salario por día (valor predeterminado)
                                $tiempo = isset($row_cmo['tiempo']) && is_numeric($row_cmo['tiempo']) ? $row_cmo['tiempo'] : 0; // Tiempo trabajado (valor predeterminado)
       
                                $costo_mano = ($salario_actual / 8) * $tiempo;
                                $costo_mano_de_obra += $costo_mano*3;
                            }
                                
                        }else {
                            $costo_mano_de_obra = 0;
                            $costo_mano=0;
                        }

                        $pedidos_ot="SELECT valor FROM ot WHERE ot = '$ot_cmo'";
                        $result_pedidos_ot = $conexion->query($pedidos_ot);
                        $total_pedidos_ot = $result_pedidos_ot->fetch_assoc()['valor'];

                        $facturas_ot="SELECT SUM(facturas.valor_pesos) as total_facturas FROM facturas 
                        left join pedido on facturas.id_pedido = pedido.id
                        WHERE facturas.ot = '$ot_cmo'";
                        $result_facturas_ot = $conexion->query($facturas_ot);
                        $total_facturas_ot = $result_facturas_ot->fetch_assoc()['total_facturas'];

                        $compras_ot="SELECT SUM(cantidad * 
                                    CASE 
                                        WHEN moneda != 'MXN' THEN precio_unitario * 22 
                                        ELSE precio_unitario 
                                    END) AS total_compras 
                                    FROM compras 
                                    WHERE ot = '$ot_cmo'";
                        $result_compras_ot = $conexion->query($compras_ot);
                        $total_compras_ot = $result_compras_ot->fetch_assoc()['total_compras'];
                        if ($total_pedidos_ot > 0) {
                            $porcentaje_facturado = ($total_facturas_ot / $total_pedidos_ot) * 100;
                        } else {
                            $porcentaje_facturado = 0;
                        }
                    
                    echo "<tr style='background-color: #f2f2f2;'>";
                        echo "<td>" . $fila_ot['fecha_alta'] . "</td>";
                        echo "<td class='liga'><a href='general.php?pestaña=evaluacion_ot&ot=" . urlencode($fila_ot['ot']) . "' target='_blank'>" . $fila_ot['ot'] . "</a></td>";
                        echo "<td>" . $fila_ot['cliente'] . "</td>";
                        echo "<td>" . $fila_ot['descripcion'] . "</td>";
                        echo "<td>" . $fila_ot['responsable'] . "</td>";
                        echo "<td> Valor $" . $total_pedidos_ot . "</td>";
                        echo "<td> Gastos $" . $total_compras_ot + $costo_mano_de_obra . "</td>";
                        echo "<td>". number_format($porcentaje_facturado, 2)."%</td>";
                        echo "<td>";
                            echo "<select
                                    name='estado' 
                                    class='entrada' 
                                    onchange='actualizarEstado(" . $fila_ot['ot'] . ", this.value)'
                                >";
                            echo "<option value='Activo'" . ($fila_ot['estado'] == 'Activo' ? " selected" : "") . ">Activo</option>";
                            echo "<option value='Perdido por monto'" . ($fila_ot['estado'] == 'Perdido por monto' ? " selected" : "") . ">Perdido por monto</option>";
                            echo "<option value='Perdido por tiempo'" . ($fila_ot['estado'] == 'Perdido por tiempo' ? " selected" : "") . ">Perdido por tiempo</option>";
                            echo "<option value='Cancelado'" . ($fila_ot['estado'] == 'Cancelado' ? " selected" : "") . ">Cancelado</option>";
                            echo "</select>";
                        echo "</td>";
                        echo "<td class='toggle-btn' onclick='toggleTabla(" . $fila_ot['ot'] . ")'>▼</td>";
                    echo "</tr>";

                    echo "<tr>
                            <td colspan='10'>
                                <div id='tabla_oculta_" . $fila_ot['ot'] . "' class='tabla_oculta oculto'>";

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
                                        facturas.id_pedido as pedido_id,
                                        facturas.ot as ot,
                                        pedido.descripcion as pedido
                                    FROM 
                                        facturas
                                    LEFT JOIN 
                                        pedido ON facturas.id_pedido = pedido.id
                                    WHERE facturas.ot = '" . $fila_ot['ot'] . "'";
                    $resultado_facturas = $conexion->query($sql_facturas);
                    $total_facturas = 0;
                    while ($fila_facturas = $resultado_facturas->fetch_assoc()) {
                        $total_facturas += $fila_facturas['factura_valor'];
                        echo "<tr>";
                        echo "<td><a href='../documentos/finanzas/ot/{$fila_ot['ot']}/{$fila_facturas['factura_folio']}.zip' target='_blank'>" . $fila_facturas['factura_folio'] . "</a></td>";
                        echo "<td><a href='../documentos/finanzas/ot/{$fila_ot['ot']}/{$fila_facturas['descripcion']}.pdf' target='_blank'>" . $fila_facturas['descripcion'] . "</a></td>";
                        echo "<td>" . $fila_facturas['pedido'] . "</td>";
                        echo "<td class='dinero'>$" . number_format($fila_facturas['factura_valor'], 2) . "</td>";
                        echo "</tr>";
                    }

                   
                    echo "</table>";
                    echo "<div class='dinero total'><strong>Total Facturas: </strong>$" . number_format($total_facturas, 2) . "</div>";
                    echo "<script>document.getElementById('porcentaje_facturado_" . $fila_ot['ot'] . "').innerHTML = 'Facturacion: " . number_format($porcentaje_facturado, 2) . "%';</script>";
                    echo "</div>
                            </td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No se encontraron resultados para los criterios de búsqueda proporcionados.</p>";
            }
        }
        ?>
            
        </section>
    </div>

    <!-- Modal para mostrar la imagen -->
    <div id="modal_imagen" class="modal">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <img class="modal-content" id="imagen_pedido">
    </div>
</body>
</html>
