<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preguntas Frecuentes | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="../productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="../contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <?php
                    // Iniciar sesión solo si no ha sido iniciada
                     if (session_status() == PHP_SESSION_NONE) {
                         session_start();
                     }

                    // Lógica para mostrar menú de usuario o enlaces de login/registro
                    if(isset($_SESSION["usuario"]["usuario"])){
                        $aliasUsuarioActual = htmlspecialchars($_SESSION['usuario']['usuario']);
                         // Asumiendo que la imagen de perfil está en ../util/img/ o la ruta guardada
                         $imagenPerfil = '../util/img/usuario.jpg'; // Ruta por defecto
                         if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                             // La ruta guardada en BD es relativa a 'util/', así que para llegar a ella desde 'php/terminos/'
                             // necesitamos subir dos niveles ('../../'), ir a 'php/' (que ya está implícito si 'util' está en 'php'),
                             // luego a 'util/', y usar la ruta de la BD. Si 'util' está a la misma altura que 'php',
                             // entonces la ruta sería '../../util/' + ruta_bd
                             // Basado en index.php, parece que 'util' está dentro de 'php', así que la ruta desde 'php/terminos' a 'php/util' es '../util'.
                             $rutaImagen = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                             // Opcional: verificar si el archivo existe si la ruta_bd es variable
                             // if (file_exists(__DIR__ . '/../' . $rutaImagen)) { // Comprobar ruta real en el servidor
                             //     $imagenPerfil = $rutaImagen;
                             // } else {
                             //     // Si no existe, usar la por defecto
                             //     $imagenPerfil = '../util/img/usuario.jpg';
                             // }
                             // Simplificando: asumimos que la ruta de BD es correcta o usamos la por defecto
                             if (!empty($_SESSION['usuario']['foto_perfil'])) {
                                  $imagenPerfil = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                             } else {
                                  $imagenPerfil = '../util/img/usuario.jpg'; // Asegurar que siempre haya una ruta válida
                             }
                         }


                        echo '<div class="relative">';
                        echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                         echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . htmlspecialchars($imagenPerfil) . '" alt="Imagen de Perfil">';

                        echo '        <span>' . $aliasUsuarioActual . '</span>';
                        echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        echo '    </button>';
                        echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                        // Ajusta las rutas en el desplegable relativas desde php/terminos/
                        echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                        echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                         // Enlaces para gestionar productos/servicios si es vendedor/admin (asumo que el rol está en sesión)
                         if (isset($_SESSION['usuario']['rol'])) {
                             if ($_SESSION['usuario']['rol'] === 'vendedor' || $_SESSION['usuario']['rol'] === 'admin') {
                                  echo '        <hr class="border-gray-200">';
                                  echo '        <a href="../productos/gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Productos</a>';
                                  echo '        <a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Servicios</a>';
                             }
                         }
                        echo '        <hr class="border-gray-200">';
                        echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                        echo '    </div>';
                        echo '</div>';
                    } else {
                        // Ajusta las rutas de login/registro relativas desde php/terminos/
                        echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                        echo '<a href="../usuarios/registro.php" class="ml-4 bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                    }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Preguntas Frecuentes</h1>

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">¿Cómo me registro en We-Connect?</h2>
                    <p class="text-gray-600">Puedes registrarte haciendo clic en el botón "Regístrate" en la esquina superior derecha de la página principal y completando el formulario.</p>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">¿Puedo vender tanto productos como servicios?</h2>
                    <p class="text-gray-600">Sí, We-Connect te permite ofrecer tanto productos físicos como servicios profesionales en tu perfil.</p>
                </div>

                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">¿Cómo puedo contactar a un emprendedor?</h2>
                    <p class="text-gray-600">En la página de detalle de cada producto o servicio, encontrarás un botón para contactar al emprendedor, usualmente a través de WhatsApp o un formulario de contacto interno.</p>
                </div>

                </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4 text-white">Acerca de We-Connect</h3>
                    <p class="text-gray-300">
                        We-Connect es una plataforma que conecta a compradores y vendedores de productos y servicios,
                        facilitando el comercio y las oportunidades de negocio.
                    </p>
                </div>
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4 text-white">Enlaces Útiles</h3>
                    <ul class="list-none p-0">
                        <li><a href="../../index.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Inicio</a></li>
                        <li><a href="../productos/producto.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Productos</a></li>
                        <li><a href="../servicios/servicio.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Servicios</a></li>
                        <li><a href="../contacto.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Contacto</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4 text-white">Soporte</h3>
                    <ul class="list-none p-0">
                        <li><a href="faq.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas Frecuentes</a></li>
                        <li><a href="terms.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos de Servicio</a></li>
                        <li><a href="privacy.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política de Privacidad</a></li>
                        <li><a href="../contacto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a></li>
                    </ul>
                </div>
                 <div class="footer-section social-icons-footer">
                    <h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3>
                    <div class="flex justify-center md:justify-start space-x-4 text-xl">
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright border-t border-gray-700 pt-8 text-gray-500 text-sm text-center">
                <p>&copy; <?php echo date('Y'); ?> We-Connect. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="../../js/desplegable.js"></script>

</body>
</html>