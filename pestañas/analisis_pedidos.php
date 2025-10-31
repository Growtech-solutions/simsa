
<head>
    <title>Análisis de Pedidos</title>
</head>
<body id="analisis_pedidos">
    <?php
    // Obtener la fecha actual y el primer día del mes actual
    $fecha_actual = date('Y-m-d');
    $fecha_inicio_mes = date('Y-01-01');

    // Obtener el rango de fechas seleccionado o usar las fechas predeterminadas
    $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : $fecha_inicio_mes;
    $fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $_GET['fecha_solicitudfin'] : $fecha_actual;

    // Generar la consulta para obtener la suma de valor_pesos por mes en el rango de fechas seleccionado
    $sql = "SELECT YEAR(fecha_alta) as year, MONTH(fecha_alta) as mes, SUM(valor_pesos) as total_valor_pesos 
            FROM pedido 
            WHERE fecha_alta BETWEEN '$fecha_solicitud' AND '$fecha_solicitudfin'
            GROUP BY YEAR(fecha_alta), MONTH(fecha_alta)
            ORDER BY YEAR(fecha_alta), MONTH(fecha_alta)";
    $result = $conexion->query($sql);

    // Crear un array de todos los meses en el rango seleccionado y su valor correspondiente
    $months = [];
    $totals = [];
    $current = strtotime($fecha_solicitud);
    $end = strtotime($fecha_solicitudfin);
    
    while ($current <= $end) {
        $year = date("Y", $current);
        $month = date("m", $current);
        $months[] = date("F Y", $current);
        $totals[$year . '-' . $month] = 0; // Inicializar con 0 en caso de que no haya pedidos en ese mes
        $current = strtotime("+1 month", $current);
    }

    // Rellenar los totales con los datos obtenidos de la consulta
    while ($row = $result->fetch_assoc()) {
        $totals[$row['year'] . '-' . str_pad($row['mes'], 2, '0', STR_PAD_LEFT)] = $row['total_valor_pesos'];
    }

    $conexion->close();
    ?>

    <!-- Formulario de selección del rango de fechas -->
    <div class="contenedor__servicios">
        <h2 class="titulo">Análisis de Pedidos</h2>
        <form method="GET">
            <label for="fecha_solicitud">Entre:</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($fecha_solicitud, ENT_QUOTES); ?>" placeholder="Fecha inicio">
        
            <label for="fecha_solicitudfin">y:</label>
            <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($fecha_solicitudfin, ENT_QUOTES); ?>" placeholder="Fecha fin">

            <input type="hidden" name="pestaña" value="analisis_pedidos">
            <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc, ENT_QUOTES); ?>">
            <button type="submit">Filtrar</button>
        </form>
        <canvas id="graficaPedidos"></canvas>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('graficaPedidos').getContext('2d');
        var graficaPedidos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Valor Total de Pedidos por Mes (en Pesos)',
                    data: <?php echo json_encode(array_values($totals)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
