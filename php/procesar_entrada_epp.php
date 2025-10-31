<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php'; 
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si se enviaron datos mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se enviaron todas las variables necesarias
    if (isset($_POST['epp']) && is_array($_POST['epp'])) {
        // Recuperar los datos enviados por el formulario
        $epps = $_POST['epp'];
        $fecha = date("Y-m-d");
        $estado = 'buen estado';
        $pestaña = $_POST['pestaña'];
        $header_loc = $_POST['header_loc'];
        // Inicializar un array para registrar los resultados
        $log = [];

        // Insertar cada epp en la base de datos
        foreach ($epps as $index => $epp) {
            if (!empty($epp)) {
                $query_detalle = "INSERT INTO almacen_epp (epp, estado, alta) VALUES (?, ?, ?)";
                $stmt_detalle = $conexion->prepare($query_detalle);

                if ($stmt_detalle) {
                    $stmt_detalle->bind_param("sss", $epp, $estado, $fecha);
                    if ($stmt_detalle->execute()) {
                        $log[] = "Registro $index insertado con éxito: epp='$epp'";
                    } else {
                        $log[] = "Error al insertar registro $index: " . htmlspecialchars($stmt_detalle->error);
                    }
                    $stmt_detalle->close();
                } else {
                    $log[] = "Error al preparar la consulta para registro $index: " . htmlspecialchars($conexion->error);
                }
            }
        }

        // Mostrar el log
        echo "<pre>";
        foreach ($log as $entry) {
            echo htmlspecialchars($entry) . "\n";
        }
        echo "</pre>";

        header("Location: " . $_SERVER['HTTP_REFERER'] );
        exit; // Ensure no further code is executed after redirection
        
    } else {
        echo "Error: Todos los campos son obligatorios.";
    }
} else {
    echo "Error: Método de solicitud incorrecto.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
