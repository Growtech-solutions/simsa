<?php

echo 
"<style>
    .agregarEncargado {
        margin-left: .5rem;
        text-align: center;
        cursor: pointer;
    }
    .encargado_nombre{
        width: 80px;
    }
    .encargado_cantidad{
        width: 50px;
    }
    .encargado_tiempo{
        width: 50px;
    }
    .guardar{
        margin-left:70%;
        margin-top: .8rem;
    }
    .centrado {
        text-align: center;
    }
    .eliminarEncargado {
        margin-left: .5rem;
        text-align: center;
        cursor: pointer;
        color: red;
    }
       
.select-actividad {
    width: 80px;
    white-space: normal;
    overflow-wrap: break-word;
}

</style>";


// Consulta SQL
$id_pieza = $fila["id"];
$sql = "SELECT * FROM encargado WHERE id_pieza = $id_pieza AND fecha = CURDATE();";

// Ejecutar la consulta
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    // Mostrar los datos en una tabla
    echo "<table>";
        echo "<form action='' method='post'>";
            echo "<tr>
                <th>Nombre";
                echo "<input class='agregarEncargado' type='submit' name='agregarEncargado' value='+'>";
                echo "<input type='hidden' name='id_pieza' value='" . $fila["id"] . "'>"; 
        echo "</form>".
            "   </th>
                <th>Actividad</th>
                <th>Tiempo</th>
                <th>Cantidad</th>
                <th>Eliminar</th>
            </tr>";
        while($row = $result->fetch_assoc()) {
            $id_tra=$row["id_trabajador"];
            $sql_trabajador = "SELECT apellidos, nombre FROM trabajadores WHERE id = '$id_tra'";
            $result_trabajador = $conexion_transimex->query($sql_trabajador);
            $trabajador = $result_trabajador->fetch_assoc();

            $ot_act=$fila["ot"];
            $sql_ot = "SELECT precios.id, precios.descripcion, ot.ot FROM precios 
            left join ot on precios.id_pedido= ot.id_pedido 
            WHERE ot = '$ot_act'";
            $result_ot = $conexion->query($sql_ot);

            echo "<form action='' method='post'>";
            echo "<tr>";
                if (($row["id_trabajador"]) == null) {
                    echo "<td class='encargado_nombre'>";
                        $SelectTrabajadores->obtenerNombres('id_trabajador[]', '');  
                    echo "</td>";
                } else {
                    echo "<td>".$trabajador["apellidos"]." ".$trabajador["nombre"]."</td>";
                }

                if (($row["actividad"]) == null) {
                    echo "<td style='width: 50px;'>";
                    echo "<select name='actividad[]' style='width: 80px; white-space: normal;' class='select-actividad'>";
                    echo "<option value=''>" . 'Actividad' . "</option>";
                    while ($actividad = $result_ot->fetch_assoc()) {
                        // Reemplazamos los espacios con saltos de línea visualmente (opcional)
                        $descripcion = wordwrap($actividad['descripcion'], 20, "\n", true); 
                        echo "<option value='" . $actividad['id'] . "'>" . $descripcion . "</option>";
                    }
                    echo "</select>";
                    echo "</td>";
                } else {
                    $actividad_id = $row["actividad"];
                    $sql_actividad = "SELECT descripcion FROM precios WHERE id = '$actividad_id'";
                    $result_actividad = $conexion->query($sql_actividad);
                    $actividad = $result_actividad->fetch_assoc();
                    echo "<td class='centrado' style='width: 50px;'>" . $actividad["descripcion"] . "</td>";
                }
                

                if (($row["tiempo"]) == null) {
                    echo "<td>";
                    echo "<input class='encargado_tiempo' type='text' name='encargado_tiempo[]' value='" . ($row["tiempo"] ?? '') . "'>";
                    echo "</td>";
                } else {
                    echo "<td class='centrado'>".$row["tiempo"]."</td>";
                }
                if (($row["cantidad"]) == null) {
                    echo "<td>";
                    echo "<input class='encargado_cantidad' type='number' name='encargado_cantidad[]' value='" . ($row["cantidad"] ?? '') . "'>";
                    echo "</td>";
                } else {
                    echo "<td class='centrado'>".$row["cantidad"]."</td>";
                }

                echo "<td>";
                echo "<input class='eliminarEncargado' type='submit' name='eliminarEncargado' value='Eliminar'>";
                echo "<input type='hidden' name='id_encargado' value='" . $row["id"] . "'>"; 
                echo "</td>";
                
            echo "</tr>";
            echo "<input type='hidden' name='id_encargado[]' value='" . $row["id"] . "'>";
        }
    echo "</table>";
    echo "<form action='' method='post'>";
        echo "<input class='guardar' type='submit' name='guardar' value='Guardar'>";
        echo "<input type='hidden' name='id_pieza' value='" . $fila["id"] . "'>"; 
    echo "</form>";  
} else {
    echo "<form action='' method='post'>";
    echo "Agregar encargado";
        echo "<input class='agregarEncargado' type='submit' name='agregarEncargado' value='+'>";
        echo "<input type='hidden' name='id_pieza' value='" . $fila["id"] . "'>"; 
    echo "</form>";
}

?>