<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css"> <link rel="stylesheet" href="../css/index.css"> </head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a> <nav class="flex items-center">
                <a href="productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <?php
                session_start(); // Inicia la sesión
                if (isset($_SESSION['usuario']['usuario'])) {
                    $aliasUsuario = htmlspecialchars($_SESSION['usuario']['usuario']);
                    echo '<div class="relative">';
                    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                    echo '        <span>' . $aliasUsuario . '</span>';
                    echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                    echo '    </button>';
                    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                    echo '        <a href="usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                    echo '        <a href="usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                    echo '        <hr class="border-gray-200">';
                    echo '        <a href="usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                    echo '    </div>';
                    echo '</div>';
                } else {
                    echo '<a href="usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex justify-center flex-grow">
        <div class="max-w-md bg-white rounded-md shadow-md p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">¡Cuéntanos tus dudas o ideas!</h2>
            <p class="text-gray-600 mb-4 text-center">Estamos aquí para ayudarte a crecer. Si tienes alguna pregunta, sugerencia o simplemente quieres saludarnos, ¡no dudes en escribirnos!</p>
            <form id="contacto-form" action="util/send_contact_email.php" method="post">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Tu Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="¿Cómo te llamas?" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Tu Correo Electrónico:</label>
                    <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Tu dirección de correo" required>
                </div>
                <div class="mb-4">
                    <label for="asunto" class="block text-gray-700 text-sm font-bold mb-2">Asunto:</label>
                    <input type="text" id="asunto" name="asunto" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="¿De qué quieres hablar?" required>
                </div>
                <div class="mb-6">
                    <label for="mensaje" class="block text-gray-700 text-sm font-bold mb-2">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Escribe tu mensaje aquí" required></textarea>
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

     <script src="../js/desplegable.js"></script> </body>
</html>