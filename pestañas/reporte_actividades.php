<?php

// Obtener las fechas del formulario o calcular la semana actual
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('monday this week'));
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d', strtotime('sunday this week'));

// Obtener otros filtros
$area = isset($_GET['area']) ? $_GET['area'] : '';
$ot = isset($_GET['ot']) ? $_GET['ot'] : '';

// Construir la consulta SQL
$sql = "SELECT 
            piezas.pieza AS nombre_pieza,
            piezas.area AS pieza_area,
            piezas.ot AS pieza_ot,
            ot.id_pedido AS ot_pedido_id,
            encargado.id as encargado_id,
            encargado.ot_tardia AS encargado_ot,
            encargado.cantidad,
            trabajadores.nombre,
            trabajadores.salario,
            trabajadores.apellidos,
            encargado.tiempo,
            encargado.pieza_tardia,
            encargado.actividad,
            encargado.fecha
        FROM piezas 
        RIGHT JOIN encargado ON piezas.id = encargado.id_pieza 
        LEFT JOIN ot ON COALESCE(piezas.ot, encargado.ot_tardia) = ot.ot
        LEFT JOIN transimex.trabajadores ON encargado.id_trabajador = trabajadores.id
        WHERE encargado.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

// Agregar filtros
if (!empty($ot)) {
    $sql .= " AND (piezas.ot LIKE '%$ot%' OR encargado.ot_tardia LIKE '%$ot%')";
}

$sql .= " ORDER BY encargado.fecha DESC, piezas.pieza ASC";

// Ejecutar la consulta
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte diario</title>
    <style>
        .principal {
            margin-top: 1.5%;
            margin-bottom: 3%;
        }
    </style>
