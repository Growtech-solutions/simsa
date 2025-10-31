<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        $allowedRoles = ["gerencia", "Proyectos"]; // Roles permitidos para acceder a esta página
        $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión
    
        // Verifica si el rol del usuario está permitido
        if (!in_array($userRole, $allowedRoles)) {
            // Si el rol no está permitido, redirige a una página de acceso denegado
            header("Location: ../access_denied.php");
            exit();
        }
        include '../php/acciones.php';
        $header_loc='recursos_humanos';
    ?>
</head>
<body>
    <header>
        <nav class="navegador">
            <a href="../home.php">
                <img class="navlogo" src="../img/logo.png" alt="logo">
            </a>
            <section class="area">
                <a href="recursos_humanos.php?pestaña=registro_actividad&header_loc=<?php echo $header_loc; ?>" class="area__boton">Actividades</a>
                <div class="dropdown-content">
                    <a href="recursos_humanos.php?pestaña=registro_actividad&header_loc=<?php echo $header_loc; ?>">Registro actividad</a>
                    <a href="recursos_humanos.php?pestaña=lista_pendientes&header_loc=<?php echo $header_loc; ?>">Lista pendientes</a>
                    <a href="recursos_humanos.php?pestaña=habilitaciones&header_loc=<?php echo $header_loc; ?>">Habilitaciones</a>
                    <a href="recursos_humanos.php?pestaña=acceso_planta&header_loc=<?php echo $header_loc; ?>">Acceso a plantas</a>
                    <a href="recursos_humanos.php?pestaña=alta_actividad&header_loc=<?php echo $header_loc; ?>">Alta actividad</a>
                    <a href="recursos_humanos.php?pestaña=alta_periodico&header_loc=<?php echo $header_loc; ?>">Alta periodico</a>
                </div>
            </section>
            <section class="area">
                <a href="recursos_humanos.php?pestaña=almacen_epp&header_loc=<?php echo $header_loc; ?>" class="area__boton">EPP</a>
                <div class="dropdown-content">
                    <a href="recursos_humanos.php?pestaña=almacen_epp&header_loc=<?php echo $header_loc; ?>">EPP</a>
                    <a href="recursos_humanos.php?pestaña=entrada_epp&header_loc=<?php echo $header_loc; ?>">Entrada EPP</a>
                    <a href="recursos_humanos.php?pestaña=historico_epp&header_loc=<?php echo $header_loc; ?>">Movimientos</a>
                </div>
            </section>
            <section class="area">
                <a href="recursos_humanos.php?pestaña=trabajadores&header_loc=<?php echo $header_loc; ?>" class="area__boton">Trabajadores</a>
                <div class="dropdown-content">
                    <a href="recursos_humanos.php?pestaña=alta_trabajador&header_loc=<?php echo $header_loc; ?>">Alta trabajador</a>
                    <a href="recursos_humanos.php?pestaña=trabajadores&header_loc=<?php echo $header_loc; ?>">Editar trabajador</a>
                    <a href="recursos_humanos.php?pestaña=baja_trabajador&header_loc=<?php echo $header_loc; ?>">Registro de baja</a>
                    <a href="recursos_humanos.php?pestaña=alta_infonavit&header_loc=<?php echo $header_loc; ?>">Registro infonavit</a>
                    <a href="recursos_humanos.php?pestaña=registro_incapacidades&header_loc=<?php echo $header_loc; ?>">Registro incapacidades</a>
                </div>
            </section>
            <section class="area">
                <a href="recursos_humanos.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>" class="area__boton">Reporte</a>
                <div class="dropdown-content">
                    <a href="recursos_humanos.php?pestaña=reporte_diario&header_loc=<?php echo $header_loc; ?>">Reporte diario</a>
                    <a href="recursos_humanos.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Reporte semanal</a>
                    <a href="recursos_humanos.php?pestaña=reporte_casos&header_loc=<?php echo $header_loc; ?>">Reporte casos especiales</a>
                </div>
            </section>
            <section class="area">
                <a href="recursos_humanos.php?pestaña=reclutaminto&header_loc=<?php echo $header_loc; ?>" class="area__boton">Reclutamiento</a>
                <div class="dropdown-content">
                    <a href="recursos_humanos.php?pestaña=encargado&header_loc=<?php echo $header_loc; ?>">Contactos</a>
                    <a href="recursos_humanos.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Proceso candidatos</a>
                    <a href="recursos_humanos.php?pestaña=reporte_casos&header_loc=<?php echo $header_loc; ?>">Proceso capacitacion</a>
                </div>
            </section>
            
        </nav>
    </header>
    <main class="main">
        <aside>
            <nav class="subnavegador">
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=cronograma&header_loc=<?php echo $header_loc; ?>" class="area__boton">Cronograma</a>
                </section>
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=retardos&header_loc=<?php echo $header_loc; ?>" class="area__boton">Retardos</a>
                </section>
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=horas_tardias&header_loc=<?php echo $header_loc; ?>" class="area__boton">Horas tardias</a>
                </section>
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=historial_de_compras&header_loc=<?php echo $header_loc; ?>" class="area__boton">Compras</a>
                </section>
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>" class="area__boton">Requisición</a>
                </section>
                <section class="area">
                    <a href="recursos_humanos.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>" class="area__boton">Nosotros mismos</a>
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
        $pestaña = 'registro_actividad';
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