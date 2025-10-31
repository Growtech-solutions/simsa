<!DOCTYPE html>
<html lang="en">
<head>
    <title>Análisis de beneficio</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body id="analisis_beneficio">
    <?php

    // Obtener el rango de fechas
    $fecha_actual = date('Y-m-d');
    $fecha_inicio = date('Y-01-01');
    $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : $fecha_inicio;
    $fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $_GET['fecha_solicitudfin'] : $fecha_actual;

    // Consultar las facturas
    $facturas_sql = "SELECT YEAR(alta_sistema) as year, MONTH(alta_sistema) as month, SUM(valor_pesos) as total_facturas
                     FROM facturas
                     WHERE alta_sistema BETWEEN '$fecha_solicitud' AND '$fecha_solicitudfin'
                     GROUP BY YEAR(alta_sistema), MONTH(alta_sistema)";
    $facturas_result = $conexion->query($facturas_sql);

    // Consultar las compras
    $compras_sql = "SELECT YEAR(fecha_solicitud) as year, MONTH(fecha_solicitud) as month, SUM(total_pesos) as total_compras
                    FROM orden_compra
                    WHERE fecha_solicitud BETWEEN '$fecha_solicitud' AND '$fecha_solicitudfin'
                    GROUP BY YEAR(fecha_solicitud), MONTH(fecha_solicitud)";
    $compras_result = $conexion->query($compras_sql);

    $nomina_sql = "SELECT YEAR(Fecha_final) as year, MONTH(Fecha_final) as month, SUM(Percepcion_empresa) as nomina
                    FROM nomina
                    WHERE Fecha_inicial BETWEEN '$fecha_solicitud' AND '$fecha_solicitudfin'
                    GROUP BY YEAR(Fecha_final), MONTH(Fecha_final)";
    $nomina_result = $conexion->query($nomina_sql);
    // Preparar datos
    $data = [];
    while ($row = $facturas_result->fetch_assoc()) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        $data[$key]['facturas'] = $row['total_facturas'];
        $data[$key]['compras'] = 0; // Inicializar compras como 0
    }
    while ($row = $compras_result->fetch_assoc()) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        if (!isset($data[$key])) {
            $data[$key]['facturas'] = 0; // Inicializar facturas como 0 si no existen
        }
        $data[$key]['compras'] = $row['total_compras'];
    }
    while ($row = $nomina_result->fetch_assoc()) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        if (!isset($data[$key])) {
            $data[$key]['facturas'] = 0; // Inicializar facturas como 0 si no existen
            $data[$key]['compras'] = 0; // Inicializar compras como 0 si no existen
        }
        $data[$key]['nomina'] = $row['nomina'];
    }


    // Calcular diferencia y preparar gráficos
    $months = [];
    $facturas = [];
    $compras = [];
    $diferencias = [];
    $nomina = [];
    foreach ($data as $key => $values) {
        $months[] = $key;
        $facturas[] = $values['facturas'];
        $compras[] = $values['compras'] * -1; // Mantener el valor negativo para las compras
        $nomina[] = $values['nomina']*-1;
        $diferencias[] = $values['facturas'] - $values['compras'] - $values['nomina'];
        
    }

    $conexion->close();
    ?>

    <div class="contenedor__servicios">
        <h2 class="titulo">Análisis de Ingresos y Compras</h2>
        <form method="GET">
            <label for="fecha_solicitud">Desde:</label>
            <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($fecha_solicitud, ENT_QUOTES); ?>">
            
            <label for="fecha_solicitudfin">Hasta:</label>
            <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($fecha_solicitudfin, ENT_QUOTES); ?>">
            
            <input type="hidden" name="pestaña" value="analisis_beneficio">
            <button type="submit">Filtrar</button>
        </form>
        <canvas id="graficaComprasFacturas"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('graficaComprasFacturas').getContext('2d');
        var graficaComprasFacturas = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: 'Facturas',
                        data: <?php echo json_encode($facturas); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 1,
                        fill: true
                    },
                    {
                        label: 'Compras',
                        data: <?php echo json_encode($compras); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 1,
                        fill: true
                    },
                    {
                        label: 'Nómina',
                        data: <?php echo json_encode($nomina); ?>,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderWidth: 1,
                        fill: true
                    },
                    {
                        label: 'Beneficio',
                        data: <?php echo json_encode($diferencias); ?>,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: true
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
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
