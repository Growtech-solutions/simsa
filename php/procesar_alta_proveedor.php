<?php
// Verificar si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configurar la conexión a la base de datos
    include '../conexion.php'; 

    // Crear una conexión a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
$proveedor = isset($_POST['proveedor']) ? $_POST['proveedor'] : null;
$direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : null;
$correo = isset($_POST['correo']) ? $_POST['correo'] : null;
$periodo_pago = isset($_POST['periodo_pago']) ? $_POST['periodo_pago'] : 0;
$header_loc= isset($_POST['header_loc']) ? $_POST['header_loc'] : null;
   
    // Definir el estado como 1
    $estado = 1;

    // Preparar la consulta SQL para insertar los datos en la tabla ot
    $sql = "INSERT INTO proveedor (proveedor, direccion, telefono, correo, periodo_pago) 
            VALUES ('$proveedor', '$direccion', '$telefono', '$correo', '$periodo_pago')";  

    // Ejecutar la consulta SQL
    if ($conexion->query($sql) === TRUE) {
        $confirmacion="Registro insertado correctamente.";
        // Redirige a proyectos.php inmediatamente
        header("Location: ../header_main_aside/$header_loc.php?pestaña=altadeproveedor&confirmacion=" . urlencode($confirmacion));
        exit;
    
    } else {
        echo "Error al insertar el registro: vulva a revisas los datos" . $conexion->error;
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    $confirmacion="Error al insertar el registro: vulva a revisas los datos";
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
        exit();
}