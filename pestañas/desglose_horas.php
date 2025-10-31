<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historial de Horas</title>
    <style>
        .centrado { text-align: center; }
        h1{
            text-align:center;
        }
        form{
            text-align:center
        }
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { padding: 8px; text-align: left; }
        tbody tr:nth-child(odd) { background-color: #f7f7f7; }
         .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
    </style>
</head>
<body>
    <div class="principal">
        <section>
            <h1>Historial de Horas</h1>

            <!-- Formulario para seleccionar la OT -->
            <form method="GET" action="">
                <label for="ot">Seleccione la OT:</label>
                <input type="text" id="ot" name="ot" value="<?php echo isset($_GET['ot']) ? htmlspecialchars($_GET['ot']) : ''; ?>">
                <input type="hidden" name="pestaña" value="desglose_horas">
                <button type="submit">Buscar</button>
            </form>
            <br>
            <?php

            // Verifica si se ha seleccionado una OT
            if (isset($_GET['ot']) && !empty($_GET['ot'])) {
                $ot = $conexion->real_escape_string($_GET['ot']);
            
                // Consulta principal sin paginación
                $sql_horas = "
                SELECT id_trabajador, tiempo, fecha, pieza_tardia, nombre_pieza, nombre, apellidos
                FROM (
                    -- Caso 1: Asociación directa en encargado
                    SELECT e.id_trabajador, e.tiempo, e.fecha, e.pieza_tardia, NULL AS nombre_pieza, t.nombre, t.apellidos
                    FROM encargado e
                    LEFT JOIN transimex.trabajadores t ON e.id_trabajador = t.id
                    WHERE e.ot_tardia = ?
            
                    UNION
                    
                    -- Caso 2: Asociación a través de piezas
                    SELECT e.id_trabajador, e.tiempo, e.fecha, e.pieza_tardia, p.pieza AS nombre_pieza, t.nombre, t.apellidos
                    FROM encargado e
                    JOIN piezas p ON e.id_pieza = p.id
                    LEFT JOIN transimex.trabajadores t ON e.id_trabajador = t.id
                    WHERE p.ot = ?
                ) AS historial";
            
                $stmt = $conexion->prepare($sql_horas);
                $stmt->bind_param("ii", $ot, $ot); // Si 'ot' es un entero
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($id, $tiempo, $fecha, $piezaextra, $nombre_pieza, $nombre, $apellidos);
            
                if ($stmt->num_rows > 0) {
                    echo "<table border='1'>
                            <tr>
                                <th>Nombre</th>
                                <th>Tiempo</th>
                                <th>Fecha</th>
                                <th>Pieza Tardia</th>
                                <th>Nombre Pieza</th>
                            </tr>";
            
                    while ($stmt->fetch()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($nombre) . " " . htmlspecialchars($apellidos) . "</td>
                                <td>" . htmlspecialchars($tiempo) . "</td>
                                <td>" . htmlspecialchars($fecha) . "</td>
                                <td>" . htmlspecialchars($piezaextra) . "</td>
                                <td>" . htmlspecialchars($nombre_pieza) . "</td>
                            </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No se encontraron registros para la OT seleccionada.";
                }
                $stmt->close();
            } else {
                echo "<p>Por favor, seleccione una OT para ver el historial de horas.</p>";
            }
            $conexion->close();
            ?>
        </section>
    </div>
</body>
</html>


