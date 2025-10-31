<?php
// Incluir el archivo de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include '../conexion.php';
date_default_timezone_set('America/Monterrey');
session_start();

if (!isset($_SESSION['username'])) {
    die("Error: Usuario no autenticado.");
}

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("La conexión falló: " . $conexion->connect_error);
}


$header_loc= $_POST['header_loc'];
$pedido = $_POST['pedido'];
$cantidad = $_POST['cantidad'];
$unidad = $_POST['unidad'];
$descripcion = $_POST['descripcion'];
$precio_unitario = $_POST['precio_unitario'];
$moneda = $_POST['moneda'];
$cliente = $_POST['cliente'];
$planta = $_POST['planta'];

$mensajeDatos = '';
$mensajeArchivo = '';

// Base directory for file uploads
$base_dir = '../documentos/finanzas/' . $cliente . '/';

// Check and create directory
$ot_dir = $base_dir . $pedido;
if (!is_dir($ot_dir) && !mkdir($ot_dir, 0777, true)) {
    $mensajeArchivo = "Error: No se pudo crear el directorio $ot_dir.";
}

// Process file upload if a file was submitted
if (isset($_FILES['pedido']) && $_FILES['pedido']['error'] == UPLOAD_ERR_OK) {
    $uploaded_file = $_FILES['pedido']['tmp_name'];
    $uploaded_file_name = $_FILES['pedido']['name'];
    $file_extension = pathinfo($uploaded_file_name, PATHINFO_EXTENSION);
    $new_file_name = $pedido . '.' . $file_extension;
    $target_file = $ot_dir . '/' . $new_file_name;

    if (move_uploaded_file($uploaded_file, $target_file)) {
        $mensajeArchivo = "el archivo se ha cargado correctamente.";
    } else {
        $mensajeArchivo = "error al cargar el archivo.";
    }
} else {
    $mensajeArchivo = "No se subió ningún archivo.";
}

// Process the form data
$conexion->begin_transaction();
try {
    $sql_insert_pedido = "INSERT INTO pedido ( descripcion, fecha_alta, cliente, planta) VALUES ( ?, NOW(), ?, ?)";
    $stmt_oc = $conexion->prepare($sql_insert_pedido);
    if ($stmt_oc === false) {
        throw new Exception("Error al preparar la consulta de OC: " . $conexion->error);
    }
    $stmt_oc->bind_param("sss" , $pedido, $cliente, $planta);
    $stmt_oc->execute();

    $nuevoid = $conexion->insert_id;

    $sql_insert_partida = "INSERT INTO partidas (cantidad, descripcion, unidad, id_pedido, precio_unitario, moneda) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_partida = $conexion->prepare($sql_insert_partida);
    if ($stmt_partida === false) {
        throw new Exception("Error al preparar la consulta de partidas: " . $conexion->error);
    }

    foreach ($cantidad as $index => $cantidad_item) {
        $descripcion_item = $descripcion[$index];
        $unidad_item = $unidad[$index];
        $precio_unitario_item = $precio_unitario[$index];
        $moneda_item = $moneda[$index];

        if (!empty($cantidad_item) && !empty($descripcion_item) && !empty($unidad_item) && !empty($precio_unitario_item) && !empty($moneda_item)) {
            $stmt_partida->bind_param("dssids", $cantidad_item, $descripcion_item, $unidad_item, $nuevoid, $precio_unitario_item, $moneda_item);
            $stmt_partida->execute();

            if ($stmt_partida->error) {
                throw new Exception("Error al ejecutar la consulta de partidas: " . $stmt_partida->error);
            }
        }
    }

    $conexion->commit();
    $mensajeDatos = "Los datos se han guardado correctamente.";

    // **PHPMailer** para enviar el correo
    require '../../vendor/autoload.php'; // Si instalaste PHPMailer con Composer

    $mail = new PHPMailer(true); // Instancia de PHPMailer

    try {
        //Configuración del servidor de correo
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'rafael@growtech-solutions.com.mx';  // Tu correo
        $mail->Password = 'hnju vixi pstb zfcx';  // Tu contraseña o token de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Destinatarios
        $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
        $mail->addAddress('rafael@growtech-solutions.com.mx');
        $mail->addAddress('nelly@e-simsa.com.mx', 'Nelly');
        $mail->addAddress('raul.garza@e-simsa.com.mx ', 'Raul');
        // Agrega más direcciones según sea necesario

        // Asunto
        $mail->Subject = "Alta de pedido: $pedido para la OT: $ot";

        // Cuerpo HTML
        $message = "
        <html>
        <head>
          <meta charset='UTF-8'>
          <title>Nuevo Pedido Registrado</title>
        </head>
        <body>
          <p><strong>{$_SESSION['username']}</strong> ha registrado un nuevo pedido:</p>
             <strong>Pedido:</strong> $pedido<br>
             <strong>Fecha de Registro:</strong> " . date("Y-m-d H:i:s") . "</p>
          <table border='1' cellpadding='5' cellspacing='0'>
            <tr>
              <th>Cantidad</th>
              <th>Descripcion</th>
              <th>Unidad</th>
              <th>Precio Unitario</th>
              <th>Moneda</th>
            </tr>";
        
        foreach ($cantidad as $index => $cantidad_item) {
            if (!empty($cantidad_item) && !empty($descripcion[$index]) && !empty($unidad[$index]) && !empty($precio_unitario[$index]) && !empty($moneda[$index])) {
                $message .= "
                <tr>
                  <td>{$cantidad_item}</td>
                  <td>{$descripcion[$index]}</td>
                  <td>{$unidad[$index]}</td>
                  <td>{$precio_unitario[$index]}</td>
                  <td>{$moneda[$index]}</td>
                </tr>";
            }
        }
        
        $message .= "
        </table>
        <p>Para más detalles, por favor, consulte el sistema.</p>
        </body>
        </html>";
        
        $mail->isHTML(true); // Establece el formato como HTML
        $mail->Body = $message; // $message contiene el HTML que definimos arriba
        $mail->CharSet = 'UTF-8';

        $mail->Body    = $message;

        // Adjuntar archivo si existe
        if (isset($target_file) && file_exists($target_file)) {
            $mail->addAttachment($target_file, basename($target_file));
        }

        // Enviar el correo
        $mail->send();
        echo 'Correo enviado exitosamente.';
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
    }

} catch (Exception $e) {
    $conexion->rollback();
    $mensajeDatos = "Error: " . $e->getMessage();
}

$conexion->close();

header("Location: " . $_SERVER['HTTP_REFERER'] . "&mensajeDatos=" . urlencode($mensajeDatos) . "&mensajeArchivo=" . urlencode($mensajeArchivo));
exit();
?>



