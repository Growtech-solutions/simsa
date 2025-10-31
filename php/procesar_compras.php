<?php

include '../conexion.php'; 
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se enviaron datos mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se enviaron todas las variables necesarias
    if (isset($_POST['descripcion'], $_POST['cantidad'], $_POST['unidad'], $_POST['ot'], $_POST['responsable'], $_POST['comentario'])) {
        // Recuperar los datos enviados por el formulario
        $descripciones = $_POST['descripcion'];
        $header_loc= $_POST['header_loc'];
        $cantidades = $_POST['cantidad']; 
        $unidades = $_POST['unidad'];
        $comentarios = $_POST['comentario'];
        $fecha_actual = date("Y-m-d");

        // Recuperar OT y responsable
        $ot = $_POST['ot'];
        $responsable = $_POST['responsable'];

        // Verificar si hay al menos una cantidad para procesar
        $validEntries = array_filter($cantidades, function($cantidad) {
            return $cantidad > 0;
        });

        if (!empty($validEntries)) {
            // Inicializar un array para registrar los resultados
            $log = [];

            // Insertar cada detalle de compra en la base de datos
            for ($i = 0; $i < count($descripciones); $i++) {
                if ($cantidades[$i] > 0) {
                    $query_detalle = "INSERT INTO compras (ot, descripcion, cantidad, unidad, responsable, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_detalle = $conexion->prepare($query_detalle);

                    if ($stmt_detalle) {
                        $stmt_detalle->bind_param("ssdsss", $ot, $descripciones[$i], $cantidades[$i], $unidades[$i], $responsable, $comentarios[$i]);
                        if ($stmt_detalle->execute()) {
                            $log[] = "Registro $i insertado con éxito: OT='$ot', Descripción='$descripciones[$i]', Cantidad='$cantidades[$i]', Unidad='$unidades[$i]', Responsable='$responsable', Fecha='$fecha_actual', Observaciones='$comentarios[$i]'";
                            $confirmacion = "Registro insertado con éxito.";
                        } else {
                            $log[] = "Error al insertar registro $i: " . htmlspecialchars($stmt_detalle->error);
                        }
                        $stmt_detalle->close();
                    } else {
                        $log[] = "Error al preparar la consulta para registro $i: " . htmlspecialchars($conexion->error);
                    }
                }
            }

            // Mostrar el log
            echo "<pre>";
            foreach ($log as $entry) {
                echo htmlspecialchars($entry) . "\n";
            }
            echo "</pre>";

            // Redirigir a la página de compras después de mostrar el log (esto puede ser opcional)
            header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
            exit; // Ensure no further code is executed after redirection
        } else {
            $confirmacion = "No se han especificado cantidades válidas para procesar.";
        }
    } else {
        $confirmacion = "Error: Todos los campos son obligatorios.";
    }
} else {
    $confirmacion = "Error: Método de solicitud incorrecto.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>



