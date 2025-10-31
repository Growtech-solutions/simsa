<?php
// archivo de actualización (ejemplo: actualizar_estado.php)
require_once '../conexion.php'; // Conexión a la base de datos
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }

if (isset($_POST['ot']) && isset($_POST['estado'])) {
    $ot = intval($_POST['ot']);
    $estado = $_POST['estado'];

    // Actualizar el estado en la base de datos
    $sql = "UPDATE ot SET estado = ? WHERE ot = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $estado, $ot);
    
    if ($stmt->execute()) {
        echo "Estado actualizado correctamente";
    } else {
        echo "Error al actualizar el estado: " . $conexion->error;
    }
    $stmt->close();
    $conexion->close();
    exit; // Detener el script después de procesar la solicitud
}
