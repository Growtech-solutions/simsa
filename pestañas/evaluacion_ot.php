<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reporte de OT</title>
    <style>
        .documento_ot {
            width: 80%;
            padding: 20px;
            border: 1px solid black;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
        }
        .logo_ot {
            height: 8rem;
        }
        .text-color {
          color: rgb(29, 20, 62);
        }
        .header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            align-items: center;
            text-align: center;
            justify-content: space-between;
        }
        .header h2 {
            flex: 1;
            text-align: center;
            margin: 0;
            font-size: 28px;
        }
        .ot {
            font-size: 28px;
            color: rgb(29, 20, 62);
        }
        .detalles {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            margin-top: 20px;
            gap: 20px; /* Optional: Adjust gap between grid items */
        }
        .resumen {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
            gap: 20px; /* Optional: Adjust gap between grid items */
            text-align: center;
        }
        .resumen2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
            gap: 20px; /* Optional: Adjust gap between grid items */
            text-align: center;
        }
        .detalles p, .resumen p {
            margin: 5px 0;
        }
        .centrado {
            text-align: center;
        }
        .linea-gris {
            width: 98%;
            border-top: 1px solid gray;
            margin: 20px auto;
        }
        .resumen p:nth-child(1),
        .resumen p:nth-child(2),
        .resumen p:nth-child(4),
        .resumen p:nth-child(6) {
            grid-column: 1 / 2;
        }
        .resumen p:nth-child(3),
        .resumen p:nth-child(5) {
            grid-column: 2 / 3;
        }
         .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
    </style>
</head>
<body id="gerencia">

