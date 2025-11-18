<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <style>
        
        .contenedor__servicios {
            width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .titulo {
            color: #2c3e50;
            font-size: 2.2em;
            margin-bottom: 30px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .titulo i {
            color: #4CAF50;
        }
        
        /* Botones unificados */
        .btn {
            padding: 12px 24px;
            margin: 2px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        
        .btn-new {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #008CBA, #007bb5);
            color: white;
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .btn-filter {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        
        .btn-clear {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-cancel {
            background: #fff;
            color: #666;
            border: 2px solid #ddd;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }
        
        .btn-update {
            background: linear-gradient(135deg, #008CBA, #007bb5);
            color: white;
        }
        
        /* Sección de filtros unificada */
        .filtros {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        
        .filtros h4 {
            color: #495057;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group input {
            width: 80%;
            padding: 12px 40px 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .input-group input:focus {
            outline: none;
            border-color: #007bff;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .input-icon {
            position: absolute;
            right: 10%;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 16px;
        }
        
        /* Tabla unificada */
        .tabla-container {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow-x: auto;
            margin-bottom: 25px;
        }
        
        
        
        
        
        /* Paginación unificada */
        .paginacion {
            margin-top: 20px;
            text-align: center;
            padding: 20px 0;
        }
        
        .paginacion a {
            display: inline-block;
            padding: 10px 15px;
            margin: 0 4px;
            text-decoration: none;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
            background: #fff;
        }
        
        .paginacion a:hover {
            border-color: #007bff;
            color: #007bff;
            transform: translateY(-1px);
        }
        
        .paginacion a.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-color: #007bff;
        }
        
        /* Modales unificados */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: #ffffff;
            margin: 3% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
        }
        
        .modal h3 {
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 1.5em;
            font-weight: 600;
            text-align: center;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            margin-top: -5px;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .form-grid.single {
            grid-template-columns: 1fr;
        }
        
        .form-input {
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #4CAF50;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        /* Animaciones */
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .contenedor__servicios {
                padding: 15px;
            }
            
            .titulo {
                font-size: 1.8em;
            }
            
            .filtros-grid {
                grid-template-columns: 1fr;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .tabla-container {
                padding: 10px;
            }
            
            .paginacion a {
                padding: 8px 10px;
                margin: 0 2px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body id="alta_cliente">

<?php
// Tu código PHP existente aquí
// Configuración de paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Procesar alta de cliente
if (isset($_POST['alta_cliente'])) {
    $nombre = $_POST['nombre'];
    $rfc = $_POST['rfc'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $municipio = $_POST['municipio'];
    $estado = $_POST['estado'];
    $pais = $_POST['pais'];
    $codigo_postal = $_POST['codigo_postal'];

    $sql = "INSERT INTO cliente (razon_social, rfc, email, telefono, ciudad, estado, pais, codigo_postal, fecha_alta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssss", $nombre, $rfc, $email, $telefono, $municipio, $estado, $pais, $codigo_postal);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cliente guardado correctamente');</script>";
    } else {
        echo "<script>alert('Error al guardar cliente');</script>";
    }
}

// Procesar edición de cliente
if (isset($_POST['editar_cliente'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $rfc = $_POST['rfc'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $municipio = $_POST['municipio'];
    $estado = $_POST['estado'];
    $pais = $_POST['pais'];
    $codigo_postal = $_POST['codigo_postal'];

    $sql = "UPDATE cliente SET razon_social=?, rfc=?, email=?, telefono=?, ciudad=?, estado=?, pais=?, codigo_postal=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $rfc, $email, $telefono, $municipio, $estado, $pais, $codigo_postal, $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cliente actualizado correctamente');</script>";
    } else {
        echo "<script>alert('Error al actualizar cliente');</script>";
    }
}

// Construcción de filtros
$where_conditions = [];
$params = [];
$types = "";

if (!empty($_GET['nombre'])) {
    $where_conditions[] = "razon_social LIKE ?";
    $params[] = "%" . $_GET['nombre'] . "%";
    $types .= "s";
}
if (!empty($_GET['rfc'])) {
    $where_conditions[] = "rfc LIKE ?";
    $params[] = "%" . $_GET['rfc'] . "%";
    $types .= "s";
}
if (!empty($_GET['estado'])) {
    $where_conditions[] = "estado LIKE ?";
    $params[] = "%" . $_GET['estado'] . "%";
    $types .= "s";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM cliente $where_clause";
if (!empty($params)) {
    $count_stmt = $conexion->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $total_registros = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_registros = $conexion->query($count_sql)->fetch_assoc()['total'];
}

$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obtener clientes
$sql = "SELECT * FROM cliente $where_clause ORDER BY fecha_alta DESC LIMIT $registros_por_pagina OFFSET $offset";
if (!empty($params)) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conexion->query($sql);
}
?>

<div class="contenedor__servicios">
    
    
    <!-- Botón para nuevo cliente -->
    
    
    <!-- Tabla de clientes -->
    <div class="tabla-container">
        <div style="text-align: left; margin-bottom: 25px;">
        <button class="btn btn-new" onclick="abrirModal('modalAlta')">
            <span style="margin-right: 8px;">➕</span>
            Nuevo Cliente
        </button>
    </div>
        <h2 class="titulo">
        Gestión de Clientes
    </h2>
    <form method="GET">
            <div class="filtros-grid">
                <div class="input-group">
                    <input type="text" 
                           name="nombre" 
                           placeholder="Buscar por nombre..." 
                           value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : ''; ?>">
                    <span class="input-icon">👤</span>
                </div>
                
                <div class="input-group">
                    <input type="text" 
                           name="rfc" 
                           placeholder="Buscar por RFC..." 
                           value="<?php echo isset($_GET['rfc']) ? htmlspecialchars($_GET['rfc']) : ''; ?>">
                    <span class="input-icon">📄</span>
                </div>
                
                <div class="input-group">
                    <input type="text" 
                           name="estado" 
                           placeholder="Buscar por estado..." 
                           value="<?php echo isset($_GET['estado']) ? htmlspecialchars($_GET['estado']) : ''; ?>">
                    <span class="input-icon">📍</span>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="submit" class="btn btn-filter">
                    <span style="margin-right: 6px;">🔎</span>
                    Filtrar
                </button>
                <a href="?" class="btn btn-clear">
                    <span style="margin-right: 6px;">🧹</span>
                    Limpiar
                </a>
            </div>
            <input type="hidden" name="pestaña" value="alta_cliente">
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>RFC</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cliente = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><?php echo htmlspecialchars($cliente['razon_social']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['rfc']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['ciudad']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['estado']); ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editarCliente(<?php echo htmlspecialchars(json_encode($cliente)); ?>)">
                            ✏️ Editar
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Paginación -->
    <div class="paginacion">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <a href="?pagina=<?php echo $i; ?>&<?php
                // Mantener los parámetros de filtro en la paginación
                $query_params = $_GET;
                unset($query_params['pagina']);
                echo http_build_query($query_params); ?>" 
               class="<?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    </div>
    
    
</div>

<!-- Modal para alta de cliente -->
<div id="modalAlta" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal('modalAlta')">&times;</span>
        <h3>✨ Nuevo Cliente</h3>
        <form method="POST">
            <input type="hidden" name="alta_cliente" value="1">
            
            <div class="form-grid">
                <input class="form-input" type="text" name="nombre" placeholder="Nombre completo" required>
                <input class="form-input" type="text" name="rfc" placeholder="RFC" required>
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="email" name="email" placeholder="Correo electrónico">
                <input class="form-input" type="text" name="telefono" placeholder="Teléfono">
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="text" name="municipio" placeholder="Municipio">
                <input class="form-input" type="text" name="estado" placeholder="Estado">
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="text" name="pais" placeholder="País">
                <input class="form-input" type="text" name="codigo_postal" placeholder="Código Postal">
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal('modalAlta')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-save">
                    💾 Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para editar cliente -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal('modalEditar')">&times;</span>
        <h3>✏️ Editar Cliente</h3>
        <form method="POST" id="formEditar">
            <input type="hidden" name="editar_cliente" value="1">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-grid">
                <input class="form-input" type="text" name="nombre" id="edit_nombre" placeholder="Nombre completo" required>
                <input class="form-input" type="text" name="rfc" id="edit_rfc" placeholder="RFC" required>
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="email" name="email" id="edit_email" placeholder="Correo electrónico">
                <input class="form-input" type="text" name="telefono" id="edit_telefono" placeholder="Teléfono">
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="text" name="municipio" id="edit_municipio" placeholder="Municipio">
                <input class="form-input" type="text" name="estado" id="edit_estado" placeholder="Estado">
            </div>
            
            <div class="form-grid">
                <input class="form-input" type="text" name="pais" id="edit_pais" placeholder="País">
                <input class="form-input" type="text" name="codigo_postal" id="edit_codigo_postal" placeholder="Código Postal">
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="cerrarModal('modalEditar')">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-update">
                    🔄 Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function editarCliente(cliente) {
    document.getElementById('edit_id').value = cliente.id;
    document.getElementById('edit_nombre').value = cliente.razon_social;
    document.getElementById('edit_rfc').value = cliente.rfc;
    document.getElementById('edit_email').value = cliente.email;
    document.getElementById('edit_telefono').value = cliente.telefono;
    document.getElementById('edit_municipio').value = cliente.ciudad;
    document.getElementById('edit_estado').value = cliente.estado;
    document.getElementById('edit_pais').value = cliente.pais;
    document.getElementById('edit_codigo_postal').value = cliente.codigo_postal;
    abrirModal('modalEditar');
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

</body>
</html>
