<!DOCTYPE html>
<html lang="en">
<?php
// Fetch epps from database
$eppsResult = $conexion->query("SELECT epp FROM listas where epp is not null");
$epps = [];
if ($eppsResult) {
    while ($row = $eppsResult->fetch_assoc()) {
        $epps[] = $row['epp'];
    }
}
?>
<head>
    <title>Entrada de epp</title>
    <style>
        .entrada.altadepieza__campo.pases {
            display: none;
        }
        .ocultar{
            border: white;
        }
    </style>
</head>
<body id="entrada_epp">

<div class="contenedor__servicios">
    <h2 class="titulo">Entrada de epp</h2>
    <form class="servicios__form" action="../php/procesar_entrada_epp.php" method="POST">
        <?php for ($i = 1; $i <= 24; $i++): ?>
                <select class='entrada' name='epp[]' id='epp<?= $i ?>'>
                    <option value="">Seleccione</option>
                    <?php foreach ($epps as $epp): ?>
                        <option value="<?= htmlspecialchars($epp) ?>"><?= htmlspecialchars($epp) ?></option>
                    <?php endforeach; ?>
                </select>
        <?php endfor; ?>

        <input type="hidden" name="pestaña" value="entrada_epp">
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        
        <!-- Submit button -->
        <div class="altadepieza__boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>

</body>
</html>

