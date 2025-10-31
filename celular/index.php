<?php
// Establecer la zona horaria a Monterrey
date_default_timezone_set('America/Monterrey');
// Archivo de conexión a la base de datos (db_connection.php)
include '../conexion.php'; 



// Crear conexiónz
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
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/recursos_humanos.php";
                        </script>
                        <?php
                    exit();
                    case 'manufactura':
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/manufactura.php?pestaña=avance_ot";
                        </script>
                        <?php
                    exit();
                    case 'proyectos':
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/proyectos.php?pestaña=avance_ot";
                        </script>
                        <?php
                    exit();
                    case 'finanzas':
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/finanzas.php";
                        </script>
                        <?php
                    exit();
                    case 'almacen':
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/almacen.php";
                        </script>
                        <?php
                    exit();
                    case 'gerencia':
                        ?>
                        <script>
                            window.location.href = "../header_main_aside_celular/proyectos.php";
                        </script>
                        <?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="Normalize.css"> 
    <link rel="stylesheet" href="../styles_celular.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <link rel="manifest" href="../manifest.json">

    <style>
        /* Pantalla de carga */
        #loader {
            position: fixed;
            width: 100%;
            height: 100vh;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        }

        /* Animación de palpitación */
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 0.7; }
        }

        #loader img {
            width: 250px; /* Tamaño del logo */
            animation: pulse 1.5s infinite ease-in-out; /* Animación */
        }

        /* Ocultar el contenido hasta que cargue */
        #contenido {
            display: none;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.getElementById("loader").style.display = "none";
                document.getElementById("contenido").style.display = "block";
            }, 1000); // Espera 1 segundo para una mejor transición
        });
        self.addEventListener("fetch", function(event) {
    event.respondWith(
        caches.match(event.request).then(function(response) {
            return response || fetch(event.request);
        })
    );
});

    </script>
</head>
<body>
    <div id="loader">
        <img src="../img/logo.png" alt="Cargando..." class="logo-loader">
    </div>
    <div id="contenido">

        <section class="contenedor">
            <nav></nav>
            <nav class="index_nav">
                <img class="logo" src="../img/logo.png" alt="Logo">
                <form class="inicio" method="post">
                    <input class="entrada acceso" type="text" id="username" name="username" placeholder="Usuario" required><br><br>
                    <input class="entrada acceso" type="password" id="password" name="password" placeholder="Contraseña" required>
                    <div class="inicio__boton">
                        <input class="inicio__boton__enviar" type="submit" value="Iniciar Sesión">
                    </div>
                </form>
                <p class="error-message"><?php if(isset($errorMessage)) { echo $errorMessage; } ?></p>
            </nav>
            <nav></nav>
        </section>

    </div>
    <script>
    let deferredPrompt;

    window.addEventListener('beforeinstallprompt', (e) => {
        // Evita que el navegador muestre el prompt automáticamente
        e.preventDefault();
        deferredPrompt = e;

        // Mostrar tu "alarma" personalizada para invitar a instalar
        const installBanner = document.createElement('div');
        installBanner.style.position = 'fixed';
        installBanner.style.bottom = '0';
        installBanner.style.width = '100%';
        installBanner.style.backgroundColor = '#ffc107';
        installBanner.style.color = '#000';
        installBanner.style.textAlign = 'center';
        installBanner.style.padding = '15px';
        installBanner.innerHTML = `
            ¿Quieres instalar esta app en tu dispositivo? 
            <button id="btnInstalar" style="margin-left: 10px; padding: 5px 10px;">Instalar</button>
        `;
        document.body.appendChild(installBanner);

        // Manejar clic en el botón de instalar
        document.getElementById('btnInstalar').addEventListener('click', () => {
            installBanner.remove();
            deferredPrompt.prompt();

            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('El usuario aceptó instalar la PWA');
                } else {
                    console.log('El usuario rechazó instalar la PWA');
                }
                deferredPrompt = null;
            });
        });
    });
</script>

</body>
</html>
