<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ordenes de compra</title>
    <style>
        .centrado {
            text-align: center;
        }
        .formulario_reporte_fecha {
            margin-right: 10px;
        }
        .formulario_reporte_area {
            margin-bottom: 10px;
        }
        .reporte_tabla {
            margin-top: 20px;
        }

        .iniciar{
            margin: .3rem;
        }
        .paginacion {
            margin-top: 20px;
            text-align: center;
        }
        .paginacion a, .paginacion span {
            margin: 0 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
        }
        .paginacion span {
            background-color: #007bff;
            color: #fff;
        }
        .registros-por-pagina {
            text-align: center;
            margin-bottom: 20px;
        }
        .formulario_reporte_ot{
          margin-right:20px;  
        }

    </style>
</head>
<body id="historial_oc">

    <div class="principal">
        <section>
            <h1>Historial OC</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="req">Req:</label>
                    <input class="formulario_reporte_ot" type="text" id="req" name="req" value="<?php echo isset($_GET['req']) ? $_GET['req'] : ''; ?>" placeholder="Buscar por Req">

                    <label for="proveedor">proveedor:</label>
                    <input  class="formulario_reporte_ot" type="text" id="proveedor" name="proveedor" value="<?php echo isset($_GET['proveedor']) ? $_GET['proveedor'] : ''; ?>" placeholder="proveedor">

                    <label for="oc">OC:</label>
                    <input class="formulario_reporte_ot" type="text" id="oc" name="oc" value="<?php echo isset($_GET['oc']) ? $_GET['oc'] : ''; ?>" placeholder="OC">
                    
                    <label for="fecha_solicitud">Solicitud entre</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : ''; ?>" placeholder="Fecha solicitud">
                    
                    <label for="fecha_solicitudfin">y :</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo isset($_GET['fecha_solicitudfin']) ? $_GET['fecha_solicitudfin'] : ''; ?>" placeholder="Fecha solicitud fin">

                    <label for="pago">Pendientes pago:</label>
                    <input class="formulario_reporte_ot" type="checkbox" id="pago" name="pago" value="1" <?php echo isset($_GET['pago']) ? 'checked' : ''; ?>>
                    
                    <input type="hidden" name="pestaña" value="historial_oc">
                    
                    <input type="submit" value="Buscar">
                </form>
            </div>

            <div class="registros-por-pagina">
                <form method="GET" action="">
                    <label for="registros_por_pagina">Registros por pagina:</label>
                    <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                        <option value="10" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 10) echo 'selected'; ?>>10</option>
                        <option value="20" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 20) echo 'selected'; ?>>20</option>
                        <option value="50" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 50) echo 'selected'; ?>>50</option>
                        <option value="100" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 100) echo 'selected'; ?>>100</option>
                    </select>
                    <?php
                    // Keep other filters when changing records per page
                    if (!empty($_GET['req'])) echo '<input type="hidden" name="req" value="' . $_GET['req'] . '">';
                    if (!empty($_GET['proveedor'])) echo '<input type="hidden" name="proveedor" value="' . $_GET['proveedor'] . '">';
                    if (!empty($_GET['oc'])) echo '<input type="hidden" name="oc" value="' . $_GET['oc'] . '">';
                    if (!empty($_GET['fecha_solicitud'])) echo '<input type="hidden" name="fecha_solicitud" value="' . $_GET['fecha_solicitud'] . '">';
                    if (!empty($_GET['fecha_solicitudfin'])) echo '<input type="hidden" name="fecha_solicitudfin" value="' . $_GET['fecha_solicitudfin'] . '">';
                    if (isset($_GET['pestaña'])) echo '<input type="hidden" name="pestaña" value="' . $_GET['pestaña'] . '">';
                    if (isset($_GET['pago'])) echo '<input type="hidden" name="pago" value="' . $_GET['pago'] . '">';
                    ?>
                </form>
            </div>

            <?php
            // Display active filters
            $activeFilters = [];
            if (!empty($_GET['req'])) $activeFilters[] = "req: " . htmlspecialchars($_GET['req']);
            if (!empty($_GET['proveedor'])) $activeFilters[] = "proveedor: " . htmlspecialchars($_GET['proveedor']);
            if (!empty($_GET['oc'])) $activeFilters[] = "OC: " . htmlspecialchars($_GET['oc']);
            if (!empty($_GET['fecha_solicitud'])) $activeFilters[] = "Fecha solicitud: " . htmlspecialchars($_GET['fecha_solicitud']);
            if (!empty($_GET['fecha_solicitudfin'])) $activeFilters[] = "Fecha solicitud fin: " . htmlspecialchars($_GET['fecha_solicitudfin']);
            if (isset($_GET['pestaña'])) $activeFilters[] = "Pestaña: " . htmlspecialchars($_GET['pestaña']);
            if (isset($_GET['pago']) && $_GET['pago'] == 1) {
                $activeFilters[] = "Pendientes pago";
            }
            ?>

            <table border="1">
               <?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>
    .centrado {
        text-align: center;
    }
    .table-cell {
        position: relative;
        width: 50%;
        justify-content: right;
    }
