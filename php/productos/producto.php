<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos y Servicios | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    <!-- favicon -->
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors",1);
        require ('../util/config.php');
        require ('../util/depurar.php');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["usuario"]["usuario"])) {
            //CUIDADO AMIGO esta función es peligrosa, tiene que ejecutarse antes de que
            //se ejecute el código body
            header("location: ../usuarios/login.php");
            exit;
        }
    ?>
</head>

<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4">
                <a href="../recursos/recursos.php" class="text-gray-700 hover:text-black mr-4 font-semibold">Recursos</a>
                <a></a>
                <a href="../productos/producto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Productos</a>
                <a></a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Servicios</a>

                <a href="../contacto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Contacto</a>
                <?php
                if (isset($_SESSION["usuario"])) {
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
                        echo '        <a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                        echo '        <a href="../categoria/index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categoría</a>';
                        echo '        <hr class="border-red-200">';
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

    <!-- Buscador -->
    <div class="w-full flex justify-center mt-8 mb-8">
        <form class="w-full max-w-md flex">
            <input 
                type="text" 
                name="busqueda" 
                placeholder="Buscar productos o emprendedores..."
                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition text-gray-700 placeholder-gray-400"
            >
            <button 
                type="submit"
                class="ml-2 px-4 py-2 rounded-md bg-yellow-500 text-black font-semibold hover:bg-yellow-600 focus:outline-none focus:shadow-outline transition"
            >
                Buscar
            </button>
        </form>
    </div>
    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-semibold text-gray-800">Explora los productos que ofrecen nuestros emprendedores
            </h1>
            <a href="crearProducto.php"
                class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Crear
                Producto</a>
        </div>



        <?php
        //$idProducto = $_GET['id_producto'];
        //$sql = "SELECT * FROM productos WHERE id_producto = $idProducto";
        // $stmt = $_conexion->prepare($sql);
        $sql = "SELECT * FROM producto LIMIT 20";
        $resultado = $_conexion->query($sql);
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            if ($resultado->num_rows == 0) {
                echo "<h2 class='text-red-500'>No hay productos disponibles ahora mismo...</h2>";
            }
            while ($producto = $resultado->fetch_assoc()) { ?>
                <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                    <img src="<?php echo $producto["imagen"] ?>" alt="<?php echo $producto["nombre"] ?>"
                        class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-700 mb-2"><?php echo $producto["nombre"] ?></h3>
                        <p class="text-gray-600 text-sm mb-2"><?php echo $producto["descripcion"] ?></p>
                        <p class="text-gray-500 text-xs mb-2">Ofrecido por: <?php echo $producto["usuario"] ?></p>

                        <a href="detalleProducto.php?id_producto=<?php echo $producto['id_producto']; ?>"
                            class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver
                            Detalles</a>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

    <script src="../../js/script2.js"></script>
</body>

</html>
<?php


?>