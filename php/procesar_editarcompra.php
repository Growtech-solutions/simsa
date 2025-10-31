<?php
include '../conexion.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Establecer la conexión a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Actualizar los datos si se envía el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $responsable = $_POST['responsable'];
        $ot = $_POST['ot'];
        $cantidad = $_POST['cantidad'];
        $unidad = $_POST['unidad'];
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
        $moneda = $_POST['moneda'];
        $precio_unitario = empty($_POST['precio_unitario']) || $_POST['precio_unitario'] == '0.00' ? null : $_POST['precio_unitario'];
        $comentarios = $_POST['comentarios'];
        $cotizacion = $_POST['cotizacion'];
        $header_loc = $_POST['header_loc'];

        // Actualizar los datos de la compra
        $sql_update = "UPDATE compras SET 
            responsable = '$responsable',
            ot = '$ot',
            cantidad = '$cantidad',
            unidad = '$unidad',
            descripcion = '$descripcion',
            moneda='$moneda',
            precio_unitario = " . ($precio_unitario === null ? "NULL" : "'$precio_unitario'") . ",
            comentarios = '$comentarios',
            cotizacion = '$cotizacion'
            WHERE id = $id";

        if ($conexion->query($sql_update) === TRUE) {
            $confirmacion = "Compra actualizada correctamente.";
                header("Location: " . $_SERVER['HTTP_REFERER']. "&confirmacion=" . urlencode($confirmacion));
                exit;
        } else {
            echo "Error al actualizar los datos: " . $conexion->error;
        }
    }

    $conexion->close();
} else {
    echo "ID no proporcionado.";
}
