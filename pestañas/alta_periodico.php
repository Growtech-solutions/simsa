<?php
// Registrar actividad
if (isset($_POST['alta_periodico'])) {
    $objeto= $conexion->real_escape_string($_POST['objeto']);
    $actividad = $conexion->real_escape_string($_POST['actividad']);
    $frecuencia = $conexion->real_escape_string($_POST['frecuencia']);
    
    $insert_per = "INSERT INTO periodicos (id_act, objeto, frecuencia) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($insert_per);
    $stmt->bind_param("isd", $actividad, $objeto, $frecuencia);
    $stmt->execute();
    $stmt->close();

}

// Eliminar actividad
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convertir a número para evitar inyección SQL
    
    $delete_per = "DELETE FROM periodicos WHERE id = ?";
    $stm = $conexion->prepare($delete_per);
    $stm->bind_param("i", $id);
    $stm->execute();
    $stm->close();

}


// Obtener actividades con su tipo desde la tabla listas
$sql = "SELECT 
            actividades.actividad, 
            periodicos.objeto, 
            periodicos.frecuencia, 
            periodicos.id as id_periodico 
        FROM periodicos left join actividades on periodicos.id_act = actividades.id";
$resultado = $conexion->query($sql);

// Obtener opciones para el select de tipo
$sql_listas = "SELECT objeto FROM listas WHERE objeto IS NOT NULL";
$resultado_listas = $conexion->query($sql_listas);

$sql_actividades = "SELECT * FROM actividades ";
$resultado_actividad = $conexion->query($sql_actividades);
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
            <h2>Lista de periodicos</h2>
            <table>
                <tr>
                    <th>Actividad</th>
                    <th>Objeto</th>
                    <th>Frecuencia</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?= $fila["actividad"] ?></td>
                    <td><?= $fila["objeto"] ?></td>
                    <td><?= $fila["frecuencia"] ?></td>
                    <td>
                        <a href="?id=<?= $fila["id_periodico"] ?>&pestaña=alta_periodico" onclick="return confirm('¿Seguro que deseas eliminar?');">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div>
            <h2>Registrar Nueva Actividad</h2>
            <form action="" method="POST">
                
                <select name="actividad">
                    <option value="">Actividad</option>
                    <?php while ($actividad = $resultado_actividad->fetch_assoc()) { ?>
                        <option value="<?= $actividad['id'] ?>"><?= $actividad['actividad'] ?></option>
                    <?php } ?>
                </select>
                <select name="objeto">
                    <option value="">Objeto</option>
                    <?php while ($objeto = $resultado_listas->fetch_assoc()) { ?>
                        <option value="<?= $objeto['objeto'] ?>"><?= $objeto['objeto'] ?></option>
                    <?php } ?>
                </select>
                <br>
                <input type="text" name="frecuencia" placeholder="Frecuencia semanal" required>
                <button type="submit" name="alta_periodico">Agregar</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php $conexion->close(); ?>
