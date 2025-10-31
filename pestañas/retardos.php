<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registro de retrasos</title>
    <style>
        
        .formulario {
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom:20%;
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
<body id="retardos">
<div class="formulario">
    <h2>Registrar retraso</h2>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            $sql = "SELECT id, nombre FROM trabajadores WHERE Estado=1 AND id != 2243 ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . "</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores.</option>";
            }
            ?>
        </select>

        <label for="horas">Penalización:</label>
        <select id="penalizacion" name="penalizacion" required>
            <option>Seleccione</option>
            <option value="0.25">15 min</option>
            <option value="0.50">30 min</option>
            <option value="1">1 hr</option>
            <option value="2">2 hrs</option>
            <option value="3">3 hrs</option>
        </select>
        
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>

        <button type="submit">Registrar retraso</button>
    </form>

    <?php
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trabajador = $_POST['trabajador'];
    $penalizacion = $_POST['penalizacion'];
    $fecha = $_POST['fecha'];

    // Validar que no haya campos vacíos
    if (!empty($trabajador) && !empty($penalizacion) && !empty($fecha)) {
        // Insertar el registro en la base de datos
        $sql = "INSERT INTO retardo (trabajador, penalizacion, fecha) VALUES (?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql);
        $stmt->bind_param("ids", $trabajador, $penalizacion, $fecha);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Registro de retraso exitoso</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }
    } else {
        echo "Por favor, complete todos los campos.";
    }
}

    ?>
</div>
</body>
</html>