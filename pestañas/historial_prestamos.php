<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Préstamos</title>
    <style>
        .principal { margin-bottom: 3%; }
    </style>
</head>
<body>
<div class="principal">
    <section>
        <form method="GET" action="">
        <input type="hidden" name="pestaña" value="registro_prestamos">
        <button type="submit" style="padding: 10px 20px;
          border: none;
          color: white;
          background-color: #007bff;
          border-radius: 4px;
          cursor: pointer;
          font-size: 14px;">+ Registrar prestamo</button>
      </form>
        <h1 class="text-2xl font-bold text-blue-700">Historial de Préstamos</h1>
        <br>

        <div class="buscador">
            <form class="reporte_formulario" method="GET" action="">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">

                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">

                <label for="nombre_trabajador">Trabajador:</label>
                <input type="text" id="nombre_trabajador" name="nombre_trabajador" value="<?php echo $_GET['nombre_trabajador'] ?? ''; ?>">

                <input type="hidden" name="pestaña" value="historial_prestamos">
                <input type="submit" value="Buscar">
            </form>
        </div>

        <div class="registros-por-pagina">
            <form method="GET" action="">
                <label for="registros_por_pagina">Registros por página:</label>
                <select name="registros_por_pagina" id="registros_por_pagina" onchange="this.form.submit()">
                    <?php
                    $opciones = [10, 20, 50, 100];
                    foreach ($opciones as $opcion) {
                        $selected = (isset($_GET['registros_por_pagina']) && $_GET['registros_por_pagina'] == $opcion) ? 'selected' : '';
                        echo "<option value='$opcion' $selected>$opcion</option>";
                    }
                    if (!empty($_GET['nombre_trabajador'])) echo '<input type="hidden" name="nombre_trabajador" value="' . $_GET['nombre_trabajador'] . '">';
                    if (!empty($_GET['fecha_inicio'])) echo '<input type="hidden" name="fecha_inicio" value="' . $_GET['fecha_inicio'] . '">';
                    if (!empty($_GET['fecha_fin'])) echo '<input type="hidden" name="fecha_fin" value="' . $_GET['fecha_fin'] . '">';
                    ?>
                </select>
            </form>
        </div>

        <?php

        $nombre_trabajador = $_GET['nombre_trabajador'] ?? '';
        $fecha_inicio = $_GET['fecha_inicio'] ?? '';
        $fecha_fin = $_GET['fecha_fin'] ?? '';
        $registros_por_pagina = isset($_GET['registros_por_pagina']) ? (int)$_GET['registros_por_pagina'] : 20;
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $registros_por_pagina;

        $sql_base = "
            FROM prestamos
            LEFT JOIN trabajadores ON prestamos.id_trabajador = trabajadores.id
            WHERE 1=1
        ";
        $params = [];
        $types = '';

        if (!empty($nombre_trabajador)) {
            $sql_base .= " AND CONCAT_WS(' ', trabajadores.nombre, trabajadores.apellidos) LIKE ?";
            $params[] = '%' . $nombre_trabajador . '%';
            $types .= 's';
        }

        if (!empty($fecha_inicio) && !empty($fecha_fin)) {
            $sql_base .= " AND prestamos.fecha BETWEEN ? AND ?";
            $params[] = $fecha_inicio;
            $params[] = $fecha_fin;
            $types .= 'ss';
        }

        // Conteo total
        $sql_total = "SELECT COUNT(*) " . $sql_base;
        $stmt = $conexion_transimex->prepare($sql_total);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->bind_result($total_registros);
        $stmt->fetch();
        $stmt->close();

        // Datos paginados
        $sql_final = "
            SELECT trabajadores.nombre, trabajadores.apellidos, prestamos.prestamo, prestamos.fecha, prestamos.semanas, prestamos.monto_semanal, prestamos.fecha_final
            " . $sql_base . " ORDER BY prestamos.fecha DESC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $registros_por_pagina;
        $types .= 'ii';

        $stmt = $conexion_transimex->prepare($sql_final);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nombre, $apellidos, $prestamo, $fecha, $semanas, $monto, $fecha_final);

        if ($stmt->num_rows > 0) {
            echo "<table border='1'>
                <tr>
                    <th>Trabajador</th>
                    <th>Fecha Inicial</th>
                    <th>Préstamo</th>
                    <th>Semanas</th>
                    <th>Monto Semanal</th>
                    <th>Fecha Final</th>
                </tr>";
            while ($stmt->fetch()) {
                echo "<tr>
                        <td>" . htmlspecialchars($nombre . " " . $apellidos) . "</td>
                        <td>" . htmlspecialchars($fecha) . "</td>
                        <td>$" . number_format($prestamo, 0) . "</td>
                        <td>" . htmlspecialchars($semanas) . "</td>
                        <td>$" . number_format($monto, 0) . "</td>
                        <td>" . htmlspecialchars($fecha_final ?? '---') . "</td>
                    </tr>";
            }
            echo "</table>";

            $total_paginas = ceil($total_registros / $registros_por_pagina);
            echo '<div class="paginacion">';
            for ($i = 1; $i <= $total_paginas; $i++) {
                if ($i == $pagina_actual) {
                    echo '<span>' . $i . '</span>';
                } else {
                    echo '<a href="?pagina=' . $i . '&nombre_trabajador=' . urlencode($nombre_trabajador) . '&fecha_inicio=' . urlencode($fecha_inicio) . '&fecha_fin=' . urlencode($fecha_fin) . '&registros_por_pagina=' . $registros_por_pagina . '">' . $i . '</a>';
                }
            }
            echo '</div>';
        } else {
            echo "No se encontraron registros de préstamos.";
        }

        $stmt->close();
        $conexion_transimex->close();
        ?>
    </section>
</div>
</body>
</html>
