<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Asistencia</title>
    <style>
        .formulario {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
        .formulario input, .formulario select {
            display: block;
            width: 90%;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
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
    <h1 class="text-2xl font-bold text-blue-600">Registro de Asistencia</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $ubicacion = $_POST['ubicacion'];
        $fecha_hora = $_POST['fecha_hora'];

        if ($ubicacion === 'fuera') {
            $ubicacion_texto = 'Fuera de Planta';
            $latitud = null;
            $longitud = null;
        } else {
            $ubicacion_id = intval($ubicacion);
            $sql_coords = "SELECT nombre, latitud, longitud FROM ubicaciones WHERE id = ?";
            $stmt = $conexion_transimex->prepare($sql_coords);
            $stmt->bind_param("i", $ubicacion_id);
            $stmt->execute();
            $stmt->bind_result($ubicacion_texto, $latitud, $longitud);
            $stmt->fetch();
            $stmt->close();
        }

        $sql_insert = "INSERT INTO asistencia (trabajador_id, fecha, ubicacion, latitud, longitud) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql_insert);
        $stmt->bind_param("issdd", $trabajador, $fecha_hora, $ubicacion_texto, $latitud, $longitud);

        if ($stmt->execute()) {
            echo "<br><p>✅ Asistencia registrada correctamente.</p>";
        } else {
            echo "<br><p>❌ Error al registrar asistencia: " . $stmt->error . "</p>";
        }
    }
    ?>
    <br>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE estado = 'Activo' ORDER BY nombre";
            $res = $conexion_transimex->query($sql);
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['nombre']} {$row['apellidos']}</option>";
            }
            ?>
        </select>

        <label for="ubicacion">Seleccionar Ubicación:</label>
        <select id="ubicacion" name="ubicacion" required>
            <?php
            $sql = "SELECT id, nombre FROM ubicaciones ORDER BY nombre";
            $res = $conexion_transimex->query($sql);
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
            }
            ?>
            <option value="fuera">Fuera de Planta</option>
        </select>

        <label for="fecha_hora">Fecha y hora de llegada:</label>
        <input type="datetime-local" name="fecha_hora" required>

        <button type="submit">Registrar Asistencia</button>
    </form>

    
</div>
</body>
</html>
