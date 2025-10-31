<?php
// Variables de filtros
$encargado = $_GET['encargado'] ?? '';
$objeto = $_GET['objeto'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

// Generar la consulta SQL dinámica
$sql = "
SET SESSION group_concat_max_len = 10000;

SET @sql = NULL;
SELECT GROUP_CONCAT(DISTINCT 
    CONCAT(
        'MIN(CASE WHEN p.objeto = ''', p.objeto, ''' 
        THEN DATE_ADD(h.fecha, INTERVAL p.frecuencia WEEK) 
        ELSE NULL END) AS `', p.objeto, '`'
    )
) INTO @sql
FROM periodicos p;

SET @sql = CONCAT('
    SELECT 
        t.nombre AS encargado, 
        t.apellidos AS encargado_apellido,
        p.objeto,
        MIN(DATE_ADD(h.fecha, INTERVAL p.frecuencia WEEK)) AS fecha_vencimiento
    FROM historial_actividades h
    LEFT JOIN transimex.trabajadores t ON h.id_encargado = t.id
    LEFT JOIN periodicos p ON h.actividad = p.id
    WHERE 1=1 ',
    IF('$encargado' != '', ' AND t.nombre = \"$encargado\" ', ''),
    IF('$objeto' != '', ' AND p.objeto = \"$objeto\" ', ''),
    IF('$fecha_inicio' != '', ' AND DATE_ADD(h.fecha, INTERVAL p.frecuencia WEEK) >= \"$fecha_inicio\" ', ''),
    IF('$fecha_fin' != '', ' AND DATE_ADD(h.fecha, INTERVAL p.frecuencia WEEK) <= \"$fecha_fin\" ', ''),
    ' GROUP BY t.id, p.objeto
      ORDER BY fecha_vencimiento ASC'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
";

// Ejecutar la consulta
if ($conexion->multi_query($sql)) {
    do {
        if ($resultado = $conexion->store_result()) {
            $datos = $resultado->fetch_all(MYSQLI_ASSOC);
            $resultado->free();
        }
    } while ($conexion->more_results() && $conexion->next_result());
}

// Obtener valores únicos para filtros
$filtro_nombre_sql = "SELECT DISTINCT t.nombre AS encargado, t.apellidos AS encargado_apellido FROM historial_actividades h
LEFT JOIN transimex.trabajadores t ON h.id_encargado = t.id
LEFT JOIN periodicos p ON h.actividad = p.id";
$filtro_nombre_resultado = $conexion->query($filtro_nombre_sql);
$filtro_nombre = $filtro_nombre_resultado->fetch_all(MYSQLI_ASSOC);

$filtro_objeto_sql = "SELECT DISTINCT p.objeto FROM historial_actividades h
LEFT JOIN transimex.trabajadores t ON h.id_encargado = t.id
LEFT JOIN periodicos p ON h.actividad = p.id";
$filtro_objeto_resultado = $conexion->query($filtro_objeto_sql);
$filtro_objeto = $filtro_objeto_resultado->fetch_all(MYSQLI_ASSOC);

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso a plantas</title>
    <style>
        .rojo { background-color: #ffcccc; }
        .amarillo { background-color: #fff2cc; }
        .verde { background-color: #ccffcc; }
        h2 { text-align: center; }
        .reporte_formulario{
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .formulario_reporte_ot{
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="principal">
        <section>
            <h2>Acceso a plantas</h2>

            <!-- Filtros -->
            <form class="reporte_formulario" id="filtroForm">
                <select class="formulario_reporte_ot" name="encargado" id="encargado">
                    <option value="">Trabajador</option>
                    <?php foreach ($filtro_nombre as $f) {
                        echo "<option value='{$f['encargado']}'>{$f['encargado']} {$f['encargado_apellido']}</option>";
                    } ?>
                </select>

                <select class="formulario_reporte_ot" name="objeto" id="objeto">
                    <option value="">Planta</option>
                    <?php foreach ($filtro_objeto as $f) {
                        echo "<option value='{$f['objeto']}'>{$f['objeto']}</option>";
                    } ?>
                </select>

                <input type="hidden" name="pestaña" value="acceso_planta">

                <input type="submit" value="Buscar">
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Trabajado</th>
                        <th>Planta</th>
                        <th>Vencimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($datos)) {
                        foreach ($datos as $fila) {
                            $dias_restantes = (strtotime($fila['fecha_vencimiento']) - time()) / 86400;
                            $clase = ($dias_restantes < 15) ? 'rojo' : (($dias_restantes <= 90) ? 'amarillo' : 'verde');
                            
                            echo "<tr>";
                            echo "<td>{$fila['encargado']} {$fila['encargado_apellido']}</td>";
                            echo "<td>{$fila['objeto']}</td>";
                            echo "<td class='$clase'>{$fila['fecha_vencimiento']}</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No hay datos disponibles</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>

<script>
    document.getElementById("filtroForm").addEventListener("submit", function(event) {
        event.preventDefault();
        let params = new URLSearchParams(new FormData(this)).toString();
        window.location.href = window.location.pathname + "?" + params;
    });
</script>

</body>
</html>
