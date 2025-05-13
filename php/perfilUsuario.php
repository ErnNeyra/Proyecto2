<?php
   /* session_start();
    require('./util/config.php');

    // Verificar si se ha proporcionado un ID de usuario
    $id_usuario = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id_usuario'];
    
    // Obtener información del usuario
    $sql = "SELECT * FROM usuario WHERE id_usuario = ?";
    $stmt = $_conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();
    

    // Obtener productos del usuario
    $sql_productos = "SELECT * FROM producto WHERE usuario = ? ORDER BY fecha_creacion DESC";
    $stmt_productos = $_conexion->prepare($sql_productos);
    $stmt_productos->bind_param("s", $usuario['usuario']);
    $stmt_productos->execute();
    $productos = $stmt_productos->get_result();

    */
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
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <!--  -->
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="md:flex items-start">
                <div class="md:w-1/4 text-center mb-6 md:mb-0">
                    <img src="<?php echo $usuario['imagen_perfil'] ?? 'assets/default-profile.png'; ?>" 
                         alt="Foto de perfil" 
                         class="profile-image mx-auto mb-4">
                </div>
                <div class="md:w-3/4 md:pl-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($usuario['nombre']); ?></h1>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($usuario['descripcion'] ?? 'Sin descripción'); ?></p>
                    <p class="text-gray-600 mb-4">
                        <svg class="inline w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <?php echo htmlspecialchars($usuario['ciudad'] ?? 'Ubicación no especificada'); ?>
                    </p>
                </div>
            </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Productos publicados</h2>
        <div class="grid gap-6">
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
                            <?php if(isset($_SESSION['usuario']) && $_SESSION['usuario'] == $usuario['usuario']): ?>
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
        </div>
    </main>

    <script>
        function eliminarProducto(id) {
            if(confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                window.location.href = `eliminarProducto.php?id=${id}`;
            }
        }
    </script>
</body>
</html>