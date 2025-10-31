<?php
// Verificar si se recibió el parámetro "ot" por GET
if(isset($_GET['ot'])) {
    // Obtener el valor de la OT desde el parámetro GET
    $ot = $_GET['ot'];

    // Preparar la conexión a la base de datos
    include '../conexion.php'; 
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Consulta para obtener el nombre del proyecto asociado a la OT
    $consulta_nombre_proyecto = "SELECT descripcion FROM ot WHERE ot = '$ot'";
    $resultado_nombre_proyecto = $conexion->query($consulta_nombre_proyecto);

    // Verificar si se encontró el nombre del proyecto
    if ($resultado_nombre_proyecto->num_rows > 0) {
        // Obtener el nombre del proyecto
        $fila = $resultado_nombre_proyecto->fetch_assoc();
        $nombre_proyecto = $fila['descripcion'];

        // Devolver el nombre del proyecto como respuesta
        echo $nombre_proyecto;
    } else {
        // Si no se encontró el nombre del proyecto, devolver un mensaje de error
        echo "Nombre del proyecto no encontrado";
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se recibió el parámetro "ot", devolver un mensaje de error
    echo "Error: no se proporcionó el número de OT";
}
?>
