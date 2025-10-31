<?php
 include '../conexion.php'; 
 $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }
 
$id_pedido = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$header_loc= $_GET['header_loc'];

if ($id_pedido > 0) {
    // Eliminar las partidas asociadas al pedido
    $sql_delete_partidas = "DELETE FROM partidas WHERE id_pedido = ?";
    $stmt_delete_partidas = $conexion->prepare($sql_delete_partidas);
    $stmt_delete_partidas->bind_param('i', $id_pedido);
    $stmt_delete_partidas->execute();
    $stmt_delete_partidas->close();

    // Eliminar el pedido
    $sql_delete_pedido = "DELETE FROM pedido WHERE id = ?";
    $stmt_delete_pedido = $conexion->prepare($sql_delete_pedido);
    $stmt_delete_pedido->bind_param('i', $id_pedido);
    $stmt_delete_pedido->execute();
    $stmt_delete_pedido->close();

    // Redirigir después de la eliminación
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$conexion->close();
?>
