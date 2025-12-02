<?php
 include '../conexion_transimex.php'; 
 $conexion = new mysqli($host_transimex, $usuario_transimex, $contrasena_transimex, $base_de_datos_transimex);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }
$asistencia_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($asistencia_id > 0) {
    // Eliminar la asistencia
    $sql_delete_asistencia = "DELETE FROM asistencia WHERE id = ?";
    $stmt_delete_asistencia = $conexion->prepare($sql_delete_asistencia);
    $stmt_delete_asistencia->bind_param('i', $asistencia_id);
    $stmt_delete_asistencia->execute();
    $stmt_delete_asistencia->close();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
else {
    echo "ID de asistencia no proporcionado.";
    exit;
}

$conexion->close();
?>
