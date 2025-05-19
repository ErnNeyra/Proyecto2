<?php
// Página para editar una publicación del Tablón existente
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

    $aliasUsuarioActual = htmlspecialchars($_SESSION["usuario"]["usuario"]);

    $idPublicacion = $_GET['id'] ?? null; // Obtener el ID de la publicación de la URL
    $publicacion = null; // Para almacenar los datos de la publicación a editar

    $errorTitulo = $errorDescripcion = $errorCategoriaPrincipal = $errorCategoriaB2B = $error = ""; // Errores específicos
    $mensaje_error_carga = ""; // Error si no se puede cargar la publicación
    $success = "";

    // --- Cargar la publicación existente para editar ---
    if ($idPublicacion === null || !is_numeric($idPublicacion)) {
        $mensaje_error_carga = "ID de publicación no especificado o inválido.";
    } else {
        $idPublicacion = (int)$idPublicacion;

        // Seleccionar la publicación y verificar que pertenece al usuario logueado
        // Incluimos categoria_nombre por si quieres mostrarlo (aunque se edita con el selector principal)
        // Incluimos categoria_b2b por si decides re-añadirlo o mostrar su valor anterior
        $sqlPublicacion = "SELECT id, tipo, titulo, descripcion, usuario_alias, fecha_publicacion, categoria_nombre, categoria_b2b FROM necesidades_ofertas WHERE id = ? AND usuario_alias = ?";
        $stmtPublicacion = $_conexion->prepare($sqlPublicacion);

        if ($stmtPublicacion === false) {
            $mensaje_error_carga = "Error preparando la consulta para cargar la publicación.";
            error_log("Error preparando consulta SELECT necesidades_ofertas en editarPublicacion.php: " . $_conexion->error);
        } else {
            $stmtPublicacion->bind_param("is", $idPublicacion, $_SESSION['usuario']['usuario']);
            $stmtPublicacion->execute();
            $resultadoPublicacion = $stmtPublicacion->get_result();

            if ($resultadoPublicacion->num_rows === 0) {
                $mensaje_error_carga = "Publicación no encontrada o no tienes permiso para editarla.";
            } else {
                $publicacion = $resultadoPublicacion->fetch_assoc(); // Datos de la publicación cargada
                // Limpiar datos para mostrar en el formulario
                $publicacion['titulo'] = htmlspecialchars($publicacion['titulo']);
                $publicacion['descripcion'] = htmlspecialchars($publicacion['descripcion']);
                // categoria_b2b se mantiene por si acaso, si la eliminaste de BD ya no se seleccionará
                $publicacion['categoria_b2b'] = htmlspecialchars($publicacion['categoria_b2b'] ?? '');
                $publicacion['categoria_nombre'] = htmlspecialchars($publicacion['categoria_nombre'] ?? ''); // Categoría principal actual

            }
            $stmtPublicacion->close();
        }
    }

    // --- Cargar categorías para el formulario ---
    // Solo necesitamos cargar las categorías si la publicación se cargó correctamente
    $categorias = [];
    if ($publicacion) {
        $sqlCategorias = "SELECT nombre FROM categoria ORDER BY nombre"; // Ordenar alfabéticamente
        $resultadoCategorias = $_conexion->query($sqlCategorias);

        if ($resultadoCategorias) {
            while ($fila = $resultadoCategorias->fetch_assoc()) {
                $categorias[] = $fila['nombre'];
            }
            $resultadoCategorias->free(); // Liberar memoria
        } else {
            $mensaje_error_carga = "Error al cargar las categorías para el formulario.";
            error_log("Error al obtener categorías para el formulario de editar publicacion: " . $_conexion->error);
        }
    }


    // --- Procesar el formulario si se envió (POST) ---
    if ($_SERVER["REQUEST_METHOD"] == "POST" && $publicacion && empty($mensaje_error_carga) && !empty($categorias)) {
        // Validar y obtener los datos del formulario enviado
        // Usar valor POST si existe, si no, el cargado
        $tmpTitulo = depurar($_POST['titulo'] ?? $publicacion['titulo']);
        if($tmpTitulo == "") { $errorTitulo = "El título es obligatorio"; } elseif(strlen($tmpTitulo) < 3 || strlen($tmpTitulo) > 100) { $errorTitulo = "El título debe tener entre 3 y 100 caracteres"; } else { $titulo = $tmpTitulo; }

        $tmpDescripcion = depurar($_POST['descripcion'] ?? $publicacion['descripcion']);
        if($tmpDescripcion == "") { $errorDescripcion = "La descripción es obligatoria"; } elseif(strlen($tmpDescripcion) < 10 || strlen($tmpDescripcion) > 500) { $errorDescripcion = "La descripción debe tener entre 10 y 500 caracteres"; } else { $descripcion = $tmpDescripcion; }

        // Manejar categoria_b2b si decides mantenerla (aunque ya la eliminamos de la BD)
        // Si la eliminaste, esta parte no hará nada útil.
        $categoria_b2b = depurar($_POST['categoria_b2b'] ?? $publicacion['categoria_b2b']);


        // Validar la categoría principal seleccionada
        $categoria_principal_seleccionada = depurar($_POST['categoria_principal'] ?? $publicacion['categoria_nombre']); // Usar valor POST si existe, si no, el cargado
        if (empty($categoria_principal_seleccionada)) {
            $errorCategoriaPrincipal = "Por favor, selecciona una categoría principal.";
        } else {
            // Validar que la categoría seleccionada existe en nuestra lista cargada (seguridad)
            if (!in_array($categoria_principal_seleccionada, $categorias)) {
                $errorCategoriaPrincipal = "La categoría principal seleccionada no es válida.";
            } else {
                // Si la categoría seleccionada es válida
                $categoria_principal_para_db = $categoria_principal_seleccionada;
            }
        }


        // Si no hay errores de validación, proceder con la actualización
        if(
            empty($errorTitulo) &&
            empty($errorDescripcion) &&
            empty($errorCategoriaPrincipal) && // Validar error de categoría principal
            empty($errorCategoriaB2B) // Validar error de categoria_b2b si aplicara
        ) {
            // Consulta UPDATE ahora incluye la columna categoria_nombre
            // Si mantuviste categoria_b2b en la BD, también la incluirías aquí
            // Si la eliminaste, la consulta es más simple
            $sql = $_conexion->prepare("UPDATE necesidades_ofertas SET titulo = ?, descripcion = ?, categoria_nombre = ? WHERE id = ? AND usuario_alias = ?");
            // Tipos de bind_param: s (titulo), s (descripcion), s (categoria_nombre), i (id), s (usuario_alias)
            $sql->bind_param("sssis", $titulo, $descripcion, $categoria_principal_para_db, $idPublicacion, $aliasUsuarioActual);


            if ($sql->execute()) {
                $success = "Publicación actualizada con éxito.";
                // Opcional: Recargar los datos de la publicación después de actualizar para mostrar los cambios
                $publicacion['titulo'] = $titulo;
                $publicacion['descripcion'] = $descripcion;
                $publicacion['categoria_nombre'] = $categoria_principal_para_db;
                // $publicacion['categoria_b2b'] = $categoria_b2b; // Si la mantienes

                // header("Location: detallePublicacion.php?id=" . $idPublicacion); // Redirigir al detalle
                // exit();

            } else {
                $error = "Error al actualizar la publicación: " . $_conexion->error;
                error_log("Error UPDATE necesidades_ofertas ID $idPublicacion por usuario $aliasUsuarioActual: " . $_conexion->error);
            }
            $sql->close(); // Cerrar el statement
        }
    }

    $_conexion->close(); // Cerrar la conexión a la base de datos al final

    // Si no se pudo cargar la publicación inicialmente, mostrar mensaje de error y salir
    if (!$publicacion && !empty($mensaje_error_carga)) {
        // Mostramos el header y footer antes de salir
?>
     <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error al cargar Publicación | We-Connect</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
             <link rel="stylesheet" href="../../css/index.css">
            <link rel="stylesheet" href="../../css/styles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                          <a href="../recursos/recursos.php" class="main-nav-link text-gray-700 hover:text-black mr-4 font-semibold">Recursos </a>
                       

                        <?php
                            if(isset($_SESSION["usuario"]["usuario"])){
                                $aliasUsuarioActualHeader = htmlspecialchars($_SESSION['usuario']['usuario']);
                                $imagenPerfil = '../util/img/usuario.jpg';
                                if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                                     $rutaImagen = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                                     if (file_exists($rutaImagen)) { $imagenPerfil = $rutaImagen; }
                                }
                                echo '<div class="relative"><button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none"><img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . htmlspecialchars($imagenPerfil) . '" alt="Imagen de Perfil"><span>' . $aliasUsuarioActualHeader . '</span><svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button><div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden"><a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Mi Panel</a><a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Editar Perfil</a><hr class="border-gray-200"><a href="../productos/gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Gestionar Productos</a><a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Gestionar Servicios</a><hr class="border-gray-200"><a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Tablón Comunidad</a><a href="../mensajes/conversaciones.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 font-semibold">Mis Mensajes</a><hr class="border-gray-200"><a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Cerrar Sesión</a></div></div>';
                            } else {
                                echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                                echo '<a href="../usuarios/registro.php" class="ml-4 bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600">Regístrate</a>';
                            }
                        ?>
                    </nav>
                </div>
            </header>
            <main class="container mx-auto py-12 px-6 flex-grow text-center">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative inline-block" role="alert">
                     <strong class="font-bold">Error:</strong>
                     <span class="block sm:inline"><?php echo htmlspecialchars($mensaje_error_carga); ?></span>
                </div>
                 <div class="mt-8">
                      <a href="tablon.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                           Volver al Tablón
                      </a>
                 </div>
            </main>
            <footer class="bg-gray-800 text-white py-8">
                <div class="container mx-auto px-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg::grid-cols-4 gap-8">
                        <div class="footer-section"><h3 class="text-lg font-semibold mb-4 text-white">Acerca de We-Connect</h3><p class="text-gray-300">We-Connect es una plataforma que conecta a compradores y vendedores de productos y servicios, facilitando el comercio y las oportunidades de negocio.</p></div>
                        <div class="footer-section"><h3 class="text-lg font-semibold mb-4 text-white">Enlaces Útiles</h3><ul class="list-none p-0"><li><a href="../../index.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Inicio</a></li><li><a href="../productos/producto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Productos</a></li><li><a href="../servicios/servicio.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Servicios</a></li><li><a href="../contacto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Contacto</a></li><li><a href="../recursos/recursos.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Recursos Emprendedores</a></li><li><a href="tablon.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Tablón Comunidad</a></li><li><a href="../categoria/index.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Categorías</a></li><li><a href="../mensajes/conversaciones.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 font-semibold">Mis Mensajes</a></li></ul></div>
                        <div class="footer-section"><h3 class="text-lg font-semibold mb-4 text-white">Soporte</h3><ul class="list-none p-0"><li><a href="../terminos/faq.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas Frecuentes</a></li><li><a href="../terminos/terms.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos de Servicio</a></li><li><a href="../terminos/privacy.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política de Privacidad</a></li><li><a href="../contacto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a></li></ul></div>
                        <div class="footer-section social-icons-footer"><h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3><div class="flex justify-center md:justify-start space-x-4 text-xl"><a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-facebook-f"></i></a><a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-twitter-f"></i></a><a href="#" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i class="fab fa-linkedin-in"></i></a></div></div>
                    </div>
                    <div class="copyright border-t border-gray-700 pt-8 text-gray-500 text-sm text-center"><p>© <?php echo date('Y'); ?> We-Connect. Todos los derechos reservados.</p></div>
                </div>
            </footer>
             <script src="../../js/desplegable.js"></script>
             <script src="../../js/script2.js"></script>
        </body>
        </html>
     <?php
     exit(); // Salir después de mostrar el error
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $publicacion ? 'Editar Publicación: ' . $publicacion['titulo'] : 'Editar Publicación | We-Connect'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/styles.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center"><?php echo $publicacion ? 'Editar Publicación' : 'Publicación no encontrada'; ?></h1>

             <?php // Mostrar errores de validación/actualización
             if (!empty($errorTitulo) || !empty($errorDescripcion) || !empty($errorCategoriaPrincipal) || !empty($errorCategoriaB2B) || !empty($error) ): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error de validación o actualización:</strong>
                    <span class="block sm:inline">
                         <?php
                             echo $errorTitulo . " ";
                             echo $errorDescripcion . " ";
                             echo $errorCategoriaPrincipal . " ";
                             echo $errorCategoriaB2B . " "; // Mostrar error de categoria_b2b si existe
                             echo $error;
                         ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Éxito:</strong>
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($publicacion && !empty($categorias)): // Mostrar el formulario solo si se cargó la publicación y hay categorías ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($publicacion['id']); ?>">

                    <div class="mb-4">
                        <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
                        <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($_POST['titulo'] ?? $publicacion['titulo']); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php if (!empty($errorTitulo)) echo "<p class='text-red-500 text-xs italic mt-1'>$errorTitulo</p>"; ?>
                    </div>
                    <div class="mb-4">
                        <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?php echo htmlspecialchars($_POST['descripcion'] ?? $publicacion['descripcion']); ?></textarea>
                        <?php if (!empty($errorDescripcion)) echo "<p class='text-red-500 text-xs italic mt-1'>$errorDescripcion</p>"; ?>
                    </div>

                     <div class="mb-4">
                         <label for="categoria_principal" class="block text-gray-700 text-sm font-bold mb-2">Categoría Principal:</label>
                         <select name="categoria_principal" id="categoria_principal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                             <option value="">-- Selecciona una Categoría --</option>
                             <?php
                                 // Usar valor POST si existe (en caso de error), si no, el valor cargado de la BD
                                 $selectedCategoria = htmlspecialchars($_POST['categoria_principal'] ?? $publicacion['categoria_nombre']);
                                 foreach ($categorias as $cat) {
                                     $isSelected = ($cat === $selectedCategoria) ? 'selected' : '';
                                     echo '<option value="' . htmlspecialchars($cat) . '" ' . $isSelected . '>' . htmlspecialchars($cat) . '</option>';
                                 }
                             ?>
                         </select>
                         <?php if (!empty($errorCategoriaPrincipal)) echo "<p class='text-red-500 text-xs italic mt-1'>$errorCategoriaPrincipal</p>"; ?>
                     </div>
                     <?php if (isset($publicacion['categoria_b2b'])): // Check if the column exists in the loaded data ?>
                         <div class="mb-6">
                             <label for="categoria_b2b" class="block text-gray-700 text-sm font-bold mb-2">Categoría B2B Específica (Opcional):</label>
                             <input type="text" name="categoria_b2b" id="categoria_b2b" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($_POST['categoria_b2b'] ?? $publicacion['categoria_b2b']); ?>">
                             <?php if (!empty($errorCategoriaB2B)) echo "<p class='text-red-500 text-xs italic mt-1'>$errorCategoriaB2B</p>"; ?>
                         </div>
                     <?php endif; ?>
                     <div class="flex items-center justify-between">
                        <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Guardar Cambios</button>
                         <a href="tablon.php" class="inline-block align-baseline font-semibold text-sm text-gray-600 hover:text-gray-800">Cancelar</a>
                    </div>
                </form>
             <?php elseif (!empty($mensaje_error_carga)): // Mostrar mensaje de error de carga si no hay formulario ?>
                 <?php elseif ($publicacion && empty($categorias)): // Publicación cargada pero sin categorías -->
                 ?>
                  <p class="text-center text-gray-600">No se pudieron cargar las categorías disponibles para editar.</p>
                   <div class="mt-8 text-center">
                       <a href="tablon.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Volver al Tablón
                       </a>
                  </div>
             <?php endif; // Fin if ($publicacion) ?>

        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg::grid-cols-4 gap-8">
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