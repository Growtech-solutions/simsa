<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Herramienta</title>

    <script>
        function confirmarActualizacion(cantidad) {
            return confirm("Se actualizarán los trabajadores a almacen. ¿Desea continuar?");
        }
    </script>
</head>
<?php
         if (isset($_POST['actualizar_trabajador'])) {
            // Capturar los filtros del formulario POST
            $folio = isset($_POST['folio']) ? $conexion->real_escape_string($_POST['folio']) : '';
            $herramienta = isset($_POST['herramienta']) ? $conexion->real_escape_string($_POST['herramienta']) : '';
            $area = isset($_POST['area']) ? $conexion->real_escape_string($_POST['area']) : '';
            $estado = isset($_POST['estado']) ? $conexion->real_escape_string($_POST['estado']) : '';
            $fecha_alta = isset($_POST['fecha_alta']) ? $conexion->real_escape_string($_POST['fecha_alta']) : '';
            $fecha_alta_fin = isset($_POST['fecha_alta_fin']) ? $conexion->real_escape_string($_POST['fecha_alta_fin']) : '';
            $trabajador = isset($_POST['trabajador']) ? $conexion->real_escape_string($_POST['trabajador']) : '';
            $header_loc = isset($_POST['header_loc']) ? $conexion->real_escape_string($_POST['header_loc']) : '';

            // Consulta de actualización
            $updateQuery = "UPDATE almacen_herramienta SET trabajador = 'Almacen' WHERE 1=1";
    
            if (!empty($folio)) {
                $updateQuery .= " AND folio LIKE '%$folio%'";
            }
            if (!empty($herramienta)) {
                $updateQuery .= " AND herramienta LIKE '%$herramienta%'";
            }
            if (!empty($area)) {
                $updateQuery .= " AND area LIKE '%$area%'";
            }
            if (!empty($estado)) {
                $updateQuery .= " AND estado LIKE '%$estado%'";
            }
            if (!empty($fecha_alta)) {
                $updateQuery .= " AND fecha_alta >= '$fecha_alta'";
            }
            if (!empty($fecha_alta_fin)) {
                $updateQuery .= " AND fecha_alta <= '$fecha_alta_fin'";
            }
            if (!empty($trabajador)) {
                $updateQuery .= " AND trabajador LIKE '%$trabajador%'";
            }
    
            // Ejecutar la consulta de actualización
            $conexion->query($updateQuery);
        }
