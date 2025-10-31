<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta Premios</title>
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
    <h2>Registrar Premio</h2>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <?php $SelectTrabajadores->obtenerNombres('trabajador',''); ?>

        <label for="horas">Cantidad de Horas:</label>
        <input type="number" id="horas" name="horas" min="0" step="0.01" required>
        
        <label for="horas">Orden de trabajo:</label>
        <input type="number" id="ot" name="ot" required>

        <label for="tipo_premio">Tipo de Premio:</label>
        <?php $selectDatos->obtenerOpciones('listas', 'bono', 'tipo_premio','');?>

        <button type="submit">Registrar Premio</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $trabajador = $_POST['trabajador'];
        $horas = $_POST['horas'];
        $tipo_premio = $_POST['tipo_premio'];
        $ot = $_POST['ot'];

        

        $sql = "INSERT INTO bonos (id_trabajador, horas, fecha,ot,tipo) VALUES (?, ?, CURDATE(),?,?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("idis", $trabajador, $horas,$ot,$tipo_premio);

        if ($stmt->execute()) {
            echo "<p>Premio registrado exitosamente.</p>";
        } else {
            echo "<p>Error al registrar el premio: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
    ?>
</div>
</body>
</html>
