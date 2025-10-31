<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resumen trabajo</title>
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
        .detalles {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            margin-top: 20px;
            gap: 20px;
        }
        .resumen, .resumen2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 20px;
            gap: 20px;
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php
// Función para obtener tiempos por pieza, vuelta, encargados y bonos
function obtenerTiemposPorOT($conexion, $ot) {
    // Consulta de tiempos para encargados sin pieza ni vuelta
    $sql_tiempo_encargado = "
        SELECT 'Sin Pieza/Vuelta' AS pieza_tardia, SUM(e.tiempo) AS tiempo_total, CONCAT(t.nombre, ' ', t.apellidos) AS nombre
        FROM encargado e
        LEFT JOIN transimex.trabajadores t ON e.id_trabajador = t.id
        WHERE e.ot_tardia = '$ot' AND e.id_pieza IS NULL
    ";
    $result_tiempo_encargado = $conexion->query($sql_tiempo_encargado);

    // Consulta de tiempos por pieza
    $sql_tiempo_pieza = "
        SELECT p.pieza AS nombre_pieza, SUM(e.tiempo) AS tiempo_total
        FROM encargado e
        JOIN piezas p ON e.id_pieza = p.id
        WHERE p.ot = '$ot'
        GROUP BY p.id
    ";
    $result_tiempo_pieza = $conexion->query($sql_tiempo_pieza);

    // Consulta para sumar horas de bonos (premios)
    $sql_tiempo_bonos = "SELECT SUM(horas) AS tiempo_total FROM bonos WHERE ot = '$ot'";
    $result_tiempo_bonos = $conexion->query($sql_tiempo_bonos);

    return [$result_tiempo_encargado, $result_tiempo_pieza, $result_tiempo_bonos];
}

?>
<div class="principal">
    <section class="mensaje">
        <div class="centrado">
            <h2>Tiempo por ot</h2>
            <form method="GET" action="">
                <label for="ot">Ingrese la OT:</label>
                <input type="text" id="ot" name="ot" required>
                <input type="hidden" name="pestaña" value="tiempo_piezas">
                <input type="submit" value="Buscar">
            </form>
            <br>
        </div>
         
        <?php
        if (isset($_GET['ot'])) {
            $ot = $conexion->real_escape_string($_GET['ot']);
            $sql_ot = "SELECT * FROM ot WHERE ot='$ot'";
            $result_ot = $conexion->query($sql_ot);
?>
        <?php
            if ($result_ot->num_rows > 0) {
            // Obtener tiempos por OT
            list($result_tiempo_encargado, $result_tiempo_pieza, $result_tiempo_bonos) = obtenerTiemposPorOT($conexion, $ot);
        
            // Preparar los datos para el gráfico
            $tiempos = [];
            $labels = [];
        
            // Procesar los datos de encargados sin pieza ni vuelta
            while ($encargado = $result_tiempo_encargado->fetch_assoc()) {
                $labels[] = $encargado['nombre'];
                $tiempos[] = (float)$encargado['tiempo_total'];
            }
        
            // Procesar los datos de piezas
            while ($pieza = $result_tiempo_pieza->fetch_assoc()) {
                $labels[] = $pieza['nombre_pieza'];
                $tiempos[] = (float)$pieza['tiempo_total'];
            }
        
            // Procesar los datos de bonos (premios)
            while ($bono = $result_tiempo_bonos->fetch_assoc()) {
                $labels[] = "bonos";
                $tiempos[] = (float)$bono['tiempo_total'];
            }
        
            echo "<div class='centrado'>";
            echo "<canvas id='tiempoChart' width='400' height='200'></canvas>";
            echo "</div>";
                ?>
                <script>
                    const ctx = document.getElementById('tiempoChart').getContext('2d');
                    const tiempoChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                label: 'Tiempo Total (horas)',
                                data: <?php echo json_encode($tiempos); ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Tiempo (horas)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Piezas'
                                    }
                                }
                            }
                        }
                    });
                </script>
                <?php
            } else {
                echo "<p>No se encontraron datos para la OT: " . htmlspecialchars($ot) . "</p>";
            }
        }
        ?>
    </section>
</div>
</body>
</html>
