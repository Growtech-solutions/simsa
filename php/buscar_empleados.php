<?php
include '../conexion_transimex.php'; 

$conexion_transimex = new mysqli($host_transimex, $usuario_transimex, $contrasena_transimex, $base_de_datos_transimex);

if ($conexion_transimex->connect_error) {
    die("Error de conexión: " . $conexion_transimex->connect_error);
}

$search = $_GET['q'] ?? '';
$sql = "SELECT id, CONCAT(nombre, ' ', apellidos) as nombre_completo FROM trabajadores 
        WHERE id LIKE ? OR CONCAT(nombre, ' ', apellidos) LIKE ? LIMIT 10";
$stmt = $conexion_transimex->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta"]);
    exit;
}
$param = '%' . $search . '%';
$stmt->bind_param('ss', $param, $param);
$stmt->execute();
$result = $stmt->get_result();

$resultados = [];
while ($row = $result->fetch_assoc()) {
    $resultados[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre_completo']
    ];
}

$stmt->close();
$conexion_transimex->close();

header('Content-Type: application/json');
echo json_encode($resultados);
