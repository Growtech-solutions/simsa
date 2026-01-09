<?php
include '../conexion_servicios.php';
include 'procedimiento_asistencia.php';
$sql_periodo = "SELECT * FROM periodo WHERE id = $periodo_id";
$result_periodo = $conexion_servicios->query($sql_periodo);
$periodo = $result_periodo ? $result_periodo->fetch_assoc() : null;
$fecha_inicio = $periodo['fecha_inicio'] ?? null;
$fecha_fin = $periodo['fecha_fin'] ?? null;
$dias_en_rango = (new DateTime($fecha_inicio))->diff(new DateTime($fecha_fin))->days + 1;
$tipo_nomina = $periodo['tipo'];
$clave_tipo_nomina = $periodo['clave_tipo_nomina'];
$PeriodicidadPago = $periodo['periodicidad_pago'];
$folio_periodo = $periodo['id'];

//Datos Emisor
$sql_perfil_fiscal = "SELECT * FROM perfil_fiscal";
$result_perfil_fiscal = $conexion_servicios->query($sql_perfil_fiscal);
$emisor = $result_perfil_fiscal ? $result_perfil_fiscal->fetch_assoc() : null;
$rfc_emisor = $emisor['rfc'];
$razon_social_emisor = $emisor ? $emisor['razon_social'] : '';
$regimen_fiscal_emisor = $emisor ? $emisor['regimen_fiscal'] : '';
$ruta_certificado = $emisor ? $emisor['ruta_cer'] : '';
$ruta_key = $emisor ? $emisor['ruta_key'] : '';
$password_key = $emisor ? $emisor['password_key'] : '';
$codigo_postal_emisor = $emisor ? $emisor['codigo_postal'] : '';
$registro_patronal_emisor = $emisor ? $emisor['registro_patronal'] : '';
$curp_emisor = $emisor ? $emisor['CURP'] : '';

//UMA
$uma = 113.14;

//
$salario_minimo = 278.80;

//ISN
$isn_tasa = 0.03; 
//Riesgo de trabajo
$riesgo_trabajo = 0.00522; // Nivel II

// Inicialización de totales
$total_empleados = 0;
$total_percepciones = 0;
$total_deducciones = 0;
$total_neto = 0;
$prima_vacacional_recibida = 0;
$percepciones_exentas = 0;
$sbc = 0;

// Datos de los trabajadores
$sql_trabajador = "SELECT * FROM trabajadores WHERE estado = 'Activo' AND contrato = '$tipo_nomina' AND fecha_ingreso <= '$fecha_fin' AND empresa = 'simsa'";
$result_trabajadores = $conexion_transimex->query($sql_trabajador);

