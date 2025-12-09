<?php
header('Content-Type: application/json');

// Verificar parámetros obligatorios
if (!isset($_FILES['foto_perfil']) || !isset($_POST['id']) || !isset($_POST['carpeta'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    exit;
}

$id = intval($_POST['id']);

// Limpiar carpeta para evitar inyecciones tipo ../
$carpeta = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $_POST['carpeta']);
$carpeta = trim(str_replace('%20', ' ', $carpeta));

if ($carpeta === "") {
    echo json_encode(['success' => false, 'error' => 'Carpeta inválida.']);
    exit;
}

$directorioDestino = "/var/www/transimex/documentos/RecursosHumanos/trabajadores/" . $carpeta;
$directorioAlterno = "/var/www/transimex/documentos/RecursosHumanos/fotos_trabajadores";

// Crear carpetas si no existen
if (!is_dir($directorioDestino)) mkdir($directorioDestino, 0755, true);
if (!is_dir($directorioAlterno)) mkdir($directorioAlterno, 0755, true);

// Archivo recibido
$archivo = $_FILES['foto_perfil'];
$nombreTmp = $archivo['tmp_name'];

if (!is_uploaded_file($nombreTmp)) {
    echo json_encode(['success' => false, 'error' => 'Archivo inválido o demasiado grande.']);
    exit;
}

$ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
$extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($ext, $extensionesPermitidas)) {
    echo json_encode(['success' => false, 'error' => 'Formato de imagen no permitido.']);
    exit;
}

// Guardaremos todo como PNG final
$rutaFinal = $directorioDestino . "/perfil.png";
$rutaCopia = $directorioAlterno . "/$id.png";

// Eliminar versiones previas
@unlink($rutaFinal);
@unlink($rutaCopia);

// Mover archivo temporal a destino
if (!move_uploaded_file($nombreTmp, $rutaFinal)) {
    echo json_encode(['success' => false, 'error' => 'Error al guardar la imagen.']);
    exit;
}

// Crear copia
if (!copy($rutaFinal, $rutaCopia)) {
    echo json_encode(['success' => false, 'error' => 'No se pudo copiar la imagen.']);
    exit;
}

// Ejecutar script Python para generar codificaciones
$scriptPython = "/var/www/transimex/CRM/asistencia/generar_codificaciones.py";
if (file_exists($scriptPython)) {
    exec("python3 " . escapeshellarg($scriptPython) . " 2>&1", $output, $returnVar);
    if ($returnVar !== 0) {
        error_log("Error ejecutando generar_codificaciones.py: " . implode("\n", $output));
    }
}

// Todo bien → redirigir
echo json_encode(['success' => true]);



// Si existe referer, redirige
if (!headers_sent() && isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
exit;

?>
