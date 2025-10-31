<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../conexion.php';

    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }
    $ot = $_POST['ot'];
    $response = array();

    // Obtener el nombre del proyecto y el cliente basado en la OT
    $sql_proyecto = "SELECT descripcion, id_pedido FROM ot WHERE ot = '$ot' LIMIT 1";
    $result_proyecto = $conexion->query($sql_proyecto);
    if ($result_proyecto->num_rows > 0) {
        $row_proyecto = $result_proyecto->fetch_assoc();
        $response['nombreProyecto'] = $row_proyecto['descripcion'];
        $id_pedido = $row_proyecto['id_pedido'];

        // Obtener el cliente y el pedido basado en el id_pedido
        $sql_pedido = "SELECT cliente, descripcion FROM pedido WHERE id = '$id_pedido'";
        $result_pedido = $conexion->query($sql_pedido);
        if ($result_pedido->num_rows > 0) {
            $row_pedido = $result_pedido->fetch_assoc();
            $response['cliente'] = $row_pedido['cliente'];
        } else {
            $response['cliente'] = "Cliente no encontrado";
        }
    } else {
        $response['nombreProyecto'] = "Proyecto no encontrado";
        $response['cliente'] = "Cliente no encontrado";
    }

    // Obtener los pedidos basados en la OT
    $sql_pedidos = "SELECT id, descripcion FROM pedido WHERE id = '$id_pedido'";
    $result_pedidos = $conexion->query($sql_pedidos);
    $pedidos = array();
    if ($result_pedidos->num_rows > 0) {
        while ($row_pedido = $result_pedidos->fetch_assoc()) {
            $pedidos[] = $row_pedido;
        }
    }
    $response['pedidos'] = $pedidos;

    echo json_encode($response);
    $conexion->close();
}
?>