<?php 
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica si la conexión tuvo errores
if ($conexion->connect_error) {
    error_log("Error de conexión a la base de datos: " . $conexion->connect_error);
    exit;
}

if (isset($_GET['ot'])) {
    $ot = $_GET['ot'];
    $pedido = "SELECT id_pedido FROM ot WHERE ot = ?";
    $stmt = $conexion->prepare($pedido);

    $stmt->bind_param("s", $ot);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_pedido = $row['id_pedido'];

        $stmt = $conexion->prepare("SELECT id, descripcion FROM precios WHERE id_pedido = ?");

        $stmt->bind_param("s", $id_pedido);
        $stmt->execute();
        $result = $stmt->get_result();

        $actividades = [];
        while ($row = $result->fetch_assoc()) {
            $actividades[] = $row;
        }

        echo json_encode($actividades);
    } else {
        error_log("No se encontró OT: " . $ot);
        echo json_encode(["error" => "No se encontró la OT: $ot"]);
    }
} else {
    error_log("No se recibió parámetro 'ot'");
    echo json_encode(["error" => "No se recibió OT"]);
}
?>

