<?php

// Configuración de paginación
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Contar total de registros
$queryCount = "SELECT COUNT(*) as total FROM proveedor";
$resultCount = mysqli_query($conexion, $queryCount);
$totalRegistros = mysqli_fetch_assoc($resultCount)['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Query con LIMIT para paginación
$query = "SELECT * FROM proveedor ORDER BY id DESC LIMIT $offset, $registrosPorPagina";
$result = mysqli_query($conexion, $query);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Proveedores</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    
    .acciones {
      display: flex;
      gap: 5px;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      padding: 8px 12px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      color: white;
    }
    .btn-editar {
      background-color: #2980b9;
    }
    .btn-eliminar {
      background-color: #c0392b;
    }
    .btn i {
      margin-right: 6px;
    }
    .btn-editar:hover {
      background-color: #1f618d;
    }
    .btn-eliminar:hover {
      background-color: #922b21;
    }
    
    /* Estilos para paginación */
    .paginacion {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 20px 0;
      gap: 5px;
    }
    .paginacion a,
    .paginacion span {
      padding: 8px 12px;
      text-decoration: none;
      border: 1px solid #ddd;
      border-radius: 4px;
      color: #007bff;
    }
    .paginacion a:hover {
      background-color: #e9ecef;
    }
    .paginacion .activa {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }
    .paginacion .deshabilitado {
      color: #6c757d;
      cursor: not-allowed;
    }
    .info-paginacion {
      text-align: center;
      margin: 10px 0;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <div class="principal">
    <div>
    <form method="GET" action="">
        <input type="hidden" name="pestaña" value="altadeproveedor">
        <button type="submit" style="padding: 10px 20px;
          border: none;
          color: white;
          background-color: #007bff;
          border-radius: 4px;
          cursor: pointer;
          font-size: 14px;">+ Registrar Proveedor</button>
    </form>

    <h1 class="text-2xl font-bold text-blue-600">Historial de Proveedores</h2>
    
    <!-- Información de paginación -->
    <div class="info-paginacion">
      Mostrando <?= min($offset + 1, $totalRegistros) ?> - <?= min($offset + $registrosPorPagina, $totalRegistros) ?> de <?= $totalRegistros ?> proveedores
    </div>
    
    <br>
    <table>
      <thead>
        <tr>
          <th>Proveedor</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($row['proveedor']) ?></td>
            <td><?= htmlspecialchars($row['direccion']) ?></td>
            <td><?= htmlspecialchars($row['telefono']) ?></td>
            <td><?= htmlspecialchars($row['correo']) ?></td>
            <td class="acciones">
                <button class="btn btn-editar" type="button" onclick="abrirModalEditar(<?= $row['id'] ?>, '<?= htmlspecialchars($row['proveedor']) ?>', '<?= htmlspecialchars($row['direccion']) ?>', '<?= htmlspecialchars($row['telefono']) ?>', '<?= htmlspecialchars($row['correo']) ?>')">
                <i class="fas fa-edit"></i>Editar
                </button>
                <form action="" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?');">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="accion" value="eliminar_proveedor">
                <button class="btn btn-eliminar" type="submit"><i class="fas fa-trash"></i>Eliminar</button>
                </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    
    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
    <div class="paginacion">
      <!-- Botón Anterior -->
      <?php if ($paginaActual > 1): ?>
        <a href="?pestaña=proveedores&pagina=<?= $paginaActual - 1 ?>">« Anterior</a>
      <?php else: ?>
        <span class="deshabilitado">« Anterior</span>
      <?php endif; ?>
      
      <!-- Números de página -->
      <?php
      $inicio = max(1, $paginaActual - 2);
      $fin = min($totalPaginas, $paginaActual + 2);
      
      if ($inicio > 1) {
        echo '<a href="?pestaña=proveedores&pagina=1">1</a>';
        if ($inicio > 2) echo '<span>...</span>';
      }
      
      for ($i = $inicio; $i <= $fin; $i++) {
        if ($i == $paginaActual) {
          echo '<span class="activa">' . $i . '</span>';
        } else {
          echo '<a href="?pestaña=proveedores&pagina=' . $i . '">' . $i . '</a>';
        }
      }
      
      if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) echo '<span>...</span>';
        echo '<a href="?pestaña=proveedores&pagina=' . $totalPaginas . '">' . $totalPaginas . '</a>';
      }
      ?>
      
      <!-- Botón Siguiente -->
      <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?pestaña=proveedores&pagina=<?= $paginaActual + 1 ?>">Siguiente »</a>
      <?php else: ?>
        <span class="deshabilitado">Siguiente »</span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
        </div>
  </div>
  <!-- Modal para editar proveedor -->
  <div id="modalEditar" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 80%; max-width: 500px;">
      <h2>Editar Proveedor</h2>
      <form id="formEditarProveedor" method="POST" action="">
        <input type="hidden" id="editId" name="id">
        <input type="hidden" name="accion" value="editar_proveedor">
        
        <div style="margin-bottom: 15px;">
          <label for="editProveedor">Proveedor:</label>
          <input type="text" id="editProveedor" name="proveedor"  style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label for="editDireccion">Dirección:</label>
          <input type="text" id="editDireccion" name="direccion"  style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label for="editTelefono">Teléfono:</label>
          <input type="text" id="editTelefono" name="telefono"  style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
          <label for="editCorreo">Correo:</label>
          <input type="email" id="editCorreo" name="correo"  style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        
        <div style="text-align: right;">
          <button type="button" onclick="cerrarModalEditar()" style="padding: 8px 16px; margin-right: 10px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancelar</button>
          <button type="submit" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  function abrirModalEditar(id, proveedor, direccion, telefono, correo) {
    document.getElementById('editId').value = id;
    document.getElementById('editProveedor').value = proveedor;
    document.getElementById('editDireccion').value = direccion;
    document.getElementById('editTelefono').value = telefono;
    document.getElementById('editCorreo').value = correo;
    document.getElementById('modalEditar').style.display = 'block';
  }

  function cerrarModalEditar() {
    document.getElementById('modalEditar').style.display = 'none';
  }

  // Cerrar modal al hacer clic fuera de él
  window.onclick = function(event) {
    var modal = document.getElementById('modalEditar');
    if (event.target == modal) {
      modal.style.display = 'none';
    }
  }
  </script>
</body>
</html>
