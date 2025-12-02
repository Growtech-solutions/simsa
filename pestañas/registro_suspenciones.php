<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Suspensión</title>
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
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
        }
        .formulario label {
            display: block;
            margin-bottom: 10px;
        }
        .formulario button {
            padding: 10px 20px;
            background-color: blue;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="formulario">
    <h1 class="text-2xl font-bold text-blue-600">Registrar Suspensión</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $dias = $_POST['dias']-1;
        $motivo = $_POST['motivo'];
        $fecha = $_POST['fecha'];
        $fecha_final = date('Y-m-d', strtotime("+$dias days", strtotime($fecha)));
        $dias = $_POST['dias'];

        $sql = "INSERT INTO suspensiones (id_trabajador, dias, fecha_inicial, fecha_final, motivo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql);
        $stmt->bind_param("iisss", $trabajador, $dias, $fecha, $fecha_final, $motivo);

        if ($stmt->execute()) {
            echo "<br><p style='color: green;'>✅ Suspensión registrada exitosamente.</p>";
        } else {
            echo "<br><p style='color: red;'>❌ Error al registrar la suspensión: " . $stmt->error . "</p>";
        }
    }
    ?>
    <br>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php

            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE Estado='Activo' ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " " . $fila["apellidos"] . "</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores disponibles.</option>";
            }
            ?>
        </select>

        <label for="dias">Días:</label>
        <input type="number" id="dias" name="dias" min="1" required>

        <label for="motivo">Motivo:</label>
        <input type="text" id="motivo" name="motivo" required>

        <label for="fecha">Fecha de Inicio:</label>
        <input type="date" id="fecha" name="fecha" required>

        <button type="submit">Registrar Suspensión</button>
    </form>
</div>
</body>
</html>
