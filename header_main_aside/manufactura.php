<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manufactura</title>

    <link rel="stylesheet" href="../Normalize.css">
    <link rel="stylesheet" href="../styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="../img/icon.png" type="image/png">
    <script src="../java/funciones.js"></script>

    <?php
        date_default_timezone_set('America/Monterrey');
        session_start(); // Inicia la sesión
            
        // Verifica el rol del usuario
        $allowedRoles = ["gerencia", "Proyectos","manufactura"]; // Roles permitidos para acceder a esta página
        $userRole = $_SESSION['role']; // Suponiendo que el rol del usuario está almacenado en la sesión
        
        // Verifica si el rol del usuario está permitido
        if (!in_array($userRole, $allowedRoles)) {
            // Si el rol no está permitido, redirige a una página de acceso denegado
            header("Location: ../access_denied.php");
            exit();
        }
        
        // El usuario tiene permiso para acceder, continúa mostrando la página
        include '../php/acciones.php';
            $header_loc='manufactura';
    ?>
    <style>
        /* Estilos para el menú desplegable */
        .area {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #f1f1f1;}

        .area:hover .dropdown-content {
            display: block;
        }

    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var params = new URLSearchParams(window.location.search);
            var pestaña = params.get('pestaña');

            if (pestaña !== 'areas') {
            document.querySelectorAll('.area__boton').forEach(function (boton) {
                var botonPestaña = new URL(boton.href).searchParams.get('pestaña');
                if (botonPestaña === pestaña) {
                boton.classList.add('active');
                }
            });
            }
        });
    </script>
        
</head>

<header>
    <nav class="navegador">
        <a href="../home.php">
            <img class="navlogo" src="../img/logo.png" alt="logo">
        </a>
        <?php
            // Consulta para obtener las áreas
            $sql_areas = "SELECT area FROM listas WHERE area IS NOT NULL ORDER BY area";
            $resultado = $conexion->query($sql_areas);
        
            // Verificar si hay resultados
            if ($resultado->num_rows > 0) {
                // Mostrar las áreas
                while ($fila = $resultado->fetch_assoc()) {
                    $area = htmlspecialchars($fila['area']); // Escapar para seguridad
        ?>
                    <section class="area">
                        <a class="area__boton" href="manufactura.php?pestaña=areas&header_loc=manufactura&area=<?= $area ?>">
                            <?= $area ?>
                        </a>
                    </section>
                    
        <?php
                }
            } else {
                echo '<a href="#">Sin áreas</a>';
            }
        ?>
    </nav>
</header>
    
    <main class="main">
        <aside>
            <nav class="subnavegador">
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=avance_ot&header_loc=<?php echo $header_loc; ?>">Avance de ot</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=historial_de_compras&header_loc=<?php echo $header_loc; ?>">Historial Compras</a>
                </section>
                <section class="area">
                <a href="manufactura.php?pestaña=premios&header_loc=<?php echo $header_loc; ?>" class="area__boton">Registro premios</a>
            </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=historial_premios&header_loc=<?php echo $header_loc; ?>">Premios</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=solicitudpieza&header_loc=<?php echo $header_loc; ?>">Solicitud de piezas</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=solicitudcompra&header_loc=<?php echo $header_loc; ?>">Solicitud de compra</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=solicitud_factura&header_loc=<?php echo $header_loc; ?>">Solicitud de factura</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=requisicion&header_loc=<?php echo $header_loc; ?>">Requisición</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=reporte_diario&header_loc=<?php echo $header_loc; ?>">Reporte diario</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=reporte_semanal&header_loc=<?php echo $header_loc; ?>" class="area__boton">Reporte semanal</a>
                </section>
                <section class="area">
                    <a class="area__boton" href="manufactura.php?pestaña=horas_tardias&header_loc=<?php echo $header_loc; ?>" class="area__boton">Horas tardías</a>
                </section>
                <section class="area">
                    <a href="manufactura.php?pestaña=ot_700&header_loc=<?php echo $header_loc; ?>" class="area__boton">Nosotros mismos</a>
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
    </main>
</body>
</html>