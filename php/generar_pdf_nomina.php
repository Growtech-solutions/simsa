<?php 
 include '../conexion_servicios.php';

 // Verificar la conexión
 if ($conexion_servicios->connect_error) {
     die("Error de conexión: " . $conexion_servicios->connect_error);
 }

$id_periodo = isset($_GET['periodo']) ? (int)$_GET['periodo'] : 0;
$id_trabajador = isset($_GET['trabajador_id']) ? (int)$_GET['trabajador_id'] : 0;

$sql_xml = "SELECT xml_timbrado FROM nomina WHERE periodo_id = ? AND trabajador_id = ?";
function generarQR($emisor, $receptor, $total, $uuid, $selloCFD) {
    $total_fmt = number_format((float)$total, 6, '.', '');
    $url = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx";
    $url .= "?id=$uuid&re=$emisor&rr=$receptor&tt=$total_fmt&fe=" . substr($selloCFD, -8);
    return $url;
}
function numeroATexto($numero) {
    $formatter = new NumberFormatter("es", NumberFormatter::SPELLOUT);
    $partes = explode('.', number_format($numero, 2, '.', ''));
    $texto = $formatter->format($partes[0]);
    $centavos = str_pad($partes[1], 2, '0', STR_PAD_RIGHT);
    return strtoupper($texto) . " PESOS $centavos/100 M.N.";
}
$stmt_xml = $conexion_servicios->prepare($sql_xml);
$stmt_xml->bind_param("ii", $id_periodo, $id_trabajador);
$stmt_xml->execute();
$result_xml = $stmt_xml->get_result();

if ($result_xml && $result_xml->num_rows > 0) {
    $row = $result_xml->fetch_assoc();
    $xml_data = $row['xml_timbrado'];
} else {
    echo "No se encontró el XML para el trabajador y periodo especificados.";
    exit;
}
$stmt_xml->close();

require('../../recursos/PDF/fpdf.php');
require('../../recursos/PDF/phpqrcode/qrlib.php');

// Cargar el XML desde BD
$xml = new DOMDocument();
$xml->loadXML($xml_data);

// Obtener datos básicos del comprobante
$comprobante = $xml->getElementsByTagName('Comprobante')->item(0);
$emisor = $xml->getElementsByTagName('Emisor')->item(0);
$receptor = $xml->getElementsByTagName('Receptor')->item(0);
$nomina = $xml->getElementsByTagName('Nomina')->item(0);


// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

