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
        
        $allowedRoles = ["gerencia", "Proyectos"];
        $userRole = $_SESSION['role'];
    
        if (!in_array($userRole, $allowedRoles)) {
            header("Location: ../access_denied.php");
            exit();
        }
        include '../php/acciones.php';
        $header_loc = 'proyectos';
    ?>
</head>
<body>
    <!-- Barra de navegación -->
    <header>
        <nav class="navegador">
            <div class="logo-container">
                <a href="../celular/home_celular.php">
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
            <div id="proyectos" class="submenu-item" onclick="toggleSubmenu('proyectos')">
                <a href="#">Proyectos</a>
                <div class="submenu-options">
                    <a href="proyectos.php?pestaña=avance_ot&header_loc=<?php echo $header_loc; ?>">Avance de ot</a>
                    <a href="proyectos.php?pestaña=evaluacion_ot&header_loc=<?php echo $header_loc; ?>">Resumen de ot</a>
                    <a href="proyectos.php?pestaña=tiempo_piezas&header_loc=<?php echo $header_loc; ?>">Horas por pieza</a>
                    <a href="proyectos.php?pestaña=desglose_horas&header_loc=<?php echo $header_loc; ?>">Desglose de horas</a>
                    <a href="proyectos.php?pestaña=facturasproyectos&header_loc=<?php echo $header_loc; ?>">Reporte de ots</a>
                    <a href="proyectos.php?pestaña=facturaspedidos&header_loc=<?php echo $header_loc; ?>">Reporte de pedidos</a>
                    <a href="proyectos.php?pestaña=alta_proyectos&header_loc=<?php echo $header_loc; ?>">Alta de proyectos</a>
                    <a href="proyectos.php?pestaña=alta_pedido&header_loc=<?php echo $header_loc; ?>">Alta de pedido</a>
                    <a href="proyectos.php?pestaña=alta_precios&header_loc=<?php echo $header_loc; ?>">Alta de precios</a>
                </div>
            </div>
            <div id="solicitudes" class="submenu-item" onclick="toggleSubmenu('solicitudes')">
                <a href="#">Solicitudes</a>
                <div class="submenu-options">
                    <a href="proyectos.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
                    <a href="proyectos.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
                    <a href="proyectos.php?pestaña=solicitudpieza_celular&header_loc=<?php echo $header_loc; ?>">Solicitud de piezas</a>
                    <a href="proyectos.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
                </div>
            </div>
            <div id="reportes" class="submenu-item" onclick="toggleSubmenu('reportes')">
                <a href="#">Reportes</a>
                <div class="submenu-options">
                <a href="proyectos.php?pestaña=reporte_diario&header_loc=<?php echo $header_loc; ?>">Reporte diario</a>
                    <a href="proyectos.php?pestaña=reporte_actividades&header_loc=<?php echo $header_loc; ?>">Reporte actividades</a>
                    <a href="proyectos.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Reporte semanal</a>
                    <a href="proyectos.php?pestaña=asistencia_fotos&header_loc=<?php echo $header_loc; ?>">Fotos asistencia</a>
                    <a href="proyectos.php?pestaña=reporte_casos&header_loc=<?php echo $header_loc; ?>">Reporte casos especiales</a>
                    <a href="proyectos.php?pestaña=acceso_planta&header_loc=<?php echo $header_loc; ?>">Acceso a plantas</a>
                </div>
            </div>
            <div id="historial" class="submenu-item" onclick="toggleSubmenu('historial')">
                <a href="#">Historiales</a>
                <div class="submenu-options">
                    <a href="proyectos.php?pestaña=historial_de_compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
                    <a href="proyectos.php?pestaña=pedidos&header_loc=<?php echo $header_loc; ?>">Pedidos</a>
                    <a href="proyectos.php?pestaña=facturas&header_loc=<?php echo $header_loc; ?>">Facturas</a>
                    <a href="proyectos.php?pestaña=historial_premios&header_loc=<?php echo $header_loc; ?>">Premios</a>
                </div>
            </div>
            <section class="menu-area">
            <a href="proyectos.php?pestaña=premios&header_loc=<?php echo $header_loc; ?>" class="menu-item">Registro premios</a>
            <a href="proyectos.php?pestaña=horas_tardias_celular&header_loc=<?php echo $header_loc; ?>" class="menu-item">Registro Horas</a>
            <a href="proyectos.php?pestaña=herramienta&header_loc=<?php echo $header_loc; ?>" class="menu-item">Herramienta</a>
            <a href="proyectos.php?pestaña=registro_listas&header_loc=<?php echo $header_loc; ?>" class="menu-item">Registro listas</a>     
            <a href="proyectos.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>" class="menu-item">Nosotros mismos</a>
        </section>
        </section>
    </div>

    <!-- Contenido Principal -->
    <main class="main">
        <?php
            if (isset($_GET['pestaña'])) {
                $pestaña = basename($_GET['pestaña']);
            } else {
                $pestaña = 'avance_ot';
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
