<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos y Servicios | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <?php
                    session_start();
                    if (isset($_SESSION['usuario']['usuario'])):
                ?>
                    <div class="relative">
                        <button id="dropdownBtn" class="text-gray-700 hover:text-black focus:outline-none flex items-center">
                            <span class="mr-2"><?php echo htmlspecialchars($_SESSION['usuario']['usuario']); ?></span>
                            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </button>
                        <div id="dropdownMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg origin-top-right hidden">
                            <div class="py-1">
                                <a href="panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Mi Panel</a>
                                <a href="editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Editar Perfil</a>
                                <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Cerrar Sesión</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <?php
        if(isset($_SESSION["mensaje"])){
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>";
            echo "<strong class='font-bold'>Éxito!</strong>";
            echo "<span class='block sm:inline'>".$_SESSION["mensaje"]."</span>";
            echo "<span class='absolute top-0 bottom-0 right-0 px-4 py-3'>";
            echo "<svg class='fill-current h-6 w-6 text-green-500' role='button' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'><title>Close</title><path fill-rule='evenodd' d='M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.586l-2.651 3.263a1.2 1.2 0 0 1-1.697-1.697L8.303 10l-3.263-2.651a1.2 1.2 0 0 1 1.697-1.697L10 8.414l2.651-3.263a1.2 1.2 0 0 1 1.697 1.697L11.697 10l3.263 2.651a1.2 1.2 0 0 1 0 1.697z' clip-rule='evenodd'/></svg>";
            echo "</span>";
            echo "</div>";
            unset($_SESSION["mensaje"]);
        }
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );
        require('util/config.php');
    ?>
    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-semibold text-gray-800">Explora las ofertas de nuestros emprendedores</h1>
            <?php if (isset($_SESSION['usuario']['id_usuario'])): ?>
                <a href="crearProducto.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Crear Producto</a>
            <?php else: ?>
                <span class="text-gray-500 italic">Inicia sesión para crear tus productos.</span>
            <?php endif; ?>
        </div>
        <?php
            $sql = "SELECT * FROM producto LIMIT 3";
            $resultado = $_conexion -> query($sql);
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if($resultado -> num_rows == 0){
                echo "<h2 class='text-red-500'>No hay productos disponibles ahora mismo...</h2>";
            }
            while($producto = $resultado -> fetch_assoc()){ ?>
                <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                    <img src="<?php echo $producto["imagen"]?>" alt="<?php echo $producto["nombre"] ?>" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-700 mb-2"><?php echo $producto["nombre"] ?></h3>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $producto["descripcion"] ?></p>
                        <p class="text-gray-500 text-xs mb-2">Ofrecido por: <?php echo $producto["usuario"] ?></p>
                        <div class="mb-2">
                            <label class="block text-gray-700 text-sm font-bold mb-1">Valorar:</label>
                            <div class="flex items-center">
                                <button class="star-button text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="1">&#9733;</button>
                                <button class="star-button text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="2">&#9733;</button>
                                <button class="star-button text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="3">&#9733;</button>
                                <button class="star-button text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="4">&#9733;</button>
                                <button class="star-button text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="5">&#9733;</button>
                                <span class="text-gray-600 text-sm ml-2">(0 valoraciones)</span>
                            </div>
                        </div>
                        <a href="detalleProducto.php" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
                    </div>
                </div>
            <?php
            } ?>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

    <script src="../js/valoracion.js"></script>
    <script>
        const dropdownBtn = document.getElementById('dropdownBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            // Cerrar el desplegable si se hace clic fuera
            document.addEventListener('click', (event) => {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>
<?php


?>