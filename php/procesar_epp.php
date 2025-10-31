<?php
// Incluye la conexión a la base de datos
include '../conexion.php';
 // Crear una conexión a la base de datos
 $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

 // Verificar la conexión
 if ($conexion->connect_error) {
     die("Error de conexión: " . $conexion->connect_error);
 }


// Verifica si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener el ID de la epp
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $header_loc= isset($_GET['header_loc']) ? $_GET['header_loc'] : null;

    // Verificar si el ID es válido
    if ($id) {
        // Obtener los datos enviados por el formulario
        $trabajador = !empty($_POST['trabajador']) ? $_POST['trabajador'] : null;
        $entrega = !empty($_POST['entrega']) ? $_POST['entrega'] : null;
        $estado = !empty($_POST['estado']) ? $_POST['estado'] : null;

        // Verificar si se conectó correctamente a la base de datos
        $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Preparar la consulta para actualizar los datos de la epp
        $sql = "UPDATE almacen_epp SET trabajador = ?, entrega = ?, estado = ? WHERE folio = ?";

        // Preparar la declaración SQL
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }

        // Vincular los parámetros
        $stmt->bind_param("sssi", $trabajador, $entrega, $estado, $id);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['HTTP_REFERER'] );
            exit();
        } else {
            echo "Error al actualizar la epp: " . $stmt->error;
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
        $conexion->close();
    } else {
        echo "ID de epp no válido.";
    }
} else {
    echo "No se ha enviado el formulario.";
}
?>
