<!DOCTYPE html>
<html lang="en">
<meta http-equiv="refresh" content="60">

<?php
// Contar piezas donde area es igual a pagina y fechainicial es NULL

$pagina_id = htmlspecialchars($_GET['area']);; // Reemplaza esto con el valor correcto de 'pagina'
$sql_count = "SELECT COUNT(*) as cantidad_piezas 
              FROM piezas 
              WHERE area = '$pagina_id' 
              AND fecha_inicial IS NULL 
              AND fecha_final IS NULL";

$resultado_count = $conexion->query($sql_count);
$cantidad_piezas = 0;

if ($resultado_count->num_rows > 0) {
    $fila_count = $resultado_count->fetch_assoc();
    $cantidad_piezas = $fila_count['cantidad_piezas'];
}
?>
        <div class="">
            <section>  
                <h1>Piezas de <?php echo htmlspecialchars($pagina_id); ?></h1>
                <div class="buscador">
                <form class="reporte_formulario form_area" method="GET" action="">
                    <label for="ot">OT:</label>
                    <input class="formulario_reporte_ot" type="text" id="ot" name="ot" placeholder="Buscar por OT">

                    <label for="pieza">Pieza:</label>
                    <input class="formulario_reporte_ot" type="text" id="pieza" name="pieza" placeholder="Nombre pieza">

                    <input type="hidden" name="pestaña" value="areas">

                    <input type="hidden" name="area" value="<?php echo htmlspecialchars($pagina_id); ?>">

                    <input type="submit" name="GenerarReporte" value="Buscar">
                </form>
                <div class="cantidad_piezas">
        <strong>Cantidad de piezas sin iniciar: </strong> <?php echo $cantidad_piezas; ?>
    </div>
                </div> 
                <table border="1">
                <tr>
                    <th class='desktop-only' >Prioridad</th>
                    <th>OT</th>
                    <th>Nombre</th>
                    <th class='desktop-only'>Cantidad</th>
                    <th class='desktop-only'>Comentario</th>
                    <th class='desktop-only'>Encargado</th>
                    <th>Acciones</th>
                    <th class='desktop-only'>Fecha de Inicio</th>
                </tr>
                <?php include '../php/tablapiezas.php'; ?>
            </table>
        </div>
    </main>
</body>
</html>

