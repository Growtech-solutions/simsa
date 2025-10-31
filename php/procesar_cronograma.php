<?php
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : NULL;
    $duracion = isset($_POST['duracion']) ? $_POST['duracion'] : NULL;
    $id_pieza = isset($_POST['pieza']) ? intval($_POST['pieza']) : NULL;
    $nombre = isset($_POST['trabajador']) ? $conexion->real_escape_string($_POST['trabajador']) : '';
    $tiempo = isset($_POST['horas']) ? $_POST['horas'] : NULL;
    $fecha_actual = date('Y-m-d');
    $header_loc=$_POST['header_loc'];   

    if (isset($_POST['action']) && $_POST['action'] === 'Actualizar duracion') {
        // Actualizar duraci��n
        if ($duracion !== NULL && $id !== NULL) {
            $sql_update = "UPDATE cronograma SET duracion = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql_update);
            $stmt->bind_param('ii', $duracion, $id);

            if ($stmt->execute()) {
                // Llamar al procedimiento almacenado para actualizar fechas en cascada
                $sql_procedure = "CALL cascade_update(?)";
                $stmt_procedure = $conexion->prepare($sql_procedure);
                $stmt_procedure->bind_param('i', $id);

                if ($stmt_procedure->execute()) {
                    header('Location: ../header_main_aside/' . $header_loc . '.php?pestaña=cronograma');
                    exit;
                } else {
                    echo "Error al ejecutar el procedimiento almacenado: " . $conexion->error;
                }
            } else {
                echo "Error al actualizar la duración: " . $stmt->error;
            }
        } else {
            echo "Datos incompletos para actualizar la duración.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'Registrar horas') {
        // Insertar registro de horas
        if ($tiempo !== NULL && $id_pieza !== NULL) {
            $sql_encargado = "INSERT INTO encargado (id_pieza, nombre, tiempo, fecha) VALUES (?, ?, ?, ?)";
            $stmt_encargado = $conexion->prepare($sql_encargado);
            $stmt_encargado->bind_param('isis', $id_pieza, $nombre, $tiempo, $fecha_actual);

            if ($stmt_encargado->execute()) {
                header('Location: ../header_main_aside/' . $header_loc . '.php?pestaña=cronograma');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo "Error al registrar las horas: " . $stmt_encargado->error;
            }
        } else {
            echo "Datos incompletos para registrar las horas.";
        }
    } else {
        echo "Acci��n no v��lida.";
    }
} else {
    echo "M��todo de solicitud no v��lido.";
    exit;
}

$conexion->close();
?>
