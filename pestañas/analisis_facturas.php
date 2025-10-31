<head>
    <title>Análisis de Facturas</title>
</head>
<body id="analisis_facturas">
    <?php
    // Obtener la fecha actual
    $fecha_actual = date('Y-m-d');
    // Obtener el primer día del mes actual
    $fecha_inicio_mes = date('Y-01-01');

    // Obtener el rango de fechas seleccionado o usar las fechas predeterminadas
    $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : $fecha_inicio_mes;
    $fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $_GET['fecha_solicitudfin'] : $fecha_actual;

    // Generar la consulta para obtener la suma de valor_pesos por mes en el rango de fechas seleccionado
    $sql = "SELECT YEAR(alta_sistema) as year, MONTH(alta_sistema) as mes, SUM(valor_pesos) as total_valor_pesos 
            FROM facturas 
            WHERE alta_sistema BETWEEN '$fecha_solicitud' AND '$fecha_solicitudfin'
            GROUP BY YEAR(alta_sistema), MONTH(alta_sistema)
            ORDER BY YEAR(alta_sistema), MONTH(alta_sistema)";
    $result = $conexion->query($sql);

    // Crear un array de todos los meses en el rango seleccionado y su valor correspondiente
    $months = [];
    $totals = [];
    $acumulado = 0;
    $acumulados = [];
    $current = strtotime($fecha_solicitud);
    $end = strtotime($fecha_solicitudfin);

    while ($current <= $end) {
        $year = date("Y", $current);
        $month = date("m", $current);
        $months[] = date("F Y", $current);
        $totals[$year . '-' . $month] = 0; // Inicializar con 0 en caso de que no haya facturas en ese mes
        $current = strtotime("+1 month", $current);
    }

    // Rellenar los totales con los datos obtenidos de la consulta
    while ($row = $result->fetch_assoc()) {
        $key = $row['year'] . '-' . str_pad($row['mes'], 2, '0', STR_PAD_LEFT);
        $totals[$key] = $row['total_valor_pesos'];
        $acumulado += $row['total_valor_pesos'];
        $acumulados[] = $acumulado;
    }

    $conexion->close();
    ?>

    <!-- Formulario de selección del rango de fechas -->
    <div class="contenedor__servicios">
        <h2 class="titulo">Análisis de Facturas</h2>
        <form method="GET">
            <label for="fecha_solicitud">Desde:</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($fecha_solicitud, ENT_QUOTES); ?>" placeholder="Fecha inicio">
        
            <label for="fecha_solicitudfin">Hasta:</label>
            <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($fecha_solicitudfin, ENT_QUOTES); ?>" placeholder="Fecha fin">
        
            <input type="hidden" name="pestaña" value="analisis_facturas">
            <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc, ENT_QUOTES); ?>">
            <button type="submit">Filtrar</button>
        </form>
        <canvas id="graficaFacturas"></canvas>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('graficaFacturas').getContext('2d');
        var graficaFacturas = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Valor Total de Facturas por Mes (en Pesos)',
                        data: <?php echo json_encode(array_values($totals)); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Valor Acumulado (en Pesos)',
                        data: <?php echo json_encode($acumulados); ?>,
                        type: 'line',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        fill: false,
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
                            }
                        },
                        position: 'left',
                    },
                    y2: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
                            }
                        },
                        position: 'right',
                        grid: {
                            drawOnChartArea: false, // Esto evita que las líneas de la segunda escala se dibujen en el área del gráfico
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