// Encabezado y logo en el mismo renglón
$pdf->SetFont('Arial', 'B', 14);
$pdf->Image('../img/logo.png', 10, 0, 40);
$pdf->SetXY(83, 10); // Posiciona el cursor a la derecha del logo
$pdf->Cell(0, 10, utf8_decode("Recibo de Nómina"), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(150, 10); 
$pdf->Cell(0, 10, utf8_decode($comprobante->getAttribute("Fecha")), 0, 1, 'R');

$pdf->SetFont('Arial', '', 10);
$pdf->Ln(10);

$pdf->SetY(25);
$pdf->Ln(10);


// ===============================
// DATOS DEL EMISOR Y RECEPTOR
// ===============================
$pdf->SetFillColor(230,230,230); // gris claro
$pdf->Cell(95,8,'Emisor',1,0,'C',true);
$pdf->Cell(95,8,'Receptor',1,1,'C',true);

$pdf->SetFont('Arial','',8);
$pdf->MultiCell(95,5,utf8_decode("RFC: ".$emisor->getAttribute("Rfc")."\n".
                                "Nombre: ".$emisor->getAttribute("Nombre")."\n".
                                "Régimen: ".$emisor->getAttribute("RegimenFiscal")),1);

$x = $pdf->GetX(); $y = $pdf->GetY();
$pdf->SetXY(105,$y-15);
$pdf->MultiCell(95,5,utf8_decode("RFC: ".$receptor->getAttribute("Rfc")."\n".
                                "Nombre: ".$receptor->getAttribute("Nombre")."\n".
                                "UsoCFDI: ".$receptor->getAttribute("UsoCFDI")),1);

// ===============================
// DATOS DEL COMPROBANTE
// ===============================

$sql_nomina = "SELECT horas_simples, horas_dobles, horas_triples, vacaciones, asistencia FROM nomina WHERE periodo_id = ? AND trabajador_id = ?";
$stmt_nomina = $conexion_servicios->prepare($sql_nomina);
$stmt_nomina->bind_param("ii", $id_periodo, $id_trabajador);
$stmt_nomina->execute();
$result_nomina = $stmt_nomina->get_result();
if ($result_nomina && $result_nomina->num_rows > 0) {
    $row_nomina = $result_nomina->fetch_assoc();
    $horas_simples = $row_nomina['horas_simples'];
    $horas_dobles = $row_nomina['horas_dobles'];
    $horas_triples = $row_nomina['horas_triples'];
    $vacaciones = $row_nomina['vacaciones'];
    $asistencia = $row_nomina['asistencia'];
} else {
    $horas_simples = 0;
    $horas_dobles = 0;
    $horas_triples = 0;
    $vacaciones = 0;
    $asistencia = 0;
}
$sql_trabajador = "SELECT salario FROM trabajadores WHERE id = ?";
$stmt_trabajador = $conexion_servicios->prepare($sql_trabajador);
$stmt_trabajador->bind_param("i", $id_trabajador);
$stmt_trabajador->execute();
$result_trabajador = $stmt_trabajador->get_result();
if ($result_trabajador && $result_trabajador->num_rows > 0) {
    $row_trabajador = $result_trabajador->fetch_assoc();
    $salario = $row_trabajador['salario'];
} else {
    $salario = 0;
}
$stmt_nomina->close();
$pdf->Ln(2);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(0,8,'Datos nomina',1,1,'C',true);
$pdf->SetFont('Arial','',8);

$pdf->Cell(95,6,'Salario diario: '.$salario,1,0);
$pdf->Cell(95,6,'Asistencia: '.$asistencia,1,1);
$pdf->Cell(95,6,'Horas simples: '.$horas_simples,1,0);
$pdf->Cell(95,6,'Horas extra: '.$horas_dobles+$horas_triples,1,1);


// ===============================
// DETALLE DE NÓMINA
// ===============================
$pdf->Ln(2);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(0,8,utf8_decode('Detalle de Nómina'),1,1,'C',true);
$pdf->SetFont('Arial','',8);

// Percepciones
$percepciones = $xml->getElementsByTagName('Percepcion');
$pdf->Cell(0,6,'PERCEPCIONES',1,1,'L',true);
foreach ($percepciones as $p) {
    $pdf->Cell(95,6,$p->getAttribute("Concepto"),1,0);
    $pdf->Cell(95,6,'$'.number_format($p->getAttribute("ImporteGravado") + $p->getAttribute("ImporteExento"),2),1,1,'R');
}

// Deducciones
$deducciones = $xml->getElementsByTagName('Deduccion');
$pdf->Ln(1);
$pdf->Cell(0,6,'DEDUCCIONES',1,1,'L',true);
foreach ($deducciones as $d) {
    $pdf->Cell(95,6,utf8_decode($d->getAttribute("Concepto")),1,0);
    $pdf->Cell(95,6,'$'.number_format($d->getAttribute("Importe"),2),1,1,'R');
}

$sql_complemento= "SELECT complemento FROM nomina WHERE periodo_id = ? AND trabajador_id = ?";
$stmt_complemento = $conexion_servicios->prepare($sql_complemento);
$stmt_complemento->bind_param("ii", $id_periodo, $id_trabajador);
$stmt_complemento->execute();
$result_complemento = $stmt_complemento->get_result();
if ($result_complemento && $result_complemento->num_rows > 0) {
    $row_complemento = $result_complemento->fetch_assoc();
    $complemento = $row_complemento['complemento'];
} else {
    $complemento = 0;
}
$stmt_complemento->close();

// Otros pagos
$otrosPagos = $xml->getElementsByTagName('OtroPago');
if ($otrosPagos->length > 0 || $complemento > 0) {
    $pdf->Ln(1);
    $pdf->Cell(0,6,'OTROS PAGOS',1,1,'L',true);
    foreach ($otrosPagos as $o) {
        $pdf->Cell(95,6,$o->getAttribute("Concepto"),1,0);
        $pdf->Cell(95,6,'$'.number_format($o->getAttribute("Importe"),2),1,1,'R');
    }
    if ($complemento > 0) {
        $pdf->Ln(1);
        $pdf->Cell(95,6,'Otros',1,0);
        $pdf->Cell(95,6,'$'.number_format($complemento,2),1,1,'R');
    }
}

// ===============================
// TOTALES
// ===============================
$pdf->Ln(2);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(95,8,'SUBTOTAL',1,0,'C',true);
if ($complemento > 0) {
    $pdf->Cell(95,8,'$'.number_format($comprobante->getAttribute("SubTotal")+ $complemento,2),1,1,'C');
} else {
    $pdf->Cell(95,8,'$'.number_format($comprobante->getAttribute("SubTotal"),2),1,1,'C');
}
$pdf->Cell(95,8,'DEDUCCIONES',1,0,'C',true);
$pdf->Cell(95,8,'$'.number_format($comprobante->getAttribute("Descuento"),2),1,1,'C');
$pdf->Cell(95,8,'NETO A PAGAR',1,0,'C',true);
if ($complemento > 0) {
    $pdf->Cell(95,8,'$'.number_format($comprobante->getAttribute("Total")+ $complemento,2),1,1,'C');
} else {
    $pdf->Cell(95,8,'$'.number_format($comprobante->getAttribute("Total"),2),1,1,'C');
}

// Obtener UUID y Sello
$timbre = $xml->getElementsByTagName('TimbreFiscalDigital')->item(0);
$uuid = $timbre ? $timbre->getAttribute("UUID") : '';
$selloCFD = $comprobante->getAttribute("Sello");

// Generar URL QR
$qr_url = generarQR(
    $emisor->getAttribute("Rfc"),
    $receptor->getAttribute("Rfc"),
    $comprobante->getAttribute("Total"),
    $uuid,
    $selloCFD
);

// Generar imagen QR temporal con extensión .png
$qr_temp = tempnam(sys_get_temp_dir(), 'qr_');
$qr_temp_png = $qr_temp . '.png';
QRcode::png($qr_url, $qr_temp_png, QR_ECLEVEL_L, 3);

$pdf->SetFont('Arial', '', 8);
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 6, utf8_decode('Total con letra: ' . numeroATexto((float)$comprobante->getAttribute("Total"))), 0, 1);

