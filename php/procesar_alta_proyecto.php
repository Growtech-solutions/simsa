<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configurar la conexión a la base de datos
    include '../conexion.php'; 
    session_start();  // Asegúrate de que la sesión esté iniciada

    // Crear una conexión a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $header_loc= $_POST['header_loc'];
    $ot = $_POST['ot'];
    $descripcion = $_POST['descripcion'];
    $responsable = $_POST['responsable'];
    $pedido = $_POST['pedido'];
    $valor = $_POST['valor'];
    $usuario = $_POST['usuarios'];

    // Preparar la consulta SQL para insertar los datos en la tabla ot
    $sql = "INSERT INTO ot (descripcion, fecha_alta, responsable,  id_pedido, valor, usuario) 
            VALUES ('$descripcion', NOW(), '$responsable','$pedido', '$valor', '$usuario')";  // Utilizamos NOW() para la fecha

// Obtener información del pedido
$ifo_pedido = "SELECT * FROM pedido WHERE id = $pedido";
$resultado = $conexion->query($ifo_pedido);

if ($resultado && $resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
    $cliente = $fila['cliente']; // Asumiendo que la columna se llama 'cliente'
    $planta = $fila['planta'];   // Asumiendo que la columna se llama 'planta'
} else {
    $cliente = "Desconocido";
    $planta = "Desconocida";
}
        // Ejecutar la consulta SQL
    if ($conexion->query($sql) === TRUE) {
        // Mensaje de confirmación antes de la redirección
        $confirmacion = "Registro insertado correctamente.";

        // Ahora, enviar el correo
        require '../../vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Google
            $mail->SMTPAuth = true;
            $mail->Username = 'rafael@growtech-solutions.com.mx'; // Alias o dirección principal
            $mail->Password = 'qlld nvlm amig hgab'; // Clave de aplicación generada
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Remitente y destinatario
            $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
            $mail->addAddress('rafael@growtech-solutions.com.mx', 'Rafael'); // Destinatario (puedes agregar más)
            $mail->addAddress('nelly@e-simsa.com.mx', 'Nelly');
            $mail->addAddress('raul.garza@e-simsa.com.mx ', 'Raul');
            $mail->addAddress('raulolguin@e-simsa.com.mx ', 'Raul');
            $mail->addAddress('jmzertuche@e-simsa.com.mx ', 'Zertuche');

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Alta OT: ' . $ot . ' | ' . $descripcion . ' | ' . $cliente;
            $message = "
                <html>
                <head>
                  <title>Nuevo Proyecto Registrado</title>
                </head>
                <body>
                  <p><strong>{$_SESSION['username']}</strong> ha registrado un nuevo proyecto:</p>
                  <p><strong>OT:</strong> $ot<br>
                     <strong>Nombre del Proyecto:</strong> $descripcion<br>
                     <strong>Cliente:</strong> $cliente $planta<br>
                     <strong>Usuario:</strong> $usuario<br>
                     <strong>Responsable:</strong> $responsable<br>
                     <strong>Fecha de Registro:</strong> " . date("Y-m-d H:i:s") . "</p>
                </body>
                </html>
            ";
            $mail->isHTML(true); // Establece el formato como HTML
            $mail->Body = $message; // $message contiene el HTML que definimos arriba
            $mail->CharSet = 'UTF-8';
            // Enviar el correo
            $mail->send();
            // Si todo sale bien, confirmación de envío de correo
            $confirmacion = "Registro y correo enviado correctamente. ";

        } catch (Exception $e) {
            // Si hay error al enviar el correo, confirma que el registro se hizo, pero no se envió el correo
            $confirmacion = "Registro insertado correctamente, correo no enviado. ";
        }

        // Redirigir a la página de proyectos con el mensaje de confirmación
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
        exit(); // Terminar la ejecución después de la redirección

    } else {
        // Si ocurre un error en la inserción de la base de datos
        $confirmacion = "Error al insertar el registro: " . $conexion->error;
        header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
        exit(); // Terminar la ejecución después de la redirección
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // Si no se han enviado datos por POST, redirigir con un error
    $confirmacion = "Error en registro y correo.";
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
    exit();
}
?>


