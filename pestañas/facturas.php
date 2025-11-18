<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facturas</title>
<style>
    table {
        margin-left:5%;
    }
</style>
</head>
<body id="facturas">
    <div class="principal">
        <section>
            <form method="get" action="">
            <input type="hidden" name="pestaña" value="alta_de_factura">
            <button type="submit" style="padding: 10px 20px; border: none; color: white; background-color: #007bff; border-radius: 4px; cursor: pointer; font-size: 14px;">+ Registrar factura</button>
        </form>
            <h1 class="text-2xl font-bold text-blue-600 text-center">Facturas</h1>
            <br>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="ot">OT:</label>
                    <input type="text" id="ot" name="ot" value="<?php echo htmlspecialchars($_GET['ot'] ?? '', ENT_QUOTES); ?>" placeholder="Buscar por OT">

                    <label for="folio">Folio:</label>
                    <input type="text" id="folio" name="folio" value="<?php echo htmlspecialchars($_GET['folio'] ?? '', ENT_QUOTES); ?>" placeholder="Folio">

                    <label for="pedido">Pedido:</label>
                    <input type="text" id="pedido" name="pedido" value="<?php echo htmlspecialchars($_GET['pedido'] ?? '', ENT_QUOTES); ?>" placeholder="Pedido">
                    
                    <label for="estado">Estado:</label>
                    <select name="estado" id="estado">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="facturado" <?php echo (isset($_GET['estado']) && $_GET['estado'] === 'facturado') ? 'selected' : ''; ?>>Facturado</option>
                    </select>
                    <br><br>
                    <label for="fecha_solicitud">Alta entre:</label>
                    <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($_GET['fecha_solicitud'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha solicitud">
                    
                    <label for="fecha_solicitudfin">y:</label>
                    <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($_GET['fecha_solicitudfin'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha solicitud fin">
                    
                    <label for="cliente">Cliente:</label>
                    <input type="text" id="cliente" name="cliente" value="<?php echo htmlspecialchars($_GET['cliente'] ?? '', ENT_QUOTES); ?>" placeholder="Cliente">
                    
                   <label for="fecha_pago">Pago entre:</label>
                    <input type="date" id="fecha_pago" name="fecha_pago" value="<?php echo htmlspecialchars($_GET['fecha_pago'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha Pago">
                    
                    <label for="fecha_pagofin">y:</label>
                    <input type="date" id="fecha_pagofin" name="fecha_pagofin" value="<?php echo htmlspecialchars($_GET['fecha_pagofin'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha pago fin">
                    
                    <input type="hidden" name="pestaña" value="facturas">

                    <input type="submit" value="Buscar">
                </form>
            </div>

            <div class="registros-por-pagina">
                <form method="GET" action="">
                    <label for="registros_por_pagina">Registros por página:</label>
                    <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                        <option value="10" <?php echo (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="20" <?php echo (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 20) ? 'selected' : ''; ?>>20</option>
                        <option value="50" <?php echo (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 50) ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 100) ? 'selected' : ''; ?>>100</option>
                    </select>
                    <input type="hidden" name="pestaña" value="facturas">
                    <?php
                    $filters = ['ot', 'folio', 'pedido', 'fecha_solicitud', 'fecha_solicitudfin', 'cliente','fecha_pago', 'fecha_pagofin', 'estado'];
                    foreach ($filters as $filter) {
                        if (!empty($_GET[$filter])) {
                            echo '<input type="hidden" name="' . $filter . '" value="' . htmlspecialchars($_GET[$filter], ENT_QUOTES) . '">';
                        }
                    }
                    ?>
                </form>
            </div>

            <?php
            $activeFilters = [];
            foreach ($filters as $filter) {
                if (!empty($_GET[$filter])) {
                    $activeFilters[] = ucfirst($filter) . ": " . htmlspecialchars($_GET[$filter], ENT_QUOTES);
                }
            }

            if (!empty($activeFilters)) {
                echo "<div class='active-filters'><strong>Filtros activos:</strong> " . implode(", ", $activeFilters) . "</div>";
            }
            ?>

            <table>
            <?php
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
            $folio = isset($_GET['folio']) ? $conexion->real_escape_string($_GET['folio']) : '';
            $pedido = isset($_GET['pedido']) ? $conexion->real_escape_string($_GET['pedido']) : '';
            $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $conexion->real_escape_string($_GET['fecha_solicitud']) : '';
            $fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $conexion->real_escape_string($_GET['fecha_solicitudfin']) : '';
            $cliente = isset($_GET['cliente']) ? $conexion->real_escape_string($_GET['cliente']) : '';
            $estado = isset($_GET['estado']) ? $conexion->real_escape_string($_GET['estado']) : '';
            $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
            $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina_actual - 1) * $registros_por_pagina;
            $fecha_pago= isset($_GET['fecha_pago']) ? $conexion->real_escape_string($_GET['fecha_pago']) : '';
                                $fecha_pagofin = isset($_GET['fecha_pagofin']) ? $conexion->real_escape_string($_GET['fecha_pagofin']) : '';

            // Preparar consulta para obtener las facturas
            $sql_facturas = "SELECT 
                                facturas.id as factura_id,
                                facturas.folio as factura_folio,
                                facturas.valor_pesos as factura_valor,
                                facturas.alta_sistema,
                                facturas.fecha_pago,
                                pedido.descripcion as pedido,
                                pedido.cliente as cliente,
                                ot.ot as ot
                            FROM 
                                facturas
                                LEFT JOIN pedido ON facturas.id_pedido = pedido.id
                                LEFT JOIN ot ON  pedido.id = ot.id_pedido
                                
                            WHERE 1=1
                            ";

            $conditions = [];
            $params = [];
            $types = '';

            if (!empty($ot)) {
                $conditions[] = "pedido.ot = ?";
                $params[] = "$ot";
                $types .= 's';
            }

            if (!empty($folio)) {
                $conditions[] = "facturas.folio LIKE ?";
                $params[] = "%$folio%";
                $types .= 's';
            }

            if (!empty($pedido)) {
                $conditions[] = "pedido.descripcion LIKE ?";
                $params[] = "%$pedido%";
                $types .= 's';
            }

            if (!empty($fecha_solicitud) && !empty($fecha_solicitudfin)) {
                $conditions[] = "facturas.alta_sistema BETWEEN ? AND ?";
                $params[] = $fecha_solicitud;
                $params[] = $fecha_solicitudfin;
                $types .= 'ss';
            }

            if (!empty($fecha_pago) && !empty($fecha_pagofin)) {
                $conditions[] = "facturas.fecha_pago BETWEEN ? AND ?";
                $params[] = $fecha_pago;
                $params[] = $fecha_pagofin;
                $types .= 'ss';
            }

            if (!empty($cliente)) {
                $conditions[] = "pedido.cliente LIKE ?";
                $params[] = "%$cliente%";
                $types .= 's';
            }

            if ($estado === 'pendiente') {
                $conditions[] = "facturas.fecha_pago IS NULL";
            } elseif ($estado === 'facturado') {
                $conditions[] = "facturas.fecha_pago IS NOT NULL";
            }

            if (!empty($conditions)) {
                $sql_facturas .= " AND " . implode(" AND ", $conditions);
            }

            $sql_facturas .= " group by facturas.id ORDER BY facturas.folio DESC LIMIT ? OFFSET ?";
            $params[] = $registros_por_pagina;
            $params[] = $offset;
            $types .= 'ii';

            $stmt = $conexion->prepare($sql_facturas);

            if ($stmt === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            // Verify that the number of parameters matches the placeholders
            if (count($params) !== strlen($types)) {
                die("Mismatch between number of parameters and placeholders");
            }

            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                die("Error en la ejecución de la consulta: " . $stmt->error);
            }

            $stmt->bind_result($factura_id, $factura_folio, $factura_valor, $alta_sistema, $fecha_pago, $pedido, $cliente, $ot);

            echo "<thead>
                    <tr>
                        <th>ID</th>
                        <th>OT</th>
                        <th>Folio</th>
                        <th>Valor</th>
                        <th>Fecha Alta Sistema</th>
                        <th>Fecha Pago</th>
                        <th>Pedido</th>
                        <th>Cliente</th>
                    </tr>
                </thead>
                <tbody>";

            while ($stmt->fetch()) {
                echo "<tr>
                    <td><a class='liga' href='$header_loc.php?pestaña=editar_factura&header_loc=$header_loc&id=".$factura_id."'>{$factura_id}</a></td>
                    <td>{$ot}</td>
                    <td>{$factura_folio}</td>
                    <td class='dinero'>{$factura_valor}</td>
                    <td>{$alta_sistema}</td>
                    <td>{$fecha_pago}</td>
                    <td>{$pedido}</td>
                    <td>{$cliente}</td>
                </tr>";
            }

            echo "</tbody></table>";

            $stmt->close();

            // Preparar consulta para contar los registros totales
            $sql_count = "SELECT COUNT(*) FROM facturas
                                LEFT JOIN pedido ON facturas.id_pedido = pedido.id
                                LEFT JOIN ot ON pedido.id = ot.id_pedido WHERE 1=1";
            if (!empty($conditions)) {
                $sql_count .= " AND " . implode(" AND ", $conditions);
            }

            $stmt_count = $conexion->prepare($sql_count);

            if ($stmt_count === false) {
                die("Error en la preparación de la consulta: " . $conexion->error);
            }

            $count_params = array_slice($params, 0, count($params) - 2);
            $count_types = substr($types, 0, strlen($types) - 2);

            if (!empty($count_params)) {
                $stmt_count->bind_param($count_types, ...$count_params);
            }

            if (!$stmt_count->execute()) {
                die("Error en la ejecución de la consulta: " . $stmt_count->error);
            }

            $stmt_count->bind_result($total_registros);
            $stmt_count->fetch();
            $stmt_count->close();

            $total_paginas = ceil($total_registros / $registros_por_pagina);

            // Build the URL without the page parameter
            $current_url = strtok($_SERVER["REQUEST_URI"], '?');
            $query_params = $_GET;
            unset($query_params['pagina']);

            echo "<div class='paginacion'>";
            if ($pagina_actual > 1) {
                $query_params['pagina'] = 1;
                echo "<a href='$current_url?" . http_build_query($query_params) . "'>&laquo; Primera</a>";
                $query_params['pagina'] = $pagina_actual - 1;
                echo "<a href='$current_url?" . http_build_query($query_params) . "'>&lsaquo; Anterior</a>";
            }

            $range = 2;
            for ($i = max(1, $pagina_actual - $range); $i <= min($pagina_actual + $range, $total_paginas); $i++) {
                $query_params['pagina'] = $i;
                if ($i === $pagina_actual) {
                    echo "<span>$i</span>";
                } else {
                    echo "<a href='$current_url?" . http_build_query($query_params) . "'>$i</a>";
                }
            }

            if ($pagina_actual < $total_paginas) {
                $query_params['pagina'] = $pagina_actual + 1;
                echo "<a href='$current_url?" . http_build_query($query_params) . "'>Siguiente &rsaquo;</a>";
                $query_params['pagina'] = $total_paginas;
                echo "<a href='$current_url?" . http_build_query($query_params) . "'>Última &raquo;</a>";
            }
            echo "</div>";

            $conexion->close();
            ?>
        </section>
    </div>
</body>
</html>

