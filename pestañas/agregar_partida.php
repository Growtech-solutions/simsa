<?php
$header_loc = isset($_GET['header_loc']) ? $_GET['header_loc'] : 'index';
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cantidad = $conexion->real_escape_string($_POST['cantidad']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $precio_unitario = $conexion->real_escape_string($_POST['precio_unitario']);
    $moneda = $conexion->real_escape_string($_POST['moneda']);
    $unidad = $conexion->real_escape_string($_POST['unidad']);

    $sql_insert_partida = "INSERT INTO partidas (id_pedido, cantidad, descripcion, precio_unitario, moneda, unidad) 
                           VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert_partida = $conexion->prepare($sql_insert_partida);
    $stmt_insert_partida->bind_param('iisdss', $id_pedido, $cantidad, $descripcion, $precio_unitario, $moneda, $unidad);
    $stmt_insert_partida->execute();
    $stmt_insert_partida->close();

    echo '<meta http-equiv="refresh" content="1">';
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<div>
    <h2>Agregar Partida</h2>
<form method="post">
    <label>Cantidad:</label>
    <input class='entrada' type="number" step="0.01" name="cantidad" placeholder='Cantidad' required>
    <label>Descripcion:</label>
    <input class='entrada' type="text" name="descripcion" placeholder='Descripción' required>
    <label>Precio unitario:</label>
    <input class='entrada' type="number" step="0.01" name="precio_unitario" placeholder='Precio Unitario' required>
    <br><br>
    <label>Moneda:</label>
    <?php $selectDatos->obtenerOpciones('listas', 'moneda', 'moneda', 'entrada');?> 
    <label>Unidad:</label>
    <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad', 'entrada');?> 
    <br> <br>
    <input type="submit" value="Agregar Partida">
    <a href='?pestaña=editar_pedido&header_loc=<?php echo $header_loc; ?>&id=<?php echo $id_pedido; ?>'>Volver al Pedido</a>
</form>



<?php
$conexion->close();
?>
</div>

