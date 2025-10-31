<head>
    <title>Prerrequisitos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        button {
            cursor: pointer;
            padding: 5px 10px;
        }
    </style>
    <script>
        function addRequisito() {
            document.getElementById("newRow").style.display = "table-row";
        }
    </script>
</head>

<body id="prerrequisitos">
    <div>
        <h1>Requisitos pieza</h1>

        <?php
        $id_pieza = isset($_GET['id_pieza']) ? intval($_GET['id_pieza']) : 0;

        // Ajustar la consulta SQL
        $requisitos = "
            SELECT prerrequisitos.*, p.pieza AS nombre_pieza, pr.pieza AS nombre_prerrequisito,c.descripcion AS nombre_prerrequisito_compra, p.ot 
            FROM prerrequisitos
            LEFT JOIN piezas p ON prerrequisitos.pieza = p.id 
            LEFT JOIN piezas pr ON prerrequisitos.prerrequisito = pr.id 
            LEFT JOIN compras c ON prerrequisitos.compra = c.id 
            WHERE prerrequisitos.pieza = $id_pieza";
        $resultado_requisitos = $conexion->query($requisitos);

        // Consulta para llenar el desplegable de prerrequisitos
        $consulta_requisitos = "
            SELECT id, pieza FROM piezas
            WHERE fecha_final IS NULL 
            AND ot=(SELECT ot FROM piezas WHERE id = $id_pieza)";
        $requisitos_resultado = $conexion->query($consulta_requisitos);
        
        $consulta_compras = "
        SELECT compras.id, descripcion, firma_llegada 
        FROM compras
        JOIN orden_compra ON compras.id_oc = orden_compra.id  -- Asegurate de que `oc.id` es la clave correcta para la unión
        WHERE firma_llegada IS NULL 
        AND ot = (SELECT ot FROM piezas WHERE id = $id_pieza);";
                                
             
        $compras_resultado = $conexion->query($consulta_compras);
        ?>

        <table>
            <tr>
                <th>Requisito pieza <button onclick="addRequisito()">+</button></th>
                <th>Requisito compra</th>
                <th>Acción</th>
            </tr>

            <?php
            if ($resultado_requisitos->num_rows > 0) {
                while ($fila = $resultado_requisitos->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($fila['nombre_prerrequisito']) . "</td>"; // Mostrar el nombre del prerrequisito
                    echo "<td>" . htmlspecialchars($fila['nombre_prerrequisito_compra']) . "</td>";
                    echo "<td>";
                        echo "<form action='' method='POST' style='display:inline;'>";
                            echo "<input type='hidden' name='id_requisito' value='" . $fila['id'] . "'>";
                            echo "<button type='submit' name='borrarRequisito'>Eliminar</button>";
                        echo "</form>";
                    echo "</td>";
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay prerrequisitos</td></tr>";
            }
            ?>

            <tr id="newRow" style="display: none;">
                <form action="" method="POST">
                    <input type="hidden" name="id_pieza" value="<?php echo $id_pieza; ?>">
                    <td>
                        <select name="prerrequisito">
                            <option value="">Selecciona una pieza</option>
                            <?php
                            while ($requisito = $requisitos_resultado->fetch_assoc()) {
                                echo "<option value=\"" . $requisito['id'] . "\">" . htmlspecialchars($requisito['pieza']) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name="prerrequisito_compra">
                            <option value="">Selecciona una compra</option>
                            <?php
                            while ($compra = $compras_resultado->fetch_assoc()) {
                                echo "<option value=\"" . $compra['id'] . "\">" . htmlspecialchars($compra['descripcion']) . "</option>";
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                        <input type="hidden" name="pestaña" value="prerrequisitos">
                        <button type="submit" name="agregarRequisito">Agregar</button>
                    </td>
                </form>
            </tr>
        </table>

       
    </div>
</body>
</html>


