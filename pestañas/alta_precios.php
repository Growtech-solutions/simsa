<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta precios</title>
</head>
<body>
<style>
    .requisicion_form {
        gap: 1rem;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
    }
    .altadepieza__campo:nth-child(2){
        grid-column: 2 / 4;
    }
    .enviar_requisicion {
        grid-column: 2 / 4;
    }


    .escrito{
            display: block;
            text-align: center;
            margin-top: 10px;
            border:none;
            font-weight: bold;
        }
</style>
<div class=" principal">
    <section>
    <h2 class="titulo">Alta de precios</h2>
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


    <form class="requisicion_form" action="../php/procesar_alta_precios.php" method="POST">
        
         <input class="escrito altadepieza__campo" type="text" id="ot" name="ot" placeholder="OT" required oninput="obtenerNombreProyecto()">
        <input class="escrito altadepieza__campo" type="text" name="nombreDelProyecto" id="nombreDelProyecto" placeholder="Nombre del proyecto" readonly>
        
        <?php for ($i = 1; $i <= 15; $i++): ?>

            <input type="text" class="entrada requisicion" name="descripcion[]" placeholder="Descripción">

            <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad[]', 'entrada');  ?>
            
            <input type="number" class="entrada requisicion" name="precio_unitario[]" placeholder="Precio Unitario" step="0.01">
            
        <?php endfor; ?>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
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