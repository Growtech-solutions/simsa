<?php
include '../conexion.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $header_loc = $_POST['header_loc'];
        $folio = !empty($_POST['folio']) ? $_POST['folio'] : null;
        $id_pedido = !empty($_POST['id_pedido']) ? intval($_POST['id_pedido']) : null;
        $pedido = !empty($_POST['pedido']) ? intval($_POST['pedido']) : null;
        $cliente = !empty($_POST['cliente']) ? intval($_POST['cliente']) : null;
        $ot = !empty($_POST['ot']) ? $_POST['ot'] : null;
        $valor = !empty($_POST['valor']) ? floatval($_POST['valor']) : null;
        $moneda = !empty($_POST['moneda']) ? $_POST['moneda'] : null;
        $tipo_de_cambio = !empty($_POST['tipo_de_cambio']) ? floatval($_POST['tipo_de_cambio']) : null;
        $alta_sistema = !empty($_POST['alta_sistema']) ? $_POST['alta_sistema'] : null;
        $fecha_pago = !empty($_POST['fecha_pago']) ? $_POST['fecha_pago'] : null;
        $responsable = !empty($_POST['responsable']) ? $_POST['responsable'] : null;
        $descripcion = !empty($_POST['descripcion']) ? $_POST['descripcion'] : null;
        $observaciones = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;
        $portal = !empty($_POST['portal']) ? $_POST['portal'] : null;
if (isset($ot)) {
    $sql_select = "SELECT pedido.cliente, pedido.id, pedido.descripcion AS id_pedido FROM pedido 
                   LEFT JOIN ot ON ot.id_pedido = pedido.id 
                   WHERE ot.ot = ?";
    $stmt_select = $conexion->prepare($sql_select);
    $stmt_select->bind_param('s', $ot);
    $stmt_select->execute();
    $stmt_select->bind_result($cliente, $id_pedido, $pedido);
    if ($stmt_select->fetch()) {
        // Assign the fetched values to variables
        $_POST['id_pedido'] = $id_pedido;
        $_POST['cliente'] = $cliente;
        $_POST['descripcion'] = $pedido;
    }
    $stmt_select->close();
}
         

        // Actualizar los datos de la factura con prepared statements
        $sql_update = "UPDATE facturas SET 
            folio = ?, id_pedido = ?, valor = ?, moneda = ?, tipo_de_cambio = ?, 
            alta_sistema = ?, fecha_pago = ?, responsable = ?, descripcion = ?, 
            observaciones = ?, portal = ?, ot = ?, id_pedido = ?, cliente = ?  WHERE id = ?";
        
        $stmt = $conexion->prepare($sql_update);
        $stmt->bind_param('sidsdssssssiisi', 
            $folio, $id_pedido, $valor, $moneda, $tipo_de_cambio, 
            $alta_sistema, $fecha_pago, $responsable, $descripcion, 
            $observaciones, $portal, $ot,$id_pedido, $cliente, $id);

        $mensajeArchivo = '';

        // Base directory for file uploads
        $base_dir = '../documentos/finanzas/ot/';
        
        if (!empty($ot)) {
            $ot_dir = $base_dir . $ot;
            
            if (!is_dir($ot_dir) && !mkdir($ot_dir, 0777, true)) {
                $mensajeArchivo = "Error: No se pudo crear el directorio $ot_dir.";
            }
        }

        // Procesar carga de archivo
        if (!empty($_FILES['zip']['name']) && $_FILES['zip']['error'] == UPLOAD_ERR_OK) {
            $uploaded_file = $_FILES['zip']['tmp_name'];
            $uploaded_file_name = $_FILES['zip']['name'];
            $file_extension = pathinfo($uploaded_file_name, PATHINFO_EXTENSION);
            $new_file_name = $folio ? $folio . '.' . $file_extension : "archivo." . $file_extension;
            $target_file = $ot_dir . '/' . $new_file_name;
            if (move_uploaded_file($uploaded_file, $target_file)) {
                $mensajeArchivo = "El archivo se ha cargado correctamente.";
            } else {
                $mensajeArchivo = "Error al cargar el archivo.";
            }
        } else {
            $mensajeArchivo = "No se subió ningún archivo.";
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conexion->close();
            header("Location: " . $_SERVER['HTTP_REFERER'] );
            exit;
        } else {
            echo "Error al actualizar los datos: " . $stmt->error;
        }
    }
} else {
    echo "ID no proporcionado.";
    exit;
}