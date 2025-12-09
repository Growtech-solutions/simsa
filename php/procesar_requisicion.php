<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include '../conexion.php';
session_start();

if (!isset($_SESSION['username'])) {
    die("Error: Usuario no autenticado.");
}

$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("La conexión falló: " . $conexion->connect_error);
}

$fecha_llegada = $_POST['fecha_llegada'];
$responsable = $_POST['responsable'];
$proveedor = $_POST['proveedor'];
$cantidad = $_POST['cantidad'];
$unidad = $_POST['unidad'];
$descripcion = $_POST['descripcion'];
$ot = $_POST['ot'];
$precio_unitario = $_POST['precio_unitario'];
$moneda = $_POST['moneda'];
$cotizacion = $_POST['cotizacion'];
$header_loc= $_POST['header_loc'];
$observaciones = $_POST['observaciones'];

$contizacion_oc = $cotizacion[0];
$moneda_oc = $moneda[0];

$conexion->begin_transaction();

try {
    // Insertar en la tabla `oc`
    $sql_insert_oc = "INSERT INTO orden_compra (proveedor, responsable, fecha_solicitud, llegada_estimada, cotizacion, moneda) VALUES (?, ?, ?, NOW(), ?, ?)";
    $stmt_oc = $conexion->prepare($sql_insert_oc);
    if ($stmt_oc === false) {
        throw new Exception("Error al preparar la consulta de OC: " . $conexion->error);
    }
    $stmt_oc->bind_param("sssss", $proveedor, $responsable, $fecha_llegada, $contizacion_oc, $moneda_oc);
    $stmt_oc->execute();

    $nuevoid = $conexion->insert_id;

    // Insertar en la tabla `compras`
    $sql_insert_compras = "INSERT INTO compras (ot, cantidad, descripcion, unidad, id_oc, precio_unitario, moneda, cotizacion, responsable, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_compras = $conexion->prepare($sql_insert_compras);
    if ($stmt_compras === false) {
        throw new Exception("Error al preparar la consulta de compras: " . $conexion->error);
    }

    foreach ($cantidad as $i => $cantidad_item) {
        $ot_item = $ot[$i];
        $descripcion_item = $descripcion[$i];
        $unidad_item = $unidad[$i];
        $precio_unitario_item = $precio_unitario[$i];
        $moneda_item = $moneda[$i];
        $cotizacion_item = $cotizacion[$i];
        $observaciones_item = $observaciones[$i];

        // Verificar si todos los valores necesarios están presentes
        if (!empty($cantidad_item) && !empty($descripcion_item) && !empty($unidad_item) && !empty($precio_unitario_item) && !empty($moneda_item)) {
            $stmt_compras->bind_param("idssssssss", $ot_item, $cantidad_item, $descripcion_item, $unidad_item, $nuevoid, $precio_unitario_item, $moneda_item, $cotizacion_item, $responsable, $observaciones_item);

            if (!$stmt_compras->execute()) {
                throw new Exception("Error al insertar en compras: " . $stmt_compras->error);
            }
        }
    }

    // Manejo del archivo adjunto
    $carpeta_cotizacion = "../documentos/finanzas/cotizaciones/$nuevoid/";
    if (!file_exists($carpeta_cotizacion)) {
        mkdir($carpeta_cotizacion, 0777, true);
    }

    $nombre_documento = "cotizacion";
    $archivo_adjunto = "";
    if ($_FILES[$nombre_documento]['error'] == UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES[$nombre_documento]['name'];
        $ruta_temporal = $_FILES[$nombre_documento]['tmp_name'];
        $ruta_destino = $carpeta_cotizacion . $nombre_archivo;
        if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
            $archivo_adjunto = $ruta_destino;
            echo "El archivo $nombre_archivo se ha subido correctamente.<br>";

        } else {
            throw new Exception("Error al subir el archivo $nombre_documento.");
        }
    } 

    $conexion->commit();

    // Incluir PHPMailer
    require '../../../vendor/autoload.php'; // Asegúrate de que la ruta al autoload sea correcta

// Instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configuración del servidor de correo SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'rafael@growtech-solutions.com.mx'; // Tu correo de envío
    $mail->Password = 'xyrz wbfe ahhc dgwl'; // Tu contraseña o token de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Destinatarios
    $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
    $mail->addAddress('rafael@growtech-solutions.com.mx');  // Dirección de correo de destino
    $mail->addAddress($_SESSION['username']);  // También enviar al usuario que está realizando la acción

    // Asunto
    $mail->Subject = "Requisición: $nuevoid";

    // Cuerpo HTML del mensaje
    $mensaje = "
    <html>
    <head>
      <meta charset='UTF-8'>
      <title>Requisición de Compra</title>
    </head>
    <body>
      <p><strong>$responsable</strong> ha creado una nueva requisición de compra con ID: $nuevoid.</p>
      <h2>Orden de Compra</h2>
      <table border='1'>
          <tr>
              <th>Proveedor</th>
              <th>Responsable</th>
              <th>Fecha Solicitud</th>
              <th>Fecha Llegada</th>
          </tr>
          <tr>
              <td>$proveedor</td>
              <td>$responsable</td>
              <td>" . date('Y-m-d H:i:s') . "</td>
              <td>$fecha_llegada</td>
          </tr>
      </table><br>";

    $mensaje .= "<h2>Detalles de la Compra</h2>
    <table border='1'>
        <tr>
            <th>OT</th>
            <th>Cantidad</th>
            <th>Descripción</th>
            <th>Unidad</th>
            <th>Precio Unitario</th>
            <th>Moneda</th>
            <th>Cotización</th>
            <th>Comentarios</th>
        </tr>";

    // Iterar sobre los elementos de la compra y añadirlos a la tabla HTML
    foreach ($cantidad as $i => $cantidad_item) {
        $ot_item = $ot[$i];
        $descripcion_item = $descripcion[$i];
        $unidad_item = $unidad[$i];
        $precio_unitario_item = $precio_unitario[$i];
        $moneda_item = $moneda[$i];
        $cotizacion_item = $cotizacion[$i];
        $observaciones_item = $observaciones[$i];

        if (!empty($cantidad_item) && !empty($descripcion_item) && !empty($unidad_item) && !empty($precio_unitario_item) && !empty($moneda_item)) {
            $mensaje .= "<tr>
                <td>$ot_item</td>
                <td>$cantidad_item</td>
                <td>$descripcion_item</td>
                <td>$unidad_item</td>
                <td>$precio_unitario_item</td>
                <td>$moneda_item</td>
                <td>$cotizacion_item</td>
                <td>$observaciones_item</td>
            </tr>";
        }
    }

    $mensaje .= "</table>";

    // Establecer el cuerpo del mensaje
    $mail->isHTML(true); // Configurar el formato como HTML
    $mail->Body    = $mensaje;
    $mail->CharSet = 'UTF-8';

    // Adjuntar archivo si existe
    if (isset($archivo_adjunto) && file_exists($archivo_adjunto)) {
        $mail->addAttachment($archivo_adjunto, basename($archivo_adjunto)); // Adjuntar archivo
    }

    // Enviar correo
    $mail->send();
} catch (Exception $e) {
    $confirmacion = "Error al enviar el correo.";
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
}

    $confirmacion = "Registros insertados con éxito.";
    header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));

} catch (Exception $e) {
    $conexion->rollback();
    echo "Error: " . $e->getMessage();
}

$conexion->close();
?>

