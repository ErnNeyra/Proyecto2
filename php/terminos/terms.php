<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
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
                <a href="../recursos/recursos.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Recursos</a>
                <?php

                    if (isset($_SESSION['usuario'])) {
                        // Obtener datos del usuario de la sesión, usando htmlspecialchars por seguridad
                        $nombreUsuario = htmlspecialchars($_SESSION['usuario']['usuario']); // Usamos 'usuario'

                        // Determinar la ruta de la imagen de perfil
                        // Ruta por defecto desde php/categoria-index.php a php/util/
                        $imagenPerfil = '../util/img/usuario.png'; // Ruta por defecto desde php/

                        // Verificamos si existe la foto de perfil del usuario en la sesión y no está vacía
                        if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                            // La ruta guardada en BD es relativa a 'util/', así que desde php/categoria-index.php es 'util/' + ruta_bd
                            $rutaImagenBD = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                        
                            if (file_exists($rutaImagenBD)) { // Esta comprobación asume que PHP está en la raíz del sitio o se ajusta include_path
                                $imagenPerfil = htmlspecialchars($rutaImagenBD); // Usar la ruta validada
                            }
                            // Si no existe en el sistema de archivos, la variable $imagenPerfil mantiene la ruta por defecto
                        }
                    

                        // Obtener el rol del usuario si está seteado (necesario para enlaces condicionales)
                        $userRole = $_SESSION['usuario']['rol'] ?? '';


                        // Estructura del desplegable - Rutas relativas desde php/categoria-index.php
                        echo '<div class="relative">'; // Clase relativa para el posicionamiento absoluto del desplegable
                        echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-marca-primario transition duration-200 focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                        // Mostrar la foto de perfil
                        echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . $imagenPerfil . '" alt="Imagen de Perfil de ' . $nombreUsuario . '">';
                        echo '        <span>' . $nombreUsuario . '</span>';
                        echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        echo '    </button>';

                        // Contenido del desplegable (oculto por defecto)
                        echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                        // Enlaces del desplegable - Rutas relativas desde php/categoria-index.php
                        echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Perfil</a>'; // Mantener "Mi Perfil" según tu código original
                        echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';

                        // Enlaces de gestión (solo para vendedores/admin) - Añadidos/Integrados
                        if ($userRole === 'vendedor' || $userRole === 'admin') {
                            echo '        <hr class="border-gray-200">'; // Separador
                            echo '        <a href="../productos/gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Productos</a>';
                            echo '        <a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Servicios</a>';
                        }

                        // Enlaces de comunidad/contenido
                        echo '        <hr class="border-gray-200">'; // Separador
                        echo '        <a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                        // Enlace a la página principal de Categorías (asumimos que la página de listado está en php/categoria/index.php)
                        echo '        <a href="../categoria/index.php"" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categorias</a>'; // Mantener "Categorias" según tu código

                        // Enlace a Mis Mensajes (Añadido/Integrado)
                        // Asumiendo que la carpeta mensajes existe con conversacioens.php dentro
                        // Si has descartado esta funcionalidad, puedes eliminar esta línea
                        if (file_exists('mensajes/conversaciones.php')) { // Verificar si el archivo de mensajes existe
                            echo '        <a href="mensajes/conversaciones.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200 font-semibold">Mis Mensajes</a>';
                        }


                        // Enlace para cerrar sesión
                        echo '        <hr class="border-gray-200">'; // Separador antes de cerrar sesión
                        echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';

                        echo '    </div>'; // Cierre del div del desplegable
                        echo '</div>'; // Cierre del div relativo del desplegable

                    } else {
                        // Código para usuarios NO logueados
                        // Rutas de login/registro relativas desde php/categoria-index.php
                        echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
                        echo '<a href="../usuarios/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                    }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Términos de Servicio</h1>

            <div class="prose max-w-none"> <p>Bienvenido a We-Connect. Al acceder o utilizar nuestra plataforma, aceptas cumplir y estar sujeto a los siguientes Términos de Servicio.</p>

                <h2>Uso de la Plataforma</h2>
                <p>We-Connect proporciona un espacio para que emprendedores ofrezcan y vendan sus productos y servicios, y para que los usuarios encuentren y adquieran estos productos y servicios. El uso de la plataforma debe ser para fines lícitos y respetando a otros usuarios.</p>

                <h2>Cuentas de Usuario</h2>
                <p>Eres responsable de mantener la confidencialidad de tu cuenta y contraseña y de restringir el acceso a tu ordenador. Aceptas la responsabilidad de todas las actividades que ocurran bajo tu cuenta o contraseña.</p>

                <h2>Contenido del Usuario</h2>
                <p>Eres el único responsable del contenido que publicas en la plataforma (productos, servicios, comentarios, etc.). Te comprometes a no publicar contenido que sea ilegal, ofensivo, difamatorio, o que infrinja derechos de propiedad intelectual.</p>

                <h2>Transacciones</h2>
                <p>We-Connect facilita la conexión entre compradores y vendedores, pero no es parte directa en las transacciones. Los acuerdos de compra/venta se establecen directamente entre el comprador y el vendedor. No garantizamos la calidad, seguridad o legalidad de los productos o servicios ofrecidos.</p>

                <h2>Modificaciones</h2>
                <p>Nos reservamos el derecho de modificar estos Términos de Servicio en cualquier momento. Te notificaremos sobre cambios significativos publicando los nuevos términos en la plataforma.</p>

                <h2>Terminación</h2>
                <p>Podemos suspender o terminar tu acceso a la plataforma si incumples estos Términos de Servicio.</p>

                <h2>Ley Aplicable</h2>
                <p>Estos Términos se regirán e interpretarán de acuerdo con las leyes de [Tu País/Región], sin dar efecto a sus disposiciones sobre conflicto de leyes.</p>

                <p>Al usar We-Connect, confirmas que has leído, entendido y aceptado estos Términos de Servicio.</p>

                <p>Fecha de entrada en vigor: 18 de mayo de 2025</p>
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
    <script src="../../js/script2.js"></script>

</body>
</html>