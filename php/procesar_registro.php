<?php
// Archivo de conexión a la base de datos
include '../conexion.php';

// Crear conexión
$connection = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Comprobar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario de manera segura
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = $_POST['password'];  // La contraseña ingresada por el usuario
    $confirm_password = $_POST['confirm_password'];  // Confirmación de la contraseña
    $role = $_POST['role']; // Rol del usuario
    $header_loc = $_POST['header_loc'];
    
    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $errorMessage = "Las contraseñas no coinciden.";
    } else {
        // Crear un hash seguro para la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Genera el hash

        // Consulta SQL para insertar el nuevo usuario con el hash de la contraseña
        $sql = "INSERT INTO usuario (usuario, contraseña, rol) VALUES (?, ?, ?)";

        // Preparar la consulta para evitar inyección SQL
        if ($stmt = mysqli_prepare($connection, $sql)) {
            // Enlazar los parámetros de la consulta
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $role);

            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                $confirmacion = "Usuario insertado con éxito.";
                header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
                exit();
            } else {
                echo "Error al registrar el usuario: " . mysqli_stmt_error($stmt);
            }

            // Cerrar la declaración
            mysqli_stmt_close($stmt);
        } else {
            echo "Error en la consulta: " . mysqli_error($connection);
        }
    }
}

// Cerrar la conexión
mysqli_close($connection);
?>
