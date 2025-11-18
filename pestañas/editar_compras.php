<!DOCTYPE html>
<html lang="en">
<head>
    <title>Editar compras</title>
</head>
<body>
<?php
$header_loc = htmlspecialchars($_GET['header_loc']);
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Establecer la conexión a la base de datos
    $conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los datos de la pieza
    $sql = "SELECT * FROM compras WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        echo "No se encontró la pieza.";
        exit;
    }

    $conexion->close();
} else {
    echo "ID no proporcionado.";
    exit;
}
?>
<style>
.editar_compra:nth-child(1) {
    grid-column: 1/3;
}
.editar_compra:nth-child(6) {
    grid-column: 3/5;
}

.label {
    display: block;
    text-align: right;
    margin-top: 8px;
}
</style>

<div class="principal">
    <div>
    <h1 class="text-2xl font-bold text-blue-600 text-center">Editar Compra</h1>
        <?php
        // Verifica si el parámetro 'confirmacion' está presente en la URL
        if (isset($_GET['confirmacion'])) {
            // Sanear el valor para evitar inyección de archivos
            $confirmacion = htmlspecialchars($_GET['confirmacion']);
            // Mostrar la confirmación de forma adecuada
            echo "<div class='confirmacion'>$confirmacion</div>";
        }
        ?>
    <br>
    <form class="servicios__form" method="POST" action="../php/procesar_editarcompra.php?id=<?php echo $id; ?>">
        
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'responsables', 'responsable', 'entrada editar_compra', $fila['responsable']); ?>
        <label class="label" for="ot">OT:</label>
        <input class="entrada editar_compra" type="number" id="ot" name="ot" placeholder="OT" value="<?php echo $fila['ot']; ?>">
        <input class="entrada editar_compra" type="number" step="0.0001" id="cantidad" name="cantidad" placeholder="Cantidad" value="<?php echo $fila['cantidad']; ?>">
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'unidades', 'unidad', 'entrada editar_compra', $fila['unidad']); ?>
        <textarea class="entrada editar_compra" id="descripcion" name="descripcion" placeholder="Descripcion"><?php echo htmlspecialchars($fila['descripcion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <input class="entrada editar_compra" type="number" step="0.0001" id="precio_unitario" name="precio_unitario" placeholder="Precio Unitario" value="<?php echo $fila['precio_unitario']; ?>">
        <?php $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'moneda', 'moneda', 'entrada editar_compra', $fila['moneda']); ?>
        <select class="entrada editar_compra" id="cotizacion" name="cotizacion">
            <?php 
                if ($fila['cotizacion'] == null) {
                    echo '<option value="null">compra</option>';
                } else {
                    echo '<option value="' . $fila['cotizacion'] . '">' . $fila['cotizacion'] . '</option>';
                }
            ?>
            <option value="+IVA">+IVA</option>
            <option value="NETO">NETO</option>
        </select>
        <textarea class="entrada editar_compra" id="comentarios" name="comentarios" placeholder="Comentarios"><?php echo $fila['comentarios']; ?></textarea>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        <div class="altadeproyecto__boton__enviar">
            <input class="boton__enviar" type="submit" value="Actualizar">
        </div>
    </form>
</div>
            </div>
</body>
</html>




