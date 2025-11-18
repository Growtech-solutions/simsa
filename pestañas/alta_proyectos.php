<!DOCTYPE html>
<html lang="es">
<?php
// Consulta para obtener el siguiente valor de OT
$query = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$base_de_datos' AND TABLE_NAME = 'ot'";
$result = mysqli_query($conexion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $siguiente_ot = $row['AUTO_INCREMENT'];
} else {
    // Manejo de error en caso de que la consulta falle
    echo "Error en la consulta: " . mysqli_error($conexion);
    $siguiente_ot = ''; // O establece un valor por defecto
}
$header_loc = htmlspecialchars($_GET['header_loc']);
?>
<style>
/* Alta de proyecto */
.entrada:nth-child(2) {
    grid-column: 2/4;
}
.entrada:nth-child(4) {
    grid-column: 1/3;
}
.entrada:nth-child(5) {
    grid-column: 3/5;
}
.entrada:nth-child(6) {
    grid-column: 1/5;
}
.altadeproyecto__boton__enviar {
    grid-column: 4;
}


</style>
<body id="altadeproyecto">
    <div class="principal">
        <div>
        <div class="max-w-4xl mx-auto p-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Alta de proyecto</h2>
            <form class="space-y-4" action="../php/procesar_alta_proyecto.php" method="POST">
            <div class="w-full">
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                   type="text" id="ot" name="ot" placeholder="OT" value="<?php echo $siguiente_ot; ?>" required readonly>
            </div>
            
            <div class="w-full">
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   type="text" id="nombreDelProyecto" name="descripcion" placeholder="Nombre del proyecto" required>
            </div>
            
            <div class="w-full">
                <?php 
                $selectDatos->obtenerOpciones('listas', 'usuarios', 'usuarios', 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'); 
                ?>
            </div>

            <div class="w-full">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    id="pedido" name="pedido" required>
                <option value="" disabled selected>Selecciona un pedido</option>
                <?php
                $query_pedidos = "SELECT id, descripcion, cliente, planta FROM pedido";
                $result_pedidos = mysqli_query($conexion, $query_pedidos);
                if ($result_pedidos) {
                    while ($row_pedido = mysqli_fetch_assoc($result_pedidos)) {
                    echo "<option value='" . $row_pedido['id'] . "'>" . htmlspecialchars($row_pedido['descripcion']) ."-". htmlspecialchars($row_pedido['cliente']) ."-".htmlspecialchars($row_pedido['planta']) ."</option>";
                    }
                } else {
                    echo "<option value='' disabled>Error al cargar pedidos</option>";
                }
                ?>
                </select>
            </div>

            <div class="w-full">
                <?php 
                $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable', 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent'); 
                ?>
            </div>

            <div class="w-full">
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   type="number" id="valor" name="valor" placeholder="Valor" required step="0.01" 
                   title="Por favor, ingresa un número con hasta 2 decimales.">
            </div>

            <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Enviar
                </button>
            </div>
            </form>

            <?php
            if (isset($_GET['confirmacion'])) {
            $confirmacion = htmlspecialchars($_GET['confirmacion']);
            echo "<div class='mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg'>$confirmacion</div>";
            }
            ?>
        </div>
        </div>
    </div>
</body>
</html>
