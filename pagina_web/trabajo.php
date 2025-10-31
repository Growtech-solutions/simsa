<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bolsa de trabajo - SIMSA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        header { background: #003366; color: #fff; padding: 30px 0; text-align: center; }
        .logo { max-width: 180px; margin-bottom: 10px; }
        nav { background: #005599; padding: 10px 0; text-align: center; }
        nav a { color: #fff; margin: 0 20px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        nav a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);}
        h1, h2 { margin-top: 0; }
        .img-trabajo { width: 100%; max-width: 350px; border-radius: 8px; margin: 15px 0; }
        .btn { background: #005599; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; }
        .btn:hover { background: #003366; }
        .puestos { margin: 20px 0; }
        .puesto { margin-bottom: 15px; }
        footer { background: #003366; color: #fff; text-align: center; padding: 15px 0; margin-top: 40px; }
    </style>
</head>
<body>
    <header>
        <img src="../img/logo.png" alt="Logo SIMSA" class="logo">
        <h1>Trabaja con nosotros</h1>
    </header>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="index.html#servicios">Servicios</a>
        <a href="index.html#nosotros">Nosotros</a>
        <a href="#contacto">Contacto</a>
    </nav>
    <main class="container">
        <h2>¿Buscas trabajo?</h2>
        <img src="../img/trabajo.jpg" alt="Bolsa de trabajo SIMSA" class="img-trabajo">
        <p>Únete a nuestro equipo y forma parte de una empresa con más de 50 años de experiencia.</p>
        <div class="puestos">
            <div class="puesto">
                <strong>Pailero</strong><br>
                Experiencia en fabricación y reparación de estructuras metálicas.
            </div>
            <div class="puesto">
                <strong>Soldador</strong><br>
                Conocimiento en soldadura especializada (MIG, TIG, eléctrica).
            </div>
            <div class="puesto">
                <strong>Ayudante general</strong><br>
                Apoyo en tareas de taller y manejo de herramientas.
            </div>
        </div>
        <form method="post" action="#">
            <label for="nombre_trabajo">Nombre:</label><br>
            <input type="text" id="nombre_trabajo" name="nombre_trabajo" required><br><br>
            <label for="email_trabajo">Correo electrónico:</label><br>
            <input type="email" id="email_trabajo" name="email_trabajo" required><br><br>
            <label for="puesto">Puesto de interés:</label><br>
            <select id="puesto" name="puesto" required>
                <option value="">Selecciona un puesto</option>
                <option value="pailero">Pailero</option>
                <option value="soldador">Soldador</option>
                <option value="ayudante">Ayudante general</option>
            </select><br><br>
            <label for="mensaje_trabajo">Mensaje:</label><br>
            <textarea id="mensaje_trabajo" name="mensaje_trabajo" rows="4" required></textarea><br><br>
            <button type="submit" class="btn">Enviar solicitud</button>
        </form>
    </main>
    <footer>
        &copy; <?php echo date('Y'); ?> SIMSA. Todos los derec
