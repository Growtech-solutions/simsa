<?php
function obtener_asistencia_trabajador($conexion, $fecha_ini, $fecha_fin, $id_trabajador) {
    $resultados = [];
    $inicio = new DateTime($fecha_ini);
    $fin = new DateTime($fecha_fin);

    for ($fecha = clone $inicio; $fecha <= $fin; $fecha->modify('+1 day')) {
        $f = $fecha->format("Y-m-d");

        // Obtener datos de turno
        $dias_es = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        $dia_semana_en = $fecha->format("l");
        $dia_semana = isset($dias_es[$dia_semana_en]) ? $dias_es[$dia_semana_en] : $dia_semana_en;
        $id_turno_row = $conexion->query("SELECT turno, calculo_horas from trabajadores where id = '$id_trabajador'");
        $id_turno = ($id_turno_row && $rowt = $id_turno_row->fetch_assoc()) ? $rowt['turno'] : null;
        $calculo_horas = ($id_turno_row && $rowt) ? $rowt['calculo_horas'] : 'Reloj';
        $hora_entrada_turno = null;
        $hora_salida_turno = null;
        $minutos_descanso = 0;
        if ($id_turno) {
            $sql_comida = "SELECT hora_entrada, hora_salida, minutos_descanso from turno_horarios where id_turno = '$id_turno' and dia_semana = '$dia_semana'";
            $comida_row = $conexion->query($sql_comida);
            if ($comida_row && $rowc = $comida_row->fetch_assoc()) {
                $hora_entrada_turno = date('H:i', strtotime($rowc['hora_entrada']));
                $hora_salida_turno = date('H:i', strtotime($rowc['hora_salida']));
                $minutos_descanso = intval($rowc['minutos_descanso']);
            }
        }

        // Calcular horas_turno_dia correctamente usando DateTime
        $horas_turno_dia = 0;
        if ($hora_entrada_turno && $hora_salida_turno) {
            $entrada_dtr = DateTime::createFromFormat('H:i', $hora_entrada_turno);
            $salida_dtr = DateTime::createFromFormat('H:i', $hora_salida_turno);
            if ($entrada_dtr && $salida_dtr) {
            $diff = $entrada_dtr->diff($salida_dtr);
            $horas = $diff->h + ($diff->i / 60);
            if ($salida_dtr < $entrada_dtr) {
                $horas += 24;
            }
            $horas_turno_dia = $horas - ($minutos_descanso / 60);
            }
        }

        //tipo_turno
        $sql_tipo_turno = "SELECT turno FROM trabajadores WHERE id = '$id_trabajador'";
        $tipo_turno_row = $conexion->query($sql_tipo_turno);
        $tipo_turno = ($tipo_turno_row && $rowt = $tipo_turno_row->fetch_assoc()) ? $rowt['turno'] : null;

        // Asistencia
        $q_asistencia = $conexion->query("SELECT tipo, fecha FROM asistencia 
            WHERE trabajador_id='$id_trabajador' AND fecha LIKE '$f%' ORDER BY fecha ASC");
        $entrada_real = $salida_real = "-";
        $primera_entrada = null;
        $ultima_salida = null;
        if ($q_asistencia && $q_asistencia->num_rows > 0) {
            while ($rowa = $q_asistencia->fetch_assoc()) {
                if ($rowa['tipo'] == 'entrada' && $primera_entrada === null) {
                    $dt = new DateTime($rowa['fecha']);
                    $primera_entrada = $dt->format('H:i');
                }
                if ($rowa['tipo'] == 'salida') {
                    $dt = new DateTime($rowa['fecha']);
                    $ultima_salida = $dt->format('H:i');
                }
            }
            if ($primera_entrada !== null) {
                $entrada_real = $primera_entrada;
            }
            if ($ultima_salida !== null) {
                $salida_real = $ultima_salida;
            }
        }

        // ENTRADA: Si checó antes o igual al inicio de turno, mostrar la hora de inicio de turno, si después, la hora real
        $entrada_mostrar = "-";
        $entrada_real_dt = DateTime::createFromFormat('H:i', $entrada_real);
        $hora_entrada_turno_dt = DateTime::createFromFormat('H:i', $hora_entrada_turno);
        if ($entrada_real === "-") {
            $entrada_mostrar = "-";
        }
        else{
            if ($entrada_real_dt && $hora_entrada_turno_dt) {
            if ($entrada_real_dt <= $hora_entrada_turno_dt) {
                $entrada_mostrar = $hora_entrada_turno;
            } else {
                $entrada_mostrar = $entrada_real;
            }
            } else {
                $entrada_mostrar = $hora_entrada_turno;
            }
        }
        

        // DESCANSO
        $descanso_mostrar = $minutos_descanso > 0 ? $minutos_descanso . " min" : "-";

        // SALIDA
        if ($salida_real !== "-") {
            $salida_mostrar = $salida_real;
        } elseif ($hora_salida_turno) {
            $salida_mostrar = 0;
        } else {
            $salida_mostrar = 0;
        }

        // HORAS SIMPLES (horas normales)
        $horas_simples = "0";
        if (
            $entrada_mostrar !== "-" && $entrada_mostrar !== "0" &&
            $salida_mostrar !== "-" && $salida_mostrar !== "0"
        ) {
            $entrada_dt = DateTime::createFromFormat('H:i', $entrada_mostrar);
            $salida_dt = DateTime::createFromFormat('H:i', $salida_mostrar);
            if ($entrada_dt && $salida_dt) {
                $diff = $entrada_dt->diff($salida_dt);
                $horas_diff = $diff->h + ($diff->i / 60);
                if ($salida_dt < $entrada_dt) {
                    $horas_diff += 24;
                }
                $horas_simples_calculadas = max(0, $horas_diff - ($minutos_descanso / 60));
                if ($hora_entrada_turno && $hora_salida_turno) {
                    $turno_entrada_dt = DateTime::createFromFormat('H:i', $hora_entrada_turno);
                    $turno_salida_dt = DateTime::createFromFormat('H:i', $hora_salida_turno);
                    $turno_diff = $turno_entrada_dt->diff($turno_salida_dt);
                    $horas_turno = $turno_diff->h + ($turno_diff->i / 60);
                    if ($turno_salida_dt < $turno_entrada_dt) {
                        $horas_turno += 24;
                    }
                    $horas_turno -= ($minutos_descanso / 60);
                    $horas_simples = number_format(min($horas_simples_calculadas, $horas_turno), 2);
                } else {
                    $horas_simples = number_format($horas_simples_calculadas, 2);
                }
            }
        }

        // HORAS EXTRA
        $horas_extra = "0";
        $q_extra = $conexion->query("SELECT total_horas FROM horas_extra WHERE id_trabajador='$id_trabajador' AND fecha='$f'");
        if ($q_extra && $row_extra = $q_extra->fetch_assoc()) {
            $horas_extra = number_format($row_extra['total_horas'], 2);
        }

        // VACACIONES
        $q_vac = $conexion->query("SELECT 1 FROM vacaciones 
            WHERE id_trabajador='$id_trabajador' AND '$f' BETWEEN fecha_inicio AND fecha_fin");
        $vac = ($q_vac && $q_vac->num_rows > 0) ? "&#10003;" : "-";

        // INCAPACIDAD
        $q_inc = $conexion->query("SELECT 1 FROM incapacidades 
            WHERE id_trabajador='$id_trabajador' AND '$f' BETWEEN fecha_inicio AND fecha_fin");
        $inc = ($q_inc && $q_inc->num_rows > 0) ? "&#10003;" : "-";

        // SUSPENSIÓN
        $q_sus = $conexion->query("SELECT 1 FROM suspensiones 
            WHERE id_trabajador='$id_trabajador' AND '$f' BETWEEN fecha_inicial AND fecha_final");
        $sus = ($q_sus && $q_sus->num_rows > 0) ? "&#10003;" : "-";

        // BONOS
        $sql_bonos = "select id_trabajador, fecha, horas from bonos where id_trabajador='$id_trabajador' AND fecha = '$f'";
        $q_bonos = $conexion->query($sql_bonos);
        $bonos = ($q_bonos && $q_bonos->num_rows > 0) ? $q_bonos->fetch_assoc()['horas'] : "0";

       $sql_reporte_horas = "SELECT SUM(tiempo) AS total
                            FROM (
                                SELECT tiempo FROM transimex.encargado 
                                WHERE id_trabajador='$id_trabajador' AND fecha='$f'     
                                UNION ALL
                                SELECT tiempo FROM simsa.encargado
                                WHERE id_trabajador='$id_trabajador' AND fecha='$f'
                            ) AS t";

        $q_reporte_horas = $conexion->query($sql_reporte_horas);

        $horas_reporte = ($q_reporte_horas && $q_reporte_horas->num_rows > 0) ? $q_reporte_horas->fetch_assoc()['total'] : "0";

        // Calcular horas simples y extras según reporte
        $horas_simple_reporte = "0";
        $horas_extra_reporte = "0";
        if ($hora_entrada_turno && $hora_salida_turno) {
            $turno_entrada_dt = DateTime::createFromFormat('H:i', $hora_entrada_turno);
            $turno_salida_dt = DateTime::createFromFormat('H:i', $hora_salida_turno);
            if ($turno_entrada_dt && $turno_salida_dt) {
            $turno_diff = $turno_entrada_dt->diff($turno_salida_dt);
            $duracion_turno = $turno_diff->h + ($turno_diff->i / 60);
            if ($turno_salida_dt < $turno_entrada_dt) {
                $duracion_turno += 24;
            }
            $duracion_turno -= ($minutos_descanso / 60);
            $horas_reporte_float = (float)$horas_reporte;
            if ($horas_reporte_float <= $duracion_turno) {
                $horas_simple_reporte = number_format($horas_reporte_float, 2);
                $horas_extra_reporte = "0";
            } else {
                $horas_simple_reporte = number_format($duracion_turno, 2);
                $horas_extra_reporte = number_format($horas_reporte_float - $duracion_turno, 2);
            }
            }
        } else {
            $horas_simple_reporte = number_format((float)$horas_reporte, 2);
            $horas_extra_reporte = "0";
        }

        

        // BANCO DE HORAS ACUMULADO POR TRABAJADOR: suma progresiva día a día
        static $banco_acumulado = [];
        $horas_simples_float = (float)$horas_simples;
        $horas_extra_float = (float)$horas_extra;
        $horas_turno = 0;
        if ($hora_entrada_turno && $hora_salida_turno) {
            $turno_entrada_dt = DateTime::createFromFormat('H:i', $hora_entrada_turno);
            $turno_salida_dt = DateTime::createFromFormat('H:i', $hora_salida_turno);
            if ($turno_entrada_dt && $turno_salida_dt) {
            $turno_diff = $turno_entrada_dt->diff($turno_salida_dt);
            $horas_turno = $turno_diff->h + ($turno_diff->i / 60);
            if ($turno_salida_dt < $turno_entrada_dt) {
                $horas_turno += 24;
            }
            $horas_turno -= ($minutos_descanso / 60);
            }
        }
        // Inicializar acumulado por trabajador si no existe
        if (!isset($banco_acumulado[$id_trabajador])) {
            $banco_acumulado[$id_trabajador] = 0;
        }
        // Si tiene vacaciones o incapacidad, el banco de horas es 0
        if ($vac === "&#10003;" || $inc === "&#10003;") {
            $banco_dia = 0;
        } else {
            $banco_dia = ($horas_simples_float + $horas_extra_float) - $horas_turno;
        }
        $banco_acumulado[$id_trabajador] += $banco_dia;
        $banco_reloj = number_format($banco_acumulado[$id_trabajador], 2);

        // BANCO DE HORAS ACUMULADO SEGÚN REPORTE: suma progresiva día a día POR TRABAJADOR
        static $banco_reporte_acumulado = [];
        $horas_reporte_float = (float)$horas_reporte;
        if (!isset($banco_reporte_acumulado[$id_trabajador])) {
            $banco_reporte_acumulado[$id_trabajador] = 0;
        }
        $banco_dia_reporte = $horas_reporte_float - $horas_turno;
        $banco_reporte_acumulado[$id_trabajador] += $banco_dia_reporte;
        $banco_reporte = number_format($banco_reporte_acumulado[$id_trabajador], 2);

        // Total bonos acumulados
        static $total_bonos = 0;
        $total_bonos += (float)$bonos;

        // ESTADO 
        $estado = '0';
        // el registro no es falta si tiene horas reportadas y no checo en el reloj
        if ($entrada_real === "-" && $horas_reporte == 0) {
            $estado = 'F';
        } 
        /* el registro es falta si no checo en el reloj aunque registre horas
        if ($entrada_real === "-") {
            $estado = 'F';
            $horas_reporte = 0;
        }
        */
        if ($vac === "&#10003;") {
            $estado = 'V';
        } 
        if ($inc === "&#10003;") {
            $estado = 'I';
        } 
        if ($sus === "&#10003;") {
            $estado = 'S';
        } 
        if ($hora_entrada_turno === null && $hora_salida_turno === null) {
            $estado = 'D';
        } 

        // Incidencias: vacaciones, incapacidad, suspensión, falta
        $incidencias = 0;
        if ($hora_entrada_turno === null) $incidencias++;
        if ($vac === "&#10003;") $incidencias++;
        if ($inc === "&#10003;") $incidencias++;
        if ($sus === "&#10003;") $incidencias++;

        // Si tiene horas y alguna incidencia o más de una incidencia
        if ((float)$horas_simples > 0 && ($vac === "&#10003;" || $inc === "&#10003;" || $sus === "&#10003;") || $incidencias > 1) {
            $estado = 'DR';
        } 
        
        if ($estado == '0') {
            $estado = 'A';
        }
        if ($estado != 'F') {
            $horas_turno_dia = 0;
        }

        //Horas semanales del turno 
        $sql_turno = "SELECT SUM(horas_dia) from turno_horarios left join turnos on turno_horarios.id_turno = turnos.id_turno 
        where turnos.id_turno = '$id_turno' GROUP BY turnos.id_turno";
        $resultado_turno = $conexion->query($sql_turno);
        $horas_semanales_turno = ($resultado_turno->num_rows > 0) ? $resultado_turno->fetch_assoc()['SUM(horas_dia)'] : 0;

        // Horas del turno acumuluables por dia
        $dias_contados = [
            'Lunes' => 0,
            'Martes' => 0,
            'Miércoles' => 0,
            'Jueves' => 0,
            'Viernes' => 0,
            'Sábado' => 0,
            'Domingo' => 0
        ];

        // Contar cuántos días de cada tipo hay en el rango
        $fecha_tmp = clone $inicio;
        while ($fecha_tmp <= $fin) {
            $dia_en = $fecha_tmp->format('l');
            $dia_es = isset($dias_es[$dia_en]) ? $dias_es[$dia_en] : $dia_en;
            if (isset($dias_contados[$dia_es])) {
                $dias_contados[$dia_es]++;
            }
            $fecha_tmp->modify('+1 day');
        }

        // Obtener horas del turno para cada día de la semana
        $horas_turno_dias = [];
        $sql_horas_turno = "SELECT dia_semana, hora_entrada, hora_salida, minutos_descanso FROM turno_horarios WHERE id_turno = '$id_turno'";
        $res_horas_turno = $conexion->query($sql_horas_turno);
        if ($res_horas_turno) {
            while ($row = $res_horas_turno->fetch_assoc()) {
                $entrada = DateTime::createFromFormat('H:i:s', $row['hora_entrada']);
                $salida = DateTime::createFromFormat('H:i:s', $row['hora_salida']);
                if ($entrada && $salida) {
                    $diff = $entrada->diff($salida);
                    $horas = $diff->h + ($diff->i / 60);
                    if ($salida < $entrada) {
                        $horas += 24;
                    }
                    $horas -= ($row['minutos_descanso'] / 60);
                    $horas_turno_dias[$row['dia_semana']] = $horas;
                }
            }
        }

        // Calcular horas acumuladas del turno en el rango
        $horas_dia_acumulado = 0;
        foreach ($dias_contados as $dia => $cantidad) {
            if (isset($horas_turno_dias[$dia])) {
                $horas_dia_acumulado += $horas_turno_dias[$dia] * $cantidad;
            }
        }
        $horas_dia_acumulado = number_format($horas_dia_acumulado, 2);

        $resultados[] = [
            'fecha' => $f,
            'estado' => $estado,
            'tipo_calculo' => $calculo_horas,
            'tipo_turno' => $tipo_turno,
            'horas_semanales_turno' => $horas_semanales_turno,
            'horas_total_turno' => $horas_dia_acumulado,
            'entrada_real' => $entrada_real,
            'salida_real' => $salida_real,
            'entrada_turno' => $hora_entrada_turno,
            'salida_turno' => $hora_salida_turno,
            'descanso_turno' => $descanso_mostrar,
            'entrada_mostrada' => $entrada_mostrar,
            'horas_turno_dia' => number_format((float)$horas_turno_dia, 2),
            'horas_reporte' => $horas_reporte,
            'horas_simple_reporte' => $horas_simple_reporte,
            'horas_extra_reporte' => $horas_extra_reporte,
            'banco_reporte' => $banco_reporte,
            'horas_reloj' => number_format((float)$horas_simples + (float)$horas_extra, 2),
            'horas_simples_reloj' => $horas_simples,
            'horas_extras_reloj' => $horas_extra,
            'banco_reloj' => $banco_reloj,
            'bonos' => $bonos,
            'vacaciones' => $vac,
            'incapacidades' => $inc,
            'suspenciones' => $sus, 
            'total_bonos' => number_format($total_bonos, 2)
        ];
    }
    return $resultados;
}
/*
$fecha_ini = '2025-08-10';
$fecha_fin = '2025-08-15';
$id_trabajador = 1;

$resultados = obtener_asistencia_trabajador($conexion, $fecha_ini, $fecha_fin, $id_trabajador);

echo "<pre>";
print_r($resultados);
echo "</pre>";
*/