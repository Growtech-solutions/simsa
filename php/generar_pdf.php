<?php
require('../../recursos/PDF/fpdf.php');

// Establecer la conexión con la base de datos
include '../conexion.php';
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el ID de la orden de compra de la URL
$id_orden_compra = isset($_GET['id']) ? $_GET['id'] : null;

if ($id_orden_compra) {
    // Consulta para obtener los detalles de la orden de compra
    $sql_oc = "SELECT * FROM orden_compra WHERE id = '$id_orden_compra'";
    $resultado_oc = $conexion->query($sql_oc);

    if ($resultado_oc->num_rows > 0) {
        $fila_oc = $resultado_oc->fetch_assoc();
        
        $sql_proveedor = "SELECT proveedor, direccion, telefono, correo FROM proveedor WHERE proveedor = '" . $fila_oc['proveedor'] . "'";
        $resultado_proveedor = $conexion->query($sql_proveedor);

        if ($resultado_proveedor->num_rows > 0) {
            $fila_proveedor = $resultado_proveedor->fetch_assoc();
            $direccionprov = ($fila_proveedor['direccion'] !== null) ? $fila_proveedor['direccion'] : 'No disponible';
            if (!empty($fila_proveedor['correo'])) {
                $contactoprov = $fila_proveedor['correo'];
            } elseif (!empty($fila_proveedor['telefono'])) {
                $contactoprov = $fila_proveedor['telefono'];
            } else {
                $contactoprov = "No disponible";
            }
        } else {
            $direccionprov = "No disponible";
            $contactoprov = "No disponible";
        }
        
        // Crear un nuevo PDF
        $pdf = new FPDF();
        $pdf->AddPage();

        // Cargar las fuentes Arial y Arial_Bold
        $pdf->AddFont('Arial', '', 'Arial.php');
        $pdf->AddFont('Arial_Bold', 'B', 'Arial_Bold.php');

        // Agregar el logo
        $pdf->Image('../img/logo.png', 10, 5, 30);

        // Agregar la OC en la esquina superior derecha
        $pdf->SetFont('Arial_Bold', 'B', 12);
        $pdf->Cell(0, 10, 'OC: ' . utf8_decode($fila_oc['oc']), 0, 1, 'R');

// Encabezado de la orden de compra
$pdf->SetFont('Arial_Bold', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('SUMINISTROS INDUSTRIALES MODERNOS'), 0, 1, 'C');
$pdf->Ln(5); // Salto de línea para separar el encabezado del contenido

$pdf->SetFont('Arial', '', 10);

// Establecemos el ancho de las columnas
$colWidth = 110; // Ajusta según sea necesario

// Primera fila de etiquetas y valores
$labels = ['Proveedor:', 'Llegada Estimada:'];
$values = [$fila_oc['proveedor'], $fila_oc['llegada_estimada']];

for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($colWidth, 10, utf8_decode($labels[$i] . ' ' . $values[$i]), 0, 0);
}
$pdf->Ln(5);

// Primera fila de etiquetas y valores
$labels = ['Dirección:', ''];
$values = [$direccionprov, ''];

for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($colWidth, 10, utf8_decode($labels[$i] . ' ' . $values[$i]), 0, 0);
}
$pdf->Ln(5);

// Primera fila de etiquetas y valores
$labels = ['Referencia:', 'Requisición:'];
$values = [$fila_oc['observaciones'], $fila_oc['id']];

for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($colWidth, 10, utf8_decode($labels[$i] . ' ' . $values[$i]), 0, 0);
}
$pdf->Ln(5);

// Segunda fila de etiquetas y valores
$labels2 = [ 'Condición de pago:', 'Solicita:'];
$values2 = [ $fila_oc['cotizacion'],$fila_oc['responsable'] ];

for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($colWidth, 10, utf8_decode($labels2[$i] . ' ' . $values2[$i]), 0, 0);
}
$pdf->Ln(5);

// Primera fila de etiquetas y valores
$labels = ['', 'Fecha Solicitud:'];
$values = ['', $fila_oc['fecha_solicitud']];

for ($i = 0; $i < 2; $i++) {
    $pdf->Cell($colWidth, 10, utf8_decode($labels[$i] . ' ' . $values[$i]), 0, 0);
}

