<script>
function mostrarModal(id) {
    document.getElementById("modal-" + id).style.display = "block";
}
function cerrarModal(id) {
    document.getElementById("modal-" + id).style.display = "none";
}
</script>

<?php
// --- Calcular jueves anterior y miércoles siguiente ---
$hoy = new DateTime();

// Jueves anterior
$jueves = clone $hoy;
$jueves->modify("last thursday");

// Miércoles siguiente
$miercoles = clone $jueves;
$miercoles->modify("next wednesday");

// Si se mandan fechas por form, usarlas
if (isset($_POST['fecha_ini']) && isset($_POST['fecha_fin'])) {
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_fin = $_POST['fecha_fin'];
} else {
    $fecha_ini = $jueves->format("Y-m-d");
    $fecha_fin = $miercoles->format("Y-m-d");
}
// Obtener todas las fechas del rango
$fechas = [];
$inicio = new DateTime($fecha_ini);
$fin = new DateTime($fecha_fin);
for ($fecha = clone $inicio; $fecha <= $fin; $fecha->modify('+1 day')) {
    $fechas[] = $fecha->format("Y-m-d");
}

include 'procedimiento_asistencia.php';

// Obtener todos los trabajadores
$datos = [];
$sql_trabajadores = "SELECT id, nombre, apellidos FROM trabajadores WHERE estado = 'Activo'";
if (isset($_POST['empresa_filtro']) && $_POST['empresa_filtro'] !== 'Todas') {
    $empresa_filtro = $conexion_transimex->real_escape_string($_POST['empresa_filtro']);
    $sql_trabajadores .= " AND empresa = '$empresa_filtro'";
}
$sql_trabajadores .= " ORDER BY empresa, apellidos, nombre";
$res_trabajadores = $conexion_transimex->query($sql_trabajadores);
if ($res_trabajadores) {
    while ($trab = $res_trabajadores->fetch_assoc()) {
        $id_trabajador = $trab['id'];
        $nombre_trabajador = $trab['nombre'] . ' ' . $trab['apellidos'];
        $asistencias = obtener_asistencia_trabajador($conexion_transimex, $fecha_ini, $fecha_fin, $id_trabajador);

        // Indexar por fecha para fácil acceso en la tabla
        $registro = [];
        foreach ($asistencias as $asistencia) {
            $registro[$asistencia['fecha']] = array_merge($asistencia, [
                'id_trabajador' => $id_trabajador,
                'estado' => $asistencia['estado'],
                'tipo_turno' => $asistencia['tipo_turno'],
                'tipo_calculo' => $asistencia['tipo_calculo'],
                'bonos' => $asistencia['bonos'],
                'horas_reloj' =>  $asistencia['horas_reloj'],
                'horas_reporte' => $asistencia['horas_reporte'],
                'total_bonos' => $asistencia['total_bonos'],
                'horas_turno_dia' => $asistencia['horas_turno_dia'],
                'horas_total_turno' => $asistencia['horas_total_turno'],
            ]);
        }
        $datos[$nombre_trabajador] = $registro;
    }
}

function simbolo($estado, $row) {
    switch ($estado) {
        case 'V': return '<span style="color: brown; font-weight: bold;">V</span>';
        case 'F': return '<span style="color: red; font-weight: bold;">F</span>';
        case 'DR': return '<span style="color: orange; font-weight: bold;">DR</span>';
        case 'I': return '<span style="color: purple; font-weight: bold;">I</span>';
        case 'S': return '<span style="color: blue; font-weight: bold;">S</span>';
        case 'D':
            // Mostrar horas si existen, si no solo la letra D
            $horas = 0;
            if ($row['tipo_calculo'] == 'Reporte') {
            $horas = $row['horas_reporte'] ?? 0;
            } elseif ($row['tipo_calculo'] == 'Reloj') {
            $horas = ($row['horas_reloj'] ?? 0);
            } elseif ($row['tipo_calculo'] == 'Automatico') {
            $horas = ($row['horas_turno_dia'] ?? 0);
            }
            if ($horas > 0) {
            return '<span style="color: gray; font-weight: bold;">' . number_format($horas, 2) . '</span>';
            } else {
            return '<span style="color: gray; font-weight: bold;">D</span>';
            }
        case 'A':
            // Mostrar horas si existen, si no solo la letra A
            $horas = 0;
            if ($row['tipo_calculo'] == 'Reporte') {
            $horas = $row['horas_reporte'] ?? 0;
            } elseif ($row['tipo_calculo'] == 'Reloj') {
            $horas = ($row['horas_reloj'] ?? 0);
            } elseif ($row['tipo_calculo'] == 'Automatico') {
            $horas = ($row['horas_turno_dia'] ?? 0);
            }
            if ($horas > 0) {
            return '<span style="color: green; font-weight: bold;">' . number_format($horas, 2) . '</span>';
            } else {
            return '<span style="color: green; font-weight: bold;">A</span>';
            }
            break;
        default: return '';
    }
}
?>
<div class="principal">
    <div>
        <form method="GET" action="" style="display:inline-block;">
            <input type="hidden" name="pestaña" value="historial_asistencia">
            <button type="submit" style="padding: 10px 20px;
              border: none;
              color: white;
              background-color: #007bff;
              border-radius: 4px;
              cursor: pointer;
              font-size: 14px;">Editar asistencia</button>
        </form>
        <form method="GET" action="" style="display:inline-block; margin-right:10px;">
            <input type="hidden" name="pestaña" value="registro_asistencia">
            <button type="submit" style="padding: 10px 20px;
              border: none;
              color: white;
              background-color: #007bff;
              border-radius: 4px;
              cursor: pointer;
              font-size: 14px;">+ Registrar asistencia</button>
        </form>
        <form method="GET" action="" style="display:inline-block;">
            <input type="hidden" name="pestaña" value="autorizar_horas">
            <button type="submit" style="padding: 10px 20px;
              border: none;
              color: white;
              background-color: #007bff;
              border-radius: 4px;
              cursor: pointer;
              font-size: 14px;">Autorizar horas extra</button>
        </form>
        <br><br>
