<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> </head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="registro.php" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="login.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex justify-center flex-grow">
        <div class="max-w-md bg-white rounded-md shadow-md p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">¡Cuéntanos tus dudas o ideas!</h2>
            <p class="text-gray-600 mb-4 text-center">Estamos aquí para ayudarte a crecer. Si tienes alguna pregunta, sugerencia o simplemente quieres saludarnos, ¡no dudes en escribirnos!</p>
            <form id="contacto-form">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Tu Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="¿Cómo te llamas?">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Tu Correo Electrónico:</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Tu dirección de correo">
                </div>
                <div class="mb-4">
                    <label for="asunto" class="block text-gray-700 text-sm font-bold mb-2">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="¿De qué quieres hablar?">
                </div>
                <div class="mb-6">
                    <label for="mensaje" class="block text-gray-700 text-sm font-bold mb-2">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Escribe tu mensaje aquí"></textarea>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Enviar Mensaje</button>
                    <a href="../index.php" class="inline-block align-baseline font-semibold text-sm text-gray-500 hover:text-gray-800 ml-4">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>