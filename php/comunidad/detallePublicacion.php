<?php
// Página para ver los detalles de una publicación del Tablón

// Iniciar sesión (si es necesario, aunque la página puede ser pública)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos de utilidad (conexión BD y depuración) - Rutas relativas desde php/comunidad/
require('../util/config.php');
require('../util/depurar.php');

$aliasUsuarioActual = isset($_SESSION["usuario"]["usuario"]) ? htmlspecialchars($_SESSION['usuario']['usuario']) : '';

// --- Obtener el ID de la publicación de la URL ---
$idPublicacion = $_GET['id'] ?? null;
$publicacion = null; // Para almacenar los datos de la publicación

$mensaje_error = ""; // Para mensajes de error

// --- Cargar la publicación desde la base de datos ---
if ($idPublicacion === null || !is_numeric($idPublicacion)) {
    $mensaje_error = "ID de publicación no especificado o inválido.";
} else {
    $idPublicacion = (int)$idPublicacion;

    // Consultar la base de datos para obtener los detalles de la publicación
    // Incluimos la columna categoria_nombre en la selección
    $sqlPublicacion = "SELECT id, tipo, titulo, descripcion, usuario_alias, fecha_publicacion, categoria_nombre FROM necesidades_ofertas WHERE id = ?";
    $stmtPublicacion = $_conexion->prepare($sqlPublicacion);

    if ($stmtPublicacion === false) {
        $mensaje_error = "Error preparando la consulta para cargar la publicación.";
        error_log("Error preparando consulta SELECT necesidades_ofertas en detallePublicacion.php: " . $_conexion->error);
    } else {
        $stmtPublicacion->bind_param("i", $idPublicacion);
        $stmtPublicacion->execute();
        $resultadoPublicacion = $stmtPublicacion->get_result();

        if ($resultadoPublicacion->num_rows === 0) {
            $mensaje_error = "Publicación no encontrada.";
        } else {
            $publicacion = $resultadoPublicacion->fetch_assoc(); // Obtiene todos los datos
             // Limpiar datos para mostrar en la página
            $publicacion['titulo'] = htmlspecialchars($publicacion['titulo']);
            $publicacion['descripcion'] = htmlspecialchars($publicacion['descripcion']);
            $publicacion['usuario_alias'] = htmlspecialchars($publicacion['usuario_alias']);
            $publicacion['categoria_nombre'] = htmlspecialchars($publicacion['categoria_nombre'] ?? 'No asignada'); // Limpiar y manejar si es NULL

        }
        $stmtPublicacion->close();
    }
}

$_conexion->close(); // Cerrar la conexión a la base de datos al final

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $publicacion ? 'Detalle: ' . $publicacion['titulo'] . ' | Tablón | We-Connect' : 'Publicación no encontrada | Tablón | We-Connect'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
   
    <link rel="stylesheet" href="../../css/styles.css">
     <link rel="stylesheet" href="../../css/tablon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
       
    </style>

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
                 <a href="../recursos/recursos.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Recursos</a>
                


                         <?php


if (isset($_SESSION['usuario'])) {
    // Obtener datos del usuario de la sesión, usando htmlspecialchars por seguridad
    $nombreUsuario = htmlspecialchars($_SESSION['usuario']['usuario']); // Usamos 'usuario'

    // Determinar la ruta de la imagen de perfil
    // Ruta por defecto desde php/categoria-index.php a php/util/
    $imagenPerfil = 'util/img/usuario.png'; // Ruta por defecto desde php/

    // Verificamos si existe la foto de perfil del usuario en la sesión y no está vacía
    if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
        // La ruta guardada en BD es relativa a 'util/', así que desde php/categoria-index.php es 'util/' + ruta_bd
        $rutaImagenBD = 'util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
       
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
    echo '        <a href="usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';

    echo '    </div>'; // Cierre del div del desplegable
    echo '</div>'; // Cierre del div relativo del desplegable

} else {
    
    echo '<a href="usuarios/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
    echo '<a href="usuarios/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
}
?>
            </nav>
        </div>
    </header>


    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl mx-auto">

            <?php if ($publicacion): ?>
                <div class="publicacion-detail-card">
                    <h1><?php echo $publicacion['titulo']; ?></h1>

                    <div class="meta-info">
                        <span>Tipo: <strong><?php echo htmlspecialchars(ucfirst($publicacion['tipo'])); ?></strong></span> |
                        <span>Por: <strong><?php echo $publicacion['usuario_alias']; ?></strong></span> |
                         <span>Categoría: <strong class="categoria-display"><?php echo $publicacion['categoria_nombre']; ?></strong></span> |
                        <span>Publicado el: <strong><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($publicacion['fecha_publicacion']))); ?></strong></span>
                    </div>

                    <p><?php echo $publicacion['descripcion']; ?></p>

                     <div class="action-links flex flex-wrap justify-end">
                         <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['usuario'] !== $publicacion['usuario_alias']): ?>
                              <a href="../mensajes/iniciarConversacion.php?user=<?php echo urlencode($publicacion['usuario_alias']); ?>">Contactar</a>
                         <?php endif; ?>

                         <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['usuario'] === $publicacion['usuario_alias']): ?>
                             <a href="editarPublicacion.php?id=<?php echo htmlspecialchars($publicacion['id']); ?>">Editar Publicación</a>
                         <?php endif; ?>

                         <a href="tablon.php">Volver al Tablón</a>
                    </div>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <h1 class="text-3xl font-semibold text-gray-800 mb-4">Publicación no encontrada</h1>
                    <p class="text-gray-600 mb-8"><?php echo htmlspecialchars($mensaje_error); ?></p>
                    <a href="tablon.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                         Volver al Tablón
                     </a>
                </div>
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

    <script src="../../js/desplegable.js"></script>
    <script src="../../js/script2.js"></script>

</body>
</html>