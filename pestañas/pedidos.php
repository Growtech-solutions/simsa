<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historial de Pedidos</title>
    <style>
          table {
            margin-left:5%;
        }
    </style>
</head>
<body>
<?php $header_loc= $_GET['header_loc']; ?>
<div class="principal">
    <section>
        <h1>Pedidos</h1>
        <div class="buscador">
            <form class="reporte_formulario" method="GET" action="">
                <label for="descripcion">Descripción:</label>
                <input class="formulario_reporte_ot" type="text" id="descripcion" name="descripcion" value="<?php echo isset($_GET['descripcion']) ? $_GET['descripcion'] : ''; ?>" placeholder="Descripción">

                <label for="cliente">Cliente:</label>
                <input class="formulario_reporte_ot" type="text" id="cliente" name="cliente" value="<?php echo isset($_GET['cliente']) ? $_GET['cliente'] : ''; ?>" placeholder="Cliente">

                <label for="planta">Planta:</label>
                <input class="formulario_reporte_ot" type="text" id="planta" name="planta" value="<?php echo isset($_GET['planta']) ? $_GET['planta'] : ''; ?>" placeholder="Planta">

                <label for="fecha_alta">Fecha Alta entre</label>
                <input class="formulario_reporte_ot" type="date" id="fecha_alta" name="fecha_alta" value="<?php echo isset($_GET['fecha_alta']) ? $_GET['fecha_alta'] : ''; ?>" placeholder="Fecha alta">

                <label for="fecha_altafin">y :</label>
                <input class="formulario_reporte_ot" type="date" id="fecha_altafin" name="fecha_altafin" value="<?php echo isset($_GET['fecha_altafin']) ? $_GET['fecha_altafin'] : ''; ?>" placeholder="Fecha alta fin">

                <input type="hidden" name="pestaña" value="pedidos">
                <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc); ?>">

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
                <input type="hidden" name="pestaña" value="pedidos">
                <?php

                if (!empty($_GET['descripcion'])) echo '<input type="hidden" name="descripcion" value="' . $_GET['descripcion'] . '">';
                if (!empty($_GET['cliente'])) echo '<input type="hidden" name="cliente" value="' . $_GET['cliente'] . '">';
                if (!empty($_GET['planta'])) echo '<input type="hidden" name="planta" value="' . $_GET['planta'] . '">';
                if (!empty($_GET['fecha_alta'])) echo '<input type="hidden" name="fecha_alta" value="' . $_GET['fecha_alta'] . '">';
                if (!empty($_GET['fecha_altafin'])) echo '<input type="hidden" name="fecha_altafin" value="' . $_GET['fecha_altafin'] . '">';
                ?>
            </form>
        </div>

        <?php
        // Obtener los filtros de la solicitud
        $descripcion = isset($_GET['descripcion']) ? $conexion->real_escape_string($_GET['descripcion']) : '';
        $fecha_alta = isset($_GET['fecha_alta']) ? $conexion->real_escape_string($_GET['fecha_alta']) : '';
        $cliente = isset($_GET['cliente']) ? $conexion->real_escape_string($_GET['cliente']) : '';
        $planta = isset($_GET['planta']) ? $conexion->real_escape_string($_GET['planta']) : '';
        $fecha_altafin = isset($_GET['fecha_altafin']) ? $conexion->real_escape_string($_GET['fecha_altafin']) : '';
        $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        // Construir la consulta
        $sql_pedidos_base = "SELECT id, descripcion, fecha_alta, valor_pesos, cliente, planta FROM pedido WHERE 1=1";

        if (!empty($descripcion)) {
            $sql_pedidos_base .= " AND descripcion LIKE '%$descripcion%'";
        }
        if (!empty($cliente)) {
            $sql_pedidos_base .= " AND cliente LIKE '%$cliente%'";
        }
        if (!empty($planta)) {
            $sql_pedidos_base .= " AND planta LIKE '%$planta%'";
        }
        if (!empty($fecha_alta) && !empty($fecha_altafin)) {
            $sql_pedidos_base .= " AND fecha_alta BETWEEN '$fecha_alta' AND '$fecha_altafin'";
        } elseif (!empty($fecha_alta)) {
            $sql_pedidos_base .= " AND fecha_alta >= '$fecha_alta'";
        } elseif (!empty($fecha_altafin)) {
            $sql_pedidos_base .= " AND fecha_alta <= '$fecha_altafin'";
        }

        // Calcular el número total de registros
        $sql_pedidos_count = "SELECT COUNT(*) FROM (" . $sql_pedidos_base . ") AS total";
        $stmt_count = $conexion->prepare($sql_pedidos_count);
        if ($stmt_count) {
            $stmt_count->execute();
            $stmt_count->store_result();
            $stmt_count->bind_result($total_registros);
            $stmt_count->fetch();
            $total_paginas = ceil($total_registros / $registros_por_pagina);
            $stmt_count->close();
        } else {
            echo "Error en la consulta de conteo: " . $conexion->error;
        }

        // Obtener los registros para la página actual
        $sql_pedidos = $sql_pedidos_base . " ORDER BY fecha_alta DESC LIMIT ?, ?";
        $stmt = $conexion->prepare($sql_pedidos);

        if ($stmt) {
            $stmt->bind_param('ii', $offset, $registros_por_pagina);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $descripcion, $fecha_alta, $valor_pesos, $cliente, $planta);

            if ($stmt->num_rows > 0) {
                echo "<div class='reporte_tabla'>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Cliente</th><th>Descripción</th><th>Fecha Alta</th><th>Valor en Pesos</th><th>Acciones</th></tr>";

                while ($stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($id) . "</td>";
                    echo "<td>" . htmlspecialchars($cliente) . " " . htmlspecialchars($planta) . "</td>";
                    echo "<td>" . htmlspecialchars($descripcion) . "</td>";
                    echo "<td>" . htmlspecialchars($fecha_alta) . "</td>";
                    echo "<td>" . htmlspecialchars($valor_pesos) . "</td>";
                    echo "<td>
                            <a href='../header_main_aside/$header_loc.php?pestaña=editar_pedido&header_loc=$header_loc&id=" . htmlspecialchars($id) . "'>Editar</a> | 
                            <a href='../php/eliminar_pedido.php?header_loc=$header_loc&id=" . htmlspecialchars($id) . "' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este pedido?\");'>Eliminar</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";

                // Construye la URL base sin el parámetro de página
$current_url = strtok($_SERVER["REQUEST_URI"], '?');
$query_params = $_GET;
unset($query_params['pagina']);

echo "<div class='paginacion'>";

// Botón para la primera y la anterior página
if ($pagina_actual > 1) {
    $query_params['pagina'] = 1;
    echo "<a href='$current_url?" . http_build_query($query_params) . "'>&laquo; Primera</a>";
    $query_params['pagina'] = $pagina_actual - 1;
    echo "<a href='$current_url?" . http_build_query($query_params) . "'>&lsaquo; Anterior</a>";
}

// Rango de páginas
$range = 2;
for ($i = max(1, $pagina_actual - $range); $i <= min($pagina_actual + $range, $total_paginas); $i++) {
    $query_params['pagina'] = $i;
    if ($i == $pagina_actual) {
        echo "<span>$i</span>";
    } else {
        echo "<a href='$current_url?" . http_build_query($query_params) . "'>$i</a>";
    }
}

// Botón para la siguiente y la última página
if ($pagina_actual < $total_paginas) {
    $query_params['pagina'] = $pagina_actual + 1;
    echo "<a href='$current_url?" . http_build_query($query_params) . "'>Siguiente &rsaquo;</a>";
    $query_params['pagina'] = $total_paginas;
    echo "<a href='$current_url?" . http_build_query($query_params) . "'>Última &raquo;</a>";
}

echo "</div>";

            } else {
                echo "<p>No se encontraron pedidos con los criterios especificados.</p>";
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conexion->error;
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
        ?>
    </section>
</div>
</body>
</html>
