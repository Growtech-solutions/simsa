<!DOCTYPE html>
<html lang="en">
<head>
   <title>Solicitud de pieza</title>
    <style>
        .escrito{
            display: block;
            text-align: center;
            margin-top: 10px;
            border:none;
            font-weight: bold;
        }
    </style>
</head>
<body id="solicitudpieza">
<div>
    <div>
    <h2 class="titulo">Solicitud de pieza</h2>
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
    <form class="servicios__form_celular" action="../php/procesar_solicitud_pieza.php" method="POST">
        <!-- Common fields -->
        
        <input class="escrito altadepieza__campo" type="text" id="ot" name="ot_texto" placeholder="OT" required oninput="obtenerNombreProyecto()">
        <input class="escrito " type="text" name="nombreDelProyecto" id="nombreDelProyecto" placeholder="Nombre del proyecto" readonly>
        <input type="hidden" name="area" value="Paileria">
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        
        <!-- Input fields for description, quantity, and comment -->
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <input type="number" class="entrada altadepieza__campo__cantidad" name="cantidad[]" placeholder="Cantidad" >
            <input type="text" class="entrada altadepieza__campo__descripcion" name="descripcion[]" placeholder="Descripción" ></input>
        <?php endfor; ?>
        
        <!-- Submit button -->
        <div class="boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>
</div>
</body>
</html>

