
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registro actividad</title>
    <style>
        .custom-file {
            grid-column: 3 / 5;
        }
        .resumen {
            grid-column: 1 / 3;
        }
        
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Librería jQuery -->
</head>
<script>
$(document).ready(function(){
    $("#tipo").change(function(){
        var tipoSeleccionado = $(this).val(); // Obtener el valor seleccionado

        $.ajax({
            url: "../pestañas/obtener_actividades.php", // Archivo que procesará la petición
            type: "POST",
            data: { tipo: tipoSeleccionado },
            success: function(data) {
                $("#periodicos").html(data); // Actualiza el select de actividades
            }
        });
    });
});
</script>
<body id="documentoplanta">

<div class="contenedor__servicios">
    <h2 class="titulo">Registro Actividad</h2>
    <?php if (isset($_GET["confirmacion"])) { ?>
        <p class="confirmacion"><?= htmlspecialchars($_GET["confirmacion"]) ?></p>
    <?php } ?>
    <form class="servicios__form" action="../php/procesar_actividad.php" method="POST" enctype="multipart/form-data">
    
    <select id="tipo" name="tipo" class="entrada altadepieza__campo ">
            <option value="">Seleccione un tipo</option>
            <?php
            $sql_tipos = "SELECT DISTINCT tipo FROM actividades";
            $resultado_tipos = $conexion->query($sql_tipos);
            while ($fila = $resultado_tipos->fetch_assoc()) {
                echo "<option value='{$fila["tipo"]}'>{$fila["tipo"]}</option>";
            }
            ?>
    </select>

    <select id="periodicos" name="periodicos" class="entrada altadepieza__campo" required>
        <option value="">Seleccione una actividad</option>
    </select>

            
            <input type="date" name="fecha" id="fecha" class="entrada" value="<?php echo date('Y-m-d'); ?>" required>
        <?php
        $trabajadores = "SELECT * FROM transimex.trabajadores";
        $resultado_trabajadores = $conexion->query($trabajadores);

        for ($i = 1; $i <= 24; $i++): ?>
            <select class="entrada" name="trabajador<?= $i ?>" >
                <option value="">Seleccione trabajador</option>
                <?php while ($fila = $resultado_trabajadores->fetch_assoc()) { ?>
                    <option value="<?= $fila["id"] ?>"><?= $fila["nombre"] . " " . $fila["apellidos"] ?></option>
                <?php } 
                // Reset the result pointer to the beginning
                $resultado_trabajadores->data_seek(0);
                ?>
            </select>
        <?php endfor; ?>
        <textarea name="resumen" class="resumen entrada" placeholder="Resumen"></textarea>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="doc_act" name="doc_act">
            <label class="custom-file-label" for="doc_act">
                <img class="upload" src="../img/upload.png" alt="Upload Icon">Pedido
            </label>
        </div>
        <!-- Submit button -->
        <div class="altadepieza__boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>

</body>
</html>