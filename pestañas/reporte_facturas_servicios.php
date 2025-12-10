<?php
// -----------------------------
// CONFIGURACIÓN BD
// -----------------------------
include "../conexion.php";
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// -----------------------------
// FILTROS (por default año actual)
// -----------------------------
$anio_actual = date("Y");
$inicio = $_GET["inicio"] ?? ($anio_actual . "-01-01");
$fin    = $_GET["fin"]    ?? ($anio_actual . "-12-31");

// -----------------------------
// CONSULTA A LA BD (AGRUPADO POR MES)
// -----------------------------
$sql = "
    SELECT DATE_FORMAT(alta_sistema, '%Y-%m') AS mes,
           SUM(valor_pesos) AS total
    FROM facturas
    WHERE DATE(alta_sistema) BETWEEN ? AND ?
    GROUP BY DATE_FORMAT(alta_sistema, '%Y-%m')
    ORDER BY DATE_FORMAT(alta_sistema, '%Y-%m')
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $inicio, $fin);
$stmt->execute();
$stmt->bind_result($mes, $total);

$meses = [];
$totales = [];

while ($stmt->fetch()) {
    $meses[] = $mes;
    $totales[] = (float)$total;
}

$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gráfica de Facturación</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .titulo {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .filtros {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .grafica-container {
        position: relative;
        height: 400px;
        width: 100%;
        margin-top: 20px;
    }
    form{
        display: flex;
        justify-content: center;
    }
</style>
</head>
<body>

<div class="principal">
    <div>
    <div class="titulo">📊 Gráfica de Facturación por Mes</div>

    <form method="GET" class="filtros">
        <div>
            <label>Fecha inicio</label><br>
            <input type="date" name="inicio" value="<?= htmlspecialchars($inicio) ?>">
        </div>

        <div>
            <label>Fecha fin</label><br>
            <input type="date" name="fin" value="<?= htmlspecialchars($fin) ?>">
        </div>

        <div>
            <button style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;" type="submit">Actualizar</button>
        </div>

        <input type="hidden" name="pestaña" value="reporte_facturas_servicios">
    </form>

    <div>
        <p style="font-size: 18px; margin: 15px 0;">
            <strong>Total facturado en el periodo:</strong> 
            $<?= number_format(array_sum($totales), 2) ?>
        </p>
    </div>

    <div class="grafica-container">
        <canvas id="grafica"></canvas>
    </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var meses = <?= json_encode($meses) ?>;
    var totales = <?= json_encode($totales) ?>;

    console.log("Meses:", meses);
    console.log("Totales:", totales);

    const ctx = document.getElementById("grafica");

    if (!meses.length) {
        ctx.insertAdjacentHTML("beforebegin", "<p>No hay datos para graficar en el rango seleccionado.</p>");
        return;
    }

    new Chart(ctx, {
        type: "line",
        data: {
            labels: meses,
            datasets: [{
                label: "Facturación (MXN)",
                data: totales,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

</body>
</html>
