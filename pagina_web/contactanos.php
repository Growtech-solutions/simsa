
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Incluye PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre = $_POST['Nombre'] ?? '';
    $telefono = $_POST['Telefono'] ?? '';
    $ciudad = $_POST['Ciudad'] ?? '';
    $correo = $_POST['Correo'] ?? '';
    $mensaje = $_POST['Mensaje'] ?? '';

    // Validar los datos recibidos
    if (empty($nombre) || empty($telefono) || empty($ciudad) || empty($correo) || empty($mensaje)) {
        echo 'Por favor, completa todos los campos.';
        exit;
    }

    // Configurar PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Google
        $mail->SMTPAuth = true;
        $mail->Username = 'rafael@growtech-solutions.com.mx'; // Alias o dirección principal
        $mail->Password = 'hnju vixi pstb zfcx'; // Clave de aplicación generada
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Remitente y destinatario
        $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Growtech Solutions');
        $mail->addAddress('rafael@growtech-solutions.com.mx', 'Rafael'); // Destinatario (puedes agregar más)

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo mensaje de contacto';
        $mail->Body = "
            <h3>Has recibido un nuevo mensaje de contacto</h3>
            <p><strong>Nombre:</strong> {$nombre}</p>
            <p><strong>Teléfono:</strong> {$telefono}</p>
            <p><strong>Ciudad:</strong> {$ciudad}</p>
            <p><strong>Correo Electrónico:</strong> {$correo}</p>
            <p><strong>Mensaje:</strong> {$mensaje}</p>
        ";

        // Enviar el correo
        $mail->send();
        header('Location: correo_enviado.php'); 
    } catch (Exception $e) {
        echo "Hubo un error al enviar el correo: {$mail->ErrorInfo}";
    }
} else {
    echo 'Método no permitido.';
}
