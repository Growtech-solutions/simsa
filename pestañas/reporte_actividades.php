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
            <h2>Reporte</h2>
            <form class="reporte_formulario" method="GET" action="">
                <label for="fecha_inicio">Fecha inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= $fecha_inicio ?>">

                <label for="fecha_fin">Fecha fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?= $fecha_fin ?>">

                <label for="ot">OT:</label>
                <input type="text" id="ot" name="ot" value="<?= $ot ?>">   

                <label for="folio">Folio:</label>
                <input type="text" id="folio" name="folio" value="<?= isset($_GET['folio']) ? $_GET['folio'] : '' ?>">

                <input type="hidden" name="pestaña" value="reporte_actividades">
                <input type="submit" value="Generar Reporte">
                <a onclick="generarPDF()" target="_blank">
                    <img src='../img/pdf.png' alt='Descargar PDF' style='width: 30px; height: auto;'>
                </a>
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
                            echo "<td>" . ($fila['nombre_pieza'] ?? $fila['pieza_tardia']) . "</td>";
                            echo "<td>" . ($fila['tiempo']) . "</td>";

                            // Obtener la actividad
                            $actividad_id = $fila["actividad"];
                            $sql_actividad = "SELECT descripcion,precio FROM precios WHERE id = '$actividad_id'";
                            $result_actividad = $conexion->query($sql_actividad);
                            $actividad = $result_actividad->fetch_assoc();
                            echo "<td>" . ($actividad["descripcion"] ?? "No especificado") . "</td>";
                            echo "<td>
                                <form method='post' action='' style='display:inline;'>
                                    <input type='number' name='nueva_cantidad' value='" . $fila['cantidad'] . "' min='0' style='width:60px;'>
                                    <input type='hidden' name='id_encargado_cantidad' value='" . $fila["encargado_id"] . "'>
                                    <input type='submit' name='actualizar_cantidad' value='Actualizar' style='font-size:10px;'>
                                </form>
                            </td>";
                            $valor_total=($fila['cantidad']*$actividad["precio"]);
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
