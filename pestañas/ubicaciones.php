<?php
// Consultar ubicaciones
$ubicaciones = $conexion_transimex->query("SELECT * FROM ubicaciones ORDER BY id DESC");
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar conexión a base de datos
if ($conexion_transimex->connect_error) {
    die("Error de conexión: " . $conexion_transimex->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ubicaciones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        #map { height: 400px; width: 100%; margin-bottom: 20px; }
        #pac-input { margin-top: 10px; }
        a { color: #333; text-decoration: none; }
    </style>
</head>
<body class="p-4">

<div class="principal">
    <div>
    <h1 class="text-2xl font-bold text-blue-600">Alta de Ubicaciones</h1>

    <!-- Formulario -->
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nombre de la ubicación</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Buscar dirección</label>
            <input id="pac-input" class="form-control" type="text" placeholder="Escribe una dirección">
        </div>

        <div id="map"></div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Latitud</label>
                <input type="text" name="latitud" id="latitud" class="form-control" readonly required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitud</label>
                <input type="text" name="longitud" id="longitud" class="form-control" readonly required>
            </div>
        </div>

        <button class="btn btn-primary mt-3" name="guardar_ubicacion">Guardar ubicación</button>
    </form>

    <hr class="my-4">

    <!-- Tabla de ubicaciones -->
    <h3>Ubicaciones Registradas</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Latitud</th>
                <th>Longitud</th>
                <th>Mapa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ubicaciones as $u): ?>
                <tr>
                    <td><?= $u["id"] ?></td>
                    <td><?= htmlspecialchars($u["nombre"]) ?></td>
                    <td><?= $u["latitud"] ?></td>
                    <td><?= $u["longitud"] ?></td>
                    <td>
                        <a href="https://www.google.com/maps?q=<?= $u["latitud"] ?>,<?= $u["longitud"] ?>" target="_blank" class="btn btn-sm btn-info">
                            Ver en Maps
                        </a>
                    </td>
                    <td>
                        <a href="?eliminar_ubicacion=<?= $u["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que quieres eliminar esta ubicación?')">
                            Eliminar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDPIypsQ-tjN30LQR8eyBU4tOXtzYZTsOk&callback=initMap&libraries=places" async defer></script>
<script>
function initMap() {
    const defaultPos = { lat: 19.432608, lng: -99.133209 }; // CDMX por defecto

    // Crear mapa
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: defaultPos
    });

    // Crear marcador
    const marker = new google.maps.Marker({
        position: defaultPos,
        map: map,
        draggable: true
    });

    // Inicializar valores
    document.getElementById("latitud").value = defaultPos.lat;
    document.getElementById("longitud").value = defaultPos.lng;

    // Arrastrar marcador
    marker.addListener("dragend", function () {
        const pos = marker.getPosition();
        document.getElementById("latitud").value = pos.lat().toFixed(6);
        document.getElementById("longitud").value = pos.lng().toFixed(6);
    });

    // Click en el mapa
    map.addListener("click", function (event) {
        marker.setPosition(event.latLng);
        document.getElementById("latitud").value = event.latLng.lat().toFixed(6);
        document.getElementById("longitud").value = event.latLng.lng().toFixed(6);
    });

    // Campo de búsqueda
    const input = document.getElementById("pac-input");
    const searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Cambiar mapa y marcador al seleccionar una dirección
    searchBox.addListener("places_changed", function() {
        const places = searchBox.getPlaces();
        if (places.length === 0) return;

        const place = places[0];
        if (!place.geometry) return;

        // Centrar el mapa
        map.setCenter(place.geometry.location);
        map.setZoom(15);

        // Mover marcador
        marker.setPosition(place.geometry.location);

        // Actualizar lat/long
        document.getElementById("latitud").value = place.geometry.location.lat().toFixed(6);
        document.getElementById("longitud").value = place.geometry.location.lng().toFixed(6);
    });
}
</script>
</div>
</body>
</html>
