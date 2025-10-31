<!DOCTYPE html>
<html lang="en">
<?php

$header_loc = $_GET['header_loc'];
$id = isset($_GET['id']) ? $_GET['id'] : null;
    // Obtener los datos de la OT
    $sql = "SELECT cronograma.*,piezas.pieza, CONCAT(trabajadores.nombre, ' ', trabajadores.apellidos) AS nombre FROM cronograma 
            left join piezas on cronograma.id_pieza = piezas.id 
            left join trabajadores on cronograma.id_trabajador = trabajadores.id
            WHERE  cronograma.id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        echo "No se encontro la OT.";
        exit;
    }
?>
<head>
    <meta charset="UTF-8">
    <title>Editar OT</title>
    <style>
        .boton{
            border: none; 
  color: black; 
  padding: 14px 28px; 
  cursor: pointer; 
  border-radius: 5px; 
  background-color: var(--gris);
        }

    </style>
</head>
<body>
<div class="contenedor__servicios">
    <h1>Editar agenda</h1>
    <form class="servicios__form" method="POST" action="../php/procesar_cronograma.php">
    <!-- Campos de entrada existentes -->
    <label class="label" for="trabajador">Trabajador:</label>
    <input class="entrada editar_ot" type="text" name="trabajador" value="<?php echo isset($fila['nombre']) ? $fila['nombre'] : ''; ?>" readonly />

    <label class="label" for="trabajo">Trabajo:</label>
    <input class="entrada editar_ot" type="text" name="trabajo" value="<?php echo isset($fila['pieza']) ? $fila['pieza'] : ''; ?>" readonly />

    <label class="label" for="fecha_inicial">Fecha inicial:</label>
    <input class="entrada editar_ot" type="text" value="<?php echo isset($fila['fecha_inicial']) ? $fila['fecha_inicial'] : ''; ?>" readonly />

    <label class="label" for="duracion">Duracion:</label>
    <input class="entrada editar_ot" type="number" id="duracion" name="duracion" value="<?php echo isset($fila['duracion']) ? $fila['duracion'] : ''; ?>">

    <label class="label" for="pieza">Pieza:</label>
    <?php
        $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL AND fecha_inicial IS NOT NULL AND area = 'casos_especiales'";
        $result = mysqli_query($conexion, $sql_piezas);

        if ($result) {
            echo "<select class='entrada' name='pieza'>";
            echo "<option value='" . $fila['id_pieza'] . "'>" . $fila['trabajo'] . "</option>";

            while ($pieza = mysqli_fetch_assoc($result)) {
                echo "<option value='" . htmlspecialchars($pieza['id']) . "'>" . htmlspecialchars($pieza['nombre']) . "</option>";
            }
            echo "</select>";
        } else {
            echo "Error al obtener las piezas.";
        }
    ?>
    
    <label class="label" for="horas">Horas:</label>
    <input class="entrada editar_ot" id="horas" name="horas">

    <input class="entrada editar_ot" type="hidden" id="id" name="id" value="<?php echo isset($fila['id']) ? $fila['id'] : ''; ?>">
<div></div>
<div></div>
<input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>" />
        <input class="boton" type="submit" name="action" value="Actualizar duracion" />
        <input class="boton" type="submit" name="action" value="Registrar horas" />

</form>

</div>
</body>
</html>



