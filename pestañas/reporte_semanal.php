<?php
// Función para obtener el próximo miércoles más cercano
function obtener_proximo_miercoles() {
    $hoy = new DateTime();
    $dia_semana = $hoy->format('w');
    
    // Si hoy es miércoles
    if ($dia_semana == 3) {
        return $hoy;
    }

    // Calcular la diferencia para llegar al próximo miércoles
    $diferencia = (3 - $dia_semana + 7) % 7;
    $proximo_miercoles = $hoy->add(new DateInterval('P' . $diferencia . 'D'));
    return $proximo_miercoles;
}

// Función para obtener el jueves anterior al próximo miércoles más cercano
function obtener_jueves_anterior_al_proximo_miercoles() {
    $proximo_miercoles = obtener_proximo_miercoles();
    $jueves_anterior = $proximo_miercoles->sub(new DateInterval('P6D'));
    return $jueves_anterior->format('Y-m-d');
}

// Establecer las fechas por defecto
$fecha_inicial_default = obtener_jueves_anterior_al_proximo_miercoles();
$fecha_final_default = obtener_proximo_miercoles()->format('Y-m-d');

// Obtener las fechas de los parámetros GET o usar las fechas por defecto
$fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : $fecha_inicial_default;
$fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : $fecha_final_default;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal</title>
</head>

