<!DOCTYPE html>
<html lang="en">

<head>
    <title>Solicitud de compras</title>
    <style>
        .servicios__form_compras {
            width:auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* Create a 4-column grid */
            gap: 1rem; /* Optional: Adds space between grid items */
        }
        
        
        .servicios__form_compras input,
        .servicios__form_compras textarea,
        .servicios__form_compras select {
            width: 100%; /* Ensure inputs and labels take full width of their grid cell */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }

        
        .altadepieza__boton__enviar {
            grid-column: span 4; /* Span submit button across all columns */
            text-align: center;
        }

        .boton__enviar {
            padding: 0.5rem 1rem;
        }
        .escrito{
            display: block;
            text-align: center;
            margin-top: 10px;
            border:none;
            font-weight: bold;
        }
        h2{
            text-align:center;
        }
    
    </style>
</head>
<body>
<div class="principal">
    <h2>Solicitud de compras</h2>
    <div>
    <?php
        // Verifica si el parámetro 'confirmacion' está presente en la URL
        if (isset($_GET['confirmacion'])) {
            // Sanear el valor para evitar inyección de archivos
            $confirmacion = htmlspecialchars($_GET['confirmacion']);
            // Mostrar la confirmación de forma adecuada
            echo "<div class='confirmacion'>$confirmacion</div>";
        }
    ?>
    <form class="servicios__form_compras" action="../php/procesar_compras.php" method="POST">
        <!-- Common fields -->
        <input class="escrito altadepieza__campo" type="text" id="ot" name="ot" placeholder="OT" required oninput="obtenerNombreProyecto()">
        <input class="escrito altadepieza__campo" type="text" name="nombreDelProyecto" id="nombreDelProyecto" placeholder="Nombre del proyecto" readonly>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

        <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable','escrito');?>

        <!-- Input fields for description, quantity, and unit -->
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <input type="text" class="entrada" name="cantidad[]" placeholder="Cantidad">

            <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad[]','entrada');  ?>

            <textarea class="entrada descripcion_compra" name="descripcion[]" placeholder="Descripción"></textarea>
            <!-- Select field for unit -->
            
            <textarea class="entrada" name="comentario[]" placeholder="Comentario"></textarea>
        <?php endfor; ?>
        
        <!-- Submit button -->
        <div class="altadepieza__boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
        </div>
</div>
</body>
</html>
