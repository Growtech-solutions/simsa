<!DOCTYPE html>
<html lang="en">
<?php
// Fetch herramientas from database
$herramientasResult = $conexion->query("SELECT herramienta FROM listas where herramienta IS NOT NULL ORDER BY herramienta");
$herramientas = [];
if ($herramientasResult) {
    while ($row = $herramientasResult->fetch_assoc()) {
        $herramientas[] = $row['herramienta'];
    }
}
?>
<head>
    <title>Entrada de herramienta</title>
    <style>
        .entrada.altadepieza__campo.pases {
            display: none;
        }
        .ocultar{
            border: white;
        }
    </style>
</head>
<body id="entrada_herramienta">

<div class="contenedor__servicios">
    <h2 class="titulo">Entrada de herramienta</h2>
    <form class="servicios__form" action="../php/procesar_entrada_herramienta.php?header_loc=<?php echo $header_loc; ?>" method="POST">
        <?php for ($i = 1; $i <= 24; $i++): ?>
                <select class='entrada' name='herramienta[]' id='herramienta<?= $i ?>'>
                    <option value="">Seleccione</option>
                    <?php foreach ($herramientas as $herramienta): ?>
                        <option value="<?= htmlspecialchars($herramienta) ?>"><?= htmlspecialchars($herramienta) ?></option>
                    <?php endforeach; ?>
                </select>
        <?php endfor; ?>
        
        <!-- Submit button -->
        <div class="altadepieza__boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>

</body>
</html>