<body id="reporte_semanal">
    <div class="">
        <div class="contenedor__cronograma">
            <h2 class="text-2xl font-bold text-blue-600 text-center">Reporte</h2>
            <br>
            <form class="reporte_formulario" method="GET" action="">
                <label for="fecha">Fecha Inicial:</label>
                <input class="formulario_reporte_fecha" type="date" id="fecha_inicial" name="fecha_inicial" value="<?php echo $fecha_inicial; ?>">
                <label for="fecha">Fecha Final:</label>
                <input class="formulario_reporte_fecha" type="date" id="fecha_final" name="fecha_final" value="<?php echo $fecha_final; ?>">
                <input type="hidden" name="header_loc" value=<?php $header_loc ?>>
                <input type="hidden" name="pestaña" value="reporte_semanal">
                <input type="submit" value="Generar Reporte">
            </form>
            <div class="reporte_tabla">
                <?php
                // Definir las variables de los filtros
                $fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : '';
                $fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : '';

                // Comprobar si las fechas están vacías
                if (empty($fecha_inicial) || empty($fecha_final)) {
                    echo "Seleccione intervalo de fechas <br>";
                    exit(); // Salir del script si las fechas están vacías
                }

                // Asegurarse de que las fechas estén en el formato correcto
                $fecha_inicial_sql = $conexion_transimex->real_escape_string($fecha_inicial);
                $fecha_final_sql = $conexion_transimex->real_escape_string($fecha_final);

                // Llamar al procedimiento almacenado con las fechas
                $sql = "CALL transimex.reporte_encargado_dinamico('$fecha_inicial_sql', '$fecha_final_sql')";
                $resultado = $conexion_transimex->query($sql);

                // Obtener el objeto DateTime para la fecha final
                $fecha_final_obj = new DateTime($fecha_final);

                // Almacenar los resultados en una matriz
                $trabajadores = array();
                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        $trabajadores[] = $fila;
                    }
                }

                // Mostrar los resultados
                if (count($trabajadores) > 0) {
                    echo "<table border='1'>";
                    echo "<tr>
                    <th class='desktop-only'>ID</th>
                    <th class='desktop-only'>Empresa</th>
                    <th>Nombre</th>";

                    // Array to map English day names to Spanish equivalents
                    $day_names_es = array(
                        'Monday' => 'Lunes',
                        'Tuesday' => 'Martes',
                        'Wednesday' => 'Miércoles',
                        'Thursday' => 'Jueves',
                        'Friday' => 'Viernes',
                        'Saturday' => 'Sábado',
                        'Sunday' => 'Domingo'
                    );

                    $fecha_actual = new DateTime($fecha_inicial);
                    while ($fecha_actual <= $fecha_final_obj) {
                        $nombre_dia = $day_names_es[$fecha_actual->format('l')]; // Get the Spanish name of the day
                        echo "<th>" . $nombre_dia . "</th>";
                        $fecha_actual->modify('+1 day');
                    }
                    echo "<th class='desktop-only'>Retardos</th>";
                    echo "<th class='desktop-only'>Total Horas</th>"; // Add a column for the total
                    echo "<th class='desktop-only'>Horas Simples</th>";
                    echo "<th class='desktop-only'>Horas Dobles</th>";
                    echo "<th class='desktop-only'>Horas Triples</th>";
                    echo "<th class='desktop-only'>Premio puntualidad</th>";
                    echo "<th class='desktop-only'>Premios extra</th>";
                    echo "<th class=''>Faltas</th>";
                    echo "<th class='desktop-only'>Faltas justificadas</th>";
                    echo "<th class='desktop-only'>Vacaciones</th>";
                    echo "<th class='desktop-only'>Incapacidad</th>";
                    echo "<th class='desktop-only'>Bajas</th>";
                    echo "</tr>";

                    foreach ($trabajadores as $fila) {
                        echo "<tr>";
                        echo "<td class='desktop-only'>" . $fila['id'] . "</td>";
                        echo "<td class='desktop-only'>" . $fila['empresa'] . "</td>";
                        echo "<td>" . $fila['nombre_trabajador'] . "</td>";

                        // Inicializar variables para el cálculo de horas
                        $faltas= $fila['faltas'];
                        $total_horas = $fila['total_horas'];
                        $id_trabajador = $fila['id'];
                        $retrasos = $fila['retrasos'];
                        $bono_extra = $fila['horas_bonos'];
                        $vacaciones_sabado = $fila['tomo_vacaciones_sabado'];
                        $total_horas_simples = 0;
                        $total_horas_dobles = 0;
                        $total_horas_triples = 0;
                        $horas_sabado = 0; 
                        $horas_domingo = 0; 
                        $bono_puntualidad = 0;
                        $hrs_vacaciones = $fila['vacaciones']*8.5;
                        $asistencia=(7-$faltas);
                        
                        
                        $fecha_actual = new DateTime($fecha_inicial);
                        $valores_columnas = []; // Almacena los valores de cada columna

                        while ($fecha_actual <= $fecha_final_obj) {
                            $fecha_actual_str = $fecha_actual->format('Y-m-d');
                            $horas_trabajadas = isset($fila[$fecha_actual_str]) ? $fila[$fecha_actual_str] : null;

                            // Calcular sabado con vacaciones o media hora extra si cumple los requisitos
                            if ($fecha_actual->format('N') == 6 && $horas_trabajadas > 0) {
                                if ($total_horas >= 47.5 && $faltas == 0 ) {
                                    $total_horas += 0.5;
                                    $horas_trabajadas += 0.5;
                                }
                            }
                            
                            // Verificar si es sábado y se tomaron vacaciones
                            if ($vacaciones_sabado>=1) {
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
                        
                        // Mostrar los valores en las celdas
                        foreach ($valores_columnas as $valor) {
                            echo "<td>" . ($valor !== null ? $valor : "") . "</td>";
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

                        echo "<td class='desktop-only'>" . $retrasos . "</td>";
                        echo "<td class='desktop-only'>" . $total_horas . "</td>"; // Mostrar el total de horas
                        echo "<td class='desktop-only'>" . $total_horas_simples . "</td>"; // Mostrar el total de horas simples
                        echo "<td class='desktop-only'>" . $total_horas_dobles . "</td>"; // Mostrar el total de horas dobles
                        echo "<td class='desktop-only'>" . $total_horas_triples . "</td>"; // Mostrar el total de horas triples
                        echo "<td class='desktop-only'>" . ($bono_puntualidad > 1 ? 'si' : 'no') . "</td>";
                        echo "<td class='desktop-only'>". $bono_extra ."</td>";
                        echo "<td class=''>" . $fila['faltas'] . "</td>";
                        echo "<td class='desktop-only'>" . $fila['faltas_justificadas'] . "</td>";
                        echo "<td class='desktop-only'>" . $fila['vacaciones'] . "</td>";
                        echo "<td class='desktop-only'>" . $fila['incapacitacion'] . "</td>";
                        echo "<td class='desktop-only'>" . $fila['baja'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "No se encontraron trabajadores que trabajaran en esos días.";
                }
                // Cerrar la conexión a la base de datos
                $conexion_transimex->close();
                // Mostrar el botón de "Calcular Nómina" solo si el rol de sesión es "gerencia"
                if ($userRole === 'gerencia') {
                ?>
                    <form method="GET" action="">
                        <input type="hidden" id="fecha_inicial" name="fecha_inicial" value="<?php echo htmlspecialchars($fecha_inicial); ?>">
                        <input type="hidden" id="fecha_final" name="fecha_final" value="<?php echo htmlspecialchars($fecha_final); ?>">
                        <input type="hidden" name="pestaña" value="calcular_nomina_semanal">
                        <input type="submit" value="Calcular Nómina">
                    </form>
                <?php
                }
                ?>
                
            </div>               
        </div>
    </div>
</body>
</html>