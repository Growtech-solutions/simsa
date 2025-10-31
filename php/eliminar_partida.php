<?php
 include '../conexion.php'; 
 $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }

$header_loc= $_GET['header_loc']; 
$partida_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_pedido = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : 0;

if ($partida_id > 0) {
    // Eliminar la partida
    $sql_delete_partida = "DELETE FROM partidas WHERE id = ?";
    $stmt_delete_partida = $conexion->prepare($sql_delete_partida);
    $stmt_delete_partida->bind_param('i', $partida_id);
    $stmt_delete_partida->execute();
    $stmt_delete_partida->close();

    // Redirigir después de la eliminación
    header("Location: " . $_SERVER['HTTP_REFERER']);
    
    exit;
}

$conexion->close();
?>
