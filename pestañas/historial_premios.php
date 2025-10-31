<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historial de Premios</title>
    <style>
    .principal {
           margin-top:1.5%;
           margin-bottom:3%;
        }
    .reporte_formulario{
        margin-left:25%;
    }
    </style>
</head>
<body id="premios">

    <div class="principal">
        <section>
            <h1>Historial de Premios</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                   
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : ''; ?>" placeholder="Fecha inicio">
                    
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_fin" name="fecha_fin" value="<?php echo isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : ''; ?>" placeholder="Fecha fin">

                    <input type="hidden" name="pestaña" value="historial_premios">
                    <input type="submit" value="Buscar">
                </form>
            </div>

            <div class="registros-por-pagina">
                <form method="GET" action="">
                    <label for="registros_por_pagina">Registros por página:</label>
                    <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                        <option value="10" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 10) echo 'selected'; ?>>10</option>
                        <option value="20" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 20) echo 'selected'; ?>>20</option>
                        <option value="50" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 50) echo 'selected'; ?>>50</option>
                        <option value="100" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 100) echo 'selected'; ?>>100</option>
                    </select>
                    <?php
                    // Keep other filters when changing records per page
                    if (!empty($_GET['id_trabajador'])) echo '<input type="hidden" name="id_trabajador" value="' . $_GET['id_trabajador'] . '">';
                    if (!empty($_GET['fecha_inicio'])) echo '<input type="hidden" name="fecha_inicio" value="' . $_GET['fecha_inicio'] . '">';
                    if (!empty($_GET['fecha_fin'])) echo '<input type="hidden" name="fecha_fin" value="' . $_GET['fecha_fin'] . '">';
                    ?>
                </form>
            </div>

            <?php
            // Display active filters
            $activeFilters = [];
            if (!empty($_GET['id_trabajador'])) $activeFilters[] = "ID Trabajador: " . htmlspecialchars($_GET['id_trabajador']);
            if (!empty($_GET['fecha_inicio'])) $activeFilters[] = "Fecha Inicio: " . htmlspecialchars($_GET['fecha_inicio']);
            if (!empty($_GET['fecha_fin'])) $activeFilters[] = "Fecha Fin: " . htmlspecialchars($_GET['fecha_fin']);

            if (!empty($activeFilters)) {
                echo "<div class='active-filters'><strong>Filtros activos:</strong> " . implode(", ", $activeFilters) . "</div>";
            }

            $id_trabajador = isset($_GET['id_trabajador']) ? $conexion_transimex->real_escape_string($_GET['id_trabajador']) : '';
            $fecha_inicio = isset($_GET['fecha_inicio']) ? $conexion_transimex->real_escape_string($_GET['fecha_inicio']) : '';
            $fecha_fin = isset($_GET['fecha_fin']) ? $conexion_transimex->real_escape_string($_GET['fecha_fin']) : '';

            $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
            $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $offset = ($pagina_actual - 1) * $registros_por_pagina;

            $sql_premios = "
                SELECT fecha,tipo,trabajadores.apellidos, trabajadores.nombre,ot,horas FROM 
                bonos left join trabajadores on bonos.id_trabajador=trabajadores.id
                WHERE 1=1";

            $conditions = [];
            $params = [];
            $types = '';

            if (!empty($id_trabajador)) {
                $conditions[] = "id_trabajador LIKE ?";
                $params[] = "%$id_trabajador%";
                $types .= 's';
            }
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $conditions[] = "fecha BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
                $types .= 'ss';
            }
            if (!empty($conditions)) {
                $sql_premios .= " AND " . implode(" AND ", $conditions);
            }
            $sql_premios .= " ORDER BY fecha DESC";

            $stmt = $conexion_transimex->prepare($sql_premios);
            if ($stmt) {
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $stmt->store_result();
                $total_registros = $stmt->num_rows;

                $sql_premios .= " LIMIT ?, ?";
                $params[] = $offset;
                $params[] = $registros_por_pagina;
                $types .= 'ii';

                $stmt = $conexion_transimex->prepare($sql_premios);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($fecha, $tipo, $nombre, $apellido, $ot, $horas);

                if ($stmt->num_rows > 0) {
                    echo "<table border='1'>
                        <tr>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Tipo</th>
                            <th>OT</th>
                            <th>Horas</th>
                        </tr>";

                    while ($stmt->fetch()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($fecha) . "</td> 
                                <td>" .htmlspecialchars($apellido)." ". htmlspecialchars($nombre) . "</td>
                                <td>" . htmlspecialchars($tipo) . "</td>
                                <td>" . htmlspecialchars($ot) . "</td>
                                <td>" . htmlspecialchars($horas) . "</td>
                            </tr>";
                    }

                    echo "</table>";

                    $total_paginas = ceil($total_registros / $registros_por_pagina);

                    echo '<div class="paginacion">';
                    for ($i = 1; $i <= $total_paginas; $i++) {
                        if ($i == $pagina_actual) {
                            echo '<span>' . $i . '</span>';
                        } else {
                            echo '<a href="?pagina=' . $i . '&id_trabajador=' . urlencode($id_trabajador) . '&fecha_inicio=' . urlencode($fecha_inicio) . '&fecha_fin=' . urlencode($fecha_fin) . '&registros_por_pagina=' . $registros_por_pagina . '">' . $i . '</a>';
                        }
                    }
                    echo '</div>';
                } else {
                    echo "No se encontraron resultados.";
                }
                $stmt->close();
            } else {
                echo "Error en la consulta: " . $conexion_transimex->error;
            }
            $conexion_transimex->close();
            ?>
        </section>
    </div>

</body>
</html>