<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facturas pendientes</title>
    <style>
        .dinero {
            text-align: right;
        }
    </style>
</head>
<body id="facturas_pendientes">

    <div class="">
        <section>
            <h1>Facturas pendientes</h1>
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
                    <label for="fecha_solicitud">Entre:</label>
                    <input type="date" id="fecha_solicitud" name="fecha_solicitud" value="<?php echo htmlspecialchars($_GET['fecha_solicitud'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha solicitud">
                    
                    <label for="fecha_solicitudfin">y:</label>
                    <input type="date" id="fecha_solicitudfin" name="fecha_solicitudfin" value="<?php echo htmlspecialchars($_GET['fecha_solicitudfin'] ?? '', ENT_QUOTES); ?>" placeholder="Fecha solicitud fin">
                    
                    <label for="cliente">Cliente:</label>
                    <input type="text" id="cliente" name="cliente" value="<?php echo htmlspecialchars($_GET['cliente'] ?? '', ENT_QUOTES); ?>" placeholder="Cliente">
                    
                    <input type="hidden" name="pestaña" value="facturas_pendientes">
                    <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc, ENT_QUOTES); ?>">

                    <input type="submit" value="Buscar">
                </form>
            </div>

            <div class="registros-por-pagina">
                <!-- No se necesita selección de registros por página -->
            </div>

           

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Alta Sistema</th>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>OT</th>
                        <th>Valor</th>
                        
                        <th>Moneda</th>
                        <th>Pedido</th>
                        <th>Fecha Pago</th>
                        <th>Descripción</th>
                        <th>Observaciones</th>
                        <th>Portal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ot = isset($_GET['ot']) ? $conexion->real_escape_string($_GET['ot']) : '';
                    $folio = isset($_GET['folio']) ? $conexion->real_escape_string($_GET['folio']) : '';
                    $pedido = isset($_GET['pedido']) ? $conexion->real_escape_string($_GET['pedido']) : '';
                    $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $conexion->real_escape_string($_GET['fecha_solicitud']) : '';
                    $fecha_solicitudfin = isset($_GET['fecha_solicitudfin']) ? $conexion->real_escape_string($_GET['fecha_solicitudfin']) : '';
                    $cliente = isset($_GET['cliente']) ? $conexion->real_escape_string($_GET['cliente']) : '';
                    $estado = isset($_GET['estado']) ? $conexion->real_escape_string($_GET['estado']) : '';

                    // Preparar consulta para obtener las Facturas pendientes
                    $sql_facturas = "SELECT 
                                        facturas.id as factura_id,
                                        facturas.folio as factura_folio,
                                        facturas.valor_pesos as factura_valor,
                                        facturas.moneda as factura_moneda,
                                        facturas.alta_sistema,
                                        facturas.fecha_pago,
                                        pedido.descripcion,
                                        pedido.cliente,
                                        facturas.ot,
                                        facturas.descripcion as desc_fac,
                                        facturas.observaciones,
                                        facturas.portal
                                    FROM 
                                        facturas 
                                    LEFT JOIN 
                                        pedido ON pedido.id = facturas.id_pedido 
                                    WHERE 
                                        facturas.fecha_pago IS NULL"; // Base query, conditions will be appended dynamically


                    $conditions = [];
                    $params = [];
                    $types = '';

                    if (!empty($ot)) {
                        $conditions[] = "facturas.ot LIKE ?";
                        $params[] = "%$ot%";
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

                    // No hay paginación, por lo tanto eliminamos LIMIT y OFFSET
                    $sql_facturas .= " group by facturas.id ORDER BY facturas.alta_sistema DESC";

                    $stmt = $conexion->prepare($sql_facturas);

                    if ($stmt === false) {
                        die("Error en la preparación de la consulta: " . $conexion->error);
                    }

                    // Verificar que el número de parámetros coincida con los placeholders
                    if (count($params) !== strlen($types)) {
                        die("Mismatch entre número de parámetros y placeholders");
                    }

                    if ($types) {
                        $stmt->bind_param($types, ...$params);
                    }

                    if (!$stmt->execute()) {
                        die("Error en la ejecución de la consulta: " . $stmt->error);
                    }

                    $stmt->store_result();
                    $stmt->bind_result($factura_id, $factura_folio, $factura_valor, $factura_moneda, $alta_sistema, $fecha_pago, $pedido, $cliente, $ot, $descripcion, $observaciones,$portal);

                    while ($stmt->fetch()) {
                        echo "<tr>
                            <td><a href='$header_loc.php?pestaña=editar_factura&header_loc=$header_loc&id=".$factura_id."'>{$factura_id}</a></td>
                            <td>{$alta_sistema}</td>
                            <td>{$factura_folio}</td>
                            <td>{$cliente}</td>
                            <td>{$ot}</td>
                            <td class='dinero'>$" . number_format($factura_valor, 2, '.', ',') . "</td>
                            <td>{$factura_moneda}</td>
                            <td><a href='../documentos/finanzas/ot/{$ot}/{$pedido}.pdf' target='_blank'>{$pedido}</a></td>
                            <td>{$fecha_pago}</td>
                            <td>{$descripcion}</td>
                            <td>{$observaciones}</td>
                            <td>{$portal}</td>
                        </tr>";
                    }

                    $stmt->close();

                    $conexion->close();
                    ?>
                </tbody>
            </table>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var rows = document.querySelectorAll("tr[data-href]");
                rows.forEach(function(row) {
                    row.addEventListener("click", function() {
                        window.location.href = row.getAttribute("data-href");
                    });
                });
            });
            </script>

        </section>
    </div>
</body>
</html>

