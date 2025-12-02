<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Vacaciones</title>
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
    <h1 class="text-2xl font-bold text-blue-600">Registrar Vacaciones</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $motivo = $_POST['motivo'] ?? '';

        $sql = "INSERT INTO vacaciones (id_trabajador, fecha_inicio, fecha_fin, motivo) VALUES (?, ?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql);
        $stmt->bind_param("isss", $trabajador, $fecha_inicio, $fecha_fin, $motivo);

        if ($stmt->execute()) {
            echo "<br><p>✅ Vacaciones registradas correctamente.</p>";
        } else {
            echo "<br><p>❌ Error al registrar: " . $stmt->error . "</p>";
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
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " " . $fila["apellidos"] ."</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores disponibles</option>";
            }
            ?>
        </select>

        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required>

        <label for="motivo">Motivo (opcional):</label>
        <textarea id="motivo" name="motivo"></textarea>

        <button type="submit">Registrar Vacaciones</button>
    </form>

    
</div>
</body>
</html>
