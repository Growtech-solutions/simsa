<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include '../conexion_servicios.php';
include '../conexion_transimex.php';
$conexion_transimex = new mysqli($host_transimex, $usuario_transimex, $contrasena_transimex, $base_de_datos_transimex);

if ($conexion_servicios->connect_error) exit("❌ Conexión fallida: " . $conexion_servicios->connect_error);

$periodo_id = $_GET['id_periodo'];
$stmt = $conexion_servicios->prepare("SELECT * FROM periodo WHERE id = ?");
$stmt->bind_param("s", $periodo_id);
$stmt->execute();
$result = $stmt->get_result();
$periodo = $result->fetch_assoc();
// Inicializar arreglos para éxito y error
    $timbrados = [];
    $errores = [];
if (!$periodo) {
    die("Periodo no encontrado");
}

include '../pestañas/calculo_nomina_servicios.php';

function obtenerNumeroSerieCertificado($ruta_certificado) {
    $cer_der = file_get_contents($ruta_certificado);
    if (!$cer_der) return false;

    $cert_pem = "-----BEGIN CERTIFICATE-----\n" .
                chunk_split(base64_encode($cer_der), 64, "\n") .
                "-----END CERTIFICATE-----\n";

    $cert_data = openssl_x509_parse($cert_pem);
    if (!$cert_data || empty($cert_data['serialNumberHex'])) return false;

    return strtoupper(ltrim($cert_data['serialNumberHex'], '0'));
}
$cert_base64 = base64_encode(file_get_contents($ruta_certificado));
$key_base64  = base64_encode(file_get_contents($ruta_key));
$no_certificado = obtenerNumeroSerieCertificado($ruta_certificado);

date_default_timezone_set('America/Mexico_City');
$fechaTimbrado = date('Y-m-d\TH:i:s');

// Calcular días pagados automáticamente
$numDiasPagados = (strtotime($periodo['fecha_fin']) - strtotime($periodo['fecha_inicio'])) / 86400 + 1;

