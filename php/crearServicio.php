<?php
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('util/config.php');
    require ('util/depurar.php');
    session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Servicio | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<?php
    session_start();

    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['usuario'])) {
        // Si no ha iniciado sesión, redirigir a la página de inicio de sesión
        header("location: login.php"); // O una página de error de acceso no autorizado
        echo"<h2>Debes iniciar sesión para añadir un producto.</h2>";
        exit();
    }else{
        // Puedes usar $id_usuario para realizar consultas a la base de datos o cualquier otra operación
        echo "<h2>Bienvenid@ ".$_SESSION["usuario"]."</h2>";
        echo "<h2>Tu ID de usuario es: ".$_SESSION["id_usuario"]."</h2>";
    }
?>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="producto.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="servicio.php" class="text-gray-700 hover:text-black mr-4">Servicios</a>
                <?php
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
    </header>

    <?php
        $sql = "SELECT nombre FROM categoria";
        $resultado = $_conexion -> query($sql);
        $categorias = [];
        /* fetch_assoc() devuelve una fila de resultados como un array asociativo. Esto significa que podrás acceder
        a cada columna de la fila por su nombre */
        while($fila = $resultado -> fetch_assoc()){
            array_push($categorias, $fila["nombre"]);
        }
    ?>
    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Servicio</h1>
            <form action="crearServicio.php" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                    <textarea id="descripcion" name="descripcion" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="precio" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: [establecer límite].</p>
                </div>
                <!-- CATegorIA -->
                <div class="mb-4">
                    <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                    <select id="categoria" name="categoria" class="form-select form-select-lg">
                    <option value="">---Selecciona una categoría---</option>
                    <?php
                    foreach ($categorias as $categoria): ?>
                        <!-- Ambas formas php las interpreta de la misma manera, está creando un option con el valor $estudio e imprimiendolo en el select -->
                        <option value="<?php echo $categoria;?>"><?= $categoria ?></option>
                    <?php endforeach; ?>
                    </select>
                    <?php if (isset($errorCategoria)) echo "<span class='error'>$errorCategoria</span>"; ?>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Servicio</button>
                    <a href="servicio.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        &copy; 2025 We-Connect. Todos los derechos reservados.
    </footer>
</body>
</html>