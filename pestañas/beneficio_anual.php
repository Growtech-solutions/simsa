<!DOCTYPE html>
<html lang="en">
<head>
    <title>Análisis de beneficio</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body id="analisis_beneficio_anual">
    <?php
    // Obtener el rango de fechas por año
    $fecha_actual = date('Y-m-d');
    $anio_actual = date('Y');
    $anio_inicio = isset($_GET['anio_inicio']) ? $_GET['anio_inicio'] : $anio_actual - 6; // 6 años atrás
    $anio_fin = isset($_GET['anio_fin']) ? $_GET['anio_fin'] : $anio_actual;

    // Establecer las fechas de inicio y fin
    $fecha_inicio = "$anio_inicio-01-01"; // 1 de enero del año de inicio
    $fecha_solicitudfin = "$anio_fin-12-31"; // 31 de diciembre del año final

    // Consultar las facturas
    $facturas_sql = "SELECT YEAR(alta_sistema) as year, SUM(valor_pesos) as total_facturas
                     FROM facturas
                     WHERE alta_sistema BETWEEN '$fecha_inicio' AND '$fecha_solicitudfin'
                     GROUP BY YEAR(alta_sistema)";
    $facturas_result = $conexion->query($facturas_sql);

    // Consultar las compras
    $compras_sql = "SELECT YEAR(fecha_solicitud) as year, SUM(total_pesos) as total_compras
                    FROM orden_compra
                    WHERE fecha_solicitud BETWEEN '$fecha_inicio' AND '$fecha_solicitudfin'
                    GROUP BY YEAR(fecha_solicitud)";
    $compras_result = $conexion->query($compras_sql);

    // Consultar la nómina
    $nomina_sql = "SELECT YEAR(Fecha_inicial) as year, SUM(Percepcion_empresa) as nomina
                   FROM nomina
                   WHERE Fecha_inicial BETWEEN '$fecha_inicio' AND '$fecha_solicitudfin'
                   GROUP BY YEAR(Fecha_inicial)";
    $nomina_result = $conexion->query($nomina_sql);

    // Preparar datos para el gráfico
    $facturas_data = [];
    $compras_data = [];
    $nomina_data = [];
    $beneficio_data = [];

    // Combinar los resultados en un solo array
    $data = [];
    while ($row = $facturas_result->fetch_assoc()) {
        $data[$row['year']]['facturas'] = $row['total_facturas'];
    }

    while ($row = $compras_result->fetch_assoc()) {
        $data[$row['year']]['compras'] = $row['total_compras'];
    }

    while ($row = $nomina_result->fetch_assoc()) {
        $data[$row['year']]['nomina'] = $row['nomina'];
    }

    // Calcular datos finales
    foreach ($data as $year => $values) {
        $facturas = $values['facturas'] ?? 0;
        $compras = $values['compras'] ?? 0;
        $nomina = $values['nomina'] ?? 0;

        $facturas_data[] = ['x' => $year, 'y' => $facturas];
        $compras_data[] = ['x' => $year, 'y' => $compras];
        $nomina_data[] = ['x' => $year, 'y' => $nomina];
        $beneficio_data[] = ['x' => $year, 'y' => $facturas - $compras - $nomina];
    }

    $conexion->close();
    ?>

    <div class="contenedor__servicios">
        <h2 class="titulo">Análisis de Ingresos, Compras y Beneficio</h2>
        <form method="GET">
            <label for="anio_inicio">Desde el año:</label>
            <input type="number" id="anio_inicio" name="anio_inicio" value="<?php echo htmlspecialchars($anio_inicio, ENT_QUOTES); ?>" min="2000" max="<?php echo $anio_actual; ?>">

            <label for="anio_fin">Hasta el año:</label>
            <input type="number" id="anio_fin" name="anio_fin" value="<?php echo htmlspecialchars($anio_fin, ENT_QUOTES); ?>" min="2000" max="<?php echo $anio_actual; ?>">
            
            <input type="hidden" name="pestaña" value="beneficio_anual">
            <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc, ENT_QUOTES); ?>">
            
            <button type="submit">Filtrar</button>
        </form>
        <canvas id="graficaComprasFacturas"></canvas>
    </div>

    <script>
        const facturasData = <?php echo json_encode($facturas_data); ?>;
        const comprasData = <?php echo json_encode($compras_data); ?>;
        const nominaData = <?php echo json_encode($nomina_data); ?>;
        const beneficioData = <?php echo json_encode($beneficio_data); ?>;

        const ctx = document.getElementById('graficaComprasFacturas').getContext('2d');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Facturas',
                        data: facturasData,
                        type: 'line',
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Compras',
                        data: comprasData,
                        type: 'line',
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Nómina',
                        data: nominaData,
                        type: 'line',
                        backgroundColor: 'rgba(45, 87, 44, 0.6)',
                        borderColor: 'rgba(45, 87, 44, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Beneficio',
                        data: beneficioData,
                        type: 'line',
                        fill: false,
                        borderColor: 'rgba(75, 0, 130, 1)'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Año'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Monto (Pesos)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

