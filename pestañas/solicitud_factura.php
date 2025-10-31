<!DOCTYPE html>
<html lang="en">
<body id="solicitud_factura">
    <div class="principal">
        <div>
            <h2 class="titulo">Solicitud de factura</h2>
            <?php
            // Verifica si el parámetro 'confirmacion' está presente en la URL
            if (isset($_GET['confirmacion'])) {
                // Sanear el valor para evitar inyección de archivos
                $confirmacion = htmlspecialchars($_GET['confirmacion']);
                // Mostrar la confirmación de forma adecuada
                echo "<div class='confirmacion'>$confirmacion</div>";
            }
            ?>
            <br>
            <form class="servicios__form" action="../php/procesar_solicitud_factura.php" method="POST" onsubmit="prepararEnvio()">
                <input class="entrada altadepieza__campo" type="text" id="ot" name="ot" placeholder="OT" required oninput="obtenerNombreProyecto()">
                <input class="entrada altadepieza__campo" type="text" name="cliente" id="cliente" placeholder="Cliente" readonly>
                <input class="entrada altadepieza__campo" type="text" name="nombreDelProyecto" id="nombreDelProyecto" placeholder="Nombre del proyecto" readonly>
                <select class="entrada" id="pedido" name="pedido" required title="pedido">   
                    <option>Seleccione pedido</option>
                </select>
                <textarea style="grid-column: span 2;" class="entrada" id="descripcion" name="descripcion" placeholder="Descripcion de solicitud" required></textarea>
                
                <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable','entrada');?>
                <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                <div class="altadeproyecto__boton__enviar">
                    <input class="boton__enviar" type="submit" value="Enviar">
                </div>
            </form>
        </div>
    </div>

    <script>
    function obtenerNombreProyecto() {
        var ot = document.getElementById("ot").value;

        if (ot) {
            // Llamada AJAX para obtener el nombre del proyecto, cliente y los pedidos
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../php/obtener_pedidos.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);

        document.getElementById("nombreDelProyecto").value = response.nombreProyecto || "No encontrado";
        document.getElementById("cliente").value = response.cliente || "No encontrado";

        var pedidoSelect = document.getElementById("pedido");
        pedidoSelect.innerHTML = '<option>Seleccione pedido</option>';

        if (response.pedidos && response.pedidos.length > 0) {
            response.pedidos.forEach(function(pedido) {
                var option = document.createElement("option");
                option.value = pedido.descripcion;
                option.text = pedido.descripcion;
                pedidoSelect.appendChild(option);
            });
        }
    }
};

            xhr.send("ot=" + ot);
        } else {
            document.getElementById("nombreDelProyecto").value = "";
            document.getElementById("cliente").value = "";
            var pedidoSelect = document.getElementById("pedido");
            pedidoSelect.innerHTML = '<option>Seleccione pedido</option>';
        }
    }

    function formatoMoneda(input) {
        // Obtener el valor actual del campo
        var valor = input.value;

        // Remover el signo de dólar y cualquier otro carácter que no sea un dígito o un punto decimal
        valor = valor.replace(/[^0-9.]/g, '');

        // Asegurarse de que solo haya un punto decimal
        var partes = valor.split('.');
        if (partes.length > 2) {
            partes = [partes[0] + '.' + partes.slice(1).join('')];
        }

        // Formatear la parte entera con comas
        partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // Concatenar la parte entera y la parte decimal, con el signo '$' al inicio
        input.value = '$' + partes.join('.');

        // Actualizar el valor sin formato en el campo oculto
        document.getElementById('montoSinFormato').value = valor.replace(/,/g, '');
    }

    function prepararEnvio() {
        // Remover el formato de moneda antes de enviar el formulario
        var montoInput = document.getElementById('monto');
        var montoSinFormato = montoInput.value.replace(/[^0-9.]/g, '');
        document.getElementById('montoSinFormato').value = montoSinFormato;
    }
    </script>
    
</body>
</html>


