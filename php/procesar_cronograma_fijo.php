<?php
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : NULL;
    $fecha_inicial = isset($_POST['fecha_inicial']) ? $_POST['fecha_inicial'] : NULL;
    $fecha_final = isset($_POST['fecha_final']) ? $_POST['fecha_final'] : NULL;
    $header_loc = isset($_POST['header_loc']) ? $_POST['header_loc'] : NULL;

    if (isset($_POST['action']) && $_POST['action'] === 'Actualizar') {
        // Actualizar fechas en el cronograma fijo
        if ($fecha_inicial !== NULL && $fecha_final !== NULL && $id !== NULL) {
            $sql_update = "UPDATE cronograma_fijo SET fecha_inicial = ?, fecha_final = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql_update);
            $stmt->bind_param('ssi', $fecha_inicial, $fecha_final, $id);

            if ($stmt->execute()) {
                header('Location: ../header_main_aside/' . $header_loc . '.php?pestaña=cronograma');
                exit;
            } else {
                echo "Error al actualizar las fechas: " . $stmt->error;
            }
        } else {
            echo "Datos incompletos para actualizar las fechas.";
        }
    } else {
        echo "Acción no válida.";
    }
     if (isset($_POST['borrar']) && $_POST['borrar'] === 'Eliminar') {
        // Actualizar fechas en el cronograma fijo
        if ($fecha_inicial !== NULL && $fecha_final !== NULL && $id !== NULL) {
            $sql_delete = "delete from cronograma_fijo WHERE id = ?";
            $stmt = $conexion->prepare($sql_delete);
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                header('Location: ../header_main_aside/' . $header_loc . '.php?pestaña=cronograma');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo "Error al actualizar las fechas: " . $stmt->error;
            }
        } else {
            echo "Datos incompletos para actualizar las fechas.";
        }
    } else {
        echo "Acción no válida.";
    }
    
} else {
    echo "Método de solicitud no válido.";
    exit;
}

$conexion->close();
?>