</style>";

$req = isset($_GET['req']) ? $conexion->real_escape_string($_GET['req']) : '';
$proveedor = isset($_GET['proveedor']) ? $conexion->real_escape_string($_GET['proveedor']) : '';
$oc = isset($_GET['oc']) ? $conexion->real_escape_string($_GET['oc']) : '';
$fecha_solicitud = isset($_GET['fecha_solicitud']) ? $conexion->real_escape_string($_GET['fecha_solicitud']) : '';
$fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $conexion->real_escape_string($_GET['fecha_solicitudfin']) : '';
$pestaña = isset($_GET['pestaña']) ? $conexion->real_escape_string($_GET['pestaña']) : 'historial_oc';
$pago = isset($_GET['pago']) ? (int)$_GET['pago'] : 0;

$registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$sql_oc = "
    SELECT 
        id,
        IFNULL(oc, '') AS oc,
        IFNULL(proveedor, '') AS proveedor,
        IFNULL(responsable, '') AS responsable,
        IFNULL(fecha_solicitud, '') AS fecha_solicitud,
        IFNULL(llegada_estimada, '') AS fecha_llegada,
        IFNULL(moneda, '') AS moneda,
        IFNULL(total_pesos, 0) AS total_pesos,
        pago_estimado as pago_estimado,
        pago
    FROM orden_compra WHERE 1=1";

$conditions = [];
$params = [];
$types = '';

if (!empty($req)) {
    $conditions[] = "id = ?";
    $params[] = "$req";
    $types .= 'i';
}
if (!empty($proveedor)) {
    $conditions[] = "proveedor LIKE ?";
    $params[] = "%$proveedor%";
    $types .= 's';
}
if ($pago==1){
    $conditions[] = "pago is null";
}
if (!empty($oc)) {
    $conditions[] = "oc = ?";
    $params[] = $oc;
    $types .= 's'; // Cambié 'i' por 's' ya que OC probablemente sea una cadena
}

if (!empty($fecha_solicitud) && !empty($fecha_solicitudfin)) {
    $conditions[] = "fecha_solicitud BETWEEN ? AND ?";
    $params[] = $fecha_solicitud;
    $params[] = $fecha_solicitudfin;
    $types .= 'ss';
}

if (!empty($conditions)) {
    $sql_oc .= " AND " . implode(" AND ", $conditions);
}
if ($pago==1){
    $sql_oc .= " ORDER BY pago_estimado, id DESC";
}
else{
    $sql_oc .= " ORDER BY id DESC";
}

