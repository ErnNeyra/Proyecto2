<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../../index.php" class="logo inline-block">
                <img src="../util/img/Logo.png" alt="We-Connect Logo" class="h-10 w-auto">
            </a>
            <nav class="flex items-center space-x-4 md:space-x-6">
                <a href="../productos/producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="../servicios/servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <a href="../contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <?php
                    // Iniciar sesión solo si no ha sido iniciada
                     if (session_status() == PHP_SESSION_NONE) {
                         session_start();
                     }

                    // Lógica para mostrar menú de usuario o enlaces de login/registro
                    if(isset($_SESSION["usuario"]["usuario"])){
                        $aliasUsuarioActual = htmlspecialchars($_SESSION['usuario']['usuario']);
                         $imagenPerfil = '../util/img/usuario.jpg'; // Ruta por defecto
                         if (isset($_SESSION['usuario']['foto_perfil']) && !empty($_SESSION['usuario']['foto_perfil'])) {
                              $imagenPerfil = '../util/' . ltrim($_SESSION['usuario']['foto_perfil'], '/');
                         } else {
                              $imagenPerfil = '../util/img/usuario.jpg';
                         }

                        echo '<div class="relative">';
                        echo '    <button id="user-dropdown-button" class="flex items-center text-gray-700 hover:text-black focus:outline-none" aria-expanded="false" aria-haspopup="true">';
                         echo '        <img class="h-8 w-8 rounded-full mr-2 object-cover" src="' . htmlspecialchars($imagenPerfil) . '" alt="Imagen de Perfil">';

                        echo '        <span>' . $aliasUsuarioActual . '</span>';
                        echo '        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                        echo '    </button>';
                        echo '    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-10 hidden">';
                        // Ajusta las rutas en el desplegable relativas desde php/terminos/
                        echo '        <a href="../usuarios/panelUsuario.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Mi Panel</a>';
                        echo '        <a href="../usuarios/editarPerfil.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Editar Perfil</a>';
                         if (isset($_SESSION['usuario']['rol'])) {
                             if ($_SESSION['usuario']['rol'] === 'vendedor' || $_SESSION['usuario']['rol'] === 'admin') {
                                  echo '        <hr class="border-gray-200">';
                                  echo '        <a href="../productos/gestionarProductos.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Productos</a>';
                                  echo '        <a href="../servicios/gestionarServicios.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition duration-200">Gestionar Servicios</a>';
                             }
                         }
                        echo '        <hr class="border-gray-200">';
                        echo '        <a href="../usuarios/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100 transition duration-200">Cerrar Sesión</a>';
                        echo '    </div>';
                        echo '</div>';
                    } else {
                        // Ajusta las rutas de login/registro relativas desde php/terminos/
                        echo '<a href="../usuarios/login.php" class="text-gray-700 hover:text-black">Iniciar Sesión</a>';
                        echo '<a href="../usuarios/registro.php" class="ml-4 bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200 font-semibold">Regístrate</a>';
                    }
                ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Política de Privacidad</h1>

            <div class="prose max-w-none"> <p>Esta Política de Privacidad describe cómo We-Connect recoge, utiliza y protege tu información personal.</p>

                <h2>Información que recogemos</h2>
                <p>Recogemos información que nos proporcionas directamente al registrarte o usar nuestros servicios, como nombre, email, información de perfil, detalles de productos/servicios que publicas, etc.</p>
                <p>También recogemos información automáticamente cuando usas la plataforma, como tu dirección IP, tipo de navegador, páginas visitadas, etc.</p>

                <h2>Cómo utilizamos tu información</h2>
                <p>Utilizamos la información para:</p>
                <ul>
                    <li>Proporcionar y mantener nuestro servicio.</li>
                    <li>Personalizar tu experiencia y contenido.</li>
                    <li>Comunicarnos contigo, responder a tus consultas y enviarte notificaciones.</li>
                    <li>Mejorar y desarrollar nuevas características de la plataforma.</li>
                    <li>Prevenir fraudes y garantizar la seguridad.</li>
                </ul>

                <h2>Compartir tu información</h2>
                <p>No compartimos tu información personal con terceros, excepto cuando sea necesario para operar la plataforma, cumplir con la ley, o proteger nuestros derechos.</p>

                <h2>Seguridad de los datos</h2>
                <p>Implementamos medidas de seguridad para proteger tu información, pero ningún método de transmisión por Internet o almacenamiento electrónico es 100% seguro.</p>

                <h2>Tus derechos</h2>
                <p>Tienes derecho a acceder, corregir o eliminar tu información personal. También puedes oponerte al procesamiento de tus datos o solicitar la limitación de su uso.</p>

                <h2>Cambios a esta política</h2>
                <p>Podemos actualizar nuestra Política de Privacidad ocasionalmente. Te notificaremos cualquier cambio publicando la nueva política en esta página.</p>

                <p>Última actualización: 18 de mayo de 2025</p>
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
                    <ul class="list-