<!DOCTYPE html>
<html lang="en">
<head>

    <title>Editar Orden de Compras</title>
    <style>
        .label {
            display: block;
            text-align: right;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Obtener los datos de la orden de compra
    $sql = "SELECT * FROM orden_compra WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        echo "No se encontró la orden de compra.";
        exit;
    }

    
} else {
    echo "ID no proporcionado.";
    exit;
}
?>

<div class="contenedor__servicios">
    <h1 class='centrado'>Editar Orden de Compra</h1>
    <div class="escrito">
        <?php
        // Verifica si el parámetro 'confirmacion' está presente en la URL
        if (isset($_GET['confirmacion'])) {
            // Sanear el valor para evitar inyección de archivos
            $confirmacion = htmlspecialchars($_GET['confirmacion']);
            // Mostrar la confirmación de forma adecuada
            echo "<div class='confirmacion'>$confirmacion</div>";
        }
        ?>
    </div>
    <br>
    <form class="servicios__form" method="POST" action="../php/procesar_orden_compra.php?id=<?php echo $id; ?>">
        <label class="label" for="oc">OC:</label>
        <input class="entrada editar_compra" type="text" id="oc" name="oc" placeholder="OC" value="<?php echo $fila['oc']; ?>" >

        <label class="label" for="responsable">Responsable:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'responsables', 'responsable', 'entrada editar_compra', $fila['responsable']); ?>
        
        <label class="label" for="moneda">Moneda:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'moneda', 'moneda', 'entrada editar_compra', $fila['moneda']); ?>

        <label class="label" for="tipo_de_cambio">Tipo de Cambio:</label>
        <input class="entrada editar_compra" type="text" id="tipo_de_cambio" name="tipo_de_cambio" placeholder="Tipo de Cambio" value="<?php echo $fila['tipo_cambio']; ?>" >

        <label class="label" for="fecha_llegada">Fecha de Llegada:</label>
        <input class="entrada editar_compra" type="date" id="fecha_llegada" name="fecha_llegada" value="<?php echo $fila['llegada_estimada']; ?>" >

        <label class="label" for="proveedor">Proveedor:</label>
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('proveedor', 'proveedor', 'proveedor', 'entrada editar_compra', $fila['proveedor']); ?>      
        
        <label class="label" for="proveedor">Pago estimado:</label>
        <input class="entrada editar_compra" type="date" id="pago_estimado" name="pago_estimado" value="<?php echo $fila['pago_estimado']; ?>" >
        
        <label class="label" for="cotizacion">Compra:</label>
        <select class="entrada editar_compra" id="cotizacion" name="cotizacion">
            <option value="<?php echo $fila['cotizacion']; ?>"><?php echo $fila['cotizacion'] ?: 'Seleccione compra'; ?></option>
            <option value="+IVA">+IVA</option>
            <option value="NETO">NETO</option>
        </select>

        <input type="hidden" name="header_loc" value="<?php echo htmlspecialchars($header_loc); ?>">

        <div class="altadeproyecto__boton__enviar">
            <input class="boton__enviar" type="submit" value="Actualizar">
        </div>
    </form>
</div>
</body>
</html>
