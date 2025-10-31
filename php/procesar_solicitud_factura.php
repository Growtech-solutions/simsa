<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si se han enviado datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configurar la conexión a la base de datos
    include '../conexion.php'; 

    // Iniciar la sesión
    session_start();

    // Crear una conexión a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos del formulario
    $ot = $_POST['ot'];
    $descripcion = $_POST['descripcion'];
    $responsable = $_POST['responsable'];
    $pedido = $_POST['pedido'];
    $header_loc= $_POST['header_loc'];
    $alta_sistema = date('Y-m-d');

    // Sacar el id del pedido de la base de datos
    $sql_pedido = "SELECT id_pedido FROM ot WHERE ot = '$ot' LIMIT 1";
    $result_pedido = $conexion->query($sql_pedido);

    if ($result_pedido->num_rows > 0) {
        // Obtener el id del pedido
        $row_pedido = $result_pedido->fetch_assoc();
        $id_pedido = $row_pedido['id_pedido'];

        // Insertar la nueva factura en la base de datos
        $sql = "INSERT INTO facturas (id_pedido, descripcion, responsable,alta_sistema,ot) 
                VALUES ($id_pedido, '$descripcion', '$responsable','$alta_sistema','$ot')";

        // Ejecutar la consulta SQL
        if ($conexion->query($sql) === TRUE) {
            require '../../../vendor/autoload.php'; // Asegúrate de que la ruta al autoload sea correcta
        
            // Instancia de PHPMailer
            $mail = new PHPMailer(true);
        
            try {
                // Configuración del servidor de correo SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'rafael@growtech-solutions.com.mx'; // Tu correo de envío
                $mail->Password = 'hnju vixi pstb zfcx'; // Tu contraseña o token de aplicación
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;
        
                // Destinatarios
                $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
                $mail->addAddress('rafael@growtech-solutions.com.mx');  // Dirección de correo de destino
                $mail->addAddress('nelly@e-simsa.com.mx');
                $mail->addAddress($_SESSION['username']);
        
                $mail->Subject = "Nueva Solicitud de Factura para la ot: $ot";
        
                $mensaje = "Se ha registrado una nueva solicitud de factura con los siguientes detalles:<br><br>" .
                           "OT: $ot<br>" .
                           "Descripción: $descripcion<br>" .
                           "Responsable: $responsable<br>" .
                           "Pedido: $pedido<br>";
        
                $mail->isHTML(true); // Configurar el formato como HTML
                $mail->Body    = $mensaje;
                $mail->CharSet = 'UTF-8';
        
                // Enviar el correo
                if ($mail->send()) {
                    $confirmacion = "Registro insertado correctamente.";
                } else {
                    $confirmacion = "Error al enviar el correo.";
                }
        
                // Redirige a gerencia.php después de enviar el correo
                header("Location: ../header_main_aside/$header_loc.php?pestaña=solicitud_factura&confirmacion=" . urlencode($confirmacion));
                exit;
        
            } catch (Exception $e) {
                $confirmacion = "Error al insertar el registro: vuelva a revisar los datos" . $conexion->error;
            }
        } else {
            $confirmacion = "Error al insertar el registro: vuelva a revisar los datos" . $conexion->error;
        }
        
    } else {
        $confirmacion = "Pedido no encontrado.";
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {

    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
    exit();
}

?>