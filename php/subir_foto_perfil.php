<?php
header('Content-Type: application/json');

// Verificar que se haya enviado el archivo, la carpeta y el ID
if (!isset($_FILES['foto_perfil']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    exit;
}

$carpeta = basename(str_replace('%20', ' ', $_POST['carpeta']));
$id = intval($_POST['id']); // Sanitizar id

$directorioDestino = "/var/www/transimex/documentos/RecursosHumanos/trabajadores/" . $carpeta;
$directorioAlterno = "/var/www/transimex/documentos/RecursosHumanos/fotos_trabajadores";

// Crear carpetas si no existen
if (!is_dir($directorioDestino)) {
    mkdir($directorioDestino, 0755, true);
}
if (!is_dir($directorioAlterno)) {
    mkdir($directorioAlterno, 0755, true);
}

// Validar archivo
$archivo = $_FILES['foto_perfil'];
$nombreTmp = $archivo['tmp_name'];
$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($ext, $extensionesPermitidas)) {
    echo json_encode(['success' => false, 'error' => 'Formato de imagen no permitido.']);
    exit;
}

// Ruta original y copia
$rutaFinal = $directorioDestino . '/perfil.png';
$rutaCopia = $directorioAlterno . '/' . $id . '.png';

// Eliminar si existen
if (file_exists($rutaFinal)) {
    unlink($rutaFinal);
}
if (file_exists($rutaCopia)) {
    unlink($rutaCopia);
}

// Guardar archivo
if (move_uploaded_file($nombreTmp, $rutaFinal)) {
    // Copiar a nueva ubicación con nombre por ID
    copy($rutaFinal, $rutaCopia);
    
    // Puedes redirigir o responder como gustes
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar la imagen.']);
}

