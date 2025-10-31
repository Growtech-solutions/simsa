<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar PHPMailer
require '../../vendor/autoload.php';

// Estilos CSS para la carga de archivos
echo "<style>
table {
    grid-column: 1/5;
}
.custom-file {
  position: relative;
  display: inline-block;
  width: 100%;
  margin-bottom: 10px;
}
.custom-file-input {
  position: relative;
  z-index: 2;
  width: 100%;
  height: 40px;
  margin: 0;
  opacity: 0;
  grid-column: 4/5;
  gap:1rem;
}
.custom-file-label {
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  z-index: 1;
  height: 40px;
  padding: 10px;
  line-height: 20px;
  color: #495057;
  background-color: #ffffff;
  border: 1px solid #ced4da;
  border-radius: 5px;
  cursor: pointer;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.upload {
  width: 20px; /* Ancho de la imagen de carga */
  height: 20px; /* Altura de la imagen de carga */
  margin-right: 5px; /* Espacio entre la imagen y el texto */
  vertical-align: middle; /* Alineación vertical con el texto */
}
.custom-file-input:focus ~ .custom-file-label {
  border-color: #4d90fe;
}
</style>";

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$header_loc = $_POST['header_loc'];

// Manejar la solicitud del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seleccionado'])) {
    $selected_compras = $_POST['seleccionado'];

    // Manejar el envío del formulario para crear la requisición
    if (isset($_POST['crear_requisicion'])) {
        $responsable = $conexion->real_escape_string($_POST['responsable']);
        $proveedor = $conexion->real_escape_string($_POST['proveedor']);
        $fecha_solicitud = date('Y-m-d H:i:s');
        $fecha_llegada = $conexion->real_escape_string($_POST['fecha_llegada']);

        if (!empty($selected_compras)) {
            // Insertar en la tabla oc
            $sql_insert = "INSERT INTO orden_compra (responsable, proveedor, fecha_solicitud, llegada_estimada) VALUES ('$responsable', '$proveedor', '$fecha_solicitud', '$fecha_llegada')";
            if ($conexion->query($sql_insert) === TRUE) {
                // Obtener el nuevo id_oc
                $nueva_id = $conexion->insert_id;

                // Actualizar las entradas seleccionadas de compras2
                foreach ($selected_compras as $compra_id) {
                    $sql_compras2_requisicion = "UPDATE compras SET id_oc=$nueva_id WHERE id=$compra_id";
                    if ($conexion->query($sql_compras2_requisicion) !== TRUE) {
                        echo "Error al actualizar la compra con id $compra_id: " . $conexion->error;
                    }
                }

                // Crear carpeta para los documentos cargados
                $carpeta_trabajador = "../documentos/finanzas/cotizaciones/$nueva_id/";
                if (!file_exists($carpeta_trabajador)) {
                    mkdir($carpeta_trabajador, 0777, true);
                }

                // Manejar la carga de archivos
                $nombres_documentos = array("cotizacion"); // Nombres de los campos de archivos
                $archivo_adjunto = "";
                foreach ($nombres_documentos as $nombre_documento) {
                    if ($_FILES[$nombre_documento]['error'] == UPLOAD_ERR_OK) {
                        $nombre_archivo = $_FILES[$nombre_documento]['name'];
                        $ruta_temporal = $_FILES[$nombre_documento]['tmp_name'];
                        $ruta_destino = $carpeta_trabajador . $nombre_archivo;
                        move_uploaded_file($ruta_temporal, $ruta_destino);
                        $archivo_adjunto = $ruta_destino;
                        echo "El archivo $nombre_archivo se ha subido correctamente.<br>";
                    } else {
                        echo "Error al subir el archivo $nombre_documento.<br>";
                    }
                }

                // Llamar al procedimiento almacenado para actualizar los totales
                //$sql_call_proc = "CALL calculate_and_update_totals($nueva_id)";
                //if ($conexion->query($sql_call_proc) === TRUE) {
                    // Crear correo con PHPMailer
                    $mail = new PHPMailer(true);
                    try {
                        // Configuración del servidor SMTP
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; // Dirección del servidor SMTP
                        $mail->SMTPAuth = true;
                        $mail->Username = 'rafael@growtech-solutions.com.mx'; // Tu correo electrónico
                        $mail->Password = 'hnju vixi pstb zfcx'; // Tu contraseña
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;

                        // Destinatario y asunto
                        $mail->setFrom('notificaciones@growtech-solutions.com.mx', 'Gestor Producción');
                        $mail->addAddress('rafael@growtech-solutions.com.mx');
                        $mail->addAddress($_SESSION['username']);
                        $mail->Subject = "Requisición: $nueva_id";

                        // Contenido del mensaje
                        $tabla_oc = "<h2>Orden de Compra</h2>
                        <table border='1'>
                            <tr>
                                <th>Proveedor</th>
                                <th>Responsable</th>
                                <th>Fecha Solicitud</th>
                                <th>Llegada estimada</th>
                            </tr>
                            <tr>
                                <td>$proveedor</td>
                                <td>$responsable</td>
                                <td>$fecha_solicitud</td>
                                <td>$fecha_llegada</td>
                            </tr>
                        </table>";

                        $tabla_compras2 = "<h2>Detalles de la Compra</h2>
                        <table border='1'>
                            <tr>
                                <th>OT</th>
                                <th>Cantidad</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Precio Unitario</th>
                                <th>Moneda</th>
                            </tr>";

                        $sql_compras = "SELECT * FROM compras WHERE id_oc=$nueva_id";
                        $resultado_compras = $conexion->query($sql_compras);

                        if ($resultado_compras->num_rows > 0) {
                            while ($fila = $resultado_compras->fetch_assoc()) {
                                $tabla_compras2 .= "<tr>
                                    <td>{$fila['ot']}</td>
                                    <td>{$fila['cantidad']}</td>
                                    <td>{$fila['descripcion']}</td>
                                    <td>{$fila['unidad']}</td>
                                    <td>{$fila['precio_unitario']}</td>
                                    <td>{$fila['moneda']}</td>
                                </tr>";
                            }
                        }

                        $tabla_compras2 .= "</table>";

                        $mensaje = "$responsable ha creado una nueva requisición de compra con ID: $nueva_id.<br><br>";
                        $mensaje .= $tabla_oc;
                        $mensaje .= "<br>";
                        $mensaje .= $tabla_compras2;

                        // Agregar el mensaje al correo
                        $mail->isHTML(true);
                        $mail->Body = $mensaje;
                        $mail->CharSet = 'UTF-8';

                        // Adjuntar archivo
                        if ($archivo_adjunto) {
                            $mail->addAttachment($archivo_adjunto, basename($archivo_adjunto));
                        }
    
                        // Enviar correo
                        $mail->send();
                        echo "Correo de notificación enviado correctamente.";

                    } catch (Exception $e) {
                        echo "Error al enviar el correo: {$mail->ErrorInfo}";
                    }

                    header("Location: ../header_main_aside/$header_loc.php?pestaña=compras");
                    exit;
                /*} else {
                    echo "Error al llamar al procedimiento almacenado: " . $conexion->error;
                }*/
            } else {
                echo "Error al insertar los datos en oc: " . $conexion->error;
            }
        } else {
            echo "No se seleccionaron compras para actualizar.";
        }
    }

    // Obtener detalles de las compras seleccionadas
    $compras_ids = implode(",", $selected_compras);
    $sql_compras = "SELECT * FROM compras WHERE id IN ($compras_ids)";
    $resultado = $conexion->query($sql_compras);

} else {
    echo "No se seleccionaron compras.";
    exit;
}
?>