<h1 class="text-2xl font-bold text-blue-600">Reporte de Asistencia</h1>
<br>
<div style="text-align: center; margin-bottom: 20px;">
    <form method="post" style="display: inline-block;">
        Fecha inicial: <input type="date" name="fecha_ini" value="<?= $fecha_ini ?>">
        Fecha final: <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>">
        Empresa <select style="border: 1px solid #ccc; border-radius: 4px; padding: 5px;" name="empresa_filtro">
            <option value="Todas">Todas</option>
            <?php
            $empresas = $conexion_transimex->query("SELECT DISTINCT empresa FROM trabajadores ORDER BY empresa");
            while ($empresa = $empresas->fetch_assoc()) {
                $selected = (isset($_POST['empresa_filtro']) && $_POST['empresa_filtro'] === $empresa['empresa']) ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($empresa['empresa']) . "\" $selected>" . htmlspecialchars($empresa['empresa']) . "</option>";
            }
            ?>
        </select>
        <button type="submit" style="background-color: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
            Filtrar
        </button>
    </form>
</div>

<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; text-align:center;">
    <tr>
        <th>Trabajador</th>
        <?php foreach ($fechas as $f): ?>
            <th><?= $f ?></th>
        <?php endforeach; ?>
        <th>Bonos</th>
        <th>Total Horas</th>
    </tr>

    <?php foreach ($datos as $trabajador => $registro): 
        $total = 0; $bonos = 0; 
        $id_modal = $registro[array_key_first($registro)]['id_trabajador'];
    ?>
    <tr>
        <td>
            <a href="#" onclick="mostrarModal('<?= $id_modal ?>'); return false;" 
               style="color: #007bff; text-decoration: underline; cursor: pointer;">
                <?= htmlspecialchars($trabajador) ?>
            </a>
        </td>
        <?php foreach ($fechas as $f): 
            $row = $registro[$f] ;
            if ($row['tipo_calculo'] === 'Reporte') $total += $row['horas_reporte'];
            elseif ($row['tipo_calculo'] === 'Reloj') $total += $row['horas_reloj'];
            elseif ($row['tipo_calculo'] === 'Automatico') $total += $row['horas_turno_dia'];
            $bonos += $row['bonos'];
        ?>
        <?php if ($row['tipo_calculo'] === 'Automatico' && $row['estado'] === 'F') { ?>
            <td style="color: green; font-weight: bold;"><?= $row['horas_turno_dia'] ?></td>
        <?php } else { ?>
            <td><?= simbolo($row['estado'], $row) ?></td>
        <?php } ?>
        <?php endforeach; ?>

        <td><b><?= $bonos ?></b></td>
        <td><b><?= number_format($total, 2) ?></b></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php foreach ($datos as $trabajador => $registro): 
    $id_modal = $registro[array_key_first($registro)]['id_trabajador'];
?>
<div>
</div>
    <!-- Modal de detalle -->
    <div id="modal-<?= $id_modal ?>" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6);">
        <div class="modal-content" style="background:#fff; margin:5% auto; padding:20px; border-radius:10px; width:80%; max-height:80%; overflow-y:auto;">
            <span onclick="cerrarModal('<?= $id_modal ?>')" style="float:right; font-size:20px; font-weight:bold; cursor:pointer;">&times;</span>
            <h1 class="text-2xl font-bold text-blue-600"><?= htmlspecialchars($trabajador) ?></h1>
            
            <table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; text-align:center; width:100%; margin-top:10px;">
                <tr>
                    <th>Fecha</th>
                    <th>Entrada</th>
                    <th>Descanso</th>
                    <th>Salida</th>
                    <th>Horas simples</th>
                    <th>Horas extra</th>
                    <th>Banco horas</th>
                    <th>Vacaciones</th>
                    <th>Incapacidad</th>
                    <th>Suspensión</th>
                </tr>
                <?php foreach ($registro as $dia): ?>
                <tr>
                    <td><?= $dia['fecha'] ?? '-' ?></td>
                    <td><?= $dia['entrada_mostrada'] ?? '-' ?></td>
                    <td><?= $dia['descanso_turno'] ?? '-' ?></td>
                    <td><?= $dia['salida_real'] ?? '-' ?></td>
                    <?php if ($dia['tipo_calculo'] === 'Reloj'): ?>
                        <td><?= $dia['horas_simples_reloj'] ?? 0 ?></td>
                        <td><?= $dia['horas_extras_reloj'] ?? 0 ?></td>
                        <td><?= $dia['banco_reloj'] ?? 0 ?></td>
                    <?php elseif ($dia['tipo_calculo'] === 'Reporte'): ?>
                        <td><?= $dia['horas_simple_reporte'] ?? 0 ?></td>
                        <td><?= $dia['horas_extra_reporte'] ?? 0 ?></td>
                        <td><?= $dia['banco_reporte'] ?? 0 ?></td>
                    <?php elseif ($dia['tipo_calculo'] === 'Automatico'): ?>
                        <td><?= $dia['horas_turno_dia'] ?? 0 ?></td>
                        <td>0</td>
                        <td>0</td>
                    <?php endif; ?>
                    <td><?= $dia['vacaciones'] ?? 0 ?></td>
                    <td><?= $dia['incapacidades'] ?? 0 ?></td>
                    <td><?= $dia['suspenciones'] ?? 0 ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
<?php endforeach; ?>
