<?php
include '../../conexion.php';
$conexion = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);
date_default_timezone_set('America/Monterrey');

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si los parámetros están definidos
    if (isset($_POST['image']) && isset($_POST['latitude']) && isset($_POST['longitude'])) {
        // Obtener los datos enviados por el formulario (imagen, latitud, longitud)
        $image = $_POST['image'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $timestamp = date('Y-m-d H:i:s');

        // Decodificar imagen base64 y guardarla en un archivo
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
        $temp_file = '/tmp/reconocimiento_' . uniqid() . '.png';
        file_put_contents($temp_file, $data);

        // Ejecutar el script Python con la ruta del archivo
        $command = "/var/www/venv/bin/python /var/www/transimex/CRM/asistencia/reconocimiento.py '{$temp_file}'";
        $output = shell_exec($command);
        unlink($temp_file); // Borrar después de usar

        // Depuración: Mostrar el output completo
        error_log("Salida del script Python: " . $output);  // Log en el archivo de errores

        // Verificar el resultado del script Python
        if (strpos($output, "Desconocido") === false) {
            $worker_id = trim($output); // El ID del trabajador que fue reconocido
        } else {
            $worker_id = null; // Si es desconocido
        }

        // Depuración: Verificar el valor de $worker_id
        error_log("ID del trabajador: " . $worker_id);  // Log en el archivo de errores

        // Guardar la asistencia en la base de datos
        $stmt = $conexion->prepare("INSERT INTO asistencia (latitud, longitud, fecha, trabajador_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ddss", $latitude, $longitude, $timestamp, $worker_id);
        $stmt->execute();

        // Consulta para obtener el nombre y apellido del trabajador
        $sql_nombre = "SELECT nombre, apellidos FROM trabajadores WHERE id = $worker_id";
        $resultado = $conexion->query($sql_nombre);

        // Si el trabajador existe, obtenemos el nombre y apellidos
        if ($resultado->num_rows > 0) {
            $trabajador = $resultado->fetch_assoc();
            $nombre = $trabajador['nombre'];
            $apellidos = $trabajador['apellidos'];
        } else {
            $nombre = "No identificado";
            $apellidos = "";
        }

        // Preparar la consulta SQL para obtener las ubicaciones dentro de un rango de 2 km
        $sql = "SELECT
            id,
            nombre,
            latitud,
            longitud,
            (6371000 * acos(
                cos(radians($latitude)) * cos(radians(latitud)) * cos(radians(longitud) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitud))
            )) AS distancia_m
        FROM ubicaciones
        HAVING distancia_m <= 2000;";

        // Ejecutar la consulta
        $resultado = $conexion->query($sql);

        // Verificar los resultados
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $ubicacion_nombre = $row["nombre"];
            }
        } else {
            $ubicacion_nombre = "Fuera de planta";
        }

        echo json_encode([
            'worker_id' => $worker_id,
            'worker_name' => $nombre . ' ' . $apellidos,
            'ubicacion' => $ubicacion_nombre
        ]);

        $stmt->close();
        $conexion->close();
    } else {
        echo json_encode(['error' => 'Faltan datos necesarios']);
    }
}
?>
