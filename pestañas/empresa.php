<?php
require_once '../conexion.php'; // Ajusta el path si es necesario

// Obtener datos de la empresa
$query = "SELECT razon_social, nombre_compania, registro_patronal, rfc FROM empresa LIMIT 1";
$result = $conexion->query($query);
$empresa = $result ? $result->fetch_assoc() : null;

$logoPath = '../img/logo.png';
$logoExists = file_exists($logoPath) && filesize($logoPath) > 0;
?>
<style>
    .empresa-form-container {
        max-width: 400px;
        margin: 30px auto;
        padding: 24px 32px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        font-family: Arial, sans-serif;
    }
    .empresa-form-container label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
        color: #333;
    }
    .empresa-form-container input[type="text"],
    .empresa-form-container input[type="file"] {
        width: 100%;
        padding: 7px 10px;
        margin-bottom: 16px;
        border: 1px solid #bbb;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .empresa-form-container button[type="submit"] {
        background: #1976d2;
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
        transition: background 0.2s;
    }
    .empresa-form-container button[type="submit"]:hover {
        background: #125ea2;
    }
    .empresa-form-container img {
        display: block;
        margin: 0 auto 16px auto;
        border-radius: 6px;
        border: 1px solid #eee;
        background: #fafafa;
        padding: 4px;
    }
</style>
<div class="empresa-form-container">
<?php
if (isset($_GET['confirmacion'])) {
    $confirmacion = htmlspecialchars($_GET['confirmacion']);
    echo "<div class='confirmacion'>$confirmacion</div>";
}
?>
<form method="post" action="../php/guardar_empresa.php" enctype="multipart/form-data">
    <div>
        <?php if ($logoExists): ?>
            <img src="<?= htmlspecialchars($logoPath) ?>" alt="Logo" style="max-width:150px;max-height:150px;">
        <?php else: ?>
            <label for="logo">Subir logo:</label>
            <input type="file" name="logo" id="logo" accept="image/*" onchange="previewLogo(event)">
            <img id="logoPreview" style="display:none;max-width:150px;max-height:150px;">
            <script>
                function previewLogo(event) {
                    const [file] = event.target.files;
                    if (file) {
                        const preview = document.getElementById('logoPreview');
                        preview.src = URL.createObjectURL(file);
                        preview.style.display = 'block';
                    }
                }
            </script>
        <?php endif; ?>
    </div>
    <div>
        <label for="razon_social">Razón Social:</label>
        <input type="text" name="razon_social" id="razon_social" value="<?= htmlspecialchars($empresa['razon_social'] ?? '') ?>">
    </div>
    <div>
        <label for="nombre_empresa">Nombre compañia:</label>
        <input type="text" name="nombre_empresa" id="nombre_empresa" value="<?= htmlspecialchars($empresa['nombre_compania'] ?? '') ?>">
    </div>
    <div>
        <label for="registro_patronal">Registro Patronal:</label>
        <input type="text" name="registro_patronal" id="registro_patronal" value="<?= htmlspecialchars($empresa['registro_patronal'] ?? '') ?>">
    </div>
    <div>
        <label for="rfc">RFC:</label>
        <input type="text" name="rfc" id="rfc" value="<?= htmlspecialchars($empresa['rfc'] ?? '') ?>">
    </div>
    <div>
        <label>Archivo .cer:</label>
        <input type="file" name="archivo_cer" accept=".cer" >
    </div>
    <div>
        <label>Archivo .key:</label>
        <input type="file" name="archivo_key" accept=".key" >
    </div>
    <button type="submit">Guardar</button>
</form>
</div>