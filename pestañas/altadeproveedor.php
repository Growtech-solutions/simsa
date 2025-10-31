<!DOCTYPE html>
<html lang="en">
<body id="altadeproveedor">
        <div class="contenedor__servicios" >
            <h2 class="titulo">Alta de proveedor</h2>
            <form class="servicios__form" action="../php/procesar_alta_proveedor.php" method="POST">
                <input class="entrada altadeproyecto__campo" type="text" id="proveedor" name="proveedor" placeholder="Nombre" required>
                <input class="entrada altadeproyecto__campo" type="text" id="direccion" name="direccion" placeholder="Direccion">
                <input class="entrada altadeproyecto__campo" type="text" id="telefono" name="telefono" placeholder="telefono">
                <input class="entrada altadeproyecto__campo" type="text" id="correo" name="correo" placeholder="correo">
                <input class="entrada altadeproyecto__campo" type="number" id="periodo_pago" name="periodo_pago" placeholder="Dias credito">
                <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                <div class="altadeproyecto__boton__enviar">
                    <input class="boton__enviar" type="submit" value="Enviar">
                </div>
            </form>
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
    </main>
</body>
</html>