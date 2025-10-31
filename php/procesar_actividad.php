<?php
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verifica si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$id_periodico = $_POST["periodicos"];
$fecha = $_POST["fecha"];
$resumen = $_POST["resumen"];
$header_loc = $_POST["header_loc"];

$sql_periodico = "SELECT objeto, id_act FROM periodicos WHERE id = ?";
$stmt = $conexion->prepare($sql_periodico);
$stmt->bind_param("i", $id_periodico);
$stmt->execute();
$resultado = $stmt->get_result();
$periodico_info = $resultado->fetch_assoc();

if (!$periodico_info) {
    die("Error: No se encontró el periódico.");
}
$id_actividad = $periodico_info["id_act"];
$objeto = $periodico_info["objeto"];

$sql_tipo = "SELECT * FROM actividades WHERE id = ?";
$stmt = $conexion->prepare($sql_tipo);
$stmt->bind_param("i", $id_actividad);
$stmt->execute();
$resultado = $stmt->get_result();
$actividad_info = $resultado->fetch_assoc();


$tipo = $actividad_info["tipo"];
$actividad_nombre = $actividad_info["actividad"];

// Crear la carpeta de almacenamiento con todas las subcarpetas necesarias
// Verifica que las variables $tipo y $actividad_nombre no estén vacías
if (empty($tipo) || empty($actividad_nombre)) {
    die("Error: La variable tipo o actividad_nombre está vacía.");
}

$directorio = "../SIG/$tipo/$actividad_nombre/";

if (!file_exists($directorio)) {
    if (!mkdir($directorio, 0777, true)) {
        die("Error al crear la carpeta: " . $directorio);
    }
}


// Procesar la carga del archivo
$archivo_nombre = null;
if (!empty($_FILES["doc_act"]["name"])) {
    $extension = pathinfo($_FILES["doc_act"]["name"], PATHINFO_EXTENSION);
    $archivo_nombre = $fecha . "_" . $objeto . "." . $extension;
    $ruta_archivo = $directorio . $archivo_nombre;

    if (!move_uploaded_file($_FILES["doc_act"]["tmp_name"], $ruta_archivo)) {
        die("Error al subir el archivo a: " . $ruta_archivo);
    }
    
}


// Insertar un registro por cada trabajador seleccionado
for ($i = 1; $i <= 24; $i++) {
    if (!empty($_POST["trabajador$i"])) {
        $id_trabajador = $_POST["trabajador$i"];

        $sql_insert = "INSERT INTO historial_actividades (id_encargado, actividad, fecha, resumen) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql_insert);
        $stmt->bind_param("iiss", $id_trabajador, $id_periodico, $fecha, $resumen);
        $stmt->execute();
    }
}

$stmt->close();
$conexion->close();

$confirmacion = "Registro insertado correctamente.";
header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
exit();
?>
