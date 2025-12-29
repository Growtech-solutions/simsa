<?php 
include '../conexion.php'; 
 // Crear la conexión a la base de datos
        $conexion = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);
// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id_periodo = isset($_GET['periodo']) ? (int)$_GET['periodo'] : 0;
$id_trabajador = isset($_GET['trabajador_id']) ? (int)$_GET['trabajador_id'] : 0;

$sql_xml = "SELECT xml_timbrado FROM nomina WHERE periodo_id = ? AND trabajador_id = ?";

$stmt_xml = $conexion->prepare($sql_xml);
$stmt_xml->bind_param("ii", $id_periodo, $id_trabajador);
$stmt_xml->execute();
$result_xml = $stmt_xml->get_result();

if ($result_xml && $result_xml->num_rows > 0) {
    $row = $result_xml->fetch_assoc();
    $xml_data = $row['xml_timbrado'];

    // Evitar caracteres antes del XML
    ob_clean();

    // Establecer encabezados para la descarga del archivo XML
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="nomina_' . $id_trabajador . '.xml"');

    echo $xml_data;
    exit;
} else {
    echo "No se encontró el XML para el trabajador y periodo especificados.";
}

$stmt_xml->close();
$conexion->close();
