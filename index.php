<?php
// Establecer la zona horaria a Monterrey
date_default_timezone_set('America/Monterrey');
// Archivo de conexión a la base de datos (db_connection.php)
include 'conexion.php'; 

// Crear conexión
$connection = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Iniciar sesión
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario de manera segura
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = $_POST['password'];

    // Consulta SQL para obtener el usuario
    $sql = "SELECT * FROM usuario WHERE usuario = ?";

    // Preparar la consulta para evitar inyección SQL
    if ($stmt = mysqli_prepare($connection, $sql)) {
        // Enlazar el parámetro de la consulta
        mysqli_stmt_bind_param($stmt, "s", $username);

        // Ejecutar la consulta
        mysqli_stmt_execute($stmt);

        // Obtener el resultado
        $result = mysqli_stmt_get_result($stmt);

        // Verificar si se encontró el usuario
        if (mysqli_num_rows($result) > 0) {
            // Obtener los datos del usuario
            $row = mysqli_fetch_assoc($result);

            // Verificar si la contraseña es correcta usando password_verify
            if (password_verify($password, $row['contraseña'])) {
                // La contraseña es correcta, iniciar sesión
                $_SESSION['username'] = $username;  // Almacena el nombre de usuario en la sesión
                $_SESSION['role'] = $row['rol'];   // Almacena el rol del usuario en la sesión
                
                // Redirigir al usuario según su rol
                switch (strtolower($row['rol'])) { // Aseguramos que el rol sea minúscula
                    case 'recursos_humanos':
                        header("Location: header_main_aside/recursos_humanos.php");
                    exit();
                    case 'manufactura':
                        header("Location: header_main_aside/manufactura.php?pestaña=avance_ot");
                    exit();
                    case 'proyectos':
                        header("Location: header_main_aside/proyectos.php?pestaña=avance_ot");
                    exit();
                    case 'finanzas':
                        header("Location: header_main_aside/finanzas.php");
                    exit();
                    case 'almacen':
                        header("Location: header_main_aside/almacen.php");
                    exit();
                    case 'gerencia':
                        header("Location:  header_main_aside/general.php");
                    exit();
                    default:
                        // Redirigir al home para cualquier otro rol
                        header("Location: home.php");
                        exit();
                }
            } else {
                $errorMessage = "Contraseña incorrecta.";
            }
        } else {
            $errorMessage = "El usuario no existe.";
        }

        // Cerrar la declaración
        mysqli_stmt_close($stmt);
    } else {
        echo "Error en la consulta: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="Normalize.css"> 
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="icon" href="img/icon.png" type="image/png">
</head>
<body>
    <section class="contenedor">
        <nav>

        </nav>
        <nav>
            <img class="logo" src="img/logo.png" alt="">
            <form class="inicio" method="post">
                <input class="entrada acceso" type="text" id="username" name="username" placeholder="Usuario" required><br><br>
                <input class="entrada acceso" type="password" id="password" name="password" placeholder="Contraseña" required>
                <div class="inicio__boton">
                    <input class="inicio__boton__enviar" type="submit" value="Iniciar Sesión">
                </div>
            </form>
            <p class="error-message"><?php if(isset($errorMessage)) { echo $errorMessage; } ?></p>
        </nav>
        <nav>

        </nav>
    </section>
</body>
</html>
