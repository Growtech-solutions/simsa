<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Infonavit</title>
    <style>
        .principal { margin-bottom: 3%; }
    </style>
</head>
<body>
<div class="principal">
    <section>
        <form method="GET" action="">
        <input type="hidden" name="pestaña" value="registro_infonavit">
        <button type="submit" style="padding: 10px 20px;
          border: none;
          color: white;
          background-color: #007bff;
          border-radius: 4px;
          cursor: pointer;
          font-size: 14px;">+ Registrar infonavit</button>
      </form>
        <h1 class="text-2xl font-bold text-blue-600">Historial de Créditos Infonavit</h1>
        <br>

        <div class="buscador">
            <form class="reporte_formulario" method="GET" action="">
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $_GET['fecha_inicio'] ?? ''; ?>">

                <label for="fecha_fin">Fecha Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $_GET['fecha_fin'] ?? ''; ?>">

                <label for="nombre_trabajador">Trabajador:</label>
                <input type="text" id="nombre_trabajador" name="nombre_trabajador" value="<?php echo $_GET['nombre_trabajador'] ?? ''; ?>">

                <input type="hidden" name="pestaña" value="historial_infonavit">
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
            FROM infonavit
            LEFT JOIN trabajadores ON infonavit.id_trabajador = trabajadores.id
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
            $sql_base .= " AND infonavit.fecha_inicial BETWEEN ? AND ?";
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
            SELECT trabajadores.nombre, trabajadores.apellidos, infonavit.tipo, infonavit.monto, infonavit.fecha_inicial, infonavit.fecha_final, infonavit.estado, infonavit.num_credito
            " . $sql_base . " ORDER BY infonavit.fecha_inicial DESC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $registros_por_pagina;
        $types .= 'ii';

        $stmt = $conexion_transimex->prepare($sql_final);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($nombre, $apellidos, $tipo, $monto, $f_inicial, $f_final, $estado, $num_credito);

        if ($stmt->num_rows > 0) {
            echo "<table border='1'>
                <tr>
                    <th>Trabajador</th>
                    <th>Tipo</th>
                    <th>Semanal</th>
                    <th>Fecha Inicial</th>
                    <th>Fecha Final</th>
                    <th>Núm. Crédito</th>
                    <th>Estado</th>
                </tr>";
            while ($stmt->fetch()) {
                echo "<tr>
                        <td>" . htmlspecialchars($nombre . " " . $apellidos) . "</td>
                        <td>" . htmlspecialchars($tipo) . "</td>
                        <td>$" . number_format($monto, 2) . "</td>
                        <td>" . htmlspecialchars($f_inicial) . "</td>
                        <td>" . htmlspecialchars($f_final ?? '---') . "</td>
                        <td>" . htmlspecialchars($num_credito) . "</td>
                        <td>" . ($estado == 1 ? 'Activo' : 'Inactivo') . "</td>
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
            echo "No se encontraron registros de Infonavit.";
        }

        $stmt->close();
        $conexion_transimex->close();
        ?>
    </section>
</div>
</body>
</html>
