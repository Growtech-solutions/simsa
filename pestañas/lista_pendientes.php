<?php

// Escapar valores de entrada
$responsable = isset($_GET['responsable']) ? $conexion->real_escape_string($_GET['responsable']) : '';
$actividad = isset($_GET['actividad']) ? $conexion->real_escape_string($_GET['actividad']) : '';
$fecha_inicio = isset($_GET['fecha_solicitud']) ? $conexion->real_escape_string($_GET['fecha_solicitud']) : '';
$fecha_fin = isset($_GET['fecha_solicitudfin']) ? $conexion->real_escape_string($_GET['fecha_solicitudfin']) : '';
$resumen = isset($_GET['resumen']) ? $conexion->real_escape_string($_GET['resumen']) : '';

// Paginación
$registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construcción de la consulta SQL
$sql = "SELECT 
    trabajadores.nombre AS id_encargado, 
    trabajadores.apellidos AS apellidos,
    actividades.actividad AS actividad,
    actividades.tipo AS departamento,
    historial_actividades.fecha AS fecha, 
    historial_actividades.resumen AS resumen,
    periodicos.frecuencia AS frecuencia,
    periodicos.objeto AS objeto,
    DATE_ADD(historial_actividades.fecha, INTERVAL periodicos.frecuencia WEEK) AS fecha_fin
FROM 
    historial_actividades 
LEFT JOIN 
    transimex.trabajadores ON historial_actividades.id_encargado = trabajadores.id
LEFT JOIN 
    periodicos ON historial_actividades.actividad = periodicos.id
LEFT JOIN 
    actividades ON periodicos.id_act = actividades.id
WHERE historial_actividades.fecha = (
    SELECT MAX(h2.fecha) 
    FROM historial_actividades h2
    WHERE h2.actividad = historial_actividades.actividad
)
AND actividades.tipo != 'Habilitaciones'
";

$conditions = [];
$params = [];
$types = '';

// Aplicar filtros
if (!empty($responsable)) {
    $conditions[] = "trabajadores.nombre LIKE ?";
    $params[] = "%$responsable%";
    $types .= 's';
}
if (!empty($actividad)) {
    $conditions[] = "actividades.actividad LIKE ?";
    $params[] = "%$actividad%";
    $types .= 's';
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $conditions[] = "historial_actividades.fecha BETWEEN ? AND ?";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
    $types .= 'ss';
}
if (!empty($resumen)) {
    $conditions[] = "historial_actividades.resumen LIKE ?";
    $params[] = "%$resumen%";
    $types .= 's';
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY fecha_fin";

// Obtener total de registros
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$stmt->store_result();
$total_registros = $stmt->num_rows;

// Agregar paginación
$sql .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $registros_por_pagina;
$types .= 'ii';

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Histórico Actividades</title>
</head>
<body>

<div class="principal">
    <section>
    <h1>Actividades</h1>
    
    <form class="reporte_formulario" method="GET" action="">
        <label for="responsable">Responsable:</label>
        <input type="text" id="responsable" name="responsable" value="<?php echo htmlspecialchars($_GET['responsable'] ?? ''); ?>" placeholder="Buscar por responsable">
        
        <label for="actividad">Actividad:</label>
        <input type="text" id="actividad" name="actividad" value="<?php echo htmlspecialchars($_GET['actividad'] ?? ''); ?>" placeholder="Actividad">
        
        <label for="fecha_solicitud">Vencimiento entre:</label>
        <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($_GET['fecha_solicitud'] ?? ''); ?>">
        
        <label for="fecha_solicitudfin">y:</label>
        <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($_GET['fecha_solicitudfin'] ?? ''); ?>">
        
        <label for="resumen">Resumen:</label>
        <input type="text" id="resumen" name="resumen" value="<?php echo htmlspecialchars($_GET['resumen'] ?? ''); ?>" placeholder="Resumen">
        
        <input type="hidden" name="pestaña" value="lista_pendientes">
        <input type="submit" value="Buscar">
    </form>

    <div class="registros-por-pagina">
        <form method="GET" action="">
            <label for="registros_por_pagina">Registros por página:</label>
            <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                <?php foreach ([10, 20, 50, 100] as $option) { ?>
                    <option value="<?php echo $option; ?>" <?php echo ($option == $registros_por_pagina) ? 'selected' : ''; ?>>
                        <?php echo $option; ?>
                    </option>
                <?php } ?>
            </select>

            <?php
                $filtros = ['responsable', 'actividad', 'fecha_solicitud', 'fecha_solicitudfin', 'resumen'];
                foreach ($filtros as $filtro) {
                    if (!empty($_GET[$filtro])) {
                        echo '<input type="hidden" name="' . $filtro . '" value="' . htmlspecialchars($_GET[$filtro]) . '">';
                    }
                }
            ?>
        </form>
    </div>

    <table border="1">
        <tr>
            <th>Vencimiento</th>
            <th>Responsable</th>
            <th>Actividad</th>
            <th>Resumen</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['fecha_fin']); ?></td>
                <td><?php echo htmlspecialchars($row['id_encargado']. " " . $row['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($row['actividad']. "-" . $row['objeto']); ?></td>
                <td><?php echo htmlspecialchars($row['resumen']); ?></td>
            </tr>
        <?php } ?>
    </table>

    <div class="paginacion">
        <?php
        $total_paginas = ceil($total_registros / $registros_por_pagina);
        for ($i = 1; $i <= $total_paginas; $i++) {
            echo "<a href='?pagina=$i&registros_por_pagina=$registros_por_pagina";
            foreach ($filtros as $filter) {
                if (!empty($_GET[$filter])) {
                    echo "&$filter=" . urlencode($_GET[$filter]);
                }
            }
            echo "'> $i </a>";
        }
        ?>
    </div>
</section>
    
</div>

</body>
</html>

<?php
$stmt->close();
$conexion->close();
?>
