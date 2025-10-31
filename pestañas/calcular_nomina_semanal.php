<head>
    <title>Nómina</title>
    <style>
        .centrado {
            text-align: center;
        }
        .reporte_tabla {
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body id="reporte_casos">
    <div class="contenedor__cronograma">
        <h2>Nómina</h2>

        <div class="reporte_tabla">
            <?php
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

            // Obtener las fechas de los parámetros GET
            $fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : '';
            $fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : '';

            // Verificar si las fechas están presentes
            if (!empty($fecha_inicial) && !empty($fecha_final)) {
               // Asegurarse de que las fechas estén en el formato correcto
                $fecha_inicial_sql = $conexion_transimex->real_escape_string($fecha_inicial);
                $fecha_final_sql = $conexion_transimex->real_escape_string($fecha_final);

                // Obtener información de la empresa y guardar en variables
                $sql_empresa = "SELECT razon_social, nombre_compania, registro_patronal, rfc FROM empresa LIMIT 1";
                $resultado_empresa = $conexion->query($sql_empresa);
                if ($resultado_empresa && $empresa_info = $resultado_empresa->fetch_assoc()) {
                    $razon_social_empresa = $empresa_info['razon_social'];
                    $nombre_compania_empresa = $empresa_info['nombre_compania'];
                    $registro_patronal_empresa = $empresa_info['registro_patronal'];
                    $rfc_empresa = $empresa_info['rfc'];
                } else {
                    $razon_social_empresa = '';
                    $nombre_compania_empresa = '';
                    $registro_patronal_empresa = '';
                    $rfc_empresa = '';
                }


                // Llamar al procedimiento almacenado con las fechas
                $sql = "CALL transimex.reporte_encargado_dinamico('$fecha_inicial_sql', '$fecha_final_sql')";
                $resultado = $conexion_transimex->query($sql);

                // Obtener el objeto DateTime para la fecha final
                $fecha_final_obj = new DateTime($fecha_final);

                $trabajadores = array();
                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        $trabajadores[] = $fila;
                    }
                }

                // Mostrar los resultados
                if ($resultado->num_rows > 0) {
                    echo "<table>";
                    echo "<tr style='position: sticky; top: 0; background-color: #f2f2f2; z-index: 2;'>
                        <th>Periodo</th>
                        <th>Fecha inicial</th>
                        <th>Fecha final</th>

                        <th>ID empleado</th>
                        <th>Nombre</th>
                        <th>Paterno</th>
                        <th>Materno</th>
                        <th>NSS</th>
                        <th>RFC</th>
                        <th>Dirección</th>
                        <th>CURP</th>
                        <th>CP</th>
                        <th>Fecha ingreso</th>
                        <th>Forma de pago</th>
                        <th>Clave bancaria</th>

                        <th>Departamento</th>
                        <th>Puesto</th>
                        <th>Horario</th>

                        <th>Empresa</th>
                        <th>Razon Social</th>
                        <th>RFC empresa</th>
                        <th>Registro patronal</th>
                        <th>Tipo nomina</th>
                        
                        <th>Vacaciones totales</th>
                        <th>Vacaciones usadas</th>
                        
                        <th>Salario diario</th>
                        <th>Hrs simples</th>
                        <th>Hrs dobles</th>
                        <th>Hrs triples</th>
                        <th>Bono asistencia</th>
                        <th>Bono puntualidad</th>
                        <th>Bono despensa</th>
                        <th>Bonos extra</th>
                        
                        <th>Faltas</th>
                        <th>Vacaciones semana</th>
                        <th>Bajas</th>
                        <th>Asistencia</th>
                        <th>UMA</th>
                        <th>Factor</th>
                        <th>SDI</th>
                        <th>Salario hora</th>
                        <th>Concepto simples</th>
                        <th>Simples</th>
                        <th>Concepto dobles</th>
                        <th>Dobles</th>
                        <th>Concepto triples</th>
                        <th>Triples</th>
                        <th>Concepto bonos</th>
                        <th>Bonos</th>
                        <th>Concepto vacaciones</th>
                        <th>Pago vacaciones</th>
                        <th>Concepto incapacidad</th>
                        <th>Pago incapacidad</th>
                        <th>Concepto prestamo</th>
                        <th>Prestamo</th>
                        <th>Concepto infonavit</th>
                        <th>Infonavit</th>
                        <th>Percepciones gravadas</th>
                        <th>Percepciones excentas</th>
                        <th>Limite inferior</th>
                        <th>Excedente</th>
                        <th>Tasa isr</th>
                        <th>Cuota fija</th>
                        <th>Isr excedente</th>
                        <th>Isr a cargo</th>
                        <th>Concepto subsidio</th>
                        <th>Subsidio </th>
                        <th>Concepto isr</th>
                        <th>Isr a retener</th>
                        <th>SBC</th>
                        <th>Excedente SBC</th>
                        <th>Prestaciones</th>
                        <th>Gastos medicos</th>
                        <th>Invalidez y vida</th>
                        <th>Cesantia y vejez</th>
                        <th>Concepto IMSS</th>
                        <th>IMSS</th>
                        <th>Total ingresos</th>
                        <th>Total deducciones</th>
                        <th>Percepcion trabajador</th>
                        <th>Percepcion empresa</th>
                    </tr>";
                    
                     foreach ($trabajadores as $fila) {
                        $faltas= $fila['faltas'];
                        $total_horas = $fila['total_horas'];
                        $id_trabajador = $fila['id'];
                        $retrasos = $fila['retrasos'];
                        $bono_extra = $fila['horas_bonos'];
                        $apellidos = explode(' ', $fila['apellidos'], 3);
                        $apellido_paterno = isset($apellidos[0]) ? $apellidos[0] : '';
                        $apellido_materno = isset($apellidos[1]) ? $apellidos[1] : '';
                        $vacaciones_usadas = $fila['vacaciones'];
                        $vacaciones_sabado = $fila['tomo_vacaciones_sabado'];
                        $prestamo = $fila['prestamo'];
                        $infonavit = $fila['infonavit'];
                        $asistencia=(7-$faltas);
                        $semana_final = date('W', strtotime($fecha_final));
                        $año= date('Y', strtotime($fecha_final));
                        $periodo = $año . str_pad($semana_final, 2, "0", STR_PAD_LEFT) . "0";
                        $salario_diario = $fila['salario_historico'];

                        $total_horas_simples = 0;
                        $total_horas_dobles = 0;
                        $total_horas_triples = 0;
                        $horas_sabado = 0; 
                        $horas_domingo = 0; 
                        $bono_puntualidad = 0;

                        //horas que equivalen las vacaciones entre semana 
                        $hrs_vacaciones = $fila['vacaciones']*8.5;


                        $fecha_actual = new DateTime($fecha_inicial);
                        $valores_columnas = []; // Almacena los valores de cada columna

                        while ($fecha_actual <= $fecha_final_obj) {
                            $fecha_actual_str = $fecha_actual->format('Y-m-d');
                            $horas_trabajadas = isset($fila[$fecha_actual_str]) ? $fila[$fecha_actual_str] : null;

                            // Calcular sabado con vacaciones o media hora extra si cumple los requisitos
                            if ($fecha_actual->format('N') == 6 && $horas_trabajadas > 0) {
                                if ($total_horas >= 47.5 && $horas_sabado > 0 && $faltas == 0 ) {
                                    $total_horas += 0.5;
                                    $horas_trabajadas += 0.5;
                                }
                            }
                            
                            // Verificar si es sábado y se tomaron vacaciones
                            if ($vacaciones_sabado>1) {
                                $horas_sabado = $horas_trabajadas;
                                $hrs_vacaciones -= 3; 
                            }

                            // Verificar si es domingo
                            if ($fecha_actual->format('N') == 7) {
                                $horas_domingo = $horas_trabajadas; // Guardar horas del domingo
                            }

                            // Mostrar las horas trabajadas, vacío si no hay horas registradas
                            $valores_columnas[$fecha_actual_str] = $horas_trabajadas;
                            $fecha_actual->modify('+1 day');
                        }
                        
                        // Calcular horas simples, dobles y triples
                        $horas_simples_restantes = 48;
                        $horas_dobles_restantes = 9 ;
                        
                        // Paso 1: Calcular horas simples, dobles y triples
                        foreach ($valores_columnas as $fecha => $horas) {
                            if ($horas !== null) {
                                if ($fecha != array_search($horas_domingo, $valores_columnas)) {
                                    if ($horas_simples_restantes > 0) {
                                        $horas_simples = min($horas, $horas_simples_restantes);
                                        $total_horas_simples += $horas_simples;
                                        $horas -= $horas_simples;
                                        $horas_simples_restantes -= $horas_simples;
                                    }
                                    if ($horas > 0 && $horas_dobles_restantes > 0) {
                                        $horas_dobles = min($horas, $horas_dobles_restantes);
                                        $total_horas_dobles += $horas_dobles;
                                        $horas -= $horas_dobles;
                                        $horas_dobles_restantes -= $horas_dobles;
                                    }
                                    if ($horas > 0) {
                                        $total_horas_triples += $horas;
                                    }
                                }
                            }
                        }
                        
                        // Paso 2: Restar las horas de retraso del total de horas simples
                        $total_horas_simples -= $retrasos;
                        
                        // Procesar horas del domingo
                        if ($total_horas_simples < 48) {
                            // Calcular las horas restantes para completar 48
                            $restantes = 48 - $total_horas_simples;
                        
                            // Verificar si hay suficientes horas del domingo para cubrir las horas restantes
                            if ($horas_domingo >= $restantes) {
                                // Reducir las horas del domingo y añadirlas a las horas simples
                                $horas_domingo -= $restantes;
                                $total_horas_simples += $restantes;
                            } else {
                                // Si no hay suficientes horas del domingo, usar todas las horas disponibles
                                $total_horas_simples += $horas_domingo;
                                $horas_domingo = 0;
                            }
                        }
                        
                        // Ahora, ajustar las horas del domingo a dobles y triples
                        if ($horas_domingo <= 8) {
                            $total_horas_dobles += $horas_domingo;
                        } else {
                            $total_horas_dobles += 8;
                            $total_horas_triples += $horas_domingo - 8;
                        }
                        
                        // Paso 3: Reacomodar horas si las simples son menores a 48
                        if ($total_horas_simples < 48) {
                            // Mover horas dobles a simples
                            $horas_necesarias = 48 - $total_horas_simples - $hrs_vacaciones ;
                            $horas_a_mover = min($total_horas_dobles, $horas_necesarias);
                        
                            // Ajustar totales
                            $total_horas_simples += $horas_a_mover;
                            $total_horas_dobles -= $horas_a_mover;
                        
                            // Si todavía faltan horas para completar las simples, mover horas de triples a simples
                            $horas_necesarias -= $horas_a_mover;
                            if ($horas_necesarias > 0) {
                                $horas_a_mover = min($total_horas_triples, $horas_necesarias);
                                $total_horas_simples += $horas_a_mover;
                                $total_horas_triples -= $horas_a_mover;
                            }
                        }

                        if ($asistencia==7 && $total_horas>=48 ) {
                            $bono_asistencia=4.8;
                        } else {
                            $bono_asistencia=0;
                        }
                        if ($retrasos == 0 && $asistencia==7 && $total_horas>=48) {
                            $bono_puntualidad = 4.8;
                        } else {
                            $bono_puntualidad =0;
                        }
                        $bono_puntualidad=$bono_puntualidad+$bono_asistencia;

                $fecha_ingreso = new DateTime($fila["fecha_ingreso"]); // Fecha de ingreso desde la base de datos
                $hoy = new DateTime(); 
                $diferencia = $fecha_ingreso->diff($hoy);
                $antiguedad_años = $diferencia->y;
                
                switch (true) {
                    case ($antiguedad_años == 1):
                        $vacaciones = 12;
                        break;
                    case ($antiguedad_años == 2):
                        $vacaciones = 14;
                        break;
                    case ($antiguedad_años == 3):
                        $vacaciones = 16;
                        break;
                    case ($antiguedad_años == 4):
                        $vacaciones = 18;
                        break;
                    case ($antiguedad_años == 5):
                        $vacaciones = 20;
                        break;
                    case ($antiguedad_años >= 6 && $antiguedad_años <= 10):
                        $vacaciones = 22;
                        break;
                    case ($antiguedad_años >= 11 && $antiguedad_años <= 15):
                        $vacaciones = 24;
                        break;
                    case ($antiguedad_años >= 16 && $antiguedad_años <= 20):
                        $vacaciones = 26;
                        break;
                    case ($antiguedad_años >= 21 && $antiguedad_años <= 25):
                        $vacaciones = 28;
                        break;
                    case ($antiguedad_años >= 26 && $antiguedad_años <= 30):
                        $vacaciones = 30;
                        break;
                    case ($antiguedad_años >= 31 && $antiguedad_años <= 35):
                        $vacaciones = 32;
                        break;
                    default:
                        $vacaciones = 0; // O el valor que consideres adecuado para antigüedades fuera de rango.
                        break;
                }
                        $uma=108.57;

                        //Salarios
                        $factor=(($vacaciones*.25+15+365)/365);
                        $sdi=($salario_diario*$factor);
                        $salario_por_hora=(($salario_diario*7) / 48);
                        $salario_hora_doble=($total_horas_dobles*$salario_por_hora*2);
                        $salario_hora_triple=($total_horas_triples*$salario_por_hora*3);

//-----------------------------------------CHECAR ------------------------------------------------------
                        //Esto se hace cuando SIEMPRE se trabajan horas extra y se paga por semana y aumenta un 16% cada hora
                        $salario_por_hora=((($salario_diario / $factor) / 8) * 56 / 48);
                        $salario_hora_doble=($total_horas_dobles*$salario_por_hora*2 * 48 / 56);
                        $salario_hora_simple=($total_horas_simples*$salario_por_hora);
                        $salario_hora_triple=($total_horas_triples*$salario_por_hora*3 * 48 / 56);
//--------------------------------------------------------------------------------------------------------

                        //bonos
                        if ($total_horas_simples>44) {
                            $bono_asistencia=(($salario_hora_simple+$salario_hora_doble+$salario_hora_triple)*0.10);
                            $bono_puntualidad=(($salario_hora_simple+$salario_hora_doble+$salario_hora_triple)*0.10);
                        } else {
                            $bono_asistencia=0;
                            $bono_puntualidad=0;
                        }
                        $bono_despensa=300*($total_horas_simples/48);
                        $bonos=(($bono_extra)*$salario_por_hora)+$bono_despensa+$bono_puntualidad+$bono_asistencia;

                        // Pago vacaciones
                        $pago_vacaciones=($fila['vacaciones']*(1.25*$sdi));
                        $vacaciones_excentas=$fila['vacaciones']*(0.25*$sdi);
                        $vacaciones_gravadas=$fila['vacaciones']*$sdi;

                        /* pago incapacidad OPCIONAL*/
                        $pago_incapacidad=0;
                        
                        $percepciones_gravadas=($salario_hora_simple+($salario_hora_doble/2)+$salario_hora_triple+$bonos+$vacaciones_gravadas)-$prestamo-$infonavit;
                        $percepciones_excentas=($salario_hora_doble/2)+$vacaciones_excentas;  
                        
                        switch (true) {
                        case ($percepciones_gravadas >= 0.01 && $percepciones_gravadas <= 171.78):
                            $limite_inferior = 0.01;
                            $limite_superior = 171.78;
                            $cuota_fija = 0;
                            $porcentaje_excedente = 1.92;
                            break;
                    
                        case ($percepciones_gravadas >= 171.79 && $percepciones_gravadas <= 1458.03):
                            $limite_inferior = 171.79;
                            $limite_superior = 1458.03;
                            $cuota_fija = 3.29;
                            $porcentaje_excedente = 6.40;
                            break;
                    
                        case ($percepciones_gravadas >= 1458.04 && $percepciones_gravadas <= 2562.35):
                            $limite_inferior = 1458.04;
                            $limite_superior = 2562.35;
                            $cuota_fija = 85.61;
                            $porcentaje_excedente = 10.88;
                            break;
                    
                        case ($percepciones_gravadas >= 2562.36 && $percepciones_gravadas <= 2978.63):
                            $limite_inferior = 2562.36;
                            $limite_superior = 2978.63;
                            $cuota_fija = 205.8;
                            $porcentaje_excedente = 16;
                            break;
                    
                        case ($percepciones_gravadas >= 2978.64 && $percepciones_gravadas <= 3566.22):
                            $limite_inferior = 2978.64;
                            $limite_superior = 3566.22;
                            $cuota_fija = 272.37;
                            $porcentaje_excedente = 17.92;
                            break;
                    
                        case ($percepciones_gravadas >= 3566.23 && $percepciones_gravadas <= 7192.64):
                            $limite_inferior = 3566.23;
                            $limite_superior = 7192.64;
                            $cuota_fija = 377.65;
                            $porcentaje_excedente = 21.36;
                            break;
                    
                        case ($percepciones_gravadas >= 7192.65 && $percepciones_gravadas <= 11336.57):
                            $limite_inferior = 7192.65;
                            $limite_superior = 11336.57;
                            $cuota_fija = 1152.27;
                            $porcentaje_excedente = 23.52;
                            break;
                    
                        case ($percepciones_gravadas >= 11336.58 && $percepciones_gravadas <= 14307.55):
                            $limite_inferior = 11336.58;
                            $limite_superior = 14307.55;
                            $cuota_fija = 2674.94;
                            $porcentaje_excedente = 30;
                            break;
                    
                        case ($percepciones_gravadas >= 14307.56 && $percepciones_gravadas <= 21643.3):
                            $limite_inferior = 14307.56;
                            $limite_superior = 21643.3;
                            $cuota_fija = 3884.2;
                            $porcentaje_excedente = 32;
                            break;
                    
                        case ($percepciones_gravadas >= 21643.31 && $percepciones_gravadas <= 28857.78):
                            $limite_inferior = 21643.31;
                            $limite_superior = 28857.78;
                            $cuota_fija = 5218.92;
                            $porcentaje_excedente = 34;
                            break;
                    
                        case ($percepciones_gravadas >= 28857.79 && $percepciones_gravadas <= 86573.35):
                            $limite_inferior = 28857.79;
                            $limite_superior = 86573.35;
                            $cuota_fija = 7527.59;
                            $porcentaje_excedente = 34;
                            break;
                    
                        case ($percepciones_gravadas >= 86573.36):
                            $limite_inferior = 86573.36;
                            $limite_superior = PHP_INT_MAX;
                            $cuota_fija = 27150.83;
                            $porcentaje_excedente = 35;
                            break;
                    
                        default:
                            // Manejar el caso en que las percepciones no se encuentran en ningún rango
                            $limite_inferior = 0;
                            $limite_superior = 0;
                            $cuota_fija = 0;
                            $porcentaje_excedente = 0;
                            break;
                        }
                        
                        $sueldo=$percepciones_gravadas;
                        $excedente_limite=$percepciones_gravadas-$limite_inferior;
                        $isr_excedente=$excedente_limite/100*$porcentaje_excedente;
                        $isr_a_cargo=$isr_excedente+$cuota_fija;
                        
                        if ($sueldo<2091.0){
                             $subsidio=($uma/100*11.82)*7;
                        }else{
                            $subsidio=0;
                        }
                        if ($subsidio>$isr_a_cargo){
                            $subsidio=$isr_a_cargo;
                        }
                        $sbc=($sdi-(3*$uma)*(0.4/100)*$asistencia);
                        $isr_retencion=($isr_a_cargo-$subsidio);
                        if($sbc<0){
                            $excedentesbc=0;
                        }else{
                            $excedentesbc=$sbc;
                        }
                        $prestaciones=(($sdi/100)*0.25*$asistencia);
                        $gastos_medicos=(($sdi/100)*0.375*$asistencia);
                        $invalidez=(($sdi/100)*0.625*$asistencia);
                        $cesantia=(($sdi/100)*1.125*$asistencia);
                        $imss = ($excedentesbc) + ($prestaciones) + ($gastos_medicos) + ($invalidez) + ($cesantia);
                        $ingresos=($salario_hora_simple+$salario_hora_doble+$salario_hora_triple+$bonos+$pago_vacaciones+$subsidio);
                        $deducciones=$prestamo+$infonavit+$isr_retencion;
                        $percepcion_trabajador=$ingresos-$deducciones;
                        $percepcion_empresa=$ingresos+$deducciones+$imss;
                        echo "<tr>";
                            echo "<td>" . $periodo . "</td>";
                            echo "<td>" . $fecha_inicial . "</td>";
                            echo "<td>" . $fecha_final . "</td>";
                            echo "<td>" . $fila['id'] . "</td>";
                            echo "<td style='position: sticky; left: 0; background-color: #f2f2f2; z-index: 1;'>" . $fila['nombre'] . "</td>";
                            echo "<td>" . $apellido_paterno . "</td>";
                            echo "<td>" . $apellido_materno . "</td>";
                            echo "<td>" . $fila['nss'] . "</td>";
                            echo "<td>" . $fila['rfc'] . "</td>";
                            echo "<td>" . $fila['direccion'] . "</td>";
                            echo "<td>" . $fila['curp'] . "</td>";
                            echo "<td>" . $fila['codigo_postal'] . "</td>";
                            echo "<td>" . $fila['fecha_ingreso'] . "</td>";
                            echo "<td>" . $fila['forma_de_pago'] . "</td>";
                            echo "<td>" . $fila['clave_bancaria'] . "</td>";

                            echo "<td>" . $fila['area'] . "</td>";
                            echo "<td>" . $fila['puesto'] . "</td>";
                            echo "<td>Matutino</td>";

                            echo "<td>" . $fila['empresa'] . "</td>";
                            echo "<td>" . $razon_social_empresa . "</td>";
                            echo "<td>" . $rfc_empresa . "</td>";
                            echo "<td>" . $registro_patronal_empresa . "</td>";
                            echo "<td>Semanal</td>";
                            
                            echo "<td>" . ($vacaciones) . "</td>";
                            echo "<td>" . ($vacaciones_usadas) . "</td>";
                            echo "<td> $" . $salario_diario . "</td>";
                            echo "<td>" . $total_horas_simples. "</td>";
                            echo "<td>" . $total_horas_dobles  . "</td>";
                            echo "<td>" . $total_horas_triples  . "</td>";
                            echo "<td>" . $bono_asistencia  . "</td>";
                            echo "<td>" . $bono_puntualidad  . "</td>";
                            echo "<td> $" . $bono_despensa  . "</td>";
                            echo "<td>" . $bono_extra  . "</td>";
                            echo "<td>" . $fila['faltas'] . "</td>";
                            echo "<td>" . $fila['vacaciones'] . "</td>";
                            echo "<td>" . $fila['incapacitacion'] . "</td>";
                            echo "<td>" . $asistencia . "</td>";
                            echo "<td>" . $uma . "</td>";
                            echo "<td>" . $factor . "</td>";
                            echo "<td>" . $sdi . "</td>";
                            echo "<td> $" . $salario_por_hora . "</td>";
                            echo "<td>000001</td>";
                            echo "<td> $" . $salario_hora_simple . "</td>";
                            echo "<td>000019</td>";
                            echo "<td> $" . $salario_hora_doble . "</td>";
                            echo "<td>000019</td>";
                            echo "<td> $" . $salario_hora_triple . "</td>";
                            echo "<td>000002</td>";
                            echo "<td> $" . $bonos . "</td>";
                            echo "<td>000004</td>";
                            echo "<td> $" . $pago_vacaciones . "</td>";
                            echo "<td>000005</td>";
                            echo "<td> $" . $pago_incapacidad . "</td>";
                            echo "<td>000003</td>";
                            echo "<td> $" . $prestamo . "</td>";
                            echo "<td>000002</td>";
                            echo "<td> $" . $infonavit . "</td>";
                            echo "<td> $" . $percepciones_gravadas . "</td>";
                            echo "<td> $" . $percepciones_excentas . "</td>";
                            echo "<td> $" .$limite_inferior. "</td>";
                            echo "<td> $" .$excedente_limite. "</td>";
                            echo "<td>" .$porcentaje_excedente. "% </td>";
                            echo "<td> $" .$cuota_fija. "</td>";
                            echo "<td> $" .$isr_excedente. "</td>";
                            echo "<td> $" .$isr_a_cargo. "</td>";
                            echo "<td>000002</td>";
                            echo "<td> $" .$subsidio. "</td>";
                            echo "<td>000001</td>";
                            echo "<td> $" .$isr_retencion. "</td>";
                            echo "<td>" .$sbc. "</td>";
                            echo "<td>" .$excedentesbc. "</td>";
                            echo "<td> $" .$prestaciones. "</td>";
                            echo "<td> $" .$gastos_medicos. "</td>";
                            echo "<td> $" .$invalidez. "</td>";
                            echo "<td> $" .$cesantia. "</td>";
                            echo "<td>000001</td>";
                            echo "<td> $" .$imss. "</td>";
                            echo "<td> $" .$ingresos. "</td>";
                            echo "<td> $" .$deducciones. "</td>";
                            echo "<td> $" .$percepcion_trabajador. "</td>";
                            echo "<td> $" .$percepcion_empresa. "</td>";
                             
                        echo "</tr>";
            }
        } else {
            echo "No se encontraron trabajadores que trabajaran en esos días.";
        }
            }
            ?>
            </div>
            <div>
            <form method="GET" action="../php/guardar_nomina.php">
                <input type="hidden" id="fecha_inicial" name="fecha_inicial" value="<?php echo $fecha_inicial; ?>">
                <input type="hidden" id="fecha_final" name="fecha_final" value="<?php echo $fecha_final; ?>">
                <input type="submit" value="Guardar Nómina">
            </form>
            
        </div>