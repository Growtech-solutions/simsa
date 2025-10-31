<!DOCTYPE html>
<html lang="en">
<body id="compras">
    <main>
        <style>
           .centrado {
            text-align: center;
        }
        .formulario_reporte_fecha {
            margin-right: 10px;
        }
        .formulario_reporte_area {
            margin-bottom: 10px;
        }
        .reporte_tabla {
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid black;
            padding: 1rem;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .iniciar{
            margin: .3rem;
        }
        .centrado {
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
        }
        .descripcion{
            text-align:left;
        }
        .vacio {
            border: none;
        }
        .tabla {
            width: 100%;
        }
        table {
            width: 95%;
            margin: 0 auto; /* Centrar la tabla horizontalmente */
            border-collapse: collapse; /* Asegura que no haya espacios entre los bordes de las celdas */
        }
        th, td {
            border: none; /* Elimina los bordes de las celdas */
            padding: 8px;
            text-align: center; /* Centra el texto en las celdas */
        }
        th {
            background-color: #f2f2f2; /* Color de fondo para el encabezado */
        }
        .altadeproyecto__boton__enviar {
            margin-top: 20px;
            text-align: center;
        }
        .boton__enviar {
            width: 20%;
            background-color: var(--gris);
            color: white;
            border: none;
            cursor: pointer;
        }
        .boton__enviar:hover {
            background-color: var(--gris-claro);
        }
    </style>
        <div class="principal">
            <section>
                <h1>Compras pendientes</h1>
                <div class="buscador">

                    <form class="reporte_formulario" method="GET" action="">
                    <label for="ot">OT:</label>
                    <input class="formulario_reporte_ot" type="text" id="ot" name="ot" placeholder="Buscar por OT">

                    <label for="descripcion">Pieza:</label>
                    <input class="formulario_reporte_ot" type="text" id="descripcion" name="descripcion" placeholder="Descripcion">
                    
                    
                    <label for="fecha_solicitud">Fecha_solicitud:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitud" name="fecha_solicitud" placeholder="fecha_solicitud">
                    
                    <label for="responsable">Responsable:</label>
                     <select class="formulario_reporte_ot" name="responsable" id="responsable" placeholder="Responsable">
                        <option>Seleccione responsable</option>
                        <option value="Andrés Quevedo">Andrés Quevedo</option>
                        <option value="Bernardo Garza">Bernardo Garza</option>
                        <option value="Mario Vázquez">Mario Vázquez</option>
                        <option value="Raúl Domingo">Raúl Domingo </option>
                    </select>
                    <br><br>
                    <label for="pieza">Provedor:</label>
                    <input class="formulario_reporte_ot" type="text" id="provedor" name="provedor" placeholder="Provedor">
                    <input type="hidden" name="pestaña" value="compras">
                    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                    <input type="submit" value="Buscar">
                </form>
                </div> <br>
                <table border="1">

                <?php
                    // Definir las variables de filtro
                    $ot = isset($_GET['ot']) ? $_GET['ot'] : null;
                    $descripcion = isset($_GET['descripcion']) ? $_GET['descripcion'] : null;
                    $oc = isset($_GET['oc']) ? $_GET['oc'] : null;
                    $fecha_solicitud = isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : null;
                    $responsable = isset($_GET['responsable']) ? $_GET['responsable'] : null;
                    $proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : null;

                    // Consulta SQL base
                    $sql_compras = "SELECT * FROM compras WHERE id_oc IS NULL";

                    // Agregar condiciones basadas en los filtros proporcionados
                    if (!empty($ot)) {
                        $sql_compras .= " AND ot LIKE '%$ot%'";
                    }
                    if (!empty($descripcion)) {
                        $sql_compras .= " AND descripcion LIKE '%$descripcion%'";
                    }
                    if (!empty($oc)) {
                        $sql_compras .= " AND oc LIKE '%$oc%'";
                    }
                    if (!empty($fecha_solicitud)) {
                        $sql_compras .= " AND fecha_solicitud LIKE '%$fecha_solicitud%'";
                    }
                    if (!empty($responsable)) {
                        $sql_compras .= " AND responsable LIKE '%$responsable%'";
                    }
                    if (!empty($proveedor)) {
                        $sql_compras .= " AND proveedor LIKE '%$proveedor%'";
                    }

                    // Ejecutar la consulta
                    $resultado = $conexion->query($sql_compras);

                    // Mostrar los resultados
                    if ($resultado->num_rows > 0) {
                        echo "<form class='tabla' action='$header_loc.php?pestaña=crear_requisicion&header_loc=<?php echo $header_loc; ?>' method='POST'>"; // Formulario para múltiples selecciones
                            echo "<table>";
                            echo "<tr>
                                    <th class='vacio'></th>
                                    <th>Folio</th>
                                    <th>OT</th>
                                    <th>Cantidad</th>
                                    <th>Descripción</th>
                                    <th>Precio unitario</th>
                                    <th>Cotizacion</th>
                                    <th>Comentarios</th>
                                    <th>Responsable</th>
                                </tr>";
                        
                            while ($fila = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='centrado vacio'><input type='checkbox' name='seleccionado[]' value='" . $fila["id"] . "'></td>";
                                echo "<td><a href='../header_main_aside/$header_loc.php?pestaña=editar_compras&id=" . $fila['id'] . "&header_loc=$header_loc'>" . $fila['id'] . "</a></td>";
                                echo "<td>" . $fila['ot'] . "</td>";
                                echo "<td>" . $fila['cantidad'] . " " . $fila['unidad'] . "</td>";
                                echo "<td class='descripcion'>" . $fila['descripcion'] . "</td>";
                                echo "<td>" . $fila['precio_unitario'] . " " . $fila['moneda'] . "</td>";
                                echo "<td>" . $fila['cotizacion'] . "</td>";
                                echo "<td>" . $fila['comentarios'] . "</td>";
                                echo "<td>" . $fila['responsable'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<br>";
                            echo "<div class='altadeproyecto__boton__enviar'>";
                            echo "<input class='boton__enviar' type='submit' value='Generar requisicion'>";
                            echo "</div>";
                        echo "</form>";
                    } else {
                        echo "No se encontraron piezas.";
                    }
                ?>
                
            </table>
        </div>
    </main>
</body>
</html>