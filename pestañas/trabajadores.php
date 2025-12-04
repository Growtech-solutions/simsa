<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ficha del Empleado</title>
  <style>
    .button-trab{
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        
    }
    .grid2 {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 20px;
    }

    .sidebar {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .sidebar img {
      width: 100%;
      border-radius: 10px;
      object-fit: cover;
      margin-bottom: 20px;
    }

    .main-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .tabs {
      display: flex;
      border-bottom: 2px solid #ddd;
      margin-bottom: 15px;
    }

    .tabs button {
      background: none;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: bold;
      color: #555;
    }

    .tabs button.active {
      border-bottom: 3px solid #007bff;
      color: #007bff;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .tab-section {
      margin-bottom: 15px;
    }

    .tab-section label {
      font-weight: bold;
    }

    .field {
      margin-bottom: 10px;
    }
    .modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}

.modal-content {
  background-color: #fff;
  margin: auto;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
  position: absolute;
}

.modal-content h2 {
  text-align: center;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="select"]{
  padding: 10px;
  margin: 5px 0;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

  </style>
</head>
<body>
<?php

$id = $_GET['id'] ?? '1';
// Consulta SQL (solo la base, se debe usar en conexión real y pasar a ejecución con bind parameters)
if ($userRole !== 'gerencia') {
  $sql = "SELECT * FROM trabajadores WHERE area != 'Administracion' AND id = ?";
} else {
   $sql = "SELECT * FROM trabajadores WHERE id = ?";
}
$stmt = $conexion_transimex->prepare($sql);
$stmt->bind_param("i", $id); // i para entero
$stmt->execute();
$resultado = $stmt->get_result();
$empleado = $resultado->fetch_assoc();

if (!$empleado) {
    die("Empleado no encontrado.");
}
$fecha_ingreso = new DateTime($empleado["fecha_ingreso"]); // Fecha de ingreso desde la base de datos
$hoy = new DateTime(); // Fecha actual
$fecha_anterior = clone $fecha_ingreso; // Clonamos para no modificar la original
$fecha_anterior->modify('-1 year');

$vacaciones_tomadas_sql = "SELECT
    SUM(CASE WHEN (e.pieza_ot = 703 OR e.ot_tardia = 703) THEN 1 ELSE 0 END) AS vacaciones
FROM (
    SELECT 
        a.fecha,
        a.id_pieza,
        a.id_trabajador,
        a.ot_tardia,
        p.ot AS pieza_ot
    FROM encargado a
    LEFT JOIN transimex.piezas p ON a.id_pieza = p.id
    WHERE a.id_trabajador = '$id' AND a.fecha BETWEEN '" . $fecha_anterior->format('Y-m-d') . "' AND '" . $hoy->format('Y-m-d') . "'

    UNION ALL

    SELECT 
        b.fecha,
        b.id_pieza,
        b.id_trabajador,
        b.ot_tardia,
        p2.ot AS pieza_ot
    FROM simsa.encargado b
    LEFT JOIN simsa.piezas p2 ON b.id_pieza = p2.id
    WHERE b.id_trabajador = '$id' AND b.fecha BETWEEN '" . $fecha_anterior->format('Y-m-d') . "' AND '" . $hoy->format('Y-m-d') . "'
) AS e
LEFT JOIN transimex.trabajadores t ON e.id_trabajador = t.id";

$vacaciones_tomadas_result = $conexion_transimex->query($vacaciones_tomadas_sql);
$vacaciones_tomadas = $vacaciones_tomadas_result->fetch_row()[0];
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
$sql_vacaciones_tomadas = "SELECT sum(dias_solicitados) AS total_vacaciones 
        FROM vacaciones 
        WHERE id_trabajador = ? 
        AND fecha_inicio >= ? 
        AND fecha_fin <= ?";
$stmt = $conexion_transimex->prepare($sql_vacaciones_tomadas);
$fecha_inicio = $fecha_ingreso->format('Y-m-d');
$fecha_actual = $hoy->format('Y-m-d');
$stmt->bind_param("iss", $empleado['id'], $fecha_inicio, $fecha_actual);
$stmt->execute();
$resultado = $stmt->get_result();
if ($fila = $resultado->fetch_assoc()) {
    $vacaciones_tomadas = $fila['total_vacaciones'] ?? 0;
} else {
    $vacaciones_tomadas = "No se encontraron datos.";
}

$anio_actual = date('Y');
$fecha_inicial = "$anio_actual-01-01";
$fecha_inicial = date('Y-m-d', strtotime($fecha_inicial));
$fecha_final = "$anio_actual-12-31";
$fecha_final = date('Y-m-d', strtotime($fecha_final));




$sql_suspensiones = "
    SELECT SUM(dias) AS total_suspensiones 
    FROM suspensiones 
    WHERE id_trabajador = ? 
    AND fecha_inicial >= ? 
    AND fecha_final <= ?
";

$stmt = $conexion_transimex->prepare($sql_suspensiones);

// Aseguramos que $empleado tenga el ID numérico
$id_trabajador = $empleado['id'] ?? $empleado;

$stmt->bind_param("iss", $id_trabajador, $fecha_inicial, $fecha_final);
$stmt->execute();

$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $total_suspensiones = $fila['total_suspensiones'] ?? 0;
} else {
    $total_suspensiones = 0; // O puedes mostrar un mensaje si es necesario
}

