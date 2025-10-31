<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HOME_PAGE</title>

    <link rel="preload" href="normalize.css">
    <link rel="stylesheet" href="normalize.css">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.typekit.net/oov2wcw.css">
    <link rel="icon" href="img/icon.png" type="image/png">
    
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
<section class="contenedor">
    <nav>

    </nav>
    <nav>
        <img class="logo" src="img/logo.png" alt="">
    </nav>
    <nav>

    </nav>
</section>
<main>
    <div class="areas">
        <section class="area">
            <a href="header_main_aside/proyectos.php?pestaña=avance_ot" class="area__boton">Proyectos</a>
        </section>
        <section class="area">
            <a href="header_main_aside/recursos_humanos.php" class="area__boton">Recursos Humanos</a>
        </section>
        <section class="area">
            <a href="header_main_aside/manufactura.php?pestaña=avance_ot" class="area__boton">Manufactura</a>
        </section>
        <section class="area">
            <a href="header_main_aside/almacen.php" class="area__boton">Almacen</a>
        </section>
        <section class="area">
            <a href="header_main_aside/finanzas.php" class="area__boton">Finanzas</a>
        </section>
        <section class="area">
            <a href="header_main_aside/gerencia.php?pestaña=avance_ot" class="area__boton">Gerencia</a>
        </section>
        
    </div>
    
</main>