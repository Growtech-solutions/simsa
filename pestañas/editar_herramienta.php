<!DOCTYPE html>
<html lang="en">
<head>
    <title>Editar Herramienta</title>
</head>
<body>
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;
$header_loc = isset($_GET['header_loc']) ? $_GET['header_loc'] : null;
if ($id) {

    // Obtener los datos de la herramienta
    $sql = "SELECT * FROM almacen_herramienta WHERE folio = $id";
    $resultado = $conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
    } else {
        echo "No se encontró la herramienta.";
        exit;
    }

    // Obtener datos para los desplegables
    $sql_trabajadores = "SELECT nombre,apellidos FROM trabajadores WHERE Estado='Activo'";
    $resultado_trabajadores = $conexion->query($sql_trabajadores);

    $sql_areas = "SELECT area_herramienta FROM listas WHERE area_herramienta IS NOT NULL";
    $resultado_areas = $conexion->query($sql_areas);
    
   

} else {
    echo "ID no proporcionado.";
    exit;
}
?>
<style>
.editar_herramienta:nth-child(1) { 
    grid-column: 1/3; 
    text-align:center;
}
.editar_herramienta:nth-child(2) { 
    grid-column: 3/5; 
    text-align:center;
}

.label {
    display: block;
    text-align: right;
    margin-top: 8px;
}
</style>

<div class="contenedor__servicios">
    <h1 style="text-align:center">Editar Herramienta</h1>
    <?php
            // Verifica si el parámetro 'confirmacion' está presente en la URL
            if (isset($_GET['confirmacion'])) {
                // Sanear el valor para evitar inyección de archivos
                $confirmacion = htmlspecialchars($_GET['confirmacion']);
                // Mostrar la confirmación de forma adecuada
                echo "<div class='confirmacion'>$confirmacion</div>";
            }
            ?>
    <form class="servicios__form" method="POST" action="../php/procesar_herramienta.php">
        <p class=" editar_herramienta"><strong><?php echo htmlspecialchars($fila['herramienta']); ?></strong></p>

        <p class=" editar_herramienta"><strong><?php echo htmlspecialchars($fila['alta']); ?></strong></p>

        <label class="label" for="trabajador">Trabajador:</label>
        <select class="entrada editar_herramienta" id="trabajador" name="trabajador">
            <option value="<?php echo htmlspecialchars($fila['trabajador']); ?>"><?php echo htmlspecialchars($fila['trabajador']); ?></option>
            <?php
            if ($resultado_trabajadores->num_rows > 0) {
                echo '<option value="Almacen">Almacen</option>';
                echo '<option value="Daniel Garza">Daniel Garza</option>';
                echo '<option value="José Bernardo">José Bernardo</option>';
                echo '<option value="Mario Vazques">Mario Vazques</option>';
                echo '<option value="Norberto Garcia">Norberto Garcia</option>';
                echo '<option value="Andrés Quevedo">Andrés Quevedo</option>';
                while ($fila_trabajadores = $resultado_trabajadores->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($fila_trabajadores['nombre']) . " " . htmlspecialchars($fila_trabajadores['apellidos']) .'">' . htmlspecialchars($fila_trabajadores['nombre']) . " " . htmlspecialchars($fila_trabajadores['apellidos']) . '</option>';
                }
            } else {
                echo '<option value="">No hay trabajadores disponibles</option>';
            }
            ?>
        </select>

        <label class="label" for="area">Área:</label>
        <select class="entrada editar_herramienta" id="area" name="area">
            <option value="<?php echo htmlspecialchars($fila['area']); ?>"><?php echo htmlspecialchars($fila['area']); ?></option>
            <?php
            if ($resultado_areas->num_rows > 0) {
                while ($fila_areas = $resultado_areas->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($fila_areas['area_herramienta']) . '">' . htmlspecialchars($fila_areas['area_herramienta']) . '</option>';
                }
            } else {
                echo '<option value="">No hay áreas disponibles</option>';
            }
            ?>
        </select>

        <label class="label" for="entrega">Fecha Entrega:</label>
        <input class="entrada editar_herramienta" type="date" id="entrega" name="entrega" value="<?php echo htmlspecialchars($fila['entrega']); ?>">

        <input class="entrada editar_herramienta" type="hidden" id="id" name="id" value="<?php echo $id; ?>">
        
        <label class="label" for="estado">Estado:</label>
        <select class="entrada editar_herramienta" id="estado" name="estado">
            <option value="<?php echo htmlspecialchars($fila['estado']); ?>"><?php echo htmlspecialchars($fila['estado']); ?></option>
            <option value="buen estado">Buen Estado</option>
            <option value="dañado">Dañado</option>
            <option value="basura">Basura</option>
        </select>

        <div class="altadeproyecto__boton__enviar">
            <input class="boton__enviar" type="submit" value="Actualizar">
        </div>
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
    </form>
</div>
</body>
</html>
