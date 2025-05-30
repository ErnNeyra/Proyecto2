<?php
// Página para listar todas las categorías disponibles

    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('../util/config.php');
    require ('../util/depurar.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION["usuario"]["usuario"])) {
        //CUIDADO AMIGO esta función es peligrosa, tiene que ejecutarse antes de que
        //se ejecute el código body
        header("location: ../usuarios/login.php");
        exit;
    }

    $aliasUsuarioActual = isset($_SESSION["usuario"]["usuario"]) ? htmlspecialchars($_SESSION['usuario']['usuario']) : '';

    // --- Obtener todas las categorías de la base de datos ---
    $sqlCategorias = "SELECT id_categoria, nombre, descripcion FROM categoria ORDER BY nombre"; // Selecciona id, nombre y descripción
    $resultadoCategorias = $_conexion->query($sqlCategorias);

    $categorias = [];
    if ($resultadoCategorias) {
        while ($fila = $resultadoCategorias->fetch_assoc()) {
            $categorias[] = $fila; // Guarda todo el array asociativo (id_categoria, nombre, descripcion)
        }
        $resultadoCategorias->free(); // Liberar memoria
    } else {
        // Manejar error si la consulta de categorías falla
        $mensaje_error = "Error al cargar las categorías: " . $_conexion->error;
        error_log("Error al obtener categorías para listar en php/categoria/index.php: " . $_conexion->error);
    }

    $_conexion->close(); // Cerrar la conexión a la base de datos

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar Categorías | We-Connect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/categorias.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    </head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="main-header shadow-md sticky top-0 z-50">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="../productos/producto.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="../servicios/servicio.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="../contacto.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="../recursos/recursos.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Recursos </a>
                <?php
                if (isset($_SESSION['usuario'])) {
                    // Obtener datos del usuario de la sesión, usando htmlspecialchars por seguridad
                    $nombreUsuario = htmlspecialchars($_SESSION['usuario']['usuario']); // Usamos 'usuario'

                    // Determinar la ruta de la imagen de perfil
                    // RUTA CORREGIDA: Ahora es relativa a la ubicación actual (php/categoria/)
                    $imagenPerfil = '../util/img/usuario.png'; // Ruta por defecto CORRECTA

                    // Verificamos si existe la foto de perfil del usuario en la sesión y no está vacía
                    if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                        // La ruta guardada en BD es relativa a 'util/', así que desde php/categoria-index.php es 'util/' + ruta_bd
                        $rutaImagenBD = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                    
                        // Comprobamos si el archivo existe antes de usarlo
                        // Esto es útil si las rutas en la DB no siempre son precisas o los archivos se borran
                        if (file_exists($rutaImagenBD)) {
                            $imagenPerfil = htmlspecialchars($rutaImagenBD); // Usar la ruta validada
                        }
                        // Si no existe en el sistema de archivos, la variable $imagenPerfil mantiene la ruta por defecto (que ahora es correcta)
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
                    echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Perfil</a>';
                    echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';

                    // Enlaces de gestión (solo para vendedores/admin)
                    if ($userRole === 'vendedor' || $userRole === 'admin') {
                        echo '        <hr class="border-gray-200">'; // Separador
                        echo '        <a href="../productos/gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Productos</a>';
                        echo '        <a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Servicios</a>';
                    }

                    // Enlaces de comunidad/contenido
                    echo '        <hr class="border-gray-200">'; // Separador
                    echo '        <a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                    echo '        <a href="index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categorias</a>';

                    // Enlace a Mis Mensajes
                    // Si has descartado esta funcionalidad, puedes eliminar esta línea
                    // La comprobación file_exists no es necesaria en el echo, la ruta ../mensajes/conversaciones.php es correcta desde aquí
                    echo '        <a href="../mensajes/conversaciones.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200 font-semibold">Mis Mensajes</a>';


                    // Enlace para cerrar sesión
                    echo '        <hr class="border-gray-200">'; // Separador antes de cerrar sesión
                    echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';

                    echo '    </div>'; // Cierre del div del desplegable
                    echo '</div>'; // Cierre del div relativo del desplegable

                } else {
                    // Código para usuarios NO logueados
                    echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
                    echo '<a href="../usuarios/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Explora por Categorías</h1>

            <?php if (!empty($mensaje_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($categorias)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($categorias as $categoria): ?>
                        <div class="category-card flex flex-col justify-between">
                             <div>
                                <h2 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($categoria['nombre']); ?></h2>
                                 <?php if (!empty($categoria['descripcion'])): ?>
                                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars(substr($categoria['descripcion'], 0, 100)) . (strlen($categoria['descripcion']) > 100 ? '...' : ''); ?></p>
                                 <?php endif; ?>
                             </div>
                             <div class="mt-auto">
                                 <a href="ver_categoria.php?cat=<?php echo urlencode(htmlspecialchars($categoria['nombre'])); ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold py-2 px-4 rounded">
                                     Ver Contenido <i class="fas fa-arrow-right ml-1"></i>
                                 </a>
                             </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (empty($mensaje_error)): ?>
                <p class="text-center text-gray-600">Aún no hay categorías disponibles.</p>
            <?php endif; ?>

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
                        <li><a href="../terminos/faq.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas Frecuentes</a></li>
                        <li><a href="../terminos/terms.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos de Servicio</a></li>
                        <li><a href="../terminos/privacy.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política de Privacidad</a></li>
                        <li><a href="../contacto.php"
                                     class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a></li>
                    </ul>
                </div>
                   <div class="footer-section social-icons-footer">
                    <h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3>
                    <div class="flex justify-center md:justify-start space-x-4 text-xl">
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-twitter-f"></i></a>
                        <a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright border-t border-gray-700 pt-8 text-gray-500 text-sm text-center">
                <p>© <?php echo date('Y'); ?> We-Connect. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
    <script src="../../js/script2.js"></script>
    
</body>
</html>