<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);


// Iniciar sesión para manejar mensajes de confirmación
session_start();

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se recibieron datos por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verificar que todas las variables necesarias están definidas
    if (!empty($_POST['fecha']) && !empty($_POST['ot']) && !empty($_POST['trabajador'])) {

        // Obtener los datos del formulario
        $fecha = $_POST['fecha'];
        $ot = trim($_POST['ot']); 
        $trabajadores = $_POST['trabajador']; 
        $actividades = array_map(function($actividad) {
            return empty($actividad) ? null : $actividad;
        }, $_POST['actividad']);
        $tiempos = array_map(function($tiempo) {
            return empty($tiempo) ? 0 : $tiempo;
        }, $_POST['tiempo']);
        $cantidades = array_map(function($cantidad) {
            return empty($cantidad) ? 0 : $cantidad;
        }, $_POST['cantidad']); 
        $pieza = $_POST['pieza'];

        $registros_insertados = 0;
        $errores = [];

        // Recorrer los arrays y procesar cada fila
        for ($i = 0; $i < count($trabajadores); $i++) {
            // Obtener valores y limpiar espacios en blanco
            $trabajador = trim($trabajadores[$i]);
            $actividad = empty(trim($actividades[$i])) ? null : trim($actividades[$i]);
            $tiempo = trim($tiempos[$i]);
            $cantidad = trim($cantidades[$i]);

            // Validar que los campos no estén vacíos
            if (!empty($trabajador) ) {
                
                // Preparar la consulta
                $query = "INSERT INTO encargado (id_trabajador, fecha, id_pieza, actividad, tiempo,cantidad,ot_tardia) 
                          VALUES ( ?, ?, ?, ?, ?,?,?)";
                $stmt = $conexion->prepare($query);

                if ($stmt === false) {
                    $errores[] = "Error al preparar la consulta: " . $conexion->error;
                    continue;
                }
                // Vincular parámetros
                $stmt->bind_param("isissii", $trabajador, $fecha, $pieza, $actividad, $tiempo,$cantidad,$ot);

                // Ejecutar la consulta y verificar el resultado
                if ($stmt->execute()) {
                    $registros_insertados++;
                } else {
                    $errores[] = "Error al insertar el registro para el trabajador $trabajador: " . $stmt->error;
                }

                // Cerrar la consulta
                $stmt->close();
            }
        }

        // Mensaje de confirmación
        if ($registros_insertados > 0) {
            $_SESSION['mensaje'] = "$registros_insertados registros insertados correctamente.";
        } else {
            $_SESSION['mensaje'] = "No se insertaron registros. " . implode(" ", $errores);
        }

        // Redirigir a la página principal
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" .  $_SESSION['mensaje']);
        exit;
    } else {
        $_SESSION['mensaje'] = "Error: Todos los campos son obligatorios.";
    }
} else {
    $_SESSION['mensaje'] = "Error: Método de solicitud incorrecto.";
}

// Cerrar la conexión
$conexion->close();

// Redirigir a la página con mensaje de error
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>






