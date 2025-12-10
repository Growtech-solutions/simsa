<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pensión Alimenticia</title>
    <style>
        .formulario {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .formulario label {
            display: block;
            margin-bottom: 10px;
        }
        .formulario input, .formulario select {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
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
    <h2>Registrar Pensión Alimenticia</h2>

    <form method="POST">
        <!-- Seleccionar trabajador -->
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE Estado=1 ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);

            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " " . $fila["apellidos"] . "</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores.</option>";
            }
            ?>
        </select>

        <!-- Monto semanal -->
        <label for="monto_semanal">Monto semanal:</label>
        <input type="number" id="monto_semanal" name="monto_semanal" step="0.01" required>

        <!-- Estado -->
        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>

        <button type="submit">Registrar Pensión</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $monto = $_POST['monto_semanal'];
        $estado = $_POST['estado'];

        $sql = "INSERT INTO pension_aliment (id_trabajador, monto_semanal, Estado) VALUES (?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql);
        $stmt->bind_param("ids", $trabajador, $monto, $estado);

        if ($stmt->execute()) {
            echo "<p>Pensión registrada exitosamente.</p>";
        } else {
            echo "<p>Error al registrar la pensión: " . $stmt->error . "</p>";
        }
    }
    ?>
</div>

</body>
</html>
