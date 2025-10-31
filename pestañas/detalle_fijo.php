<!DOCTYPE html>
<html lang="en">
<?php

$id = isset($_GET['id']) ? $_GET['id'] : null;
$header_loc = isset($_GET['header_loc']) ? $_GET['header_loc'] : null;

// Obtener los datos del cronograma fijo
$sql = "SELECT cf.*, CONCAT(t.nombre, ' ', t.apellidos) AS trabajador, p.pieza AS pieza
        FROM cronograma_fijo cf
        JOIN trabajadores t ON cf.id_trabajador = t.id
        JOIN piezas p ON cf.id_pieza = p.id
        WHERE cf.id = $id";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $fila = $resultado->fetch_assoc();
} else {
    echo "No se encontró el registro.";
    exit;
}
?>
<head>
    <title>Editar Trabajo Fijo</title>
    <style>
        .boton {
            border: none;
            color: white;
            padding: 14px 28px;
            cursor: pointer;
            border-radius: 5px;
            background-color: #2a8bf5;
        }

    </style>
</head>
<body>
<div class="contenedor__servicios">
    <h1>Editar Trabajo Fijo</h1>
    <form class="servicios__form" method="POST" action="../php/procesar_cronograma_fijo.php">
    
    <!-- Mostrar el nombre del trabajador -->
    <label class="label" for="trabajador">Trabajador:</label>
    <input class="entrada" type="text" name="trabajador" value="<?php echo isset($fila['trabajador']) ? $fila['trabajador'] : ''; ?>" readonly />

    <!-- Mostrar el nombre de la pieza -->
    <label class="label" for="pieza">Pieza:</label>
    <input class="entrada" type="text" name="pieza" value="<?php echo isset($fila['pieza']) ? $fila['pieza'] : ''; ?>" readonly />

    <!-- Editar las fechas -->
    <label class="label" for="fecha_inicial">Fecha Inicial:</label>
    <input class="entrada " type="date" id="fecha_inicial" name="fecha_inicial" value="<?php echo isset($fila['fecha_inicial']) ? $fila['fecha_inicial'] : ''; ?>" required />

    <label class="label" for="fecha_final">Fecha Final:</label>
    <input class="entrada " type="date" id="fecha_final" name="fecha_final" value="<?php echo isset($fila['fecha_final']) ? $fila['fecha_final'] : ''; ?>" required />

    <!-- ID oculto para enviar en el formulario -->
    <input type="hidden" name="id" value="<?php echo isset($fila['id']) ? $fila['id'] : ''; ?>">

    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

    <!-- Botones de acción -->
    <input class="boton" type="submit" name="action" value="Actualizar" />
    <input class=" eliminarEncargado" type="submit" name="borrar" value="Eliminar" />

    </form>
</div>

<?php 
// Cerrar la conexión
$conexion->close();
?>
</body>
</html>
