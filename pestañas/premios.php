<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta Premios</title>
    <style>
        .principal{
            max-width: 800px;
        }
        .principal label {
            display: block;
            margin-bottom: 10px;
        }
        .principal input, .principal select {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .principal button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
        .principal button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="principal">
    <div>
    <h2 class="text-2xl font-bold text-blue-600 text-center">Registrar Premio</h2>
    <br>
    <form method="POST" class="space-y-4">
        <div class="form-group">
            <label for="trabajador" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Trabajador:</label>
            <?php $SelectTrabajadores->obtenerNombres('trabajador',''); ?>
        </div>

        <div class="form-group">
            <label for="horas" class="block text-sm font-medium text-gray-700 mb-2">Cantidad de Horas:</label>
            <input type="number" id="horas" name="horas" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="form-group">
            <label for="ot" class="block text-sm font-medium text-gray-700 mb-2">Orden de trabajo:</label>
            <input type="number" id="ot" name="ot" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="form-group">
            <label for="tipo_premio" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Premio:</label>
            <?php $selectDatos->obtenerOpciones('listas', 'bono', 'tipo_premio','');?>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">Registrar Premio</button>
    </form>
    </div>

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
