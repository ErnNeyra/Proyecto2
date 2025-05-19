<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Producto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css"> <link rel="stylesheet" href="../../css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
          <nav class="flex items-center space-x-4">
                <a href="../recursos/recursos.php" class="text-gray-700 hover:text-black mr-4 font-semibold">Recursos</a>
                <a></a>
                <a href="../productos/producto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Productos</a>
                <a></a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Servicios</a>

                <a href="../contacto.php" class="text-gray-700 hover:text-marca-primario transition duration-200">Contacto</a>
                <?php
                    if (!isset($_GET['id_producto'])) {
                        die("Error: ID de producto no especificado.");
                    }

                    $idProducto = $_GET["id_producto"];

                    // --- Obtener datos del producto ---
                    $sqlProducto = "SELECT * FROM producto WHERE id_producto = ?";
                    $stmtProducto = $_conexion->prepare($sqlProducto);

                    if ($stmtProducto === false) {
                         die("Error preparando la consulta del producto: " . $_conexion->error);
                    }

                    $stmtProducto->bind_param("i", $idProducto);
                    $stmtProducto->execute();
                    $resultadoProducto = $stmtProducto->get_result();

                    if ($resultadoProducto->num_rows === 0) {
                        die("Error: Producto no encontrado.");
                    }

                    $producto = $resultadoProducto->fetch_assoc();
                    $nombre = htmlspecialchars($producto["nombre"]);
                    $precio = htmlspecialchars($producto["precio"]);
                    $categoria = htmlspecialchars($producto["categoria"]); // Obtenemos la categoría
                    $descripcion = htmlspecialchars($producto["descripcion"]);
                    $imagen = htmlspecialchars($producto["imagen"]);
                    $usuarioPropietario = htmlspecialchars($producto["usuario"]);
                    $stmtProducto->close();

                    // --- Obtener comentarios y valoraciones para productos ---
                    $sqlComentarios = "SELECT cp.*, u.usuario AS nombre_usuario_comentario, u.foto_perfil
                                      FROM comentarios_producto cp
                                      JOIN usuario u ON cp.usuario_alias = u.usuario
                                      WHERE cp.id_producto = ?
                                      ORDER BY cp.fecha_creacion DESC";

                    $stmtComentarios = $_conexion->prepare($sqlComentarios);
                    if ($stmtComentarios === false) { die("Error preparando la consulta de comentarios: " . $_conexion->error); }
                    $stmtComentarios->bind_param("i", $idProducto);
                    $stmtComentarios->execute();
                    $resultadoComentarios = $stmtComentarios->get_result();
                    $comentarios = [];
                    if ($resultadoComentarios) {
                        while ($fila = $resultadoComentarios->fetch_assoc()) {
                            $comentarios[] = $fila;
                        }
                        $resultadoComentarios->free();
                    }
                    $stmtComentarios->close();

                    // --- Calcular valoración promedio para productos ---
                    $sqlPromedio = "SELECT AVG(valoracion) AS promedio, COUNT(id_comentario) AS total_valoraciones
                                    FROM comentarios_producto
                                    WHERE id_producto = ?";

                    $stmtPromedio = $_conexion->prepare($sqlPromedio);
                     if ($stmtPromedio === false) { die("Error preparando la consulta de promedio: " . $_conexion->error); }
                    $stmtPromedio->bind_param("i", $idProducto);
                    $stmtPromedio->execute();
                    $resultadoPromedio = $stmtPromedio->get_result()->fetch_assoc();
                    $valoracionPromedio = round($resultadoPromedio['promedio'] ?? 0, 1);
                    $totalValoraciones = $resultadoPromedio['total_valoraciones'] ?? 0;
                    $stmtPromedio->close();

                    // --- Obtener productos relacionados (misma categoría, excluyendo el actual) ---
                    $sqlRelacionados = "SELECT * FROM producto
                                        WHERE categoria = ? AND id_producto != ?
                                        ORDER BY fecha_agregado DESC LIMIT 10";

                    $stmtRelacionados = $_conexion->prepare($sqlRelacionados);
                    if ($stmtRelacionados === false) { die("Error preparando la consulta de relacionados: " . $_conexion->error); }
                    $stmtRelacionados->bind_param("si", $categoria, $idProducto);
                    $stmtRelacionados->execute();
                    $resultadoRelacionados = $stmtRelacionados->get_result();
                    $productosRelacionados = [];
                    if ($resultadoRelacionados) {
                        while ($fila = $resultadoRelacionados->fetch_assoc()) {
                            $productosRelacionados[] = $fila;
                        }
                        $resultadoRelacionados->free();
                    }
                    $stmtRelacionados->close();


                    // --- Mostrar menú de usuario ---
                    if(isset($_SESSION["usuario"]["usuario"])){
                        $aliasUsuarioActual = htmlspecialchars($_SESSION['usuario']['usuario']);
                         $imagenPerfil = '../util/img/usuario.png'; // Ruta por defecto para el header
                         if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                             $rutaImagen = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                             if (file_exists($rutaImagen)) {
                                 $imagenPerfil = $rutaImagen;
                             }
                         }

                        echo '<div class="relative">';
                        echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                         echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . htmlspecialchars($imagenPerfil) . '" alt="Imagen de Perfil">';

                        echo '        <span>' . $aliasUsuarioActual . '</span>';
                        echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        echo '    </button>';
                        echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                        echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                        echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                         echo '        <hr class="border-gray-200">';
                        echo '        <a href="../comunidad/tablon.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Tablón Comunidad</a>';
                        echo '        <a href="../categoria/index.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Categoría</a>';
                        echo '        <hr class="border-red-200">';
                         if (isset($_SESSION['usuario']['rol'])) {
                             if ($_SESSION['usuario']['rol'] === 'vendedor' || $_SESSION['usuario']['rol'] === 'admin') {
                                  echo '        <hr class="border-gray-200">';
                                  echo '        <a href="gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Productos</a>';
                                  echo '        <a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Servicios</a>';
                             }
                         }
                        echo '        <hr class="border-gray-200">';
                        echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                        echo '    </div>';
                        echo '</div>';
                    } else {
                        echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                        echo '<a href="../usuarios/registro.php" class="ml-4 bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                    }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div id="contenedor-imagen-principal" class="mb-4 cursor-zoom-in">
                        <img id="imagen-principal" src="../util/<?php echo $imagen ?>" alt="Imagen del Producto" class="w-full rounded-md">
                    </div>
                    <div id="lightbox" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-80 z-50 hidden flex items-center justify-center">
                        <button id="cerrar-lightbox" class="absolute top-4 right-4 text-white text-3xl focus:outline-none">&times;</button>
                         <button id="anterior-imagen" class="absolute left-4 text-white text-3xl focus:outline-none">&lsaquo;</button>
                        <img id="lightbox-imagen" src="" alt="Imagen Ampliada" class="max-w-full max-h-full rounded-md">
                         <button id="siguiente-imagen" class="absolute right-4 text-white text-3xl focus:outline-none">&rsaquo;</button>
                    </div>
                </div>
                <div>
                    <h1 class="text-3xl font-semibold text-gray-800 mb-4"><?php echo $nombre ?></h1>
                    <div class="flex items-center mb-4">
                         <?php
                             $estrellasLlenas = floor($valoracionPromedio);
                             $mediaEstrella = ($valoracionPromedio - $estrellasLlenas) >= 0.5;

                             for ($i = 1; $i <= 5; $i++) {
                                 if ($i <= $estrellasLlenas) {
                                     echo '<i class="fas fa-star text-yellow-500 text-xl mr-1"></i>';
                                 } elseif ($i == $estrellasLlenas + 1 && $mediaEstrella) {
                                     echo '<i class="fas fa-star-half-alt text-yellow-500 text-xl mr-1"></i>';
                                 } else {
                                     echo '<i class="far fa-star text-gray-300 text-xl mr-1"></i>';
                                 }
                             }
                         ?>
                        <span class="text-gray-600 text-sm ml-2">(<?php echo $totalValoraciones; ?> valoraciones)</span>
                    </div>
                    <p class="text-gray-700 leading-relaxed mb-6"><?php echo $descripcion ?></p>
                    <p class="text-gray-600 font-semibold mb-2">Precio: <span class="text-black"><?php echo $precio?>€</span></p>
                    <p class="text-gray-600 mb-4"><span class="text-green-500">Disponible</span></p>

                    <?php
                        // Verificar si el usuario logueado es el propietario del producto
                        if (isset($_SESSION["usuario"]["usuario"]) && $_SESSION["usuario"]["usuario"] === $usuarioPropietario) {
                            // Mostrar el botón de editar
                            echo '<div class="mb-6">';
                            echo '    <a href="editarProducto.php?id_producto=' . htmlspecialchars($idProducto) . '" class="bg-yellow-500 text-black py-3 px-6 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Editar Producto</a>';
                            echo '</div>';
                        } else {
                             echo '<div class="mb-6 text-gray-600">';
                             echo '    Este producto no te pertenece.';
                             echo '</div>';
                        }
                     ?>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Ofrecido por:</h2>
                        <div class="flex items-center">
                             <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-white font-semibold mr-2">
                                EP
                            </div>
                            <p class="text-gray-700 font-semibold"><?php echo $usuarioPropietario ?></p>
                        </div>
                        <p class="text-gray-600 text-sm mt-1">Breve descripción del emprendedor.</p>
                         </div>
                </div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Comentarios de otros usuarios</h2>
                <div id="lista-comentarios">
                    <?php
                        // --- Mostrar comentarios existentes (Sin Foto de Perfil) ---
                        if (!empty($comentarios)) {
                            foreach ($comentarios as $comentario) {
                                echo '<div class="mb-6 p-4 bg-gray-100 rounded-md border border-gray-200 comentario-item" data-id="' . htmlspecialchars($comentario['id_comentario']) . '">';
                                 echo '    <div class="flex items-center mb-2">';
                                 echo '        <p class="text-gray-700 font-semibold mr-2">' . htmlspecialchars($comentario['nombre_usuario_comentario']) . ':</p>';
                                  echo '        <span class="text-gray-600 text-sm">' . htmlspecialchars(date("d/m/Y H:i", strtotime($comentario['fecha_creacion']))) . '</span>'; // Formatear fecha
                                  echo '    </div>';
                                echo '    <p class="text-gray-700 mb-2">' . htmlspecialchars($comentario['comentario']) . '</p>';
                                echo '    <div class="flex items-center">';
                                 // Mostrar estrellas de valoración para este comentario
                                 $valoracionIndividual = $comentario['valoracion'] ?? 0;
                                 for ($i = 1; $i <= 5; $i++) {
                                     if ($i <= $valoracionIndividual) {
                                         echo '<i class="fas fa-star text-yellow-500 text-sm mr-1"></i>';
                                     } else {
                                         echo '<i class="far fa-star text-gray-300 text-sm mr-1"></i>';
                                     }
                                 }
                                echo '    </div>';
                                echo '</div>';
                            }
                        } else {
                            echo "<p class='text-center text-gray-600'>Aún no hay comentarios. ¡Sé el primero en comentar!</p>";
                        }
                    ?>
                </div>

           <div class="mt-8">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Dejar un comentario y valoración</h3>
    <?php if (isset($_SESSION["usuario"]["usuario"])): ?>
        <form id="formulario-comentario" method="POST" data-submit-url="../../php/productos/guardar_comentario_producto_ajax.php">
             <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($idProducto); ?>">

            <div class="mb-4">
                <label for="valoracion-comentario" class="block text-gray-700 text-sm font-bold mb-2">Tu valoración:</label>
                <div class="flex items-center">
                    <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="1">&#9733;</button>
                    <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="2">&#9733;</button>
                    <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="3">&#9733;</button>
                    <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="4">&#9733;</button>
                    <button type="button" class="star-button-comentario text-gray-300 hover:text-yellow-500 focus:outline-none text-xl mr-1" data-value="5">&#9733;</button>
                    <input type="hidden" id="valoracion-comentario" name="valoracion" value="0">
                </div>
            </div>
            <div class="mb-4">
                <label for="comentario" class="block text-gray-700 text-sm font-bold mb-2">Tu comentario:</label>
                <textarea id="comentario" name="comentario" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Escribe tu opinión"></textarea>
            </div>
            <button type="submit" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 focus:outline-none focus:shadow-outline">Publicar comentario</button>
             <div id="feedback-mensaje" class="mt-4 text-sm font-semibold"></div>
        </form>
    <?php else: ?>
        <p class="text-gray-600">Debes <a href="../usuarios/login.php" class="text-indigo-500 hover:underline">iniciar sesión</a> para dejar un comentario y valoración.</p>
    <?php endif; ?>
</div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Productos relacionados</h2>
                 <?php if (!empty($productosRelacionados)): ?>
                    <div id="carrusel-relacionados-container" class="overflow-x-auto whitespace-nowrap scroll-smooth py-4 -ml-4 pl-4 relative">
                        <div id="carrusel-relacionados" class="whitespace-nowrap transition-transform duration-300">
                            <?php foreach ($productosRelacionados as $productoRelacionado): ?>
                                <div class="inline-block mr-4 w-72 shadow-md rounded-md overflow-hidden border border-gray-200">
                                     <a href="detalleProducto.php?id_producto=<?php echo htmlspecialchars($productoRelacionado['id_producto']); ?>" class="block">
                                        <img src="../util/<?php echo htmlspecialchars($productoRelacionado['imagen']); ?>" alt="<?php echo htmlspecialchars($productoRelacionado['nombre']); ?>" class="w-full h-40 object-cover">
                                    </a>
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-700"><?php echo htmlspecialchars($productoRelacionado['nombre']); ?></h3>
                                        <p class="text-gray-600 text-sm">Precio: <?php echo htmlspecialchars($productoRelacionado['precio']); ?>€</p>
                                         <a href="detalleProducto.php?id_producto=<?php echo htmlspecialchars($productoRelacionado['id_producto']); ?>" class="text-yellow-500 hover:underline text-sm mt-2 inline-block">Ver Detalles</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button id="prev-relacionado" class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -ml-2 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button id="next-relacionado" class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-300 hover:bg-gray-400 text-black rounded-full w-10 h-10 flex items-center justify-center -mr-2 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                 <?php else: ?>
                     <p class="text-center text-gray-600">No hay productos relacionados en esta categoría por el momento.</p>
                 <?php endif; ?>
            </div>
        </div>
    </main>

    <a href="https://wa.me/+34693680668?text=Hola%20estoy%20interesado%20en%20tu%20producto%20<?php echo urlencode($nombre); ?>%20que%20has%20publicado%20en%20We-Connect!%20Quiero%20saber%20más%20sobre%20él"
       class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 rounded-full shadow-lg p-4 flex items-center justify-center gap-2"
       target="_blank" rel="noopener" aria-label="Contactar por WhatsApp">
        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.93 11.93 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.15 1.6 5.96L0 24l6.23-1.63A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.19-1.24-6.19-3.48-8.52zM12 22c-1.85 0-3.66-.5-5.23-1.45l-.37-.22-3.7.97.99-3.6-.24-.37A9.94 9.94 0 0 1 2 12c0-5.52 4.48-10 10-10s10 4.48 10 10-4.48 10-10 10zm5.2-7.6c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.41-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.34.42-.51.14-.17.18-.29.28-.48.09-.19.05-.36-.02-.5-.07-.14-.61-1.47-.84-2.01-.22-.53-.45-.46-.62-.47-.16-.01-.36-.01-.56-.01s-.51.07-.78.36c-.27.29-1.03 1.01-1.03 2.47 0 1.46 1.06 2.87 1.21 3.07.15.2 2.09 3.19 5.08 4.34.71.25 1.26.4 1.69.51.71.18 1.36.16 1.87.1.57-.07 1.65-.67 1.89-1.32.23-.65.23-1.2.16-1.32-.07-.12-.25-.19-.53-.33z"/>
        </svg>
        <span class="text-white font-semibold text-base">Chatear con el emprendedor</span>
    </a>

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
                        <li><a href="producto.php"
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
                        <li><a href="php/terminos/faq.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Preguntas Frecuentes</a></li>
                        <li><a href="php/terminos/terms.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Términos de Servicio</a></li>
                        <li><a href="php/terminos/privacy.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Política de Privacidad</a></li>
                        <li><a href="php/contacto.php" class="hover:text-marca-secundaria transition duration-200 text-gray-300 hover:text-yellow-500">Ayuda</a></li>
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 6fb7bbcbf981b0ad6af3190dbf3118e25e02922a


    <script src="../../js/desplegable.js"></script>
    <script src="../../js/lightbox.js"></script> <script src="../../js/comentario.js"></script> <script src="../../js/relatedProductsCarousel.js"></script> </body>
<<<<<<< HEAD
</html>
=======
</html>
=======
    <script src="../../js/script2.js"></script>
</body>
</html>
>>>>>>> fb7cb498cf3ef04097a88d26c3ed5c9f5f58bbd6
>>>>>>> 6fb7bbcbf981b0ad6af3190dbf3118e25e02922a
