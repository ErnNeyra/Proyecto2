<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos y Servicios | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> </head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="./listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="./contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="./registro.php" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="./login.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-semibold text-gray-800">Explora las ofertas de nuestros emprendedores</h1>
            <a href="./crearProducto.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Crear Producto</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                <img src="https://via.placeholder.com/400x300/abcdef/ffffff?Text=Producto/Servicio%201" alt="Producto/Servicio 1" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Nombre del Producto/Servicio 1</h3>
                    <p class="text-gray-600 text-sm mb-2">Breve descripción de la oferta 1.</p>
                    <p class="text-gray-500 text-xs mb-2">Ofrecido por: Emprendedor A</p>
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
                    <a href="../html/detalle-producto.html" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
                </div>
            </div>
            <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                <img src="https://via.placeholder.com/400x300/fedcba/ffffff?Text=Otro%20Producto%202" alt="Otro Producto 2" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Otro Producto/Servicio Genial 2</h3>
                    <p class="text-gray-600 text-sm mb-2">Una descripción más de esta increíble oferta 2.</p>
                    <p class="text-gray-500 text-xs mb-2">Ofrecido por: Emprendedor B</p>
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
                    <a href="../html/detalle-producto.html" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
                </div>
            </div>
            <div class="bg-white rounded-md shadow-md overflow-hidden border border-gray-200">
                <img src="https://via.placeholder.com/400x300/cbaabc/ffffff?Text=Tercer%20Item%203" alt="Tercer Item 3" class="w-full h-48 object-cover">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-2">Tercer Producto/Servicio 3</h3>
                    <p class="text-gray-600 text-sm mb-2">Descripción breve del tercer elemento 3.</p>
                    <p class="text-gray-500 text-xs mb-2">Ofrecido por: Emprendedor C</p>
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
                    <a href="../html/detalle-producto.html" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 text-sm inline-block mt-4">Ver Detalles</a>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

    <script src="js/valoracion.js"></script>
</body>
</html>
<?php
   
    
?>