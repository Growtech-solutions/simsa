<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HOME_PAGE</title>

    <link rel="preload" href="normalize.css">
    <link rel="stylesheet" href="normalize.css">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="img/icon.png" type="image/png">

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contenedor {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #003366;
            color: white;
        }

        .logo {
            width: 120px;
        }

        /* Menú Hamburguesa */
        .menu-btn {
            font-size: 24px;
            cursor: pointer;
            background: none;
            border: none;
            color: white;
        }

        .menu {
            display: none;
            flex-direction: column;
            background-color: white;
            position: absolute;
            top: 110px;
            width: 100%;
            left: 0;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .menu.show {
            display: flex;
        }

        .area {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .area a {
            text-decoration: none;
            font-size: 18px;
            color: #003366;
            display: block;
        }

        .area a:hover {
            color: #0066cc;
        }

    </style>

    <?php
        session_start(); // Inicia la sesión
        if (!isset($_SESSION['username'])) {
            header("Location: index.php");
            exit();
        }
        $allowedRoles = ["gerencia"]; 
        $userRole = $_SESSION['role']; 
        if (!in_array($userRole, $allowedRoles)) {
            header("Location: /access_denied.php");
            exit();
        }
    ?>

</head>
<body>

<section class="contenedor">
    <button class="menu-btn" onclick="toggleMenu()">☰</button>
    <img class="logo" src="../img/logo.png" alt="Logo">
</section>

<nav class="menu" id="menu">
    <div class="area"><a href="../header_main_aside_celular/proyectos.php?pestaña=avance_ot">Proyectos</a></div>
    <div class="area"><a href="header_main_aside/recursos_humanos.php">Recursos Humanos</a></div>
    <div class="area"><a href="../header_main_aside_celular/manufactura.php?pestaña=horas_tardias_celular">Manufactura</a></div>
    <div class="area"><a href="header_main_aside/almacen.php">Almacén</a></div>
    <div class="area"><a href="header_main_aside/finanzas.php">Finanzas</a></div>
    <div class="area"><a href="header_main_aside/gerencia.php?pestaña=avance_ot">Gerencia</a></div>
</nav>

<script>
    function toggleMenu() {
        document.getElementById("menu").classList.toggle("show");
    }
</script>

</body>
</html>
