<?php 

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Periodos de Nómina</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
a{
    color: #333;
    text-decoration: none;
}
</style>

<body class="bg-light">

<div class="container py-4">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <form method="get" class="d-flex align-items-center gap-2">
      <h3 class="mb-0">Periodos de Nómina -</h3>
      <select name="anio" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
        <?php
          $anio_actual = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
          // Obtener años disponibles de la base de datos
          $anios = [];
          $sql_anios = "SELECT DISTINCT YEAR(fecha_inicio) as anio FROM periodo ORDER BY anio DESC";
          $result_anios = $conexion->query($sql_anios);
          if ($result_anios) {
            while ($row = $result_anios->fetch_assoc()) {
              $anios[] = $row['anio'];
            }
          }
          if (empty($anios)) $anios[] = $anio_actual;
          foreach ($anios as $anio) {
            echo '<option value="' . $anio . '"' . ($anio == $anio_actual ? ' selected' : '') . '>' . $anio . '</option>';
          }
        ?>
      </select>
    </form>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoPeriodo">➕ Crear nuevo periodo</button>
  </div>

  <div class="row g-3">
    <?php
    // Consulta para obtener los periodos de nómina
    $sql = "SELECT id, fecha_inicio, fecha_fin, tipo, 10000 AS neto
    FROM periodo WHERE YEAR(fecha_inicio) = $anio_actual ORDER BY fecha_inicio DESC";
    $result = $conexion->query($sql);

    if ($result && $result->num_rows > 0):
        while ($periodo = $result->fetch_assoc()):

            $sql_timbrados = "SELECT COUNT(*) AS timbrados, SUM(total_neto) AS total_neto FROM nomina WHERE periodo_id = " . $periodo['id'] . " AND uuid IS NOT NULL";
            $result_timbrados = $conexion->query($sql_timbrados);
            if ($result_timbrados) {
              $row_timbrados = $result_timbrados->fetch_assoc();
              $timbrados = $row_timbrados['timbrados'];
              $neto_timbrados = $row_timbrados['total_neto'] ?? 0;
            } else {
              $timbrados = 0;
              $neto_timbrados = 0;
            }

          $sql_cont_trab = "SELECT COUNT(*) AS total_trabajadores FROM trabajadores WHERE fecha_ingreso <= '" . $periodo['fecha_fin'] . "' AND contrato = '" . $periodo['tipo'] . "' AND empresa = 'suministros' and estado = 'Activo';";
          $result_cont_trab = $conexion_transimex->query($sql_cont_trab);
          $total_trabajadores = $result_cont_trab ? $result_cont_trab->fetch_assoc()['total_trabajadores'] : 0;
          $sql_transimex = "
            SELECT COUNT(DISTINCT e.id_trabajador) AS total
            FROM transimex.encargado e
            INNER JOIN trabajadores t ON e.id_trabajador = t.id
            WHERE e.fecha BETWEEN '{$periodo['fecha_inicio']}' AND '{$periodo['fecha_fin']}'
              AND t.fecha_ingreso <= '{$periodo['fecha_fin']}'
              AND t.contrato = '{$periodo['tipo']}'
              AND t.empresa = 'SUMINISTROS'
              AND t.estado = 'Activo'
            ";

            $result_sin_horas_transimex = $conexion_transimex->query($sql_transimex);
            $total_trabajadores_transimex = $result_sin_horas_transimex ? (int)$result_sin_horas_transimex->fetch_assoc()['total'] : 0;
            $sql_simsa = "
            SELECT COUNT(DISTINCT e.id_trabajador) AS total
            FROM simsa.encargado e
            INNER JOIN transimex.trabajadores t ON e.id_trabajador = t.id
            WHERE e.fecha BETWEEN '{$periodo['fecha_inicio']}' AND '{$periodo['fecha_fin']}'
              AND t.fecha_ingreso <= '{$periodo['fecha_fin']}'
              AND t.contrato = '{$periodo['tipo']}'
              AND t.empresa = 'SUMINISTROS'
              AND t.estado = 'Activo'
            ";

            $result_sin_horas_simsa = $conexion->query($sql_simsa);
            $total_trabajadores_simsa = $result_sin_horas_simsa ? (int)$result_sin_horas_simsa->fetch_assoc()['total'] : 0;
            $total_sin_horas = $total_trabajadores - ($total_trabajadores_simsa + $total_trabajadores_transimex);
          $total_trabajadores -= $total_sin_horas;
          // Determinar el estado del periodo
          if ($timbrados >= $total_trabajadores && $total_trabajadores > 0) {
              $estado = 'Cerrado';
          } else {
              $estado = 'Abierto';
          }
          // Determinar el color del badge según el estado
          $badge = ($estado === 'Cerrado') ? 'success' : 'warning';
          // URL de detalle (ajusta si necesitas pasar el ID)
          $detalle_url = 'general.php?pestaña=detalle_nomina&periodo_id=' . urlencode($periodo['id']);
    ?>
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm border-<?php echo $badge; ?>">
            <div class="card-body">
              <h5 class="card-title">
                <?php echo date('d/m/Y', strtotime($periodo['fecha_inicio'])); ?> - <?php echo date('d/m/Y', strtotime($periodo['fecha_fin'])); ?>
              </h5>
              <p class="mb-1 text-muted">Tipo: <?php echo htmlspecialchars($periodo['tipo']); ?></p>
              <p class="mb-2">
                <span class="badge bg-<?php echo $badge; ?>">
                  <?php echo $estado === 'Cerrado' ? '✅ Cerrado' : '🟡 Abierto'; ?>
                </span>
              </p>
              <div class="d-flex justify-content-between">
                <span class="fw-bold">Neto timbrado:</span> <span>$<?php echo number_format($neto_timbrados, 0, '.', ','); ?></span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="fw-bold">Trabajadores timbrados:</span> <span><?php echo $timbrados; ?> / <?php echo $total_trabajadores; ?></span>
                </div>
            </div>
            <div class="card-footer text-end">
              <a href="<?php echo htmlspecialchars($detalle_url); ?>" class="btn btn-sm btn-outline-primary">👁️ Ver detalle</a>
            </div>
          </div>
        </div>
    <?php
        endwhile;
    else:
    ?>
        <div class="col-12">
          <div class="alert alert-info">No hay periodos registrados.</div>
        </div>
    <?php
    endif;
    ?>
</div>

  <!-- Modal para crear nuevo periodo -->
<div class="modal fade" id="modalNuevoPeriodo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">➕ Crear nuevo periodo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo de periodo</label>
                        <select class="form-select" name="tipoPeriodo" required>
                            <option value="Semanal">Ordinario semanal</option>
                            <option value="Quincenal">Ordinario quincenal</option>
                            <option value="Mensual">Ordinario mensual</option>
                            <option value="Aguinaldo">Extraordinario aguinaldo</option>
                            <option value="PTU">Extraordinario PTU</option>
                            <option value="Prima vacacional">Extraordinario prima vacacional</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fechaInicio" class="form-label">Fecha inicial</label>
                        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fechaFinal" class="form-label">Fecha final</label>
                        <input type="date" class="form-control" id="fechaFinal" name="fechaFinal" required>
                    </div>
                    <input type="hidden" name="accion" value="crear_periodo_simsa">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
                    <button type="submit" class="btn btn-success">✅ Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>