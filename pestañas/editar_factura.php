<!DOCTYPE html>
<html lang="en">
<head>
    <title>Editar Factura</title>
</head>
<body>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    $sql_clientes = "SELECT razon_social FROM cliente";
    $resultado_clientes = $conexion->query($sql_clientes);

    $sql_facturas= "SELECT 
                        facturas.*,
                        pedido.descripcion as 'pedido_descripcion',
                        pedido.cliente as 'ot_cliente'
                    FROM
                        facturas 
                    left join 
                        pedido on pedido.id=id_pedido 
                    WHERE 
                        facturas.id = $id";
    $resultado = $conexion->query($sql_facturas);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        echo "No se encontró la factura con id= $id.";
        exit;
    }
} else {
    echo "ID no proporcionado.";
    exit;
}
?>

<style>
.editar_factura:nth-child(1) {
    grid-column: 1/3;
}

.label {
    display: block;
    text-align: right;
    margin-top: 8px;
}

.custom-file {
    grid-column: 2/4;
}
</style>

<div class="contenedor__servicios">
    <h1>Editar Factura</h1>
    <?php
            // Verifica si el parámetro 'confirmacion' está presente en la URL
            if (isset($_GET['confirmacion'])) {
                // Sanear el valor para evitar inyección de archivos
                $confirmacion = htmlspecialchars($_GET['confirmacion']);
                // Mostrar la confirmación de forma adecuada
                echo "<div class='confirmacion'>$confirmacion</div>";
            }
            ?>
    <form class="servicios__form" method="POST" enctype="multipart/form-data" action="../php/procesar_factura.php?id=<?php echo $id; ?>">
        <label class="label" for="folio">Folio:</label>
        <input class="entrada editar_factura" type="text" id="folio" name="folio" value="<?php echo $fila['folio']; ?>">
        
        <label class="label" for="pedido">Pedido:</label>
        <input class="entrada editar_factura" type="text" id="pedido" name="pedido" value="<?php echo $fila['pedido_descripcion']; ?>">
        
        <label class="label" for="ot">OT:</label>
        <input class="entrada editar_factura" type="text" id="ot" name="ot" value="<?php echo $fila['ot']; ?>">
        
        <label class="label" for="cliente">Cliente:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('cliente', 'razon_social', 'cliente', 'entrada editar_factura', $fila['ot_cliente']); ?>
        
        <label class="label" for="valor">Valor:</label>
        <input class="entrada editar_factura" type="number" step="0.01" id="valor" name="valor" value="<?php echo $fila['valor']; ?>">

        <label class="label" for="moneda">Moneda:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'moneda', 'moneda', 'entrada editar_factura', $fila['moneda']); ?>

        <label class="label" for="tipo_de_cambio">Tipo de Cambio:</label>
        <input class="entrada editar_factura" type="number" step="0.01" id="tipo_de_cambio" name="tipo_de_cambio" value="<?php echo $fila['tipo_de_cambio']; ?>">

        <label class="label" for="alta_sistema">Alta Sistema:</label>
        <input class="entrada editar_factura" type="date" id="alta_sistema" name="alta_sistema" value="<?php echo $fila['alta_sistema']; ?>">

        <label class="label" for="fecha_pago">Fecha Pago:</label>
        <input class="entrada editar_factura" type="date" id="fecha_pago" name="fecha_pago" value="<?php echo $fila['fecha_pago']; ?>">

        <label class="label" for="responsable">Responsable:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'responsables', 'responsable', 'entrada editar_factura', $fila['responsable']); ?>
        
        <label class="label" for="portal">Portal:</label>
        <input class="entrada editar_factura" type="date" id="portal" name="portal" value="<?php echo $fila['portal']; ?>">
        
        <label class="label" for="id_pedido">ID Pedido:</label>
        <input class="entrada editar_factura" type="number" id="id_pedido" name="id_pedido" value="<?php echo $fila['id_pedido']; ?>">
        
        <label class="label" for="descripcion">Descripción:</label>
        <textarea class="entrada editar_factura" id="descripcion" name="descripcion"><?php echo $fila['descripcion']; ?></textarea>

        <label class="label" for="observaciones">Observaciones:</label>
        <textarea class="entrada editar_factura" id="observaciones" name="observaciones"><?php echo $fila['observaciones']; ?></textarea>

        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

        <div class="custom-file">
            <input type="file" class="custom-file-input" id="zip" name="zip">
            <label class="custom-file-label" for="zip">
                <img class="upload" src="../img/upload.png" alt="Upload Icon">Factura zip
            </label>
        </div>

        <div class="altadeproyecto__boton__enviar">
            <input class="boton__enviar" type="submit" value="Actualizar">
        </div>
    </form>
</div>
</body>
</html>
