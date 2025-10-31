<!DOCTYPE html>
<html lang="en">
<head>
    <title>Alta de consumbiles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 19px;
        }

        .contenedor__servicios {
            max-width: 90%;
            margin: auto;
            background-color: #fff;
            padding: 19px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .titulo {
            color: #333;
        }

        .alta_consumible {
            display: grid;
            grid-template-columns: 19% 19% 19% 19% 19% ;
            gap: 10px; /* More space between input fields */
        }

        .entrada {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .entrada:focus {
            border-color: #007bff; /* Highlight on focus */
            outline: none;
        }

        .altaconsumible__boton__enviar {
            grid-column: 2 / span 3;
            text-align: center;
            margin-top: 19px;
        }

        .boton__enviar {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 19px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;

        }

        .boton__enviar:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .escrito {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 19px;
        }
    </style>
</head>
<body id="alta_consumbles">
    <div>
        <div class="contenedor__servicios">
    <h2 class="titulo">Alta de consumbiles</h2>
    <form class="alta_consumible" action="" method="POST">
        
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <input class="entrada" type="text" name="nombre[]" placeholder="Producto" >
            <input class="entrada" type="text" name="proveedor[]" placeholder="Proveedor" >
            <input class="entrada" type="number" step="0.01" name="costo[]" placeholder="Costo" >
            <input class="entrada" type="text" name="unidad[]" placeholder="Unidad" >
            <input class="entrada" type="number" step="0.01" name="minimo[]" placeholder="Cantidad Mínima" >
        <?php endfor; ?>

        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        <input type="hidden" name="pestaña" value="alta_consumibles">
        <div class="altaconsumible__boton__enviar">
            <input class="boton__enviar" type="submit" name="submit" value="Registrar consumbiles">
        </div>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        // Enable error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Prepare and bind
        $stmt = $conexion->prepare("INSERT INTO consumibles (nombre, proveedor, unidad, minimo, costo) VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $conexion->error);
        }

        // Loop through the form inputs and insert them into the database
        $success = true;
        foreach ($_POST['nombre'] as $index => $nombre) {
            $proveedor = $_POST['proveedor'][$index];
            $minimo = $_POST['minimo'][$index];
            $costo = $_POST['costo'][$index];
            $unidad = $_POST['unidad'][$index];
            
            // Check if the consumible name is not empty before inserting
            if (!empty($nombre)) {
                if (!$stmt->bind_param("sssdd", $nombre, $proveedor, $unidad, $minimo, $costo)) { 
                    echo "Bind failed: " . $stmt->error;
                    $success = false;
                    break;
                }
                if (!$stmt->execute()) {
                    echo "Execute failed: " . $stmt->error;
                    $success = false;
                    break;
                }
            }
        }

        $stmt->close();
        $conexion->close();

        if ($success) {
            echo "<p class='escrito'>consumbiles registrados con éxito.</p>";
        }
    }
    ?>
</div>
    </div>

</body>
</html>