// Obtener datos del TimbreFiscalDigital
$tfd = [];
if ($timbre) {
    $tfd['NoCertificadoSAT'] = $timbre->getAttribute("NoCertificadoSAT");
    $tfd['UUID'] = $timbre->getAttribute("UUID");
    $tfd['RfcProvCertif'] = $timbre->getAttribute("RfcProvCertif");
    $tfd['SelloCFD'] = $timbre->getAttribute("SelloCFD");
    $tfd['SelloSAT'] = $timbre->getAttribute("SelloSAT");
} else {
    $tfd['NoCertificadoSAT'] = '';
    $tfd['UUID'] = '';
    $tfd['RfcProvCertif'] = '';
    $tfd['SelloCFD'] = '';
    $tfd['SelloSAT'] = '';
}

// Mostrar QR y datos al lado
$qr_x = 10;
$qr_y = $pdf->GetY();
$qr_w = 35;
$pdf->Image($qr_temp_png, $qr_x, $qr_y, $qr_w);
unlink($qr_temp_png);
unlink($qr_temp); // Limpia el archivo temporal original también

// Posicionar al lado del QR
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(40, 6, utf8_decode("No. Certificado del SAT:"), 0, 0);
$pdf->SetFont('Arial', '', 6);
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y + 5);
$pdf->Cell(60, 6, utf8_decode($tfd['NoCertificadoSAT']), 0, 1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y + 10);
$pdf->Cell(40, 6, utf8_decode("UUID:"), 0, 0);
$pdf->SetFont('Arial', '', 6);
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y + 15);
$pdf->Cell(60, 6, utf8_decode($tfd['UUID']), 0, 1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y + 20);
$pdf->Cell(35, 6, utf8_decode("RFC PAC:"), 0, 0);
$pdf->SetFont('Arial', '', 6);
$pdf->SetXY($qr_x + $qr_w + 5, $qr_y + 25);
$pdf->Cell(0, 6, utf8_decode($tfd['RfcProvCertif']), 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 6, utf8_decode("Sello del CFD:"), 0, 1);
$pdf->SetFont('Arial', '', 4);
$pdf->MultiCell(0, 3, utf8_decode($tfd['SelloCFD']), 0, 'L');

// Espacio después del sello CFD
$pdf->Ln(1);

// Sello del SAT
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 6, utf8_decode("Sello del SAT:"), 0, 1);
$pdf->SetFont('Arial', '', 4);
$pdf->MultiCell(0, 3, utf8_decode($tfd['SelloSAT']), 0, 'L');

$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 6, utf8_decode("Certificado del SAT:"), 0, 1);
$pdf->SetFont('Arial', '', 4);
$pdf->MultiCell(0, 3, utf8_decode($comprobante->getAttribute("Certificado")), 0, 'L');

// Salida
$pdf->Output("I","recibo_nomina.pdf");
