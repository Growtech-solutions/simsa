<head>
    <title>Reporte diario</title>
    <style>
        .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
        .center{
            text-align:center;
        }
    </style>
</head>
<body id="encargado">
    <div class="principal">
        <div>
            <h2 class="center">Reporte</h2>
            <form class="reporte_formulario" method="GET" action="">
                <label for="fecha">Fecha:</label>
                <input class="formulario_reporte_fecha" type="date" id="fecha" name="fecha">
                <label for="ot">OT:</label>
                <input class="formulario_reporte_ot" type="text" id="ot" name="ot">   
                <label for="area">Area:</label>
                <select class="formulario_reporte_area" type="text" id="area" name="area">
                    <option value="">Seleccione área</option>
                    <option value="Maquinados">Maquinados</option>
                    <option value="Paileria">Paileria</option>
                    <option value="casosespeciales">casosespeciales</option>
                    <option value="Vueltas">Vueltas</option>
                </select>
                <input type="hidden" name="pestaña" value="reporte_diario">
                <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                <input type="submit" value="Generar Reporte">
            </form>
            <div class="reporte_tabla">
            <?php
                // Definir las variables de los filtros
                $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
                $area = isset($_GET['area']) ? $_GET['area'] : '';
                $ot = isset($_GET['ot']) ? $_GET['ot'] : '';
                $pestaña= isset($_GET['pestaña']) ? $_GET['pestaña'] : '';
                $header_loc= isset($_GET['header_loc']) ? $_GET['header_loc'] : '';

                $sql = "SELECT 
                    piezas.pieza AS nombre_pieza,
                    piezas.area AS pieza_area,
                    piezas.ot AS pieza_ot,
                    encargado.id as encargado_id,
                    encargado.ot_tardia AS encargado_ot,
                    encargado.cantidad,
                    trabajadores.nombre,
                    trabajadores.apellidos,
                    encargado.tiempo,
                    encargado.pieza_tardia,
                    encargado.actividad,
                    encargado.fecha
                FROM 
                    piezas 
                RIGHT JOIN 
                    encargado ON piezas.id = encargado.id_pieza 
                LEFT JOIN
                    transimex.trabajadores ON encargado.id_trabajador = trabajadores.id
                WHERE 
                    encargado.fecha = '$fecha'";

                // Agregar condiciones según los filtros proporcionados
                if (!empty($area)) {
                    $sql .= " AND (piezas.area LIKE '%$area%' OR vueltas.area LIKE '%$area%')";
                }
                $sql .= " ORDER BY encargado.id_trabajador";

                // Ejecutar la consulta
                $resultado = $conexion->query($sql);

                // Mostrar los resultados
                if ($resultado->num_rows > 0) {
                    echo "<table border='1'>";
                    echo "<tr>
                            <th>Nombre</th>
                            <th>OT</th>
                            <th>Pieza</th>
                            <th>Actividad</th>
                            <th>Cantidad</th>
                            <th>Área</th>
                            <th>Tiempo</th>
                            <th>Fecha</th>
                            <th>Eliminar</th>
                        </tr>";
                                
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila['nombre'] . " ". $fila['apellidos'] . " </td>";
                        if ($fila['pieza_ot'] == NULL) {
                            echo "<td>" . $fila['encargado_ot'] . "</td>";
                        } else {
                            echo "<td>" . $fila['pieza_ot'] . "</td>";
                        }

                        // Mostrar el nombre de la pieza o de la vuelta si es nulo
                        if ($fila['nombre_pieza'] == NULL) {
                            echo "<td>" .  $fila['pieza_tardia'] . "</td>";
                        } else {
                            echo "<td>" . $fila['nombre_pieza'] . "</td>";
                        }
                        $actividad_id = $fila["actividad"];
                    $sql_actividad = "SELECT descripcion FROM precios WHERE id = '$actividad_id'";
                    $result_actividad = $conexion->query($sql_actividad);
                    $actividad = $result_actividad->fetch_assoc();
                    echo "<td>".$actividad["descripcion"]."</td>";
                        echo "<td>" .  $fila['cantidad'] . "</td>";

                        // Mostrar el área de la pieza o 'vueltas' si es nulo
                        if ($fila['pieza_area'] == NULL) {
                            echo "<td>Sin area</td>";
                        } else {
                            echo "<td>" . $fila['pieza_area'] . "</td>";
                        }
                        echo "<td class='centrado'>" . $fila['tiempo'] . ' hrs' . "</td>";
                        echo "<td>" . $fila['fecha'] . "</td>";
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
