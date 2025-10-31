<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta Factura</title>
</head>
<body id="alta_de_factura">
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $folio = $_POST['folio'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $moneda = $_POST['moneda'];
    $pedido = $_POST['pedido'];
    $ot = $_POST['ot'];
    $responsable = $_POST['responsable'];
    $observaciones = $_POST['observaciones'];
    $alta_sistema = date('Y-m-d'); // Establecer la fecha actual como alta_sistema
    
    // Obtener el id del pedido relacionado con la OT y la descripción del pedido
    $sql_pedido = "SELECT id FROM pedido 
    left join ot on pedido.id=ot.id_pedido
    WHERE ot = '$ot'";
    $result_pedido = $conexion->query($sql_pedido);

    if ($result_pedido->num_rows > 0) {
        // Obtener el id del pedido
        $row_pedido = $result_pedido->fetch_assoc();
        $id_pedido = $row_pedido['id'];

        // Calcular el valor en pesos (supone que se necesita un tipo de cambio si es en USD)
        if ($moneda == "USD") {
            $valor_pesos = $monto * 22; // Ejemplo de tipo de cambio USD a MXN
        } else {
            $valor_pesos = $monto; // Si la moneda es MXN, no se necesita conversión
        }

        // Preparar la consulta SQL para insertar los datos en la tabla facturas
        $sql = "INSERT INTO facturas (folio, id_pedido, valor, moneda, valor_pesos, alta_sistema, responsable, descripcion, observaciones,ot)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar y enlazar la declaración
        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("sidsdssssi", $folio, $id_pedido, $monto, $moneda, $valor_pesos, $alta_sistema, $responsable, $descripcion, $observaciones,$ot);
            // Ejecutar la declaración
            if ($stmt->execute()) {
                // Mensaje de confirmación antes de la redirección
                $confirmacion = "Factura registrada correctamente.";
                // Ahora, enviar el correo
                require '../../../vendor/autoload.php';
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
                    $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
                    $mail->addAddress('rafael@growtech-solutions.com.mx', 'Rafael'); // Destinatario (puedes agregar más)

                    // Contenido del correo
                $mail->isHTML(true);
                $mail->Subject = 'Nueva Factura Generada - Folio: '.$folio;
                $message = "
                    <html>
                        <head>
                        <title>Se ha generado una nueva factura.</title>
                        </head>
                        <body>
                        <p><strong>{$_SESSION['username']}</strong> ha registrado una nueva factura:</p>
                        <p><strong>Folio:</strong> $folio</p>
                            <p><strong>Descripción:</strong> $descripcion</p>
                            <p><strong>Monto:</strong> $" . number_format($monto, 2) . " $moneda</p>
                            <p><strong>Valor en Pesos:</strong> $" . number_format($valor_pesos, 2) . " MXN</p>
                            <p><strong>OT:</strong> $ot</p>
                            <p><strong>Pedido:</strong> $pedido</p>
                            <p><strong>Responsable:</strong> $responsable</p>
                            <p><strong>Observaciones:</strong> $observaciones</p>
                            <p><strong>Alta en Sistema:</strong> $alta_sistema</p>
                        </body>
                    </html>
                ";
                $mail->isHTML(true); // Establece el formato como HTML
                $mail->Body = $message; // $message contiene el HTML que definimos arriba
                $mail->CharSet = 'UTF-8';
                // Enviar el correo
                $mail->send();
                } catch (Exception $e) {
                    echo "Error al enviar el correo: {$mail->ErrorInfo}";
                }

                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conexion->error;
            }

        }
    }
}
?>

<style>
    label {
        text-align: left;
    }
</style>

<body id="alta_de_factura">
    <div class="contenedor__servicios">
        <h2 class="titulo">Alta de factura</h2>
        <br>
        <form class="servicios__form" action="" method="POST" onsubmit="prepararEnvio()">
            <label>OT:</label>
            <input class="entrada" type="text" id="ot" name="ot" required oninput="obtenerNombreProyecto()">
            <label>Proyecto:</label>
            <input class="entrada altadepieza__campo" type="text" name="nombreDelProyecto" id="nombreDelProyecto" readonly>
            <label>Pedido:</label>
            <select class="entrada" id="pedido" name="pedido" required title="pedido"></select>
            <label>Folio:</label>
            <input class="entrada editar_factura" type="text" id="folio" name="folio" required>
            <label>Descripción:</label>
            <textarea class="entrada" id="descripcion" name="descripcion" required></textarea>
            <label>Monto:</label>
            <input class="entrada" type="text" id="monto" required oninput="formatoMoneda(this)">
            <input type="hidden" id="montoSinFormato" name="monto" required>
            <label>Moneda:</label>
            <?php $selectDatos->obtenerOpciones('listas', 'moneda', 'moneda', 'entrada editar_factura'); ?>
            <label>Responsable:</label>
            <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable', 'entrada editar_factura'); ?>
            <label>Observaciones:</label>
            <textarea class="entrada editar_factura" id="observaciones" name="observaciones"></textarea>
            <div class="altadeproyecto__boton__enviar">
                <input class="boton__enviar" type="submit" value="Enviar">
            </div>
        </form>
    </div>

    <script>
        function obtenerNombreProyecto() {
            var ot = document.getElementById("ot").value;

            if (ot) {
                // Llamada AJAX para obtener el nombre del proyecto, cliente y los pedidos
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../php/obtener_pedidos.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        document.getElementById("nombreDelProyecto").value = response.nombreProyecto;

                        // Actualizar las opciones del select de pedidos
                        var pedidoSelect = document.getElementById("pedido");
                        pedidoSelect.innerHTML = '<option>Seleccione pedido</option>';
                        response.pedidos.forEach(function(pedido) {
                            var option = document.createElement("option");
                            option.value = pedido.descripcion;
                            option.text = pedido.descripcion;
                            pedidoSelect.appendChild(option);
                        });
                    }
                };
                xhr.send("ot=" + ot);
            } else {
                document.getElementById("nombreDelProyecto").value = "";
                var pedidoSelect = document.getElementById("pedido");
                pedidoSelect.innerHTML = '<option>Seleccione pedido</option>';
            }
        }

        function formatoMoneda(input) {
            // Obtener el valor actual del campo
            var valor = input.value;

            // Remover el signo de dólar y cualquier otro carácter que no sea un dígito o un punto decimal
            valor = valor.replace(/[^0-9.]/g, '');

            // Asegurarse de que solo haya un punto decimal
            var partes = valor.split('.');
            if (partes.length > 2) {
                partes = [partes[0] + '.' + partes.slice(1).join('')];
            }

            // Formatear la parte entera con comas
            partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

            // Concatenar la parte entera y la parte decimal, con el signo '$' al inicio
            input.value = '$' + partes.join('.');

            // Actualizar el valor sin formato en el campo oculto
            document.getElementById('montoSinFormato').value = valor.replace(/,/g, '');
        }

        function prepararEnvio() {
            // Remover el formato de moneda antes de enviar el formulario
            var montoConFormato = document.getElementById('monto').value;
            var montoSinFormato = montoConFormato.replace(/[^0-9.]/g, '');
            document.getElementById('montoSinFormato').value = montoSinFormato;
        }
    </script>
</body>
</html>