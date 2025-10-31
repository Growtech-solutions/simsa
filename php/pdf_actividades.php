<?php
// Incluir la librería FPDF
require('../../recursos/PDF/fpdf.php');
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los filtros desde la URL
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d', strtotime('monday this week'));
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d', strtotime('sunday this week'));
$folio = isset($_GET['folio']) ? $_GET['folio'] : '';
$ot = isset($_GET['ot']) ? $_GET['ot'] : '';

// Obtener información del OT y el pedido
$sql_ot = "SELECT ot.*,pedido.*, pedido.descripcion AS pedido_desc
           FROM ot 
           LEFT JOIN pedido ON pedido.id = ot.id_pedido
           WHERE ot = '$ot'";
$result_ot = $conexion->query($sql_ot);
$datos_ot = $result_ot->fetch_assoc();

// Construir la consulta SQL con agrupación por pieza
$sql = "SELECT 
            piezas.pieza AS nombre_pieza,
            encargado.actividad,
            encargado.cantidad,
            encargado.fecha,
            trabajadores.nombre,
            trabajadores.apellidos,
            precios.precio,
            precios.descripcion,
            precios.unidad,
            precios.no_item
        FROM piezas 
        RIGHT JOIN encargado ON piezas.id = encargado.id_pieza 
        LEFT JOIN transimex.trabajadores ON encargado.id_trabajador = trabajadores.id
        LEFT JOIN precios ON encargado.actividad = precios.id
        WHERE encargado.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

if (!empty($area)) {
    $sql .= " AND piezas.area LIKE '%$area%'";
}
if (!empty($ot)) {
    $sql .= " AND (piezas.ot LIKE '%$ot%' OR encargado.ot_tardia LIKE '%$ot%')";
}

$sql .= " ORDER BY piezas.pieza, encargado.fecha ASC";

$resultado = $conexion->query($sql);

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Image('../img/logo.png', 10, 8, 30); // Logo en la esquina izquierda

