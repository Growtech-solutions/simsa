<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body id="altadeproveedor" class="bg-gray-50">
    <main class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Alta de proveedor</h2>
            <form class="space-y-4" action="../php/procesar_alta_proveedor.php" method="POST">
                <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" 
                       type="text" id="proveedor" name="proveedor" placeholder="Nombre" required>
                
                <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" 
                       type="text" id="direccion" name="direccion" placeholder="Dirección">
                
                <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" 
                       type="text" id="telefono" name="telefono" placeholder="Teléfono">
                
                <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" 
                       type="text" id="correo" name="correo" placeholder="Correo">
                
                <input class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition" 
                       type="number" id="periodo_pago" name="periodo_pago" placeholder="Días crédito">
                
                <input type="hidden" name="header_loc" value="<?php echo $header_loc; ?>">
                
                <div class="pt-4">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-md hover:shadow-lg" 
                            type="submit">
                        Enviar
                    </button>
                </div>
            </form>
            <?php
            if (isset($_GET['confirmacion'])) {
                $confirmacion = htmlspecialchars($_GET['confirmacion']);
                echo "<div class='mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg'>$confirmacion</div>";
            }
            ?>
        </div>
    </main>
</body>
</html>