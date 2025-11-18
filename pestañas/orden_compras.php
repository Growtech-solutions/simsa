<!DOCTYPE html>
<html lang="en">
    
<body id="orden_compras"> 
    <style>
        .iniciar{
            margin: .3rem;
        }
    </style>
        <div class="principal">
            <section>
                <h1 class="text-2xl font-bold text-blue-600 text-center">Compras pendientes</h1>
                <br>
                <div class="buscador">

                    <form class="reporte_formulario" method="GET" action="">
                    
                    <label for="req">Req:</label>
                    <input class="formulario_reporte_ot" type="text" id="req" name="req" placeholder="Requisicion">
                    
                    <label for="oc">OC:</label>
                    <input class="formulario_reporte_ot" type="text" id="oc" name="oc" placeholder="OC">
                    
                    <label for="fecha_solicitud">Fecha solicitud:</label>
                    <input class="formulario_reporte_ot" type="date" id="fecha_solicitud" name="fecha_solicitud" placeholder="fecha_solicitud">

                    <label for="responsable">Responsable:</label>
                    <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable', 'formulario_reporte_ot'); ?>
                    
                    <label for="pieza">Provedor:</label>
                    <input class="formulario_reporte_ot" type="text" id="provedor" name="provedor" placeholder="Provedor">
                    
                    <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc); ?>">
                    <input type="hidden" name="pestaña" value="<?php echo htmlspecialchars($pestaña); ?>">

                    <input type="submit" value="Buscar">
                </form>
                </div> 
                <table border="1">

                <?php
echo '<script>
    function toggleTabla(id) {
        var tablas_ocultas = document.querySelectorAll(".tabla_oculta");
        for (var i = 0; i < tablas_ocultas.length; i++) {
            if (tablas_ocultas[i].id === "tabla_oculta_" + id) {
                tablas_ocultas[i].classList.toggle("oculto");
            } else {
                tablas_ocultas[i].classList.add("oculto");
            }
        }
    }
</script>';

$conexion->set_charset("utf8");



