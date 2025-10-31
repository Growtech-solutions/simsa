<!DOCTYPE html>
<html lang="en">

<style>
.servicios__form {
    display: grid;
    grid-template-columns: 33% 33% 33%;
    gap: 1rem;
    font-family: var(--fuente-principal);
}
.servicios__form select {
    width: 100%; /* O ajusta a un valor fijo, como '150px' */
    padding: 5px; /* Ajusta el relleno si es necesario */
    box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
}
/* Estilos para las alertas */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-family: 'Arial', sans-serif;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Icono de éxito */
.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

/* Icono de advertencia */
.alert-warning {
    color: #8a6d3b;
    background-color: #fcf8e3;
    border-color: #faebcc;
}

/* Icono de error */
.alert-error {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

/* Estilo de los iconos */
.alert-icon {
    font-size: 20px;
    display: inline-block;
}

/* Estilos adicionales */
.alert-text {
    flex-grow: 1;
}

.close-button {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #777;
    margin-left: 10px;
}

</style>

<body id="agregar_trabajo_fijo">
    <div class="contenedor__servicios">
        <h2 class="titulo">Agregar trabajo fijo</h2>
        
        <?php 
            if (isset($_GET['exito'])) {
                echo '
                <div class="alert alert-success">
                    <span class="alert-icon">✔️</span>
                    <span class="alert-text">Trabajos fijos agregados con éxito.</span>
                    <button class="close-button" onclick="this.parentElement.style.display=\'none\';">✖️</button>
                </div>';
            }
            
            if (isset($_GET['ocupados'])) {
                $trabajadores_ocupados = htmlspecialchars($_GET['ocupados']);
                echo '
                <div class="alert alert-warning">
                    <span class="alert-icon">⚠️</span>
                    <span class="alert-text">Los siguientes trabajadores ya tienen un trabajo asignado en ese rango de fechas: <strong>' . $trabajadores_ocupados . '</strong>.</span>
                    <button class="close-button" onclick="this.parentElement.style.display=\'none\';">✖️</button>
                </div>';
            }
        ?>

        <form class="servicios__form" action="../php/procesar_trabajo_fijo.php" method="POST">
            <div>
                <label>Desde:</label>
                <input class="entrada" type="date" id="fecha_inicial" name="fecha_inicial" required>
            </div>

            <div>
                <label>Hasta:</label>
                <input class="entrada" type="date" id="fecha_final" name="fecha_final" required>
            </div>

            <div>
                <?php 
                    $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL";
                    $result_piezas = mysqli_query($conexion, $sql_piezas);
                    $piezas = [];
                    $piezas_nombre = [];
                    while ($row = mysqli_fetch_assoc($result_piezas)) {
                        $piezas[] = $row['id'];
                        $piezas_nombre[] = $row['pieza'];
                    }

                    echo "<select class='entrada' name='pieza[]'>";
                    echo "<option value=''>Seleccione pieza</option>";
                    foreach ($piezas as $key => $id_pieza) {
                        echo "<option value='" . htmlspecialchars($id_pieza) . "'>" . htmlspecialchars($piezas_nombre[$key]) . "</option>";
                    }
                    echo "</select>";
                ?>
            </div>

            
                <?php 
                for ($i = 1; $i <= 12; $i++):
                    // Query to obtain workers
                    $sql_trabajadores = "SELECT id, CONCAT(nombre, ' ', apellidos) AS nombre FROM trabajadores where Estado='Activo' ORDER BY nombre";
                    $result_trabajadores = mysqli_query($conexion, $sql_trabajadores);
                    
                    echo "<select class='entrada' id='trabajador' name='trabajador[]'>";
                    echo "<option value=''>Seleccione trabajador</option>";
                    while ($row = mysqli_fetch_assoc($result_trabajadores)) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                    echo "</select>";
                endfor;
                ?>
            <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

            <div >
                <input class="boton__enviar" type="submit" value="Enviar">
            </div>
        </form>
    </div>

    <?php 
        // Close the conexion
        mysqli_close($conexion);
    ?>
</body>
</html>
