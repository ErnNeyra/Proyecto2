<?php
    // **CONFIGURACIÓN DE LA BASE DE DATOS** (Asegúrate de que coincida con tu configuración)
    $servername = "localhost";
    $username = "usuario_db";
    $password = "contraseña_db";
    $dbname = "nombre_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Consulta para obtener todos los productos (puedes añadir ORDER BY, LIMIT, etc.)
    $sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen_url, u.nombre AS nombre_usuario
            FROM productos p
            INNER JOIN usuarios u ON p.id_usuario = u.id";
    $result = $conn->query($sql);

    $productos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    $conn->close();
?>

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
            <a href="index2.html" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="/listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="/contacto.html" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="/registro.html" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="/login.html" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/panel_usuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-semibold text-gray-800">Explora las ofertas de nuestros emprendedores</h1>
            <a href="/crear-producto.html" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Crear Producto</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($productos)): ?>
                <p class="text-gray-600 text-center">No hay productos disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                        <?php if ($producto['imagen_url']): ?>
                            <img src="<?php echo htmlspecialchars($producto['imagen_url']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x300/cccccc/eeeeee?Text=Sin%20Imagen" alt="Sin Imagen" class="w-full h-48 object-cover">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="font-semibold text-gray-700 mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="text-gray-500 text-xs mb-2">Ofrecido por: <?php echo htmlspecialchars($producto['nombre_usuario']); ?></p>
                            <div class="mb-2">
                                <label class="block text-gray-700 text-sm font-bold mb-1">Valorar:</label>
                                <div class="flex items-center">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button class="star-button text-gray-300 <?php if ($i <= 0) echo 'hover:text-yellow-500'; ?> focus:outline-none text-xl mr-1" data-value="<?php echo $i; ?>">&#9733;</button>
                                    <?php endfor; ?>
                                    <span class="text-gray-600 text-sm ml-2">(0 valoraciones)</span>
                                </div>
                            </div>
                            <a href="/detalle-producto.html" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        &copy; 2025 We-Connect. Todos los derechos reservados.
    </footer>

    <script src="js/valoracion.js"></script>
</body>
</html>