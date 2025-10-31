<?php

$partida_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 0;
$header_loc= $_GET['header_loc'];

// Obtener los datos de la partida
$sql_partida = "SELECT cantidad, descripcion, precio_unitario, moneda, tipo_cambio, unidad FROM partidas WHERE id = ?";
$stmt_partida = $conexion->prepare($sql_partida);
$stmt_partida->bind_param('i', $partida_id);
$stmt_partida->execute();
$stmt_partida->store_result();
$stmt_partida->bind_result($cantidad, $descripcion, $precio_unitario, $moneda, $tipo_de_cambio, $unidad);
$stmt_partida->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar la partida
    $nueva_cantidad = $conexion->real_escape_string($_POST['cantidad']);
    $nueva_descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $nuevo_precio_unitario = $conexion->real_escape_string($_POST['precio_unitario']);
    $nueva_moneda = $conexion->real_escape_string($_POST['moneda']);
    $nuevo_tipo_de_cambio = $conexion->real_escape_string($_POST['tipo_de_cambio']);
    $nueva_unidad = $conexion->real_escape_string($_POST['unidad']);
    
    $nuevo_total = $nuevo_precio_unitario * $nueva_cantidad;
    $nuevo_total_pesos = ($nueva_moneda === 'USD') ? $nuevo_total * $nuevo_tipo_de_cambio : $nuevo_total;

    $sql_update_partida = "UPDATE partidas SET cantidad = ?, descripcion = ?, precio_unitario = ?, moneda = ?, total = ?, total_pesos = ?, tipo_cambio = ?, unidad = ? WHERE id = ?";
    $stmt_update_partida = $conexion->prepare($sql_update_partida);
    $stmt_update_partida->bind_param('dsdssdssi', $nueva_cantidad, $nueva_descripcion, $nuevo_precio_unitario, $nueva_moneda, $nuevo_total, $nuevo_total_pesos, $nuevo_tipo_de_cambio, $nueva_unidad, $partida_id);
    $stmt_update_partida->execute();
    $stmt_update_partida->close();

    echo '<meta http-equiv="refresh" content="1">';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<div>
    <h2>Editar Partida</h2>
<form method="post">
    <label>Cantidad:</label>
    <input class='entrada' type="number" step="0.01" name="cantidad" value="<?php echo htmlspecialchars($cantidad); ?>" required>
    <label>Descripción:</label>
    <input class='entrada'  type="text" name="descripcion" value="<?php echo htmlspecialchars($descripcion); ?>" required>
    <label>Precio Unitario:</label>
    <input class='entrada'  type="number" step="0.01" name="precio_unitario" value="<?php echo htmlspecialchars($precio_unitario); ?>" required><br><br>
    <label>Moneda:</label>
    <select class="entrada requisicion" name="moneda" required>
                <option value="<?php echo htmlspecialchars($moneda); ?>"><?php echo htmlspecialchars($moneda); ?></option>
                <option value="MXN">MXN</option>
                <option value="USD">USD</option>
            </select>
    <label>Tipo de Cambio:</label>
    <input class='entrada'  type="number" step="0.01" name="tipo_de_cambio" value="<?php echo htmlspecialchars($tipo_de_cambio); ?>" required>
    <label>Unidad:</label>
    <select class="entrada" name="unidad" required>
        <option value="<?php echo htmlspecialchars($unidad); ?>"><?php echo htmlspecialchars($unidad); ?></option>
        <option value="pzs">pzs</option>
        <option value="lts">lts</option>
        <option value="mts">mts</option>
        <option value="kg">kg</option>
        <option value="Galones">Galones</option>
        <option value="Servicio">Servicio</option>
    </select>
    <br><br>
    <input type="submit" value="Actualizar Partida">
</form>

<a href='?pestaña=editar_pedido&header_loc=<?php echo $header_loc; ?>&id=<?php echo $id_pedido; ?>'>Volver al Pedido</a>

<?php
$stmt_partida->close();
$conexion->close();
?>

</div>
