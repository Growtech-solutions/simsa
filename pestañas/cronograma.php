<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cronograma</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .cita {
            text-align: center;
            vertical-align: middle;
            border: 1px solid black; /* Añadir borde negro */
        }

        .cita a {
            color: #333;
            text-decoration: none;
        }

        .cita a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 80%;
            margin: auto;
        }

        .form-control {
            margin-bottom: 10px;
        }

        .table thead th {
            background-color: #343a40;
            color: #fff;
            width:90%;
            text-align:center;
        }
        

        h1 {
            margin-top: 20px;
            font-size: 2rem;
            color: #343a40;
            text-align: center;
        }
        .form-row{
            display:grid;
            grid-template-columns:33.33% 33.33% 33.33%
            
        }
        input:{
            width:100%;
        }
        .nombre-trabajador {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Hacer que la primera columna (trabajador) sea sticky */
.nombre-trabajador {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    position: sticky;
    left: 0;
    background-color: white; /* Para mantener el fondo */
    z-index: 10;
}

/* Hacer que la primera fila (fechas) sea sticky */
.table thead th {
    position: sticky;
    top: 0;
    background-color: #343a40;
    color: #fff;
    text-align: center;
    z-index: 20;
}

/* Asegurar que el contenido de las celdas mantenga su altura */
.cita {
    vertical-align: middle;
    border: 1px solid black;
}
    </style>
</head>

<body id="cronograma">
    <?php 
    
    $fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : date('Y-m-d');
    $fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : date('Y-m-d', strtotime($fecha_inicial . ' +15 days'));
    $area_seleccionada = isset($_GET['area']) ? $_GET['area'] : 'Todos';

    $sql_trabajadores = "SELECT id, CONCAT(nombre, ' ', apellidos) AS nombre FROM trabajadores WHERE Estado = 'Activo' and cronograma=1 ";
    if ($area_seleccionada != 'Todos') {
        $sql_trabajadores .= " AND area = '$area_seleccionada'";
    }
    $sql_trabajadores .= " ORDER BY area DESC";
    $result_trabajadores = mysqli_query($conexion, $sql_trabajadores);
    $trabajadores = [];
    while($row = mysqli_fetch_assoc($result_trabajadores)) {
        $trabajadores[] = $row;
    }

    $sql_cronograma = "SELECT cronograma.id, id_trabajador, id_pieza, duracion, cronograma.fecha_inicial, cronograma.fecha_final , piezas.pieza AS pieza_nombre
    FROM cronograma left join piezas on cronograma.id_pieza = piezas.id
    WHERE cronograma.fecha_inicial <= '$fecha_final' AND cronograma.fecha_final >= '$fecha_inicial'
    ORDER BY id_trabajador, fecha_inicial";
    
    $result_cronograma = mysqli_query($conexion, $sql_cronograma);
    $cronograma = [];
    while($row = mysqli_fetch_assoc($result_cronograma)) {
        $cronograma[] = $row;
    }

    function generar_dias($fecha_inicial, $fecha_final) {
        $dias = [];
        $current_date = strtotime($fecha_inicial);

        while ($current_date <= strtotime($fecha_final)) {
            $dias[] = date('Y-m-d', $current_date);
            $current_date = strtotime('+1 day', $current_date);
        }

        return $dias;
    }

    $dias = generar_dias($fecha_inicial, $fecha_final);

    function calcular_fecha_final($fecha, $duracion) {
        return date('Y-m-d', strtotime($fecha . ' +' . ($duracion - 1) . ' days'));
    }

    function generar_color_pastel() {
        $r = mt_rand(180, 255);
        $g = mt_rand(180, 255);
        $b = mt_rand(180, 255);
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    $colores_trabajadores = [];
    foreach ($cronograma as $cita) {
        if (!isset($colores_trabajadores[$cita['id_trabajador']])) {
            $color = generar_color_pastel();
            while (in_array($color, $colores_trabajadores)) {
                $color = generar_color_pastel();
            }
            $colores_trabajadores[$cita['id_trabajador']] = $color;
        }
    }

    $citas_por_trabajador = [];

    foreach ($cronograma as $cita) {
    $fecha_inicio_cita = $cita['fecha_inicial'];
    $fecha_final_cita = $cita['fecha_final'];

    foreach ($dias as $index => $dia) {
        if ($dia >= $fecha_inicial && $dia <= $fecha_final) {
            if ($dia >= $fecha_inicio_cita && $dia <= $fecha_final_cita) {
                if (!isset($citas_por_trabajador[$cita['id_trabajador']])) {
                    $citas_por_trabajador[$cita['id_trabajador']] = [];
                }

                if (!isset($citas_por_trabajador[$cita['id_trabajador']][$index])) {
                    $citas_por_trabajador[$cita['id_trabajador']][$index] = [];
                }

                $colspan = 1;
                
                // Ajustar para manejar fechas de inicio menores al rango mostrado
                $inicio = array_search(max($fecha_inicio_cita, $fecha_inicial), $dias); 
                $fin = array_search($fecha_final_cita, $dias);

                if ($fin !== false && $inicio !== false) {
                    $colspan = $fin - $inicio + 1;
                }

                $citas_por_trabajador[$cita['id_trabajador']][$index] = [
                    'id' => $cita['id'],
                    'pieza_nombre' => $cita['pieza_nombre'],
                    'color' => $colores_trabajadores[$cita['id_trabajador']],
                    'colspan' => $colspan,
                    'start' => $fecha_inicio_cita,
                    'end' => $fecha_final_cita
                ];
            }
        }
    }
}


// Consulta para obtener datos de cronograma_fijo
$sql_cronograma_fijo = "
    SELECT cf.id, cf.id_trabajador, cf.id_pieza, 
           IF(cf.fecha_inicial < '$fecha_inicial', '$fecha_inicial', cf.fecha_inicial) AS fecha_inicial, 
           IF(cf.fecha_final > '$fecha_final', '$fecha_final', cf.fecha_final) AS fecha_final, 
           t.nombre AS trabajador_nombre, 
           p.pieza AS pieza_nombre
    FROM cronograma_fijo cf
    JOIN trabajadores t ON cf.id_trabajador = t.id
    JOIN piezas p ON cf.id_pieza = p.id
    WHERE cf.fecha_inicial <= '$fecha_final' 
      AND cf.fecha_final >= '$fecha_inicial'
    ORDER BY cf.id_trabajador, cf.fecha_inicial";

$result_cronograma_fijo = mysqli_query($conexion, $sql_cronograma_fijo);
$cronograma_fijo = [];
while ($row = mysqli_fetch_assoc($result_cronograma_fijo)) {
    $cronograma_fijo[] = $row;
}



    ?>
    <div class="container-cronograma">
        <h1>Cronograma</h1>
        <div class="flex">
            <form method="GET" action="">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="fecha_inicial">Fecha inicial:</label>
                        <input type="date" id="fecha_inicial" name="fecha_inicial" value="<?php echo $fecha_inicial; ?>" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_final">Fecha final:</label>
                        <input type="date" id="fecha_final" name="fecha_final" value="<?php echo $fecha_final; ?>" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="area">Área:</label>
                        <select id="area" name="area" class="form-control">
                            <option value="Todos" <?php if ($area_seleccionada == 'Todos') echo 'selected'; ?>>Todos</option>
                            <option value="Pailería" <?php if ($area_seleccionada == 'Pailería') echo 'selected'; ?>>Pailería</option>
                            <option value="Maquinados" <?php if ($area_seleccionada == 'Maquinados') echo 'selected'; ?>>Maquinados</option>
                        </select>
                    </div>
                    
                </div>
                <input type="hidden" name="pestaña" value="cronograma">
                <button style="grid-column: 3;"  type="submit" class="btn btn-primary">Filtrar</button>
                <a href="<?php echo $header_loc; ?>.php?pestaña=agregar_trabajo_fijo" class="btn btn-primary">Agregar trabajo fijo</a>
                
            </form>
        
        </div>


            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <?php foreach ($dias as $dia): ?>
                            <th><?php echo $dia; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($trabajadores as $trabajador): ?>
        <tr>
            <!-- Fila del cronograma dinámico -->
            <td class="nombre-trabajador" rowspan="<?php 
                // Contar las filas que se mostrarán. Si hay cronograma fijo en el rango, rowspan será 2.
                $mostrar_cronograma_fijo = false;
                foreach ($cronograma_fijo as $fijo) {
                    if ($fijo['id_trabajador'] == $trabajador['id'] && $fijo['fecha_final'] >= $fecha_inicial && $fijo['fecha_final'] <= $fecha_final) {
                        $mostrar_cronograma_fijo = true;
                        break;
                    }
                }
                echo $mostrar_cronograma_fijo ? 2 : 1; // Mostrar una fila si no hay cronograma fijo, dos si lo hay
            ?>">
                <a href="<?php echo $header_loc; ?>.php?pestaña=agendar&id=<?php echo $trabajador['id']; ?>">
                    <?php echo $trabajador['nombre']; ?>
                </a>
            </td>

            <!-- Fila de citas dinámicas -->
            <?php
            $index = 0;
            while ($index < count($dias)) {
                $dia = $dias[$index];
                if (isset($citas_por_trabajador[$trabajador['id']][$index])) {
                    $cita_actual = $citas_por_trabajador[$trabajador['id']][$index];
                    echo '<td class="cita" style="background-color: ' . $cita_actual['color'] . '; border: 1px solid black;" colspan="' . $cita_actual['colspan'] . '">';
                    echo '<a href="' . $header_loc . '.php?pestaña=detalle_agenda&&header_loc='.$header_loc.'&id=' . $cita_actual['id'] . '">' . $cita_actual['pieza_nombre'] . '</a>';
                    echo '</td>';
                    $index += $cita_actual['colspan'];
                } else {
                    echo '<td class="cita sin-color"></td>';
                    $index++;
                }
            }
            ?>
        </tr>

        <?php if ($mostrar_cronograma_fijo): ?>
        <tr>
            <!-- Fila del cronograma fijo -->
            <?php
            $index = 0;
            while ($index < count($dias)) {
                $dia = $dias[$index];
                $cita_fija = null;

                // Comprobamos si el trabajador tiene cronograma fijo en el rango
                foreach ($cronograma_fijo as $fijo) {
                    if ($fijo['id_trabajador'] == $trabajador['id'] && $dia >= $fijo['fecha_inicial'] && $dia <= $fijo['fecha_final']) {
                        $cita_fija = $fijo;
                        break;
                    }
                }

                if ($cita_fija) {
                    // Calcular el colspan adecuado para las fechas del cronograma fijo
                    $inicio_fijo = array_search($cita_fija['fecha_inicial'], $dias);
                    $fin_fijo = array_search($cita_fija['fecha_final'], $dias);
                    $colspan_fijo = $fin_fijo - $inicio_fijo + 1;

                    echo '<td class="cita" style="background-color: gold; border: 1px solid black;" colspan="' . $colspan_fijo . '">';
                    echo '<a href="' . $header_loc . '.php?pestaña=detalle_fijo&header_loc='.$header_loc.'&id=' . $cita_fija['id'] . '">' . $cita_fija['id'] . '</a>';

                    echo '</td>';
                    $index += $colspan_fijo;
                } else {
                    echo '<td class="cita sin-color"></td>';
                    $index++;
                }
            }
            ?>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</tbody>


            </table>
            <form method="post">
                <button type="submit" name="restablecer_cronograma" class="btn btn-primary">Restablecer Cronograma</button>
            </form>
 
    </div>
                
            
</body>

</html>