<div class="principal">
    <section class="mensaje">
        <div class="centrado">
            <h2>Reporte de OT</h2>
            <form method="GET" action="">
                <label for="ot">Ingrese la OT:</label>
                <input type="text" id="ot" name="ot" required>
                <input type="hidden" name="pestaña" value="evaluacion_ot">
                <input type="submit" value="Buscar">
            </form>
        </div>
        <?php
        if (isset($_GET['ot'])) {
            $ot = $conexion->real_escape_string($_GET['ot']);
            $sql_ot = "SELECT ot.*, pedido.*, ot.descripcion as ot_desc FROM ot LEFT JOIN pedido ON pedido.id = ot.id_pedido WHERE ot.ot = '$ot'";
            $result_ot = $conexion->query($sql_ot);
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
                    transimex.trabajadores ON encargado.id_trabajador = trabajadores.id
                WHERE 
                    piezas.ot = $ot OR encargado.ot_tardia = $ot;
                ";
                $result_mano_de_obra = $conexion->query($sql_mano_de_obra);
                $costo_mano_de_obra = 0;
                if ($result_mano_de_obra && $result_mano_de_obra->num_rows > 0) {
                    while ($row = $result_mano_de_obra->fetch_assoc()) {
                        // Obtiene los valores del query
                        $salario_actual = $row['salario_actual'] !== null ? $row['salario_actual'] : 280; // Salario por d��a
                        $tiempo = $row['tiempo'] !== null ? $row['tiempo'] : 0; // Tiempo trabajado
                        $costo_mano = ($salario_actual / 8) * $tiempo;
                        $costo_mano_de_obra += $costo_mano*3;
                    }
                        
                } else {
                    $costo_mano_de_obra = 0;
                }

            $sql_tiempo = "SELECT SUM(tiempo) FROM encargado LEFT JOIN piezas ON encargado.id_pieza = piezas.id WHERE piezas.ot = $ot OR encargado.ot_tardia = $ot;";
                $result_tiempo = $conexion->query($sql_tiempo);
                $tiempo = 0;
                if ($result_tiempo && $result_tiempo->num_rows > 0) {
                    $row = $result_tiempo->fetch_assoc();
                    $tiempo = $row['SUM(tiempo)'];
                }
            $sql_pedidos = "SELECT valor 
                            FROM ot 
                           
                            WHERE ot = '$ot';";
            $result_pedidos = $conexion->query($sql_pedidos);
            $total_pedidos = 0;
            if ($result_pedidos && $result_pedidos->num_rows > 0) {
                $row = $result_pedidos->fetch_assoc();
                $total_pedidos = $row['valor']; 
            }

            $sql_facturas = "SELECT SUM(facturas.valor_pesos) AS total_facturas 
            FROM facturas 
            LEFT JOIN pedido ON facturas.id_pedido = pedido.id 
            WHERE facturas.ot = '$ot';";
            $result_ot = $conexion->query($sql_facturas);
            $total_facturas = 0;
            if ($result_ot->num_rows > 0) {
                $row = $result_ot->fetch_assoc();
                $total_facturas = $row['total_facturas'];
            }
            
            $porcentaje_pendiente = ($total_pedidos > 0) ? (($total_facturas) / $total_pedidos) * 100 : 0;

            $sql_compras = "SELECT SUM(compras.cantidad * compras.precio_unitario * 
                CASE 
                    WHEN compras.moneda = 'MXN' THEN 1 
                    ELSE orden_compra.tipo_cambio 
                END) AS total_pesos 
                FROM compras 
                LEFT JOIN orden_compra ON compras.id_oc = orden_compra.id 
                WHERE compras.ot = $ot";
            $result_ot = $conexion->query($sql_compras);
            $compras = 0;
            if ($result_ot->num_rows > 0) {
                $row = $result_ot->fetch_assoc();
                $compras = $row['total_pesos'];
            }
            if ($tiempo==0){
                $ev = ($total_pedidos-$compras);
            }
            else{
                $ev = ($total_pedidos-$compras)/$costo_mano_de_obra;
            }
            $result_ot = $conexion->query($sql_ot);
            if ($result_ot->num_rows > 0) {
                $row = $result_ot->fetch_assoc();
                echo "<div class='documento_ot'>";
                echo "<div class='header'>";
                    echo "<img class='logo_ot' src='../img/logo.png' alt='Logo'>";
                    echo "<h2 class='text-color'> " . ucwords($row["ot_desc"]) . "</h2>";
                    // Botón para abrir el modal
                    echo "<h2 class='text-color'>";
                    echo "<a href='#' id='editOtBtn' style='text-decoration:none;color:inherit;' title='Editar OT'>" . htmlspecialchars($row["ot"]) . " ✏️</a>";
                    echo "</h2>";
                    
                    // Modal HTML
                    echo '
                    <div id="editOtModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.4);">
                        <div style="background:#fff;margin:10% auto;padding:30px;border-radius:10px;max-width:400px;box-shadow:0 2px 10px rgba(0,0,0,0.2);position:relative;">
                            <span id="closeModal" style="position:absolute;top:10px;right:20px;font-size:28px;cursor:pointer;">&times;</span>
                            <h3 style="margin-top:0;color:#1d143e;">Editar OT</h3>
                            <form method="POST" action="">
                                <input type="hidden" name="edit_ot" value="1">
                                <input type="hidden" name="ot" value="' . htmlspecialchars($row["ot"]) . '">
                                <label for="descripcion" style="display:block;margin-bottom:8px;">Descripción:</label>
                                <input type="text" id="descripcion" name="descripcion" value="' . htmlspecialchars($row["ot_desc"]) . '" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                                <label for="responsable" style="display:block;margin-bottom:8px;">Responsable:</label>
                                <input type="text" id="responsable" name="responsable" value="' . htmlspecialchars($row["responsable"]) . '" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                                <label for="valor" style="display:block;margin-bottom:8px;">Valor:</label>
                                <input type="number" id="valor" name="valor" step="0.01" value="' . htmlspecialchars($row["valor"]) . '" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                                <label for="usuario" style="display:block;margin-bottom:8px;">Usuario:</label>
                                <input type="text" id="usuario" name="usuario" value="' . htmlspecialchars(isset($row["usuario"]) ? $row["usuario"] : '') . '" style="width:100%;padding:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;">
                                <button type="submit" style="background:#1d143e;color:#fff;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Guardar</button>
                            </form>
                        </div>
                    </div>';
                    echo '
                    <script>
                        document.getElementById("editOtBtn").onclick = function(e) {
                            e.preventDefault();
                            document.getElementById("editOtModal").style.display = "block";
                        };
                        document.getElementById("closeModal").onclick = function() {
                            document.getElementById("editOtModal").style.display = "none";
                        };
                        window.onclick = function(event) {
                            var modal = document.getElementById("editOtModal");
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                        };
                    </script>';

                // Procesar edición si se envió el formulario
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_ot']) && $_POST['edit_ot'] == '1') {
                    $ot_edit = $conexion->real_escape_string($_POST['ot']);
                    $desc_edit = $conexion->real_escape_string($_POST['descripcion']);
                    $resp_edit = $conexion->real_escape_string($_POST['responsable']);
                    $valor_edit = $conexion->real_escape_string($_POST['valor']);
                    $usuario_edit = $conexion->real_escape_string($_POST['usuario']);
                    $update_sql = "UPDATE ot SET descripcion='$desc_edit', responsable='$resp_edit', valor='$valor_edit', usuario='$usuario_edit' WHERE ot='$ot_edit'";
                    if ($conexion->query($update_sql)) {
                        echo "<div style='background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin:10px 0;text-align:center;'>OT actualizada correctamente.</div>";
                        // Recargar para mostrar cambios
                        echo "<script>window.location.href=window.location.href;</script>";
                    } else {
                        echo "<div style='background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin:10px 0;text-align:center;'>Error al actualizar la OT.</div>";
                    }
                }
                echo "</div>";
                echo "<div class='linea-gris'></div>";

                echo "<div class='detalles'>";
                    echo "<p><strong>Cliente:</strong> " . $row["cliente"] . "</p>";
                    echo "<p><strong>Alta:</strong> " . $row["fecha_alta"] . "</p>";
                    echo "<p><strong>Descripción:</strong> " . $row["descripcion"] . "</p>";
                    echo "<p><strong>Responsable:</strong> " . $row["responsable"] . "</p>";
                echo "</div>";
                echo "<div class='linea-gris'></div>";

                echo "<div class='resumen'>";
                    echo "<p><strong>Horas trabajadas:</strong> " . $tiempo . "</p>";
                    echo "<p><strong>Mano de obra:</strong> $" . number_format($costo_mano_de_obra, 2) . "</p>";
    
                    echo "<p><strong>Valor:</strong> $" . number_format($total_pedidos, 2) . "</p>";
                    echo "<p><strong>Compras:</strong> $" . number_format($compras, 2) . "</p>";
                    echo "<p><strong>Facturas:</strong> $" . number_format($total_facturas, 2) . "</p>";
                echo "</div>";
                echo "<div class='linea-gris'></div>";
                    echo "<div class='resumen2'>";
                        echo "<p><strong>Facturado:</strong> " . number_format($porcentaje_pendiente, 2) . "%</p>";
                        echo "<p><strong>EV:</strong> " . $ev . "</p>";
                    echo "</div>";
                echo "</div>";
            } else {
                echo "<p class='centrado'>No se encontraron resultados para la OT ingresada.</p>";
            }
        }
        $conexion->close();
        ?>
    </section>
</div>
</body>
</html>