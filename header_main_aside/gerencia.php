<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Normalize.css">
    <link rel="stylesheet" href="../styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <link rel="icon" href="../img/icon.ico" type="image/x-icon">
    <script src="../java/funciones.js"></script>
    <style>
        /* Estilos para el menú desplegable */
.area {
    position: relative;
    display: inline-block;
}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var params = new URLSearchParams(window.location.search);
            var pestaña = params.get('pestaña');

            document.querySelectorAll('.area__boton').forEach(function (boton) {
                var botonPestaña = new URL(boton.href).searchParams.get('pestaña');
                if (botonPestaña === pestaña) {
                    boton.classList.add('active');
                }
            });
        });
    </script>
    
    <?php
        // Establecer la zona horaria a Monterrey
        date_default_timezone_set('America/Monterrey');
        session_start(); // Inicia la sesión
        
        // Verifica el rol del usuario
        $allowedRoles = ["gerencia"]; // Roles permitidos para acceder a esta página
        $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión
    
        // Verifica si el rol del usuario está permitido
        if (!in_array($userRole, $allowedRoles)) {
            // Si el rol no está permitido, redirige a una página de acceso denegado
            header("Location: ../access_denied.php");
            exit();
        }
        include '../php/acciones.php';
        $header_loc='gerencia';
    ?>
</head>
<body>
    <header>
    <nav class="navegador">
            <a href="../home.php">
                <img class="navlogo" src="../img/logo.png" alt="logo">
            </a>
            <section class="area">
    <a href="gerencia.php" class="area__boton">Gerencia</a>
    <div class="dropdown-content">
        <a href="gerencia.php?pestaña=empresa&header_loc=<?php echo $header_loc; ?>">Empresa</a>
        <a href="gerencia.php?pestaña=evaluacion_ot&header_loc=<?php echo $header_loc; ?>">Resumen de ot</a>
        <a href="gerencia.php?pestaña=avance_ot&header_loc=<?php echo $header_loc; ?>">Avance de proyecto</a>
        <a href="gerencia.php?pestaña=tiempo_piezas&header_loc=<?php echo $header_loc; ?>">Horas por pieza</a>
        <a href="gerencia.php?pestaña=desglose_horas&header_loc=<?php echo $header_loc; ?>">Desglose de horas</a>
        <a href="gerencia.php?pestaña=facturasproyectos&header_loc=<?php echo $header_loc; ?>">Reporte proyectos</a>
        <a href="gerencia.php?pestaña=facturaspedidos&header_loc=<?php echo $header_loc; ?>">Reporte pedidos</a>
    </div>
</section>

<section class="area">
    <a href="gerencia.php" class="area__boton">Historial</a>
    <div class="dropdown-content">
        <a href="gerencia.php?pestaña=compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
        <a href="gerencia.php?pestaña=facturas&header_loc=<?php echo $header_loc; ?>">Facturas</a>
        <a href="gerencia.php?pestaña=pedidos&header_loc=<?php echo $header_loc; ?>">Pedidos</a>
        <a href="gerencia.php?pestaña=historial_premios&header_loc=<?php echo $header_loc; ?>">Premios</a>
        <a href="gerencia.php?pestaña=prestamos&header_loc=<?php echo $header_loc; ?>">Prestamos</a>
        <a href="gerencia.php?pestaña=herramienta&header_loc=<?php echo $header_loc; ?>">Herramienta</a>
        <a href="gerencia.php?pestaña=consumibles&header_loc=<?php echo $header_loc; ?>">Consumibles</a>
        <a href="gerencia.php?pestaña=nomina&header_loc=<?php echo $header_loc; ?>">Nomina</a>
    </div>
</section>

<section class="area">
    <a href="gerencia.php" class="area__boton">Registro</a>
    <div class="dropdown-content">
        <a href="gerencia.php?pestaña=alta_proyectos&header_loc=<?php echo $header_loc; ?>">Alta ot</a>
        <a href="gerencia.php?pestaña=alta_pedido&header_loc=<?php echo $header_loc; ?>">Alta contrato</a>
        <a href="proyectos.php?pestaña=alta_precios&header_loc=<?php echo $header_loc; ?>">Alta de precios</a>
        <a href="gerencia.php?pestaña=horas_tardias&header_loc=<?php echo $header_loc; ?>">Hrs tardías</a>
        <a href="gerencia.php?pestaña=alta_trabajador&header_loc=<?php echo $header_loc; ?>">Alta trabajador</a>
        <a href="gerencia.php?pestaña=registro_listas&header_loc=<?php echo $header_loc; ?>">Registro listas</a>
        <a href="gerencia.php?pestaña=premios&header_loc=<?php echo $header_loc; ?>">Alta premios</a>
        <a href="gerencia.php?pestaña=alta_prestamo&header_loc=<?php echo $header_loc; ?>">Alta préstamos</a>
        <a href="gerencia.php?pestaña=alta_infonavit&header_loc=<?php echo $header_loc; ?>">Infonavit</a>
        <a href="gerencia.php?pestaña=baja_trabajador&header_loc=<?php echo $header_loc; ?>">Bajas</a>
        <a href="gerencia.php?pestaña=registro&header_loc=<?php echo $header_loc; ?>">Usuario</a>
    </div>
</section>

<section class="area">
    <a href="gerencia.php" class="area__boton">Análisis</a>
    <div class="dropdown-content">
        <a href="gerencia.php?pestaña=analisis_beneficio&header_loc=<?php echo $header_loc; ?>">Beneficio</a>
        <a href="gerencia.php?pestaña=analisis_facturas&header_loc=<?php echo $header_loc; ?>">Facturas</a>
        <a href="gerencia.php?pestaña=analisis_pedidos&header_loc=<?php echo $header_loc; ?>">Pedidos</a>
        <a href="gerencia.php?pestaña=analisis_compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
        <a href="gerencia.php?pestaña=analisis_nomina&header_loc=<?php echo $header_loc; ?>">Nomina</a>
        <a href="gerencia.php?pestaña=facturas_mensuales&header_loc=<?php echo $header_loc; ?>">Facturas anual</a>
        <a href="gerencia.php?pestaña=beneficio_anual&header_loc=<?php echo $header_loc; ?>">Beneficio anual</a>
    </div>
</section>

<section class="area">
    <a href="gerencia.php" class="area__boton">Reporte</a>
    <div class="dropdown-content">
        <a href="gerencia.php?pestaña=reporte_diario&header_loc=<?php echo $header_loc; ?>">Diario</a>
        <a href="gerencia.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Semanal</a>
        <a href="gerencia.php?pestaña=reporte_casos&header_loc=<?php echo $header_loc; ?>">Casos especiales</a>
    </div>
</section>

        </nav>
    </header>
    
    <main class="main">
        <aside>
           <nav class="subnavegador">
           <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=trabajadores&header_loc=<?php echo $header_loc; ?>">Trabajadores</a>
            </section>
            <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=solicitudpieza&header_loc=<?php echo $header_loc; ?>">Solicitud Pieza</a>
            </section>
            <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
            </section>
            <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
            </section>
            <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
            </section>
            <section class="area">
                <a class="area__boton" href="gerencia.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>">Nosotros Mismos</a>
            </section>
           </nav>
        </aside>
        <?php
            // Verifica si el parámetro 'pestaña' está presente en la URL
            if (isset($_GET['pestaña'])) {
                // Sanear el valor para evitar inyección de archivos
                $pestaña = basename($_GET['pestaña']);
            } else {
                // Valor predeterminado si no se pasa el parámetro
                $pestaña = 'evaluacion_ot';
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
        </html>