while ($trab = $result_trabajadores->fetch_assoc()) {
    $id_trabajador = $trab['id'];
    $nombre_trabajador = $trab['nombre'] . ' ' . $trab['apellidos'];
     $nombre_trabajador = mb_convert_case($nombre_trabajador, MB_CASE_UPPER, 'UTF-8');
    $nombre_trabajador = preg_replace('/[áàäâ]/ui', 'A', $nombre_trabajador);
    $nombre_trabajador = preg_replace('/[éèëê]/ui', 'E', $nombre_trabajador);
    $nombre_trabajador = preg_replace('/[íìïî]/ui', 'I', $nombre_trabajador);
    $nombre_trabajador = preg_replace('/[óòöô]/ui', 'O', $nombre_trabajador);
    $nombre_trabajador = preg_replace('/[úùüû]/ui', 'U', $nombre_trabajador);
    $curp_trabajador = $trab['curp'];
    $rfc_trabajador = $trab['rfc'];
    $nss_trabajador = $trab['nss'];
    $cp_trabajador = $trab['codigo_postal'];
    $puesto_trabajador = $trab['puesto'];
    $departamento_trabajador = $trab['area'];
    $sql_historial_salarios = "SELECT * FROM historial_salarios WHERE fecha_cambio <= '$fecha_fin' AND id_trabajador = $id_trabajador ORDER BY fecha_cambio DESC";
    $result_historial_salarios = $conexion_transimex->query($sql_historial_salarios);
    $sueldo_diario_base = $result_historial_salarios ? $result_historial_salarios->fetch_assoc()['valor_actual'] : 0;
    $tipo_contrato_trabajador = $trab['tipo_contrato'];
    $regimen_fiscal_trabajador = $trab['regimen_fiscal'];
    $id_turno = $trab['turno'];
    $clave_entidad_fed = $trab['clave_entidad_fed'];
    $sql_jornada = "SELECT tipo_jornada FROM turnos WHERE id_turno = $id_turno";
    $jornada_trabajador = $conexion_transimex->query($sql_jornada)->fetch_assoc()['tipo_jornada'];
    $ingreso = $trab['fecha_ingreso'];
    $antiguedad = (new DateTime($ingreso))->diff(new DateTime($fecha_fin))->y;
    $diff = (new DateTime($ingreso))->diff(new DateTime($fecha_fin));
    $diff_number = $diff->y + ($diff->m / 12) + ($diff->d / 365);
    $diff_number = round($diff_number, 2);
    $antiguedad_nomina = "P";
    if ($diff->y > 0) {
        $antiguedad_nomina .= $diff->y . "Y";
    }
    if ($diff->m > 0) {
        $antiguedad_nomina .= $diff->m . "M";
    }
    if ($diff->d > 0) {
        $antiguedad_nomina .= $diff->d . "D";
    }
    else{
        $antiguedad_nomina .= "0D";
    }
    if ($antiguedad_nomina === "P") {
        $antiguedad_nomina .= "0D";
    }

    $asistencias = obtener_asistencia_trabajador($conexion_transimex, $fecha_inicio, $fecha_fin, $id_trabajador);

    // Indexar por fecha para fácil acceso
    $registro = [];
    foreach ($asistencias as $asistencia) {
        $registro[$asistencia['fecha']] = array_merge($asistencia, [
            'id_trabajador' => $id_trabajador,
        ]);
    }

    // Cuenta de casos
    $total_bonos_asistencias = array_sum(array_column($registro, 'bonos'));
    $vacaciones_en_semana = 0;
    $faltas_en_semana = 0;
    $incapacidades_en_semana = 0;
    $suspenciones_en_semana = 0;
    $dias_descanso_en_semana = 0;
    $horas_simples = 0;
    $banco_horas = 0;
    $total_horas = 0;
    $doble_reporte = 0;

    foreach ($registro as $asistencia) {
        /* LFT Artículo 76 – Vacaciones */
    switch (true) {
        case ($antiguedad == 0): $total_vacaciones = 12; break;
        case ($antiguedad == 1): $total_vacaciones = 14; break;
        case ($antiguedad == 2): $total_vacaciones = 16; break;
        case ($antiguedad == 3): $total_vacaciones = 18; break;
        case ($antiguedad == 4): $total_vacaciones = 20; break;
        case ($antiguedad >= 5 && $antiguedad <= 10): $total_vacaciones = 22; break;
        case ($antiguedad >= 10 && $antiguedad <= 15): $total_vacaciones = 24; break;
        case ($antiguedad >= 15 && $antiguedad <= 20): $total_vacaciones = 26; break;
        case ($antiguedad >= 20 && $antiguedad <= 25): $total_vacaciones = 28; break;
        case ($antiguedad >= 25 && $antiguedad <= 30): $total_vacaciones = 30; break;
        case ($antiguedad >= 30 && $antiguedad <= 35): $total_vacaciones = 32; break;
        default: $total_vacaciones = 0; break;
    }
    /* SDI con factor de integración */
    $dias_aguinaldo = 15;
    $factor_integracion = (365 + $dias_aguinaldo + ($total_vacaciones * 0.25)) / 365;
    $sdi = $sueldo_diario_base * $factor_integracion;
    $limite_sbc = 25*$uma;
    if ($sbc > $limite_sbc){
        $sbc = $limite_sbc;
    }else{
        $sbc = $sdi;
    }
    

        if ($jornada_trabajador == 'Mixta') {
            $sueldo_hora_base = ($sueldo_diario_base/7.5);
        } 
        else if ($jornada_trabajador == 'Nocturna') {
            $sueldo_hora_base = ($sueldo_diario_base/7);
        }
        else{
            $sueldo_hora_base = ($sueldo_diario_base/8);
        }
        $sueldo_hora_base = ($sueldo_diario_base/$factor_integracion/8)*(56/48);
        
        if (isset($asistencia['estado'])) {
            switch ($asistencia['estado']) {
                case 'V': $vacaciones_en_semana++; break;
                case 'F': $faltas_en_semana++; break;
                case 'I': $incapacidades_en_semana++; break;
                case 'S': $suspenciones_en_semana++; break;
                case 'D': $dias_descanso_en_semana++; break;
                case 'DR': $doble_reporte++; break;
            }
        }

        if ($asistencia['tipo_calculo'] == 'Reloj') {
            $banco_horas = $asistencia['banco_reloj'];
            $total_horas += $asistencia['horas_reloj'] ?? 0;
        } elseif ($asistencia['tipo_calculo'] == 'Reporte') {
            $banco_horas = $asistencia['banco_reporte'];
            $total_horas += $asistencia['horas_reporte'] ?? 0;
        } elseif ($asistencia['tipo_calculo'] == 'Automatico') {
            $banco_horas = 0;
            $total_horas += $asistencia['horas_turno_dia'] ?? 0;
            $faltas_en_semana = 0;
        }
    }

    $asistencia_nomina = count($registro) - $faltas_en_semana - $suspenciones_en_semana;

    if ($banco_horas > 0) {
        $horas_simples = ($asistencia['horas_total_turno']);
    }
    else{
        $horas_simples = ($asistencia['horas_total_turno']) + $banco_horas;
        if ($horas_simples<0){
            $horas_simples=0;
        }
    }

    if ($asistencia['tipo_calculo'] == 'Automatico') {
        $horas_simples = ($asistencia['horas_total_turno']);
    }

    if ($banco_horas > 9) {
        $horas_dobles = 9;
        $horas_triples = $banco_horas - 9;
    } 
    else {
        $horas_dobles = $banco_horas;
        if ($horas_dobles < 0) {
            $horas_dobles = 0;
        }
        $horas_triples = 0;
    }
    $horas_triples = 0;
    $horas_dobles = ceil($horas_dobles);
    /* Prima vacacional */
    $valor_vacaciones = $vacaciones_en_semana * ($sueldo_diario_base);
    $prima_vacacional = $valor_vacaciones * 0.25;
    $valor_vacaciones += $prima_vacacional;

    $sql_vacaciones_recibidas = "SELECT id_trabajador, SUM(dias_solicitados) AS dias_tomados
                                 FROM vacaciones 
                                 WHERE id_trabajador = $id_trabajador
                                 AND estado = 'aprobado'
                                 AND YEAR(fecha_inicio) = YEAR(CURDATE())
                                 AND fecha_inicio <= '$fecha_inicio'";
    $resultado_vacaciones = $conexion_transimex->query($sql_vacaciones_recibidas);
    $dias_vacaciones_tomados = ($resultado_vacaciones->num_rows > 0) ? $resultado_vacaciones->fetch_assoc()['dias_tomados'] :
    $prima_vacacional_recibida = $dias_vacaciones_tomados * $sueldo_diario_base * 0.25;
    if ($prima_vacacional_recibida + $prima_vacacional > $uma*15 ){
        $prima_vacacional_gravada = ($prima_vacacional + $prima_vacacional_recibida) - ($uma*15);
        $prima_vacacional_exenta = $uma*15;
    }
    else{
        $prima_vacacional_exenta = $prima_vacacional + $prima_vacacional_recibida;
        $prima_vacacional_gravada = 0;
    
    }

    //Horas
    $valor_horas_simples = round($sueldo_hora_base * $horas_simples, 2);
    $valor_horas_dobles = round($sueldo_hora_base * 48/56 * $horas_dobles * 2, 2);
    $valor_horas_triples = round($sueldo_hora_base * 48/56 * $horas_triples * 3, 2);
    $valor_horas_triples = 0;

    $base_bono = $valor_horas_simples ;
    $bono_asistencia = 0;
    $bono_puntualidad = 0;
    $despensa = 0;
    $despensa = 300*$horas_simples/48;
    if ($horas_simples>= 44 && $faltas_en_semana == 0){
            $bono_asistencia = $base_bono*0.1;
            $bono_puntualidad = $base_bono*0.1;
            
    }
    
    $valor_bonos = $bono_asistencia + $bono_puntualidad + $despensa;
    $valor_premios = $sueldo_hora_base * $total_bonos_asistencias;
    $total_bonos = $valor_bonos + $valor_premios;

    //Infonavit
    $sql_infonavit = "SELECT monto as monto_semanal FROM infonavit WHERE id_trabajador = $id_trabajador AND estado = 1";
    $resultado_infonavit = $conexion_transimex->query($sql_infonavit);
    $monto_infonavit = ($resultado_infonavit->num_rows > 0) ? $resultado_infonavit->fetch_assoc()['monto_semanal'] : 0;
    $monto_infonavit = $monto_infonavit/7*$dias_en_rango;

    //Prestamos
    $sql_prestamos = "SELECT monto_semanal FROM prestamos WHERE id_trabajador = $id_trabajador AND fecha_final >= '$fecha_fin'";
    $resultado_prestamos = $conexion_transimex->query($sql_prestamos);
    $monto_prestamos = ($resultado_prestamos->num_rows > 0) ? $resultado_prestamos->fetch_assoc()['monto_semanal'] : 0;
    $monto_prestamos = $monto_prestamos/7*$dias_en_rango;

    // Fondo de ahorro
    $sql_fondo_ahorro = "SELECT fondo_ahorro_trabajador.monto_semanal, fondo_ahorro.fecha_final 
        FROM fondo_ahorro_trabajador 
        INNER JOIN fondo_ahorro ON fondo_ahorro_trabajador.id_fondo = fondo_ahorro.id_fondo 
        WHERE fondo_ahorro_trabajador.id_trabajador = $id_trabajador 
        AND fondo_ahorro.fecha_final >= '$fecha_fin'";
    $resultado_fondo_ahorro = $conexion_transimex->query($sql_fondo_ahorro);
    $monto_fondo_ahorro = ($resultado_fondo_ahorro && $resultado_fondo_ahorro->num_rows > 0) ? $resultado_fondo_ahorro->fetch_assoc()['monto_semanal'] : 0;
    $monto_fondo_ahorro = $monto_fondo_ahorro/7*$dias_en_rango;

    //Percepciones Gravadas y Exentas
    if ($sueldo_diario_base >= $salario_minimo){
        if ($valor_horas_dobles/2 <= ($uma*5)){
            $dobles_exentas = $valor_horas_dobles/2;
            $dobles_gravadas = $valor_horas_dobles/2;
        }
        else{
            $dobles_exentas = $uma*5;
            $dobles_gravadas = $valor_horas_dobles - ($uma*5);
        }
    }
    else{
        $dobles_exentas = $valor_horas_dobles;
        $dobles_gravadas = 0;
    }
    $dobles_exentas = round($dobles_exentas, 2);
    $dobles_gravadas = round($dobles_gravadas, 2);
    $valor_horas_dobles = round($dobles_exentas + $dobles_gravadas, 2);
    $percepciones_exentas = round($dobles_exentas + $prima_vacacional_exenta, 2);
    $percepciones_gravadas = round($valor_horas_simples + $dobles_gravadas + $valor_horas_triples + ($valor_vacaciones-$prima_vacacional) + $prima_vacacional_gravada + $total_bonos, 2);

    //Calculo de ISR y subsidio
    switch ($PeriodicidadPago) {
        case '02': // Semanal
            include 'isr_semanal.php';
            break;
        case '03': // Catorcenal
            include 'isr_catorcenal.php';
            break;
        case '04': // Quincenal
            include 'isr_quincenal.php';
            break;
        case '05': // Mensual
            include 'isr_mensual.php';
            break;
    }

    

    /*IMSS empleado*/
    $adicional=($sbc-(3*$uma))*($asistencia_nomina*0.0040);
    $prestaciones_dinero=$sbc*$asistencia_nomina*0.0025;
    $gastos_medicos_pensionados=$sbc*$asistencia_nomina*0.00375;
    $invalidez_vida=$sbc*$asistencia_nomina*0.00625;
    $cesnatia_vejez=$sbc*$asistencia_nomina*0.01125;
    $imss=$adicional+$prestaciones_dinero+$gastos_medicos_pensionados+$invalidez_vida+$cesnatia_vejez;
        $imss = 0; // Descuento del 50% para el sector servicios
    

    /*IMSS patronal*/
    $cuota_fija=$uma*$asistencia_nomina*0.204;
    $adicional=($sbc-(3*$uma))*($asistencia_nomina*0.0110);
    $prestaciones_dinero=$sbc*$asistencia_nomina*0.0070;
    $gastos_medicos_pensionados=$sbc*$asistencia_nomina*0.0105;
    $riesgo_trabajo_patronal=$sbc*$asistencia_nomina*$riesgo_trabajo;
    $invalidez_vida=$sbc*$asistencia_nomina*0.0175;
    $guarderias=$sbc*$asistencia_nomina*0.0625;
    $retiro=$sbc*$asistencia_nomina*0.02;
    $cesnatia_vejez=$sbc*$asistencia_nomina*0.05747;
    $imss_patronal=$cuota_fija+$adicional+$prestaciones_dinero+$gastos_medicos_pensionados+$riesgo_trabajo_patronal+$invalidez_vida+$guarderias+$retiro+$cesnatia_vejez;

    /*Infonavit patronal*/
    $infonavit_patronal=$sbc*$asistencia_nomina*0.05;

    $csp = $imss_patronal + $infonavit_patronal;
    $sar = $retiro;

    // Totales trabajador
    $sueldo_trabajador = round($valor_horas_simples + $valor_horas_dobles + $valor_horas_triples, 2);
    $otros_pagos = round($total_bonos + $valor_vacaciones, 2);
    
    $percepcion_trabajador = ($sueldo_trabajador + $otros_pagos);
    $percepcion_trabajador = round($percepcion_trabajador, 2);

    $deducciones = $monto_prestamos + $monto_infonavit + $isr_retencion + $imss + $monto_fondo_ahorro;
    $deducciones = round($deducciones, 2);

    $neto_trabajador = $percepcion_trabajador - $deducciones;

    if($neto_trabajador<0){
        $neto_trabajador = 0;
    }
    $isn=$percepcion_trabajador*$isn_tasa;
    $percepcion_empresa = $percepcion_trabajador + $imss_patronal + $infonavit_patronal;

    // Acumulados
    $total_empleados++;
    $total_percepciones += round($percepcion_trabajador, 2);
    $total_deducciones += round($deducciones, 2);
    $total_neto += round($neto_trabajador, 2);
    
    $trabajadores_nomina[] = [
        'id' => $id_trabajador,
        'nombre' => $nombre_trabajador,
        'curp' => $curp_trabajador,
        'rfc' => $rfc_trabajador,
        'nss' => $nss_trabajador,
        'cp' => $cp_trabajador,
        'clave_entidad_fed' => $clave_entidad_fed,
        'puesto' => $puesto_trabajador,
        'departamento' => $departamento_trabajador,
        'sueldo_diario_base' => round($sueldo_diario_base, 2),
        'sueldo_hora_base' => round($sueldo_hora_base, 2),
        'regimen_fiscal' => $regimen_fiscal_trabajador,
        'tipo_contrato' => $tipo_contrato_trabajador,
        'jornada' => $jornada_trabajador,
        'doble_reporte' => $doble_reporte,
        'fecha_ingreso' => $ingreso,
        'antiguedad' => $antiguedad,
        'antiguedad_nomina' => $antiguedad_nomina,
        'asistencia_nomina' => $asistencia_nomina,
        'vacaciones_en_semana' => $vacaciones_en_semana,
        'faltas_en_semana' => $faltas_en_semana,
        'incapacidades_en_semana' => $incapacidades_en_semana,
        'suspenciones_en_semana' => $suspenciones_en_semana,
        'dias_descanso_en_semana' => $dias_descanso_en_semana,
        'total_horas' => round($total_horas, 2),
        'horas_simples' => round($horas_simples, 2),
        'horas_dobles' => round($horas_dobles, 2),
        'horas_triples' => round($horas_triples, 2),
        'banco_horas' => round($banco_horas, 2),
        'total_bonos_asistencias' => round($total_bonos_asistencias, 2),
        'bono_asistencia' => round($bono_asistencia, 2),
        'bono_puntualidad' => round($bono_puntualidad, 2),
        'despensa' => round($despensa, 2),
        'valor_bonos' => round($valor_bonos, 2),
        'valor_premios' => round($valor_premios, 2),
        'total_bonos' => round($total_bonos, 2),
        'valor_vacaciones' => round($valor_vacaciones, 2),
        'valor_horas_simples' => round($valor_horas_simples, 2),
        'valor_horas_dobles' => round($valor_horas_dobles, 2),
        'valor_horas_triples' => round($valor_horas_triples, 2),
        'monto_infonavit' => round($monto_infonavit, 2),
        'monto_prestamos' => round($monto_prestamos, 2),
        'monto_fondo_ahorro' => round($monto_fondo_ahorro, 2),
        'dobles_exentas' => round($dobles_exentas, 2),
        'dobles_gravadas' => round($dobles_gravadas, 2),
        'percepciones_gravadas' => round($percepciones_gravadas, 2),
        'percepciones_exentas' => round($percepciones_exentas, 2),
        'isr_a_cargo' => round($isr_a_cargo, 2),
        'subsidio' => round($subsidio, 2),
        'isr_retencion' => round($isr_retencion, 2),
        'imss' => round($imss, 2),
        'percepcion_trabajador' => round($percepcion_trabajador, 2),
        'deducciones' => round($deducciones, 2),
        'neto' => round($neto_trabajador, 2),
        'sdi' => round($sdi, 2),
        'sbc' => round($sbc, 2),
        'total_vacaciones' => round($total_vacaciones, 2),
        'isn' => round($isn, 2),
        'csp' => round($csp, 2),
        'sar' => round($sar, 2),
        'imss_patronal' => round($imss_patronal, 2),
        'infonavit_patronal' => round($infonavit_patronal, 2),
        'percepcion_empresa' => round($percepcion_empresa, 2)
    ];
}
$resultado_nomina = [
    'total_empleados' => $total_empleados,
    'total_percepciones' => round($total_percepciones, 2),
    'total_deducciones' => round($total_deducciones, 2),
    'total_neto' => round($total_neto, 2)
];
/*
?>
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <?php
            if (!empty($trabajadores_nomina)) {
                foreach (array_keys($trabajadores_nomina[0]) as $col) {
                    echo "<th>" . htmlspecialchars($col) . "</th>";
                }
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($trabajadores_nomina as $trabajador): ?>
            <tr>
                <?php foreach ($trabajador as $valor): ?>
                    <td><?php echo htmlspecialchars($valor); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
*/