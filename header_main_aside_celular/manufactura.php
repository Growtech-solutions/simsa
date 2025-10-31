<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link rel="stylesheet" href="../Normalize.css">
    <link rel="stylesheet" href="../styles_celular.css?v=<?php echo time(); ?>">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <link rel="manifest" href="../manifest.json">

    <script src="../java/funciones.js"></script>
    <script>
        function toggleMenu() {
            const menu = document.querySelector(".menu-lateral");
            menu.classList.toggle("active");
        }
        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            submenu.classList.toggle("active");
        }
    </script>
    <?php
        date_default_timezone_set('America/Monterrey');
        session_start();
        
        $allowedRoles = ["gerencia", "Proyectos","manufactura"];
        $userRole = $_SESSION['role'];
    
        if (!in_array($userRole, $allowedRoles)) {
            header("Location: ../access_denied.php");
            exit();
        }
        include '../php/acciones.php';
        $header_loc = 'manufactura';
    ?>
</head>
<body>
    <!-- Barra de navegación -->
    <header>
        <nav class="navegador">
            <div class="logo-container">
                <a href="../celular/index.php">
                    <img class="navlogo" src="../img/logo.png" alt="logo">
                </a>
                <span class="simsa">SIMSA</span>
            </div>
            <div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
        </nav>
    </header>       

    <!-- Menú lateral -->
    <div class="menu-lateral">
        <section class="espacio_menu">
            <nav>
                <h1>e</h1>
            </nav>
        </section>
        <br>
        <section class="submenu">
            <div id="solicitudes" class="submenu-item" onclick="toggleSubmenu('solicitudes')">
                <a href="#">Solicitudes</a>
                <div class="submenu-options">
                    <a href="manufactura.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
                    <a href="manufactura.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
                    <a href="manufactura.php?pestaña=solicitudpieza_celular&header_loc=<?php echo $header_loc; ?>">Solicitud de piezas</a>
                    <a href="manufactura.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
                </div>
            </div>
            <div id="areas" class="submenu-item" onclick="toggleSubmenu('areas')">
                <a href="#">Áreas</a>
                <div class="submenu-options">
                    <?php
                    // Consulta para obtener las áreas
                    $sql_areas = "SELECT area FROM listas WHERE area IS NOT NULL ORDER BY area";
                    $resultado = $conexion->query($sql_areas);

                    // Verificar si hay resultados
                    if ($resultado->num_rows > 0) {
                        // Mostrar las áreas
                        while ($fila = $resultado->fetch_assoc()) {
                            $area = $fila['area'];  // Asumiendo que $campo es el nombre de la columna de áreas
                            echo '<a href="proyectos.php?pestaña=areas&header_loc='.$header_loc.'&area=' . urlencode($area) . '">' . htmlspecialchars($area) . '</a>';
                        }
                    } else {
                        echo '<a href="">Sin áreas</a>';
                    }

                    ?>
                </div>
            </div>
            <div id="historial" class="submenu-item" onclick="toggleSubmenu('historial')">
            </div>
            <section class="menu-area">
                <a href="manufactura.php?pestaña=asistencia&header_loc=<?php echo $header_loc; ?>" class="menu-item">Registro Asistencia</a> 
                <a href="manufactura.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>" class="menu-item">Reporte Semanal</a> 
                <a href="manufactura.php?pestaña=horas_tardias_celular&header_loc=<?php echo $header_loc; ?>" class="menu-item">Registro Horas</a>     
                <a href="manufactura.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>" class="menu-item">Nosotros mismos</a>
            </section>
        </section>
    </div>

    <!-- Contenido Principal -->
    <main class="main">
        <?php
            if (isset($_GET['pestaña'])) {
                $pestaña = basename($_GET['pestaña']);
            } else {
                $pestaña = 'horas_tardias_celular';
            }
            $ruta_pestaña = '../pestañas/'.$pestaña.'.php';
            if (file_exists($ruta_pestaña)) {
                include $ruta_pestaña;
            } else {
                echo "La pestaña solicitada no existe.";
            }
        ?>
    </main>
</body>
</html>