<!DOCTYPE html>
<html lang="en">
<body>

<style>
    .altatrabajador:nth-child(3) { grid-column: 3/5; }
    .altatrabajador:nth-child(4) { grid-column: 1/3; }

    .label-fecha {
        display: block;
        text-align: right;
        margin-top: 8px;
    }

    .custom-file {
        height: 90px;
    }
    .custom-file-label {
        height: 90px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        border: 2px dashed #ccc;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        color: #666;
        padding: 10px;
        text-align: center;
    }

</style>

<div class="principal">
    <div>
    <h2 class="text-2xl font-bold text-blue-600 text-center">Alta de trabajador</h2>
    <br>
    <?php 
    if(isset($_GET['confirmacion'])){
        echo "<p class='confirmacion'>".$_GET['confirmacion']."</p>";
    }
    ?>
    <form class="servicios__form" action="../php/procesar_alta_trabajador.php" method="POST" enctype="multipart/form-data">
    <input class="entrada altatrabajador" type="text" id="nombre" name="nombre" placeholder="Nombre" required>
    <input class="entrada altatrabajador" type="text" id="apellido" name="apellido" placeholder="Apellidos" required>
    <input class="entrada altatrabajador" type="text" id="CURP" name="CURP" placeholder="CURP" required>
    <input class="entrada altatrabajador" type="text" id="RFC" name="RFC" placeholder="RFC" required>
    <input class="entrada altatrabajador" type="text" id="IMSS" name="IMSS" placeholder="Número IMSS" required>
    <input class="entrada altatrabajador" type="text" id="CP" name="CP" placeholder="Código Postal" required>
    <label for="fechanacimiento" class="label-fecha">Fecha de nacimiento:</label>
    <input class="entrada altatrabajador" type="date" id="fechanacimiento" name="fechanacimiento" required>
    <label for="fechaingreso" class="label-fecha">Fecha de ingreso:</label>
    <input class="entrada altatrabajador" type="date" id="fechaingreso" name="fechaingreso" required>
    <select class="entrada altatrabajador" name="estado" id="estado" required>
        <option value="">Estado</option>
        <?php
        $ent_fed="select estado,cla from entidades_federativas";
        $result=mysqli_query($conexion,$ent_fed);
        while($row=mysqli_fetch_array($result)){
            echo "<option value='".$row['cla']."'>".$row['estado']."</option>";
        }
        ?>
    </select>
    <?php $selectDatos->obtenerOpciones('listas', 'area', 'area', 'entrada altatrabajador');  ?>
    <?php $selectDatos->obtenerOpciones('listas', 'contrato', 'tipo_contrato', 'entrada altatrabajador');  ?>
    <?php $selectDatos->obtenerOpciones('listas', 'empresa', 'empresa', 'entrada altatrabajador');  ?>
    <?php $selectDatos->obtenerOpciones('listas', 'puesto', 'puesto', 'entrada altatrabajador');  ?>
    <input class="entrada altatrabajador" type="text" id="tarjeta" name="tarjeta" placeholder="Clave banco" required>
    <?php $selectDatos->obtenerOpciones('listas', 'forma_pago', 'forma_pago', 'entrada altatrabajador');  ?>
    <input class="entrada altatrabajador" type="number" id="salario" name="salario" placeholder="Salario diario" step="0.01" required>

    <div class="custom-file">
        <input type="file" class="custom-file-input" id="curp_file" name="curp_file">
        <label class="custom-file-label" for="curp_file">
            <img class="upload" src="../img/upload.png"> CURP (PDF)
        </label>
    </div>

    <div class="custom-file">
        <input type="file" class="custom-file-input" id="rfc_file" name="rfc_file">
        <label class="custom-file-label" for="rfc_file">
            <img class="upload" src="../img/upload.png"> RFC (PDF)
        </label>
    </div>

    <div class="custom-file">
        <input type="file" class="custom-file-input" id="nss_file" name="nss_file">
        <label class="custom-file-label" for="nss_file">
            <img class="upload" src="../img/upload.png"> NSS (PDF)
        </label>
    </div>

    <div class="custom-file">
        <input type="file" class="custom-file-input" id="perfil" name="perfil">
        <label class="custom-file-label" for="perfil">
            <img class="upload" src="../img/upload.png"> Foto perfil (PNG)
        </label>
    </div>

    <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
    <input type="hidden" name="pestaña" value="alta_trabajador">

    <div class="altadeproyecto__boton__enviar">
        <input class="boton__enviar" type="submit" value="Enviar">
    </div>
</form>

</div>
    </div>
</main>
</body>
</html>

