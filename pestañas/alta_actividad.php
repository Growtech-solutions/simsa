<?php
// Registrar actividad
if (isset($_POST['alta_actividad'])) {
    $actividad = $conexion->real_escape_string($_POST['actividad']);
    $tipo = $conexion->real_escape_string($_POST['tipo']);
    
    $insert_act = "INSERT INTO actividades (actividad, tipo) VALUES (?, ?)";
    $stmt = $conexion->prepare($insert_act);
    $stmt->bind_param("ss", $actividad, $tipo);
    $stmt->execute();
    $stmt->close();

}

// Eliminar actividad
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convertir a número para evitar inyección SQL
    
    $delete_act = "DELETE FROM actividades WHERE id = ?";
    $stm = $conexion->prepare($delete_act);
    $stm->bind_param("i", $id);
    $stm->execute();
    $stm->close();

}


// Obtener actividades con su tipo desde la tabla listas
$sql = "SELECT * FROM actividades";
$resultado = $conexion->query($sql);

// Obtener opciones para el select de tipo
$sql_listas = "SELECT tipo FROM listas WHERE tipo IS NOT NULL";
$resultado_listas = $conexion->query($sql_listas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actividades</title>
    <style>
        .contenedor { display: grid; grid-template-columns: 70% 30%; gap: 20px; }
        h2 { text-align: center; }
        a { color: red; text-decoration: none; }
    </style>
</head>
<body>
    <div class="contenedor principal">
        <div>
            <h2>Lista de Actividades</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Actividad</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila["id"] ?></td>
                    <td><?= $fila["actividad"] ?></td>
                    <td><?= $fila["tipo"] ?></td>
                    <td>
                        <a href="?id=<?= $fila["id"] ?>&pestaña=alta_actividad" onclick="return confirm('¿Seguro que deseas eliminar?');">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div>
            <h2>Registrar Nueva Actividad</h2>
            <form action="" method="POST">
                <input type="text" name="actividad" placeholder="Nombre de la actividad" required>
                <select name="tipo">
                    <?php while ($tipo = $resultado_listas->fetch_assoc()) { ?>
                        <option value="<?= $tipo['tipo'] ?>"><?= $tipo['tipo'] ?></option>
                    <?php } ?>
                </select>
                <button type="submit" name="alta_actividad">Agregar</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conexion->close(); ?>
