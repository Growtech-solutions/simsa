<?php
// Función para obtener las fotos por fecha
function obtenerFotosPorFecha($startDate = null, $endDate = null) {
    // Directorio base donde están las fotos
    $baseDir = '../captures/';
    
    // Obtener todas las carpetas (por día)
    $dirs = array_filter(glob($baseDir . '*'), 'is_dir');
    
    // Si se especifica un rango de fechas, filtrar las carpetas
    if ($startDate && $endDate) {
        $dirs = array_filter($dirs, function($dir) use ($startDate, $endDate) {
            $date = basename($dir);
            return $date >= $startDate && $date <= $endDate;
        });
    }
    
    // Mostrar las imágenes por día
    foreach ($dirs as $dir) {
        $day = basename($dir); // Obtiene la fecha de la carpeta (por ejemplo: '2025-03-21')
        echo "<h2>Fotos del día: $day</h2>";
        
        // Obtener las imágenes en esta carpeta
        $images = glob($dir . '/*.png');
        
        if (count($images) > 0) {
            echo "<div class='gallery'>";
            foreach ($images as $image) {
                $imgName = basename($image); // Nombre del archivo (por ejemplo: '15:45:30.png')
                echo "<div class='image'>
                        <img src='$image' alt='$imgName' />
                        <p>$imgName</p>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "<p>No se encontraron fotos para este día.</p>";
        }
    }
}

// Si se ha enviado el formulario con las fechas, procesarlas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'] ?: null;
    $endDate = $_POST['end_date'] ?: null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Fotos por Día</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #333;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .image {
            width: 200px;
            text-align: center;
        }
        .image img {
            width: 100%;
            height: auto;
            border: 2px solid #ccc;
            border-radius: 5px;
        }
        .image p {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }
        .filter-form {
            margin-bottom: 20px;
            text-align: center;
        }
        .filter-form input {
            padding: 5px;
            margin: 5px;
            width: 120px;
        }
        .filter-form button {
            padding: 7px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h1>Galería de Fotos por Día</h1>

    <!-- Formulario para el filtro de fechas -->
    <?php 
    $startDate = $startDate ?? date('Y-m-d');
    $endDate = $endDate ?? date('Y-m-d');
    ?>
    <div class="filter-form">
        <form method="POST" action="">
            <label for="start_date">Fecha de inicio: </label>
            <input type="date" name="start_date" id="start_date" value="<?php echo $startDate; ?>">

            <label for="end_date">Fecha de fin: </label>
            <input type="date" name="end_date" id="end_date" value="<?php echo $endDate; ?>">

            <button type="submit">Filtrar</button>
        </form>
    </div>

    <?php
    // Mostrar las fotos de acuerdo al filtro (si es que se aplica)
    obtenerFotosPorFecha($startDate, $endDate);
    ?>

</body>
</html>
