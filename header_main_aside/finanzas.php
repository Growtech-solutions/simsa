<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas</title>
    <link rel="stylesheet" href="../Normalize.css">
    <link rel="stylesheet" href="../styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <script src="../java/funciones.js"></script>
    <style>
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
    $allowedRoles = ["gerencia", "finanzas","desarrollo"]; // Roles permitidos para acceder a esta página
    $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión
    
    // Verifica si el rol del usuario está permitido
    if (!in_array($userRole, $allowedRoles)) {
        // Si el rol no está permitido, redirige a una página de acceso denegado
        header("Location: ../access_denied.php");
        exit();
    }
    
    include '../php/acciones.php';
    $header_loc='finanzas';
    ?>

</head>
<header>
    <nav class="navegador">
        <a href="../home.php">
            <img class="navlogo" src="../img/logo.png" alt="logo">
        </a>
        <section class="area">
            <a class="area__boton"href="finanzas.php?pestaña=compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
        </section>
        <section class="area">
            <a class="area__boton"href="finanzas.php?pestaña=orden_compras&header_loc=<?php echo $header_loc; ?>">Orden de compras</a>
        </section>
        <section class="area">
            <a class="area__boton"href="finanzas.php?pestaña=facturas_pendientes&header_loc=<?php echo $header_loc; ?>">Facturas pendientes</a>
        </section>
        <section class="area">
                <a class="area__boton"href="finanzas.php?pestaña=Historicos" class="area__boton">Historiales</a>
                <div class="dropdown-content">
                    <a href="finanzas.php?pestaña=historial_de_compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
                    <a href="finanzas.php?pestaña=pedidos&header_loc=<?php echo $header_loc; ?>">Pedidos</a>
                    <a href="finanzas.php?pestaña=facturas&header_loc=<?php echo $header_loc; ?>">Facturas</a>
                    <a href="finanzas.php?pestaña=historial_oc&header_loc=<?php echo $header_loc; ?>">OC</a>
                </div>
            </section>
    </nav>
</header>
    <main class="main">
        <aside>
            <nav class="subnavegador">
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=facturaspedidos&header_loc=<?php echo $header_loc; ?>">Reporte pedidos</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=horas_tardias&header_loc=<?php echo $header_loc; ?>">Horas tardias</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Reporte semanal</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=alta_de_factura&header_loc=<?php echo $header_loc; ?>">Alta de factura</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=alta_cliente&header_loc=<?php echo $header_loc; ?>">Alta de cliente</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=alta_pedido&header_loc=<?php echo $header_loc; ?>">Alta de pedido</a>
                </section>
                <section class="area">
                    <a class="area__boton"href="finanzas.php?pestaña=altadeproveedor&header_loc=<?php echo $header_loc; ?>">Alta de proveedor</a>
                </section>
                <section class="area">
                    <a href="finanzas.php?pestaña=registro_listas&header_loc=<?php echo $header_loc; ?>" class="area__boton">Registro listas</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="finanzas.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>">Nosotros Mismos</a>
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
                $pestaña = 'avance_ot';
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