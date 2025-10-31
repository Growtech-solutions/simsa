<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta pedido</title>
</head>
<body id="alta_pedido">
<style>
    .requisicion_form {
        gap: 1rem;
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        width: 90%;
    }
    .altadepieza__campo:nth-child(1){
        grid-column: 1 / 3;
    }
    .altadepieza__campo:nth-child(2){
        grid-column: 3 / 5;
    }
    .altadepieza__campo:nth-child(3){
        grid-column: 5 / 6;
    }
    .enviar_requisicion {
        grid-column: 5 / 6;
    }
    .custom-file{
        grid-column: 2 / 5;
    }

    .escrito{
            display: block;
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
</style>
<div class=" principal">
    <section>
    <h2 class="titulo">Alta de pedido</h2>
    <?php
        // Verifica si los parámetros 'mensajeDatos' y 'mensajeArchivo' están presentes en la URL
        if (isset($_GET['mensajeDatos']) || isset($_GET['mensajeArchivo'])) {
            // Sanear los valores para evitar inyección de archivos
            $mensajeDatos = isset($_GET['mensajeDatos']) ? htmlspecialchars($_GET['mensajeDatos']) : '';
            $mensajeArchivo = isset($_GET['mensajeArchivo']) ? htmlspecialchars($_GET['mensajeArchivo']) : '';
            
            // Concatenar los mensajes
            $mensajeFinal = $mensajeDatos;
            if (!empty($mensajeDatos) && !empty($mensajeArchivo)) {
                $mensajeFinal .= ". y $mensajeArchivo.";
            } elseif (!empty($mensajeArchivo)) {
                $mensajeFinal .= $mensajeArchivo;
            }

            // Mostrar la confirmación en un solo cuadro
            echo "<div class='confirmacion'>$mensajeFinal</div>";
        }
    ?>


    <form class="requisicion_form" action="../php/procesar_pedido.php" method="POST" enctype="multipart/form-data">
        <?php
            $selectDatos->obtenerOpciones('cliente', 'razon_social', 'cliente', 'escrito altadepieza__campo'); 
            $selectDatos->obtenerOpciones('listas', 'planta', 'planta', 'escrito altadepieza__campo');
        ?>
    
        <input class="escrito altadepieza__campo" type="text" name="pedido" id="pedido" placeholder="Pedido">
        <?php for ($i = 1; $i <= 15; $i++): ?>

            <input type="text" class="entrada requisicion" name="descripcion[]" placeholder="Descripción">

            <input type="text" class="entrada requisicion" name="cantidad[]" placeholder="Cantidad">

            <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad[]', 'entrada');  ?>

            <input type="number" class="entrada requisicion" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01">

            <?php $selectDatos->obtenerOpciones('listas', 'moneda', 'moneda[]','entrada');  ?>
            
            
        <?php endfor; ?>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="pedido" name="pedido">
            <label class="custom-file-label" for="pedido">
                <img class="upload" src="../img/upload.png" alt="Upload Icon">Pedido
            </label>
        </div>

        <div class="enviar_requisicion">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>
        </section>

<?php
$conexion->close();
?>

</body>
</html>