$sql_incapacidades = "
    SELECT SUM(dias) AS total_incapacidades  
    FROM incapacidades 
    WHERE id_trabajador = ? 
    AND fecha_inicio >= ? 
    AND fecha_fin <= ?
";

$stmt = $conexion_transimex->prepare($sql_incapacidades);
$id_trabajador = $empleado['id'] ?? $empleado;
$stmt->bind_param("iss", $id_trabajador, $fecha_inicial, $fecha_final);
$stmt->execute();
$resultado = $stmt->get_result();
if ($fila = $resultado->fetch_assoc()) {
    $total_incapacidades = $fila['total_incapacidades'] ?? 0;
} else {
    $total_incapacidades = 0; // Alternativamente: "No se encontraron datos.";
}

$sql_retardos = "SELECT sum(penalizacion) AS total_retardos 
        FROM asistencia 
        WHERE trabajador_id = ? 
        AND fecha >= ? 
        AND fecha <= ?";
$stmt = $conexion_transimex->prepare($sql_retardos);
$stmt->bind_param("iss", $empleado, $fecha_inicio, $fecha_fin);
$stmt->execute();
$resultado = $stmt->get_result();
if ($fila = $resultado->fetch_assoc()) {
    $total_retardos = $fila['total_retardos'] ?? 0;
} else {
    $total_retardos = "No se encontraron datos.";
}

    // Consulta SQL para generar el reporte
    $sql = "
      SELECT  
        COUNT(CASE WHEN e.ot_tardia = 700 OR piezas.ot = 700 THEN 1 END) AS faltas,
        COUNT(CASE WHEN e.ot_tardia = 701 OR piezas.ot = 701 THEN 1 END) AS permisos,
        COUNT(CASE WHEN e.ot_tardia = 702 OR piezas.ot = 702 THEN 1 END) AS suspensiones,
        COUNT(CASE WHEN e.ot_tardia = 707 OR piezas.ot = 707 THEN 1 END) AS incapacidad_tim,
        COUNT(CASE WHEN e.ot_tardia = 706 OR piezas.ot = 706 THEN 1 END) AS incapacidad_imss,
        COUNT(CASE WHEN e.ot_tardia = 712 OR piezas.ot = 712 THEN 1 END) AS faltas_justificadas,
        SUM(e.tiempo) AS tiempo,
        COALESCE((
          SELECT 
            SUM(retardo.penalizacion) 
          FROM 
            retardo 
          LEFT JOIN 
            trabajadores ON retardo.trabajador = trabajadores.id
          WHERE 
            trabajadores.id = e.id_trabajador 
            AND retardo.fecha BETWEEN '$fecha_inicial' AND '$fecha_final' 
        ), 0) AS retardos
      FROM (
        SELECT fecha, id_pieza, id_trabajador, ot_tardia, tiempo
        FROM encargado
        WHERE id_trabajador = $id AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
        UNION ALL
        SELECT fecha, id_pieza, id_trabajador, ot_tardia, tiempo
        FROM simsa.encargado
        WHERE id_trabajador = $id AND fecha BETWEEN '$fecha_inicial' AND '$fecha_final'
      ) AS e
      LEFT JOIN 
        piezas ON e.id_pieza = piezas.id
      LEFT JOIN 
        trabajadores on e.id_trabajador = trabajadores.id
    ";

    // Ejecutar la consulta
    $resultado_reporte = $conexion_transimex->query($sql);
    // Mostrar los resultados
    if ($resultado_reporte->num_rows > 0) {
        $fila = $resultado_reporte->fetch_assoc();
        $faltas = $fila['faltas'];
        $permisos = $fila['permisos'];
        $suspensiones = $fila['suspensiones'];
        $incapacidad_tim = $fila['incapacidad_tim'];
        $incapacidad_imss = $fila['incapacidad_imss'];
        $incapacidad = $incapacidad_tim + $incapacidad_imss;
        $retardo = $fila['retardos'];
        $tiempo = $fila['tiempo'];
        $faltas_justificadas = $fila['faltas_justificadas'];
    } else {
        echo "No se encontraron resultados.";
    }
