<!DOCTYPE html>
<html lang="en">
<?php

// Verificar la conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_trabajador = isset($_GET['id']) ? $_GET['id'] : null;
    $piezas = $_POST['pieza'] ?? [];
    $duraciones = $_POST['duracion'] ?? [];
    $previos = $_POST['previo'] ?? [];

    // Iterar sobre las piezas enviadas y guardarlas en la base de datos
    for ($i = 0; $i < count($piezas); $i++) {
        $id_pieza = mysqli_real_escape_string($conexion, $piezas[$i]);
        $duracion = mysqli_real_escape_string($conexion, $duraciones[$i]);
        $id_previa = mysqli_real_escape_string($conexion, $previos[$i]);

        // Verificar que los campos no estén vacíos
        if ($id_pieza && $duracion) {
            $sql_insert = "
                INSERT INTO cronograma (id_pieza, id_trabajador, id_previa, duracion)
                VALUES ('$id_pieza', '$id_trabajador', '$id_previa', '$duracion')
            ";
            
            if (mysqli_query($conexion, $sql_insert)) {
                // Obtener el ID del último registro insertado
                $last_id = mysqli_insert_id($conexion);

                 // Llamar al procedimiento almacenado para actualizar las dependencias
                $sql_procedure = "CALL actualizar_cronograma($last_id,$id_previa, $id_trabajador )";
                if (!mysqli_query($conexion, $sql_procedure)) {
                    echo "Error al ejecutar el procedimiento almacenado: " . mysqli_error($conexion);
                }
                
            } else {
                echo "Error al insertar datos: " . mysqli_error($conexion);
            }
        }
    }
}

// Consulta para obtener los trabajadores
$sql_trabajadores = "SELECT id, CONCAT(nombre, ' ', apellidos) AS nombre  FROM trabajadores WHERE Estado = 'Activo'";
$result_trabajadores = mysqli_query($conexion, $sql_trabajadores);
$trabajadores = [];
while ($row = mysqli_fetch_assoc($result_trabajadores)) {
    $trabajadores[] = $row['nombre'];
}

$username = $_SESSION['username'];

// Consulta para obtener las piezas dependiendo del usuario
if ($username == 'ngarcia@transimex.com.mx') {
    $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL AND (area = 'Paileria' )";
} elseif ($username == 'maquinado@transimex.com.mx') {
    $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL AND (area = 'Maquinados' )";
} elseif ($username == 'design@transimex.com.mx') {
    $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL AND (area = 'Diseño') ";
} else {
    $sql_piezas = "SELECT id, pieza FROM piezas WHERE fecha_final IS NULL order by pieza ";
}

$result_piezas = mysqli_query($conexion, $sql_piezas);
$piezas = [];
$piezas_nombre = [];
while ($row = mysqli_fetch_assoc($result_piezas)) {
    $piezas[] = $row['id'];
    $piezas_nombre[] = $row['pieza'];
}

// Consulta para obtener las piezas previas junto con el nombre de la pieza
$id_trabajador = isset($_GET['id']) ? $_GET['id'] : null;
$fecha_limite = date('Y-m-d', strtotime('-3 days'));
$sql_previo = "
    SELECT c.id, c.id_pieza,c.fecha_inicial, p.pieza AS nombre_pieza
    FROM cronograma c
    INNER JOIN piezas p ON c.id_pieza = p.id
    WHERE c.id_trabajador = $id_trabajador 
    AND c.fecha_final >= '$fecha_limite'
   AND p.pieza != 'ultimo trabajo'
    ORDER BY c.fecha_inicial";
$result_previo = mysqli_query($conexion, $sql_previo);
$previo = [];
while ($row = mysqli_fetch_assoc($result_previo)) {
    $previo[] = [
        'id' => $row['id'],
        'id_pieza' => $row['id_pieza'],
        'nombre_pieza' => $row['nombre_pieza']
    ];
}

// Cerrar conexión
mysqli_close($conexion);
?>
<head>
    <title>Agendar trabajo</title>
    <style>
        .formulario-grid {
            width: 100%;
            display: grid;
            grid-template-columns: 33.33% 33.33% 33.33%;
            gap: 10px;
        }
        .altadepieza__boton__enviar {
            grid-column: 3 / 4;
        }
        .contenedor_servicios {
            margin: 10%;
            margin-top: 0;
        }
    </style>
</head>
<body id="agendarcitas">
    <div class="contenedor_servicios">
        <h2 class="titulo">Agendar trabajo</h2>
        <form class="formulario-grid" action="" method="POST">
            <?php 
            for ($i = 1; $i <= 1; $i++): 
                echo "<select class='entrada' name='pieza[]'>";
                    echo "<option value=''>Seleccione pieza</option>";
                    foreach ($piezas as $key => $id_pieza) {
                        echo "<option value='" . htmlspecialchars($id_pieza) . "'>" . htmlspecialchars($piezas_nombre[$key]) . "</option>";
                    }
                echo "</select>";
                
                echo "<input class='entrada' type='number' name='duracion[]' placeholder='Días'>";
                
                echo "<select class='entrada' name='previo[]'>";
                    echo "<option value=''>Después de:</option>";
                    foreach ($previo as $pieza) {
                        echo "<option value='" . htmlspecialchars($pieza['id']) . "'>" . htmlspecialchars($pieza['nombre_pieza']) . "</option>";
                    }
                echo "</select>";
                
            endfor;
            ?>
            <div class="altadepieza__boton__enviar dos-columnas">
                <input class="boton__enviar" type="submit" value="Agendar">
            </div>
        </form>
    </div>
</body>
</html>
