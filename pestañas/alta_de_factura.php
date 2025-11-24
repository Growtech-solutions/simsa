<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta Factura</title>
    <style>
        label {
            text-align: left;
        }
    </style>
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
    $alta_sistema = date('Y-m-d');
    
    $sql_pedido = "SELECT id FROM pedido 
    left join ot on pedido.id=ot.id_pedido
    WHERE ot = '$ot'";
    $result_pedido = $conexion->query($sql_pedido);

    if ($result_pedido->num_rows > 0) {
        $row_pedido = $result_pedido->fetch_assoc();
        $id_pedido = $row_pedido['id'];

        if ($moneda == "USD") {
            $valor_pesos = $monto * 22;
        } else {
            $valor_pesos = $monto;
        }

        $sql = "INSERT INTO facturas (folio, id_pedido, valor, moneda, valor_pesos, alta_sistema, responsable, descripcion, observaciones,ot)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conexion->prepare($sql)) {
            $stmt->bind_param("sidsdssssi", $folio, $id_pedido, $monto, $moneda, $valor_pesos, $alta_sistema, $responsable, $descripcion, $observaciones,$ot);
            if ($stmt->execute()) {
                $confirmacion = "Factura registrada correctamente.";
                require '../../../vendor/autoload.php';
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rafael@growtech-solutions.com.mx';
                    $mail->Password = 'hnju vixi pstb zfcx';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
                    $mail->addAddress('rafael@growtech-solutions.com.mx', 'Rafael');

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
                    $mail->Body = $message;
                    $mail->CharSet = 'UTF-8';
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

<div class="principal">
    <div>
    <h2 class="text-2xl font-bold text-blue-600 text-center">Alta de factura</h2>
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
</div>

<script>
    function obtenerNombreProyecto() {
        var ot = document.getElementById("ot").value;

        if (ot) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/obtener_pedidos.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById("nombreDelProyecto").value = response.nombreProyecto;

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
        var valor = input.value;
        valor = valor.replace(/[^0-9.]/g, '');
        var partes = valor.split('.');
        if (partes.length > 2) {
            partes = [partes[0] + '.' + partes.slice(1).join('')];
        }
        partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        input.value = '$' + partes.join('.');
        document.getElementById('montoSinFormato').value = valor.replace(/,/g, '');
    }

    function prepararEnvio() {
        var montoConFormato = document.getElementById('monto').value;
        var montoSinFormato = montoConFormato.replace(/[^0-9.]/g, '');
        document.getElementById('montoSinFormato').value = montoSinFormato;
    }
</script>
</body>
</html>
