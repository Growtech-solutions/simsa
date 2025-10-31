<head>
    <meta charset="UTF-8">
    <title>Inventario de Almacén</title>
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
        input[type="date"] {
            padding: 8px;
            
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
<body id="almacen_epp">

    <div class="principal">
        <section>
            <h1>EPP</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="folio">Folio:</label>
                    <input class="formulario_reporte_ot" type="text" id="folio" name="folio" value="<?php echo isset($_GET['folio']) ? $_GET['folio'] : ''; ?>" placeholder="Buscar por Folio">

                    <label for="epp">epp:</label>
                    <input class="formulario_reporte_ot" type="text" id="epp" name="epp" value="<?php echo isset($_GET['epp']) ? $_GET['epp'] : ''; ?>" placeholder="epp">
                    
                    <label for="estado">Estado:</label>
                    <input class="formulario_reporte_ot" type="text" id="estado" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>" placeholder="Estado">
                    <br><br>
                    <label for="trabajador">Selecciona un trabajador:</label>
                        <select name="trabajador" id="trabajador">
                            <?php
                                $sql = "SELECT DISTINCT trabajador FROM almacen_epp ORDER BY trabajador";
                                $resultado = $conexion->query($sql);
                                if ($resultado->num_rows > 0) {
                                    echo "<option value=''>Seleccione.</option>";
                                    while ($fila = $resultado->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($fila["trabajador"]) . "'>" . htmlspecialchars($fila["trabajador"]) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Seleccione.</option>";
                                }
                            ?>
                        </select>
                    <label for="fecha_alta">Fecha Alta:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_alta" name="fecha_alta" value="<?php echo isset($_GET['fecha_alta']) ? $_GET['fecha_alta'] : ''; ?>" placeholder="Fecha Alta">
                    
                    <label style="text-align:center" for="fecha_alta_fin">y :</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_alta_fin" name="fecha_alta_fin" value="<?php echo isset($_GET['fecha_alta_fin']) ? $_GET['fecha_alta_fin'] : ''; ?>" placeholder="Fecha Alta Fin">
                    
                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                    <input type="hidden" name="pestaña" value="almacen_epp">

                    <input type="submit" value="Buscar">
                </form>
            </div>
           

            <div class="registros-por-pagina">
                <form method="GET" action="">
                    <input type="hidden" name="folio" value="<?php echo isset($_GET['folio']) ? $_GET['folio'] : ''; ?>">
                    <input type="hidden" name="epp" value="<?php echo isset($_GET['epp']) ? $_GET['epp'] : ''; ?>">
                    <input type="hidden" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>">
                    <input type="hidden" name="trabajador" value="<?php echo isset($_GET['trabajador']) ? $_GET['trabajador'] : ''; ?>">
                    <input type="hidden" name="fecha_alta" value="<?php echo isset($_GET['fecha_alta']) ? $_GET['fecha_alta'] : ''; ?>">
                    <input type="hidden" name="fecha_alta_fin" value="<?php echo isset($_GET['fecha_alta_fin']) ? $_GET['fecha_alta_fin'] : ''; ?>">
                    <input type="hidden" name="pestaña" value="almacen_epp">
                    
                    <label for="registros_por_pagina">Registros por página:</label>
                    <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                        <option value="10" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 10) echo 'selected'; ?>>10</option>
                        <option value="25" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 25) echo 'selected'; ?>>25</option>
                        <option value="50" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 50) echo 'selected'; ?>>50</option>
                        <option value="100" <?php if (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == 100) echo 'selected'; ?>>100</option>
                    </select>
                </form>
            </div>


            <table>
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>epp</th>
                        <th>Estado</th>
                        <th>Fecha Alta</th>
                        <th>Trabajador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Capturar los filtros
                    $folio = isset($_GET['folio']) ? $conexion->real_escape_string($_GET['folio']) : '';
                    $epp = isset($_GET['epp']) ? $conexion->real_escape_string($_GET['epp']) : '';
                    $estado = isset($_GET['estado']) ? $conexion->real_escape_string($_GET['estado']) : '';
                    $trabajador = isset($_GET['trabajador']) ? $conexion->real_escape_string($_GET['trabajador']) : '';
                    $fecha_alta = isset($_GET['fecha_alta']) ? $conexion->real_escape_string($_GET['fecha_alta']) : '';
                    $fecha_alta_fin = isset($_GET['fecha_alta_fin']) ? $conexion->real_escape_string($_GET['fecha_alta_fin']) : '';

                    // Configurar paginación
                    $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 10;
                    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    $offset = ($pagina_actual - 1) * $registros_por_pagina;

                    $query = "SELECT * FROM almacen_epp WHERE 1=1";
                        if (!empty($folio)) {
                            $query .= " AND folio LIKE '%$folio%'";
                        }
                        if (!empty($epp)) {
                            $query .= " AND epp LIKE '%$epp%'";
                        }
                        
                        if (!empty($estado)) {
                            $query .= " AND estado LIKE '%$estado%'";
                        }
                        if (!empty($trabajador)) {
                            $query .= " AND trabajador LIKE '%$trabajador%'";
                        }
                        if (!empty($fecha_alta)) {
                            $query .= " AND fecha_alta >= '$fecha_alta'";
                        }
                        if (!empty($fecha_alta_fin)) {
                            $query .= " AND fecha_alta <= '$fecha_alta_fin'";
                        }
                        
                        // Ordenar por folio en orden descendente
                        $query .= " ORDER BY folio DESC";
                        
                        // Paginación
                        $query .= " LIMIT $offset, $registros_por_pagina";
                        
                        $resultado = $conexion->query($query);
                        
                         $queryCount = "SELECT COUNT(*) as total FROM almacen_epp WHERE 1=1";
                    if (!empty($folio)) {
                        $queryCount .= " AND folio LIKE '%$folio%'";
                    }
                    if (!empty($epp)) {
                        $queryCount .= " AND epp LIKE '%$epp%'";
                    }
                    
                    if (!empty($estado)) {
                        $queryCount .= " AND estado LIKE '%$estado%'";
                    }
                    if (!empty($trabajador)) {
                        $queryCount .= " AND trabajador = '$trabajador'";
                    }
                    if (!empty($fecha_alta)) {
                        $queryCount .= " AND fecha_alta >= '$fecha_alta'";
                    }
                    if (!empty($fecha_alta_fin)) {
                        $queryCount .= " AND fecha_alta <= '$fecha_alta_fin'";
                    }

                    $resultadoCount = $conexion->query($queryCount);
                    $totalRegistros = $resultadoCount->fetch_assoc()['total'];

                    // Mostrar registros
                    if ($resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<tr>
                                <td><a href='$header_loc.php?pestaña=editar_epp&header_loc=$header_loc&id=" . htmlspecialchars($row['folio']) . "'>" . htmlspecialchars($row['folio']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($row['epp']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['estado']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['alta']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['trabajador']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No se encontraron registros.</td></tr>";
                    }

                   
                    ?>
                </tbody>
            </table>

            <div class="paginacion">
                <?php
                $total_paginas = ceil($totalRegistros / $registros_por_pagina);
                $max_paginas_mostradas = 5; // Máximo de páginas visibles
            
                // Definir el rango de páginas
                $inicio = max(1, $pagina_actual - floor($max_paginas_mostradas / 2));
                $fin = min($total_paginas, $inicio + $max_paginas_mostradas - 1);
            
                // Ajustar el inicio si el final es menor al máximo de páginas mostradas
                if ($fin - $inicio + 1 < $max_paginas_mostradas) {
                    $inicio = max(1, $fin - $max_paginas_mostradas + 1);
                }
            
                // Botón para ir a la primera página
                if ($inicio > 1) {
                    echo "<a href='?pestaña=almacen_epp&pagina=1&folio=$folio&epp=$epp&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>&laquo; Primero</a>";
                }
            
                // Mostrar las páginas dentro del rango
                for ($i = $inicio; $i <= $fin; $i++) {
                    if ($i == $pagina_actual) {
                        echo "<span>$i</span>";
                    } else {
                        echo "<a href='?pestaña=almacen_epp&pagina=$i&folio=$folio&epp=$epp&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>$i</a>";
                    }
                }
            
                // Botón para ir a la última página
                if ($fin < $total_paginas) {
                    echo "<a href='?pestaña=almacen_epp&pagina=$total_paginas&folio=$folio&epp=$epp&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>Último &raquo;</a>";
                }
                ?>
            </div>

        </section>
    </div>
</body>
</html>