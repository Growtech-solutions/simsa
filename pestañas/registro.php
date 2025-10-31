
<head>
    <title>Registro de Usuario</title>
</head>
<style>
.logo {
    display: block;
    margin: 0 auto 20px;
    width: 100px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    font-size: 14px;
    margin-bottom: 5px;
}

.entrada {
    padding: 10px;
    margin-bottom: 15px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
}

.entrada:focus {
    outline: none;
    border-color: #4caf50;
}

.boton {
    display: flex;
    justify-content: center;
}

.boton-enviar {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #4caf50;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.boton-enviar:hover {
    background-color: #45a049;
}

.error-message {
    color: red;
    font-size: 14px;
    text-align: center;
}
</style>
<body>
    <div class="principal">
        <section>
            <img class="logo" src="../img/logo.png" alt="Logo">
            <?php if (isset($_GET["confirmacion"])) { ?>
                <p class="confirmacion"><?= htmlspecialchars($_GET["confirmacion"]) ?></p>
            <?php } ?>
            <form class="formulario" action="../php/procesar_registro.php" method="post">
                <h2>Crear Cuenta</h2>

                <!-- Campo para el nombre de usuario -->
                <label for="username">Usuario:</label>
                <input class="entrada" type="text" id="username" name="username" placeholder="Ingresa tu usuario" required><br><br>

                <!-- Campo para la contraseña -->
                <label for="password">Contraseña:</label>
                <input class="entrada" type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required><br><br>

                <!-- Confirmar contraseña -->
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input class="entrada" type="password" id="confirm_password" name="confirm_password" placeholder="Confirma tu contraseña" required><br><br>

                <!-- Campo para elegir el rol (puedes cambiar o agregar otros roles) -->
                <label for="role">Rol:</label>
                <?php $selectDatos->obtenerOpciones('listas', 'rol', 'role', 'entrada'); ?>
                <br><br>
                <input type="hidden" name="header_loc" value=<?php $header_loc ?>>
                <!-- Botón de registro -->
                <div class="boton">
                    <input class="boton-enviar" type="submit" value="Crear Cuenta">
                </div>

                <!-- Mensaje de error si no se completa correctamente el registro -->
                <p class="error-message"><?php if(isset($errorMessage)) { echo $errorMessage; } ?></p>
            </form>
</section>
</div>
</body>
</html>
