<?php
// Conexión a la base de datos
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conexion->connect_error) {
    die("La conexión falló: " . $conexion->connect_error);
}

// Obtener las columnas de la tabla listas
$sql_columnas = "SHOW COLUMNS FROM listas";
$resultado_columnas = $conexion->query($sql_columnas);
$columnas = [];

while ($fila = $resultado_columnas->fetch_assoc()) {
    $columnas[] = $fila['Field'];
}

// Seleccionar columna
$columna_seleccionada = isset($_GET['columna']) ? $_GET['columna'] : 'rol';

// Verificar si la columna es válida
if (!in_array($columna_seleccionada, $columnas)) {
    die("Columna inválida.");
}

// Eliminar registro
if (isset($_POST['eliminarRegistro'])) {
    $valor = $_POST['valor'];
    $sql_eliminar = "DELETE FROM listas WHERE $columna_seleccionada = ?";
    $stmt = $conexion->prepare($sql_eliminar);
    $stmt->bind_param("s", $valor);
    $stmt->execute();
    $stmt->close();
}

// Agregar nuevo registro
if (isset($_POST['agregarRegistro'])) {
    $nuevo_valor = $_POST['nuevo_valor'];
    $sql_insertar = "INSERT INTO listas ($columna_seleccionada) VALUES (?)";
    $stmt = $conexion->prepare($sql_insertar);
    $stmt->bind_param("s", $nuevo_valor);
    $stmt->execute();
    $stmt->close();
}

// Consultar registros de la columna seleccionada
$sql_registros = "SELECT id, $columna_seleccionada FROM listas WHERE $columna_seleccionada IS NOT NULL";
$resultado_registros = $conexion->query($sql_registros);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Gestión de Listas</title>
        <style>
            .contenedor { display: grid; grid-template-columns: 70% 30%; gap: 20px; }
            .paginacion a { margin: 0 5px; text-decoration: none; }
            h2{text-align: center;}
        </style>
    </head>
    <body>
        <div class="contenedor principal">
                <h2>Gestión de Listas</h2>
                <form action="" method="GET">
                    <label for="columna">Selecciona lista:</label>
                    <select name="columna" id="columna" onchange="this.form.submit()">
                        <?php foreach ($columnas as $col): ?>
                            <option value="<?= $col ?>" <?= $col == $columna_seleccionada ? 'selected' : '' ?>><?= ucfirst($col) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="pestaña" value="registro_listas">
                </form>
            <div>
                <table border="1" cellspacing="0" cellpadding="10">
                    <thead>
                        <tr>
                            <th colspan="3">Registros de la columna: <?= ucfirst($columna_seleccionada) ?></th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Valor</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($fila = $resultado_registros->fetch_assoc()): ?>
                        <tr>
                            <td><?= $fila['id'] ?></td>
                            <td><?= $fila[$columna_seleccionada] ?></td>
                            <td>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="valor" value="<?= $fila[$columna_seleccionada] ?>">
                                    <button type="submit" name="eliminarRegistro" onclick="return confirm('¿Eliminar este registro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Formulario para agregar -->
            <div>
                <h3>Agregar Registro</h3>
                <form action="" method="POST">
                    <input type="text" name="nuevo_valor" required>
                    <button type="submit" name="agregarRegistro">Agregar</button>
                </form>
            </div>
        </div>
    </body>
</html>
