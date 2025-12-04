<?php
include '../conexion_transimex.php';
$conexion_transimex = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica si la conexión fue exitosa
if ($conexion_transimex->connect_error) {
    die("Conexión fallida: " . $conexion_transimex->connect_error);
}

$nombre = $_POST['nombre_turno'];
$descripcion = $_POST['descripcion'];
$entrada = $_POST['entrada'];
$salida = $_POST['salida'];
$descanso = $_POST['descanso'];

// Insertar turno
$stmt = $conexion_transimex->prepare("INSERT INTO turnos (nombre_turno, descripcion) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $descripcion);
$stmt->execute();
$id_turno = $conexion_transimex->insert_id;

// Insertar horarios
foreach ($entrada as $dia => $hora_entrada) {
    if (!empty($hora_entrada) && !empty($salida[$dia])) {
        $min_descanso = !empty($descanso[$dia]) ? intval($descanso[$dia]) : 0;
        $stmt2 = $conexion_transimex->prepare("INSERT INTO turno_horarios (id_turno, dia_semana, hora_entrada, hora_salida, minutos_descanso) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssi", $id_turno, $dia, $hora_entrada, $salida[$dia], $min_descanso);
        $stmt2->execute();
    }
}

header("Location:" . $_SERVER['HTTP_REFERER'] );
exit;
?>
