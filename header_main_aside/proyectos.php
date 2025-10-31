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
        $allowedRoles = ["gerencia", "Proyectos"]; // Roles permitidos para acceder a esta página
        $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión
    
        // Verifica si el rol del usuario está permitido
        if (!in_array($userRole, $allowedRoles)) {
            // Si el rol no está permitido, redirige a una página de acceso denegado
            header("Location: ../access_denied.php");
            exit();
        }
        include '../php/acciones.php';
        $header_loc='proyectos';
    ?>
</head>
<body>
    <header>
        <nav class="navegador">
            <a href="../home.php">
                <img class="navlogo" src="../img/logo.png" alt="logo">
            </a>
            <section class="area">
                <a href="proyectos.php?pestaña=avance_ot" class="area__boton">Proyectos</a>
                <div class="dropdown-content">
                    <a href="proyectos.php?pestaña=avance_ot&header_loc=<?php echo $header_loc; ?>">Avance de ot</a>
                    <a href="proyectos.php?pestaña=evaluacion_ot&header_loc=<?php echo $header_loc; ?>">Evaluacion de ot</a>
                    <a href="proyectos.php?pestaña=tiempo_piezas&header_loc=<?php echo $header_loc; ?>">Horas por pieza</a>
                    <a href="proyectos.php?pestaña=desglose_horas&header_loc=<?php echo $header_loc; ?>">Desglose de horas</a>
                    <a href="proyectos.php?pestaña=facturasproyectos&header_loc=<?php echo $header_loc; ?>">Facturas por proyectos</a>
                    <a href="proyectos.php?pestaña=facturaspedidos&header_loc=<?php echo $header_loc; ?>">Facturas por pedidos</a>
                    <a href="proyectos.php?pestaña=alta_proyectos&header_loc=<?php echo $header_loc; ?>">Alta de proyectos</a>
                    <a href="proyectos.php?pestaña=alta_pedido&header_loc=<?php echo $header_loc; ?>">Alta de pedido</a>
                    <a href="proyectos.php?pestaña=alta_precios&header_loc=<?php echo $header_loc; ?>">Alta de precios</a>
                </div>
            </section>

            <section class="area">
                <a href="proyectos.php?pestaña=solicitud" class="area__boton">Solicitud</a>
                <div class="dropdown-content">
                    <a href="proyectos.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
                    <a href="proyectos.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
                    <a href="proyectos.php?pestaña=solicitudpieza&header_loc=<?php echo $header_loc; ?>">Solicitud de piezas</a>
                    <a href="proyectos.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
                </div>
            </section>

            <section class="area">
                <a href="proyectos.php?pestaña=areas" class="area__boton">Áreas</a>
                <div class="dropdown-content">
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
            </section>


            <section class="area">
                <a href="proyectos.php?pestaña=reporte" class="area__boton">Reporte</a>
                <div class="dropdown-content">
                    <a href="proyectos.php?pestaña=reporte_diario&header_loc=<?php echo $header_loc; ?>">Reporte diario</a>
                    <a href="proyectos.php?pestaña=reporte_actividades&header_loc=<?php echo $header_loc; ?>">Reporte actividades</a>
                    <a href="proyectos.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>">Reporte semanal</a>
                    <a href="proyectos.php?pestaña=reporte_casos&header_loc=<?php echo $header_loc; ?>">Reporte casos especiales</a>
                    <a href="proyectos.php?pestaña=acceso_planta&header_loc=<?php echo $header_loc; ?>">Acceso a plantas</a>
                </div>
            </section>

            <section class="area">
                <a href="proyectos.php?pestaña=Historicos" class="area__boton">Historiales</a>
                <div class="dropdown-content">
                    <a href="proyectos.php?pestaña=historial_de_compras&header_loc=<?php echo $header_loc; ?>">Compras</a>
                    <a href="proyectos.php?pestaña=pedidos&header_loc=<?php echo $header_loc; ?>">Pedidos</a>
                    <a href="proyectos.php?pestaña=facturas&header_loc=<?php echo $header_loc; ?>">Facturas</a>
                    <a href="proyectos.php?pestaña=historial_premios&header_loc=<?php echo $header_loc; ?>">Premios</a>
                </div>
            </section>
        </nav>
    </header>       
        
    <main class="main">
        <aside>
        <nav class="subnavegador">
            <section class="area">
                <a href="proyectos.php?pestaña=premios&header_loc=<?php echo $header_loc; ?>" class="area__boton">Registro premios</a>
            </section>
            <section class="area">
                <a href="proyectos.php?pestaña=horas_tardias&header_loc=<?php echo $header_loc; ?>" class="area__boton">Horas tardías</a>
            </section>
            <section class="area">
                <a href="proyectos.php?pestaña=herramienta&header_loc=<?php echo $header_loc; ?>" class="area__boton">Herramienta</a>
            </section>
            <section class="area">
                <a href="proyectos.php?pestaña=registro_listas&header_loc=<?php echo $header_loc; ?>" class="area__boton">Registro listas</a>
            </section>
            <section class="area">
                <a href="proyectos.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>" class="area__boton">Nosotros mismos</a>
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