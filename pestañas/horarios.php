<?php
// obtener turnos y horarios
$query = "SELECT t.id_turno, t.nombre_turno, t.descripcion, th.dia_semana, th.hora_entrada, th.hora_salida, th.minutos_descanso
          FROM turnos t
          LEFT JOIN turno_horarios th ON t.id_turno = th.id_turno
          ORDER BY t.id_turno, FIELD(th.dia_semana,'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')";
$result = $conexion_transimex->query($query);

$turnos = [];
while ($row = $result->fetch_assoc()) {
    $turnos[$row['id_turno']]['nombre'] = $row['nombre_turno'];
    $turnos[$row['id_turno']]['descripcion'] = $row['descripcion'];
    $turnos[$row['id_turno']]['horarios'][] = [
        'dia' => $row['dia_semana'],
        'entrada' => $row['hora_entrada'],
        'salida' => $row['hora_salida'],
        'descanso' => $row['minutos_descanso'] ?? 0
    ];
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
$nombre = $_POST['nombre_turno'];
$descripcion = $_POST['descripcion'];
$entrada = $_POST['entrada'];
$salida = $_POST['salida'];
$descanso = $_POST['descanso'];

// Insertar turno
$stmt = $conexion_transimex->prepare("INSERT INTO turnos (nombre_turno, descripcion) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $descripcion);
$stmt->execute();
$id_turno = $conexion_transimex->insert_id;

// Insertar horarios
foreach ($entrada as $dia => $hora_entrada) {
    if (!empty($hora_entrada) && !empty($salida[$dia])) {
        $min_descanso = !empty($descanso[$dia]) ? intval($descanso[$dia]) : 0;
        $stmt2 = $conexion_transimex->prepare("INSERT INTO turno_horarios (id_turno, dia_semana, hora_entrada, hora_salida, minutos_descanso) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("isssi", $id_turno, $dia, $hora_entrada, $salida[$dia], $min_descanso);
        $stmt2->execute();
    }
}

header("Location:" . $_SERVER['HTTP_REFERER'] );
exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    a{
    color: #333;
    text-decoration: none;
}
</style>
<script>
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('entrada') || e.target.classList.contains('salida') || e.target.classList.contains('descanso')) {
        let dia = e.target.dataset.dia;
        let entrada = document.querySelector(`.entrada[data-dia="${dia}"]`).value;
        let salida = document.querySelector(`.salida[data-dia="${dia}"]`).value;
        let descanso = parseInt(document.querySelector(`.descanso[data-dia="${dia}"]`).value) || 0;

        if (entrada && salida) {
            let inicio = new Date(`1970-01-01T${entrada}:00`);
            let fin = new Date(`1970-01-01T${salida}:00`);
            let diffHoras = (fin - inicio) / (1000 * 60 * 60) - (descanso / 60);
            if (diffHoras < 0) diffHoras = 0; // Evita negativos
            document.querySelector(`#horas_${dia}`).value = diffHoras.toFixed(2) + " h";
        } else {
            document.querySelector(`#horas_${dia}`).value = "";
        }
    }
});
</script>

<body class="bg-light">

<div class="principal">
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            ➕ Agregar Turno
        </button>
    <div>
        <h1 class="text-2xl font-bold text-blue-600">Lista de horarios</h1>
    </div>
    <br>

    <?php if (empty($turnos)): ?>
        <div class="alert alert-info">No hay turnos registrados.</div>
    <?php endif; ?>

    <?php foreach ($turnos as $id => $turno): ?>
    <?php 
    $total_semana = 0;
    foreach ($turno['horarios'] as $h) {
        if (!empty($h['entrada']) && !empty($h['salida'])) {
            $entrada_t = strtotime($h['entrada']);
            $salida_t = strtotime($h['salida']);
            $horas_dia = (($salida_t - $entrada_t) / 3600) - ($h['descanso'] / 60);
            $total_semana += $horas_dia;
        }
    }
    ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h4 class="card-title"><?= htmlspecialchars($turno['nombre']) ?></h4>
            <p class="text-muted"><?= htmlspecialchars($turno['descripcion']) ?></p>
            <table class="table table-sm table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Día</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Descanso (min)</th>
                        <th>Horas Reales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($turno['horarios'] as $horario): ?>
                        <tr>
                            <td><?= htmlspecialchars($horario['dia']) ?></td>
                            <td><?= htmlspecialchars($horario['entrada']) ?></td>
                            <td><?= htmlspecialchars($horario['salida']) ?></td>
                            <td><?= intval($horario['descanso']) ?></td>
                            <td>
                                <?php
                                if (!empty($horario['entrada']) && !empty($horario['salida'])) {
                                    $entrada_t = strtotime($horario['entrada']);
                                    $salida_t = strtotime($horario['salida']);
                                    $total_horas = (($salida_t - $entrada_t) / 3600) - ($horario['descanso'] / 60);
                                    echo number_format($total_horas, 2);
                                } else {
                                    echo "—";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="fw-bold text-end text-primary">
                Total horas semanales: <?= number_format($total_semana, 2) ?> h
            </div>
        </div>
    </div>
<?php endforeach; ?>

</div>

<!-- Modal Agregar Turno -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Nuevo Turno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Nombre del Turno</label>
                <input type="text" name="nombre_turno" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control"></textarea>
            </div>
            <hr>
            <h6>Horarios por Día</h6>
            <?php
            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
            $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
            foreach ($dias as $dia): ?>
                <div class="row mb-2 align-items-center">
                    <div class="col-md-2"><strong><?= $dia ?></strong></div>
                    <div class="col-md-3">
                        <input type="time" name="entrada[<?= $dia ?>]" class="form-control entrada" data-dia="<?= $dia ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="time" name="salida[<?= $dia ?>]" class="form-control salida" data-dia="<?= $dia ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="descanso[<?= $dia ?>]" class="form-control descanso" min="0" max="240" placeholder="Min" data-dia="<?= $dia ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control horas-dia" id="horas_<?= $dia ?>" placeholder="0.00 h" readonly>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar Turno</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>
            </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
