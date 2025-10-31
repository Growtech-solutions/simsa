<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php'; 
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar si se enviaron datos mediante el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se enviaron todas las variables necesarias
    if (isset($_POST['descripcion'])) {
        // Recuperar los datos enviados por el formulario
        $ot = $_POST['ot_texto'] ?? null;
        $area = $_POST['area'] ?? null;
        $cantidades = $_POST['cantidad'] ?? null;
        $descripciones = $_POST['descripcion'] ?? null;
        $header_loc = $_POST['header_loc'] ?? null;
        $comentarios = $_POST['comentario'] ?? null;

        // Verificar si hay piezas para procesar
        if (!empty($descripciones)) {
            // Procesar cada pieza
            for ($i = 0; $i < count($cantidades); $i++) {
                // Verificar si al menos uno de los campos de la pieza no está vacío
                if (!empty($cantidades[$i]) || !empty($descripciones[$i]) || !empty($comentarios[$i])) {
                    // Insertar la pieza en la base de datos
                    $query = "INSERT INTO piezas (cantidad, comentarios, ot, area, pieza) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("isiss", $cantidad, $comentario, $ot, $area, $descripciones[$i]);
                    $cantidad = !empty($cantidades[$i]) ? $cantidades[$i] : 0;
                    $comentario = !empty($comentarios[$i]) ? $comentarios[$i] : "";
                    $tiempo = 0; // Este valor se puede cambiar si es necesario
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $confirmacion = "Solicitud de pieza procesada con éxito.";
            header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
            exit();

        } else {
            $confirmacion = "No se han especificado todas las piezas para procesar.";
        }
    } else {
        $confirmacion = "Error: Todos los campos son obligatorios.";
    }
} else {
    $confirmacion = "Error: Método de solicitud incorrecto.";
}

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
