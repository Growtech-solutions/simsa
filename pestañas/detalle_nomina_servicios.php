<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle de Nómina - Periodo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
a{
    color: #333;
    text-decoration: none;
}
th {
  text-align:center;
}
</style>
<?php 

$periodo_id = isset($_GET['periodo_id']) ? intval($_GET['periodo_id']) : 0;
$sql_periodo = "SELECT * FROM periodo WHERE id = $periodo_id";
$result_periodo = $conexion_servicios->query($sql_periodo);
$periodo = $result_periodo ? $result_periodo->fetch_assoc() : null;

$errores_dir = "/documentos/errores_nomina/$periodo_id/errores_nomina_$periodo_id.txt";
isset ($_GET['timbrado']) or $_GET['timbrado'] = '0';
$errores_dir = "/var/www/simsa/documentos/errores_nomina/$periodo_id/";
$errores_file = $errores_dir . "errores_nomina_$periodo_id.txt";

if (file_exists($errores_file) && isset($_GET['timbrado']) && $_GET['timbrado'] === '1') {

    $archivo_url = "/documentos/errores_nomina/"
                   . intval($periodo_id)
                   . "/errores_nomina_" . intval($periodo_id) . ".txt";

    echo '<div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Errores al timbrar la nómina</h4>
            <p>Se encontraron errores al intentar timbrar la nómina para este periodo. Por favor, revisa el archivo de errores para más detalles.</p>
            <hr>
            <p class="mb-0">
                <a href="' . htmlspecialchars($archivo_url, ENT_QUOTES, 'UTF-8') . '" target="_blank" class="alert-link">
                    Ver archivo de errores
                </a>
            </p>
          </div>';
}

?>

