<?php
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

    $aliasUsuarioActual = htmlspecialchars($_SESSION['usuario']['usuario']);
    $mensajeExito = "";
    $mensajeError = "";

    // --- Cargar categorías desde la base de datos ---
    $sqlCategorias = "SELECT nombre FROM categoria ORDER BY nombre"; // Ordenar alfabéticamente
    $resultadoCategorias = $_conexion->query($sqlCategorias);

    $categorias = [];
    if ($resultadoCategorias) {
        while ($fila = $resultadoCategorias->fetch_assoc()) {
            $categorias[] = $fila['nombre'];
        }
        $resultadoCategorias->free(); // Liberar memoria
    } else {
        // Manejar error si la consulta de categorías falla
        $mensajeError = "Error al cargar las categorías: " . $_conexion->error;
        error_log("Error al obtener categorías para el formulario de publicar: " . $_conexion->error);
    }


    // Procesar el formulario si se envió
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($mensajeError)) { // Procesar solo si no hubo error cargando categorías
        // Validar y obtener los datos del formulario
        $tipo = 'necesidad'; // Valor fijo como definimos antes para este formulario
        $titulo = depurar($_POST['titulo'] ?? '');
        $descripcion = depurar($_POST['descripcion'] ?? '');
        // Se elimina la lectura de $_POST['categoria_b2b']
        $categoria_seleccionada = depurar($_POST['categoria_principal'] ?? ''); // Obtener la categoría principal seleccionada

        // Validaciones básicas (solo título, descripción y categoría principal son obligatorios)
        if (empty($titulo) || empty($descripcion)) {
            $mensajeError = "Por favor, completa todos los campos obligatorios (Título, Descripción).";
        } elseif (empty($categoria_seleccionada)) {
            $mensajeError = "Por favor, selecciona una categoría principal.";
        } else {
            // Validar que la categoría seleccionada existe en nuestra lista cargada (seguridad)
            if (!in_array($categoria_seleccionada, $categorias)) {
                $mensajeError = "La categoría seleccionada no es válida.";
            } else {
                // Si la categoría seleccionada es válida, la guardamos en la base de datos
                // Usamos la variable $categoria_seleccionada para el campo categoria_nombre

                // Insertar en la base de datos
                // LA CONSULTA INSERT YA NO INCLUYE categoria_b2b
                $stmt = $_conexion->prepare("INSERT INTO necesidades_ofertas (tipo, titulo, descripcion, usuario_alias, categoria_nombre) VALUES (?, ?, ?, ?, ?)");

                // Tipos de bind_param: s (tipo), s (titulo), s (descripcion), s (usuario_alias), s (categoria_nombre)
                $stmt->bind_param("sssss", $tipo, $titulo, $descripcion, $aliasUsuarioActual, $categoria_seleccionada);


                if ($stmt->execute()) {
                    $mensajeExito = "¡Necesidad publicada con éxito! Redirigiendo al tablón...";
                    // Redirigir al tablón después de un breve retraso (opcional, o redirigir inmediatamente)
                    // Ruta relativa al tablón desde php/comunidad/
                    header("Refresh: 3; url=tablon.php"); // Redirige a tablon.php después de 3 segundos
                    // O redirección inmediata:
                    // header("Location: tablon.php");
                    // exit();

                } else {
                    $mensajeError = "Error al crear la publicación: " . $_conexion->error;
                    error_log("Error INSERT en necesidades_ofertas por usuario $aliasUsuarioActual: " . $_conexion->error); // Log el error
                }

                $stmt->close();
            }
        }
        // Si hubo error, los valores del formulario se mantendrán gracias al htmlspecialchars($_POST['...'])
    }

    $_conexion->close(); // Cerrar la conexión a la base de datos al final
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Necesidad | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/nuevo.css">
    <link rel="stylesheet" href="../../css/publicar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
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


if (isset($_SESSION['usuario'])) {
    // Obtener datos del usuario de la sesión, usando htmlspecialchars por seguridad
    $nombreUsuario = htmlspecialchars($_SESSION['usuario']['usuario']); // Usamos 'usuario'

    // Determinar la ruta de la imagen de perfil
    // Ruta por defecto desde php/categoria-index.php a php/util/
    $imagenPerfil = 'php/util/img/usuario.png'; // Ruta por defecto desde php/

    // Verificamos si existe la foto de perfil del usuario en la sesión y no está vacía
    if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
        // La ruta guardada en BD es relativa a 'util/', así que desde php/categoria-index.php es 'util/' + ruta_bd
        $rutaImagenBD = '' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
       
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
    <?php /* Fin Código del Header */ ?>


    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="form-container-publicar bg-white rounded-lg shadow-md p-8 max-w-xl mx-auto">

            <h1 class="form-page-title text-2xl font-semibold text-gray-800 mb-6 text-center">Publicar una Necesidad</h1>

            <?php
                // Mostrar mensajes de éxito o error
                if ($mensajeExito) {
                    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">';
                    echo '  <span class="block sm:inline">' . $mensajeExito . '</span>';
                    echo '</div>';
                }
                if ($mensajeError) {
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
                    echo '  <span class="block sm:inline">' . $mensajeError . '</span>';
                    echo '</div>';
                }
            ?>

            <form action="publicar.php" method="POST">
                <input type="hidden" name="tipo" value="necesidad">

                <div class="mb-4">
                    <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">Título de la Necesidad:</label>
                    <input type="text" name="titulo" id="titulo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo htmlspecialchars($_POST['titulo'] ?? ''); ?>" required>
                </div>

                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Describe tu Necesidad:</label>
                    <textarea name="descripcion" id="descripcion" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                </div>

                 <div class="mb-6">
                     <label for="categoria_principal" class="block text-gray-700 text-sm font-bold mb-2">Categoría Principal:</label>
                     <select name="categoria_principal" id="categoria_principal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                         <option value="">-- Selecciona una Categoría --</option>
                         <?php
                             $selectedCategoria = htmlspecialchars($_POST['categoria_principal'] ?? '');
                             foreach ($categorias as $cat) {
                                 $isSelected = ($cat === $selectedCategoria) ? 'selected' : '';
                                 echo '<option value="' . htmlspecialchars($cat) . '" ' . $isSelected . '>' . htmlspecialchars($cat) . '</option>';
                             }
                         ?>
                     </select>
                     <?php if (!empty($mensajeError) && strpos($mensajeError, 'categoría principal') !== false) echo "<p class='text-red-500 text-xs italic mt-1'>Por favor, selecciona una categoría principal válida.</p>"; ?>
                 </div>
                 <div class="flex items-center justify-between">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Publicar Necesidad
                    </button>
                    <a href="tablon.php" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
                        Cancelar
                    </a>
                </div>
            </form>

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
                        <li><a href="../recursos/recursos.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Recursos Emprendedores</a></li>
                        <li><a href="tablon.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Tablón Comunidad</a></li>
                         <li><a href="../mensajes/conversaciones.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Mis Mensajes</a></li>

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