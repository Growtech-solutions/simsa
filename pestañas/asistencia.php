<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
</head>
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
<body>
    <h1>Registro de Asistencia</h1>
    
    <video id="video" autoplay playsinline></video>
    <button id="capture">Capturar Rostro y Ubicación</button>
    <canvas id="canvas" style="display:none;"></canvas>

    <p id="workerText">Trabajador: No identificado</p>
    <p id="location">Ubicación: No disponible</p>

    <script>
document.addEventListener("DOMContentLoaded", () => {
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const captureButton = document.getElementById("capture");
    const workerText = document.getElementById("workerText");
    const locationText = document.getElementById("location");

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => video.srcObject = stream)
        .catch(error => alert("No se pudo acceder a la cámara."));

    captureButton.addEventListener("click", () => {
        // Deshabilitar el botón mientras se procesa la solicitud
        captureButton.disabled = true;
        captureButton.innerHTML = "Procesando...";

        const context = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL("image/png");

        navigator.geolocation.getCurrentPosition(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            fetch("../php/procesar_reconocimiento.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `image=${encodeURIComponent(imageData)}&latitude=${latitude}&longitude=${longitude}`
            })
            .then(response => response.json())
            .then(data => {
                // Procesar la respuesta del servidor
                // Mostrar la ubicación en el HTML
            locationText.innerHTML = `Ubicación: ${data.ubicacion}`
                if (data.worker_name) {
                    workerText.innerHTML = `<h2> ${data.worker_name}</h2>`;
                } else {
                    workerText.innerHTML = `<h2>No identificado</h2>`;
                }

                // Habilitar el botón nuevamente
                captureButton.disabled = false;
                captureButton.innerHTML = "Capturar Rostro y Ubicación";
            })
            .catch(error => {
                console.error("Error:", error);
                // Habilitar el botón en caso de error
                captureButton.disabled = false;
                captureButton.innerHTML = "Capturar Rostro y Ubicación";
            });
        });
    });
});
</script>
</body>
</html>
