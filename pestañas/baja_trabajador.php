<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Bajas</title>
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
    <h2>Registrar Baja</h2>
    <form method="POST">
        <label for="trabajador">Seleccionar Trabajador:</label>
        <select id="trabajador" name="trabajador" required>
            <?php
            $sql = "SELECT id, nombre, apellidos FROM trabajadores WHERE estado='Activo' AND id != 2243 ORDER BY nombre";
            $resultado = $conexion_transimex->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<option value='" . $fila["id"] . "'>" . $fila["nombre"] . " ". $fila["apellidos"] . "</option>";
                }
            } else {
                echo "<option value=''>No hay trabajadores.</option>";
            }
            ?>
        </select>

        <label for="causa">Causa de la Baja:</label>
        <select id="causa" name="causa" required>
            <option value="renuncia_voluntaria">Renuncia voluntaria</option>
            <option value="despido_justificado">Despido justificado</option>
            <option value="despido_injustificado">Despido injustificado</option>
        </select>
        
        <label for="comentario">Descripcion de la Baja:</label>
        <input type="text" id="comentario" name="comentario" required>
        
        <label for="fecha">Fecha de la Baja:</label>
        <input type="date" id="fecha" name="fecha" required>

        <button type="submit">Registrar Baja</button>
    </form>

    <?php
require_once '../conexion_transimex.php'; // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trabajador = $_POST['trabajador'];
    $causa = $_POST['causa'];
    $fecha = $_POST['fecha'];
    $comentario = $_POST['comentario'];

    // Insertar baja en la tabla 'bajas'
    $sql = "INSERT INTO bajas (id_trabajador, causa, fecha, comentario) VALUES (?, ?, ?, ?)";
    $stmt = $conexion_transimex->prepare($sql);
    $stmt->bind_param("isss", $trabajador, $causa, $fecha, $comentario);

    // Actualizar el estado del trabajador
    $sql_trabajador = "UPDATE trabajadores SET estado='Inactivo' WHERE id=?";
    $stmt_trabajador = $conexion_transimex->prepare($sql_trabajador);
    $stmt_trabajador->bind_param("i", $trabajador);

    if ($stmt->execute() && $stmt_trabajador->execute()) {
        echo "<p>Baja registrada exitosamente y trabajador marcado como inactivo.</p>";
    } else {
        echo "<p>Error al registrar la baja: " . $conexion_transimex->error . "</p>";
    }

    // Cerrar las consultas preparadas
    $stmt->close();
    $stmt_trabajador->close();
    $conexion_transimex->close();
}
?>

</div>
</body>
</html>