<body class="bg-light">
<div class="container py-4">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Detalle de Nómina <?php echo  htmlspecialchars($periodo['tipo']); ?> - Periodo <?php echo date('d/m/Y', strtotime($periodo['fecha_inicio'])); ?> al <?php echo date('d/m/Y', strtotime($periodo['fecha_fin'])); ?></h3>
    <a href="https://gestor.e-simsa.com.mx/php/timbrar_nomina_servicios.php?id_periodo=<?php echo $periodo_id; ?>" class="btn btn-success">✅ Timbrar todo el periodo</a>
  </div>

  <?php
    include 'calculo_nomina_servicios.php';
  ?>

  <!-- Resumen -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body d-flex justify-content-around text-center">
      <div>
        <h5>Total empleados</h5>
        <p class="fw-bold text-primary"><?php echo $total_empleados; ?></p>
      </div>
      <div>
        <h5>Percepciones</h5>
        <p class="fw-bold text-success">$<?php echo number_format($total_percepciones, 2); ?></p>
      </div>
      <div>
        <h5>Deducciones</h5>
        <p class="fw-bold text-danger">$<?php echo number_format($total_deducciones, 2); ?></p>
      </div>
      <div>
        <h5>Neto a pagar</h5>
        <p class="fw-bold text-dark">$<?php echo number_format($total_neto, 2); ?></p>
      </div>
    </div>
  </div>

  <!-- Tabla de empleados -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title mb-3">Empleados</h5>
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Empleado</th>
            <th>CURP</th>
            <th>Percepciones</th>
            <th>Deducciones</th>
            <th>Neto</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trabajadores_nomina as $trabajador): ?>
            <?php if ($trabajador['doble_reporte'] > 0 || $trabajador['percepcion_trabajador']<=0): ?>
              <tr class="table-warning" title="Este empleado tiene doble reporte o percepciones menores o iguales a cero en el periodo.">
              <td colspan="7" class="text-center text-danger fw-bold">
                <?php echo htmlspecialchars($trabajador['nombre']); ?> tiene doble reporte o percepciones menores o iguales a cero en el periodo. Por favor revisa los registros antes de timbrar.
              </td>
              </tr>
            <?php else: ?>
            <tr>
              <td><?php echo htmlspecialchars($trabajador['nombre']); ?></td>
              <td><?php echo htmlspecialchars($trabajador['curp']); ?></td>
              <td>$<?php echo number_format($trabajador['percepcion_trabajador'], 2); ?></td>
              <td>$<?php echo number_format($trabajador['deducciones'], 2); ?></td>
              <td>$<?php echo number_format($trabajador['neto'], 2); ?></td>
              <?php
              $trabajador_id = intval($trabajador['id']);
              $sql_nomina = "SELECT id FROM nomina WHERE periodo_id = $periodo_id AND trabajador_id = $trabajador_id LIMIT 1";
              $result_nomina = $conexion_servicios->query($sql_nomina);
              if ($result_nomina && $result_nomina->num_rows > 0) {
                echo '<td><span class="badge bg-success">Timbrado</span></td>
                  <td>
                    <a href="/php/generar_pdf_nomina.php?trabajador_id='.$trabajador_id.'&periodo='.$periodo_id.'" class="btn btn-sm btn-secondary" target="_blank">📄 PDF</a>
                    <a href="/php/generar_xml_nomina.php?trabajador_id='.$trabajador_id.'&periodo='.$periodo_id.'" class="btn btn-sm btn-secondary" >💾 XML</a>
                  </td>';
              } else {
                // Botón con data-* para pasar datos al modal
                echo '<td><span class="badge bg-warning">Pendiente</span></td>
                  <td>
                    <button 
                      class="btn btn-sm btn-info btn-ver-recibo"
                      data-bs-toggle="modal"
                      data-bs-target="#modalRecibo"
                      data-nombre="'.htmlspecialchars($trabajador['nombre']).'"
                        data-salario-diario="'.htmlspecialchars($trabajador['sueldo_diario_base']).'"
                        data-vacaciones-semana="'.htmlspecialchars($trabajador['vacaciones_en_semana']).'"
                        data-asistencia-nomina="'.htmlspecialchars($trabajador['asistencia_nomina']).'"
                      data-fecha-inicio="'.htmlspecialchars($periodo['fecha_inicio']).'"
                      data-fecha-fin="'.htmlspecialchars($periodo['fecha_fin']).'"
                      data-horas-simples="'.floatval($trabajador['horas_simples'] ?? 0).'"
                      data-horas-dobles="'.floatval($trabajador['horas_dobles'] ?? 0).'"
                      data-horas-triples="'.floatval($trabajador['horas_triples'] ?? 0).'"
                      data-valor-horas-simples="'.floatval($trabajador['valor_horas_simples'] ?? 0).'"
                      data-valor-horas-dobles="'.floatval($trabajador['valor_horas_dobles'] ?? 0).'"
                      data-valor-horas-triples="'.floatval($trabajador['valor_horas_triples'] ?? 0).'"
                      data-vacaciones="'.floatval($trabajador['valor_vacaciones'] ?? 0).'"
                      data-bono-asistencia="'.floatval($trabajador['bono_asistencia'] ?? 0).'"
                      data-bono-puntualidad="'.floatval($trabajador['bono_puntualidad'] ?? 0).'"
                      data-despensa="'.floatval($trabajador['despensa'] ?? 0).'"
                      data-premios="'.floatval($trabajador['valor_premios'] ?? 0).'"
                      data-subsidio="'.floatval($trabajador['subsidio'] ?? 0).'"
                      data-isr="'.floatval($trabajador['isr_retencion'] ?? 0).'"
                      data-imss="'.floatval($trabajador['imss'] ?? 0).'"
                      data-infonavit="'.floatval($trabajador['monto_infonavit'] ?? 0).'"
                      data-prestamos="'.floatval($trabajador['monto_prestamos'] ?? 0).'"
                      data-fondo-ahorro="'.floatval($trabajador['monto_fondo_ahorro'] ?? 0).'"
                      data-neto="'.floatval($trabajador['neto'] ?? 0).'"
                    >
                    👁️ Ver recibo
                    </button>
                  </td>';
              }
              ?>
            </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    var modalRecibo = document.getElementById('modalRecibo');
    var modalTitle = modalRecibo.querySelector('.modal-title');
    var periodoText = modalRecibo.querySelector('h6');
    var tbody = modalRecibo.querySelector('tbody');
    var netoCell = modalRecibo.querySelector('tfoot td:last-child');

    document.querySelectorAll('.btn-ver-recibo').forEach(function(btn) {
      btn.addEventListener('click', function() {
        // Set nombre y periodo
        modalTitle.textContent = 'Recibo preliminar - ' + btn.getAttribute('data-nombre');
        periodoText.textContent = 'Periodo: ' + btn.getAttribute('data-fecha-inicio') + ' - ' + btn.getAttribute('data-fecha-fin');
        salarioDiarioCell.textContent = parseFloat(btn.getAttribute('data-salario-diario')).toFixed(2);
        // Mostrar valores en los campos superiores del modal
        // Asume que existen elementos con los siguientes IDs en el modal:
        // #vacacionesTomadas, #asistencia, #horasSimples, #horasDobles, #horasTriples

        var vacacionesTomadasCell = modalRecibo.querySelector('#vacacionesTomadas');
        var asistenciaCell = modalRecibo.querySelector('#asistencia');
        var horasSimplesCell = modalRecibo.querySelector('#horasSimples');
        var horasDoblesCell = modalRecibo.querySelector('#horasDobles');
        var horasTriplesCell = modalRecibo.querySelector('#horasTriples');

        if (vacacionesTomadasCell)
          vacacionesTomadasCell.textContent = btn.getAttribute('data-vacaciones-semana') || '0';
        if (asistenciaCell)
          asistenciaCell.textContent = btn.getAttribute('data-asistencia-nomina') || '0';
        if (horasSimplesCell)
          horasSimplesCell.textContent = btn.getAttribute('data-horas-simples') || '0';
        if (horasDoblesCell)
          horasDoblesCell.textContent = btn.getAttribute('data-horas-dobles') || '0';
        if (horasTriplesCell)
          horasTriplesCell.textContent = btn.getAttribute('data-horas-triples') || '0';

        // Construir filas dinámicamente
        var rows = '';
        function addRow(label, value, negative, danger) {
          if (parseFloat(value) != 0) {
            rows += '<tr'+(danger?' class="table-danger"':'')+'><td>'+label+'</td><td>'+(negative?'-':'')+'$'+parseFloat(value).toFixed(2)+'</td></tr>';
          }
        }
        addRow('Horas simples', btn.getAttribute('data-valor-horas-simples'), false, false);
        addRow('Horas dobles', btn.getAttribute('data-valor-horas-dobles'), false, false);
        addRow('Horas triples', btn.getAttribute('data-valor-horas-triples'), false, false);
        addRow('Vacaciones', btn.getAttribute('data-vacaciones'), false, false);
        addRow('Asistencia', btn.getAttribute('data-bono-asistencia'), false, false);
        addRow('Puntualidad', btn.getAttribute('data-bono-puntualidad'), false, false);
        addRow('Despensa', btn.getAttribute('data-despensa'), false, false);
        addRow('Premios', btn.getAttribute('data-premios'), false, false);
        addRow('Subsidio', btn.getAttribute('data-subsidio'), false, false);
        addRow('ISR', btn.getAttribute('data-isr'), true, true);
        addRow('IMSS', btn.getAttribute('data-imss'), true, true);
        addRow('Infonavit', btn.getAttribute('data-infonavit'), true, true);
        addRow('Prestamo', btn.getAttribute('data-prestamos'), true, true);
        addRow('Fondo ahorro', btn.getAttribute('data-fondo-ahorro'), true, true);
        tbody.innerHTML = rows;
        netoCell.textContent = '$' + parseFloat(btn.getAttribute('data-neto')).toFixed(2);
      });
    });
  });
  </script>

  <!-- Modal de recibo -->
  <div class="modal fade" id="modalRecibo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"></h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div style="display:flex; align-items:center;">
            <h6></h6>
          </div>
          <div class="row mb-2">
            <div class="col-md-4">
              <span class="fw-bold">Sueldo diario:</span>
              <span class="text-primary" id="salarioDiarioCell"></span>
            </div>
            <div class="col-md-4">
              <span class="fw-bold">Vacaciones:</span>
              <span class="text-success" id="vacacionesTomadas"></span>
            </div>
            <div class="col-md-4">
              <span class="fw-bold">Asistencia:</span>
              <span class="text-success" id="asistencia"></span>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-4">
              <span class="fw-bold">Horas simples:</span>
              <span class="text-info" id="horasSimples"></span>
            </div>
            <div class="col-md-4">
              <span class="fw-bold">Horas dobles:</span>
              <span class="text-info" id="horasDobles"></span>
            </div>
            <div class="col-md-4">
              <span class="fw-bold">Horas triples:</span>
              <span class="text-info" id="horasTriples"></span>
            </div>
          </div>
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Concepto</th>
                <th>Importe</th>
              </tr>
            </thead>
            <tbody>
              <!-- Las filas se llenan dinámicamente con JS -->
            </tbody>
            <tfoot>
              <tr class="table-success fw-bold">
                <td>Neto a pagar</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancelar</button>
        </div>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 