?>
<body id="almacen">

    <div class="principal">
        <section>
            <h1>Almacén</h1>
            <div class="buscador">
                <form class="reporte_formulario" method="GET" action="">
                    <label for="folio">Folio:</label>
                    <input class="formulario_reporte_ot" type="text" id="folio" name="folio" value="<?php echo isset($_GET['folio']) ? $_GET['folio'] : ''; ?>" placeholder="Buscar por Folio">

                    <label for="herramienta">Herramienta:</label>
                    <input class="formulario_reporte_ot" type="text" id="herramienta" name="herramienta" value="<?php echo isset($_GET['herramienta']) ? $_GET['herramienta'] : ''; ?>" placeholder="Herramienta">

                    <label for="area">Área:</label>
                    <input class="formulario_reporte_ot" type="text" id="area" name="area" value="<?php echo isset($_GET['area']) ? $_GET['area'] : ''; ?>" placeholder="Área">

                    <label for="estado">Estado:</label>
                    <input class="formulario_reporte_ot" type="text" id="estado" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>" placeholder="Estado">
                    
                    <br><br>
                    <label for="trabajador">Selecciona un trabajador:</label>
                        <select name="trabajador" id="trabajador">
                            <?php
                                $sql = "SELECT DISTINCT trabajador FROM almacen_herramienta ORDER BY trabajador";
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
                    <input type="hidden" name="pestaña" value="herramienta">
                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                    <input type="submit" value="Buscar">
                </form>
            </div>
            <form method="POST" action="" onsubmit="return confirmarActualizacion(<?php echo $totalRegistros > 0 ? $totalRegistros : 0; ?>)">
                <input type="hidden" name="folio" value="<?php echo isset($_GET['folio']) ? $_GET['folio'] : ''; ?>">
                <input type="hidden" name="herramienta" value="<?php echo isset($_GET['herramienta']) ? $_GET['herramienta'] : ''; ?>">
                <input type="hidden" name="area" value="<?php echo isset($_GET['area']) ? $_GET['area'] : ''; ?>">
                <input type="hidden" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>">
                <input type="hidden" name="fecha_alta" value="<?php echo isset($_GET['fecha_alta']) ? $_GET['fecha_alta'] : ''; ?>">
                <input type="hidden" name="fecha_alta_fin" value="<?php echo isset($_GET['fecha_alta_fin']) ? $_GET['fecha_alta_fin'] : ''; ?>">
                <input type="hidden" name="trabajador" value="<?php echo isset($_GET['trabajador']) ? $_GET['trabajador'] : ''; ?>">
                <input type="hidden" name="pestaña" value="herramienta">
                <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                <input type="submit" name="actualizar_trabajador" value="Actualizar trabajador a 'Almacen'">
            </form>

            <div class="registros-por-pagina">
                <form method="GET" action="">
                    <input type="hidden" name="folio" value="<?php echo isset($_GET['folio']) ? $_GET['folio'] : ''; ?>">
                    <input type="hidden" name="herramienta" value="<?php echo isset($_GET['herramienta']) ? $_GET['herramienta'] : ''; ?>">
                    <input type="hidden" name="area" value="<?php echo isset($_GET['area']) ? $_GET['area'] : ''; ?>">
                    <input type="hidden" name="estado" value="<?php echo isset($_GET['estado']) ? $_GET['estado'] : ''; ?>">
                    <input type="hidden" name="trabajador" value="<?php echo isset($_GET['trabajador']) ? $_GET['trabajador'] : ''; ?>">
                    <input type="hidden" name="fecha_alta" value="<?php echo isset($_GET['fecha_alta']) ? $_GET['fecha_alta'] : ''; ?>">
                    <input type="hidden" name="fecha_alta_fin" value="<?php echo isset($_GET['fecha_alta_fin']) ? $_GET['fecha_alta_fin'] : ''; ?>">
                    <input type="hidden" name="pestaña" value="herramienta">
                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

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
                        <th>Herramienta</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th>Fecha Alta</th>
                        <th>Trabajador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Capturar los filtros
                    $folio = isset($_GET['folio']) ? $conexion->real_escape_string($_GET['folio']) : '';
                    $herramienta = isset($_GET['herramienta']) ? $conexion->real_escape_string($_GET['herramienta']) : '';
                    $area = isset($_GET['area']) ? $conexion->real_escape_string($_GET['area']) : '';
                    $estado = isset($_GET['estado']) ? $conexion->real_escape_string($_GET['estado']) : '';
                    $trabajador = isset($_GET['trabajador']) ? $conexion->real_escape_string($_GET['trabajador']) : '';
                    $fecha_alta = isset($_GET['fecha_alta']) ? $conexion->real_escape_string($_GET['fecha_alta']) : '';
                    $fecha_alta_fin = isset($_GET['fecha_alta_fin']) ? $conexion->real_escape_string($_GET['fecha_alta_fin']) : '';

                    // Configurar paginación
                    $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 10;
                    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                    $offset = ($pagina_actual - 1) * $registros_por_pagina;

                    $query = "SELECT * FROM almacen_herramienta WHERE 1=1";
                        if (!empty($folio)) {
                            $query .= " AND folio LIKE '%$folio%'";
                        }
                        if (!empty($herramienta)) {
                            $query .= " AND herramienta LIKE '%$herramienta%'";
                        }
                        if (!empty($area)) {
                            $query .= " AND area LIKE '%$area%'";
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
                        
                         $queryCount = "SELECT COUNT(*) as total FROM almacen_herramienta WHERE 1=1";
                    if (!empty($folio)) {
                        $queryCount .= " AND folio LIKE '%$folio%'";
                    }
                    if (!empty($herramienta)) {
                        $queryCount .= " AND herramienta LIKE '%$herramienta%'";
                    }
                    if (!empty($area)) {
                        $queryCount .= " AND area LIKE '%$area%'";
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
                                <td><a href='../header_main_aside/$header_loc.php?pestaña=editar_herramienta&header_loc=$header_loc&id=" . htmlspecialchars($row['folio']) . "'>" . htmlspecialchars($row['folio']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($row['herramienta']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['area']) . "</td>";
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
                    echo "<a href='?pestaña=herramienta&header_loc=$header_loc&pagina=1&folio=$folio&herramienta=$herramienta&area=$area&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>&laquo; Primero</a>";
                }
            
                // Mostrar las páginas dentro del rango
                for ($i = $inicio; $i <= $fin; $i++) {
                    if ($i == $pagina_actual) {
                        echo "<span>$i</span>";
                    } else {
                        echo "<a href='?pestaña=herramienta&header_loc=$header_loc&pagina=$i&folio=$folio&herramienta=$herramienta&area=$area&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>$i</a>";
                    }
                }
            
                // Botón para ir a la última página
                if ($fin < $total_paginas) {
                    echo "<a href='?pestaña=herramienta&header_loc=$header_loc&pagina=$total_paginas&folio=$folio&herramienta=$herramienta&area=$area&estado=$estado&trabajador=$trabajador&fecha_alta=$fecha_alta&fecha_alta_fin=$fecha_alta_fin&registros_por_pagina=$registros_por_pagina'>Último &raquo;</a>";
                }
                ?>
            </div>

        </section>
    </div>
</body>
</html>