foreach ($trabajadores_nomina as $t) {
    if ($t['doble_reporte']>0 || $t['percepcion_trabajador']<=0) {
        $errores[] = [
            'nombre' => $t['nombre'],
            'error' => "El empleado tiene doble reporte o percepciones menores o iguales a cero en el periodo. Por favor revisa los registros antes de timbrar.",
            'xml' => ""
        ];
        continue; // Saltar al siguiente trabajador
    }
    else{
    // Verificar si ya existe un registro de nómina para este trabajador y periodo
    $sql_check = "SELECT 1 FROM nomina WHERE periodo_id = ? AND trabajador_id = ? LIMIT 1";
    $stmt_check = $conexion_servicios->prepare($sql_check);
    $stmt_check->bind_param("ii", $periodo_id, $t['id']);
    $stmt_check->execute();
    $stmt_check->store_result();
    // Crear un nuevo documento XML por trabajador
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    if ($stmt_check->num_rows === 0) {
        // Solo ejecutar el timbrado si no existe registro previo
    $totalSueldos =  $t['percepciones_gravadas'] + $t['percepciones_exentas'];
    $totalSueldos = round($totalSueldos, 2);


    // Crear el comprobante CFDI 4.0
    $comprobante = $xml->createElement("cfdi:Comprobante");
    $comprobante->setAttribute("Sello", "");
    $comprobante->setAttribute("Version", "4.0");
    $comprobante->setAttribute("Serie", "A");
    $comprobante->setAttribute("Folio", $folio_periodo);
    $comprobante->setAttribute("Fecha", date('Y-m-d\TH:i:s'));
    $comprobante->setAttribute("Descuento", $t['deducciones']);
    $comprobante->setAttribute("NoCertificado", $no_certificado);
    $comprobante->setAttribute("Certificado", $cert_base64);
    $comprobante->setAttribute("SubTotal", $t['percepcion_trabajador']+$t['subsidio']); // se actualizará con totales
    $comprobante->setAttribute("Moneda", "MXN");
    $comprobante->setAttribute("Total",  $t['percepcion_trabajador']+$t['subsidio']-$t['deducciones']); // se actualizará con totales
    $comprobante->setAttribute("TipoDeComprobante", "N");
    $comprobante->setAttribute("LugarExpedicion", $codigo_postal_emisor);
    $comprobante->setAttribute("Exportacion", "01");
    $comprobante->setAttribute("MetodoPago", "PUE");

    // Namespaces requeridos
    $comprobante->setAttribute("xmlns:cfdi", "http://www.sat.gob.mx/cfd/4");
    $comprobante->setAttribute("xmlns:nomina12", "http://www.sat.gob.mx/nomina12");
    $comprobante->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $comprobante->setAttribute("xsi:schemaLocation", 
        "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/nomina12 http://www.sat.gob.mx/sitio_internet/cfd/nomina/nomina12.xsd"
    );

    // Emisor
    $emisor = $xml->createElement("cfdi:Emisor");
    $emisor->setAttribute("Rfc", $rfc_emisor);
    $emisor->setAttribute("Nombre", $razon_social_emisor);
    $emisor->setAttribute("RegimenFiscal", $regimen_fiscal_emisor);
    $comprobante->appendChild($emisor);
 
    // Receptor
    $receptor = $xml->createElement("cfdi:Receptor");
    $receptor->setAttribute("Rfc", $t['rfc']);
    $receptor->setAttribute("Nombre", strtoupper($t['nombre']));
    $receptor->setAttribute("UsoCFDI", "CN01"); // sin espacios, en mayúsculas
    $receptor->setAttribute("DomicilioFiscalReceptor", $t['cp']);
    $receptor->setAttribute("RegimenFiscalReceptor", $t['regimen_fiscal']);
    $comprobante->appendChild($receptor);

    // Conceptos (obligatorio aunque sea nómina)
    $conceptos = $xml->createElement("cfdi:Conceptos");
    $concepto = $xml->createElement("cfdi:Concepto");
    $concepto->setAttribute("ClaveProdServ", "84111505"); // servicio de nómina
    $concepto->setAttribute("ObjetoImp", "01");
    $concepto->setAttribute("ClaveUnidad", "ACT");
    $concepto->setAttribute("Descuento", number_format($t['deducciones'], 2, '.', ''));
    $concepto->setAttribute("Cantidad", "1");
    $concepto->setAttribute("Descripcion", "Pago de nómina");
    $concepto->setAttribute("ValorUnitario", number_format($t['percepcion_trabajador'] + $t['subsidio'], 2, '.', '')); // se actualizará con totales
    $concepto->setAttribute("Importe", number_format($t['percepcion_trabajador'] + $t['subsidio'], 2, '.', ''));
    $conceptos->appendChild($concepto);
    $comprobante->appendChild($conceptos);

    // COMPLEMENTO NOMINA
    $complemento = $xml->createElement("cfdi:Complemento");
    $nomina = $xml->createElement("nomina12:Nomina");
    $nomina->setAttribute("Version", "1.2");
    $nomina->setAttribute("TipoNomina", ($clave_tipo_nomina));
    $nomina->setAttribute("FechaPago", $periodo['fecha_fin']);
    $nomina->setAttribute("FechaInicialPago", $periodo['fecha_inicio']);
    $nomina->setAttribute("FechaFinalPago", $periodo['fecha_fin']);
    $nomina->setAttribute("NumDiasPagados", $numDiasPagados);

    // Emisor nómina
    $nominaEmisor = $xml->createElement("nomina12:Emisor");
    $nominaEmisor->setAttribute("RegistroPatronal", $registro_patronal_emisor);
    if ($regimen_fiscal_emisor !== '601' && !empty($curp_emisor)) {
        $nominaEmisor->setAttribute("Curp", $curp_emisor);
    }
    $nomina->appendChild($nominaEmisor);

    // Receptor nómina
    switch ($t['tipo_contrato']) {
        case 'Tiempo indeterminado':
            $tipo_contrato = '01';
            break;
        case 'Tiempo determinado':
            $tipo_contrato = '02';
            break;
        default:
            $tipo_contrato = '99';
            break;
    }
    switch ($t['jornada']) {
        case 'Diurna':
            $tipo_jornada = '01';
            break;
        case 'Nocturna':
            $tipo_jornada = '02';
            break;
        case 'Mixta':
            $tipo_jornada = '03';
            break;
        default:
            $tipo_jornada = '99';
            break;
    }
    $nominaReceptor = $xml->createElement("nomina12:Receptor");
    $nominaReceptor->setAttribute("Curp", $t['curp']);
    $nominaReceptor->setAttribute("NumSeguridadSocial", $t['nss']);
    $nominaReceptor->setAttribute("FechaInicioRelLaboral", $t['fecha_ingreso']);
    $nominaReceptor->setAttribute("Antigüedad", $t['antiguedad_nomina']);
    $nominaReceptor->setAttribute("ClaveEntFed", $t['clave_entidad_fed']);
    $nominaReceptor->setAttribute("PeriodicidadPago", $PeriodicidadPago);
    $nominaReceptor->setAttribute("TipoContrato", $tipo_contrato);
    $nominaReceptor->setAttribute("Sindicalizado", "No");
    $nominaReceptor->setAttribute("TipoJornada", $tipo_jornada);
    $nominaReceptor->setAttribute("TipoRegimen", "02");
    $nominaReceptor->setAttribute("NumEmpleado", $t['id']);
    $nominaReceptor->setAttribute("Puesto", $t['puesto']);
    $nominaReceptor->setAttribute("Departamento", $t['departamento']);
    $nominaReceptor->setAttribute("SalarioBaseCotApor", $t['sbc']);
    $nominaReceptor->setAttribute("RiesgoPuesto", "1");
    $nominaReceptor->setAttribute("SalarioDiarioIntegrado", $t['sdi']);
    $nomina->appendChild($nominaReceptor);

    // ==========================
    // PERCEPCIONES
    // ==========================
    $percepciones = $xml->createElement("nomina12:Percepciones");
    $percepciones->setAttribute("TotalSueldos", $totalSueldos);
    $percepciones->setAttribute("TotalGravado", $t['percepciones_gravadas']);
    $percepciones->setAttribute("TotalExento", $t['percepciones_exentas']);

    // Sueldo
    $percepcion = $xml->createElement("nomina12:Percepcion");
    $percepcion->setAttribute("TipoPercepcion", "001");
    $percepcion->setAttribute("Clave", "001");
    $percepcion->setAttribute("Concepto", "Sueldos, Salarios");
    $percepcion->setAttribute("ImporteGravado", $t['valor_horas_simples']);
    $percepcion->setAttribute("ImporteExento", "0.00");
    if (isset($t['total_bonos']) && floatval($t['total_bonos']) > 0) {
        // Agregar un nodo Percepcion adicional para bonos
        $percepcionBono = $xml->createElement("nomina12:Percepcion");
        $percepcionBono->setAttribute("TipoPercepcion", "050");
        $percepcionBono->setAttribute("Clave", "BONO");
        $percepcionBono->setAttribute("Concepto", "Bonos");
        $percepcionBono->setAttribute("ImporteGravado", $t['total_bonos']);
        $percepcionBono->setAttribute("ImporteExento", "0.00");
        $percepciones->appendChild($percepcionBono);
    }
    $percepciones->appendChild($percepcion);

    // Horas extra (dobles)
    if ($t['horas_dobles'] > 0) {
        $percepcionHE = $xml->createElement("nomina12:Percepcion");
        $percepcionHE->setAttribute("TipoPercepcion", "019");
        $percepcionHE->setAttribute("Clave", "HE2");
        $percepcionHE->setAttribute("Concepto", "Horas extra dobles");
        $percepcionHE->setAttribute("ImporteGravado", number_format($t['dobles_gravadas'], 2, '.', ''));
        $percepcionHE->setAttribute("ImporteExento", number_format($t['dobles_exentas'], 2, '.', ''));

        $horasExtra = $xml->createElement("nomina12:HorasExtra");
        if ($t['horas_dobles']>3){
            $dias= ceil($t['horas_dobles']/3);
        }
        else{
            $dias=1;
        }
        $horasExtra->setAttribute("Dias", $dias);
        $horasExtra->setAttribute("TipoHoras", "01"); 
        $horasExtra->setAttribute("HorasExtra", ceil($t['horas_dobles']));
        $horasExtra->setAttribute("ImportePagado", number_format($t['dobles_exentas'] + $t['dobles_gravadas'], 2, '.', ''));
        $percepcionHE->appendChild($horasExtra);

        $percepciones->appendChild($percepcionHE);
    }

    // Horas extra (triples)
    if ($t['horas_triples'] > 0) {
        $percepcionHE3 = $xml->createElement("nomina12:Percepcion");
        $percepcionHE3->setAttribute("TipoPercepcion", "019");
        $percepcionHE3->setAttribute("Clave", "HE3");
        $percepcionHE3->setAttribute("Concepto", "Horas extra triples");
        $percepcionHE3->setAttribute("ImporteGravado", $t['valor_horas_triples']);
        $percepcionHE3->setAttribute("ImporteExento", "0.00");

        $horasExtra3 = $xml->createElement("nomina12:HorasExtra");
        $horasExtra3->setAttribute("Dias", "1");
        $horasExtra3->setAttribute("TipoHoras", "03"); 
        $horasExtra3->setAttribute("HorasExtra", $t['horas_triples']);
        $horasExtra3->setAttribute("ImportePagado", $t['valor_horas_triples']);
        $percepcionHE3->appendChild($horasExtra3);

        $percepciones->appendChild($percepcionHE3);
    }

    $nomina->appendChild($percepciones);

    // ==========================
    // DEDUCCIONES
    // ==========================
    
    $otrasDeducciones = $t['imss'] + $t['monto_infonavit'] + $t['monto_prestamos'] + $t['monto_fondo_ahorro'];
    $totalDeducciones = $t['deducciones'];
    if ($totalDeducciones > 0) {

    $deducciones = $xml->createElement("nomina12:Deducciones");
    $deducciones->setAttribute("TotalOtrasDeducciones", $otrasDeducciones);
    $deducciones->setAttribute("TotalImpuestosRetenidos", $t['isr_retencion']);

    // ISR
    $deduccionISR = $xml->createElement("nomina12:Deduccion");
    $deduccionISR->setAttribute("TipoDeduccion", "002");
    $deduccionISR->setAttribute("Clave", "ISR");
    $deduccionISR->setAttribute("Concepto", "ISR Retenido");
    $deduccionISR->setAttribute("Importe", $t['isr_retencion']);
    $deducciones->appendChild($deduccionISR);

    // IMSS
    if ($t['imss'] > 0) {
        $deduccionIMSS = $xml->createElement("nomina12:Deduccion");
        $deduccionIMSS->setAttribute("TipoDeduccion", "001");
        $deduccionIMSS->setAttribute("Clave", "IMSS");
        $deduccionIMSS->setAttribute("Concepto", "Seguridad Social");
        $deduccionIMSS->setAttribute("Importe", $t['imss']);
        $deducciones->appendChild($deduccionIMSS);
    }

    // INFONAVIT
    if ($t['monto_infonavit'] > 0) {
        $deduccionINF = $xml->createElement("nomina12:Deduccion");
        $deduccionINF->setAttribute("TipoDeduccion", "004");
        $deduccionINF->setAttribute("Clave", "INF");
        $deduccionINF->setAttribute("Concepto", "Crédito INFONAVIT");
        $deduccionINF->setAttribute("Importe", $t['monto_infonavit']);
        $deducciones->appendChild($deduccionINF);
    }

    // FONDO DE AHORRO
    if ($t['monto_fondo_ahorro'] > 0) {
        $deduccionFA = $xml->createElement("nomina12:Deduccion");
        $deduccionFA->setAttribute("TipoDeduccion", "003");
        $deduccionFA->setAttribute("Clave", "FOA");
        $deduccionFA->setAttribute("Concepto", "Fondo de Ahorro");
        $deduccionFA->setAttribute("Importe", $t['monto_fondo_ahorro']);
        $deducciones->appendChild($deduccionFA);
    }

    // PRÉSTAMOS
    if ($t['monto_prestamos'] > 0) {
        $deduccionPRE = $xml->createElement("nomina12:Deduccion");
        $deduccionPRE->setAttribute("TipoDeduccion", "051");
        $deduccionPRE->setAttribute("Clave", "PRE");
        $deduccionPRE->setAttribute("Concepto", "Préstamos personales");
        $deduccionPRE->setAttribute("Importe", number_format($t['monto_prestamos'], 2, '.', ''));
        $deducciones->appendChild($deduccionPRE);
    }

    $nomina->appendChild($deducciones);
    }
    // ==========================
    // OTROS PAGOS (SUBSIDIO)
    // ==========================

    $otrosPagos = $xml->createElement("nomina12:OtrosPagos");

    $otroPago = $xml->createElement("nomina12:OtroPago");
    $otroPago->setAttribute("TipoOtroPago", "002");
    $otroPago->setAttribute("Clave", "SUB");
    $otroPago->setAttribute("Concepto", "Subsidio para el empleo");
    $otroPago->setAttribute("Importe", number_format($t['subsidio'], 2, '.', ''));

    $subsidio = $xml->createElement("nomina12:SubsidioAlEmpleo");
    $subsidio->setAttribute("SubsidioCausado", number_format($t['subsidio'], 2, '.', ''));

    $otroPago->appendChild($subsidio);
    $otrosPagos->appendChild($otroPago);
    $nomina->appendChild($otrosPagos);
    $nomina->setAttribute("TotalOtrosPagos", number_format($t['subsidio'], 2, '.', ''));
    

    // Totales a nivel nómina
    $nomina->setAttribute("TotalPercepciones", $t['percepcion_trabajador']);
    
    $nomina->setAttribute("TotalDeducciones", $t['deducciones']);

    $complemento->appendChild($nomina);
    $comprobante->appendChild($complemento);
    $xml->appendChild($comprobante);

    // Guardar XML
    $xml_path = "/tmp/nomina_$periodo_id.xml";
    $xml->save($xml_path);

    /*Descargar el XML
    if (file_exists($xml_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="nomina_'.$folio_periodo.'.xml"');
        header('Content-Length: ' . filesize($xml_path));
        flush(); // Limpia el búfer del sistema
        readfile($xml_path);    
        // Opcional: eliminar el archivo temporal después de descargar
        unlink($xml_path);
        exit;
    } else {
        echo "❌ Error: el archivo XML no se encontró.";
    }
    */
    //  Datos de timbrado
    $xml_base64  = base64_encode(file_get_contents($xml_path));
    $xml_enviado = file_get_contents($xml_path);

    // ==========================
    // Timbrado con PADE
    // ==========================
    $body = [
        "xmlBase64"   => $xml_base64,
        "contrato"    => "55a96814-90ac-43f6-80a8-d058aa4e1c11",
        "certBase64"  => $cert_base64,
        "keyBase64"   => $key_base64,
        "keyPass"     => $password_key,
        "prueba"      => "false",
        "opciones"    => ["CALCULAR_SELLO"]
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode("adriana.romero@cgyasc.com:RafaGar25*")
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://timbrado.pade.mx/servicio/rest/timbrado40/timbrarCfdi?contrato=55a96814-90ac-43f6-80a8-d058aa4e1c11",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($body),
    ]);

    

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        $errores[] = [
            'nombre' => $t['nombre'],
            'error' => "Error de cURL: $error",
            'xml' => $xml_enviado
        ];
    } else {
        libxml_use_internal_errors(true);
        $xml_response = simplexml_load_string($response);

        if ($xml_response === false) {
            $msg = "Error al interpretar XML: ";
            foreach (libxml_get_errors() as $err) {
                $msg .= trim($err->message) . " ";
            }
            $errores[] = [
                'nombre' => $t['nombre'],
                'error' => $msg . "Respuesta cruda: " . $response,
                'xml' => $xml_enviado
            ];
        } else {
            if ((string)$xml_response->timbradoOk === 'true') {
                if (!empty($xml_response->xmlBase64)) {
                    $xml_timbrado = base64_decode((string)$xml_response->xmlBase64);
                    file_put_contents("/tmp/nomina_{$folio_periodo}_timbrado.xml", $xml_timbrado);

                    // Obtener UUID y fecha de timbrado del XML timbrado
                    $xml_timbrado_obj = new DOMDocument();
                    $xml_timbrado_obj->loadXML($xml_timbrado);
                    $xpath = new DOMXPath($xml_timbrado_obj);
                    $xpath->registerNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
                    $uuid = '';
                    $fecha_timbrado = '';
                    $tfd = $xpath->query('//tfd:TimbreFiscalDigital')->item(0);
                    if ($tfd) {
                        $uuid = $tfd->getAttribute('UUID');
                        $fecha_timbrado = $tfd->getAttribute('FechaTimbrado');
                    }

                    // Guardar en la base de datos
                    $sql_nomina = "INSERT INTO nomina (
                        periodo_id,
                        folio_periodo,
                        trabajador_id,
                        uuid,
                        xml_timbrado,
                        fecha_timbrado,
                        simples,
                        dobles,
                        triples,
                        total_bonos,
                        total_percepciones,
                        isr,
                        imss,
                        prestamo,
                        fondo_ahorro,
                        infonavit,
                        total_deducciones,
                        total_neto,
                        bonos,
                        premios,
                        vacaciones,
                        isn,
                        csp,
                        sar,
                        imss_patronal,
                        infonavit_patronal,
                        percepcion_empresa,
                        horas_simples,
                        horas_dobles,
                        horas_triples,
                        asistencia,
                        complemento
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    $stmt = $conexion_servicios->prepare($sql_nomina);
                    $stmt->bind_param(
                        "isisssdddddddddddddddddddddddddd",
                        $periodo_id,
                        $folio_periodo,
                        $t['id'],
                        $uuid,
                        $xml_timbrado,
                        $fecha_timbrado,
                        $t['valor_horas_simples'],
                        $t['valor_horas_dobles'],
                        $t['valor_horas_triples'],
                        $t['total_bonos'],
                        $t['percepcion_trabajador'],
                        $t['isr_retencion'],
                        $t['imss'],
                        $t['monto_prestamos'],
                        $t['monto_fondo_ahorro'],
                        $t['monto_infonavit'],
                        $t['deducciones'],
                        $t['neto'],
                        $t['valor_bonos'],
                        $t['valor_premios'],
                        $t['valor_vacaciones'],
                        $t['isn'],
                        $t['csp'],
                        $t['sar'],
                        $t['infonavit_patronal'],
                        $t['imss_patronal'],
                        $t['percepcion_empresa'],
                        $t['horas_simples'],
                        $t['horas_dobles'],
                        $t['horas_triples'],
                        $t['asistencia_nomina'],
                        $t['complemento']
                    );
                    $stmt->execute();
                    $stmt->close();

                    $timbrados[] = $t['nombre'];
                } else {
                    $errores[] = [
                        'nombre' => $t['nombre'],
                        'error' => "Timbrado exitoso, pero no se recibió el XML timbrado.",
                        'xml' => $xml_enviado
                    ];
                }
            } else {
                $errores[] = [
                    'nombre' => $t['nombre'],
                    'error' => "Timbrado fallido: " . (string)$xml_response->mensaje,
                    'xml' => $xml_enviado
                ];
            }
        }
    }
        }
    }

}

// Descargar errores en un txt si hay errores
if (!empty($errores)) {
    $errores_txt = "";
    foreach ($errores as $err) {
        $errores_txt .= "Trabajador: " . $err['nombre'] . "\n";
        $errores_txt .= "Error: " . $err['error'] . "\n";
        $errores_txt .= "XML Enviado:\n" . $err['xml'] . "\n";
        $errores_txt .= "--------------------------\n";
    }
    // Convertir a UTF-8 explícitamente para evitar problemas de caracteres especiales
    $errores_txt_utf8 = mb_convert_encoding($errores_txt, 'UTF-8', 'auto');
    // Ruta local en el servidor (no URL)
    $errores_dir = "/var/www/simsa/documentos/errores_nomina/$periodo_id/";

if (!is_dir($errores_dir)) {
    mkdir($errores_dir, 0777, true);
}

$errores_file = $errores_dir . "errores_nomina_$periodo_id.txt";

// Guardar archivo con BOM UTF-8
file_put_contents($errores_file, "\xEF\xBB\xBF" . $errores_txt_utf8);

    header("Location:" . $_SERVER['HTTP_REFERER'] . "&timbrado=1");
}
else{
    header("Location:" . $_SERVER['HTTP_REFERER'] . "&timbrado=0");
}
