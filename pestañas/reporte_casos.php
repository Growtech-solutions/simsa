<!DOCTYPE html>
<html lang="en">
<head>
     <title>Reporte de Trabajadores</title>
</head>
<body id="reporte_casos">
    <div class="principal">
        <div>
            <h2>Reporte de Trabajadores</h2>

        <form class="reporte_formulario" method="GET" action="">
            <label for="fecha_inicial">Fecha Inicial:</label>
            <input class="formulario_reporte_fecha" type="date" id="fecha_inicial" name="fecha_inicial" required>
            <label for="fecha_final">Fecha Final:</label>
            <input class="formulario_reporte_fecha" type="date" id="fecha_final" name="fecha_final" required>
            <input type="hidden" name="pestaña" value="reporte_casos">
            <input type="submit" value="Generar Reporte">
        </form>
        <div class="reporte_tabla">
            <?php
            // Definir las variables de los filtros
            $fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : '';
            $fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : '';

            // Verificar si las fechas están presentes
            if (!empty($fecha_inicial) && !empty($fecha_final)) {
                // Consulta SQL para generar el reporte
                $sql = "
                    SELECT  
                        CONCAT(trabajadores.nombre, ' ', trabajadores.apellidos) AS trabajador,
                        COUNT(CASE WHEN e.ot_tardia = 700 OR piezas.ot = 700 THEN 1 END) AS faltas,
                        COUNT(CASE WHEN e.ot_tardia = 701 OR piezas.ot = 701 THEN 1 END) AS permisos,
                        COUNT(CASE WHEN e.ot_tardia = 702 OR piezas.ot = 702 THEN 1 END) AS suspensiones,
                        COUNT(CASE WHEN e.ot_tardia = 707 OR piezas.ot = 707 THEN 1 END) AS incapacidad_tim,
                        COUNT(CASE WHEN e.ot_tardia = 706 OR piezas.ot = 706 THEN 1 END) AS incapacidad_imss,
                        COUNT(CASE WHEN e.ot_tardia = 712 OR piezas.ot = 712 THEN 1 END) AS faltas_justificadas,
                        SUM(e.tiempo) AS tiempo,
                    COALESCE((
                            SELECT 
                                SUM(retardo.penalizacion) 
                            FROM 
                                retardo 
                            LEFT JOIN 
                                trabajadores ON retardo.trabajador = trabajadores.id
                            WHERE 
                                trabajadores.id = e.id_trabajador 
                                AND retardo.fecha BETWEEN '$fecha_inicial' AND '$fecha_final' 
                        ), 0) AS retardos
                    FROM (
                        SELECT fecha, id_pieza, id_trabajador, ot_tardia, tiempo
                        FROM encargado
                        UNION ALL
                        SELECT fecha, id_pieza, id_trabajador, ot_tardia, tiempo
                        FROM simsa.encargado
                    ) AS e
                    LEFT JOIN 
                        piezas ON e.id_pieza = piezas.id
                    LEFT JOIN 
                        trabajadores on e.id_trabajador = trabajadores.id
                    WHERE 
                        e.fecha BETWEEN '$fecha_inicial' AND '$fecha_final AND e.id_trabajador is not null'
                    GROUP BY 
                        e.id_trabajador;
                ";

                // Ejecutar la consulta
                $resultado = $conexion_transimex->query($sql);

                // Mostrar los resultados
                if ($resultado->num_rows > 0) {
                    echo "<table border='1'>";
                    echo "<tr>
                            <th>Trabajador</th>
                            <th>hrs trabajadas</th>
                            <th>Retardos</th>
                            <th>Faltas</th>
                            <th>Faltas justificadas</th>
                            <th>Permisos</th>
                            <th>Suspensiones</th>
                            <th>Incapacidad TIM</th>
                            <th>Incapacidad IMSS</th>
                          </tr>";
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila['trabajador'] . "</td>";
                        echo "<td class='centrado'>" . $fila['tiempo'] . "</td>";
                        echo "<td class='centrado'>" . $fila['retardos'] . "</td>";
                        echo "<td class='centrado'>" . $fila['faltas'] . "</td>";
                        echo "<td class='centrado'>" . $fila['faltas_justificadas'] . "</td>";
                        echo "<td class='centrado'>" . $fila['permisos'] . "</td>";
                        echo "<td class='centrado'>" . $fila['suspensiones'] . "</td>";
                        echo "<td class='centrado'>" . $fila['incapacidad_tim'] . "</td>";
                        echo "<td class='centrado'>" . $fila['incapacidad_imss'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No se encontraron registros para las fechas seleccionadas.";
                }

                // Cerrar la conexión a la base de datos
                $conexion_transimex->close();
            } else {
                echo "Seleccione un rango de fechas para generar el reporte.";
            }
            ?>
        </div>
        </div>
        
    </div>
</body>
</html>