<div class="contenedor__servicios">
    <h2 class="titulo">Crear requisición</h2>
    <form class="servicios__form" action="" method="POST" enctype="multipart/form-data">
        <?php
        foreach ($selected_compras as $compra_id) {
            echo "<input type='hidden' name='seleccionado[]' value='$compra_id'>";
        }
        ?>
        <label class='label'>Llegada estimada:</label>
        <input class="entrada" type="date" name="fecha_llegada" required>

        <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable', 'entrada'); ?>

        <?php $selectDatos->obtenerOpciones('proveedor', 'proveedor', 'proveedor', 'entrada'); ?>
        <label></label>

        <div class="custom-file">
            <input type="file" class="custom-file-input" id="cotizacion" name="cotizacion">
            <label class="custom-file-label" for="cotizacion">
                <img class="upload" src="../img/upload.png">Cotización
            </label>
        </div>
        
        <label></label>
        <div class="altadeproyecto__boton__enviar">
            <input class="boton__enviar" type="submit" name="crear_requisicion" value="Crear Requisición">
        </div>

        <table class='tabla' border='1'>
            <tr>
                <th>Folio</th>
                <th>OT</th>
                <th>Cantidad</th>
                <th>Descripción</th>
                <th>Precio unitario</th>
                <th>Cotización</th>
                <th>Comentarios</th>
                <th>Responsable</th>
            </tr>
            <?php
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $fila['id'] . "</td>";
                    echo "<td>" . $fila['ot'] . "</td>";
                    echo "<td>" . $fila['cantidad'] . " " . $fila['unidad'] . "</td>";
                    echo "<td>" . $fila['descripcion'] . "</td>";
                    echo "<td>" . $fila['precio_unitario'] . "</td>";
                    echo "<td>" . $fila['cotizacion'] . "</td>";
                    echo "<td>" . $fila['comentarios'] . "</td>";
                    echo "<td>" . $fila['responsable'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No se encontraron compras seleccionadas.</td></tr>";
            }
            ?>
        </table>
    </form>
</div>

<?php
$conexion->close();
?>


