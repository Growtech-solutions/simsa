
<head>
    <title>Análisis de Facturas</title>
</head>
<body id="facturas_mensuales">
   <?php
// Obtener el mes actual
$mes_actual = date('m');
// Obtener el año actual
$anio_actual = date('Y');

// Obtener el rango de años (últimos 5 años por defecto)
$anio_solicitud = isset($_GET['anio_solicitud']) ? $_GET['anio_solicitud'] : $anio_actual - 3;
$anio_solicitudfin = isset($_GET['anio_solicitudfin']) ? $_GET['anio_solicitudfin'] : $anio_actual;

// Generar la consulta para obtener el valor de las facturas de todos los meses dentro del rango de años
$sql = "SELECT YEAR(alta_sistema) as year, MONTH(alta_sistema) as month, SUM(valor_pesos) as total_valor_pesos 
        FROM facturas 
        WHERE YEAR(alta_sistema) BETWEEN '$anio_solicitud' AND '$anio_solicitudfin'
        GROUP BY YEAR(alta_sistema), MONTH(alta_sistema)
        ORDER BY YEAR(alta_sistema), MONTH(alta_sistema)";
$result = $conexion->query($sql);

// Crear un array para almacenar los valores de las facturas por cada mes y año
$facturas = [];
$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

// Inicializar el array de facturas con valores a cero para todos los meses de cada año
for ($year = $anio_solicitud; $year <= $anio_solicitudfin; $year++) {
    foreach ($meses as $key => $mes) {
        $facturas[$year][$key + 1] = 0; // Asigna valor 0 para cada mes del año
    }
}

// Rellenar el array con los datos de la consulta
while ($row = $result->fetch_assoc()) {
    $year = $row['year'];
    $month = $row['month'];
    $facturas[$year][$month] = $row['total_valor_pesos'];
}

$conexion->close();
?>

<!-- Formulario de selección del rango de años -->
<div class="contenedor__servicios">
    <h2 class="titulo">Análisis de Facturas por Mes</h2>
    <form method="GET">
        <label for="anio_solicitud">Desde Año:</label>
        <input type="number" id="anio_solicitud" name="anio_solicitud" value="<?php echo htmlspecialchars($anio_solicitud, ENT_QUOTES); ?>" placeholder="Año inicio">
        
        <label for="anio_solicitudfin">Hasta Año:</label>
        <input type="number" id="anio_solicitudfin" name="anio_solicitudfin" value="<?php echo htmlspecialchars($anio_solicitudfin, ENT_QUOTES); ?>">
        <input type="hidden" name="pestaña" value="facturas_mensuales">
            <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc, ENT_QUOTES); ?>">
            
        <button type="submit">Filtrar</button>
    </form>
    <canvas id="graficaFacturas"></canvas>
</div>

<!-- Gráfico con Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('graficaFacturas').getContext('2d');

    // Crear etiquetas para todos los meses
    var labels = <?php echo json_encode($meses); ?>;

    var datasets = [];
    var promedio = Array(labels.length).fill(0); // Array para almacenar los promedios mensuales
    var contador = Array(labels.length).fill(0); // Contador para promediar
    var acumulados = {}; // Objeto para acumular los valores anuales por mes
    var promedioAcumulado = Array(labels.length).fill(0); // Inicializar promedio acumulado

    <?php foreach ($facturas as $year => $data): ?>
    var acumulado = 0; // Inicializar acumulado para cada año
    var datosAcumulados = [];

    datasets.push({
        label: '<?php echo $year; ?>',
        data: <?php echo json_encode(array_values($data)); ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        yAxisID: 'y1' // Escala principal para valores por mes
    });

    // Sumar los valores al promedio y calcular el acumulado
    <?php foreach ($data as $month => $value): ?>
        promedio[<?php echo $month - 1; ?>] += <?php echo $value; ?>;
        contador[<?php echo $month - 1; ?>]++;
        acumulado += <?php echo $value; ?>;
        datosAcumulados.push(acumulado);

        // Sumar al promedio acumulado
        promedioAcumulado[<?php echo $month - 1; ?>] += acumulado;
    <?php endforeach; ?>

    acumulados['<?php echo $year; ?>'] = datosAcumulados;

    datasets.push({
        label: 'Acumulado <?php echo $year; ?>',
        data: datosAcumulados,
        type: 'line',
        borderColor: 'rgba(54, 162, 235, 1)',
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderWidth: 2,
        fill: false,
        yAxisID: 'y2', // Escala secundaria para acumulados
        tension: 0.4
    });
    <?php endforeach; ?>

    // Calcular los promedios mensuales
    promedio = promedio.map(function(total, index) {
        return contador[index] > 0 ? total / contador[index] : 0;
    });

    // Calcular el promedio acumulado
    promedioAcumulado = promedioAcumulado.map(function(total, index) {
        return contador[index] > 0 ? total / Object.keys(acumulados).length : 0;
    });

    // Agregar el dataset del promedio mensual
    datasets.push({
        label: 'Promedio Mensual',
        data: promedio,
        type: 'line',
        borderColor: 'rgba(255, 99, 132, 1)',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderWidth: 2,
        fill: false,
        tension: 0.4,
        yAxisID: 'y1' // Escala principal
    });

    // Agregar el dataset del promedio acumulado
    datasets.push({
        label: 'Promedio Acumulado',
        data: promedioAcumulado,
        type: 'line',
        borderColor: 'rgba(153, 102, 255, 1)',
        backgroundColor: 'rgba(153, 102, 255, 0.2)',
        borderWidth: 2,
        fill: false,
        yAxisID: 'y2', // Escala secundaria
        tension: 0.4
    });

    var graficaFacturas = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, // Las etiquetas serán los meses
            datasets: datasets // Cada dataset corresponde a un año, promedio y acumulado
        },
        options: {
            scales: {
                y1: {
                    type: 'linear',
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
                        }
                    }
                },
                y2: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false // Evita líneas de cuadrícula redundantes
                    },
                    ticks: {
                        callback: function(value) {
                            return (value ).toLocaleString() ;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            var dataset = datasets[tooltipItem.datasetIndex];
                            var value = tooltipItem.raw;
                            if (dataset.yAxisID === 'y2') {
                                return `${dataset.label}: ${(value).toLocaleString()}`;
                            }
                            return `${dataset.label}: ${new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value)}`;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>


