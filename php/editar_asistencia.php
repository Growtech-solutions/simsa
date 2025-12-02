<?php
// php/editar_asistencia.php
 include '../conexion_transimex.php'; 
 $conexion = new mysqli($host_transimex, $usuario_transimex, $contrasena_transimex, $base_de_datos_transimex);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validar datos recibidos
    if (isset($_POST['id'], $_POST['fecha'], $_POST['hora'], $_POST['penalizacion'])) {
        
        $id = (int) $_POST['id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        $penalizacion = (int) $_POST['penalizacion'];

        // Combinar fecha + hora en formato datetime
        $fecha_hora = $fecha . ' ' . $hora . ':00';

        // Preparar y ejecutar actualización
        $sql = "UPDATE asistencia 
                SET fecha = ?, penalizacion = ?
                WHERE id = ?";
        
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("sii", $fecha_hora, $penalizacion, $id);

            if ($stmt->execute()) {
                // Redirigir de vuelta al historial
                header("Location: " . $_SERVER['HTTP_REFERER'] . "");
                exit;
            } else {
                die("Error al actualizar: " . $stmt->error);
            }

            $stmt->close();
        } else {
            die("Error en la preparación: " . $conexion->error);
        }
    } else {
        die("Faltan datos en el formulario.");
    }
} else {
    die("Método no permitido.");
}

$conexion->close();
