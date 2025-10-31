<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>consumibles</title>
    <style>
        body {
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
            color: #444;
        }

        .principal {
            width: 90%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .buscador {
            text-align: center;
            margin-bottom: 20px;
        }

        .reporte_formulario {
            display: inline-block;
            text-align: left;
        }

        .reporte_formulario label {
            font-size: 14px;
            margin-right: 10px;
        }

        .reporte_formulario input[type="text"],
        .reporte_formulario select {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .reporte_formulario input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .reporte_formulario input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #eaeaea;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        th {
            background-color: #f7f7f7;
            color: #333;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even) {
            background-color: #fff;
        }

        .centrado {
            text-align: center;
            margin-top: 20px;
        }

        .paginacion {
            margin-top: 20px;
            text-align: center;
        }

        .paginacion a, .paginacion span {
            margin: 0 5px;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
        }

        .paginacion span {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .paginacion a:hover {
            background-color: #eaeaea;
        }

        .registros-por-pagina {
            text-align: center;
            margin-bottom: 20px;
        }

        .registros-por-pagina select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="number"] {
            padding: 8px;
            width: 80px; /* Ancho ajustado para el campo de cantidad */
            border: none; /* Sin bordes */
            background-color: transparent; /* Fondo transparente */
            font-size: 14px;
            color: #333; /* Color del texto */
        }

        input[type="number"]:focus {
            outline: none; /* Sin contorno al enfocar */
        }
        label{
            padding-left:1rem;
        }
        
    </style>
</head>
<body id="consumibles">
 <div>
     <div class="principal">
        <section>
            <h1>Consumibles</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="proveedor">Proveedor:</label>
                    <select class="altadeproyecto" id="proveedor" name="proveedor">
                        <option value="">Seleccione un Proveedor</option>
                        <?php
                        $proveedores = $conexion->query("SELECT DISTINCT proveedor FROM consumibles");
                        while ($proveedor = $proveedores->fetch_assoc()) {
                            $selected = (isset($_GET['proveedor']) && $_GET['proveedor'] == $proveedor['proveedor']) ? 'selected' : '';
                            echo "<option value=\"{$proveedor['proveedor']}\" $selected>{$proveedor['proveedor']}</option>";
                        }
                        ?>
                    </select>

                    <label for="nombre">Producto:</label>
                    <input class="altadeproyecto" type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_GET['nombre'] ?? '', ENT_QUOTES); ?>">

                    <label for="cantidad_minima">Productos bajo el limite:</label>
                    <input type="checkbox" id="cantidad_minima" name="cantidad_minima" value="1" <?php echo isset($_GET['cantidad_minima']) ? 'checked' : ''; ?>>
                    <input type="hidden" name="pestaña" value="consumibles">
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
                    <?php
                        $filters = ['proveedor', 'nombre', 'cantidad_minima'];
                        foreach ($filters as $filter) {
                            if (!empty($_GET[$filter])) {
                                echo '<input type="hidden" name="' . $filter . '" value="' . htmlspecialchars($_GET[$filter], ENT_QUOTES) . '">';
                            }
                        }
                    ?>
                    <input type="hidden" name="pestaña" value="consumibles">
                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                </form>
            </div>

            <table>
                <?php
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                // Actualización de la cantidad
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
                    $id = $conexion->real_escape_string($_POST['id']);
                    $nueva_cantidad = $conexion->real_escape_string($_POST['cantidad']);
                    $conexion->query("UPDATE consumibles SET cantidad = '$nueva_cantidad' WHERE id = '$id'");
                }

                // Filtros
                $proveedor = isset($_GET['proveedor']) ? $conexion->real_escape_string($_GET['proveedor']) : '';
                $nombre = isset($_GET['nombre']) ? $conexion->real_escape_string($_GET['nombre']) : '';
                $cantidad_minima = isset($_GET['cantidad_minima']) ? true : false;
                $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
                $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $offset = ($pagina_actual - 1) * $registros_por_pagina;

                // Consulta para obtener consumibles
                $sql_consumibles = "
                    SELECT 
                        id, nombre, proveedor, cantidad, minimo, costo, unidad
                    FROM consumibles
                    WHERE 1=1";

                if (!empty($proveedor)) {
                    $sql_consumibles .= " AND proveedor = '$proveedor'";
                }

                if (!empty($nombre)) {
                    $sql_consumibles .= " AND nombre LIKE '%$nombre%'";
                }

                if ($cantidad_minima) {
                    $sql_consumibles .= " AND cantidad < minimo";
                }

                $sql_consumibles .= " ORDER BY nombre ASC LIMIT $registros_por_pagina OFFSET $offset";

                $result = $conexion->query($sql_consumibles);
                if ($result->num_rows > 0) {
                    echo "<thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Proveedor</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Mínimo</th>
                                <th>Costo</th>
                                <th>Acciones</th> <!-- Nueva columna de acciones -->
                            </tr>
                          </thead>
                          <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['nombre']}</td>
                                <td>{$row['proveedor']}</td>
                                <td>
                                    <form method='POST' action=''>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <input type='number' name='cantidad' value='{$row['cantidad']}' required> <!-- Ajusta el ancho -->
                                </td>
                                <td>{$row['unidad']}</td>
                                <td>{$row['minimo']}</td>
                                <td>$ {$row['costo']}</td>
                                <td>
                                    <input type='submit' name='update' value='Actualizar'> <!-- Botón de actualizar en la nueva columna -->
                                    </form>
                                </td>
                              </tr>";
                    }
                    echo "</tbody>";
                } else {
                    echo "<tbody><tr><td colspan='6'>No se encontraron consumibles.</td></tr></tbody>";
                }
                echo "</table>";

                // Paginación
                $total_consumibles_result = $conexion->query("SELECT COUNT(*) as total FROM consumibles WHERE 1=1" . (!empty($proveedor) ? " AND proveedor = '$proveedor'" : "") . (!empty($nombre) ? " AND nombre LIKE '%$nombre%'" : "") . ($cantidad_minima ? " AND cantidad < minimo" : ""));

if (!$total_consumibles_result) {
    die("Error en la consulta SQL: " . $conexion->error);
}

$total_consumibles = $total_consumibles_result->fetch_assoc()['total'];

                      $total_paginas = ceil($total_consumibles / $registros_por_pagina);

                if ($total_paginas > 1) {
                    echo '<div class="paginacion">';
                    for ($i = 1; $i <= $total_paginas; $i++) {
                        if ($i == $pagina_actual) {
                            echo "<span>$i</span>";
                        } else {
                            echo "<a href='?pestaña=consumibles&pagina=$i&registros_por_pagina=$registros_por_pagina&proveedor=" . htmlspecialchars($proveedor, ENT_QUOTES) . "&nombre=" . htmlspecialchars($nombre, ENT_QUOTES) . "&cantidad_minima=" . ($cantidad_minima ? 1 : 0) . "'>$i</a>";
                        }
                    }
                    echo '</div>';
                }

                $conexion->close();
                ?>
            </div>
        </section>
    </div>
</body>
</html>