<?php
    error_reporting(E_ALL);
    ini_set("display_errors",1);
    require ('config.php');
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

    $error = "";
    $success = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $id_usuario = $_SESSION['id_usuario'];
        


        $imagen = $_FILES["imagen"]["name"];
        $ubicacionTemporal = $_FILES["imagen"]["tmp_name"];
        $ubicacionFinal = "../img/$imagen";
        $imagenTipo = $_FILES["imagen"]["type"];

        //mueve el archivo que se ha cargado de una ubicación a otra
        move_uploaded_file($ubicacionTemporal, $ubicacionFinal);

        if(isset($nombre) && isset($descripcion) && isset($precio) && isset($id_usuario) && isset($ubicacionFinal)){
                
            /* $sql = "INSERT INTO animes (titulo, nombre_estudio, anno_estreno, num_temporadas, imagen)
                VALUES ('$titulo','$nombreEstudio','$anioEstreno','$numeroTemporadas', '$ubicacionFinal')";
            $_conexion -> query($sql); */
            
            //1. Preparar
            $sql = $_conexion -> prepare("INSERT INTO producto
                (nombre, descripcion, precio, id_usuario, imagen)
                VALUES (?, ?, ?, ?, ?)"
            );
            //2. Enlazado
            $sql -> bind_param("ssiis",
                $nombre, $descripcion, $precio,
                $id_usuario, $ubicacionFinal
            );
            //3. Ejecución
            $sql -> execute();
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Producto | We-Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <header class="bg-white shadow-md">
        <div class="container mx-auto py-4 px-6 flex items-center justify-between">
            <a href="../index.php" class="text-xl font-bold text-black">We-Connect</a>
            <nav class="flex items-center">
                <a href="listado.php" class="text-gray-700 hover:text-black mr-4">Productos</a>
                <a href="contacto.php" class="text-gray-700 hover:text-black mr-4">Contacto</a>
                <a href="registro.php" class="bg-transparent text-gray-700 border border-gray-300 py-2 px-4 rounded-md hover:bg-gray-100 hover:border-gray-400 mr-4 transition duration-200">Registrarse</a>
                <a href="login.php" class="bg-yellow-500 text-black py-2 px-4 rounded-md hover:bg-yellow-600 transition duration-200">Iniciar Sesión</a>
                <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <a href="panelUsuario.php" class="text-gray-700 hover:text-black mr-4">Mi Panel</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container mx-auto py-12 px-6 flex-grow">
        <div class="bg-white rounded-md shadow-md p-8 max-w-lg mx-auto border border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Crear Nuevo Producto o Servicio</h1>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="text-green-500 mb-4"><?php echo $success; ?></p>
            <?php endif; ?>
            <form action="/crear_producto.php" method="post" enctype="multipart/form-data">
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
                    <input type="text" id="precio" name="precio" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen (opcional):</label>
                    <input type="file" id="imagen" name="imagen" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <p class="text-gray-500 text-xs italic">Formatos permitidos: JPG, PNG. Tamaño máximo: [establecer límite].</p>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:shadow-outline">Crear Producto</button>
                    <a href="listado.php" class="inline-block align-baseline font-semibold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-black py-4 text-center text-gray-400">
        &copy; 2025 We-Connect. Todos los derechos reservados.
    </footer>
</body>
</html>