<?php
include '../conexion_servicios.php';
$mensaje = "";
$datos_existentes = null;

// Buscar perfil actual (si solo hay uno, usamos LIMIT 1)
$query = "SELECT * FROM perfil_fiscal LIMIT 1";
$result = $conexion_servicios->query($query);
if ($result->num_rows > 0) {
    $datos_existentes = $result->fetch_assoc();
}

// Procesamiento al enviar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfc = $_POST['rfc'];
    $razon_social = $_POST['razon_social'];
    $regimen_fiscal = $_POST['regimen_fiscal'];
    $password_key = $_POST['password_key'];
    $codigo_postal = $_POST['codigo_postal'];
    $registro_patronal = $_POST['registro_patronal'];
    $curp = $_POST['curp'] ? $_POST['curp'] : null;
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $upload_dir = "/var/secure_csd/$rfc/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $key_path = $datos_existentes['ruta_key'] ?? "";
    $cer_path = $datos_existentes['ruta_cer'] ?? "";
    $logo_path = $datos_existentes['logo'] ?? "";


if (!empty($_FILES['cer']['tmp_name'])) {
    $cer_path = "";
    $cer_filename = basename($_FILES['cer']['name']);
    $cer_path = $upload_dir . $cer_filename;
    move_uploaded_file($_FILES['cer']['tmp_name'], $cer_path);
}

if (!empty($_FILES['key']['tmp_name'])) {
    $key_path = "";
    $key_filename = basename($_FILES['key']['name']);
    $key_path = $upload_dir . $key_filename;
    move_uploaded_file($_FILES['key']['tmp_name'], $key_path);
}


    $logo_dir = '/img/logos/';
    if (!file_exists($logo_dir)) {
        mkdir($logo_dir, 0777, true); // crea la carpeta si no existe
    }
    if (!empty($_FILES['logo']['name'])) {
        $logo_path = $logo_dir . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path);
    }

    if ($datos_existentes) {
        // Actualizar si ya existe
        $stmt = $conexion_servicios->prepare("UPDATE perfil_fiscal SET logo=?, rfc=?, razon_social=?, regimen_fiscal=?, ruta_cer=?, ruta_key=?, password_key=?, codigo_postal=?, registro_patronal=?, curp=?, correo=?, telefono=?, direccion=? WHERE id=?");
        $stmt->bind_param("sssssssssssssi", $logo_path, $rfc, $razon_social, $regimen_fiscal, $cer_path, $key_path, $password_key,$codigo_postal, $registro_patronal, $curp, $correo, $telefono, $direccion, $datos_existentes['id']);
    } else {
        // Insertar nuevo
        $stmt = $conexion_servicios->prepare("INSERT INTO perfil_fiscal (logo, rfc, razon_social, regimen_fiscal, ruta_cer, ruta_key, password_key,codigo_postal, registro_patronal, curp, correo, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $logo_path, $rfc, $razon_social, $regimen_fiscal, $cer_path, $key_path, $password_key, $codigo_postal, $registro_patronal, $curp, $correo, $telefono, $direccion);
    }

    if ($stmt->execute()) {
        $mensaje = "✅ Perfil fiscal guardado correctamente.";
        // Refrescar datos existentes
        $result = $conexion_servicios->query($query);
        if ($result->num_rows > 0) {
            $datos_existentes = $result->fetch_assoc();
        }
    } else {
        $mensaje = "❌ Error al guardar: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil Fiscal</title>
    <style>
        
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #333;
        }
        input[type="text"], input[type="password"], select, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        .mensaje {
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
            color: green;
        }
        .logo-preview {
            margin-top: 10px;
            text-align: center;
        }
        .logo-preview img {
            max-width: 150px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="principal">
    <div>
    <h1 class="text-2xl font-bold text-blue-600">Perfil Fiscal</h1>
    <?php if (!empty($mensaje)) echo "<div class='mensaje'>$mensaje</div>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="logo">Logo (opcional):</label>
        <input type="file" name="logo" accept="image/png">
        <?php if (!empty($datos_existentes['logo'])): ?>
            <div class="logo-preview">
                <img src="<?= htmlspecialchars($datos_existentes['logo']) ?>" alt="Logo Actual">
            </div>
        <?php endif; ?>

        <label for="rfc">RFC:</label>
        <input type="text" name="rfc" required maxlength="13" value="<?= htmlspecialchars($datos_existentes['rfc'] ?? '') ?>">

        <label for="razon_social">Razón Social:</label>
        <input type="text" name="razon_social" required value="<?= htmlspecialchars($datos_existentes['razon_social'] ?? '') ?>">

        <label for="direccion">Direccion:</label>
        <input type="text" name="direccion" required value="<?= htmlspecialchars($datos_existentes['direccion'] ?? '') ?>">

        <label for="telefono">Telefono:</label>
        <input type="text" name="telefono" required value="<?= htmlspecialchars($datos_existentes['telefono'] ?? '') ?>">

        <label for="correo">Correo:</label>
        <input type="text" name="correo" required value="<?= htmlspecialchars($datos_existentes['correo'] ?? '') ?>">

        <label for="codigo_postal">Codigo postal:</label>
        <input type="text" name="codigo_postal" required value="<?= htmlspecialchars($datos_existentes['codigo_postal'] ?? '') ?>">

        <label for="registro_patronal">Registro Patronal:</label>
        <input type="text" name="registro_patronal" required value="<?= htmlspecialchars($datos_existentes['registro_patronal'] ?? '') ?>">

        <label for="curp">CURP:</label>
        <input type="text" name="curp" maxlength="18" value="<?= htmlspecialchars($datos_existentes['CURP'] ?? '') ?>">

        <label for="regimen_fiscal">Régimen Fiscal:</label>
        <select name="regimen_fiscal" required>
            <option value="">-- Selecciona --</option>
            <?php
            $regimen_actual = $datos_existentes['regimen_fiscal'] ?? '';
            if ($regimen_actual) {
                $res = $conexion_servicios->query("SELECT Descripcion FROM RegimenFiscal WHERE Clave = '$regimen_actual' LIMIT 1");
                if ($res->num_rows > 0) {
                    $fila = $res->fetch_assoc();
                    echo "<option value='$regimen_actual' selected>" . htmlspecialchars($fila['Descripcion']) . "</option>";
                }
            }
            $regimenes="select Clave,Descripcion from RegimenFiscal";
            $result=mysqli_query($conexion_servicios,$regimenes);
            while($row=mysqli_fetch_array($result)){
                echo "<option value='".$row['Clave']."'>".$row['Descripcion']."</option>";
            }
            ?>
        </select>

        <label for="cer">Certificado (.cer):</label>
        <input type="file" name="cer" accept=".cer">
        <?php if (!empty($datos_existentes['ruta_cer'])): ?>
            <small>Archivo actual: <?= basename($datos_existentes['ruta_cer']) ?></small>
        <?php endif; ?>

        <label for="key">Llave privada (.key):</label>
        <input type="file" name="key" accept=".key">
        <?php if (!empty($datos_existentes['ruta_key'])): ?>
            <small>Archivo actual: <?= basename($datos_existentes['ruta_key']) ?></small>
        <?php endif; ?>


        <label for="password_key">Contraseña del archivo .key:</label>
        <input type="password" name="password_key" required value="<?= htmlspecialchars($datos_existentes['password_key'] ?? '') ?>">

        <button type="submit" style="margin-top:20px; padding: 10px 20px; width:100%; background-color: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
    <?= $datos_existentes ? 'Actualizar Perfil' : 'Guardar Perfil Fiscal' ?>
</button>

    </form>
        </div>
</div>
</body>
</html>
