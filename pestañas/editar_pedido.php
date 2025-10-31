<?php

$id_pedido = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$header_loc= $_GET['header_loc']; 

// Obtener los datos del pedido
$sql_pedido = "SELECT id, ot, pedido.descripcion, pedido.fecha_alta FROM pedido left join ot on ot.id_pedido = pedido.id WHERE id = ?";
$stmt_pedido = $conexion->prepare($sql_pedido);
$stmt_pedido->bind_param('i', $id_pedido);
$stmt_pedido->execute();
$stmt_pedido->store_result();
$stmt_pedido->bind_result($id, $ot, $descripcion, $fecha_alta);
$stmt_pedido->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar el pedido
    $nueva_descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $nueva_fecha_alta = $conexion->real_escape_string($_POST['fecha_alta']);
    
    $sql_update_pedido = "UPDATE pedido SET descripcion = ?, fecha_alta = ? WHERE id = ?";
    $stmt_update_pedido = $conexion->prepare($sql_update_pedido);
    $stmt_update_pedido->bind_param('ssi', $nueva_descripcion, $nueva_fecha_alta, $id_pedido);
    $stmt_update_pedido->execute();
    $stmt_update_pedido->close();

    echo '<meta http-equiv="refresh" content="1">';
    header("Location: " . $_SERVER['PHP_SELF']);
    
}

// Obtener las partidas asociadas
$sql_partidas = "SELECT id, cantidad, descripcion, precio_unitario, moneda, total, total_pesos, tipo_cambio, unidad 
                 FROM partidas WHERE id_pedido = ?";
$stmt_partidas = $conexion->prepare($sql_partidas);
$stmt_partidas->bind_param('i', $id_pedido);
$stmt_partidas->execute();
$stmt_partidas->store_result();
$stmt_partidas->bind_result($partida_id, $cantidad, $partida_descripcion, $precio_unitario, $moneda, $total, $total_pesos, $tipo_de_cambio, $unidad);

?>
<div>
    <h2>Editar Pedido</h2>
<form method="post" action="">
    <label>OT: <?php echo htmlspecialchars($ot); ?></label>
    <label>Descripción:</label>
    <input type="text" name="descripcion" value="<?php echo htmlspecialchars($descripcion); ?>">
    <label>Fecha Alta:</label>
    <input type="date" name="fecha_alta" value="<?php echo htmlspecialchars($fecha_alta); ?>">
    <input type="submit" value="Actualizar Pedido">
</form>

<h2>Partidas del Pedido</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Cantidad</th>
        <th>Unidad</th>
        <th>Descripción</th>
        <th>Precio Unitario</th>
        <th>Total</th>
        <th>Moneda</th>
        <th>Tipo de Cambio</th>
        <th>Total en Pesos</th>
        <th>Acciones</th>
    </tr>
    <?php while ($stmt_partidas->fetch()): ?>
    <tr>
        <td><?php echo htmlspecialchars($partida_id); ?></td>
        <td><?php echo htmlspecialchars($cantidad); ?></td>
        <td><?php echo htmlspecialchars($unidad); ?></td>
        <td><?php echo htmlspecialchars($partida_descripcion); ?></td>
        <td><?php echo htmlspecialchars($precio_unitario); ?></td>
        <td><?php echo htmlspecialchars($total); ?></td>
        <td><?php echo htmlspecialchars($moneda); ?></td>
        <td><?php echo htmlspecialchars($tipo_de_cambio); ?></td>
        <td><?php echo htmlspecialchars($total_pesos); ?></td>
        <td>
            <a href="../header_main_aside/<?php echo htmlspecialchars($header_loc); ?>.php?pestaña=editar_partida&id=<?php echo htmlspecialchars($partida_id); ?>&id_pedido=<?php echo htmlspecialchars($id_pedido); ?>&header_loc=<?php echo htmlspecialchars($header_loc); ?>">Editar</a> | 
            <a href="../php/eliminar_partida.php?id=<?php echo htmlspecialchars($partida_id); ?>&id_pedido=<?php echo htmlspecialchars($id_pedido); ?>&header_loc=<?php echo htmlspecialchars($header_loc); ?>" onclick="return confirm('¿Está seguro de que desea eliminar esta partida?');">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<br>
<a href='?pestaña=pedidos&header_loc=<?php echo $header_loc; ?>&id=<?php echo $id_pedido; ?>'>Volver a pedidos</a> <br>

<a href="?pestaña=agregar_partida&header_loc=<?php echo $header_loc; ?>&id_pedido=<?php echo $id_pedido; ?>">Agregar Partida</a>

<?php
$stmt_pedido->close();
$stmt_partidas->close();
$conexion->close();
?>

</div>