</head>
<body id="encargado">
    <div class="principal">
        <div>
            <form method="GET" action="">
                <input type="hidden" name="pestaña" value="horas_tardias">
                <button type="submit" style="padding: 10px 20px;
                border: none;
                color: white;
                background-color: #007bff;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;">+ Registrar horas</button>
            </form>
            <h2 class="text-2xl font-bold text-blue-600 text-center">Reporte</h2>
            <form class="reporte_formulario" method="GET" action="" style="padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label for="fecha_inicio" style="display: block; margin-bottom: 5px; font-weight: 500;">Fecha inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $fecha_inicio ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div>
                        <label for="fecha_fin" style="display: block; margin-bottom: 5px; font-weight: 500;">Fecha fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="<?= $fecha_fin ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>

                    <div>
                        <label for="ot" style="display: block; margin-bottom: 5px; font-weight: 500;">OT:</label>
                        <input type="text" id="ot" name="ot" value="<?= $ot ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <div>
                        <label for="folio" style="display: block; margin-bottom: 5px; font-weight: 500;">Folio:</label>
                        <input type="text" id="folio" name="folio" value="<?= isset($_GET['folio']) ? $_GET['folio'] : '' ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="submit" value="Generar Reporte" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">
                    <a onclick="generarPDF()" style="cursor: pointer; display: flex; align-items: center;">
                        <img src='../img/pdf.png' alt='Descargar PDF' style='width: 30px; height: auto;'>
                    </a>
                    <input type="hidden" name="pestaña" value="reporte_actividades">
                </div>
            </form>
            
            <script>
            function generarPDF() {
                let fechaInicio = document.getElementById("fecha_inicio").value;
                let fechaFin = document.getElementById("fecha_fin").value;
                let ot = document.getElementById("ot").value;
                let folio = document.getElementById("folio").value;

                let url = `../php/pdf_actividades.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&ot=${ot}&folio=${folio}`;
                window.open(url, '_blank');
            }
            </script>

            <div class="reporte_tabla">
                <?php
                if ($resultado->num_rows > 0) {
                    echo "<table border='1'>";
                    echo "<tr>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>OT</th>
                            <th>Pieza</th>
                            <th>Tiempo</th>
                            <th>Actividad</th>
                            <th>Cantidad</th>
                            <th>Valor total</th>
                            <th>Costo</th>
                            <th>Eliminar</th>
                        </tr>";

                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        if ($fila['salario']<10){
                            $fila['salario']=300;
                        }
                        else if ($fila['salario']>1000){
                            $fila['salario']=1000;
                        }
                        $costo=((($fila['salario']/8)*$fila['tiempo'])*3);
                            echo "<td>" . $fila['fecha'] . "</td>";
                            echo "<td>" . $fila['nombre'] . " " . $fila['apellidos'] . "</td>";
                            echo "<td>" . ($fila['pieza_ot'] ?? $fila['encargado_ot']) . "</td>";
                            echo "<td>";
                            echo "<form method='post' action='' style='display:inline;'>";
                            echo "<select name='nueva_pieza' style='width:150px;'>";
                            
                            // Obtener todas las piezas de esa OT
                            $ot_actual = $fila['pieza_ot'] ?? $fila['encargado_ot'];
                            $sql_piezas_ot = "SELECT id, pieza FROM piezas WHERE ot = '$ot_actual'";
                            $result_piezas_ot = $conexion->query($sql_piezas_ot);
                            
                            $pieza_actual = $fila['nombre_pieza'] ?? $fila['pieza_tardia'];
                            if ($pieza_actual === null) {
                                echo "<option value='' selected>Sin pieza</option>";
                            }
                            while($pieza = $result_piezas_ot->fetch_assoc()) {
                                $selected = ($pieza['pieza'] == $pieza_actual) ? 'selected' : '';
                                echo "<option value='" . $pieza['id'] . "' $selected>" . $pieza['pieza'] . "</option>";
                            }
                            
                            echo "</select>";
                            echo "<input type='hidden' name='id_encargado_pieza' value='" . $fila["encargado_id"] . "'>";
                            echo "<input type='submit' name='actualizar_pieza' value='Actualizar' style='font-size:10px;'>";
                            echo "</form>";
                            echo "</td>";
                            echo "<td>" . ($fila['tiempo']) . "</td>";

                            // Obtener la actividad
                            $actividad_id = $fila["actividad"];
                            $sql_actividad = "SELECT descripcion,precio FROM precios WHERE id = '$actividad_id'";
                            $result_actividad = $conexion->query($sql_actividad);
                            $actividad = $result_actividad->fetch_assoc();
                            echo "<td>";
                            echo "<form method='post' action='' style='display:inline;'>";
                            echo "<select name='nueva_actividad' style='width:150px;'>";
                            
                            // Obtener todas las actividades disponibles
                            $sql_todas_actividades = "SELECT id, descripcion FROM precios where id_pedido=".$fila['ot_pedido_id'];
                            $result_todas_actividades = $conexion->query($sql_todas_actividades);
                            
                            while($act = $result_todas_actividades->fetch_assoc()) {
                                $selected = ($act['id'] == $actividad_id) ? 'selected' : '';
                                echo "<option value='" . $act['id'] . "' $selected>" . $act['descripcion'] . "</option>";
                            }
                            
                            echo "</select>";
                            echo "<input type='hidden' name='id_encargado_actividad' value='" . $fila["encargado_id"] . "'>";
                            echo "<input type='submit' name='actualizar_actividad' value='Actualizar' style='font-size:10px;'>";
                            echo "</form>";
                            echo "</td>";
                            echo "<td>
                                <form method='post' action='' style='display:inline;'>
                                    <input type='number' name='nueva_cantidad' value='" . $fila['cantidad'] . "' min='0' style='width:60px;'>
                                    <input type='hidden' name='id_encargado_cantidad' value='" . $fila["encargado_id"] . "'>
                                    <input type='submit' name='actualizar_cantidad' value='Actualizar' style='font-size:10px;'>
                                </form>
                            </td>";
                            $valor_total = ($fila['cantidad'] * ($actividad["precio"] ?? 0));
                            echo "<td>" . $valor_total . "</td>";
                            echo "<td>" . ($costo) . "</td>";
                            echo "<td>";
                            echo "<form method='post' action=''>";
                            echo "<input class='eliminarEncargado' type='submit' name='eliminarEncargado' value='Eliminar'>";
                            echo "<input type='hidden' name='id_encargado' value='" . $fila["encargado_id"] . "'>"; 
                            echo "</form>";
                            echo "</td>";                        
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No se encontraron piezas.";
                }

                // Cerrar la conexión a la base de datos
                $conexion->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>
