<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
</head>
<body>
    <h1>Registro de Asistencia</h1>
    
    <!-- Video en vivo desde la cámara -->
    <video id="video" autoplay playsinline></video>
    <button id="capture">Capturar Rostro y Ubicación</button>
    <canvas id="canvas" style="display:none;"></canvas>
    
    <img id="preview" style="display:none; width: 100%; max-width: 400px; border: 2px solid #000;">
    
    <p id="location">Ubicación: No disponible</p>
    
    <form id="attendanceForm" method="POST" action="">
        <input type="hidden" name="image" id="imageInput">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="submit">Registrar Asistencia</button>
    </form>
</body>
</html>

<style>
body {
    font-family: Arial, sans-serif;
    text-align: center;
    margin: 20px;
}
video {
    width: 100%;
    max-width: 400px;
    border: 2px solid #000;
}
button {
    margin-top: 10px;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const captureButton = document.getElementById("capture");
    const imageInput = document.getElementById("imageInput");
    const preview = document.getElementById("preview");
    const locationText = document.getElementById("location");
    const latitudeInput = document.getElementById("latitude");
    const longitudeInput = document.getElementById("longitude");

    let capturing = false; // Variable para rastrear el estado de captura

    // Acceder a la cámara y mostrar video en vivo
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.style.display = "block"; // Asegúrate de que el video sea visible
        })
        .catch(error => {
            console.error("Error accediendo a la cámara:", error);
            alert("No se pudo acceder a la cámara. Asegúrate de permitir el acceso.");
        });

    // Captura de imagen y ubicación en un solo botón
    captureButton.addEventListener("click", () => {
    if (capturing) {
        // Si ya estamos en el proceso de captura, reiniciamos
        locationText.textContent = "Ubicación: No disponible";
        preview.style.display = "none";
        capturing = false;
        captureButton.textContent = "Capturar Rostro y Ubicación"; // Restablecer el texto del botón
    } else {
        // Iniciar el proceso de captura de imagen y ubicación
        const context = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Voltear la imagen horizontalmente
        context.translate(canvas.width, 0);  // Mover el origen a la parte derecha
        context.scale(-1, 1);  // Voltear horizontalmente

        // Captura la imagen actual del video (ahora voltea la imagen)
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convertir la imagen a base64
        const imageData = canvas.toDataURL("image/png");
        imageInput.value = imageData;

        // Mostrar la previsualización
        preview.src = imageData;
        preview.style.display = "block";

        // Obtener la ubicación del usuario
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const { latitude, longitude } = position.coords;
                locationText.textContent = `Ubicación: ${latitude}, ${longitude}`;
                latitudeInput.value = latitude;
                longitudeInput.value = longitude;
                capturing = true; // Cambiar el estado a capturando
                captureButton.textContent = "Retomar Captura"; // Cambiar el texto del botón
            }, error => {
                locationText.textContent = "No se pudo obtener la ubicación";
            });
        } else {
            locationText.textContent = "Geolocalización no soportada";
        }
    }
});

});
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados por el formulario
    $image = $_POST['image'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $timestamp = date('Y-m-d H:i:s');
    
    // Decodificar la imagen base64
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
    // Obtener la fecha del día en formato 'Y-m-d' (por ejemplo, 2025-03-21)
    $today = date('Y-m-d');

    // Crear el directorio con la fecha del día si no existe
    $dirPath = '../captures/' . $today;
    if (!file_exists($dirPath)) {
        mkdir($dirPath, 0777, true); // Crea la carpeta con permisos de lectura, escritura y ejecución
    }

    // Crear el nombre de la imagen basado en la hora, minutos y segundos (por ejemplo, 15:45:30)
    $filename = $dirPath . '/' . date('H:i:s') . '.png';

    // Decodificar la imagen base64 y guardarla en el archivo
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
    
    // Guardar la imagen en el servidor
    file_put_contents($filename, $data);
    
    $stmt = $conexion->prepare("INSERT INTO asistencia ( latitud, longitud, fecha) VALUES ( ?, ?, ?)");
    $stmt->bind_param("dds", $latitude, $longitude, $timestamp);
    $stmt->execute();
    
    echo "Asistencia registrada correctamente";
    
    // Cerrar la conexión
    $stmt->close();
    $conexion->close();
}
?> 

