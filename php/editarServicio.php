<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
<header class="bg-white shadow-md">
    <div class="container mx-auto py-4 px-6 flex items-center justify-between">
        <a href="index2.html" class="text-xl font-bold text-black">We-Connect</a>
        <nav class="flex items-center">
            <a href="/servicios.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
            <a href="/contacto.html" class="text-gray-700 hover:text-black mr-4">Contacto</a>
            <a href="/panel_usuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
        </nav>
    </div>
</header>

<main class="container mx-auto py-12 px-6 flex-grow">
    <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Editar Servicio</h1>

        <!-- Mensajes de éxito o error -->
        <p class="text-red-500 mb-4" id="error-message" style="display: none;"></p>
        <p class="text-green-500 mb-4" id="success-message" style="display: none;"></p>

        <form action="/editar_servicio.php?id=123" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="Nombre actual" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>Descripción actual</textarea>
            </div>
            <div class="mb-4">
                <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" value="100.00" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-4">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB.</p>
                <p class="text-sm mt-2">Imagen actual: <a href="/ruta/a/imagen.jpg" target="_blank" class="text-blue-500 underline">Ver imagen</a></p>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Actualizar Servicio</button>
                <a href="/servicios.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<footer class="bg-black py-4 text-center text-gray-400">
    &copy; 2025 We-Connect. Todos los derechos reservados.
</footer>
</body>
</html>
