<?php
// Página para ver contenido agrupado por categoría

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir archivos de utilidad (conexión BD y depuración) - Rutas relativas desde php/categoria/
require('../util/config.php');
require('../util/depurar.php');

$aliasUsuarioActual = isset($_SESSION["usuario"]["usuario"]) ? htmlspecialchars($_SESSION['usuario']['usuario']) : '';

// --- Obtener y validar la categoría de la URL ---
$categoria_nombre = $_GET['cat'] ?? '';
$categoria_info = null; // Para almacenar nombre y descripción de la categoría

// Cargar la información de la categoría para validación y visualización
// Modificada para seleccionar también la descripción
$sqlCategoria = "SELECT nombre, descripcion FROM categoria WHERE nombre = ?";
$stmtCategoria = $_conexion->prepare($sqlCategoria);

if ($stmtCategoria === false) {
    // Manejar error de preparación
    error_log("Error preparando la consulta de verificación de categoría en ver_categoria.php: " . $_conexion->error);
    $mensaje_error = "Error interno al verificar la categoría.";
} else {
    $stmtCategoria->bind_param("s", $categoria_nombre);
    $stmtCategoria->execute();
    $resultadoCategoria = $stmtCategoria->get_result();

    if ($resultadoCategoria->num_rows > 0) {
        $categoria_info = $resultadoCategoria->fetch_assoc(); // Obtiene nombre y descripción
    } else {
        // Categoría no encontrada en la BD
        $mensaje_error = "La categoría '" . htmlspecialchars($categoria_nombre) . "' no fue encontrada.";
    }
    $stmtCategoria->close();
}

// Ahora $categoria_valida es simplemente si $categoria_info no es null
$categoria_valida = ($categoria_info !== null);


// --- Si la categoría es válida, obtener el contenido asociado ---
$productos_asociados = [];
$servicios_asociados = [];
$publicaciones_asociadas = [];

if ($categoria_valida) {
    $categoria_nombre_db = $categoria_info['nombre']; // Usar el nombre validado de la BD

    // Obtener productos de esta categoría
    // Seleccionar las columnas necesarias para mostrar el resumen
    $sqlProductos = "SELECT id_producto, nombre, descripcion, precio, imagen FROM producto WHERE categoria = ?";
    $stmtProductos = $_conexion->prepare($sqlProductos);
    if ($stmtProductos) {
        $stmtProductos->bind_param("s", $categoria_nombre_db);
        $stmtProductos->execute();
        $resultadoProductos = $stmtProductos->get_result();
        if ($resultadoProductos) {
            while($fila = $resultadoProductos->fetch_assoc()){
                $productos_asociados[] = $fila;
            }
            $resultadoProductos->free();
        }
        $stmtProductos->close();
    } else {
         error_log("Error consultando productos por categoría en ver_categoria.php: " . $_conexion->error);
    }

    // Obtener servicios de esta categoría
    // Seleccionar las columnas necesarias para mostrar el resumen
    $sqlServicios = "SELECT id_servicio, nombre, descripcion, precio, imagen FROM servicio WHERE categoria = ?";
    $stmtServicios = $_conexion->prepare($sqlServicios);
    if ($stmtServicios) {
        $stmtServicios->bind_param("s", $categoria_nombre_db);
        $stmtServicios->execute();
        $resultadoServicios = $stmtServicios->get_result();
        if ($resultadoServicios) {
            while($fila = $resultadoServicios->fetch_assoc()){
                $servicios_asociados[] = $fila;
            }
            $resultadoServicios->free();
        }
        $stmtServicios->close();
    } else {
         error_log("Error consultando servicios por categoría en ver_categoria.php: " . $_conexion->error);
    }

    // Obtener publicaciones del tablón de esta categoría
    // Usamos la nueva columna categoria_nombre
    // Seleccionar las columnas necesarias para mostrar el resumen
    $sqlPublicaciones = "SELECT id, tipo, titulo, descripcion, usuario_alias, fecha_publicacion FROM necesidades_ofertas WHERE categoria_nombre = ? ORDER BY fecha_publicacion DESC";
    $stmtPublicaciones = $_conexion->prepare($sqlPublicaciones);
     if ($stmtPublicaciones) {
        $stmtPublicaciones->bind_param("s", $categoria_nombre_db);
        $stmtPublicaciones->execute();
        $resultadoPublicaciones = $stmtPublicaciones->get_result();
        if ($resultadoPublicaciones) {
            while ($fila = $resultadoPublicaciones->fetch_assoc()) {
                $publicaciones_asociadas[] = $fila;
            }
            $resultadoPublicaciones->free();
        }
        $stmtPublicaciones->close();
     } else {
         error_log("Error consultando publicaciones del tablón por categoría en ver_categoria.php: " . $_conexion->error);
     }
}

$_conexion->close(); // Cerrar la conexión a la base de datos al final

