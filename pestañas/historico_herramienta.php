<?php
$consumible = $_GET['consumible'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$descripcion = $_GET['descripcion'] ?? '';
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Construcción de la consulta con filtros
$sql = "SELECT historial_consumibles.*, consumibles.nombre 
        FROM historial_consumibles 
        LEFT JOIN consumibles ON historial_consumibles.id_consumible = consumibles.id 
        WHERE 1";

if (!empty($consumible)) {
    $sql .= " AND consumible = '" . $conexion->real_escape_string($consumible) . "'";
}
if (!empty($tipo)) {
    $sql .= " AND tipo = '" . $conexion->real_escape_string($tipo) . "'";
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql .= " AND fecha BETWEEN '" . $conexion->real_escape_string($fecha_inicio) . "' AND '" . $conexion->real_escape_string($fecha_fin) . "'";
}
if (!empty($descripcion)) {
    $sql .= " AND descripcion LIKE '%" . $conexion->real_escape_string($descripcion) . "%'";
}
$sql .= " ORDER BY fecha DESC LIMIT $registros_por_pagina OFFSET $offset";

$result = $conexion->query($sql);

// Obtener el total de registros para la paginación
$sql_total = "SELECT COUNT(*) as total FROM historial_consumibles WHERE 1";
$result_total = $conexion->query($sql_total);
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
?>
<head>
    <title>Historial de Consumibles</title>
</head>

<body>
    <div class="principal">
        <section>
    <style>
        body {
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
            color: #444;
        }

        .principal {
            width: 90%;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .buscador {
            text-align: center;
            margin-bottom: 20px;
        }

        .reporte_formulario {
            display: inline-block;
            text-align: left;
        }

        .reporte_formulario label {
            font-size: 14px;
            margin-right: 10px;
        }

        .reporte_formulario input[type="text"],
        .reporte_formulario select {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .reporte_formulario input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .reporte_formulario input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #eaeaea;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        th {
            background-color: #f7f7f7;
            color: #333;
        }

        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        tbody tr:nth-child(even) {
            background-color: #fff;
        }

        .centrado {
            text-align: center;
            margin-top: 20px;
        }

        .paginacion {
            margin-top: 20px;
            text-align: center;
        }

        .paginacion a, .paginacion span {
            margin: 0 5px;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #007bff;
        }

        .paginacion span {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .paginacion a:hover {
            background-color: #eaeaea;
        }

        .registros-por-pagina {
            text-align: center;
            margin-bottom: 20px;
        }

        .registros-por-pagina select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="number"] {
            padding: 8px;
            width: 80px; /* Ancho ajustado para el campo de cantidad */
            border: none; /* Sin bordes */
            background-color: transparent; /* Fondo transparente */
            font-size: 14px;
            color: #333; /* Color del texto */
        }
        input[type="date"] {
            padding: 8px;
            
            border: none; /* Sin bordes */
            background-color: transparent; /* Fondo transparente */
            font-size: 14px;
            color: #333; /* Color del texto */
        }

        input[type="number"]:focus {
            outline: none; /* Sin contorno al enfocar */
        }
        label{
            padding-left:1rem;
        }
        
    </style>
    <h2>Historial de Consumibles</h2>
    <form class="reporte_formulario" method="GET">
        <input class="formulario_reporte_ot" type="text" name="consumible" placeholder="ID Consumible" value="<?= htmlspecialchars($consumible) ?>">
        <select class="formulario_reporte_ot" name="tipo">
            <option value="">Todos</option>
            <option value="entrada" <?= $tipo == 'entrada' ? 'selected' : '' ?>>Entrada</option>
            <option value="salida" <?= $tipo == 'salida' ? 'selected' : '' ?>>Salida</option>
        </select>
        <input class="formulario_reporte_ot" type="date" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>">
        <input class="formulario_reporte_ot" type="date" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>">
        <input class="formulario_reporte_ot" type="text" name="descripcion" placeholder="Descripción" value="<?= htmlspecialchars($descripcion) ?>">
        <input class="formulario_reporte_ot" type="hidden" name="pestaña" value="historico_herramienta">
        <input type="submit" value="Buscar">
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Consumible</th>
                <th>Cambio</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['consumible']) ?></td>
                    <td><?= htmlspecialchars($row['cambio']) ?></td>
                    <td><?= htmlspecialchars($row['tipo']) ?></td>
                    <td><?= htmlspecialchars($row['fecha']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="?pestaña=historico_herramienta&pagina=<?= $i ?>&consumible=<?= urlencode($consumible) ?>&tipo=<?= urlencode($tipo) ?>&fecha_inicio=<?= urlencode($fecha_inicio) ?>&fecha_fin=<?= urlencode($fecha_fin) ?>&descripcion=<?= urlencode($descripcion) ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    </section>
    </div>
</body>
</html>
