<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Incapacidad</title>
    <style>
        .formulario {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
    <h2>Registrar Incapacidad</h2>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            
            if ($conexion_transimex->connect_error) {
                die("Error de conexión: " . $conexion_transimex->connect_error);
            }
            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE Estado='Activo' ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " " . $fila["apellidos"] ."</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores.</option>";
            }
            ?>
        </select>

        <label for="tipo_inc">Tipo:</label>
        <?php $selectDatos->obtenerOpciones('listas', 'tipo_incapacidad', 'tipo_incapacidad', ''); ?>

        <label for="dias">Días de Incapacidad:</label>
        <input type="number" id="dias" name="dias" required>

        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion" required>

        <label for="fecha">Fecha de Inicio:</label>
        <input type="date" id="fecha" name="fecha" required>

        <button type="submit">Registrar Incapacidad</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $dias = $_POST['dias'];
        $descripcion = $_POST['descripcion'];
        $fecha = $_POST['fecha'];
        $tipo_inc= $_POST['tipo_incapacidad'];

        if ($conexion_transimex->connect_error) {
            die("Error de conexión: " . $conexion_transimex->connect_error);
        }

        $sql = "INSERT INTO incapacidad (id_trabajador,tipo_incapacidad, dias, fecha, descripcion) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion_transimex->prepare($sql);
        $stmt->bind_param("isiss", $trabajador, $tipo_inc, $dias, $fecha, $descripcion);

        if ($stmt->execute()) {
            echo "<p>Incapacidad registrada exitosamente.</p>";
        } else {
            echo "<p>Error al registrar la incapacidad: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
    ?>
</div>
</body>
</html>
