<?php
    session_start();
    require('util/config.php');

    // Verificar si el usuario está logueado Y si la información del usuario está completa en la sesión
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id_usuario'])) {
        // Si falta la información esencial del usuario en la sesión, redirigir a la página de inicio de sesión
        header("location: login.php");
        exit;
    }

    // Obtener el ID del usuario de la sesión de forma segura
    $id_usuario = $_SESSION['usuario']['id_usuario'];
    $aliasUsuario = htmlspecialchars($_SESSION['usuario']['usuario']);

    // Obtener información del usuario
    $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt = $_conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    // Verificar si se encontró el usuario en la base de datos
    if (!$usuario) {
        echo "Error: No se encontró información del usuario en la base de datos.";
        exit;
    }

    // Obtener productos del usuario
    $sql_productos = "SELECT * FROM producto WHERE usuario = ? ORDER BY fecha_agregado DESC";
    $stmt_productos = $_conexion->prepare($sql_productos);
    $stmt_productos->bind_param("s", $usuario['usuario']); // Usamos el nombre de usuario del perfil
    $stmt_productos->execute();
    $productos = $stmt_productos->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?> | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .profile-image {
                width: 100px;
                height: 100px;
            }
        }

        .product-card {
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans min-h-screen">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="../productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
              <?php
                if(isset($_SESSION["usuario"]["usuario"])){
                    echo '<div class="relative">';
                    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                    echo '        <span>' . $aliasUsuario . '</span>';
                    echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                    echo '    </button>';
                    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                    echo '        <a href="editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                    echo '        <hr class="border-gray-200">';
                    echo '        <a href="logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                    echo '    </div>';
                    echo '</div>';
                }
            ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="md:flex items-start">
                <div class="md:w-1/4 text-center mb-6 md:mb-0">
                    <img src="<?php echo htmlspecialchars($usuario['foto_perfil'] ?? 'util/img/usuario.jpg'); ?>"
                         alt="Foto de perfil"
                         class="profile-image mx-auto mb-4">
                </div>
                <div class="md:w-3/4 md:pl-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($usuario['nombre']); ?></h1>
                    <p class="text-gray-600 mb-2">
                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <?php echo htmlspecialchars($usuario['usuario']); ?>
                    </p>
                    <p class="text-gray-600 mb-2">
                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <?php echo htmlspecialchars($usuario['email']); ?>
                    </p>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($usuario['area_trabajo'] ?? 'Sin área de trabajo'); ?></p>
                    <p class="text-gray-600 mb-4">
                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <?php echo htmlspecialchars($usuario['telefono'] ?? 'Teléfono no especificado'); ?>
                    </p>
                    <p class="text-gray-600 mb-4">
                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Se unió el: <?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?>
                    </p>
                    </div>
            </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tus Productos Publicados</h2>
        <div class="grid gap-6">
            <?php if ($productos->num_rows > 0): ?>
                <?php while($producto = $productos->fetch_assoc()): ?>
                    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="md:flex">
                            <div class="md:w-1/4">
                                <img src="<?php echo htmlspecialchars($producto['imagen']); ?>"
                                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                                     class="w-full h-48 object-cover">
                            </div>
                            <div class="md:w-3/4 p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </p>
                                <p class="text-lg font-bold text-yellow-500 mb-4">
                                    €<?php echo number_format($producto['precio'], 2); ?>
                                </p>
                                <?php if(isset($_SESSION['usuario']['usuario']) && $_SESSION['usuario']['usuario'] == $usuario['usuario']): ?>
                                    <div class="flex space-x-4">
                                        <a href="editarProducto.php?id=<?php echo $producto['id_producto']; ?>"
                                           class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                                            Editar producto
                                        </a>
                                        <button onclick="eliminarProducto(<?php echo $producto['id_producto']; ?>)"
                                                class="bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600 transition duration-200">
                                            Eliminar producto
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-600">Aún no has publicado ningún producto.</p>
            <?php endif; ?>
        </div>
    </main>

     <script src="../js/script2.js"></script>
</body>
</html>