<?php
header('Content-Type: application/json');
include '../conexion.php';

    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
$ot = htmlspecialchars($_GET['ot'] ?? '', ENT_QUOTES, 'UTF-8');
$response = [];

if (!empty($ot)) {
    $sql = "SELECT id, pieza FROM piezas WHERE ot = ? and fecha_final is null ORDER BY pieza";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $ot);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'id' => $row['id'],
            'pieza' => $row['pieza'] 
        ];
    }

    $stmt->close();
}

echo json_encode($response);
?>
