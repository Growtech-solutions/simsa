
<?php
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$ot = isset($_GET['ot']) ? $_GET['ot'] : null;
$pieza = isset($_GET['pieza']) ? $_GET['pieza'] : null;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

$sql = "SELECT * FROM piezas WHERE area = '$pagina_id' AND fecha_final IS NULL ORDER BY prioridad, fecha_inicial DESC,id";
if ($estado == 'Pendientes' ) {
    $sql = "SELECT * FROM piezas WHERE area = '$pagina_id' AND fecha_final IS NULL AND fecha_inicial IS NULL ORDER BY prioridad, fecha_inicial DESC,id";
}

if (!empty($ot) || !empty($pieza)) {
    $sql = "SELECT * FROM piezas WHERE area = '$pagina_id' AND fecha_final IS NULL";
    if (!empty($pieza)) {
        $sql .= " AND nombre LIKE '%$pieza%'";
    }
    if (!empty($ot)) {
        $sql .= " AND ot = '$ot'";
    }
    $sql .= " ORDER BY prioridad, fecha_inicial DESC,id";
}
$resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        while($fila = $resultado->fetch_assoc()) {
            $id_pieza=$fila["id"];
            $requisitos = "
            SELECT 
                p.pieza AS nombre_pieza, 
                GROUP_CONCAT(pr.pieza SEPARATOR ', ') AS prerrequisitos,
                GROUP_CONCAT(c.descripcion SEPARATOR ', ') AS compras,
                c.id_oc
            FROM 
                prerrequisitos
            LEFT JOIN 
                piezas p ON prerrequisitos.pieza = p.id 
            LEFT JOIN 
                piezas pr ON prerrequisitos.prerrequisito = pr.id 
            LEFT JOIN 
                compras c ON prerrequisitos.compra = c.id 
            LEFT JOIN 
                orden_compra ON c.id_oc = orden_compra.id
            
            WHERE 
                prerrequisitos.pieza = $id_pieza 
                AND pr.fecha_final IS NULL
                AND firma_llegada IS NULL
            GROUP BY 
                p.pieza;
            ";
            $resultado_requisitos = $conexion->query($requisitos);
            if ($resultado_requisitos->num_rows > 0) {
                while ($row = $resultado_requisitos->fetch_assoc()) {
                    echo "<tr>";
                        echo "<td colspan='9'>
                            Para <a href='$header_loc.php?pestaña=prerrequisitos&id_pieza=" . $fila["id"] . "'>". htmlspecialchars($row['nombre_pieza']) . "</a> <br> <strong>Piezas faltantes: </strong>" . htmlspecialchars($row['prerrequisitos']) . 
                            (!empty($row['compras']) ? "<br> <strong>Compras faltantes: </strong>" . htmlspecialchars($row['compras']) : "") . "
                        </td>";
                    echo "</tr>";
                }
            } else {
                


            echo "<tr>";
            echo "<td class='desktop-only'>";
                echo "<form class='grid2' action='' method='post'>";
                    echo "<input type='hidden' name='id_pieza' value='" . $fila["id"] . "'>";
                    echo "<input  class='no-border' type='number' name='prioridad' value='" . number_format($fila['prioridad'], 3) . "' step='0.001'>";
                    echo "<input class='no-border' type='submit' name='actualizar_prioridad' value='Actualizar'>";
                echo "</form>";
            echo "</td>";
            echo "<td>" . $fila["ot"] . "</td>";
            echo "<td><a href='$header_loc.php?pestaña=prerrequisitos&id_pieza=" . $fila["id"] . "'>" . $fila["pieza"] . "</a></td>";
            echo "<td class='desktop-only'>" . $fila["cantidad"] . "</td>";
            echo "<td class='desktop-only'>" . $fila["comentarios"] . "</td>";
            echo "<td class='desktop-only'>";
                include '../php/tablaencargado.php';
            echo "<td>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id_pieza' value='" . $fila["id"] . "'>";
                if ($fila["fecha_inicial"] == null) {
                    echo "<input class='iniciar' type='submit' name='iniciado' value='Iniciar'>";
                } else {
                    echo "<input class='iniciar' type='submit' name='terminado' value='Terminar'>";
                }
            echo "</form>";
            echo "</td>";
            echo "</td>";
            echo "<td class='desktop-only'>" . ($fila["fecha_inicial"] ?? "No iniciado") . "</td>";
            echo "</tr>";
                
                        }
        }
                        } else {
                            echo "<tr><td colspan='9'>No hay piezas pendientes.</td></tr>";
                        }
                 