<?php
// Configurar el archivo de log de errores
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/simsa/php_errors.log');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../Normalize.css">
    <link rel="stylesheet" href="../styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <script src="../java/funciones.js"></script>
    <?php
    // Establecer la zona horaria a Monterrey
    date_default_timezone_set('America/Monterrey');
    session_start(); // Inicia la sesión
    
    // Verifica el rol del usuario
    $allowedRoles = ["gerencia", "Proyectos"]; // Roles permitidos para acceder a esta página
    $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión

    // Verifica si el rol del usuario está permitido
    if (!in_array($userRole, $allowedRoles)) {
        // Si el rol no está permitido, redirige a una página de acceso denegado
        header("Location: ../access_denied.php");
        exit();
    }
    include '../php/acciones.php';
    $header_loc='general';
?>
</head>
<style>
  .liga{
    color: blue;
  }
</style>  

<body class="bg-gray-100 text-gray-800">
  <div class="flex h-screen overflow-auto">
    <!-- Sidebar -->
    <aside class="min-w-[16rem] w-64 bg-white shadow-xl p-4 space-y-4 overflow-y-auto flex-shrink-0">
      <!-- Logo/Perfil link -->
      <a href="general.php?header_loc=<?php echo $header_loc; ?>&&pestaña=perfil" onclick="mostrarPerfil()" class="text-2xl font-bold text-blue-600 hover:underline flex items-center w-full text-left">
        <img src="../img/icon.png" alt="Perfil" class="w-8 h-8 mr-2 rounded-full"> Simsa
      </a>

      <!-- Proyectos -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="folder"></i>
          <span>Proyectos</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=facturasproyectos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="layout-grid"></i>Proyectos</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=pedidos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-plus"></i>Pedidos</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=avance_ot" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="bar-chart-2"></i>Avances</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_premios" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="award"></i>Bonos</a>
          </li>
        </ul>
      </div>

      <!-- Producción -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="settings"></i>
          <span>Producción</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=solicitudpieza" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="clipboard-plus"></i>Alta actividades</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=reporte_diario" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Reporte diario</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=reporte_actividades" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="check-square"></i>Remisiones</a>
        </li>
          <?php
        $sql_areas = "SELECT area FROM listas WHERE area IS NOT NULL AND area <> 'gerencia' ORDER BY area";
        $resultado = $conexion->query($sql_areas);
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
            $area = $fila['area'];
            echo '<li>
              <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=areas&area=' . urlencode($area) . '" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left">
            <i data-lucide="layout-dashboard"></i>' . htmlspecialchars($area) . '
              </a>
            </li>';
            }
        } else {
            echo '<li><span class="text-gray-400">Sin áreas</span></li>';
        }
          ?>
        </ul>
      </div>

      <!-- RRHH -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="users"></i>
          <span>RRHH</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=trabajadores" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="user"></i>Trabajadores</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=reporte_asistencia" onclick="cambiarTitulo('Asistencia')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="check-square"></i>Asistencia</a>
          </li>
          <!--<li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=habilitaciones" onclick="cambiarTitulo('Habilitaciones')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="check-circle"></i>Habilitaciones</a>
          </li>-->
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_vacaciones" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="sun"></i>Vacaciones</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_incapacidad" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="heart-pulse"></i>Incapacidades</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_suspensiones" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="shield-off"></i>Suspensiones</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_infonavit" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="home"></i>Infonavit</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_bajas" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="user-minus"></i>Bajas</a>
          </li>
        </ul>
      </div>

      <!-- Compras -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="shopping-cart"></i>
          <span>Compras</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_de_compras" onclick="cambiarTitulo('Facturas')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Compras</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=compras" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-check"></i>Compras Pendientes</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=orden_compras" onclick="cambiarTitulo('Órdenes de compra')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="credit-card"></i>OC Pendientes</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=proveedores" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="truck"></i>Proveedores</a>
          </li>
        </ul>
      </div>

      <!-- Almacén -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="archive"></i>
          <span>Almacén</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=consumibles" onclick="cambiarTitulo('Historial Almacén')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Consumibles</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=solicitud_almacen" onclick="cambiarTitulo('Solicitud Almacén')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-plus"></i>Herramientas</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=almacen_epp" onclick="cambiarTitulo('Almacén EPP')" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="hard-hat"></i>EPP</a>
          </li>
        </ul>
      </div>

      <!-- Finanzas -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="dollar-sign"></i>
          <span>Finanzas</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?pestaña=nomina" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="wallet"></i>Nomina</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=facturas" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Facturas</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=alta_cliente" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="user-plus"></i>Clientes</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=historial_prestamos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Préstamos</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=fondo_ahorro" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="file-text"></i>Fondo Ahorro</a>
          </li>
        </ul>
      </div>

      <!-- Reportes -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="bar-chart"></i>
          <span>Reportes</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=analisis_beneficio&tipo=proyectos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="list"></i>Beneficio</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=analisis_facturas" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="chart-network"></i>Facturas</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=analisis_compras" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="folder-kanban"></i>Compras</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=facturas_mensuales" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="pie-chart"></i>Facturas anuales</a>
          </li>
          <li>
        <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=beneficio_anual" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="pie-chart"></i>Beneficio anual</a>
          </li>
        </ul>
      </div>

      <!-- Configuración -->
      <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center w-full gap-2 p-2 rounded hover:bg-blue-50 focus:outline-none">
          <i data-lucide="settings-2"></i>
          <span>Configuraciones</span>
          <i :class="open ? 'rotate-90' : ''" data-lucide="chevron-right" class="ml-auto transition-transform"></i>
        </button>
        <ul x-show="open" class="space-y-1 pl-6" x-cloak>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=registro_listas" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="list"></i>Listas</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=horarios" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="clock"></i>Horarios</a>
          </li>
            <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=correos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="at-sign"></i>Correos</a>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=usuarios" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="user-cog"></i>Usuarios</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=grupos" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="users"></i>Grupos</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=ubicaciones" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="map"></i>Ubicaciones</a>
          </li>
          <li>
            <a href="general.php?header_loc=<?php echo $header_loc; ?>&pestaña=subir_facturas" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 w-full text-left"><i data-lucide="upload"></i>Subir facturas</a>
          </li>
        </ul>
      </div>

      <!-- Alpine.js for dropdowns -->
      <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </aside>

    <!-- Contenido Principal -->
    <main class="flex-1 p-8">
      <?php
            // Verifica si el parámetro 'pestaña' está presente en la URL
            if (isset($_GET['pestaña'])) {
                // Sanear el valor para evitar inyección de archivos
                $pestaña = basename($_GET['pestaña']);
            } else {
                // Valor predeterminado si no se pasa el parámetro
                $pestaña = 'acceso'; 
            }
            // Incluir el archivo correspondiente a la pestaña
            // Se usa basename() para evitar que el usuario inyecte rutas relativas
            $ruta_pestaña = '../pestañas/'.$pestaña.'.php';
            
            // Verifica si el archivo existe antes de incluirlo
            if (file_exists($ruta_pestaña)) {
                include $ruta_pestaña;
            } else {
                echo "La pestaña solicitada no existe.";
            }
        ?>
    </main>
  </div>

  <script>
    lucide.createIcons();

    function cambiarTitulo(nombre) {
      document.getElementById("tituloContenido").textContent = nombre;
    }

    function mostrarPerfil() {
      cambiarTitulo("Perfil");
    }
  </script>
</body>
</html>

