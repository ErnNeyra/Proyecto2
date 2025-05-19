<?php
// Página principal del Tablón de Necesidades y Ofertas con filtrado por categoría

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

    // --- Obtener la lista de categorías para el filtro ---
    $sqlCategoriasFiltro = "SELECT nombre FROM categoria ORDER BY nombre"; // Obtener solo nombres
    $resultadoCategoriasFiltro = $_conexion->query($sqlCategoriasFiltro);

    $categoriasFiltro = [];
    if ($resultadoCategoriasFiltro) {
        while ($fila = $resultadoCategoriasFiltro->fetch_assoc()) {
            $categoriasFiltro[] = $fila['nombre'];
        }
        $resultadoCategoriasFiltro->free();
    } else {
        error_log("Error al obtener categorías para el filtro del tablón: " . $_conexion->error);
        // No es un error crítico, la página se mostrará sin filtro de categorías
    }

    // --- Obtener la categoría seleccionada para filtrar (de la URL o del formulario) ---
    // Priorizar POST si se envía el formulario, si no, usar GET de la URL
    $filtroCategoria = depurar($_REQUEST['filter_cat'] ?? ''); // Usar $_REQUEST para GET o POST

    // Validar si la categoría recibida en el filtro es una categoría válida
    $categoria_filtro_valida = false;
    if (empty($filtroCategoria)) {
        $categoria_filtro_valida = true; // No filtrar es válido
    } elseif (in_array($filtroCategoria, $categoriasFiltro)) {
        $categoria_filtro_valida = true; // La categoría existe en la lista
    } else {
        // Categoría de filtro inválida, ignorarla
        $filtroCategoria = '';
        $mensaje_error_filtro = "La categoría de filtro seleccionada no es válida.";
    }


    // --- Construir la consulta SQL para obtener publicaciones ---
    $sqlPublicaciones = "SELECT id, tipo, titulo, descripcion, usuario_alias, fecha_publicacion, categoria_nombre FROM necesidades_ofertas"; // Seleccionar categoria_nombre

    $parametros = []; // Array para bind_param
    $tipos = ""; // String para bind_param (ej: "s")

    if (!empty($filtroCategoria) && $categoria_filtro_valida) {
        // Añadir cláusula WHERE si hay un filtro de categoría válido
        $sqlPublicaciones .= " WHERE categoria_nombre = ?";
        $parametros[] = $filtroCategoria;
        $tipos .= "s";
    }

    // Añadir ordenación
    $sqlPublicaciones .= " ORDER BY fecha_publicacion DESC";


    // --- Ejecutar la consulta ---
    $publicaciones = [];
    if (!empty($mensaje_error_filtro)) {
        // No hacer consulta si el filtro es inválido
    } elseif ($_conexion) { // Asegurarse de que la conexión existe
        $stmtPublicaciones = $_conexion->prepare($sqlPublicaciones);

        if ($stmtPublicaciones === false) {
            error_log("Error preparando consulta SELECT necesidades_ofertas en tablon.php: " . $_conexion->error);
            // Manejar error de preparación
            $mensaje_error_bd = "Error al cargar las publicaciones.";
        } else {
            if (!empty($parametros)) {
                // Si hay parámetros, enlazar
                $stmtPublicaciones->bind_param($tipos, ...$parametros);
            }

            if ($stmtPublicaciones->execute()) {
                $resultadoPublicaciones = $stmtPublicaciones->get_result();
                if ($resultadoPublicaciones) {
                    while ($fila = $resultadoPublicaciones->fetch_assoc()) {
                        $publicaciones[] = $fila; // Guarda todos los datos, incluida categoria_nombre
                    }
                    $resultadoPublicaciones->free();
                }
            } else {
                error_log("Error ejecutando consulta SELECT necesidades_ofertas en tablon.php: " . $stmtPublicaciones->error);
                $mensaje_error_bd = "Error al cargar las publicaciones.";
            }
            $stmtPublicaciones->close();
        }
    } else {
        $mensaje_error_bd = "No se pudo conectar a la base de datos.";
        error_log("Error de conexión a BD en tablon.php");
    }


    $_conexion->close(); // Cerrar la conexión a la base de datos al final

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablón Comunidad | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/tablon.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../util/img/.faviconWC.png " type="image/x-icon">
    <!-- favicon -->
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
                        echo '        <a href="tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                        // Enlace a la página principal de Categorías (asumimos que la página de listado está en php/categoria/index.php)
                        echo '        <a href="../categoria/index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categorias</a>'; // Mantener "Categorias" según tu código

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

            <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Tablón de Necesidades </h1>

             <?php if (!empty($mensaje_error_bd)): ?>
                 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                     <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error_bd); ?></span>
                 </div>
             <?php endif; ?>

             <?php if (!empty($mensaje_error_filtro)): ?>
                 <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                     <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error_filtro); ?></span>
                 </div>
             <?php endif; ?>


            <?php if (isset($_SESSION['usuario'])): ?>
                <div class="text-center mb-8">
                    <a href="publicar.php" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Publicar Nueva Necesidad <i class="fas fa-plus ml-1"></i>
                    </a>
                </div>
             <?php endif; ?>

             <?php if (!empty($categoriasFiltro) || !empty($filtroCategoria)): // Mostrar filtro si hay categorías o ya hay un filtro activo ?>
                 <div class="mb-8 p-4 bg-gray-50 rounded-md shadow-sm flex flex-col md:flex-row items-center justify-between">
                      <form action="tablon.php" method="get" class="w-full md:w-auto flex items-center">
                           <label for="filter_cat" class="block text-gray-700 text-sm font-bold mr-2">Filtrar por Categoría:</label>
                           <select name="filter_cat" id="filter_cat" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline md:w-auto">
                               <option value="">Todas las Categorías</option>
                               <?php
                                   $selectedFiltro = htmlspecialchars($filtroCategoria);
                                   foreach ($categoriasFiltro as $cat) {
                                       $isSelected = ($cat === $selectedFiltro) ? 'selected' : '';
                                       echo '<option value="' . htmlspecialchars($cat) . '" ' . $isSelected . '>' . htmlspecialchars($cat) . '</option>';
                                   }
                               ?>
                           </select>
                           <button type="submit" class="ml-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                               Filtrar
                           </button>
                      </form>
                      <?php if (!empty($filtroCategoria)): // Opción para quitar filtro si hay uno ?>
                           <div class="mt-4 md:mt-0 md:ml-4">
                               <a href="tablon.php" class="text-sm text-gray-600 hover:text-gray-800">Quitar Filtro</a>
                           </div>
                      <?php endif; ?>
                 </div>
             <?php endif; ?>


            <?php if (!empty($publicaciones)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($publicaciones as $publicacion): ?>
                        <div class="publicacion-card">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($publicacion['titulo']); ?></h3>
                            <p class="text-gray-700 text-sm mb-3"><?php echo htmlspecialchars($publicacion['descripcion']); ?></p>
                             <div class="meta">
                                 <span>Tipo: <strong><?php echo htmlspecialchars(ucfirst($publicacion['tipo'])); ?></strong></span> |
                                 <span>Por: <strong><?php echo htmlspecialchars($publicacion['usuario_alias']); ?></strong></span> |
                                 <?php if (!empty($publicacion['categoria_nombre'])): ?>
                                     <span>Categoría: <strong><?php echo htmlspecialchars($publicacion['categoria_nombre']); ?></strong></span> |
                                 <?php endif; ?>
                                 <span>Publicado el: <strong><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($publicacion['fecha_publicacion']))); ?></strong></span>
                             </div>

                             <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['usuario'] === $publicacion['usuario_alias']): ?>
                                   <a href="editarPublicacion.php?id=<?php echo htmlspecialchars($publicacion['id']); ?>" class="detalle-link mt-3 inline-block text-blue-600 hover:text-blue-800">Editar</a>
                              <?php endif; ?>

                             </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (empty($mensaje_error_bd)): // Mostrar mensaje si no hay publicaciones y no hubo error de BD ?>
                <p class="text-center text-gray-600">No hay publicaciones disponibles en el tablón
                     <?php if (!empty($filtroCategoria)): echo "para la categoría '" . htmlspecialchars($filtroCategoria) . "'"; endif; ?>
                     todavía.</p>
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