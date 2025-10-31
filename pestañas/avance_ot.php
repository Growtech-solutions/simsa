<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Avance de OT</title>
    <style>
    .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
    </style>
</head>
<body>
<div class="principal">
    <section class="mensaje">
        <div class="centrado">
            <h2>Reporte de OT</h2>
            <form method="GET" action="">
                <label for="ot">Ingrese la OT:</label>
                <input type="text" id="ot" name="ot" required>
                <input type="hidden" name="pestaña" value="avance_ot">
                <label for="estado">Seleccione el estado:</label>
                <select name="estado">
                    <option value="todo">Todo</option>
                    <option value="terminado">Terminado</option>
                    <option value="pendiente">Pendiente</option>
                </select>
                <input type="submit" value="Buscar">
            </form>
        </div>
        <?php
        // Verificar si se enviaron los datos del formulario
        if (isset($_GET['ot'])) {
            $ot = $conexion->real_escape_string($_GET['ot']); // Escaping user input for security
            $estado = isset($_GET['estado']) ? $_GET['estado'] : 'todo';

            // Query for OT
            $sql_ot = "SELECT * FROM ot WHERE ot='$ot'";
            $result_ot = $conexion->query($sql_ot);

            if ($result_ot && $result_ot->num_rows > 0) {
                $fila_ot = $result_ot->fetch_assoc();
                echo "<div class='centrado'> \n Avance de la orden: $ot</div>";
            } else {
                echo "<div class='centrado'> \n No se encontró la orden de trabajo especificada.</div>";
            }

            // Query for Maquinado
            if ($estado == "todo") {
                $sql_maquinado = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND area = 'Maquinado' GROUP BY piezas.id;";
                $result_maquinado = $conexion->query($sql_maquinado);

                // Query for Paileria
                $sql_paileria = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND area = 'Paileria' GROUP BY piezas.id;";
                $result_paileria = $conexion->query($sql_paileria);
                
                // Query for Compras
                $sql_compras = "SELECT c.id, c.ot, c.descripcion, c.id_oc, orden_compra.fecha_solicitud, orden_compra.llegada_estimada, orden_compra.firma_llegada FROM compras c LEFT JOIN orden_compra ON c.id_oc = orden_compra.id WHERE ot='$ot' ORDER BY orden_compra.fecha_solicitud DESC";
                $result_compras = $conexion->query($sql_compras);
            }
            if ($estado == "terminado") {
                $sql_maquinado = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND fechafinal is not null AND area = 'Maquinado' GROUP BY piezas.id;";
                $result_maquinado = $conexion->query($sql_maquinado);

                // Query for Paileria
                $sql_paileria = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND fechafinal is not null AND area = 'Paileria' GROUP BY piezas.id;";
                $result_paileria = $conexion->query($sql_paileria);

                $sql_compras = "SELECT c.id, c.ot, c.descripcion, c.id_oc, orden_compra.fecha_solicitud, orden_compra.llegada_estimada, orden_compra.firma_llegada FROM compras c LEFT JOIN orden_compra ON c.id_oc = orden_compra.id WHERE ot='$ot' AND orden_compra.firma_llegada is not null ORDER BY orden_compra.fecha_solicitud DESC";
                $result_compras = $conexion->query($sql_compras);
            }
            if ($estado == "pendiente") {
                $sql_maquinado = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND fechafinal is null AND area = 'Maquinado' GROUP BY piezas.id;";
                $result_maquinado = $conexion->query($sql_maquinado);

                // Query for Paileria
                $sql_paileria = "SELECT piezas.*, SUM(encargado.cantidad) AS total_cantidad FROM piezas LEFT JOIN encargado ON piezas.id = encargado.id_pieza WHERE piezas.ot = '$ot' AND fechafinal is null AND area = 'Paileria' GROUP BY piezas.id;";
                $result_paileria = $conexion->query($sql_paileria);
                
                $sql_compras = "SELECT c.id, c.ot, c.descripcion, c.id_oc, orden_compra.fecha_solicitud, orden_compra.llegada_estimada, orden_compra.firma_llegada FROM compras c LEFT JOIN orden_compra ON c.id_oc = orden_compra.id WHERE ot='$ot' AND orden_compra.firma_llegada is null ORDER BY orden_compra.fecha_solicitud DESC";
                $result_compras = $conexion->query($sql_compras);
            }

            // Display Maquinado and Paileria table
            echo "<table class='reporte_tabla'>";
            echo "
                <tr>
                    <th>Maquinado</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Paileria</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Compras</th>
                    <th>Fecha de Solicitud</th>
                    <th>Fecha Estimada</th>
                    <th>Firma Almacén</th>
                </tr>";

            $maxRows = max(
                $result_maquinado ? $result_maquinado->num_rows : 0,
                $result_paileria ? $result_paileria->num_rows : 0,
                $result_compras ? $result_compras->num_rows : 0
            );

            for ($i = 0; $i < $maxRows; $i++) {
                echo "<tr>";

                // Fetch Maquinado data
                if ($result_maquinado && $fila_maquinado = $result_maquinado->fetch_assoc()) {
                    echo "<td>" . $fila_maquinado['pieza'] . "</td>";
                    echo "<td>" . $fila_maquinado['fecha_inicial'] . "</td>";
                    echo "<td>" . $fila_maquinado['fecha_final'] . "</td>";
                } else {
                    echo "<td></td><td></td><td></td>";
                }

                // Fetch Paileria data
                if ($result_paileria && $fila_paileria = $result_paileria->fetch_assoc()) {
                    echo "<td>" . $fila_paileria['pieza'] . "</td>";
                    echo "<td>" . $fila_paileria['fecha_inicial'] . "</td>";
                    echo "<td>" . $fila_paileria['fecha_final'] . "</td>";
                } else {
                    echo "<td></td><td></td><td></td>";
                }

               

                // Fetch Compras data
                if ($result_compras && $fila_compras = $result_compras->fetch_assoc()) {
                    echo "<td>" . $fila_compras['descripcion'] . "</td>";
                    echo "<td>" . $fila_compras['fecha_solicitud'] . "</td>";
                    echo "<td>" . $fila_compras['llegada_estimada'] . "</td>";
                    echo "<td>" . $fila_compras['firma_llegada'] . "</td>";
                } else {
                    echo "<td></td><td></td><td></td>";
                }

                echo "</tr>";
            }

            echo "</table>";
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
        ?>
    </section>
</div>
</body>
</html>