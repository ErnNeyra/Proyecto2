<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos y Servicios | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center">
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <?php
                session_start();
                if(isset($_SESSION["usuario"])){
                    $aliasUsuario = htmlspecialchars($_SESSION['usuario']['usuario']);
                    echo '<div class="relative">';
                    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                    echo '        <span>' . $aliasUsuario . '</span>';
                    echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                    echo '    </button>';
                    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                    echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                    echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                    echo '        <hr class="border-gray-200">';
                    echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                    echo '    </div>';
                    echo '</div>';
                } else {
                    echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                }
                ?>
            </nav>
        </div>
    </header>
    <?php
        if(!isset($_SESSION["usuario"]["usuario"])){
            header("location: ../usuarios/login.php");
            exit;
        }
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );
        require('../util/config.php');
    ?>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <!-- Barra de búsqueda -->
        <div class="bg-white shadow-md rounded-lg p-4 mb-8 max-w-3xl mx-auto">
            <form action="" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                <div class="relative w-full">
                    <input 
                        type="text" 
                        name="buscar" 
                        placeholder="Buscar productos..." 
                        class="w-full px-4 py-2 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:border-yellow-500"
                    >
                    <div class="absolute left-3 top-2.5">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <button 
                    type="submit" 
                    class="w-full sm:w-auto bg-yellow-500 text-black px-6 py-2 rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50"
                >
                    Buscar
                </button>
            </form>
        </div>

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-semibold text-gray-800">Explora los productos que ofrecen nuestros emprendedores</h1>
            <a href="crearProducto.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Crear Producto</a>
        </div>
        <?php
            //$idProducto = $_GET['id_producto'];
            //$sql = "SELECT * FROM productos WHERE id_producto = $idProducto";
            // $stmt = $_conexion->prepare($sql);
            $sql = "SELECT * FROM producto LIMIT 20";
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
                        <a href="detalleProducto.php?id_producto=<?php echo $producto['id_producto']; ?>" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
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
    <script src="../../js/script2.js"></script>
</body>
</html>
<?php


?>