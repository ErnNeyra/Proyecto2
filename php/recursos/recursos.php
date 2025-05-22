<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
     <link rel="stylesheet" href="../../css/recursos.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="../util/img/faviconWC.png " type="image/x-icon">
    <!-- favicon -->
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
    ?>
</head>

<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="main-header shadow-md sticky top-0 z-50">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="../productos/producto.php"
                    class="main-nav-link text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="../servicios/servicio.php"
                    class="main-nav-link text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="../contacto.php" class="main-nav-link text-gray-700 hover:text-black mr-4">Contacto</a>

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

    <section class="resources-hero py-20 md:py-32 flex items-center text-center">
        <div class="container mx-auto px-6 relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Recursos para Emprendedores</h1>
            <p class="text-lg md:text-xl max-w-3xl mx-auto">
                Encuentra aquí herramientas, formación y conéctate con otros emprendedores en nuestro tablón de
                colaboración.
            </p>
        </div>
    </section>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">

            <p class="text-gray-700 mb-8 text-center max-w-2xl mx-auto">
                Esta sección te proporciona acceso a recursos valiosos para tu negocio, así como un espacio para
                interactuar y colaborar con otros miembros de la comunidad We-Connect.
            </p>

            <div class="mb-10">
                <h2 class="resources-section-title text-2xl font-semibold text-gray-800 mb-4">Cursos y Formación Online
                </h2>
                <p class="text-gray-600 mb-6">Aprende nuevas habilidades y mejora tu conocimiento con cursos gratuitos y
                    recursos de plataformas educativas:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="resource-card">
                        <i class="fas fa-graduation-cap resource-icon text-xl"></i>
                        <a href="https://www.coursera.org/courses?query=business" target="_blank" rel="noopener">Cursos
                            de Negocios en Coursera</a>
                        <p class="text-gray-500 text-sm mt-1">Accede a cursos de universidades y empresas líderes.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-book-open resource-icon text-xl"></i>
                        <a href="https://www.edx.org/learn/entrepreneurship" target="_blank" rel="noopener">Programas de
                            Emprendimiento en edX</a>
                        <p class="text-gray-500 text-sm mt-1">Explora programas de las mejores instituciones.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-video resource-icon text-xl"></i>
                        <a href="https://www.udemy.com/courses/business/" target="_blank" rel="noopener">Cursos de
                            Negocios en Udemy</a>
                        <p class="text-gray-500 text-sm mt-1">Amplia variedad de cursos, busca opciones gratuitas.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fab fa-youtube resource-icon text-xl"></i>
                        <a href="https://creatoracademy.youtube.com/landing" target="_blank" rel="noopener">YouTube
                            Creator Academy</a>
                        <p class="text-gray-500 text-sm mt-1">Ideal para mejorar tu estrategia de contenido en video.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="resources-section-title text-2xl font-semibold text-gray-800 mb-4">Herramientas de Gestión y
                    Plantillas</h2>
                <p class="text-gray-600 mb-6">Optimiza la gestión de tu negocio con herramientas y plantillas útiles:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="resource-card">
                        <i class="fas fa-file-excel resource-icon text-xl"></i>
                        <a href="https://docs.google.com/spreadsheets/u/0/" target="_blank" rel="noopener">Google
                            Sheets</a>
                        <p class="text-gray-500 text-sm mt-1">Para contabilidad básica, inventario, y más.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-paint-brush resource-icon text-xl"></i>
                        <a href="https://www.canva.com/es/" target="_blank" rel="noopener">Canva</a>
                        <p class="text-gray-500 text-sm mt-1">Diseño gráfico sencillo para marketing.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-tasks resource-icon text-xl"></i>
                        <a href="https://trello.com/es" target="_blank" rel="noopener">Trello</a>
                        <p class="text-gray-500 text-sm mt-1">Gestión visual de proyectos y tareas.</p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-clipboard-list resource-icon text-xl"></i>
                        <a href="https://asana.com/es" target="_blank" rel="noopener">Asana</a>
                        <p class="text-gray-500 text-sm mt-1">Plataforma robusta para gestión de proyectos en equipo.
                        </p>
                    </div>
                    <div class="resource-card">
                        <i class="fas fa-file-alt resource-icon text-xl"></i>
                        <a href="https://www.emprendepyme.net/plantilla-de-plan-de-negocio.html" target="_blank"
                            rel="noopener">Plantilla de Plan de Negocio</a>
                        <p class="text-gray-500 text-sm mt-1">Estructura tu idea de negocio con esta plantilla externa.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="resources-section-title text-2xl font-semibold text-gray-800 mb-4">Herramientas con IA para
                    Emprendedores</h2>
                <p class="text-gray-600 mb-6">Accede a herramientas inteligentes para potenciar tu emprendimiento:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="resource-card flex flex-col items-center justify-between">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-lightbulb resource-icon text-2xl mb-2 text-yellow-500"></i>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 text-center">Generar Valor Agregado a un
                                Producto</h3>
                            <p class="text-gray-500 text-sm mb-3 text-center">Descubre ideas para diferenciar y mejorar
                                tu producto con ayuda de IA.</p>
                        </div>
                        <button
                            class="bg-yellow-500 text-black px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold mt-2"
                            onclick="document.getElementById('form-valor-agregado').classList.toggle('hidden')"
                            type="button">Obtener</button>
                        <form id="form-valor-agregado"
                            class="w-full mt-4 bg-orange-50 p-4 rounded-md shadow-inner hidden">
                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="nombre-producto">Nombre
                                del producto</label>
                            <input type="text" id="nombre-producto" name="nombre_producto"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="Ejemplo: Taza personalizada" required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="descripcion-producto">Descripción breve</label>
                            <textarea id="descripcion-producto" name="descripcion_producto"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
                                rows="2" placeholder="¿Qué hace tu producto? ¿Para quién es?" required></textarea>

                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="mercado-objetivo">Mercado
                                objetivo</label>
                            <input type="text" id="mercado-objetivo" name="mercado_objetivo"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="Ejemplo: Jóvenes universitarios, empresas, etc." required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="problema">Problema que
                                resuelve</label>
                            <input type="text" id="problema" name="problema"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="¿Qué necesidad o problema atiende?" required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="diferenciadores">¿Qué lo
                                hace diferente actualmente?</label>
                            <input type="text" id="diferenciadores" name="diferenciadores"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-orange-400"
                                placeholder="Opcional">

                            <button type="button" id="btn-generar-valor-agregado"
                                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold w-full mt-2">Generar</button>
                            <div id="respuesta-valor-agregado" class="mt-4 text-gray-800 text-sm whitespace-pre-line">
                            </div>
                            <div id="acciones-valor-agregado" class="flex flex-col md:flex-row gap-2 mt-2 hidden">
                                <button type="button" id="btn-reintentar-valor-agregado"
                                    class="bg-red-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>No me gusta, generar otro</button>
                                <button type="button" id="btn-copiar-valor-agregado"
                                    class="bg-green-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>Me gusta, copiar texto!</button>
                            </div>
                        </form>
                    </div>
                    <div class="resource-card flex flex-col items-center justify-between">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-bullhorn resource-icon text-2xl mb-2 text-green-500"></i>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 text-center">Generar Plan de Marketing
                            </h3>
                            <p class="text-gray-500 text-sm mb-3 text-center">Crea un plan de marketing personalizado
                                para tu negocio en minutos.</p>
                        </div>
                        <button
                            class="bg-yellow-500 text-black px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold mt-2"
                            onclick="document.getElementById('form-plan-marketing').classList.toggle('hidden')"
                            type="button">Obtener</button>
                        <form id="form-plan-marketing" class="w-full mt-4 p-4 rounded-md shadow-inner hidden">
                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="pm-nombre-negocio">Nombre
                                del negocio o producto</label>
                            <input type="text" id="pm-nombre-negocio" name="nombre_negocio"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                                placeholder="Ejemplo: Café Aroma" required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="pm-descripcion">Descripción breve</label>
                            <textarea id="pm-descripcion" name="descripcion"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                                rows="2" placeholder="¿Qué ofreces? ¿Qué te hace especial?" required></textarea>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="pm-publico-objetivo">Público objetivo</label>
                            <input type="text" id="pm-publico-objetivo" name="publico_objetivo"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                                placeholder="Ejemplo: Jóvenes, empresas, familias, etc." required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="pm-objetivo-marketing">Objetivo principal del marketing</label>
                            <input type="text" id="pm-objetivo-marketing" name="objetivo_marketing"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                                placeholder="Ejemplo: Aumentar ventas, posicionar marca, etc." required>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="pm-presupuesto">Presupuesto estimado (opcional)</label>
                            <input type="text" id="pm-presupuesto" name="presupuesto"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                                placeholder="Ejemplo: $5000, Bajo, Medio, Alto">

                            <button type="button" id="btn-generar-plan-marketing"
                                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold w-full mt-2">Generar</button>
                            <div id="respuesta-plan-marketing" class="mt-4 text-gray-800 text-sm whitespace-pre-line">
                            </div>
                            <div id="acciones-plan-marketing" class="flex flex-col md:flex-row gap-2 mt-2 hidden">
                                <button type="button" id="btn-reintentar-plan-marketing"
                                    class="bg-red-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>No me gusta, generar otro</button>
                                <button type="button" id="btn-copiar-plan-marketing"
                                    class="bg-green-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>Me gusta, copiar texto!</button>
                            </div>
                        </form>
                    </div>
                    <div class="resource-card flex flex-col items-center justify-between">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-image resource-icon text-2xl mb-2 text-purple-500"></i>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 text-center">Generar Imágenes o Logos
                            </h3>
                            <p class="text-gray-500 text-sm mb-3 text-center">Crea imágenes, logos o posters
                                promocionales únicos para tu marca utilizando IA.</p>
                        </div>
                        <button
                            class="bg-yellow-500 text-black px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold mt-2"
                            onclick="document.getElementById('form-imagen-logo').classList.toggle('hidden')"
                            type="button">Obtener</button>
                        <form id="form-imagen-logo" class="w-full mt-4 p-4 rounded-md shadow-inner hidden">
                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="tipo-imagen">¿Qué deseas
                                generar?</label>
                            <select id="tipo-imagen" name="tipo_imagen"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400"
                                required>
                                <option value="">Selecciona una opción</option>
                                <option value="logo">Logo</option>
                                <option value="poster">Poster promocional</option>
                                <option value="imagen">Imagen creativa</option>
                            </select>

                            <label class="block mb-2 text-sm font-semibold text-gray-700"
                                for="descripcion-imagen">Describe lo que quieres (colores, estilo, mensaje, elementos,
                                etc.)</label>
                            <textarea id="descripcion-imagen" name="descripcion_imagen"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400"
                                rows="3"
                                placeholder="Ejemplo: Logo minimalista para cafetería, colores marrón y crema, taza de café estilizada, sensación acogedora."
                                required></textarea>

                            <label class="block mb-2 text-sm font-semibold text-gray-700" for="uso-imagen">¿Dónde se
                                usará? (opcional)</label>
                            <input type="text" id="uso-imagen" name="uso_imagen"
                                class="w-full mb-3 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-purple-400"
                                placeholder="Ejemplo: Instagram, sitio web, tarjetas, etc.">

                            <button type="button" id="btn-generar-imagen-logo"
                                class="bg-yellow-500 text-black px-4 py-2 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold w-full mt-2">Generar</button>
                            <div id="respuesta-imagen-logo" class="mt-4 text-gray-800 text-sm whitespace-pre-line">
                            </div>
                            <div id="acciones-imagen-logo" class="flex flex-col md:flex-row gap-2 mt-2 hidden">
                                <button type="button" id="btn-reintentar-imagen-logo"
                                    class="bg-red-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>No me gusta, generar otro</button>
                                <button type="button" id="btn-copiar-imagen-logo"
                                    class="bg-green-500 text-white px-4 py-2 rounded-md transition duration-200 font-semibold flex-1 opacity-50 cursor-not-allowed"
                                    style="pointer-events: none;" disabled>Me gusta, copiar enlace!</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="tablon-colaboracion" class="mt-12 pt-8 border-t border-gray-200">
                <h2 class="resources-section-title text-2xl font-semibold text-gray-800 mb-4">Tablón de Colaboración
                </h2>
                <p class="text-gray-700 mb-6">Conecta con otros emprendedores para encontrar proveedores, socios de
                    producción o servicios especializados que tu negocio necesita, o publica lo que tú ofreces a la
                    comunidad de We-Connect.</p>

                <div class="text-center mb-8">
                    <?php if (isset($_SESSION["usuario"]["usuario"])): ?>
                        <a href="../comunidad/publicar.php"
                            class="bg-green-500 text-white py-3 px-6 rounded-md hover:bg-green-600 transition duration-200 font-semibold text-lg">Publicar
                            Necesidad </a>
                    <?php else: ?>
                        <p class="text-gray-600">Debes <a href="../usuarios/login.php"
                                class="text-indigo-500 hover:underline">iniciar sesión</a> para publicar en el tablón.</p>
                    <?php endif; ?>
                </div>


                <div id="listado-publicaciones" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <?php
                    // --- Obtener publicaciones de la base de datos ---
                    // La conexión a BD ($_conexion) ya se incluyó al inicio del archivo.
                    $sqlPublicaciones = "SELECT * FROM necesidades_ofertas ORDER BY fecha_publicacion DESC";
                    $resultadoPublicaciones = $_conexion->query($sqlPublicaciones);

                    if ($resultadoPublicaciones && $resultadoPublicaciones->num_rows > 0) {
                        while ($publicacion = $resultadoPublicaciones->fetch_assoc()) {
                            // Determinar colores e iconos según el tipo de publicación
                            $borderColorClass = ($publicacion['tipo'] === 'necesidad') ? 'border-blue-300' : 'border-green-300';
                            $bgColorClass = ($publicacion['tipo'] === 'necesidad') ? 'bg-blue-50' : 'bg-green-50';
                            $iconClass = ($publicacion['tipo'] === 'necesidad') ? 'fas fa-search text-blue-600' : 'fas fa-tools text-green-600';
                            $titleColorClass = ($publicacion['tipo'] === 'necesidad') ? 'text-blue-800' : 'text-green-800';
                            $tipoTexto = ($publicacion['tipo'] === 'necesidad') ? 'Necesidad' : 'Oferta';

                            echo '<div class="resource-card ' . $borderColorClass . ' ' . $bgColorClass . '">';
                            echo '    <div class="flex-shrink-0 mr-4">';
                            echo '        <i class="' . $iconClass . ' resource-icon text-2xl"></i>';
                            echo '    </div>';
                            echo '    <div class="flex-grow">';
                            echo '        <h3 class="text-lg font-semibold ' . $titleColorClass . ' mb-1">' . htmlspecialchars($tipoTexto) . ': ' . htmlspecialchars($publicacion['titulo']) . '</h3>';
                            echo '        <p class="text-gray-700 text-sm mb-2">' . nl2br(htmlspecialchars($publicacion['descripcion'])) . '</p>'; // nl2br para mantener saltos de línea
                            // Mostrar categoría si existe
                            if (!empty($publicacion['categoria_b2b'])) {
                                echo '        <p class="text-gray-600 text-xs mb-1">Categoría: <span class="font-semibold">' . htmlspecialchars($publicacion['categoria_b2b']) . '</span></p>';
                            }
                            // Mostrar estado (opcional, podrías darle estilos según el estado)
                            // echo '        <p class="text-gray-600 text-xs mb-1">Estado: <span class="font-semibold">' . htmlspecialchars($publicacion['estado']) . '</span></p>';
                            echo '        <p class="text-gray-600 text-xs">Publicado por: <span class="font-semibold">' . htmlspecialchars($publicacion['usuario_alias']) . '</span> el ' . htmlspecialchars(date("d/m/Y", strtotime($publicacion['fecha_publicacion']))) . '</p>';
                            // Puedes añadir un enlace para ver detalles o contactar si implementas esa funcionalidad
                            // echo '        <a href="../comunidad/detalle_publicacion.php?id=' . $publicacion['id'] . '" class="text-indigo-600 hover:underline text-sm mt-2 inline-block">Ver Detalles / Contactar</a>'; // Ruta relativa a una posible página de detalle en php/comunidad/
                            echo '    </div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p class='text-center text-gray-600 w-full md:col-span-2'>Aún no hay publicaciones en el tablón. ¡Sé el primero en publicar una necesidad u oferta!</p>";
                    }

                    // Cerrar conexión
                    $_conexion->close();
                    ?>

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
                        <li><a href="../../index.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Inicio</a>
                        </li>
                        <li><a href="../productos/producto.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Productos</a>
                        </li>
                        <li><a href="../servicios/servicio.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Servicios</a>
                        </li>
                        <li><a href="../contacto.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Contacto</a>
                        </li>
                        <li><a href="recursos.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500 font-semibold">Recursos
                                Emprendedores</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="text-lg font-semibold mb-4 text-white">Soporte</h3>
                    <ul class="list-none p-0">
                        <li><a href="../terminos/faq.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas
                                Frecuentes</a></li>
                        <li><a href="../terminos/terms.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos
                                de Servicio</a></li>
                        <li><a href="../terminos/privacy.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política
                                de Privacidad</a></li>
                        <li><a href="../contacto.php"
                                class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a>
                        </li>
                    </ul>
                </div>
                <div class="footer-section social-icons-footer">
                    <h3 class="text-lg font-semibold text-white mb-4">Síguenos</h3>
                    <div class="flex justify-center md:justify-start space-x-4 text-xl">
                        <a href="#"
                            class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i
                                class="fab fa-facebook-f"></i></a>
                        <a href="#"
                            class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i
                                class="fab fa-twitter"></i></a>
                        <a href="#"
                            class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500"><i
                                class="fab fa-linkedin-in"></i></a>
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
    <?php
        // Cargar variables de entorno del archivo .env en util
        $envPath = realpath(__DIR__ . '/../util/.env');
        $apiKeys = [];
        if ($envPath && file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                [$key, $value] = array_map('trim', explode('=', $line, 2));
                $apiKeys[$key] = $value;
            }
        }
        $OPENAI_API_KEY = $apiKeys['OPENAI_API_KEY'] ?? '';
        $IDEOGRAM_API_KEY = $apiKeys['IDEOGRAM_API_KEY'] ?? '';
    ?>
    <script>
        // API KEYS desde PHP
        const OPENAI_API_KEY = <?php echo json_encode($OPENAI_API_KEY); ?>;
        const IDEOGRAM_API_KEY = <?php echo json_encode($IDEOGRAM_API_KEY); ?>;

        // Cierra el formulario si se hace click fuera de él (opcional)
        document.addEventListener('click', function (event) {
            const form = document.getElementById('form-valor-agregado');
            const btn = event.target.closest('button[onclick*="form-valor-agregado"]');
            if (!form || form.classList.contains('hidden')) return;
            if (!form.contains(event.target) && !btn) {
                form.classList.add('hidden');
            }
        });

        document.getElementById('btn-generar-valor-agregado').addEventListener('click', generarValorAgregado);
        document.getElementById('btn-reintentar-valor-agregado').addEventListener('click', generarValorAgregado);
        document.getElementById('btn-copiar-valor-agregado').addEventListener('click', function () {
            const texto = document.getElementById('respuesta-valor-agregado').textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(texto);
            } else {
                // Fallback para navegadores antiguos
                const tempInput = document.createElement('textarea');
                tempInput.value = texto;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            }
            this.textContent = "¡Copiado!";
            setTimeout(() => { this.textContent = "Me gusta, copiar texto!"; }, 1500);
        });

        async function generarValorAgregado() {
            const nombre = document.getElementById('nombre-producto').value.trim();
            const descripcion = document.getElementById('descripcion-producto').value.trim();
            const mercado = document.getElementById('mercado-objetivo').value.trim();
            const problema = document.getElementById('problema').value.trim();
            const diferenciadores = document.getElementById('diferenciadores').value.trim();

            const btnReintentar = document.getElementById('btn-reintentar-valor-agregado');
            const btnCopiar = document.getElementById('btn-copiar-valor-agregado');

            // Deshabilitar botones antes de generar
            btnReintentar.disabled = true;
            btnCopiar.disabled = true;
            btnReintentar.classList.add('opacity-50', 'cursor-not-allowed');
            btnCopiar.classList.add('opacity-50', 'cursor-not-allowed');
            btnReintentar.style.pointerEvents = "none";
            btnCopiar.style.pointerEvents = "none";

            if (!nombre || !descripcion || !mercado || !problema) {
                document.getElementById('respuesta-valor-agregado').textContent = "Por favor, completa todos los campos obligatorios.";
                document.getElementById('acciones-valor-agregado').classList.add('hidden');
                return;
            }

            document.getElementById('respuesta-valor-agregado').textContent = "Generando propuesta de valor agregado...";
            document.getElementById('acciones-valor-agregado').classList.add('hidden');

            const prompt = `Actúa como un consultor de innovación para emprendedores. Analiza el siguiente producto y su contexto, y sugiere una forma detallada, creativa y realista de agregarle valor agregado, explicando el razonamiento y los pasos a seguir, no mas de 500 palabras. 
Producto: ${nombre}
Descripción: ${descripcion}
Mercado objetivo: ${mercado}
Problema que resuelve: ${problema}
Diferenciadores actuales: ${diferenciadores ? diferenciadores : 'No especificado'}
Propuesta de valor agregado:`;

            try {
                const response = await fetch("https://api.openai.com/v1/chat/completions", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + OPENAI_API_KEY
                    },
                    body: JSON.stringify({
                        model: "gpt-3.5-turbo",
                        messages: [
                            { role: "system", content: "Eres un experto en innovación y emprendimiento." },
                            { role: "user", content: prompt }
                        ],
                        max_tokens: 600,
                        temperature: 0.8
                    })
                });

                const data = await response.json();
                if (data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content) {
                    document.getElementById('respuesta-valor-agregado').textContent = data.choices[0].message.content.trim();
                    document.getElementById('acciones-valor-agregado').classList.remove('hidden');
                    // Habilitar botones solo cuando hay respuesta
                    btnReintentar.disabled = false;
                    btnCopiar.disabled = false;
                    btnReintentar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnCopiar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnReintentar.style.pointerEvents = "";
                    btnCopiar.style.pointerEvents = "";
                } else {
                    document.getElementById('respuesta-valor-agregado').textContent = "No se pudo generar una propuesta. Intenta nuevamente.";
                    document.getElementById('acciones-valor-agregado').classList.add('hidden');
                }
            } catch (error) {
                document.getElementById('respuesta-valor-agregado').textContent = "Ocurrió un error al conectar con la IA. Intenta nuevamente.";
                document.getElementById('acciones-valor-agregado').classList.add('hidden');
            }
        }

        document.getElementById('btn-generar-plan-marketing').addEventListener('click', generarPlanMarketing);
        document.getElementById('btn-reintentar-plan-marketing').addEventListener('click', generarPlanMarketing);
        document.getElementById('btn-copiar-plan-marketing').addEventListener('click', function () {
            const texto = document.getElementById('respuesta-plan-marketing').textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(texto);
            } else {
                const tempInput = document.createElement('textarea');
                tempInput.value = texto;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            }
            this.textContent = "¡Copiado!";
            setTimeout(() => { this.textContent = "Me gusta, copiar texto!"; }, 1500);
        });

        async function generarPlanMarketing() {
            const nombre = document.getElementById('pm-nombre-negocio').value.trim();
            const descripcion = document.getElementById('pm-descripcion').value.trim();
            const publico = document.getElementById('pm-publico-objetivo').value.trim();
            const objetivo = document.getElementById('pm-objetivo-marketing').value.trim();
            const presupuesto = document.getElementById('pm-presupuesto').value.trim();

            const btnReintentar = document.getElementById('btn-reintentar-plan-marketing');
            const btnCopiar = document.getElementById('btn-copiar-plan-marketing');

            btnReintentar.disabled = true;
            btnCopiar.disabled = true;
            btnReintentar.classList.add('opacity-50', 'cursor-not-allowed');
            btnCopiar.classList.add('opacity-50', 'cursor-not-allowed');
            btnReintentar.style.pointerEvents = "none";
            btnCopiar.style.pointerEvents = "none";

            if (!nombre || !descripcion || !publico || !objetivo) {
                document.getElementById('respuesta-plan-marketing').textContent = "Por favor, completa todos los campos obligatorios.";
                document.getElementById('acciones-plan-marketing').classList.add('hidden');
                return;
            }

            document.getElementById('respuesta-plan-marketing').textContent = "Generando plan de marketing...";
            document.getElementById('acciones-plan-marketing').classList.add('hidden');

            const prompt = `Actúa como un consultor de marketing para emprendedores. Con la siguiente información, genera un plan de marketing detallado, creativo y realista, explicando las estrategias recomendadas, canales, acciones y pasos a seguir, adaptado al contexto y presupuesto indicado. 
                Nombre del negocio/producto: ${nombre}
                Descripción: ${descripcion}
                Público objetivo: ${publico}
                Objetivo principal del marketing: ${objetivo}
                Presupuesto estimado: ${presupuesto ? presupuesto : 'No especificado'}
                Plan de marketing:`;

            try {
                const response = await fetch("https://api.openai.com/v1/chat/completions", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + OPENAI_API_KEY
                    },
                    body: JSON.stringify({
                        model: "gpt-3.5-turbo",
                        messages: [
                            { role: "system", content: "Eres un experto en marketing y emprendimiento." },
                            { role: "user", content: prompt }
                        ],
                        max_tokens: 700,
                        temperature: 0.8
                    })
                });

                const data = await response.json();
                if (data.choices && data.choices[0] && data.choices[0].message && data.choices[0].message.content) {
                    document.getElementById('respuesta-plan-marketing').textContent = data.choices[0].message.content.trim();
                    document.getElementById('acciones-plan-marketing').classList.remove('hidden');
                    btnReintentar.disabled = false;
                    btnCopiar.disabled = false;
                    btnReintentar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnCopiar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnReintentar.style.pointerEvents = "";
                    btnCopiar.style.pointerEvents = "";
                } else {
                    document.getElementById('respuesta-plan-marketing').textContent = "No se pudo generar un plan. Intenta nuevamente.";
                    document.getElementById('acciones-plan-marketing').classList.add('hidden');
                }
            } catch (error) {
                document.getElementById('respuesta-plan-marketing').textContent = "Ocurrió un error al conectar con la IA. Intenta nuevamente.";
                document.getElementById('acciones-plan-marketing').classList.add('hidden');
            }
        }

        document.getElementById('btn-generar-imagen-logo').addEventListener('click', generarImagenLogo);
        document.getElementById('btn-reintentar-imagen-logo').addEventListener('click', generarImagenLogo);
        document.getElementById('btn-copiar-imagen-logo').addEventListener('click', function () {
            const enlace = document.getElementById('respuesta-imagen-logo').textContent;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(enlace);
            } else {
                const tempInput = document.createElement('textarea');
                tempInput.value = enlace;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
            }
            this.textContent = "¡Copiado!";
            setTimeout(() => { this.textContent = "Me gusta, copiar enlace!"; }, 1500);
        });

        async function generarImagenLogo() {
            const tipo = document.getElementById('tipo-imagen').value;
            const descripcion = document.getElementById('descripcion-imagen').value.trim();
            const uso = document.getElementById('uso-imagen').value.trim();

            const btnReintentar = document.getElementById('btn-reintentar-imagen-logo');
            const btnCopiar = document.getElementById('btn-copiar-imagen-logo');

            btnReintentar.disabled = true;
            btnCopiar.disabled = true;
            btnReintentar.classList.add('opacity-50', 'cursor-not-allowed');
            btnCopiar.classList.add('opacity-50', 'cursor-not-allowed');
            btnReintentar.style.pointerEvents = "none";
            btnCopiar.style.pointerEvents = "none";

            if (!tipo || !descripcion) {
                document.getElementById('respuesta-imagen-logo').textContent = "Por favor, completa los campos obligatorios.";
                document.getElementById('acciones-imagen-logo').classList.add('hidden');
                return;
            }

            document.getElementById('respuesta-imagen-logo').textContent = "Generando imagen...";
            document.getElementById('acciones-imagen-logo').classList.add('hidden');

            // Construir el prompt para la API de Ideogram
            let prompt = `${tipo === 'logo' ? 'Logo' : tipo === 'poster' ? 'Poster promocional' : 'Imagen creativa'}: ${descripcion}`;
            if (uso) prompt += `. Uso: ${uso}`;

            try {
                const response = await fetch("../util/generar_logo.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        descripcion: prompt,
                        modo: "auto"
                    })
                });

                const data = await response.json();
                if (data.success && data.imageUrl) {
                    document.getElementById('respuesta-imagen-logo').innerHTML = `<a href="${data.imageUrl}" target="_blank" class="text-purple-700 underline">Ver imagen generada</a>`;
                    document.getElementById('acciones-imagen-logo').classList.remove('hidden');
                    btnReintentar.disabled = false;
                    btnCopiar.disabled = false;
                    btnReintentar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnCopiar.classList.remove('opacity-50', 'cursor-not-allowed');
                    btnReintentar.style.pointerEvents = "";
                    btnCopiar.style.pointerEvents = "";
                } else {
                    document.getElementById('respuesta-imagen-logo').textContent = "No se pudo generar la imagen. " + (data.error ? data.error : "Intenta nuevamente.");
                    document.getElementById('acciones-imagen-logo').classList.add('hidden');
                }
            } catch (error) {
                document.getElementById('respuesta-imagen-logo').textContent = "Ocurrió un error al conectar con la IA. Intenta nuevamente. " + error;
                document.getElementById('acciones-imagen-logo').classList.add('hidden');
            }
        }
    </script>

</body>

</html>