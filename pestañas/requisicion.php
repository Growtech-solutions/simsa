<!DOCTYPE html>
<html lang="en">
<head>
    <title>Requisicion</title>
    <!-- Include CSS files -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var currentPageId = document.body.id;
        var activeButton = document.querySelector('.area__boton[href="' + currentPageId + '.php"]');
        if (activeButton) {
            activeButton.classList.add('active');
        }

        document.getElementById('add-partida').addEventListener('click', function(e) {
        e.preventDefault();
        let partidaTemplate = document.querySelector('.partida-template');
        let newPartida = partidaTemplate.cloneNode(true);
        newPartida.classList.remove('partida-template');
        newPartida.classList.add('partida'); // Asegúrate de agregar la clase 'partida'
        newPartida.style.display = "grid"; // Asegúrate de que el nuevo elemento sea visible
        document.getElementById('partidas-container').appendChild(newPartida);
        });

    });
    </script>
</head>
<body id="requisicion">

<style>
    .requisicion_form {
         gap: 1rem;
    }
    .requisicion_form_head {
        gap: 1rem;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
    }
    .partidas-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
    }
    .partida-template,
    .partida {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .5rem;
        padding-top: 1.5rem;
    }

    .partida-template {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .5rem;
        padding-top: 1.5rem;
        display: none; /* Oculto inicialmente */
    }
    .custom-file {
        position: relative;
        display: inline-block;
        width: 100%;
        margin-bottom: 10px;
    }
    .custom-file-input {
        position: relative;
        z-index: 2;
        width: 100%;
        height: 40px;
        margin: 0;
        opacity: 0;
    }
    .custom-file-label {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1;
        height: 60px;
        padding: 10px;
        line-height: 20px;
        color: #495057;
        background-color: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 5px;
        cursor: pointer;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .upload {
        width: 20px;
        height: 20px;
        margin-right: 5px;
        vertical-align: middle;
    }
    .custom-file-input:focus ~ .custom-file-label {
        border-color: #4d90fe;
    }
    .bottom{
        display:grid;
        grid-template-columns: 33% 33% 33%;
        padding-top:1rem;
    }
   
</style>
<div class="principal">
    <div>
    <h2 class="text-2xl font-bold text-blue-600 text-center">Requisición</h2>
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
    <form class="requisicion_form" action="../php/procesar_requisicion.php" method="POST" enctype="multipart/form-data">
        <div class="requisicion_form_head">
            <label class='label'>Fecha llegada:</label>
            <input class="entrada requisicion" type="date" name="fecha_llegada">
            <?php $selectDatos->obtenerOpciones('listas', 'responsables', 'responsable', 'entrada'); ?>
            <?php $selectDatos->obtenerOpciones('proveedor', 'proveedor', 'proveedor', 'entrada'); ?>
            <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
        </div>
        
        <div id="partidas-container">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="partida">
                    <input type="text" class="entrada requisicion" name="cantidad[]" placeholder="Cantidad">
                    <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad[]','entrada');  ?>
                    <input type="text" class="entrada requisicion" name="descripcion[]" placeholder="Descripción">
                    <input type="text" class="entrada requisicion" name="ot[]" placeholder="OT">
                    <input type="text" class="entrada requisicion" name="precio_unitario[]" placeholder="Precio Unitario">
                    <?php $selectDatos->obtenerOpciones('listas', 'moneda', 'moneda[]','entrada');  ?>
                    <select class="entrada requisicion" name="cotizacion[]">
                        <option value="">Cotización</option>
                        <option value="+IVA">+IVA</option>
                        <option value="NETO">NETO</option>
                    </select>
                    <input type="text" class="entrada requisicion" name="observaciones[]" placeholder="Comentarios">
                </div>
            <?php endfor; ?>

            <div class="partida-template">
                <input type="text" class="entrada requisicion" name="cantidad[]" placeholder="Cantidad">
                <?php $selectDatos->obtenerOpciones('listas', 'unidades', 'unidad[]','entrada');  ?>
                <input type="text" class="entrada requisicion" name="descripcion[]" placeholder="Descripción">
                <input type="text" class="entrada requisicion" name="ot[]" placeholder="OT">
                <input type="text" class="entrada requisicion" name="precio_unitario[]" placeholder="Precio Unitario">
                <?php $selectDatos->obtenerOpciones('listas', 'moneda', 'moneda[]','entrada');  ?>
                <select class="entrada requisicion" name="cotizacion[]">
                    <option value="">Cotización</option>
                    <option value="+IVA">+IVA</option>
                    <option value="NETO">NETO</option>
                </select>
                <input type="text" class="entrada requisicion" name="observaciones[]" placeholder="Comentarios">
            </div>
        </div>
        
        <div class="bottom">
            <button id="add-partida" class="add-partida-button">Añadir Partida</button>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="cotizacion" name="cotizacion">
                <label class="custom-file-label" for="cotizacion">
                    <img class="upload" src="../img/upload.png" alt="Upload Icon">Cotización
                </label>
            </div>
            <div>
                <input class="boton__enviar" type="submit" value="Enviar">
            </div>
        </div>
        
    </form>
</div>
            </div>
<?php
$conexion->close();
?>

</body>
</html>