// Posicionar la información principal en el centro
$pdf->SetY(10);
$pdf->SetX(50); // Mueve el inicio del texto después de la imagen

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(110, 6, 'Suministros Industriales Modernos', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->SetX(50);
$pdf->Cell(110, 6, "Fecha de Reporte: " . date('d/m/Y'), 0, 1, 'C');

$pdf->SetX(50);
$pdf->Cell(110, 6, "Periodo: " . date('d/m/Y', strtotime($fecha_inicio)) . " al " . date('d/m/Y', strtotime($fecha_fin)), 0, 1, 'C');

// **Alinear datos de la OT a la derecha**
$pdf->SetY(4); // Mantener la misma altura que la imagen
$pdf->SetX(140); // Alinear a la derecha
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(60, 6, utf8_decode('Remision: ' . ($folio ?? 'No especificado')), 0, 1, 'R');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetY(10); // Mantener la misma altura que la imagen
$pdf->SetX(140); // Alinear a la derecha
$pdf->Cell(60, 6, utf8_decode('Pedido: ' . ($datos_ot['pedido_desc'] ?? 'No especificado')), 0, 1, 'R');
$pdf->SetX(140);
$pdf->Cell(60, 6, utf8_decode('Cliente: ' . ($datos_ot['cliente'] ?? 'No especificado')), 0, 1, 'R');
$pdf->SetX(140);
$pdf->Cell(60, 6, utf8_decode('Planta: ' . ($datos_ot['planta'] ?? 'No especificado')), 0, 1, 'R');

$pdf->Ln(10); // Espaciado adicional

// Variables para el agrupamiento
$pieza_actual = '';
$valor_acumulado_pieza = 0;
$valor_acumulado_total = 0;

$pdf->SetFont('Arial', 'B', 12);

while ($fila = $resultado->fetch_assoc()) {
    $pieza = $fila['nombre_pieza'] ?? 'Sin Pieza';

    if ($pieza_actual !== $pieza) {
        if ($pieza_actual !== '') {
            // Mostrar total acumulado de la pieza anterior
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(10, 7, '', 0, 0);
            $pdf->Cell(120, 7, 'Subtotal :', 1, 0, 'R');
            $pdf->Cell(40, 7, '$' . number_format($valor_acumulado_pieza, 2), 1, 1, 'C');
            $pdf->Ln(5);
        }

        // Nuevo encabezado de pieza alineado a la izquierda
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 7, '', 0, 0); // Margen izquierdo pequeño
        // Usar MultiCell para el nombre de la pieza, alineado a la izquierda
        $pdf->SetX(20); // Margen izquierdo pequeño (10 + 10)
        $pdf->MultiCell(160, 7, utf8_decode($pieza), 1, 'C');

        // Encabezado de la tabla alineado a la izquierda
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 7, '', 0, 0); // Margen izquierdo pequeño
        $pdf->Cell(30, 7, 'Fecha', 1, 0, 'C');
        $pdf->Cell(20, 7, 'Item', 1, 0, 'C');
        $pdf->Cell(60, 7, 'Actividad', 1, 0, 'C');
        $pdf->Cell(20, 7, 'Cantidad', 1, 0, 'C');
        $pdf->Cell(30, 7, 'Valor unitario', 1, 1, 'C');

        // Reiniciar subtotal de la pieza
        $valor_acumulado_pieza = 0;
        $pieza_actual = $pieza;
    }

    $nombre_trabajador = $fila['nombre'] . ' ' . $fila['apellidos'];
    $actividad = $fila['descripcion'] ?? 'No especificado';
    $item = $fila['no_item'] ?? '0';
    $cantidad = $fila['cantidad']. ' ' . $fila['unidad'];
    $precio = $fila['precio'] ?? 0;
    $valor_total = $fila['cantidad'] * $precio;

    // Acumular valores
    $valor_acumulado_pieza += $valor_total;
    $valor_acumulado_total += $valor_total;

    // Determinar la altura necesaria para la celda de "Actividad"
    $pdf->SetFont('Arial', '', 10);
    $altura_fila = 6; // Altura mínima de la fila
    $ancho_actividad = 60; // Ancho de la celda de actividad
    $lineas_actividad = $pdf->GetStringWidth($actividad) / $ancho_actividad;
    $lineas_actividad = ceil($lineas_actividad); // Redondear hacia arriba
    $altura_fila = max(6, $lineas_actividad * 6); // Ajustar altura de fila

    // Agregar celdas con la altura ajustada
    $pdf->Cell(10, $altura_fila, '', 0, 0);
    $pdf->Cell(30, $altura_fila, $fila['fecha'], 1, 0, 'C');

    $pdf->Cell(20, $altura_fila, $item, 1, 0, 'C');
    $pdf->SetFont('Arial', '', 10);

    // MultiCell para la actividad con el mismo ancho y altura dinámica
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell($ancho_actividad, 6, utf8_decode($actividad), 1, 'C');
    $pdf->SetXY($x + $ancho_actividad, $y);

    $pdf->Cell(20, $altura_fila, $cantidad, 1, 0, 'C');
    $pdf->Cell(30, $altura_fila, '$' . number_format($precio, 2), 1, 1, 'C');
}

// Mostrar subtotal de la última pieza
if ($pieza_actual !== '') {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 7, '', 0, 0);
    $pdf->Cell(120, 7, 'Subtotal: ', 1, 0, 'R');
    $pdf->Cell(40, 7, '$' . number_format($valor_acumulado_pieza, 2), 1, 1, 'C');
    $pdf->Ln(5);
}

// Línea final para mostrar el total acumulado general
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 7, '', 0, 0);
$pdf->Cell(120, 7, 'Subtotal:', 1, 0, 'R');
$pdf->Cell(40, 7, '$' . number_format($valor_acumulado_total, 2), 1, 1, 'C');

$pdf->Cell(10, 7, '', 0, 0);
$pdf->Cell(120, 7, 'IVA:', 1, 0, 'R');
$pdf->Cell(40, 7, '$' . number_format($valor_acumulado_total*0.16, 2), 1, 1, 'C');

$pdf->Cell(10, 7, '', 0, 0);
$pdf->Cell(120, 7, 'Total:', 1, 0, 'R');
$pdf->Cell(40, 7, '$' . number_format(($valor_acumulado_total*1.16), 2), 1, 1, 'C');

// Cerrar conexión
$conexion->close();

// Mostrar PDF en el navegador
$pdf->Output('I', 'Remision' .  date('d/m/Y') . '.pdf');
?>
