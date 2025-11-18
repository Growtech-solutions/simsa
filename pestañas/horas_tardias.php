<!DOCTYPE html>
<html lang="en">
<head>
    <title>Asignacion de horas</title>
</head>
<style>
    .ocultar{
        border: white;
    }
    .servicios__form{
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .altadepieza__boton__enviar{
        grid-column: 3/5;
    }
    .escrito{
            display: block;
            text-align: center;
            margin-top: 10px;
            border:none;
            font-weight: bold;
        }
        select {
  width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
<?php $header_loc= $_GET['header_loc']; ?>
<body id="asignaciondehoras">
<div class="principal">
    <div>
    <h2 class="text-2xl font-bold text-blue-600 text-center">Registro de horas</h2>
    <?php 
        if (isset($_GET['confirmacion'])) {
            // Sanear el valor para evitar inyección de archivos
            $confirmacion = htmlspecialchars($_GET['confirmacion']);
            // Mostrar la confirmación de forma adecuada
            echo "<div class='confirmacion'>$confirmacion</div>";
        }
        echo "<br>";
    ?>
    <script>
        function actualizarPiezas() {
            var ot = document.getElementById("ot").value.trim();
            if (ot !== "") {
                fetch("../php/obtener_piezas.php?ot=" + encodeURIComponent(ot))
                    .then(response => response.json())
                    .then(data => {
                        let piezaSelect = document.getElementById("pieza");
                        piezaSelect.innerHTML = '<option value="">Seleccione pieza</option>';
                        
                        if (data.length > 0) {
                            data.forEach(pieza => {
                                let option = document.createElement("option");
                                option.value = pieza.id;
                                option.textContent = pieza.pieza;
                                piezaSelect.appendChild(option);
                            });
                        } else {
                            piezaSelect.innerHTML = '<option value="">No hay piezas</option>';
                        }
                    })
                    .catch(error => console.error("Error al obtener piezas:", error));
            } else {
                document.getElementById("pieza").innerHTML = '<option value="">Ingrese una OT</option>';
            }
        }
    </script>
    <form class="servicios__form" action="../php/procesar_horas.php" method="POST">
        <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        <input type="date" name="fecha" id="fecha" class="entrada" value="<?php echo date('Y-m-d'); ?>" required>
        <input class="escrito " type="text" id="ot" name="ot" placeholder="OT" required oninput="obtenerNombreProyecto(); actualizarActividades(); actualizarPiezas();">
        <input class="escrito" type="text" name="nombreDelProyecto" id="nombreDelProyecto" placeholder="Nombre del proyecto" readonly>
        <select name="pieza" id="pieza" class="escrito">
            <option value="">Pieza</option>
        </select>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <?php $SelectTrabajadores->obtenerNombres('trabajador[]', 'entrada'); ?>

            <select name="actividad[]" class="entrada actividad">
                <option value="">Actividad</option>
            </select>

            <input class="entrada" type="number" step="0.01" name="cantidad[]" placeholder="Cantidad">
            <input class="entrada" type="text" name="tiempo[]" placeholder="Tiempo">
        <?php endfor; ?>
        
        <div class="altadepieza__boton__enviar">
            <input class="boton__enviar" type="submit" value="Enviar">
        </div>
    </form>
</div>
</div>
</body>
</html>







