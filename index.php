<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect: Tu Plataforma de Conexión Profesional</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/index.css">
     <link rel="stylesheet" href="css/styles.css">
    

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="shadow-md sticky top-0 z-50">
        <div class="container mx-auto py-4 px-4 flex items-center justify-between">
            <a href="index.php" class="logo inline-block">
                <img src="php/util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="php/recursos/recursos.php" class="text-gray-700 hover:text-black mr-4 font-semibold">Recursos</a>
                <a href="php/productos/producto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Productos</a>
                <a href="php/servicios/servicio.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Servicios</a>
                <a href="php/contacto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Contacto</a>
                <?php
                    session_start(); // Inicia la sesión
                    if (isset($_SESSION['usuario'])) {
                        $nombreUsuario = htmlspecialchars($_SESSION['usuario']['usuario']); // Usamos 'usuario'
                        $imagenPerfil = ''; // Inicializamos la variable de la imagen de perfil

                        // Verificamos si existe la foto de perfil del usuario en la sesión y no está vacía
                        if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                            $imagenPerfil = htmlspecialchars($_SESSION['usuario']['foto_perfil']);
                        } else {
                            // Si no hay foto de perfil del usuario, usamos la imagen por defecto
                            $imagenPerfil = 'php/util/img/usuario.png'; // Ruta por defecto corregida
                        }

                        // Estructura del desplegable
                        echo '<div class="relative">'; // Clase relativa para el posicionamiento absoluto del desplegable
                        echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-marca-primario transition duration-200 focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                        // Mostrar la foto de perfil
                        echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . $imagenPerfil . '" alt="Imagen de Perfil de ' . $nombreUsuario . '">';
                        echo '        <span>' . $nombreUsuario . '</span>';
                        echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        echo '    </button>';

                        // Contenido del desplegable (oculto por defecto)
                        echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                        echo '        <a href="php/usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Perfil</a>';
                        echo '        <a href="php/usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                        echo '        <hr class="border-gray-200">';
                       echo '        <a href="php/comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                        echo '        <a href="php/categoria/index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categoria</a>';

                       echo '        <hr class="border-red-200">';
                        echo '        <a href="php/usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                        echo '    </div>';
                        echo '</div>';
                    } else {
                        echo '<a href="php/usuarios/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
                        echo '<a href="php/usuarios/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                    }
                ?>
            </nav>
        </div>
        <?php
            error_reporting( E_ALL );
            ini_set("display_errors", 1 );
            require('php/util/config.php');
        ?>
    </header>

    <main class="flex-grow">

    <section class="hero-section space-background text-white py-20 md:py-32 flex items-center relative overflow-hidden observe-section">
        <div class="stars"></div>
        <div class="twinkling"></div>
        <div class="container mx-auto px-4 text-center hero-content relative z-10">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight js-fade-in-up" data-delay="0s">
                Bienvenido a 
                <div class="relative h-32 flex items-center justify-center overflow-hidden">
                    <img src="php/util/img/LogoBlanco.png" alt="We-Connect Logo" class="max-h-full w-auto scale-125 object-contain">
                </div>
            </h1>
            <p class="text-lg md:text-xl mb-10 max-w-3xl mx-auto js-fade-in-up" data-delay="0.3s">
                Conecta con profesionales, encuentra servicios y descubre productos increíbles.
            </p>
            </div>
    </section>

    <section id="featured-products" class="featured-products py-8 observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center js-fade-in-up" data-delay="0s">Productos Destacados</h2>
            <div id="productos-carrusel-wrapper" class="relative group">
                <div id="productos-carrusel-container" class="overflow-hidden">
                    <div id="productos-carrusel" class="flex transition-transform duration-500 ease-in-out py-4 gap-4">
                        <?php
                            //*** NO TOCAR ***

                            //recojo 20 productos y servicios aleatorios
                            $sql =" SELECT id_producto AS id, nombre, imagen, 'producto' AS tipo FROM producto
                                    UNION ALL
                                    SELECT id_servicio AS id, nombre, imagen, 'servicio' AS tipo FROM servicio
                                    ORDER BY RAND() LIMIT 20"; // Cambié el límite a 20 para mostrar más productos/servicios
                            $resultado = $_conexion->query($sql);
                            $productos_servicios = [];


                            if($resultado){
                                while($item = $resultado->fetch_assoc()){
                                    $productos_servicios[] = $item;
                                }
                            }

                            if (!empty($productos_servicios)) {
                                foreach ($productos_servicios as $item) {
                                    $detalle_url = $item['tipo'] === 'producto' ? 'php/productos/detalleProducto.php?id_producto=' : 'php/servicios/detalleServicio.php?id_servicio='; // Cambio la URL de detalle según el tipo

                                    // Mantengo tus clases responsive de ancho, pero recuerda que tu CSS custom o index.css podría afectarlas
                                    // Añadido flex-none para que no se encojan por defecto en el flex container
                                    echo '<div class="producto-card flex-none w-full sm:w-1/2 md:w-1/3 px-2 js-fade-in-up">';
                                    echo '    <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl h-full flex flex-col">'; // Añadido flex y flex-col para layout interno
                                    // Aspect ratio para la imagen
                                    echo '      <a href="' . $detalle_url . $item['id'] . '" class="block w-full h-40 overflow-hidden">'; // Ajustado h-40 directamente
                                    echo '        <img src="php/util/' . htmlspecialchars($item['imagen']) . '" alt="' . htmlspecialchars($item['nombre']) . '" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">';
                                    echo '      </a>';
                                    echo '      <div class="producto-card-content p-4 flex flex-col flex-grow">'; // Añadido flex, flex-col, flex-grow
                                    echo '        <h3 class="font-semibold text-gray-700 mb-2 truncate">' . htmlspecialchars($item['nombre']) . '</h3>'; // Añadido truncate y mb-2
                                    echo '        <a href="' . $detalle_url . $item['id'] . '" class="mt-auto inline-block text-center bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold text-sm">Ver Detalles</a>'; // Añadido mt-auto, text-center, text-sm
                                    echo '      </div>';
                                    echo '    </div>';
                                    echo '</div>';
                                }
                            } else {
                                echo "<p class='text-center text-gray-600 w-full py-8'>No hay productos o servicios destacados por el momento.</p>";
                            }
                        ?>
                    </div>
                </div>

                <button id="prev-producto" aria-label="Anterior" class="carousel-control absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-4 sm:-translate-x-6 rounded-full p-3 shadow-md focus:outline-none z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="next-producto" aria-label="Siguiente" class="carousel-control absolute right-0 top-1/2 transform -translate-y-1/2 translate-x-4 sm:translate-x-6 rounded-full p-3 shadow-md focus:outline-none z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

                <div id="carousel-indicators" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    </div>

                 <button id="pause-play-button" class="carousel-control absolute bottom-10 right-4 focus:outline-none z-10 bg-white/80 hover:bg-white rounded-full p-2 shadow-md">
                     <i id="play-icon" class="fas fa-play"></i>
                     <i id="pause-icon" class="fas fa-pause" style="display:none;"></i>
                 </button>

            </div>
        </div>
    </section>


    <section class="call-to-action bg-yellow-500 py-12 text-center observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center js-fade-in-up" data-delay="0s">¿Por Qué Elegirnos?</h2>
            <div class="why-choose-us-grid grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="why-choose-us-item text-center p-6 border border-gray-200 rounded-md shadow-sm js-fade-in-up" data-delay="0.1s">
                    <i class="fas fa-users text-4xl text-yellow-500 mb-3"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Amplia Red</h3>
                    <p class="text-gray-600">Conecta con profesionales de diversas industrias y expande tu red de contactos.</p>
                </div>
                <div class="why-choose-us-item text-center p-6 border border-gray-200 rounded-md shadow-sm js-fade-in-up" data-delay="0.2s">
                    <i class="fas fa-briefcase text-4xl text-yellow-500 mb-3"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Oportunidades de Negocio</h3>
                    <p class="text-gray-600">Encuentra nuevas oportunidades de negocio, colaboraciones y proyectos emocionantes.</p>
                </div>
                <div class="why-choose-us-item text-center p-6 border border-gray-200 rounded-md shadow-sm js-fade-in-up" data-delay="0.3s">
                    <i class="fas fa-tools text-4xl text-yellow-500 mb-3"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Servicios Especializados</h3>
                    <p class="text-gray-600">Accede a una amplia gama de servicios especializados para impulsar tu crecimiento profesional.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials py-12 bg-gray-200 observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center js-fade-in-up" data-delay="0s">Testimonios</h2>
            <div class="testimonial-grid grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="testimonial-card bg-white rounded-md shadow-md p-6 text-center border border-gray-200 js-fade-in-up" data-delay="0.1s">
                    <img src="php/util/img/susuan.jpg" alt="Ana Rodríguez" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Ana Rodríguez</h3>
                    <p class="quote text-gray-600 italic">"We-Connect ha sido fundamental para mi negocio. He encontrado clientes y colaboradores increíbles."</p>
                </div>
                <div class="testimonial-card bg-white rounded-md shadow-md p-6 text-center border border-gray-200 js-fade-in-up" data-delay="0.2s">
                    <img src="php/util/img/pruano.jpg" alt="Carlos Pérez" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Carlos Pérez</h3>
                    <p class="quote text-gray-600 italic">"La plataforma es muy fácil de usar y me ha permitido ampliar mi red de contactos de manera significativa."</p>
                </div>
            </div>
        </div>
    </section>

  <section class="how-it-works py-12 observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center js-fade-in-up" data-delay="0s">¿Cómo
                funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8grid-cols-1 md:grid-cols-3 gap-8">
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.1s">
                    <div class="step-circle bg-yellow-200 text-yellow-700 font-bold w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Regístrate</h3>
                    <p class="text-gray-600">Crea tu cuenta de forma rápida y sencilla para empezar a conectar.</p>
                </div>
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.2s">
                    <div class="step-circle bg-yellow-200 text-yellow-700 font-bold w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Explora</h3>
                    <p class="text-gray-600">Navega por perfiles, servicios y productos para encontrar lo que necesitas.</p>
                </div>
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.3s">
                    <div class="step-circle bg-yellow-200 text-yellow-700 font-bold w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Conecta</h3>
                    <p class="text-gray-600">Establece conexiones profesionales y colabora en proyectos interesantes.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="call-to-action bg-yellow-500 py-12 text-center observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 js-fade-in-up" data-delay="0s">Únete a We-Connect Hoy</h2>
            <p class="text-xl text-gray-700 mb-8 js-fade-in-up" data-delay="0.2s">
                Comienza a construir tu futuro profesional y a descubrir oportunidades increíbles.
            </p>
            <a href="php/usuarios/registro.php" class="cta-button bg-gray-800 text-white py-3 px-8 rounded-md hover:bg-gray-900 transition duration-200 font-semibold text-lg js-fade-in-up" data-delay="0.4s">
                Regístrate Ahora
            </a>
        </div>
    </section>


    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4">Acerca de We-Connect</h3>
                    <p class="text-gray-300">
                        We-Connect es una plataforma que conecta a compradores y vendedores de productos y servicios,
                        facilitando el comercio y las oportunidades de negocio.
                    </p>
                </div>
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4">Enlaces Útiles</h3>
                    <ul class="list-none p-0">
                        <li><a href="index.php" class="hover:text-marca-secundaria transition duration-200">Inicio</a></li>
                        <li><a href="php/productos/producto.php"
                                class="hover:text-marca-secundaria transition duration-200">Productos</a></li>
                        <li><a href="php/servicios/servicio.php"
                                class="hover:text-marca-secundaria transition duration-200">Servicios</a></li>
                        <li><a href="php/contacto.php"
                                class="hover:text-marca-secundaria transition duration-200">Contacto</a></li>
                    </ul>
                </div>
               <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4 text-white">Soporte</h3>
                    <ul class="list-none p-0">
                        <li><a href="php/terminos/faq.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas Frecuentes</a></li>
                        <li><a href="php/terminos/terms.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos de Servicio</a></li>
                        <li><a href="php/terminos/privacy.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política de Privacidad</a></li>
                        <li><a href="php/contacto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a></li>
                    </ul>
                </div>
                <div class="footer-section social-icons-footer">
                    <h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3>
                    <div class="flex justify-center md:justify-start space-x-4 text-xl">
                        <a href="#" class="hover:text-marca-secundaria transition duration-200"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200"><i
                                class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200"><i
                                class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright border-t border-gray-700 pt-8">
                <p>&copy; <?php echo date('Y'); ?> We-Connect. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="js/desplegable.js"></script>
      <script src="js/script2.js"></script>
    

</body>
</html>