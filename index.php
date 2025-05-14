<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We-Connect: Tu Plataforma de Conexión Profesional</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/styles.css">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="shadow-md sticky top-0 z-50">
        <div class="container mx-auto py-4 px-4 flex items-center justify-between">
            <a href="index.php" class="logo text-2xl font-bold text-gray-900 hover:text-marca-primario transition duration-200">We-Connect</a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="php/producto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Productos</a>
                <a href="php/servicio.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Servicios</a>
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
        $imagenPerfil = 'php/util/img/usuario.jpg'; // Ruta por defecto corregida
    }

    // Estructura del desplegable
    echo '<div class="relative">';
    echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-marca-primario transition duration-200 focus:outline-none" aria-expanded="false" aria-haspopup="true">';
    // Mostrar la foto de perfil
    echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . $imagenPerfil . '" alt="Imagen de Perfil de ' . $nombreUsuario . '">';
    echo '        <span>' . $nombreUsuario . '</span>';
    echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
    echo '    </button>';

    // Contenido del desplegable (oculto por defecto)
    echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
    echo '        <a href="php/perfilUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Perfil</a>';
    echo '        <a href="php/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
    echo '        <hr class="border-gray-200">';
    echo '        <a href="php/util/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
    echo '    </div>';
    echo '</div>';
} else {
    echo '<a href="php/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
    echo '<a href="php/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
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

    <section class="hero-section space-background text-white py-20 md:py-32 flex items-center relative overflow-hidden observe-section">

        <div class="stars"></div>
        <div class="twinkling"></div>
        <div class="container mx-auto px-4 text-center hero-content relative z-10">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight js-fade-in-up" data-delay="0s">
                Bienvenido a We-Connect
            </h1>
            <p class="text-lg md:text-xl mb-10 max-w-3xl mx-auto js-fade-in-up" data-delay="0.3s">
                Conecta con profesionales, encuentra servicios y descubre productos increíbles.
            </p>
            </div>
    </section>

    <section id="featured-products" class="featured-products py-8 observe-section">
        <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center js-fade-in-up" data-delay="0s">Productos Destacados</h2>
            <div id="productos-carrusel-container" class="relative overflow-hidden">
                <div id="productos-carrusel" class="whitespace-nowrap scroll-smooth transition-transform duration-300 py-4 flex gap-4"> <?php
                    // Check if $_conexion is set and valid
                    $sql = "SELECT * FROM producto LIMIT 10"; // Ejemplo: los 10 primeros productos
                    $resultado = $_conexion->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while ($producto = $resultado->fetch_assoc()) {
                            echo '<div class="producto-card inline-block w-72 shadow-md rounded-md overflow-hidden border border-gray-200 bg-white flex-shrink-0 js-fade-in-up">'; // Añadido flex-shrink-0 y js-fade-in-up
                            echo '<img src="php/' . htmlspecialchars($producto['imagen']) . '" alt="' . htmlspecialchars($producto['nombre']) . '" class="w-full h-40 object-cover">';
                            echo '<div class="producto-card-content p-4">';
                            echo '<h3 class="font-semibold text-gray-700">' . htmlspecialchars($producto['nombre']) . '</h3>';
                            echo '<p class="text-gray-600 text-sm">' . htmlspecialchars(substr($producto['descripcion'], 0, 100)) . '...</p>';
                            echo '<a href="php/detalleProducto.php?id_producto=' . $producto['id_producto'] . '" class="text-yellow-600 hover:text-yellow-700 font-semibold mt-2 inline-block">Ver Detalles</a>';
                            echo '</div>';
                            echo '</div>';
                            echo '<input type="hidden" name="id_producto" value=" '. $producto["id_producto"] .'">';
                        }
                    } else {
                        echo "<p class='text-center text-gray-600 w-full'>No hay productos destacados por el momento.</p>"; // width full para centrar
                    }
                                            // No cierres la conexión aquí si la necesitas en otras partes de la página
                                            // $_conexion->close();
                    ?>
                </div>
                <button id="prev-producto" class="carousel-control absolute left-0 top-1/2 transform -translate-y-1/2 ml-2 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button id="next-producto" class="carousel-control absolute right-0 top-1/2 transform -translate-y-1/2 mr-2 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div id="carousel-indicators" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    </div>
                <button id="pause-play-button" class="carousel-control absolute bottom-10 right-4 focus:outline-none">
                    <i id="play-icon" class="fas fa-play"></i>
                    <i id="pause-icon" class="fas fa-pause" style="display:none;"></i>
                </button>
            </div>
        </div>
    </section>

    <section class="why-choose-us py-12 bg-white observe-section">
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

    <section class="how-it-works py-12 observe-section"> <div class="container mx-auto px-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center js-fade-in-up" data-delay="0s">¿Cómo funciona?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.1s">
                    <i class="mx-auto h-12 w-12 text-yellow-500 fas fa-user-plus"></i>
                    <h3 class="font-semibold text-black mt-2">Regístrate</h3>
                    <p class="text-gray-600">Crea tu perfil y cuéntale a la comunidad sobre tu emprendimiento.</p>
                </div>
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.2s">
                    <i class="mx-auto h-12 w-12 text-yellow-500 fas fa-search"></i>
                    <h3 class="font-semibold text-black mt-2">Explora</h3>
                    <p class="text-gray-600">Descubre perfiles de otros emprendedores y sus ofertas de productos o servicios.</p>
                </div>
                <div class="how-it-works-item text-center js-fade-in-up" data-delay="0.3s">
                    <i class="mx-auto h-12 w-12 text-yellow-500 fas fa-handshake"></i>
                    <h3 class="font-semibold text-black mt-2">Conecta</h3>
                    <p class="text-gray-600">Accede a herramientas premium para contactar y colaborar directamente (próximamente).</p>
                </div>
            </div>
        </div>
    </section>
    </main>

    <footer class="bg-black py-8 text-center text-gray-400">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left mb-8"><div class="footer-section">
                    <h3 class="text-lg font-semibold text-white mb-4">We-Connect</h3>
                    <p>Conectando profesionales y oportunidades en un solo lugar.</p>
                </div>
                <div class="footer-section footer-links">
                    <h3 class="text-lg font-semibold text-white mb-4">Enlaces Útiles</h3>
                    <ul>
                        <li><a href="#" class="hover:text-marca-secundaria transition duration-200">Términos de Servicio</a></li>
                        <li><a href="#" class="hover:text-marca-secundaria transition duration-200">Política de Privacidad</a></li>
                        <li><a href="#" class="hover:text-marca-secundaria transition duration-200">Ayuda</a></li>
                        <li><a href="php/contacto.php" class="hover:text-marca-secundaria transition duration-200">Contacto</a></li>
                    </ul>
                </div>
                <div class="footer-section social-icons-footer"> <h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3>
                    <div class="flex justify-center md:justify-start space-x-4 text-xl"> <a href="#" class="hover:text-marca-secundaria transition duration-200"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                </div>
            </div>
            <div class="copyright border-t border-gray-700 pt-8"> <p>&copy; <?php echo date('Y'); ?> We-Connect. Todos los derechos reservados.</p> </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdownButton = document.getElementById('user-dropdown-button');
            const userDropdown = document.getElementById('user-dropdown');

            if (userDropdownButton && userDropdown) {
                userDropdownButton.addEventListener('click', function() {
                    userDropdown.classList.toggle('hidden');
                    this.setAttribute('aria-expanded', !userDropdown.classList.contains('hidden'));
                });

                // Cerrar el desplegable si se hace clic fuera
                document.addEventListener('click', function(event) {
                    if (!userDropdownButton.contains(event.target) && !userDropdown.contains(event.target)) {
                        userDropdown.classList.add('hidden');
                        userDropdownButton.setAttribute('aria-expanded', false);
                    }
                });
            }
        });
    </script>
    <script src="script2.js"></script>
</body>
</html>