$stmt = $conexion->prepare($sql_oc);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $stmt->store_result();
    $total_registros = $stmt->num_rows;

    $sql_oc .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $registros_por_pagina;
    $types .= 'ii';

    $stmt = $conexion->prepare($sql_oc);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $oc,$proveedor, $responsable,$fecha_solicitud,$fecha_llegada,$moneda,$total_pesos, $pago_estimado, $pago);


    if ($stmt->num_rows > 0) {
        echo "<table border='1'>
            <tr>
                <th>Req</th>
                <th>Numero OC</th>
                <th>Proveedor</th>
                <th>Responsable</th>
                <th>Fecha solicitud</th>
                <th>Fecha llegada</th>
                <th>Moneda</th>
                <th>Total en pesos</th>
                <th>Pago</th>
            </tr>";

        while ($stmt->fetch()) {
    echo "<tr>
            <td><a href='$header_loc.php?pestaña=editar_oc&header_loc=$header_loc&id=" . $id . "'>" . $id . "</a></td>
            <td>" . htmlspecialchars($oc) . "</td>
            <td>" . htmlspecialchars($proveedor) . "</td>
            <td>" . htmlspecialchars($responsable) . "</td>
            <td>" . htmlspecialchars($fecha_solicitud) . "</td>
            <td>" . htmlspecialchars($fecha_llegada) . "</td>
            <td>" . htmlspecialchars($moneda) . "</td>
            <td>$" . number_format(($total_pesos), 2, '.', ',') . "</td>
            <td>";
                $texto = htmlspecialchars($pago ?? $pago_estimado ?? ' ', ENT_QUOTES, 'UTF-8') ;
                if (!empty($pago)) {
                    $texto .= ' &#9989';
                }
                echo $texto ?: ' ';
            echo "</td>
        </tr>";
}

        
        echo "</table>";
    } else {
        echo "No se encontraron resultados.";
    }

    $total_paginas = ceil($total_registros / $registros_por_pagina);
    $max_links = 6;
    $start_link = max(1, $pagina_actual - floor($max_links / 2));
    $end_link = min($total_paginas, $start_link + $max_links - 1);

   echo "<div class='paginacion'>";
if ($pagina_actual > 1) {
    echo "<a href='?pagina=1&registros_por_pagina=$registros_por_pagina&pestaña=historial_oc";
    // Añadir todos los parámetros de filtro en la URL
    foreach (['id', 'proveedor', 'oc', 'fecha_solicitud','pago','pestaña'] as $filter) {
        if (!empty($_GET[$filter])) {
            echo '&' . $filter . '=' . urlencode($_GET[$filter]);
        }
    }
    echo "'>Primera</a>";
    echo "<a href='?pagina=" . ($pagina_actual - 1) . "&registros_por_pagina=$registros_por_pagina&pestaña=historial_oc";
    // Añadir todos los parámetros de filtro en la URL
    foreach (['id', 'proveedor', 'oc', 'fecha_solicitud','pago','pestaña'] as $filter) {
        if (!empty($_GET[$filter])) {
            echo '&' . $filter . '=' . urlencode($_GET[$filter]);
        }
    }
    echo "'>&laquo; Anterior</a>";
}
for ($i = $start_link; $i <= $end_link; $i++) {
    if ($i == $pagina_actual) {
        echo "<span>$i</span>";
    } else {
        echo "<a href='?pagina=$i&registros_por_pagina=$registros_por_pagina&pestaña=historial_oc";
        // Añadir todos los parámetros de filtro en la URL
        foreach (['id', 'proveedor', 'oc', 'fecha_solicitud','pago','pestaña'] as $filter) {
            if (!empty($_GET[$filter])) {
                echo '&' . $filter . '=' . urlencode($_GET[$filter]);
            }
        }
        echo "'>$i</a>";
    }
}
if ($end_link < $total_paginas) {
    echo "<a href='?pagina=" . ($pagina_actual + 1) . "&registros_por_pagina=$registros_por_pagina&pestaña=historial_oc";
    // Añadir todos los parámetros de filtro en la URL
    foreach (['id', 'proveedor', 'oc', 'fecha_solicitud','pago','pestaña'] as $filter) {
        if (!empty($_GET[$filter])) {
            echo '&' . $filter . '=' . urlencode($_GET[$filter]);
        }
    }
    echo "'>Siguiente &raquo;</a>";
    echo "<a href='?pagina=$total_paginas&registros_por_pagina=$registros_por_pagina&pestaña=historial_oc";
    // Añadir todos los parámetros de filtro en la URL
    foreach (['id', 'proveedor', 'oc', 'fecha_solicitud','pago','pestaña'] as $filter) {
        if (!empty($_GET[$filter])) {
            echo '&' . $filter . '=' . urlencode($_GET[$filter]);
        }
    }
    echo "'>Ultima</a>";
}
echo "</div>";

}

$conexion->close();
?>
            </table>
        </section>
    </div>
</body>
</html>

