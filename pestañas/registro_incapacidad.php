<?php

// Función para obtener las opciones del tipo de incapacidad
function obtenerOpcionesIncapacidad($conexion_transimex) {
    $sql = "SELECT DISTINCT tipo_incapacidad FROM listas ORDER BY tipo_incapacidad";
    $resultado = $conexion_transimex->query($sql);
    $opciones = "";
    while ($fila = $resultado->fetch_assoc()) {
        $opciones .= "<option value='" . $fila['tipo_incapacidad'] . "'>" . $fila['tipo_incapacidad'] . "</option>";
    }
    return $opciones;
}

// Manejo del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trabajador = $_POST['trabajador'];
    $dias = $_POST['dias']-1;
    $descripcion = $_POST['descripcion'];
    $fecha_inicio = $_POST['fecha'];
    $tipo_inc = $_POST['tipo_incapacidad'];

    // Calcular la fecha final sumando días
    $fecha_fin = date('Y-m-d', strtotime("+$dias days", strtotime($fecha_inicio)));

    $sql = "INSERT INTO incapacidades (id_trabajador,dias, tipo_incapacidad, fecha_inicio, fecha_fin, observaciones) 
            VALUES (?,?, ?, ?, ?, ?)";
    $stmt = $conexion_transimex->prepare($sql);
    $stmt->bind_param("iissss", $trabajador,$dias, $tipo_inc, $fecha_inicio, $fecha_fin, $descripcion);

    if ($stmt->execute()) {
        $mensaje = "<p style='color:green;'>✅ Incapacidad registrada exitosamente.</p>";
    } else {
        $mensaje = "<p style='color:red;'>❌ Error al registrar la incapacidad: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Incapacidad</title>
    <style>
        .formulario {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        .formulario input, .formulario select, .formulario textarea {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }
        .formulario label {
            display: block;
            margin-bottom: 5px;
        }
        .formulario button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        .formulario button:hover {
           background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="formulario">
    <h1 class="text-2xl font-bold text-blue-600">Registrar Incapacidad</h1>
    <?php if (isset($mensaje)) echo "<br><div class='mensaje'>$mensaje</div>"; ?>
    <br>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE Estado='Activo' ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " " . $fila["apellidos"] ."</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores activos</option>";
            }
            ?>
        </select>

        <label for="tipo_incapacidad">Tipo de Incapacidad:</label>
        <select name="tipo_incapacidad" id="tipo_incapacidad" required>
            <option value="">Seleccione un tipo</option>
            <?= obtenerOpcionesIncapacidad($conexion_transimex); ?>
        </select>

        <label for="dias">Días de Incapacidad:</label>
        <input type="number" name="dias" id="dias" min="1" required>

        <label for="fecha">Fecha de Inicio:</label>
        <input type="date" name="fecha" id="fecha" required>

        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" id="descripcion" required>

        <button type="submit">Registrar Incapacidad</button>
    </form>
</div>
</body>
</html>