?>
<div class="principal">
  <div class="grid2">
    <div style="grid-column: 1 / -1; display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
      <h1 class="text-2xl font-bold text-blue-600" style=" padding-left: 12rem;text-align: center; flex: 1;">Trabajadores</h1>
      <form method="GET" action="">
        <input type="hidden" name="pestaña" value="alta_trabajador">
        <button type="submit" style="padding: 10px 20px;
          border: none;
          color: white;
          background-color: #007bff;
          border-radius: 4px;
          cursor: pointer;
          font-size: 14px;">+ Agregar trabajador</button>
      </form>
    </div>
    <!-- Sidebar -->
    <div class="sidebar">
       <?php
  header('Content-Type: text/html; charset=utf-8');
  mb_internal_encoding('UTF-8');

  function quitarAcentos($cadena) {
    return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $cadena);
  }

  $nombre = quitarAcentos($empleado['nombre']);
  $apellidos = quitarAcentos($empleado['apellidos']);
  $id = $empleado['id']; // Asegúrate de tener este valor

  // Elimina caracteres no válidos para el nombre de la carpeta
  $carpeta = preg_replace('/[^\p{L}\p{N} ]+/u', '', "{$nombre} {$apellidos} {$id}");

  // Construye la URL (sin codificar)
  $fotoUrl = "https://gestor.transimex.com.mx/documentos/RecursosHumanos/trabajadores/{$carpeta}/perfil.png";
  ?>
      <form id="formFotoPerfil" enctype="multipart/form-data" method="post" action="../php/subir_foto_perfil.php" style="text-align:center;">
        <input type="hidden" name="carpeta" value="<?php echo htmlspecialchars($carpeta); ?>">
        <img id="imgPerfil" src="<?php echo $fotoUrl; ?>" alt="Foto del empleado" style="cursor:pointer;" onclick="document.getElementById('inputFotoPerfil').click();">
        <input type="file" id="inputFotoPerfil" name="foto_perfil" accept="image/*" style="display:none;" onchange="document.getElementById('formFotoPerfil').submit();">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <br>
        <small>Haz clic en la imagen para cambiarla</small>
      </form>
      <script>
      // Vista previa opcional (no recarga la página)
      document.getElementById('formFotoPerfil').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        fetch(this.action, {
          method: 'POST',
          body: formData
        })
        .then(r => r.json())
        .then(data => {
          if(data.success && data.url){
            document.getElementById('imgPerfil').src = data.url + '?t=' + Date.now();
          } else {
            alert(data.error || 'Error al subir la imagen');
          }
        })
        .catch(() => alert('Error al subir la imagen'));
      };
      </script>
    <div class="tab-section">
        <div class="field"><label>Empleado:</label> <span onclick="abrirModalBuscador()" style="cursor:pointer; color:blue; text-decoration:underline;"><?php echo htmlspecialchars($empleado['id'] ?? 'No encontrado'); ?></span></div>
        <div class="field"><label>Nombre:</label> <?php echo htmlspecialchars(($empleado['nombre'] ?? 'No encontrado') . ' ' . ($empleado['apellidos'] ?? '')); ?> </div>
        <div class="field"><label>RFC:</label> <?php echo htmlspecialchars($empleado['rfc'] ?? 'No encontrado'); ?></div>
        <div class="field"><label>IMSS:</label> <?php echo htmlspecialchars($empleado['nss'] ?? 'No encontrado'); ?></div>
        <div class="field"><label>CURP:</label> <?php echo htmlspecialchars($empleado['curp'] ?? 'No encontrado'); ?></div>
        <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" onclick="abrirModal('modalbase')">Editar</button>
      </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
      <div class="tabs">
        <button class="tablink active" onclick="openTab(event, 'personal')">Personal</button>
        <button class="tablink" onclick="openTab(event, 'laboral')">Laboral</button>
        <button class="tablink" onclick="openTab(event, 'IMSS')">IMSS</button>
        <button class="tablink" onclick="openTab(event, 'Nomina')">Nómina</button>
        <button class="tablink" onclick="openTab(event, 'Desempeño')">Desempeño</button>
        <button class="tablink" onclick="openTab(event, 'Documentos')">Documentos</button>
      </div>

        <div id="personal" class="tab-content active">
          <div class="tab-section">
            <div class="field"><label>Teléfono:</label> <?php echo htmlspecialchars($empleado['telefono'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Correo:</label> <?php echo htmlspecialchars($empleado['correo'] ?? 'No encontrado'); ?> </div>
            <div class="field"><label>Dirección:</label> <?php echo htmlspecialchars($empleado['direccion'] ?? 'No encontrado'); ?> </div>
            <div class="field"><label>Estado:</label> <?php echo htmlspecialchars($empleado['clave_entidad_fed'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>CP:</label> <?php echo htmlspecialchars($empleado['codigo_postal'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Fecha de nacimiento:</label> <?php echo htmlspecialchars($empleado['fecha_nacimiento'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Regimen Fiscal:</label> <?php echo htmlspecialchars($empleado['regimen_fiscal'] ?? 'No encontrado'); ?></div>
          </div>
          <button 
            style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" onclick="abrirModal('modalPersonal')">Editar
          </button>
      </div>

      <?php if ($empleado['turno']){
          $turno_actual = $empleado['turno'];
          $turnos_sql = "SELECT id_turno, nombre_turno FROM turnos where id_turno = '$turno_actual'";
          $result_turnos = $conexion_transimex->query($turnos_sql);
          $nombre_turno_actual= '';
          if ($result_turnos) {
            while ($row = $result_turnos->fetch_assoc()) {
              $nombre_turno_actual = $row['nombre_turno'];
            }
          }

        }
        if ($empleado['cronograma']==1){
          $cronograma="Activo";
        }
        else{
          $cronograma="Inactivo";
        }
        ?>
      <div id="laboral" class="tab-content">
        <div class="tab-section">
          <div class="field"><label>Empresa:</label> <?php echo htmlspecialchars($empleado['empresa'] ?? 'No encontrado'); ?></div>
          <div class="field"><label>Departamento:</label> <?php echo htmlspecialchars($empleado['area'] ?? 'No encontrado'); ?></div>
          <div class="field"><label>Puesto:</label> <?php echo htmlspecialchars($empleado['puesto'] ?? 'No encontrado'); ?></div>
          <div class="field"><label>Supervisor:</label> <?php echo htmlspecialchars($empleado['supervisor'] ?? 'No encontrado'); ?></div>
          <div class="field"><label>Turno:</label> <?php echo htmlspecialchars($nombre_turno_actual ?? 'No encontrado'); ?></div>
          <div class="field"><label>Cronograma:</label> <?php echo htmlspecialchars($cronograma ?? 'No encontrado'); ?></div>
        </div>
        <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" onclick="abrirModal('modalLaboral')">Editar</button>
      </div>

      <div id="IMSS" class="tab-content">
        <div class="tab-section">
            <div class="field"><label>Fecha de ingreso:</label> <?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Vacaciones:</label> <?php echo htmlspecialchars($vacaciones ?? 'No encontrado'); ?></div>
            <div class="field"><label>Vacaciones tomadas:</label> <?php echo htmlspecialchars($vacaciones_tomadas ?? 'No encontrado'); ?></div>
            <div class="field"><label>Antigüedad:</label> <?php echo htmlspecialchars($antiguedad_años ?? 'No encontrado'); ?> años</div>
            <div class="field"><label>Estado:</label> <?php echo htmlspecialchars($empleado['clave_entidad_fed'] ?? 'No encontrado'); ?></div>
        </div>
        <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" onclick="abrirModal('modalIMSS')">Editar</button>
      </div>

      <div id="Nomina" class="tab-content">
        <div class="tab-section">
            <div class="field"><label>Nomina:</label> <?php echo htmlspecialchars($empleado['contrato'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Contrato:</label> <?php echo htmlspecialchars($empleado['tipo_contrato'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Salario diario:</label> <?php echo htmlspecialchars($empleado['salario'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Complemento:</label> <?php echo htmlspecialchars($empleado['complemento'] ?? '0.00'); ?></div>
            <div class="field"><label>Forma de pago:</label> <?php echo htmlspecialchars($empleado['forma_de_pago'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>CLABE:</label> <?php echo htmlspecialchars($empleado['clave_bancaria'] ?? 'No encontrado'); ?> </div>
            <div class="field"><label>Banco:</label> <?php echo htmlspecialchars($empleado['banco'] ?? 'No encontrado'); ?></div>
            <div class="field"><label>Calculo horas:</label> <?php echo htmlspecialchars($empleado['calculo_horas'] ?? 'No encontrado'); ?></div>
        </div>
        <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" onclick="abrirModal('modalNomina')">Editar</button>
      </div>
      <div id="Desempeño" class="tab-content">
        <div class="tab-section">
          <div class="field"><label>Faltas:</label> <?php echo htmlspecialchars($total_faltas ?? 'No encontrado'); ?></div>
          <div class="field"><label>Suspensiones:</label><?php echo htmlspecialchars($total_suspensiones ?? 'No encontrado'); ?> </div>
          <div class="field"><label>Incapacidad:</label><?php echo htmlspecialchars($total_incapacidades ?? 'No encontrado'); ?> </div>
          <div class="field"><label>Retardos:</label><?php echo htmlspecialchars($total_retardos ?? 'No encontrado'); ?></div>
        </div>
      </div>

      <div id="Documentos" class="tab-content">
        <div class="tab-section">
          <?php
          $directorio = "/var/www/transimex/documentos/RecursosHumanos/trabajadores/{$carpeta}/";
          $baseUrl = "https://gestor.transimex.com.mx/documentos/RecursosHumanos/trabajadores/{$carpeta}/";

          if (is_dir($directorio)) {
            $archivos = array_diff(scandir($directorio), array('.', '..'));
            if (count($archivos) > 0) {
              echo "<ul style='list-style:none; padding:0;'>";
              foreach ($archivos as $archivo) {
                $url = $baseUrl . rawurlencode($archivo);
                $icono = "📄";
                $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','bmp','webp'])) {
                  $icono = "🖼️";
                } elseif ($ext === 'pdf') {
                  $icono = "📄";
                } elseif (in_array($ext, ['doc','docx'])) {
                  $icono = "📝";
                } elseif (in_array($ext, ['xls','xlsx'])) {
                  $icono = "📊";
                }
                echo "<li style='margin-bottom:8px;'><a href='$url' target='_blank' style='text-decoration:none;'>$icono $archivo</a></li>";
              }
              echo "</ul>";
            } else {
              echo "No hay documentos disponibles.";
            }
          } else {
            echo "No se encontró la carpeta de documentos.";
          }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal para seleccionar empleado -->
<div id="modalEmpleados" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000;">
  <div style="background:white; padding:20px; border-radius:10px; width:80%; max-width:600px; position:relative;">
    <button class="button-trab" onclick="cerrarModalBuscador()" style="position:absolute; top:10px; right:10px;">✖</button>
    <h3>Seleccionar Empleado</h3>
    <input type="text" id="filtroEmpleado" placeholder="Buscar por nombre o número" style="width:100%; padding:8px; margin-bottom:10px;">
    <div id="listaEmpleados" style="max-height:300px; overflow:auto; border:1px solid #ccc; padding:10px;">
      <!-- Aquí se cargará la lista con JavaScript/PHP -->
    </div>
  </div>
</div>
<script>
function abrirModalBuscador() {
  document.getElementById("modalEmpleados").style.display = "flex";
  cargarEmpleados(""); // carga inicial vacía
}

function cerrarModalBuscador() {
  document.getElementById("modalEmpleados").style.display = "none";
}

function cargarEmpleados(filtro) {
  const contenedor = document.getElementById('listaEmpleados');
  contenedor.innerHTML = "Cargando...";

  fetch(`../php/buscar_empleados.php?q=${encodeURIComponent(filtro)}`)
    .then(response => response.json())
    .then(data => {
      contenedor.innerHTML = "";
      if (data.length === 0) {
        contenedor.innerHTML = "<p>No se encontraron empleados.</p>";
        return;
      }
      data.forEach(emp => {
        const div = document.createElement('div');
        div.textContent = `${emp.id} - ${emp.nombre}`;
        div.style.cursor = 'pointer';
        div.style.padding = '5px 0';
        div.onclick = () => {
          window.location.href = `?id=${emp.id}&pestaña=trabajadores`;
        };
        contenedor.appendChild(div);
      });
    })
    .catch(error => {
      contenedor.innerHTML = "Error al cargar empleados.";
      console.error(error);
    });
}

document.getElementById("filtroEmpleado").addEventListener("input", function() {
  cargarEmpleados(this.value);
});

function openTab(evt, tabId) {
  const tabs = document.querySelectorAll('.tab-content');
  tabs.forEach(tab => tab.classList.remove('active'));

  const buttons = document.querySelectorAll('.tablink');
  buttons.forEach(btn => btn.classList.remove('active'));

  document.getElementById(tabId).classList.add('active');
  evt.currentTarget.classList.add('active');
}

function abrirModal(id) {
  document.getElementById(id).style.display = 'block';
}

function cerrarModal(id) {
  document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
  document.querySelectorAll('.modal').forEach(modal => {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });
}

</script>
<!-- Modal Base -->
<div id="modalbase" class="modal">
  <div class="reporte_formulario modal-content">
    <span class="close" onclick="cerrarModal('modalbase')">&times;</span>
    <h2>Editar Información Básica</h2>
    <form id="formBase" action="" method="post">
      <label>Nombre: <input type="text" name="nombre" value="<?php echo $empleado['nombre']; ?>"></label><br>
      <label>Apellidos: <input type="text" name="apellidos" value="<?php echo $empleado['apellidos']; ?>"></label><br>
      <label>RFC: <input type="text" name="rfc" value="<?php echo $empleado['rfc']; ?>"></label><br>
      <label>NSS: <input type="text" name="nss" value="<?php echo $empleado['nss']; ?>"></label><br>
      <label>CURP: <input type="text" name="curp" value="<?php echo $empleado['curp']; ?>"></label><br>
      <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
      <input type="hidden" name="pestaña" value="trabajadores">
      <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" type="submit" name="guardar_base">Guardar</button>
    </form>
  </div>
</div>
<!-- Modal Personal -->
<div id="modalPersonal" class="modal">
  <div class="reporte_formulario modal-content">
    <span class="close" onclick="cerrarModal('modalPersonal')">&times;</span>
    <h2>Editar Información Personal</h2>
    <form id="formPersonal" action="" method="post">
      <label>Teléfono: <input type="text" name="telefono" value="<?php echo $empleado['telefono']; ?>"></label><br>
      <label>Correo: <input type="text" name="correo" value="<?php echo $empleado['correo']; ?>"></label><br>
      <label>Dirección: <input type="text" name="direccion" value="<?php echo $empleado['direccion']; ?>"></label><br>
      <label>Estado: 
        <select name="estado" id="estado" required>
          <option value="">Estado</option>
          <?php
          $ent_fed = "SELECT estado, cla FROM entidades_federativas";
          $result = mysqli_query($conexion_transimex, $ent_fed);
          $estado_actual = $empleado['clave_entidad_fed'] ?? '';
          while ($row = mysqli_fetch_array($result)) {
          $selected = ($row['cla'] == $estado_actual) ? "selected" : "";
          echo "<option value='" . htmlspecialchars($row['cla']) . "' $selected>" . htmlspecialchars($row['estado']) . "</option>";
          }
          ?>
        </select>
      </label><br>
      <label>CP: <input type="text" name="codigo_postal" value="<?php echo $empleado['codigo_postal']; ?>"></label><br>
      <label>Fecha de nacimiento: <input type="date" name="fecha_nacimiento" value="<?php echo $empleado['fecha_nacimiento']; ?>"></label><br>
      <label>Regimen Fiscal: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('RegimenFiscal', 'Clave', 'regimen_fiscal', '', $empleado['regimen_fiscal']); ?></label><br>
      <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
      <input type="hidden" name="pestaña" value="trabajadores">
      <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" type="submit" name="guardar_personal">Guardar</button>
    </form>
  </div>
</div>

<!-- Modal Laboral -->
<div id="modalLaboral" class="modal">
  <div class="reporte_formulario modal-content">
    <span class="close" onclick="cerrarModal('modalLaboral')">&times;</span>
    <h2>Editar Información Laboral</h2>
    <form id="formLaboral" action="" method="post">
      <label>Empresa: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'empresa', 'empresa', '', $empleado['empresa']);?></label><br>
      <label>Departamento: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'area', 'area', '', $empleado['area']); ?></label><br>
      <label>Puesto: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'puesto', 'puesto', '', $empleado['puesto']); ?></label><br>
      <label>Supervisor: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'supervisor', 'supervisor', '', $empleado['supervisor']); ?></label><br>
      <label>Turno: 
        <select name="turno" required>
          <?php
              $turnos_sql = "SELECT id_turno, nombre_turno FROM turnos";
              $result_turnos = $conexion_transimex->query($turnos_sql);
              $turno_actual = $empleado['turno'] ?? '';
              if ($result_turnos) {
              while ($row = $result_turnos->fetch_assoc()) {
                $selected = ($row['nombre_turno'] == $turno_actual) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($row['id_turno']) . "' $selected>" . htmlspecialchars($row['nombre_turno']) . "</option>";
              }
              }
          ?>
        </select>
      </label><br>
      <label>Cronograma: 
        <select name="cronograma" required>
          <option value="1" <?php echo ($empleado['cronograma'] == 1) ? "selected" : ""; ?>>Activo</option>
          <option value="0" <?php echo ($empleado['cronograma'] == 0) ? "selected" : ""; ?>>Inactivo</option>
        </select>
      </label><br>
      <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
      <input type="hidden" name="pestaña" value="trabajadores">
      <button type="submit" style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" name="guardar_laboral">Guardar</button>
    </form>
  </div>
</div>

<!-- Modal IMSS -->
<div id="modalIMSS" class="modal">
  <div class="reporte_formulario modal-content">
    <span class="close" onclick="cerrarModal('modalIMSS')">&times;</span>
    <h2>Editar Información del IMSS</h2>
    <form id="formIMSS" action="" method="post">
      <label>Fecha de ingreso: <input type="date" name="fecha_ingreso" value="<?php echo $empleado['fecha_ingreso']; ?>"></label><br>
      <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
      <input type="hidden" name="pestaña" value="trabajadores">
      <button  style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" name="guardar_imss" type="submit">Guardar</button>
    </form>
  </div>
</div>

<!-- Modal Nomina -->
<div id="modalNomina" class="modal">
  <div class="reporte_formulario modal-content">
    <span class="close" onclick="cerrarModal('modalNomina')">&times;</span>
    <h2>Editar Información de Nómina</h2>
    <form id="formNomina" action="" method="post">
      <label>Nomina: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'contrato', 'contrato', '', $empleado['contrato']); ?></label><br>
      <label>Contrato: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'tipo_contrato', 'tipo_contrato', '', $empleado['tipo_contrato']); ?></label><br>
      <label>Salario diario: <input type="text" name="salario" value="<?php echo $empleado['salario']; ?>"></label><br>
      <label>Complemento: <input type="text" name="complemento" value="<?php echo isset($empleado['complemento']) ? $empleado['complemento'] : '0.00'; ?>"></label><br>
      <label>Forma de pago: <?php echo $selectDatosExistentes->obtenerOpcionesExistentes('listas', 'forma_pago', 'forma_pago', '', $empleado['forma_de_pago']); ?></label><br>
      <label>CLABE: <input type="text" name="clave_bancaria" value="<?php echo $empleado['clave_bancaria']; ?>"></label><br>
      <label>Banco: <input type="text" name="banco" value="<?php echo $empleado['banco']; ?>"></label><br>
      <label>Calculo horas: 
        <select name="calculo_horas">
          <option value="<?php echo $empleado['calculo_horas']; ?>" selected>
              <?php echo $empleado['calculo_horas']; ?>
          </option>
          <option value="Reloj" <?php echo ($empleado['calculo_horas'] == 'reloj') ? "selected" : ""; ?>>Reloj</option>
          <option value="Reporte" <?php echo ($empleado['calculo_horas'] == 'reporte') ? "selected" : ""; ?>>Reporte</option>
          <option value="Automatico" <?php echo ($empleado['calculo_horas'] == 'automatico') ? "selected" : ""; ?>>Automático</option>
        </select>
      </label><br>
      <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">
      <input type="hidden" name="pestaña" value="trabajadores">
      <button style="padding: 10px 20px;
            border: none;
            color: white;
            background-color: #007bff;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;" name="guardar_nomina" type="submit" >Guardar</button>
    </form>
  </div>
</div>

</body>
</html>