$pdf->Ln(10); // Salto de línea final

        // Detalles de Compras
        $pdf->SetFont('Arial_Bold', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Detalles de Compras'), 0, 1);
        $pdf->Ln(5);

        // Tabla de detalles
        $pdf->SetFont('Arial_Bold', 'B', 10);
        $pdf->Cell(30, 10, utf8_decode('Cantidad'), 1);
        $pdf->Cell(90, 10, utf8_decode('Descripción'), 1);
        $pdf->Cell(15, 10, 'OT', 1);
        $pdf->Cell(30, 10, utf8_decode('Precio Unitario'), 1);
        $pdf->Cell(30, 10, utf8_decode('Total'), 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);

        // Consulta para obtener los detalles de las compras asociadas
        $sql_compras = "SELECT * FROM compras WHERE id_oc = '$id_orden_compra'";
        $resultado_compras = $conexion->query($sql_compras);

        while ($fila_compras = $resultado_compras->fetch_assoc()) {
            // Calcular la altura necesaria para la celda de descripción sin imprimirla
            $descripcion = utf8_decode($fila_compras['descripcion']);
            $tmp_pdf = clone $pdf;  // Clonamos el PDF para medir la altura
            $tmp_pdf->MultiCell(90, 5, $descripcion, 0, 'J');
            $descripcion_altura = $tmp_pdf->GetY() - $pdf->GetY();  // Calcula la altura real

            // Altura del renglón
            $altura_renglon = max(5, $descripcion_altura);

            // Añadir las celdas con la altura uniforme
            $pdf->Cell(30, $altura_renglon, utf8_decode($fila_compras['cantidad'] . ' ' . $fila_compras['unidad']), 1);
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            $pdf->MultiCell(90, 5, $descripcion, 1);  // Ahora imprimimos la celda de descripción
            $pdf->SetXY($x + 90, $y);
            $pdf->Cell(15, $altura_renglon, utf8_decode($fila_compras['ot']), 1, 0, 'C');
            $pdf->Cell(30, $altura_renglon, number_format($fila_compras['precio_unitario'], 2), 1, 0, 'R');
            $pdf->Cell(30, $altura_renglon, number_format(($fila_compras['precio_unitario']*$fila_compras['cantidad']), 2), 1, 0, 'R');
            $pdf->Ln();
        }
        $pdf->Cell(40, 10, utf8_decode('Tipo de cambio:'), 0, 0, 'L');
        $pdf->Cell(40, 10, utf8_decode($fila_oc['tipo_cambio']), 0, 1, 'L');
        // Agregar espacio antes de los totales
        $pdf->Ln(5);

        // Agregar totales
        $pdf->SetFont('Arial_Bold', 'B', 12);

        $pdf->Cell(140, 10, utf8_decode('Subtotal'), 0, 0, 'R');
        $pdf->Cell(40, 10, utf8_decode('$ '.number_format($fila_oc['subtotal'], 2)), 0, 1, 'L');
        if ($fila_oc['cotizacion']=='NETO'){
            $pdf->Cell(140, 10, utf8_decode('I.V.A.'), 0, 0, 'R');
            $pdf->Cell(40, 10, utf8_decode('$ 0'), 0, 1, 'L');
        }
        else{
            $pdf->Cell(140, 10, utf8_decode('I.V.A.'), 0, 0, 'R');
            $pdf->Cell(40, 10, utf8_decode('$ ' . number_format($fila_oc['subtotal'] * 0.16, 2)), 0, 1, 'L');
        }

        $pdf->Cell(140, 10, 'Total:', 0, 0, 'R');
        $pdf->Cell(40, 10, utf8_decode('$ '.number_format($fila_oc['neto'], 2) . " (" . $fila_oc['moneda'] . ")"), 0, 1, 'L');
        
                      // Mover a la parte inferior izquierda
$pdf->Ln(20); // Espacio antes de los datos fiscales
$pdf->SetY(-65); // Posiciona el texto 50 unidades desde el final de la página
$pdf->SetX(10);

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 5, utf8_decode('Suministros Industriales Modernos'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Juan de la Barrera NTE. NO. 1010'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Monterrey NL C.P. 64103'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Tel. 8183930446'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('RFC: SIM081113M28'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Régimen Fiscal: 601'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Uso de CFDI: G03'), 0, 1);
$pdf->Cell(90, 5, utf8_decode('Contacto: nelly@e-simsa.com.mx'), 0, 1);

        // Salida del PDF
        $pdf->Output('I', 'Orden_Compra_' . $id_orden_compra . '.pdf');
    } else {
        echo "No se encontró la orden de compra.";
    }
} else {
    echo "ID de orden de compra no especificado.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>

