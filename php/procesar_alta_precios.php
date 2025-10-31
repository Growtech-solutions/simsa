<?php
include '../conexion.php';
date_default_timezone_set('America/Monterrey');
session_start();

if (!isset($_SESSION['username'])) {
    die("Error: Usuario no autenticado.");
}

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("La conexión falló: " . $conexion->connect_error);
}

$ot = $_POST['ot'];
$header_loc = $_POST['header_loc'];
$unidad = $_POST['unidad'];
$descripcion = $_POST['descripcion'];
$precio_unitario = $_POST['precio_unitario'];

$mensajeDatos = '';

// Start transaction
$conexion->begin_transaction();
try {
    // Iterate over each row (fields in the arrays)
    for ($i = 0; $i < count($descripcion); $i++) {
        // Validate inputs to avoid empty values
        if (!empty($descripcion[$i]) && !empty($unidad[$i]) && !empty($precio_unitario[$i])) {
            $sql_insert_precios = "INSERT INTO precios (ot, descripcion, unidad, precio) VALUES (?, ?, ?, ?)";
            $stmt_oc = $conexion->prepare($sql_insert_precios);

            if ($stmt_oc === false) {
                throw new Exception("Error al preparar la consulta: " . $conexion->error);
            }

            // Bind parameters (assuming $ot is a string, $descripcion[$i] a string, $unidad[$i] a string, $precio_unitario[$i] a float)
            $stmt_oc->bind_param("sssd", $ot, $descripcion[$i], $unidad[$i], $precio_unitario[$i]);
            $stmt_oc->execute();
        }
    }

    // Commit transaction
    $conexion->commit();
    $mensajeDatos = "Los datos se han guardado correctamente.";

} catch (Exception $e) {
    // Rollback in case of error
    $conexion->rollback();
    $mensajeDatos = "Error: " . $e->getMessage();
}

// Close connection
$conexion->close();

// Redirect with the message
header("Location: " . $_SERVER['HTTP_REFERER'] . "&mensajeDatos=" . urlencode($mensajeDatos));
exit();
?>