// Si no se especificó una categoría al principio
if (empty($_GET['cat'])) {
     $mensaje_error = "No se especificó una categoría para mostrar.";
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $categoria_valida ? htmlspecialchars($categoria_info['nombre']) . ' | Categorías | We-Connect' : 'Categoría no encontrada | We-Connect'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/styles.css">
     <link rel="stylesheet" href="../../css/categorias.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Puedes añadir o ajustar estilos específicos para mostrar los listados de productos, servicios, y publicaciones aquí o en el archivo css/categorias.css */
        .content-section {
            margin-top: 2.5rem; /* mt-10 */
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb; /* border-gray-200 */
        }
        .content-section:first-of-type { /* Usar first-of-type en lugar de first-child para no afectar el error message */
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }
        .content-item {
             border: 1px solid #e5e7eb; /* Tailwind gray-200 */
             border-radius: 0.375rem; /* rounded-md */
             padding: 1rem; /* p-4 */
             margin-bottom: 1rem; /* mb-4 */
             background-color: #f9fafb; /* Tailwind gray-50 */
             box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); /* shadow-sm */
        }
        .content-item h3 {
            font-weight: 600; /* font-semibold */
            color: #1f2937; /* gray-800 */
            margin-bottom: 0.5rem;
        }
         .content-item p {
            color: #374151; /* gray-700 */
            font-size: 0.875rem; /* text-sm */
         }
         .content-item a {
             color: #4F46E5; /* Tailwind indigo-600 */
             font-weight: 500; /* font-medium */
             font-size: 0.875rem; /* text-sm */
         }
         .content-item a:hover {
             text-decoration: underline;
         }
         .publicacion-meta {
             color: #6b7280; /* gray-500 */
             font-size: 0.75rem; /* text-xs */
             margin-top: 0.5rem;
         }
          .publicacion-meta strong {
             font-weight: 600;
             color: #4b5563; /* gray-600 */
          }

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
                 <a href="../recursos/recursos.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Recursos </a>
              


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
    echo '        <a href="index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categorias</a>'; // Mantener "Categorias" según tu código

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
    // Código para usuarios NO logueados
    // Rutas de login/registro relativas desde php/categoria-index.php
    echo '<a href="usuarios/login.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Iniciar Sesión</a>';
    echo '<a href="usuarios/registro.php" class="cta-button bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
}
?>
            </nav>
        </div>
    </header>


    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">

            <?php if ($categoria_valida): ?>
                <h1 class="text-3xl font-semibold text-gray-800 mb-4 text-center">Categoría: <?php echo htmlspecialchars($categoria_info['nombre']); ?></h1>
                 <?php if (!empty($categoria_info['descripcion'])): ?>
                     <p class="text-center text-gray-600 mb-8"><?php echo htmlspecialchars($categoria_info['descripcion']); ?></p>
                 <?php endif; ?>


                <?php if (empty($productos_asociados) && empty($servicios_asociados) && empty($publicaciones_asociadas)): ?>
                    <p class="text-center text-gray-600">Aún no hay productos, servicios o publicaciones del tablón en esta categoría.</p>
                <?php endif; ?>

                <?php if (!empty($productos_asociados)): ?>
                    <div class="content-section">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Productos (<?php echo count($productos_asociados); ?>)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($productos_asociados as $producto): ?>
                                 <div class="content-item">
                                    <h3 class="truncate"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 100)) . (strlen($producto['descripcion']) > 100 ? '...' : ''); ?></p>
                                    <p class="text-gray-600 text-sm mt-2">Precio: <?php echo htmlspecialchars($producto['precio']); ?>€</p>
                                     <a href="../productos/detalleProducto.php?id_producto=<?php echo htmlspecialchars($producto['id_producto']); ?>" class="mt-2 inline-block">Ver Producto</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($servicios_asociados)): ?>
                    <div class="content-section">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Servicios (<?php echo count($servicios_asociados); ?>)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($servicios_asociados as $servicio): ?>
                                 <div class="content-item">
                                    <h3 class="truncate"><?php echo htmlspecialchars($servicio['nombre']); ?></h3>
                                    <p><?php echo htmlspecialchars(substr($servicio['descripcion'], 0, 100)) . (strlen($servicio['descripcion']) > 100 ? '...' : ''); ?></p>
                                    <p class="text-gray-600 text-sm mt-2">Precio: <?php echo htmlspecialchars($servicio['precio']); ?>€</p>
                                     <a href="../servicios/detalleServicio.php?id_servicio=<?php echo htmlspecialchars($servicio['id_servicio']); ?>" class="mt-2 inline-block">Ver Servicio</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($publicaciones_asociadas)): ?>
                    <div class="content-section">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Publicaciones del Tablón (<?php echo count($publicaciones_asociadas); ?>)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($publicaciones_asociadas as $publicacion): ?>
                                 <div class="content-item">
                                    <h3 class="truncate"><?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
                                    <p class="text-gray-600 text-sm mb-2">Tipo: <span class="font-medium"><?php echo htmlspecialchars(ucfirst($publicacion['tipo'])); ?></span> | Por: <?php echo htmlspecialchars($publicacion['usuario_alias']); ?></p>
                                    <p><?php echo htmlspecialchars(substr($publicacion['descripcion'], 0, 100)) . (strlen($publicacion['descripcion']) > 100 ? '...' : ''); ?></p>
                                     <span class="publicacion-meta">Publicado el: <strong><?php echo htmlspecialchars(date("d/m/Y", strtotime($publicacion['fecha_publicacion']))); ?></strong></span>
                                     <?php // Asumo que tienes una página de detalle en php/comunidad/ ?>
                                    <a href="../comunidad/detallePublicacion.php?id=<?php echo htmlspecialchars($publicacion['id']); ?>" class="mt-2 inline-block">Ver Publicación</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-12">
                    <h1 class="text-3xl font-semibold text-gray-800 mb-4">Categoría no encontrada</h1>
                    <p class="text-gray-600 mb-8"><?php echo htmlspecialchars($mensaje_error ?? 'La categoría que buscas no existe.'); ?></p>
                    <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                         Ver todas las categorías
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