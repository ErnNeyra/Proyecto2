<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Producto/Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> </head>
    <body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center">
                <a href="servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="../productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <?php
                    error_reporting(E_ALL);
                    ini_set("display_errors",1);
                    require ('../util/config.php');
                    require ('../util/depurar.php');
                    session_start();
                    if(isset($_SESSION["usuario"]["usuario"])){
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
                    $idServicio = $_GET["id_servicio"];
                    $sql = "SELECT * FROM servicio WHERE id_servicio = $idServicio";
                    $resultado = $_conexion ->query($sql);
                    while($servicio = $resultado -> fetch_assoc()){
                        $nombre = $servicio["nombre"];
                        $precio = $servicio["precio"];
                        $categoria = $servicio["categoria"];
                        $descripcion = $servicio["descripcion"];
                        $imagen = $servicio["imagen"];
                        $usuario = $servicio["usuario"];
                    }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div id="contenedor-imagen-principal" class="mb-4 cursor-zoom-in">
                        <img id="imagen-principal" src="<?php echo $imagen ?>" alt="Imagen del Servicio" class="w-full rounded-md">
                    </div>
                    <div class="flex -mx-2">
                        <div class="w-1/4 px-2">
                            <img src="https://via.placeholder.com/150x100/718096/fff?Text=Miniatura%201" alt="Miniatura 1" class="w-full rounded-md cursor-pointer hover:opacity-75 mini-imagen" data-src="https://via.placeholder.com/600x400/718096/fff?Text=Imagen%201">
                        </div>
                        <div class="w-1/4 px-2">
                            <img src="https://via.placeholder.com/150x100/a0aec0/fff?Text=Miniatura%202" alt="Miniatura 2" class="w-full rounded-md cursor-pointer hover:opacity-75 mini-imagen" data-src="https://via.placeholder.com/600x400/a0aec0/fff?Text=Imagen%202">
                        </div>
                        <div class="w-1/4 px-2">
                            <img src="https://via.placeholder.com/150x100/cbd5e0/fff?Text=Miniatura%203" alt="Miniatura 3" class="w-full rounded-md cursor-pointer hover:opacity-75 mini-imagen" data-src="https://via.placeholder.com/600x400/cbd5e0/fff?Text=Imagen%203">
                        </div>
                        <div class="w-1/4 px-2">
                            <div class="h-24 bg-gray-200 rounded-md flex items-center justify-center text-gray-500 cursor-pointer hover:opacity-75">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div id="lightbox" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-80 z-50 hidden flex items-center justify-center">
                        <button id="cerrar-lightbox" class="absolute top-4 right-4 text-white text-3xl focus:outline-none">&times;</button>
                        <button id="anterior-imagen" class="absolute left-4 text-white text-3xl focus:outline-none">&lsaquo;</button>
                        <img id="lightbox-imagen" src="https://via.placeholder.com/600x400/4a5568/fff?Text=Imagen%20Principal" alt="Imagen Ampliada" class="max-w-full max-h-full rounded-md">
                        <button id="siguiente-imagen" class="absolute right-4 text-white text-3xl focus:outline-none">&rsaquo;</button>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800 mb-4"><?php echo $nombre ?></h1>
                    <div class="flex items-center mb-4">
                        <span class="text-yellow-500 text-xl mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-xl mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-xl mr-1">&#9733;</span>
                        <span class="text-gray-300 text-xl mr-1">&#9733;</span>
                        <span class="text-gray-300 text-xl mr-2">&#9733;</span>
                        <span class="text-gray-600 text-sm">(3 valoraciones)</span>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-6"><?php echo $descripcion ?></p>
                    <p class="text-gray-600 font-semibold mb-2">Precio: <span class="text-black"><?php echo $precio?>€</span></p>
                    <p class="text-gray-600 mb-4"><span class="text-green-500">Disponible</span></p>

                    <!-- BOTON DE EDITAR -->

                     <?php
                        $sql = "SELECT * FROM servicio WHERE id_servicio = ?";
                        $stmt = $_conexion->prepare($sql);
                        $stmt->bind_param("i", $_GET['id_servicio']);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $servicio = $resultado->fetch_assoc();
                        if (isset($_SESSION["usuario"]["usuario"]) && $_SESSION["usuario"]["usuario"] == $servicio["usuario"]) {
                            // Mostrar el botón de editar solo si el usuario es el propietario del servicio
                            echo '<div class="mb-6">';
                            echo '    <a href="editarServicio.php?id_servicio=' . $servicio["id_servicio"] . '" class="bg-yellow-500 text-black py-3 px-6 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Editar</a>';
                            echo '</div>';
                        } else {
                            echo '<div class="mb-6">';
                            echo '    <button class="bg-gray-300 text-gray-500 py-3 px-6 rounded-md cursor-not-allowed" disabled>Editar</button> Este servicio no te pertenece.';
                            echo '</div>';
                            
                        }
                     ?>
                    <div class="mb-6">
                        <button class="bg-yellow-500 text-black py-3 px-6 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Contactar al Emprendedor</button>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ofrecido por:</h2>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-white font-semibold mr-2">
                                EP
                            </div>
                            <p class="text-gray-700 font-semibold"><?php echo $usuario ?></p>
                        </div>
                        <p class="text-gray-600 text-sm mt-1">Breve descripción del emprendedor.</p>
                        <!-- <a href="" class="text-indigo-500 hover:underline text-sm mt-2 inline-block">Ver perfil</a>ESTO QUE ESSSSSSSSSSSSSSSSSSSSSSSs -->
                    </div>
                </div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Comentarios de otros usuarios</h2>
                <div class="mb-6 p-4 bg-gray-100 rounded-md border border-gray-200">
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Usuario 1:</span> ¡Excelente producto, muy recomendable!</p>
                    <div class="flex items-center">
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-gray-300 text-sm">&#9733;</span>
                    </div>
                </div>
                <div class="mb-6 p-4 bg-gray-100 rounded-md border border-gray-200">
                    <p class="text-gray-700 mb-2"><span class="font-semibold">Usuario 2:</span> Buen servicio, aunque tardó un poco en llegar.</p>
                    <div class="flex items-center">
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-yellow-500 text-sm mr-1">&#9733;</span>
                        <span class="text-gray-300 text-sm mr-1">&#9733;</span>
                        <span class="text-gray-300 text-sm">&#9733;</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Dejar un comentario</h3>
                    <form id="formulario-comentario">
                        <div class="mb-4">
                            <label for="valoracion-comentario" class="block text-gray-700 text-sm font-bold mb-2">Tu valoración:</label>
                            <div class="flex items-center">
                                <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="1">&#9733;</button>
                                <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="2">&#9733;</button>
                                <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="3">&#9733;</button>
                                <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="4">&#9733;</button>
                                <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="5">&#9733;</button>
                                <input type="hidden" id="valoracion-comentario" name="valoracion" value="0">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="comentario" class="block text-gray-700 text-sm font-bold mb-2">Tu comentario:</label>
                            <textarea id="comentario" name="comentario" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Escribe tu opinión"></textarea>
                        </div>
                        <button type="submit" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Publicar comentario</button>
                    </form>
                </div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Productos relacionados</h2>
                <div id="carrusel-relacionados-container" class="overflow-x-auto whitespace-nowrap scroll-smooth py-4 -ml-4 pl-4 relative">
                    <div id="carrusel-relacionados" class="whitespace-nowrap transition-transform duration-300">
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/6b7280/fff?Text=Relacionado%201" alt="Producto Relacionado 1" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto Relacionado 1</h3>
                                <p class="text-gray-600 text-sm">Descripción breve.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/4338ca/fff?Text=Relacionado%202" alt="Producto Relacionado 2" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto Relacionado 2</h3>
                                <p class="text-gray-600 text-sm">Descripción breve.</p>
                            </div>
                        </div>
                        <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                            <img src="https://via.placeholder.com/300x200/16a34a/fff?Text=Relacionado%203" alt="Producto Relacionado 3" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-700">Producto Relacionado 3</h3>
                                <p class="text-gray-600 text-sm">Descripción breve.</p>
                            </div>
                        </div>
                    </div>
                    <button id="prev-relacionado" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -ml-2 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button id="next-relacionado" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -mr-2 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>
    <script src="../../js/script2.js"></script> <script src="../../js/valoracion.js"></script> <script src="../../js/lightbox.js"></script> <script src="../../js/detalle-producto.js"></script> <script src="../../js/comentario.js"></script>
  
</body>
</html>