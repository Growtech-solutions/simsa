<?php

$id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : 0;

// AGREGAR o EDITAR
if (isset($_POST['action']) && $_POST['action'] === 'save') {
    $id = intval($_POST['id']);
    $no_item = intval($_POST['no_item']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $unidad = $conexion->real_escape_string($_POST['unidad']);
    $precio = floatval($_POST['precio']);

    if ($id > 0) {
        $conexion->query("UPDATE precios SET no_item=$no_item, descripcion='$descripcion', unidad='$unidad', precio=$precio WHERE id=$id");
    } else {
        $conexion->query("INSERT INTO precios(no_item,id_pedido,descripcion,unidad,precio) VALUES ($no_item,$id_pedido,'$descripcion','$unidad',$precio)");
    }
}

// ELIMINAR
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $conexion->query("DELETE FROM precios WHERE id=$id");
}

// LISTADO
$res = $conexion->query("SELECT * FROM precios WHERE id_pedido=$id_pedido ORDER BY no_item ASC");
$rows = [];
while($r = $res->fetch_assoc()) $rows[] = $r;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<title>Precios</title>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl p-6">
    <h1 class="text-2xl font-bold text-blue-600">Precios del Pedido #<?php echo $id_pedido; ?></h1>
    <br>

    <!-- FORMULARIO -->
    <form method="POST" class="grid grid-cols-5 gap-4 bg-gray-50 p-4 rounded-lg mb-6">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="action" value="save">

        <input name="no_item" id="no_item" type="number" placeholder="No." class="col-span-1 border p-2 rounded">
        <input name="descripcion" id="descripcion" type="text" placeholder="Descripción" class="col-span-2 border p-2 rounded">
        <input name="unidad" id="unidad" type="text" placeholder="Unidad" class="col-span-1 border p-2 rounded">
        <input name="precio" id="precio" type="number" step="0.01" placeholder="Precio" class="col-span-1 border p-2 rounded">

        <button class="col-span-5 bg-green-600 text-white py-2 rounded hover:bg-green-700">Guardar</button>
    </form>

    <!-- TABLA -->
    <table class="w-full text-left border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">No</th>
                <th class="p-2">Descripción</th>
                <th class="p-2">Unidad</th>
                <th class="p-2">Precio</th>
                <th class="p-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $r): ?>
            <tr class="border-b hover:bg-gray-50">
                <td class="p-2"><?php echo $r['no_item']; ?></td>
                <td class="p-2"><?php echo $r['descripcion']; ?></td>
                <td class="p-2"><?php echo $r['unidad']; ?></td>
                <td class="p-2">$<?php echo $r['precio']; ?></td>
                <td class="p-2 flex gap-2">
                    <button onclick="editar(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="px-3 py-1 bg-blue-600 text-white rounded">Editar</button>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                        <input type="hidden" name="action" value="delete">
                        <button class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('¿Eliminar?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function editar(data){
    document.getElementById('id').value = data.id;
    document.getElementById('no_item').value = data.no_item;
    document.getElementById('descripcion').value = data.descripcion;
    document.getElementById('unidad').value = data.unidad;
    document.getElementById('precio').value = data.precio;
}
</script>
</body>
</html>
