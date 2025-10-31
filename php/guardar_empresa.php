<?php
session_start();
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar y sanitizar entradas
$razon_social = trim($_POST['razon_social'] ?? '');
$registro_patronal = trim($_POST['registro_patronal'] ?? '');
$rfc = strtoupper(trim($_POST['rfc'] ?? ''));
$nombre_compania = trim($_POST['nombre_empresa'] ?? 'p');

// Sanear RFC (solo letras y números)
$rfc = preg_replace('/[^A-Z0-9]/i', '', $rfc);

// Guardar en base de datos (INSERT o UPDATE)
$query = "INSERT INTO empresa (razon_social, registro_patronal, rfc, nombre_compania) 
          VALUES (?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE 
              razon_social = VALUES(razon_social),
              registro_patronal = VALUES(registro_patronal),
              nombre_compania = VALUES(nombre_compania)";

$stmt = $conexion->prepare($query);
if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}
$stmt->bind_param("ssss", $razon_social, $registro_patronal, $rfc, $nombre_compania);

if (!$stmt->execute()) {
    die("Error al guardar los datos de la empresa: " . $stmt->error);
}
$stmt->close();

// Crear carpeta para el RFC
$dirDestino = "/var/secure_csd/$rfc/";

if (!is_dir($dirDestino)) {
    if (!mkdir($dirDestino, 0700, true)) {
        die("Error: No se pudo crear la carpeta destino.");
    }
    chown($dirDestino, 'www-data');
    chgrp($dirDestino, 'www-data');
    chmod($dirDestino, 0700);
}

// Verifica permisos
if (!is_writable($dirDestino)) {
    die("Error: La carpeta destino no tiene permisos de escritura.");
}

// Subir archivo .cer
if (isset($_FILES['archivo_cer']) && $_FILES['archivo_cer']['error'] === UPLOAD_ERR_OK) {
    $tmpCer = $_FILES['archivo_cer']['tmp_name'];
    $nombreCer = basename($_FILES['archivo_cer']['name']);
    $extCer = strtolower(pathinfo($nombreCer, PATHINFO_EXTENSION));
    if ($extCer !== 'cer') {
        die("El archivo .cer debe tener extensión .cer");
    }
    $destinoCer = $dirDestino . $nombreCer;
    if (!move_uploaded_file($tmpCer, $destinoCer)) {
        die("Error al mover el archivo .cer");
    }
}

// Subir archivo .key
if (isset($_FILES['archivo_key']) && $_FILES['archivo_key']['error'] === UPLOAD_ERR_OK) {
    $tmpKey = $_FILES['archivo_key']['tmp_name'];
    $nombreKey = basename($_FILES['archivo_key']['name']);
    $extKey = strtolower(pathinfo($nombreKey, PATHINFO_EXTENSION));
    if ($extKey !== 'key') {
        die("El archivo .key debe tener extensión .key");
    }
    $destinoKey = $dirDestino . $nombreKey;
    if (!move_uploaded_file($tmpKey, $destinoKey)) {
        die("Error al mover el archivo .key");
    }
}

// Subir logo si se proporcionó
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $tmpLogo = $_FILES['logo']['tmp_name'];
    $extLogo = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
    $destinoLogo = "../img/logo.png"; // Puedes cambiar el nombre si lo prefieres

    if (!move_uploaded_file($tmpLogo, $destinoLogo)) {
        die("Error al guardar el logo.");
    }
}

$confirmacion= "Empresa guardada y archivos subidos correctamente.";
header("Location: " . $_SERVER['HTTP_REFERER'] . "&confirmacion=" . urlencode($confirmacion));
?>
