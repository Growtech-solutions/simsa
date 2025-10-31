<?php
// Incluir la conexión a la base de datos
include '../conexion.php';

// Verificar que se hayan recibido los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos enviados por el formulario
    $fecha_inicial = $_POST['fecha_inicial'];
    $fecha_final = $_POST['fecha_final'];
    $id_pieza = $_POST['pieza'][0]; // Asumimos que solo se selecciona una pieza
    $id_trabajadores = $_POST['trabajador']; // Obtener todos los trabajadores seleccionados
    $header_loc = $_POST['header_loc']; 

    // Validar que todos los campos obligatorios tengan valores
    if (!empty($fecha_inicial) && !empty($fecha_final) && !empty($id_pieza) && !empty($id_trabajadores)) {
        
        // Crear la conexión a la base de datos
        $conexion = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);
        
        // Verificar la conexión
        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }
        
        // Array para almacenar trabajadores ocupados
        $trabajadores_ocupados = [];

        // Preparar consulta para verificar si el trabajador ya está ocupado en ese rango de fechas
        $check_sql = "SELECT id_trabajador FROM cronograma_fijo WHERE id_trabajador = ? 
                      AND ((fecha_inicial <= ? AND fecha_final >= ?) OR (fecha_inicial <= ? AND fecha_final >= ?))";
        $check_stmt = mysqli_prepare($conexion, $check_sql);

        // Preparar la consulta SQL para insertar los datos
        $insert_sql = "INSERT INTO cronograma_fijo (id_trabajador, id_pieza, fecha_inicial, fecha_final) 
                       VALUES (?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conexion, $insert_sql);
        
        if ($insert_stmt && $check_stmt) {
            // Iterar sobre cada trabajador seleccionado
            foreach ($id_trabajadores as $id_trabajador) {
                // Comprobar que el ID del trabajador no esté vacío
                if (!empty($id_trabajador)) {
                    // Verificar si el trabajador ya está ocupado
                    mysqli_stmt_bind_param($check_stmt, "issss", $id_trabajador, $fecha_final, $fecha_inicial, $fecha_inicial, $fecha_final);
                    mysqli_stmt_execute($check_stmt);
                    mysqli_stmt_store_result($check_stmt);

                    if (mysqli_stmt_num_rows($check_stmt) > 0) {
                        // Si el trabajador ya está ocupado, agregarlo a la lista de ocupados
                        $trabajadores_ocupados[] = $id_trabajador;
                    } else {
                        // Si no está ocupado, insertar el registro
                        mysqli_stmt_bind_param($insert_stmt, "iiss", $id_trabajador, $id_pieza, $fecha_inicial, $fecha_final);
                        if (!mysqli_stmt_execute($insert_stmt)) {
                            echo "Error al insertar el registro para el trabajador ID $id_trabajador: " . mysqli_error($conexion);
                        }
                    }
                }
            }

            // Verificar si hubo trabajadores ocupados
            if (!empty($trabajadores_ocupados)) {
                // Obtener los nombres de los trabajadores ocupados
                $ocupados_ids = implode(',', $trabajadores_ocupados);
                $nombres_sql = "SELECT CONCAT(nombre, ' ', apellidos) AS nombre FROM trabajadores WHERE id IN ($ocupados_ids)";
                $result = mysqli_query($conexion, $nombres_sql);

                if ($result) {
                    $nombres_ocupados = [];
                    while ($row = mysqli_fetch_assoc($result)) {
                        $nombres_ocupados[] = $row['nombre'];
                    }
                    $nombres_ocupados_str = implode(', ', $nombres_ocupados);
                    // Redirigir con nombres de trabajadores ocupados en la URL
                    header("Location: " . $_SERVER['HTTP_REFERER'] . "&ocupados=" . urlencode($nombres_ocupados_str));
                    exit();
                }
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER'] . "&exito=1");
                exit();
            }

            // Cerrar las sentencias
            mysqli_stmt_close($insert_stmt);
            mysqli_stmt_close($check_stmt);
        } else {
            echo "Error al preparar las consultas: " . mysqli_error($conexion);
        }
        
        // Cerrar la conexión
        mysqli_close($conexion);
    } else {
        echo "Por favor, complete todos los campos requeridos.";
    }
}
?>
