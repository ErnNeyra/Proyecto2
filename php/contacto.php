<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="icon" href="util/img/.faviconWC.png " type="image/x-icon">
    <!-- favicon -->
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <?php
    // Código PHP para mostrar mensajes de estado (éxito/error)
    // Esto leerá los parámetros 'status' y 'msg' de la URL después de enviar el formulario
    if (isset($_GET['status']) && isset($_GET['msg'])) {
        $status = htmlspecialchars($_GET['status']);
        $msg = htmlspecialchars($_GET['msg']);
        $alertClass = ($status === 'success') ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        echo '<div class="' . $alertClass . ' border px-4 py-3 rounded relative mb-4" role="alert">';
        echo '<span class="block sm:inline">' . $msg . '</span>';
        // Botón simple para cerrar el mensaje
        echo '<span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display=\'none\';">';
        echo '<svg class="fill-current h-6 w-6 text-gray-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15L6.22 7.21a1.2 1.2 0 0 1 1.697-1.697L10 8.183l2.651-3.03a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>';
        echo '</span>';
        echo '</div>';
    }
    ?>

    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="logo inline-block">
                <img src="util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center">
                <a href="productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <?php
                session_start(); // Ya se inició arriba
                if (isset($_SESSION['usuario'])) {
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
            <p class="text-gray-600 mb-4 text-center">Estamos aquí para ayudarte a crecer. Si tienes alguna pregunta, sugerencia o simplemente quieres saludarnos, ¡no dudes en escribirnos!
            </p>
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
                    <a href="../index.php" class="inline-block align-baseline font-semibold text-sm text-gray-500 hover:text-gray-800 ml-4">Cancelar</a> </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        <p>&copy; 2025 We-Connect. Todos los derechos reservados.</p>
    </footer>

     <script src="../js/script2.js"></script> </body>
</html>