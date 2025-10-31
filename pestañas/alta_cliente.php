<!DOCTYPE html>
<html lang="en">
<div>
<?php

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar los datos del formulario
    $nombre = $_POST['nombre'];
    $rfc = $_POST['rfc'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $municipio = $_POST['municipio'];
    $estado = $_POST['estado'];
    $pais = $_POST['pais'];
    $codigo_postal = $_POST['codigo_postal'];

    // Guardar en la base de datos
    $sql = "INSERT INTO cliente (razon_social, rfc, email, telefono, ciudad, estado, pais, codigo_postal, fecha_alta) 
            VALUES ('$nombre', '$rfc', '$email', '$telefono', '$municipio', '$estado', '$pais', '$codigo_postal', NOW())";
    
    if ($conexion->query($sql) === TRUE) {
        echo "Nuevo cliente guardado en la base de datos correctamente.";
    } else {
        echo "Error al guardar en la base de datos: " . $conexion->error;
    }

    // Cerrar la conexión
    $conexion->close();
}
?>

<body id="alta_cliente">
    <div class="contenedor__servicios">
        <h2 class="titulo">Alta de Cliente</h2>
        <form class="servicios__form" action="" method="POST">
            <input class="entrada altadeproyecto__campo" type="text" id="nombre" name="nombre" placeholder="Nombre" required>
            <input class="entrada altadeproyecto__campo" type="text" id="rfc" name="rfc" placeholder="RFC" required>
            <input class="entrada altadeproyecto__campo" type="email" id="email" name="email" placeholder="Correo electrónico">
            <input class="entrada altadeproyecto__campo" type="text" id="telefono" name="telefono" placeholder="Teléfono">
            <input class="entrada altadeproyecto__campo" type="text" id="municipio" name="municipio" placeholder="municipio">
            <input class="entrada altadeproyecto__campo" type="text" id="estado" name="estado" placeholder="Estado">
            <input class="entrada altadeproyecto__campo" type="text" id="pais" name="pais" placeholder="País">
            <input class="entrada altadeproyecto__campo" type="text" id="codigo_postal" name="codigo_postal" placeholder="Código Postal">
            <div class="altadeproyecto__boton__enviar">
                <input class="boton__enviar" type="submit" value="Enviar">
            </div>
        </form>
    </div>
</body>
</div>

</html>

