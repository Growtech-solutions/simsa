<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historico Compras</title>
    <style>

        table {
            margin-left:2%;
        }


        
    </style>
</head>
<body id="historial_de_compras">

    <div class="principal">
        <section>
            <h1>Compras</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="ot">OT:</label>
                    <input class="formulario_reporte_ot" type="text" id="ot" name="ot" value="<?php echo isset($_GET['ot']) ? $_GET['ot'] : ''; ?>" placeholder="Buscar por OT">

                    <label for="descripcion">Descripcion:</label>
                    <input  class="formulario_reporte_ot" type="text" id="descripcion" name="descripcion" value="<?php echo isset($_GET['descripcion']) ? $_GET['descripcion'] : ''; ?>" placeholder="Descripcion">

                    <label for="descripcion2"> y: </label>
                    <input class="formulario_reporte_ot" type="text" id="descripcion2" name="descripcion2" value="<?php echo isset($_GET['descripcion2']) ? $_GET['descripcion2'] : ''; ?>" placeholder="Descripcion2">

                    <label for="oc">OC:</label>
                    <input class="formulario_reporte_ot" type="text" id="oc" name="oc" value="<?php echo isset($_GET['oc']) ? $_GET['oc'] : ''; ?>" placeholder="OC">
                    
                    <br><br>
                    <label for="fecha_solicitud">Solicitud entre</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : ''; ?>" placeholder="Fecha solicitud">
                    
                    <label for="fecha_solicitudfin">y :</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo isset($_GET['fecha_solicitudfin']) ? $_GET['fecha_solicitudfin'] : ''; ?>" placeholder="Fecha solicitud fin">
                                        
                    <label for="provedor">Proveedor:</label>
                    <input class="formulario_reporte_ot" type="text" id="provedor" name="provedor" value="<?php echo isset($_GET['provedor']) ? $_GET['provedor'] : ''; ?>" placeholder="Proveedor">
                    
                    <label for="pago">Pendientes pago:</label>
                    <input class="formulario_reporte_ot" type="checkbox" id="pago" name="pago" value="1" <?php echo isset($_GET['pago']) ? 'checked' : ''; ?>>

                    <input type="hidden" name="pestaña" value="historial_de_compras">

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
                    if (!empty($_GET['ot'])) echo '<input type="hidden" name="ot" value="' . $_GET['ot'] . '">';
                    if (!empty($_GET['descripcion'])) echo '<input type="hidden" name="descripcion" value="' . $_GET['descripcion'] . '">';
                    if (!empty($_GET['descripcion2'])) echo '<input type="hidden" name="descripcion2" value="' . $_GET['descripcion2'] . '">';
                    if (!empty($_GET['oc'])) echo '<input type="hidden" name="oc" value="' . $_GET['oc'] . '">';
                    if (!empty($_GET['fecha_solicitud'])) echo '<input type="hidden" name="fecha_solicitud" value="' . $_GET['fecha_solicitud'] . '">';
                    if (!empty($_GET['fecha_solicitudfin'])) echo '<input type="hidden" name="fecha_solicitudfin" value="' . $_GET['fecha_solicitudfin'] . '">';
                    if (!empty($_GET['provedor'])) echo '<input type="hidden" name="provedor" value="' . $_GET['provedor'] . '">';
                    if (!empty($_GET['pestaña'])) echo '<input type="hidden" name="pestaña" value="' . $_GET['pestaña'] . '">';
                    if (!empty($_GET['pago'])) echo '<input type="hidden" name="pago" value="' . $_GET['pago'] . '">';
                    ?>
                </form>
            </div>

            <?php
            // Display active filters
            $activeFilters = [];
            if (!empty($_GET['ot'])) $activeFilters[] = "OT: " . htmlspecialchars($_GET['ot'] ?? '');
            if (!empty($_GET['descripcion'])) $activeFilters[] = "Descripcion: " . htmlspecialchars($_GET['descripcion'] ?? '');
            if (!empty($_GET['descripcion2'])) $activeFilters[] = "Descripcion2: " . htmlspecialchars($_GET['descripcion2'] ?? '');
            if (!empty($_GET['oc'])) $activeFilters[] = "OC: " . htmlspecialchars($_GET['oc'] ?? '');
            if (!empty($_GET['fecha_solicitud'])) $activeFilters[] = "Fecha solicitud: " . htmlspecialchars($_GET['fecha_solicitud'] ?? '');
            if (!empty($_GET['fecha_solicitudfin'])) $activeFilters[] = "Fecha solicitud fin: " . htmlspecialchars($_GET['fecha_solicitudfin'] ?? '');
            if (!empty($_GET['provedor'])) $activeFilters[] = "Proveedor: " . htmlspecialchars($_GET['provedor'] ?? '');
            if (!empty($_GET['pestaña'])) $activeFilters[] = "Pestaña: " . htmlspecialchars($_GET['pestaña'] ?? '');
            if (!empty($_GET['pago'])) echo '<input type="hidden" name="pago" value="' . $_GET['pago'] . '">';

            if (!empty($activeFilters)) {
                echo "<div class='active-filters'><strong>Filtros activos:</strong> " . implode(", ", $activeFilters) . "</div>";
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

$ot = isset($_GET['ot']) ? $conexion->real_escape_string($_GET['ot']) : '';
$descripcion = isset($_GET['descripcion']) ? $conexion->real_escape_string($_GET['descripcion']) : '';
$descripcion2 = isset($_GET['descripcion2']) ? $conexion->real_escape_string($_GET['descripcion2']) : '';
$oc = isset($_GET['oc']) ? $conexion->real_escape_string($_GET['oc']) : '';
$fecha_solicitud = isset($_GET['fecha_solicitud']) ? $conexion->real_escape_string($_GET['fecha_solicitud']) : '';
$fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $conexion->real_escape_string($_GET['fecha_solicitudfin']) : '';
$responsable = isset($_GET['responsable']) ? $conexion->real_escape_string($_GET['responsable']) : '';
$provedor = isset($_GET['provedor']) ? $conexion->real_escape_string($_GET['provedor']) : '';
$pago = isset($_GET['pago']) ? $conexion->real_escape_string($_GET['pago']) : 0;

$registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

$sql_compras = "
    SELECT 
        c2.id, c2.ot, c2.responsable, c2.cantidad, c2.descripcion, c2.unidad,
        c2.id_oc, c2.precio_unitario, c2.moneda,oc.pago,
        oc.oc AS numero_oc, oc.proveedor, oc.fecha_solicitud, oc.pago_estimado
    FROM compras c2
    LEFT JOIN orden_compra oc ON c2.id_oc = oc.id
    WHERE 1=1";

$conditions = [];
$params = [];
$types = '';

if (!empty($ot)) {
    $conditions[] = "c2.ot LIKE ?";
    $params[] = "%$ot%";
    $types .= 's';
}
if (!empty($descripcion)) {
    $conditions[] = "c2.descripcion LIKE ?";
    $params[] = "%$descripcion%";
    $types .= 's';
}
if (!empty($descripcion2)) {
    $conditions[] = "c2.descripcion LIKE ?";
    $params[] = "%$descripcion2%";
    $types .= 's';
}
if (!empty($oc)) {
    $conditions[] = "oc.oc LIKE ?";
    $params[] = "%$oc%";
    $types .= 's';
}
if ($pago==1){
    $conditions[] = "oc.pago is null";
}
if (!empty($fecha_solicitud) && !empty($fecha_solicitudfin)) {
    $conditions[] = "oc.fecha_solicitud BETWEEN ? AND ?";
    $params[] = $fecha_solicitud;
    $params[] = $fecha_solicitudfin;
    $types .= 'ss';
}
if (!empty($responsable)) {
    $conditions[] = "c2.responsable LIKE ?";
    $params[] = "%$responsable%";
    $types .= 's';
}
if (!empty($provedor)) {
    $conditions[] = "oc.proveedor LIKE ?";
    $params[] = "%$provedor%";
    $types .= 's';
}
if (!empty($conditions)) {
    $sql_compras .= " AND " . implode(" AND ", $conditions);
}
$sql_compras .= " ORDER BY id DESC";

$stmt = $conexion->prepare($sql_compras);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $stmt->store_result();
    $total_registros = $stmt->num_rows;

    $sql_compras .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $registros_por_pagina;
    $types .= 'ii';

    $stmt = $conexion->prepare($sql_compras);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $ot, $responsable, $cantidad, $descripcion, $unidad, $id_oc, $precio_unitario, $moneda,$pago, $numero_oc, $proveedor, $fecha_solicitud,$pago_estimado);

    if ($stmt->num_rows > 0) {
        echo "<table border='1'>
            <tr>
                <th>Fecha Solicitud</th>
                <th>Numero OC</th>
                <th>Proveedor</th>
                <th>OT</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Descripcion</th>
                <th>Precio Unitario</th>
                <th>Moneda</th>
                <th>Total</th>
                <th>Req</th>
                <th>Responsable</th>
                <th>Fecha Pago</th>
            </tr>";

        while ($stmt->fetch()) {
            echo "<tr>
                    <td>" . htmlspecialchars($fecha_solicitud ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($numero_oc ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($proveedor ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($ot ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($cantidad ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($unidad ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($descripcion ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($precio_unitario ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($moneda ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>$" . number_format(($precio_unitario * $cantidad), 2, '.', ',') . "</td>
                    <td>" . htmlspecialchars($id ?? '', ENT_QUOTES, 'UTF-8') . "</td>
                    <td>" . htmlspecialchars($responsable ?? '', ENT_QUOTES, 'UTF-8') . "</td>
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
        echo "<a href='?pagina=1&registros_por_pagina=$registros_por_pagina";
        // Añadir todos los parámetros de filtro en la URL
        foreach (['ot', 'descripcion', 'descripcion2', 'oc', 'fecha_solicitud', 'fecha_solicitudfin', 'provedor'] as $filter) {
            if (!empty($_GET[$filter])) {
                echo '&' . $filter . '=' . urlencode($_GET[$filter]);
            }
        }
        echo "'>Primera</a>";
        echo "<a href='?pagina=" . ($pagina_actual - 1) . "&registros_por_pagina=$registros_por_pagina";
        // Añadir todos los parámetros de filtro en la URL
        foreach (['ot', 'descripcion', 'descripcion2', 'oc', 'fecha_solicitud', 'fecha_solicitudfin', 'provedor'] as $filter) {
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
            echo "<a href='?pagina=$i&registros_por_pagina=$registros_por_pagina";
            // Añadir todos los parámetros de filtro en la URL
            foreach (['ot', 'descripcion', 'descripcion2', 'oc', 'fecha_solicitud', 'fecha_solicitudfin', 'provedor'] as $filter) {
                if (!empty($_GET[$filter])) {
                    echo '&' . $filter . '=' . urlencode($_GET[$filter]);
                }
            }
            echo "'>$i</a>";
        }
    }
    if ($end_link < $total_paginas) {
        echo "<a href='?pagina=" . ($pagina_actual + 1) . "&registros_por_pagina=$registros_por_pagina";
        // Añadir todos los parámetros de filtro en la URL
        foreach (['ot', 'descripcion', 'descripcion2', 'oc', 'fecha_solicitud', 'fecha_solicitudfin', 'provedor'] as $filter) {
            if (!empty($_GET[$filter])) {
                echo '&' . $filter . '=' . urlencode($_GET[$filter]);
            }
        }
        echo "'>Siguiente &raquo;</a>";
        echo "<a href='?pagina=$total_paginas&registros_por_pagina=$registros_por_pagina";
        // Añadir todos los parámetros de filtro en la URL
        foreach (['ot', 'descripcion', 'descripcion2', 'oc', 'fecha_solicitud', 'fecha_solicitudfin', 'provedor'] as $filter) {
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