echo "<style>
.contenedor {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.centrado {
    text-align: center;
    flex: 1;
}
.derecha {
    text-align: right;
    cursor: pointer; /* Añadir un puntero al elemento para indicar que es interactivo */
}
.oculto {
    display: none;
}
.dinero {
    text-align: right;
}
</style>";


// Definir las variables de los filtros
$oc = isset($_GET['oc']) ? $_GET['orden_compra'] : null;
$fecha_solicitud = isset($_GET['fecha_solicitud']) ? $_GET['fecha_solicitud'] : null;
$responsable = isset($_GET['responsable']) ? $_GET['responsable'] : null;
$proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : null;
$req = isset($_GET['req']) ? $_GET['req'] : null;

// Consulta SQL base
$sql_compras = "SELECT * FROM compras WHERE 1=1";
$sql_orden_compras = "SELECT * FROM orden_compra WHERE firma_llegada IS NULL OR pago IS NULL";

// Agregar condiciones según los filtros proporcionados
if (!empty($req)) {
    $sql_orden_compras .= " AND id LIKE '%$req%'";
}
if (!empty($oc)) {
    $sql_orden_compras .= " AND orden_compra LIKE '%$oc%'";
}
if (!empty($responsable)) {
    $sql_orden_compras .= " AND responsable LIKE '%$responsable%'";
}
if (!empty($proveedor)) {
    $sql_orden_compras .= " AND proveedor LIKE '%$proveedor%'";
}
if (!empty($proveedor)) {
    $sql_orden_compras .= " AND fecha_solicitud LIKE '%$fecha_solicitud%'";
}
$sql_orden_compras .= " ORDER BY fecha_solicitud DESC";
// Ejecutar la consulta
$resultad = $conexion->query($sql_orden_compras);

// Mostrar los resultados
if ($resultad->num_rows > 0) {
    echo "<table border='1'>";
    while ($fila_oc = $resultad->fetch_assoc()) {
        echo "<tr style='background-color: #f2f2f2;'>";
            echo "<td style='border: none;'><strong>Req:</strong><a href='$header_loc.php?pestaña=editar_oc&header_loc=$header_loc&id=" .  $fila_oc['id'] . "'>" .  $fila_oc['id'] . "</a></td>";
            echo "<td style='border: none;'><strong>Solicitud:</strong> ". $fila_oc['fecha_solicitud'] . "</td>";
            echo "<td style='border: none;'><strong>OC:</strong> ".$fila_oc['oc'] . "</td>";
            echo "<td style='border: none;'><strong>Proveedor:</strong> ".  $fila_oc['proveedor'] . "</td>";
            echo "<td style='border: none;'><strong>Llegada:</strong> ". $fila_oc['llegada_estimada'] . "</td>";
            echo "<td style='border: none;'>";  
            if ($fila_oc['firma_llegada'] == null) {
                echo "<form action='' method='POST'>";
                    echo "<input type='hidden' name='id_firma_almacen' value='" . $fila_oc["id"] . "'>";
                    echo "<input style='border: 1px solid black; border-radius: 4px; padding: 5px 10px; cursor: pointer;' class='iniciar' type='submit' name='firma' value='Firma'>";
                echo "</form>";
            } else {
                echo "Firmado";
            }
            if ($fila_oc['pago'] == null) {
                echo "<form action='' method='POST'>";
                    echo "<input type='hidden' name='pagado' value='" . $fila_oc["id"] . "'>";
                    echo "<input style='border: 1px solid black; border-radius: 4px; padding: 5px 10px; cursor: pointer;' class='iniciar' type='submit' name='submit_pagado' value='Pagar'>";
                echo "</form>";
            } else {
                echo "Pagado";
            }
            echo "</td>";
            echo "<td style='border: none;' onclick='toggleTabla(" . $fila_oc['id'] . ")'>▼</td>";
            echo "<td style='border: none;'>
                    <a href='../php/generar_pdf.php?id=" . $fila_oc['id'] . "' target='_blank'>
                        <img src='../img/pdf.png' alt='Descargar PDF' style='width: 30px; height: auto;'>
                    </a>
                  </td>";
        echo "</tr>";

        // Agregar la tabla oculta aquí
        echo "<tr>
                <td colspan='9'>
                    <div id='tabla_oculta_" . $fila_oc['id'] . "' class='tabla_oculta oculto'>
                        <table border='1'>
                            <tr>
                                <th>Folio</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Descripción</th>
                                <th>Encargado</th>
                                <th>OT</th>
                                <th>Precio Unitario</th>
                                <th>Cotizacion</th>
                                <th>Comentarios</th>
                            </tr>";
                                // Consulta y muestra de datos en la tabla oculta
                                $sql_compras_filtrada = $sql_compras . " AND id_oc = '" . $fila_oc['id'] . "'";
                                $resultado_compras = $conexion->query($sql_compras_filtrada);
                                while ($fila_compras = $resultado_compras->fetch_assoc()) {
                                    echo "<tr>";
                                        echo "<td><a href='$header_loc.php?pestaña=editar_compras&header_loc=$header_loc&id=" . $fila_compras['id'] . "'>" . $fila_compras['id'] . "</a></td>";
                                        echo "<td>" . $fila_compras['cantidad']. "</td>";
                                        echo "<td>" . $fila_compras['unidad'] . "</td>";
                                        echo "<td>" . " (".$fila_compras['moneda'].") ".$fila_compras['descripcion'] . "</td>";
                                        echo "<td>" . $fila_compras['responsable'] . "</td>";
                                        echo "<td>" . $fila_compras['ot'] . "</td>";
                                        echo "<td class='dinero'>" . $fila_compras['precio_unitario']. "</td>";
                                        echo "<td>" . $fila_compras['cotizacion'] . "</td>";
                                        echo "<td>" . $fila_compras['comentarios'] . "</td>";
                                        $moneda=$fila_compras['moneda'];
                                    echo "</tr>";
                                }
                        echo "</table>
                        <br>
                        <div class='dinero'>Valor dólar: " . $fila_oc['tipo_cambio'] . "</div>
                        <div class='dinero'>Subtotal: " . $fila_oc['subtotal'] ."</div>
                        <div class='dinero'>Neto: " . $fila_oc['neto'] ." (".$moneda.") " ."</div>
                        <div class='dinero'>Neto en pesos: " . $fila_oc['total_pesos'] . " (MXN) "."</div>";

                        // Listar y mostrar todos los archivos dentro de la carpeta
                        $ruta_carpeta = "../documentos/finanzas/cotizaciones/" . $fila_oc['id'];
                        if (is_dir($ruta_carpeta)) {
                            $archivos = scandir($ruta_carpeta);
                            if (count($archivos) > 2) { // 2 porque . y .. son siempre presentes
                                echo "<div>Archivos en la carpeta:</div>";
                                echo "<ul>";
                                foreach ($archivos as $archivo) {
                                    if ($archivo != '.' && $archivo != '..') {
                                        $ruta_archivo = $ruta_carpeta . "/" . $archivo;
                                        echo "<li><a href='$ruta_archivo' target='_blank'>$archivo</a></li>";
                                    }
                                }
                                echo "</ul>";
                            } else {
                                echo "<div>No hay archivos disponibles.</div>";
                            }
                        } else {
                            echo "<div>No hay carpeta disponible.</div>";
                        }

                    echo "</div>
                </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron requisiciones.";
}

?>

                
            </table>
        </div>
    </main>
</body>
</html>